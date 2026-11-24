<?php
/**
 * admin_audit.php - read-only list of every admin action (lib/audit.php writes it).
 * Same sidebar and same grey card as the rest of the console.
 */
require __DIR__ . '/bootstrap.php';
$admin = require_login(true);

$filter = trim((string)($_GET['q'] ?? ''));
$rows = $filter === ''
      ? db_rows('SELECT actor, action, target, details, created FROM admin_audit ORDER BY id DESC LIMIT 300')
      : db_rows('SELECT actor, action, target, details, created FROM admin_audit
                 WHERE actor LIKE ? OR action LIKE ? OR target LIKE ? OR details LIKE ?
                 ORDER BY id DESC LIMIT 300',
                array_fill(0, 4, '%' . $filter . '%'));

$me = user_row($admin);
page_top('WSYM Banking - audit log', ['sidebars', 'welcome_admin']);
?>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300&family=Open+Sans+Condensed:wght@300&display=swap" rel="stylesheet">
<div class="d-flex adminwrap">
    <?php admin_sidebar('audit', $admin, $me['pfp'] ?? null); ?>

    <div class="tabarea">
        <section class="adminpanel d-flex flex-column" style="justify-content:flex-start; max-height:calc(100vh - 60px)">
            <h1 style="margin-top:12px">Audit log</h1>
            <p class="text-center" style="font-size:15px">
                Who changed what. Newest first, maximum 300 rows.
                The same list is filtered by typing an admin, an action or a client below.
            </p>

            <form method="get" class="d-flex gap-2 justify-content-center mb-3">
                <input type="text" class="bigsearch" name="q" style="flex:1; max-width:420px"
                       placeholder="Filter by admin, action or client" value="<?php echo e($filter); ?>">
                <input type="submit" class="but mb-0" style="width:120px" value="Filter">
            </form>

            <div class="adminlist" style="width:100%; overflow-y:auto; flex:0 1 auto; max-height:100%">
                <div class="adminrow head">
                    <span style="width:150px">When</span>
                    <span style="width:110px">Admin</span>
                    <span style="width:150px">Action</span>
                    <span style="width:130px">Client</span>
                    <span class="flex-grow-1">Details</span>
                </div>
                <?php if (!$rows): ?>
                    <div class="adminrow"><span class="muted" style="width:100%; text-align:center; padding:6px 0">
                        <?php echo $filter === '' ? 'Nothing recorded yet.' : 'No row matches that filter.'; ?>
                    </span></div>
                <?php endif; ?>
                <?php foreach ($rows as $row): ?>
                    <div class="adminrow">
                        <span style="width:150px; white-space:nowrap"><?php echo e(date('Y-m-d H:i:s', strtotime($row['created']))); ?></span>
                        <span style="width:110px"><b><?php echo e($row['actor']); ?></b></span>
                        <span style="width:150px"><code><?php echo e($row['action']); ?></code></span>
                        <span style="width:130px"><?php echo e($row['target']); ?></span>
                        <span class="flex-grow-1 muted"><?php echo e($row['details']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</div>
<?php page_bottom(); ?>
