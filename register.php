<?php
/**
 * register.php - create an account, send the verification mail, log in, go to the dashboard.
 */
require __DIR__ . '/bootstrap.php';
session_boot();

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect('index.php');
}

$username = trim((string)($_POST['username2'] ?? ''));
$password = (string)($_POST['password2'] ?? '');
$email    = trim((string)($_POST['email2'] ?? ''));

$fail = function (string $reason) {
    show_result('Action Failed : ' . $reason, 'Go back and try again.', 'index.php', 5, false);
};

if (!preg_match('/^[A-Za-z0-9_.-]{3,30}$/', $username)) {
    $fail('Username must be 3-30 characters (letters, digits, dot, dash, underscore).');
}
if (strlen($password) < 8) {
    $fail('Password must be at least 8 characters.');
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $fail('That email address is not valid.');
}
if (db_row('SELECT username FROM login_credentials WHERE username = ?', [$username]) !== null) {
    $fail('That username already exists.');
}
if (db_row('SELECT username FROM login_credentials WHERE email = ?', [$email]) !== null) {
    $fail('That email address already has an account.');
}

// the token that goes in the verification link; 24 hours to use it
$token  = bin2hex(random_bytes(16));
$expire = date('Y-m-d H:i:s', time() + 24 * 60 * 60);

$con = db();
$con->begin_transaction();
$created = db_run('INSERT INTO login_credentials
                   (username, pass, email, email_verif, email_verif_token, email_verif_expire, pfp, rankname)
                   VALUES (?, ?, ?, 0, ?, ?, ?, ?)',
                  [$username, password_hash($password, PASSWORD_BCRYPT), $email, $token, $expire, 'favicon.ico', 'none']);
$wallet  = db_run('INSERT INTO balance (username, email, balance) VALUES (?, ?, 0.00)', [$username, $email]);
$rights  = db_run('INSERT INTO blacklist (username, account, deposit, withdraw, wire, ticket)
                   VALUES (?, 1, 1, 1, 1, 1)', [$username]);

if ($created !== 1 || $wallet !== 1 || $rights !== 1) {
    $con->rollback();
    $fail('The account could not be created. Try another username.');
}
$con->commit();

$link = mail_verification($username, $email, $token);
$mailProblem = wf_mail_error();

session_boot();
login_user($username);

$detail = 'We sent a verification link to <b>' . e($email) . '</b>. '
        . 'You can look at it right now at <a href="' . url('tools/mailbox.php') . '">tools/mailbox.php</a>.';
if ($mailProblem !== '') {
    $detail = '<b>The account was created, but the email could not be sent:</b> ' . e($mailProblem)
            . '<br>Your verification link is <a href="' . e($link) . '">here</a>.';
}
show_result('Action Success : Account created !', $detail, 'welcome.php', 8, true);
