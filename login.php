<?php
/**
 * login.php - check the username and password, then send the visitor to the right page.
 *
 * Admin ranks land on admin_redirect.php (choose admin console or user page), everybody
 * else on welcome.php. A failure shows the "Access Denied" page.
 */
require __DIR__ . '/bootstrap.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect('index.php');
}

$username = trim((string)($_POST['username1'] ?? ''));
$password = (string)($_POST['password1'] ?? '');
$remember = !empty($_POST['remember_me']);

$denied = function () {
    show_result('Access Denied : Check the provided info !',
                'Possible problems: wrong password or username / account does not exist / account disabled.',
                'index.php', 4, false);
};

if ($username === '' || $password === '') {
    $denied();
}

// the password column of login_credentials is called `pass`
$row = db_row('SELECT username, pass, rankname FROM login_credentials WHERE username = ?', [$username]);
if ($row === null || !password_verify($password, $row['pass'])) {
    $denied();
}

// an account disabled by an admin cannot log in
if (!allowed('account', $row['username'])) {
    $denied();
}

session_boot();
login_user($row['username'], $remember);

redirect($row['rankname'] !== 'none' ? 'admin_redirect.php' : 'welcome.php');
