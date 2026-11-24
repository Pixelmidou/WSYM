<?php
/**
 * reset.php - choose a new password with the token from the mail.
 * GET shows the form, POST saves the new password and burns the token (single use).
 */
require __DIR__ . '/bootstrap.php';
session_boot();

$token = trim((string)($_GET['token'] ?? $_POST['token'] ?? ''));
$row   = $token === '' ? null : db_row('SELECT username, pass_reset_token_expire FROM login_credentials
                                        WHERE pass_reset_token = ?', [$token]);

$dead = fn(string $why) => show_result('Action Failed : ' . $why,
    'Ask for a new reset link from the login page.', 'index.php', 6, false);

if ($row === null) {
    $dead('Token not found');
}
if ($row['pass_reset_token_expire'] !== null && strtotime($row['pass_reset_token_expire']) < time()) {
    $dead('This reset link has expired');
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $pass  = (string)($_POST['pass'] ?? '');
    $again = (string)($_POST['cpass'] ?? '');

    if (strlen($pass) < 8) {
        show_result('Action Failed : Password must be at least 8 characters', '', 'reset.php?token=' . urlencode($token), 6, false);
    }
    if ($pass !== $again) {
        show_result('Action Failed : The two passwords do not match', '', 'reset.php?token=' . urlencode($token), 6, false);
    }

    // the WHERE clause includes the token: two clicks cannot both succeed
    $changed = db_run('UPDATE login_credentials
                       SET pass = ?, pass_reset_token = NULL, pass_reset_token_expire = NULL
                       WHERE pass_reset_token = ?', [password_hash($pass, PASSWORD_BCRYPT), $token]);
    if ($changed !== 1) {
        $dead('Token not found');
    }
    show_result('Action Success : Password changed !', 'Log in with your new password.', 'index.php', 5, true);
}

$css = ['account_settings'];
page_top('WSYM Banking', $css, false);
?>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300&family=Open+Sans+Condensed:wght@300&display=swap" rel="stylesheet">
    <?php page_inside(); ?>
        <form method="post" class="cont2 d-flex flex-column justify-content-center align-items-center">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="token" value="<?php echo e($token); ?>">
            <h3 class="text-center mt-3 text-white">Choose a new password</h3>
            <div class="mt-auto mb-auto d-flex flex-column justify-content-center align-items-center">
                <label class="labbor lab">
                    <img src="<?php echo url('data/key.svg'); ?>" alt="">
                    <input type="password" placeholder="New password" name="pass" id="pass" required>
                </label>
                <label class="labbor lab">
                    <img src="<?php echo url('data/key.svg'); ?>" alt="">
                    <input type="password" placeholder="Confirm password" name="cpass" id="cpass" required>
                </label>
            </div>
            <input type="submit" value="Change my password" class="but" name="sub_pass">
        </form>
    <?php page_inside_end(); ?>
<?php page_bottom(); ?>
