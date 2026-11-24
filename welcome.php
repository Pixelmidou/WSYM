<?php
/**
 * welcome.php - the customer dashboard.
 *
 * Deposit, Withdraw, Wire and "Message support" are four POST actions handled here.
 * Each one calls the matching function in lib/money.php or lib/tickets.php and then
 * shows the answer page with the amount it just moved. The amount is read straight from
 * the form and handed to the answer page, never through the session, so the receipt
 * always shows the number that was really moved.
 */
require __DIR__ . '/bootstrap.php';
$username = require_login();
$me = user_row($username);
$balance = balance_of($username);

/* --------------------------------------------------------------- the actions */
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {

    if (isset($_POST['deposit_submit'])) {
        $amount = parse_amount($_POST['depval'] ?? '');
        $code = deposit($username, $_POST['depval'] ?? '');
        money_finish('deposit', $code, $amount ?? 0);
    }

    if (isset($_POST['withdraw_submit'])) {
        $amount = parse_amount($_POST['withval'] ?? '');
        $code = withdraw($username, $_POST['withval'] ?? '');
        money_finish('withdraw', $code, $amount ?? 0);
    }

    if (isset($_POST['wire_submit'])) {
        $amount = parse_amount($_POST['wireval'] ?? '');
        $to     = trim((string)($_POST['wireemail'] ?? ''));
        $code   = wire($username, $to, $_POST['wireval'] ?? '');
        money_finish('wire', $code, $amount ?? 0, e($to));
    }

    if (isset($_POST['send_message']) || isset($_POST['message_text'])) {
        if (!allowed('ticket', $username)) {
            show_result('Action Failed : You are blocked from messaging support', '', 'welcome.php', 4, false);
        }
        switch (thread_post($username, $username, (string)($_POST['message_text'] ?? ''), false)) {
            case 'ok':
                show_result('Action Success : Message sent !', 'Support answers in the same conversation.',
                            'tickets.php', 3, true);
            default:
                show_result('Action Failed : Empty message', 'Write something before sending.', 'welcome.php', 4, false);
        }
    }

    if (isset($_POST['dephis_submit'])) { redirect('history.php?kind=deposit'); }
    if (isset($_POST['withhis_submit'])) { redirect('history.php?kind=withdraw'); }
    if (isset($_POST['wirehis_submit'])) { redirect('history.php?kind=wire'); }

    redirect('welcome.php');
}

/* ------------------------------------------------------------- card contents */
$last = function (string $kind) use ($username): string {
    $row = history_all($username, $kind, 1)[0] ?? null;
    if ($row === null) {
        return $kind === 'deposit' ? 'There was no previous deposits'
                                   : ($kind === 'withdraw' ? 'There was no previous withdraws' : 'There was no previous wires');
    }
    $when = date('Y-m-d H:i:s', strtotime($row['date']));
    if ($kind === 'wire') {
        $to = db_val('SELECT email FROM login_credentials WHERE username = ?', [$row['receiver']], $row['receiver']);
        return "Last Known Wire was at $when with a value of $" . money((float)$row['amount']) . " to $to";
    }
    return 'Last Known ' . ucfirst($kind) . " was at $when with a value of $" . money((float)$row['amount']);
};

$presets = function (string $field, string $suffix) {
    // the five "Predefined Values" buttons
    foreach ([10, 20, 30, 40, 50] as $i => $value) {
        $id = ['ten', 'twenty', 'thirty', 'forty', 'fifty'][$i] . $suffix;
        echo '<input type="button" value="$' . $value . '" id="' . $id . '" class="vals valbuts">' . "\n";
    }
};

