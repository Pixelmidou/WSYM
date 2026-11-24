<?php
/**
 * tools/mail_test.php - find out why email does or does not work.
 *   (no parameters)            inspect the settings only
 *   ?to=you@gmail.com          send one real message
 *
 * Gmail and SMTP go through PHPMailer; when it fails, PHPMailer's own answer (the SMTP
 * reply code and text) is printed below.
 */
require __DIR__ . '/../bootstrap.php';

$transport = strtolower(env('mailer_transport', 'file'));
$to = trim((string)($_GET['to'] ?? ''));
$sent = null;

if ($to !== '') {
    $sent = wf_mail($to, 'WSYM test message', '<p>This is a test from <b>' . e(base_url()) . '</b>.</p>'
                    . '<p>If you can read this, your mail settings work.</p>');
}

$settings = [
    'mailer_transport' => $transport,
    'mailer_host'      => $transport === 'gmail' ? 'smtp.gmail.com (fixed for gmail)' : env('mailer_host', '(empty)'),
    'mailer_port'      => env('mailer_port', '465'),
    'mailer_secure'    => env('mailer_secure', 'ssl'),
    'mailer_username'  => env('mailer_username', '(empty)'),
    'App Password'     => env('mailer_password', '') === '' ? '(empty)' :
                          (strlen(str_replace(' ', '', env('mailer_password'))) === 16 ? 'set, 16 characters - looks right'
                                                                                     : 'set, but not 16 characters - a normal password will NOT work'),
    'From: address'    => mail_from(),
    'sending with'     => $transport === 'gmail' || $transport === 'smtp'
                          ? (phpmailer_load()
                              ? 'PHPMailer ' . PHPMailer\PHPMailer\PHPMailer::VERSION
                              : 'PHP SMTP - lib/phpmailer is missing!')
                          : 'PHP ' . $transport,
    'temp mailbox'     => mailbox_dir(),
];

page_top('WSYM - mail test', []);
?>
<div class="container py-4" style="max-width:860px">
    <h1 class="h4">Mail test</h1>
    <p class="text-muted">This page never guesses: it shows the settings it will use, and if you ask it to send,
       it shows the server's own answer.</p>

    <table class="table table-sm">
        <tbody>
        <?php foreach ($settings as $key => $value): ?>
            <tr><th style="width:220px"><?php echo e($key); ?></th><td><?php echo e($value); ?></td></tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php if ($sent !== null): ?>
        <?php if ($sent): ?>
            <div class="alert alert-success">The <code><?php echo e($transport); ?></code> transport accepted the message
                <?php echo $transport === 'file' ? '— read it at <a href="mailbox.php">tools/mailbox.php</a>' : '— check the inbox'; ?>.</div>
        <?php else: ?>
            <div class="alert alert-danger"><b>Not sent:</b> <?php echo e(wf_mail_error()); ?></div>
        <?php endif; ?>
    <?php endif; ?>

    <form method="get" class="d-flex gap-2 mb-3">
        <input type="email" class="form-control" name="to" placeholder="you@gmail.com" value="<?php echo e($to); ?>">
        <button class="btn btn-primary">Send a test message</button>
    </form>

    <h2 class="h6 mt-4">What each Gmail error means</h2>
    <ul>
        <li><b>Username and Password not accepted (535)</b> — you pasted your normal password. Google Account &gt; Security &gt; App passwords creates a 16-character one.</li>
        <li><b>Connection refused / timed out</b> — the firewall blocks 465 or 587. Try the other port with the matching <code>mailer_secure</code>.</li>
        <li><b>Sender refused</b> — in gmail mode the From: must be your own address, which this app already does.</li>
    </ul>
    <p><a href="../check.php">Setup check</a> &middot; <a href="mailbox.php">Local mailbox</a></p>
</div>
<?php page_bottom(); ?>
