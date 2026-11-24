<?php
/**
 * history.php - the customer's movements, newest first, with paging.
 * One page for deposits, withdrawals and wires. The total comes from COUNT(*), never
 * from the rows on screen, so the page numbers are always right.
 */
require __DIR__ . '/bootstrap.php';
$username = require_login();

$kinds = ['all' => 'Everything', 'deposit' => 'Deposits', 'withdraw' => 'Withdrawals', 'wire' => 'Wires'];
$kind  = array_key_exists($_GET['kind'] ?? '', $kinds) ? $_GET['kind'] : 'all';
$perPage = 10;
$p = pager(history_count($username, $kind), $perPage, (int)($_GET['pagenr'] ?? 1));
$rows = history_all($username, $kind, $perPage, $p['offset']);

page_top('WSYM Banking', ['welcome_admin_forms']);
?>
    <?php page_inside(); ?>
        <div class="container2">
            <h2 class="mb-2">Your history</h2>
            <div class="d-flex gap-2 mb-3">
                <?php foreach ($kinds as $key => $label): ?>
                    <a class="btn btn-sm <?php echo $key === $kind ? 'btn-primary' : 'btn-outline-secondary'; ?>"
                       href="history.php?kind=<?php echo $key; ?>"><?php echo $label; ?></a>
                <?php endforeach; ?>
                <a class="btn btn-sm btn-success" href="statement.php">Download CSV</a>
            </div>

            <?php if (!$rows): ?>
                <p class="text-center mb-3"><b>No transactions yet</b><br>
                   Nothing to show here yet &mdash; make a deposit, a withdrawal or a wire and it appears in this list.</p>
            <?php else: ?>
                <table class="table table-hover">
                    <thead>
                    <tr><th>Type</th><th>Date</th><th>Amount</th><th>Details</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?php echo ucfirst($row['kind']); ?></td>
                            <td><?php echo e(date('Y-m-d H:i:s', strtotime($row['date']))); ?></td>
                            <td>$<?php echo money((float)$row['amount']); ?></td>
                            <td><?php echo $row['kind'] === 'wire'
                                    ? 'to ' . e(db_val('SELECT email FROM login_credentials WHERE username = ?', [$row['receiver']], $row['receiver']))
                                    : ''; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="d-flex align-items-center gap-3">
                    <div>Showing page <?php echo $p['page']; ?> out of <?php echo $p['pages']; ?>
                        (<?php echo $p['total']; ?> movements)</div>
                    <div class="btn-group">
                        <a class="btn btn-sm btn-outline-secondary" href="history.php?kind=<?php echo $kind; ?>&pagenr=1">First</a>
                        <a class="btn btn-sm btn-outline-secondary" href="history.php?kind=<?php echo $kind; ?>&pagenr=<?php echo max(1, $p['page'] - 1); ?>">Previous</a>
                        <a class="btn btn-sm btn-outline-secondary" href="history.php?kind=<?php echo $kind; ?>&pagenr=<?php echo min($p['pages'], $p['page'] + 1); ?>">Next</a>
                        <a class="btn btn-sm btn-outline-secondary" href="history.php?kind=<?php echo $kind; ?>&pagenr=<?php echo $p['pages']; ?>">Last</a>
                    </div>
                </div>
            <?php endif; ?>

            <a class="btn btn-primary mt-3" href="welcome.php">Back to the welcome page</a>
        </div>
    <?php page_inside_end(); ?>
<?php page_bottom(); ?>
