<?php
/**
 * settings.php - change your username, email or password.
 *
 * Changing anything asks for the password again first ("Login again to confirm your
 * identity"): one small form, then the real one. The change itself is two SQL statements
 * in one transaction, and a username change moves the balance, permissions and tickets
 * with it.
 */
require __DIR__ . '/bootstrap.php';
$username = require_login();
$what = $_GET['do'] ?? 'username';
$what = in_array($what, ['username', 'email', 'password'], true) ? $what : 'username';
$me = user_row($username);

$confirmed = !empty($_SESSION['confirmed_at']) && (time() - (int)$_SESSION['confirmed_at']) < 600;
$note = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {

    // step 1: the password check
    if (isset($_POST['id_sub'])) {
        $row = db_row('SELECT pass FROM login_credentials WHERE username = ?', [$username]);
        if ($row !== null && password_verify((string)($_POST['id_pass'] ?? ''), $row['pass'])) {
            $_SESSION['confirmed_at'] = time();
            redirect('settings.php?do=' . $what);
        }
        $note = 'That password is not correct.';
        $confirmed = false;
    }

    // step 2: the actual change
    if ($confirmed && isset($_POST['change_submit'])) {
        if ($what === 'username') {
            $new = trim((string)($_POST['uuser'] ?? ''));
            if ($new !== trim((string)($_POST['cuuser'] ?? ''))) {
                $note = 'The two usernames do not match.';
            } else {
                switch (account_rename($username, $new)) {
                    case 'ok':
                        $_SESSION['user'] = $new;
                        unset($_SESSION['confirmed_at']);
                        show_result('Action Success : Username changed !',
                                    'Your new name is <b>' . e($new) . '</b>. Use it the next time you log in.',
                                    'welcome.php', 6, true);
                    case 'taken':     $note = 'That username is already taken.'; break;
                    case 'invalid':   $note = 'Usernames are 3-30 characters: letters, digits, dot, dash, underscore.'; break;
                    case 'unchanged': $note = 'That is already your username.'; break;
                    default:          $note = 'The rename failed, nothing was changed.';
                }
            }
        }

        if ($what === 'email') {
            $new = trim((string)($_POST['mail'] ?? ''));
            if ($new !== trim((string)($_POST['cmail'] ?? ''))) {
                $note = 'The two email addresses do not match.';
            } else {
                $link = account_change_email($username, $new);
                if ($link === null) {
                    $note = 'That address is not valid, or another account already uses it.';
                } else {
                    unset($_SESSION['confirmed_at']);
                    show_result('Action Success : Email changed !',
                                'We sent a new verification link to <b>' . e($new) . '</b>. Your account is unverified until you click it.',
                                'welcome.php', 6, true);
                }
            }
        }

        if ($what === 'password') {
            $new = (string)($_POST['pass'] ?? '');
            if ($new !== (string)($_POST['cpass'] ?? '')) {
                $note = 'The two passwords do not match.';
            } elseif (!account_set_password($username, $new)) {
                $note = 'Use at least 8 characters.';
            } else {
                unset($_SESSION['confirmed_at']);
                show_result('Action Success : Password changed !',
                            'Your new password is active now. Keep it somewhere safe.',
                            'welcome.php', 6, true);
            }
        }

    }
}

$titles = ['username' => 'Change your username', 'email' => 'Change your email', 'password' => 'Change your password'];
page_top('WSYM Banking', ['account_settings']);
?>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300&family=Open+Sans+Condensed:wght@300&display=swap" rel="stylesheet">
    <?php page_inside(); ?>
        <form method="post" class="cont2 d-flex flex-column justify-content-center align-items-center">
            <?php echo csrf_field(); ?>
            <?php if (!$confirmed): ?>
                <h3 class="text-center mt-3 text-white">Login again to confirm your identity</h3>
                <div class="mt-auto mb-auto d-flex flex-column justify-content-center align-items-center">
                    <label class="labbor lab">
                        <img src="<?php echo url('data/key.svg'); ?>" alt="">
                        <input type="password" placeholder="Your password" name="id_pass" required>
                    </label>
                </div>
                <?php if ($note !== ''): ?><span style="color:red"><?php echo e($note); ?></span><?php endif; ?>
                <input type="submit" value="Confirm" class="but" name="id_sub">
            <?php else: ?>
                <h3 class="text-center mt-3 text-white"><?php echo $titles[$what]; ?></h3>
                <div class="mt-auto mb-auto d-flex flex-column justify-content-center align-items-center">
                    <?php if ($what === 'username'): ?>
                        <label class="labbor lab"><img src="<?php echo url('data/user.svg'); ?>" alt="">
                            <input type="text" placeholder="New Username" name="uuser" required></label>
                        <label class="labbor lab"><img src="<?php echo url('data/user.svg'); ?>" alt="">
                            <input type="text" placeholder="Confirm Username" name="cuuser" required></label>
                    <?php elseif ($what === 'email'): ?>
                        <label class="labbor lab"><img src="<?php echo url('data/mail.svg'); ?>" alt="">
                            <input type="email" placeholder="New Email" name="mail" required></label>
                        <label class="labbor lab"><img src="<?php echo url('data/mail.svg'); ?>" alt="">
                            <input type="email" placeholder="Confirm Email" name="cmail" required></label>
                    <?php else: ?>
                        <label class="labbor lab"><img src="<?php echo url('data/key.svg'); ?>" alt="">
                            <input type="password" placeholder="New Password" name="pass" required></label>
                        <label class="labbor lab"><img src="<?php echo url('data/key.svg'); ?>" alt="">
                            <input type="password" placeholder="Confirm Password" name="cpass" required></label>
                    <?php endif; ?>
                </div>
                <?php if ($note !== ''): ?><span style="color:red"><?php echo e($note); ?></span><?php endif; ?>
                <input type="submit" value="<?php echo $titles[$what]; ?>" class="but" name="change_submit">
                <a class="btn btn-link" href="welcome.php">Cancel</a>
            <?php endif; ?>
        </form>
    <?php page_inside_end(); ?>
<?php page_bottom(); ?>
