<?php
/** resend.php - send a fresh verification link to the address on file. */
require __DIR__ . '/bootstrap.php';
$username = require_login();
$me = user_row($username);

if (($_SESSION['resend_wait'] ?? 0) > time()) {
    show_result('Action Failed : Please wait', 'You can ask for another link in a minute.', 'welcome.php', 4, false);
}

$token = bin2hex(random_bytes(16));
db_run('UPDATE login_credentials SET email_verif_token = ?, email_verif_expire = ? WHERE username = ?',
       [$token, date('Y-m-d H:i:s', time() + 86400), $username]);
$_SESSION['resend_wait'] = time() + 60;

mail_verification($username, (string)($me['email'] ?? ''), $token);

$problem = wf_mail_error();
show_result('Action Success : Verification email sent !',
            $problem !== '' ? '<b>Not sent:</b> ' . e($problem) : 'Open <a href="' . url('tools/mailbox.php') . '">tools/mailbox.php</a> to read it offline.',
            'welcome.php', 6, true);
