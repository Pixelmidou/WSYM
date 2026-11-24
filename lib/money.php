<?php
/**
 * lib/money.php - the money rules, in one place.
 *
 * Three functions:
 *     parse_amount($text)   "1.50" -> 1.5   ("abc" and "-5" and "0" -> null)
 *     deposit / withdraw / wire - each is one transaction
 *
 * money_bug_guard: the balance is changed and the ledger row is written inside
 * ONE transaction, so they can never disagree, and every amount is checked before
 * anything is written.
 */

/**
 * Read an amount the user typed. Returns a float with 2 decimals, or null when the text
 * is not a positive amount. The text is read with str_replace + is_numeric rather than
 * FILTER_SANITIZE_NUMBER_FLOAT, because the filter drops the decimal point and would
 * turn "1.50" into 150.
 */
function parse_amount($text): ?float
{
    $text = str_replace([' ', ',', '$'], '', trim((string)$text));
    if ($text === '' || !is_numeric($text)) {
        return null;
    }
    $value = (float)$text;
    if ($value <= 0) {
        return null;                    // a negative amount would create money
    }
    if ($value > 9999999.99) {
        return null;                    // column is DECIMAL(9,2)
    }
    return round($value, 2);
}

/** "$1,234.50" */
function money(float $amount): string
{
    return number_format($amount, 2);
}

/** What the user may do right now. Returns 'ok' | 'blocked' | 'unverified'. */
function can_spend(string $username, string $what): string
{
    if (!allowed($what, $username)) {
        return 'blocked';
    }
    $verified = (int)db_val('SELECT email_verif FROM login_credentials WHERE username = ?', [$username], 0);
    if ($verified !== 1) {
        return 'unverified';
    }
    return 'ok';
}

/** The balance of a user (0.00 when the row is missing). */
function balance_of(string $username): float
{
    return (float)db_val('SELECT balance FROM balance WHERE username = ?', [$username], 0);
}

/** Add money to an account. Returns 'ok' | 'invalid' | 'blocked' | 'unverified'. */
function deposit(string $username, $rawAmount): string
{
    $amount = parse_amount($rawAmount);
    if ($amount === null) {
        return 'invalid';
    }
    $state = can_spend($username, 'deposit');
    if ($state !== 'ok') {
        return $state;
    }

    $con = db();
    $con->begin_transaction();
    $changed = db_run('UPDATE balance SET balance = balance + ? WHERE username = ?', [$amount, $username]);
    $logged  = db_run('INSERT INTO deposit (username, deposit_amount) VALUES (?, ?)', [$username, $amount]);
    if ($changed !== 1 || $logged !== 1) {
        $con->rollback();
        return 'invalid';
    }
    $con->commit();
    return 'ok';
}

/** Take money out. Returns 'ok' | 'invalid' | 'blocked' | 'unverified' | 'insufficient'. */
function withdraw(string $username, $rawAmount): string
{
    $amount = parse_amount($rawAmount);
    if ($amount === null) {
        return 'invalid';
    }
    $state = can_spend($username, 'withdraw');
    if ($state !== 'ok') {
        return $state;
    }

    $con = db();
    $con->begin_transaction();
    // the WHERE clause is what makes an overdraft impossible, even with two tabs open
    $changed = db_run('UPDATE balance SET balance = balance - ? WHERE username = ? AND balance >= ?',
                      [$amount, $username, $amount]);
    if ($changed !== 1) {
        $con->rollback();
        return 'insufficient';
    }
    $logged = db_run('INSERT INTO withdraw (username, withdraw_amount) VALUES (?, ?)', [$username, $amount]);
    if ($logged !== 1) {
        $con->rollback();
        return 'invalid';
    }
    $con->commit();
    return 'ok';
}

/**
 * Send money to another account by email.
 * Returns 'ok' | 'invalid' | 'blocked' | 'unverified' | 'insufficient' | 'no_receiver'.
 */
function wire(string $username, string $receiverEmail, $rawAmount): string
{
    $amount = parse_amount($rawAmount);
    if ($amount === null) {
        return 'invalid';
    }
    $state = can_spend($username, 'wire');
    if ($state !== 'ok') {
        return $state;
    }

    $receiverEmail = trim($receiverEmail);
    $receiver = db_row('SELECT username, email FROM login_credentials WHERE email = ?', [$receiverEmail]);
    if ($receiver === null || $receiver['username'] === $username) {
        return 'no_receiver';           // a typo would destroy the money
    }
    if ((int)db_val('SELECT account FROM blacklist WHERE username = ?', [$receiver['username']], 1) !== 1) {
        return 'no_receiver';           // receiver account is disabled
    }

    $con = db();
    $con->begin_transaction();
    $out = db_run('UPDATE balance SET balance = balance - ? WHERE username = ? AND balance >= ?',
                  [$amount, $username, $amount]);
    if ($out !== 1) {
        $con->rollback();
        return 'insufficient';
    }
    $in  = db_run('UPDATE balance SET balance = balance + ? WHERE username = ?', [$amount, $receiver['username']]);
    $log = db_run('INSERT INTO wire (username, receiver, wire_amount) VALUES (?, ?, ?)',
                  [$username, $receiver['username'], $amount]);
    if ($in !== 1 || $log !== 1) {
        $con->rollback();
        return 'invalid';
    }
    $con->commit();
    return 'ok';
}

/** The message for a result code, per action. Used by the result page. */
function money_result(string $action, string $code, float $amount = 0, string $extra = ''): array
{
    $amountText = '$' . money($amount);
    $map = [
        'ok'           => ["Action Success : Money was deposited in your account !", "A value of $amountText was added to your account", true],
        'invalid'      => ['Action Failed : Invalid amount', 'Type a positive amount such as 25 or 25.50.', false],
        'insufficient' => ['Action Failed : Insufficient funds', 'Your balance does not cover that amount.', false],
        'no_receiver'  => ['Action Failed : Recipient not found', 'No active account uses that email address, and you cannot wire to yourself.', false],
        'blocked'      => ['Action Failed : You are blocked from this action', 'An administrator can allow it again from the admin console.', false],
        'unverified'   => ['Action Failed : Email is not verified', 'Check your inbox for the verification link.', false],
    ];

    if ($action === 'withdraw') {
        $map['ok'] = ['Action Success : Money was withdrawn from your account !', "A value of $amountText was removed from your account", true];
        $map['blocked'] = ['Action Failed : You are blocked from withdrawing money', '', false];
    }
    if ($action === 'wire') {
        $map['ok'] = ['Action Success : Money was successfully wired !', "A value of $amountText was sent to $extra", true];
        $map['blocked'] = ['Action Failed : You are blocked from wiring money', '', false];
    }

    return $map[$code] ?? $map['invalid'];
}

/**
 * Finish a money action: show the answer page with the real amount on it.
 * This is the fix for the "$0.00 was added" page - the amount is passed straight to the
 * result page instead of being parked in the session and read back later.
 */
function money_finish(string $action, string $code, float $amount = 0, string $extra = ''): void
{
    [$title, $detail, $ok] = money_result($action, $code, $amount, $extra);
    show_result($title, $detail, 'welcome.php', 4, $ok);
}
