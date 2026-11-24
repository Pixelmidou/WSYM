<?php
/**
 * statement.php - the whole history as a CSV file that opens in Excel.
 * Optional ?from=YYYY-MM-DD&to=YYYY-MM-DD
 */
require __DIR__ . '/bootstrap.php';
$username = require_login();

$from = trim((string)($_GET['from'] ?? ''));
$to   = trim((string)($_GET['to'] ?? ''));
$rows = history_all($username);

$fromTime = $from !== '' ? strtotime($from . ' 00:00:00') : null;
$toTime   = $to !== ''   ? strtotime($to . ' 23:59:59')   : null;

$file = 'wsym-statement-' . preg_replace('/[^A-Za-z0-9_-]/', '_', $username) . '-' . date('Y-m-d') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $file . '"');

$out = fopen('php://output', 'w');
fwrite($out, "\xEF\xBB\xBF");                       // BOM so Excel reads UTF-8 correctly
fputcsv($out, ['Date', 'Type', 'Amount (USD)', 'Counterparty']);

$total = 0.0;
foreach ($rows as $row) {
    $time = strtotime($row['date']);
    if ($fromTime !== null && $time < $fromTime) { continue; }
    if ($toTime !== null && $time > $toTime) { continue; }
    $amount = (float)$row['amount'];
    $total += $row['kind'] === 'deposit' ? $amount : -$amount;
    fputcsv($out, [
        date('Y-m-d H:i:s', $time),
        ucfirst($row['kind']),
        number_format($amount, 2, '.', ''),
        $row['kind'] === 'wire'
            ? (string)db_val('SELECT email FROM login_credentials WHERE username = ?', [$row['receiver']], $row['receiver'])
            : ($row['kind'] === 'deposit' ? 'Your account' : 'Your account'),
    ]);
}
fputcsv($out, []);
fputcsv($out, ['Net change', '', number_format($total, 2, '.', ''), '']);
fputcsv($out, ['Balance now', '', number_format(balance_of($username), 2, '.', ''), '']);
fclose($out);
exit;
