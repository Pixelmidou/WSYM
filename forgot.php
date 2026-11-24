<?php
/**
 * forgot.php - "I forgot my password": email a single-use reset link.
 * The answer page is identical whether or not the address exists, so nobody can use this
 * page to find out who has an account here.
 */
require __DIR__ . '/bootstrap.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect('index.php');
}

$email = trim((string)($_POST['email3'] ?? ''));
$row   = filter_var($email, FILTER_VALIDATE_EMAIL)
       ? db_row('SELECT username, email FROM login_credentials WHERE email = ?', [$email])
       : null;

$detail = 'If an account uses that address, a reset link is on its way. It works once, for 30 minutes.';

if ($row !== null) {
    $token = bin2hex(random_bytes(16));
    db_run('UPDATE login_credentials SET pass_reset_token = ?, pass_reset_token_expire = ?
            WHERE username = ?', [$token, date('Y-m-d H:i:s', time() + 30 * 60), $row['username']]);
    $link = mail_password_reset($row['username'], $row['email'], $token);
    if (wf_mail_error() !== '') {
        $detail .= '<br><b>The email could not be sent:</b> ' . e(wf_mail_error())
                 . '<br>The link is <a href="' . e($link) . '">here</a>.';
    } else {
        $detail .= '<br>No mail? Open <a href="' . url('tools/mailbox.php') . '">tools/mailbox.php</a>.';
    }
}

show_result('Password reset requested', $detail, 'index.php', 8, true);
