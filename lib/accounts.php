<?php
/**
 * lib/accounts.php - the three things that change an account: rename, email, password.
 *
 * Kept apart from the pages because both settings.php (the customer) and admin.php
 * (Info management) need exactly the same rules. One function each, and the rename is
 * the interesting one: the child tables point at login_credentials(username), so the
 * parent must move LAST and the foreign keys have to step aside while it happens.
 */

/**
 * Rename a customer, and move everything that belongs to them.
 * Returns 'ok' | 'taken' | 'invalid' | 'unchanged' | 'error'.
 */
function account_rename(string $old, string $new): string
{
    $new = trim($new);
    if (!preg_match('/^[A-Za-z0-9_.-]{3,30}$/', $new)) {
        return 'invalid';
    }
    if ($new === $old) {
        return 'unchanged';
    }
    if (db_row('SELECT username FROM login_credentials WHERE username = ?', [$new]) !== null) {
        return 'taken';
    }

    // deposit / withdraw / wire have no foreign key at all, ticket and balance do
    $children = ['balance', 'blacklist', 'deposit', 'withdraw', 'wire', 'ticket_message', 'ticket'];

    $con = db();
    $con->query('SET foreign_key_checks = 0');       // session only, restored below
    $con->begin_transaction();

    foreach ($children as $table) {
        if (db_query("UPDATE `$table` SET username = ? WHERE username = ?", [$new, $old]) === false) {
            $con->rollback();
            $con->query('SET foreign_key_checks = 1');
            return 'error';
        }
    }
    $moved = db_run('UPDATE login_credentials SET username = ? WHERE username = ?', [$new, $old]);

    if ($moved !== 1) {
        $con->rollback();
        $con->query('SET foreign_key_checks = 1');
        return 'error';
    }
    $con->commit();
    $con->query('SET foreign_key_checks = 1');
    return 'ok';
}

/**
 * Change the email address and force a new verification.
 * Returns the link that was emailed, or null when the address is refused.
 */
function account_change_email(string $username, string $newEmail): ?string
{
    $newEmail = trim($newEmail);
    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        return null;
    }
    if (db_row('SELECT username FROM login_credentials WHERE email = ? AND username <> ?', [$newEmail, $username]) !== null) {
        return null;
    }

    $token = bin2hex(random_bytes(16));
    $con = db();
    $con->begin_transaction();
    $ok = db_run('UPDATE login_credentials
                  SET email = ?, email_verif = 0, email_verif_token = ?, email_verif_expire = ?
                  WHERE username = ?',
                 [$newEmail, $token, date('Y-m-d H:i:s', time() + 86400), $username]) === 1;
    $ok = db_run('UPDATE balance SET email = ? WHERE username = ?', [$newEmail, $username]) >= 0 && $ok;
    if (!$ok) {
        $con->rollback();
        return null;
    }
    $con->commit();

    return mail_verification($username, $newEmail, $token);
}

/** Set a new password. Returns false when it is too short. */
function account_set_password(string $username, string $password): bool
{
    if (strlen($password) < 8) {
        return false;
    }
    return db_run('UPDATE login_credentials SET pass = ? WHERE username = ?',
                  [password_hash($password, PASSWORD_BCRYPT), $username]) === 1;
}

/** Mark the account unverified and hand out a fresh verification link (admin action). */
function account_force_reverify(string $username): ?string
{
    $row = db_row('SELECT email FROM login_credentials WHERE username = ?', [$username]);
    if ($row === null) {
        return null;
    }
    $token = bin2hex(random_bytes(16));
    db_run('UPDATE login_credentials SET email_verif = 0, email_verif_token = ?, email_verif_expire = ?
            WHERE username = ?', [$token, date('Y-m-d H:i:s', time() + 86400), $username]);
    return mail_verification($username, (string)$row['email'], $token);
}

/** Hand out a single-use password reset link (admin action). */
function account_force_reset(string $username): ?string
{
    $row = db_row('SELECT email FROM login_credentials WHERE username = ?', [$username]);
    if ($row === null) {
        return null;
    }
    $token = bin2hex(random_bytes(16));
    db_run('UPDATE login_credentials SET pass_reset_token = ?, pass_reset_token_expire = ?
            WHERE username = ?', [$token, date('Y-m-d H:i:s', time() + 3600), $username]);
    return mail_password_reset($username, (string)$row['email'], $token);
}

/** Sign the customer out everywhere: their next click lands on the login page. */
function account_end_session(string $username): void
{
    db_run('UPDATE login_credentials SET session_epoch = session_epoch + 1 WHERE username = ?', [$username]);
}
