<?php
/**
 * verify.php - the link inside the verification mail lands here.
 * Tokens are single-use: verifying clears them, so a second click cannot verify again.
 */
require __DIR__ . '/bootstrap.php';

$token = trim((string)($_GET['token'] ?? ''));
$row   = $token === '' ? null : db_row('SELECT username, email_verif_expire FROM login_credentials
                                        WHERE email_verif_token = ?', [$token]);

if ($row === null) {
    show_result('Action Failed : This verification link is not valid', 
                'The link was already used, or the account no longer exists.', 'index.php', 6, false);
}
if ($row['email_verif_expire'] !== null && strtotime($row['email_verif_expire']) < time()) {
    show_result('Action Failed : This verification link has expired',
                'Register again or ask an administrator for a new link.', 'index.php', 6, false);
}

db_run('UPDATE login_credentials
        SET email_verif = 1, email_verif_token = NULL, email_verif_expire = NULL
        WHERE email_verif_token = ?', [$token]);

show_result('Action Success : Email Verified !',
            'You can now deposit, withdraw and wire money.', 'welcome.php', 4, true);