page_top('WSYM Banking', ['welcome_style']);
?>
<link href="<?php echo url('font-awesome-4.7.0/css/font-awesome.min.css'); ?>" rel="stylesheet">
    <header>
        <nav class="container3">
            <ul class="container2" id="balancev">
                <img src="<?php echo url('data/favicon.ico'); ?>" alt="" width="50px" height="50px">
                <li>WSYM Banking</li>
            </ul>
            <ul class="container1">
                <li class="navitem" id="depositv">Deposit Money</li>
                <li class="navitem" id="withdrawv">Withdraw Money</li>
                <li class="navitem" id="wirev">Wire Money</li>
                <li class="navitem"><a href="tickets.php" style="text-decoration:none;color:inherit">Message Support</a></li>
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle dr" data-bs-toggle="dropdown">
                    <img src="<?php echo avatar_url($me['pfp'] ?? null); ?>" alt="pfp" width="32" height="32" class="rounded-circle me-2" id="output" style="object-fit:cover">
                    <strong><?php echo e($username); ?></strong>
                </a>
                <ul class="dropdown-menu text-small shadow">
                    <li><a class="dropdown-item" href="settings.php?do=username">Change Your Username</a></li>
                    <li><a class="dropdown-item" href="settings.php?do=email">Change Your Email</a></li>
                    <li><a class="dropdown-item" href="settings.php?do=password">Change Your Password</a></li>
                    <li><hr></li>
                    <li>
                        <form method="post" action="avatar.php" enctype="multipart/form-data" class="dropdown-item">
                            <?php echo csrf_field(); ?>
                            <div class="mb-2">Upload Your Photo :</div>
                            <input type="file" accept="image/png, image/jpeg, image/jpg, image/gif" name="imgupload">
                            <div class="d-flex gap-2 mt-2 align-items-center justify-content-center">
                                <input type="submit" value="Upload Image" name="imgsub">
                                <input type="submit" value="Delete Image" name="imgdel" formnovalidate>
                            </div>
                        </form>
                    </li>
                    <li><hr></li>
                    <?php if (($_SESSION['admin'] ?? false)): ?>
                        <li><a href="admin_redirect.php" class="dropdown-item">Go Back to Redirections</a></li>
                        <li><a href="admin.php" class="dropdown-item">Admin console</a></li>
                    <?php endif; ?>
                    <?php if ((int)($me['email_verif'] ?? 0) !== 1): ?>
                        <li><a href="resend.php" class="dropdown-item">Resend Email Verification</a></li>
                    <?php endif; ?>
                    <li><a class="dropdown-item" href="logout.php" onclick="return confirm('Are you sure you want to logout ?')">Sign Out</a></li>
                </ul>
            </ul>
        </nav>
    </header>
    <main>
        <div class="container4 balance" id="balance">
            <h1>Welcome Back</h1>
            <div class="push">Your Balance is : $<?php echo money($balance); ?></div>
            <div style="font-size: 15px; text-align: center;"><?php echo e($last('deposit')); ?></div>
            <div style="font-size: 15px; text-align: center;"><?php echo e($last('withdraw')); ?></div>
            <div class="push2" style="font-size: 15px; text-align: center;"><?php echo e($last('wire')); ?></div>
            <div style="font-size: 14px; text-align: center; margin-top: 8px;">
                <a href="history.php">Full history</a> &middot;
                <a href="statement.php">Download statement (CSV)</a> &middot;
                <a href="tickets.php">My messages</a>
            </div>
        </div>

        <form method="post" class="container4 deposit" id="deposit">
            <?php echo csrf_field(); ?>
            <h1 class="deptitle">Deposit Money</h1>
            <label class="labbor">
                <img src="<?php echo url('data/dollar.svg'); ?>" alt="">
                <input type="text" placeholder="Value in Dollars" name="depval" id="depval" required>
            </label>
            <div><span>Predefined Values : </span><?php $presets('depval', ''); ?></div>
            <div class="d-flex gap-3 mt-auto">
                <input type="submit" value="Deposit" class="but" style="width:250px" id="depositbut" name="deposit_submit" onclick="return confirm('Are you sure ?')">
                <input type="submit" value="Deposit History" class="but" style="width:250px" name="dephis_submit" formnovalidate>
            </div>
        </form>

        <form method="post" class="container4 withdraw" id="withdraw">
            <?php echo csrf_field(); ?>
            <h1 class="deptitle">Withdraw Money</h1>
            <label class="labbor">
                <img src="<?php echo url('data/dollar.svg'); ?>" alt="">
                <input type="text" placeholder="Value in Dollars" name="withval" id="withval" required>
            </label>
            <div><span>Predefined Values : </span><?php $presets('withval', 'n'); ?></div>
            <div class="d-flex gap-3 mt-auto">
                <input type="submit" value="Withdraw" class="but" style="width:250px" id="withdrawbut" name="withdraw_submit" onclick="return confirm('Are you sure ?')">
                <input type="submit" value="Withdraw History" class="but" style="width:250px" name="withhis_submit" formnovalidate>
            </div>
        </form>

        <form method="post" class="container4 wire" id="wire">
            <?php echo csrf_field(); ?>
            <h1>Wire Money</h1>
            <div class="desc">Insert the email of the person you want to wire the money to</div>
            <label class="labbor lab">
                <img src="<?php echo url('data/mail.svg'); ?>" alt="">
                <input type="email" placeholder="Email" name="wireemail" id="wireres" required>
            </label>
            <label class="labbor">
                <img src="<?php echo url('data/dollar.svg'); ?>" alt="">
                <input type="text" placeholder="Value in Dollars" name="wireval" id="wireval" required>
            </label>
            <div><span>Predefined Values : </span><?php $presets('wireval', 'nn'); ?></div>
            <div class="d-flex gap-3 mt-auto">
                <input type="submit" value="Wire the Money" class="but" style="width:250px" id="wirebut" name="wire_submit" onclick="return confirm('Are you sure ?')">
                <input type="submit" value="Wire History" class="but" style="width:250px" name="wirehis_submit" formnovalidate>
            </div>
        </form>

        <form method="post" class="container4 ticket" id="ticket">
            <?php echo csrf_field(); ?>
            <h1 class="deptitle">Message support</h1>
            <div class="desc">P.S. : One conversation at a time &mdash;
                <a href="tickets.php">open your messages</a></div>
            <label>
                <textarea id="tick" name="message_text" cols="40" rows="6"
                          placeholder="Write your message - support answers in the same thread"></textarea>
            </label>
            <input type="submit" value="Send Message" class="but" id="ticketbut" name="send_message">
        </form>
    </main>
    <script src="<?php echo url('js/welcome.js'); ?>"></script>
<?php page_bottom(); ?>
