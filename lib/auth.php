<?php
/**
 * lib/auth.php - who is logged in, and are they allowed to be here.
 *
 * Session keys used everywhere:
 *     $_SESSION['user']        the username
 *     $_SESSION['admin']       true when the account has an admin rank
 *     $_SESSION['epoch']       login_credentials.session_epoch at login time
 *     $_SESSION['start']       first request time
 *     $_SESSION['expire']      last activity + 90 minutes
 */

/** Start the session once, with the cookie rules this app wants. */
function session_boot(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    // Some Linux images give the PHP user a session folder it can write into but not read
    // (that is the safe setting for shared hosting). A normal writable folder is used instead
    // when that is the case - Windows/XAMPP is never affected.
    $savePath = ini_get('session.save_path');
    if ($savePath !== '' && is_dir($savePath) && !is_readable($savePath)) {
        $own = sys_get_temp_dir() . '/wsym_sessions';
        if (!is_dir($own)) { @mkdir($own, 0700, true); }
        if (is_dir($own) && is_readable($own) && is_writable($own)) {
            session_save_path($own);
        }
    }

    // "Remember Me" is decided at login (login.php sets remember_me before calling this)
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

/** Log a username in. remember = keep the cookie for 30 days instead of the browser session. */
function login_user(string $username, bool $remember = false): void
{
    $row = db_row('SELECT username, rankname, session_epoch FROM login_credentials WHERE username = ?', [$username]);
    if ($row === null) {
        return;
    }

    session_regenerate_id(true);
    $_SESSION['user']   = $row['username'];
    $_SESSION['admin']  = $row['rankname'] !== 'none';
    $_SESSION['epoch']  = (int)$row['session_epoch'];
    $_SESSION['start']  = time();
    $_SESSION['expire'] = time() + (90 * 60);

    if ($remember) {
        // renew the cookie with a 30-day life, same session id
        setcookie(session_name(), session_id(), [
            'expires'  => time() + (30 * 24 * 60 * 60),
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    db_run('UPDATE login_credentials SET lastlogin = NOW() WHERE username = ?', [$username]);
}

/** Forget the user completely. */
function logout_user(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

/**
 * Make sure the visitor may see this page, and that the login is still valid.
 * $admin = true means "admin console only".
 * Returns the username.
 */
function require_login(bool $admin = false): string
{
    session_boot();

    if (empty($_SESSION['user'])) {
        redirect('index.php');
    }

    // "Expire session" from Info management: the admin bumped session_epoch
    $epoch = db_val('SELECT session_epoch FROM login_credentials WHERE username = ?', [$_SESSION['user']], null);
    if ($epoch === null || (int)$epoch !== (int)($_SESSION['epoch'] ?? -1)) {
        logout_user();
        redirect('index.php?ended=1');
    }

    // 90 minutes of inactivity ends the session
    if (isset($_SESSION['expire']) && time() > (int)$_SESSION['expire']) {
        logout_user();
        redirect('index.php?expired=1');
    }
    $_SESSION['expire'] = time() + (90 * 60);

    if ($admin && empty($_SESSION['admin'])) {
        redirect('welcome.php');          // a normal user never sees the admin console
    }

    return (string)$_SESSION['user'];
}

/** The logged-in username, or null. */
function current_user(): ?string
{
    session_boot();
    return $_SESSION['user'] ?? null;
}

/** The account row (username, email, pfp, email_verif, rankname, session_epoch). */
function user_row(string $username): array
{
    return db_row('SELECT username, email, pfp, email_verif, rankname, session_epoch
                   FROM login_credentials WHERE username = ?', [$username]) ?? [];
}

/**
 * What this user is allowed to do: ['account','deposit','withdraw','wire','ticket'].
 * 1 = allowed. The table is called blacklist, but each column stores a permission.
 */
function perms(string $username): array
{
    $row = db_row('SELECT account, deposit, withdraw, wire, ticket FROM blacklist WHERE username = ?', [$username]);
    return $row ?? ['account' => 1, 'deposit' => 1, 'withdraw' => 1, 'wire' => 1, 'ticket' => 1];
}

/** One yes/no: allowed('deposit', $username). */
function allowed(string $what, string $username): bool
{
    return (int)(perms($username)[$what] ?? 1) === 1;
}

/* ------------------------------------------------------------------ CSRF */

/**
 * One token per session, checked on every POST.
 * A form includes csrf_field(); check_csrf() rejects anything that does not carry it.
 */
function csrf_token(): string
{
    session_boot();
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

/** The hidden input to put inside a <form method="post">. */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
}

/** Stop the request when a POST arrives without the session token. */
function check_csrf(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        return;
    }
    if (($_POST['csrf'] ?? '') === ($_SESSION['csrf'] ?? '-')) {
        return;
    }
    http_response_code(400);
    show_result('Action Failed : Your session expired', 'Reload the page and try again.', 'index.php', 4, false);
}
