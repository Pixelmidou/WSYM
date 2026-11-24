<?php
/**
 * check.php - "is my install OK?". Open it in the browser after setup; it lists every
 * setting it can verify in plain English, with OK or a fix.
 */
require __DIR__ . '/bootstrap.php';

$rows = [];
$add = function (string $what, bool $ok, string $fix = '') use (&$rows) {
    $rows[] = ['what' => $what, 'ok' => $ok, 'fix' => $fix];
};

// ---- php
$add('PHP ' . PHP_VERSION . ' (this app needs 8.1 or newer)', PHP_VERSION_ID >= 80100, 'Install a newer XAMPP.');
foreach (['mysqli', 'mbstring', 'openssl'] as $ext) {
    $add('PHP extension "' . $ext . '"', extension_loaded($ext),
         'Open php.ini and remove the ";" before extension=' . $ext);
}

// ---- .env
$add('.env file exists', is_file(APP_ROOT . '/.env'), 'Copy .env.example to .env in this folder.');
$add('database_name = "' . env('database_name', '(empty)') . '"', env('database_name') !== '', 'Set database_name in .env.');
$add('mailer_transport = "' . env('mailer_transport', 'file') . '"',
     in_array(env('mailer_transport', 'file'), ['file', 'gmail', 'smtp', 'mail'], true),
     'Use file, gmail, smtp or mail.');

// ---- database
$con = db();
$add('Connected to MySQL as "' . env('database_username') . '"', true);
$tables = ['login_credentials', 'ranks', 'balance', 'blacklist', 'deposit', 'withdraw', 'wire', 'ticket', 'ticket_message', 'admin_audit'];
foreach ($tables as $table) {
    $exists = db_val('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = ? AND table_name = ?',
                     [env('database_name'), $table], 0);
    $add('table "' . $table . '"', (int)$exists === 1, 'Import wsym.sql into the database named in .env.');
}
$add('the three demo accounts can log in',
     (int)db_val('SELECT COUNT(*) FROM login_credentials WHERE username IN ("Admin","VerifiedUser","nonVerifiedUser")', [], 0) === 3,
     'Re-import wsym.sql (it creates them with the passwords shown on the login page).');

// ---- files
foreach (['data', 'data/uploads', 'css', 'js', 'bootstrap-5.0.2-dist', 'font-awesome-4.7.0'] as $dir) {
    $add('folder "' . $dir . '/"', is_dir(APP_ROOT . '/' . $dir), 'Unzip the whole archive again.');
}

// the pictures and icons the pages ask for. A missing one shows as a broken image and
// nothing in the page reports it, so every file is listed here by name.
foreach (['favicon.ico', 'logo.png', 'background.jpg', 'user.svg', 'key.svg', 'mail.svg', 'dollar.svg',
          'repeat.svg', 'stop.svg', 'write.svg', 'calender.svg', 'clock.svg'] as $image) {
    $add('data/' . $image, is_file(APP_ROOT . '/data/' . $image),
         'Unzip the archive again - data/ must keep every file that came in it.');
}
$add('data/uploads/ is writable', is_writable(APP_ROOT . '/data/uploads'),
     'Windows: right-click data\\uploads > Properties > uncheck Read-only.');
// GD only decides how crisply a photo is cropped. Without it the browser crops the
// picture instead, the app works exactly the same, so a missing GD is not a problem.
$add('Photo cropping (optional): ' . (extension_loaded('gd') ? 'done by PHP (GD)' : 'done by the browser'),
     true, '');

// ---- mail
$transport = env('mailer_transport', 'file');
if ($transport === 'gmail') {
    $add('Gmail address looks right', filter_var(env('mailer_username'), FILTER_VALIDATE_EMAIL) !== false,
         'Set mailer_username in .env.');
    $add('App Password is 16 characters (spaces are ignored)', strlen(str_replace(' ', '', env('mailer_password'))) === 16,
         'Google > Security > App passwords creates one. Your normal password never works.');
} else {
    $add('mail transport "' . $transport . '" needs no password', true);
}
$add('temp mailbox is writable', is_dir(mailbox_dir()) && is_writable(mailbox_dir()),
     'mailer_transport=file writes there. Read it at tools/mailbox.php.');
$add('base_url = "' . (env('base_url') !== '' ? env('base_url') : '(worked out from the request)') . '"', true);

$failed = count(array_filter($rows, fn($r) => !$r['ok']));
$optional = array_filter($rows, fn($r) => $r['ok'] && str_starts_with($r['what'], 'Photo cropping'));
page_top('WSYM - setup check', []);
?>
<div class="container py-5" style="max-width:820px">
    <h1 class="h3 mb-1">WSYM setup check</h1>
    <p class="text-muted"><?php echo count($rows) - $failed; ?> of <?php echo count($rows); ?> checks pass.
        <?php if ($optional): ?>The photo cropping line is information only: both ways work equally well.<?php endif; ?></p>
    <?php if ($failed === 0): ?>
        <div class="alert alert-success">Everything looks right. Open <a href="index.php">the app</a>.</div>
    <?php else: ?>
        <div class="alert alert-warning"><?php echo $failed; ?> thing(s) to fix — each one has a hint on the right.</div>
    <?php endif; ?>
    <table class="table table-sm">
        <tbody>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td style="width:60%"><?php echo e($row['what']); ?></td>
                <td>
                    <?php if ($row['ok']): ?>
                        <span class="badge bg-success">OK</span>
                    <?php else: ?>
                        <span class="badge bg-danger">FIX</span>
                        <div class="text-muted" style="font-size:13px"><?php echo e($row['fix']); ?></div>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <p><a href="index.php">Open WSYM</a> &middot; <a href="tools/mailbox.php">Local mailbox</a> &middot;
       <a href="tools/mail_test.php">Test the mail settings</a></p>
</div>
<?php page_bottom(); ?>
