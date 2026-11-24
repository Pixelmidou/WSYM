<?php
/** tools/mailbox.php - read the mails captured by mailer_transport=file. */
require __DIR__ . '/../bootstrap.php';

$dir = mailbox_dir();
$files = glob($dir . '/*.html');
usort($files, fn($a, $b) => filemtime($b) <=> filemtime($a));

$open = (string)($_GET['open'] ?? '');
$body = '';
if ($open !== '' && is_file($dir . '/' . basename($open))) {
    $body = (string)file_get_contents($dir . '/' . basename($open));
}

page_top('WSYM - local mailbox', []);
?>
<div class="container py-4" style="max-width:900px">
    <h1 class="h4">Local mailbox</h1>
    <p class="text-muted">Every mail the app "sent" while <code>mailer_transport=file</code>. Newest first.
        Folder: <code><?php echo e($dir); ?></code> — <a href="mailbox.php">refresh</a>.</p>

    <?php if (!$files): ?>
        <div class="alert alert-secondary">No mail yet. Register an account or use "I forgot my password".</div>
    <?php endif; ?>

    <ul class="list-group mb-4">
        <?php foreach ($files as $i => $file): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span><?php echo e(date('Y-m-d H:i:s', filemtime($file))); ?> &middot; <?php echo e(basename($file)); ?></span>
                <a class="btn btn-sm btn-primary" href="mailbox.php?open=<?php echo urlencode(basename($file)); ?>">open</a>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php if ($body !== ''): ?>
        <div class="card p-3">
            <div class="d-flex justify-content-between mb-2">
                <b>Message</b>
                <a class="btn btn-sm btn-outline-secondary" href="mailbox.php">close</a>
            </div>
            <?php
            // the captured file is written by this app, but only the body is shown
            echo preg_replace('#<script\b[^>]*>.*?</script>#is', '', $body);
            ?>
        </div>
    <?php endif; ?>
</div>
<?php page_bottom(); ?>
