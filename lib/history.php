<?php
/**
 * lib/history.php - deposits, withdrawals and wires in one list.
 *
 * The three tables keep their own column names, but everything that reads them goes
 * through history_all(), which returns one uniform shape - so a deposit, a withdrawal
 * and a wire can be listed by the same loop.
 */

/** Column names per table, so one function can read all three. */
function history_tables(): array
{
    return [
        'deposit'  => ['table' => 'deposit',  'date' => 'deposit_date',  'amount' => 'deposit_amount'],
        'withdraw' => ['table' => 'withdraw', 'date' => 'withdraw_date', 'amount' => 'withdraw_amount'],
        'wire'     => ['table' => 'wire',     'date' => 'wire_date',     'amount' => 'wire_amount'],
    ];
}

/**
 * The movements of one user, newest first.
 * Returns rows: ['kind' => 'deposit', 'date' => '2026-11-23 10:00:00', 'amount' => '25.00', 'receiver' => '']
 */
function history_all(string $username, string $kind = 'all', int $limit = 0, int $offset = 0): array
{
    $rows = [];
    foreach (history_tables() as $name => $t) {
        if ($kind !== 'all' && $kind !== $name) {
            continue;
        }
        $extra = $name === 'wire' ? ', receiver' : '';
        foreach (db_rows("SELECT {$t['date']} AS moved_at, {$t['amount']} AS amount{$extra}
                          FROM {$t['table']} WHERE username = ?", [$username]) as $row) {
            $rows[] = [
                'kind'     => $name,
                'date'     => $row['moved_at'],
                'amount'   => $row['amount'],
                'receiver' => $row['receiver'] ?? '',
            ];
        }
    }
    usort($rows, fn($a, $b) => strcmp($b['date'], $a['date']));
    return $limit > 0 ? array_slice($rows, $offset, $limit) : array_slice($rows, $offset);
}

/** How many movements a user has (drives the pager: counts rows, never the page of rows). */
function history_count(string $username, string $kind = 'all'): int
{
    $total = 0;
    foreach (history_tables() as $name => $t) {
        if ($kind !== 'all' && $kind !== $name) {
            continue;
        }
        $total += (int)db_val("SELECT COUNT(*) FROM {$t['table']} WHERE username = ?", [$username], 0);
    }
    return $total;
}

/**
 * Admin search: movements of everybody whose username or email contains $text.
 * Prepared statements only, so ' OR '1'='1 returns nothing.
 */
function history_search(string $kind, string $text, int $limit = 20, int $offset = 0): array
{
    $t = history_tables()[$kind] ?? null;
    if ($t === null) {
        return [];
    }
    $like = '%' . $text . '%';
    $receiver = $kind === 'wire' ? ', w.receiver' : '';
    $sql = "SELECT w.username, w.{$t['date']} AS moved_at, w.{$t['amount']} AS amount{$receiver}
            FROM {$t['table']} w
            JOIN login_credentials l ON l.username = w.username
            WHERE (? = '' OR l.username LIKE ? OR l.email LIKE ?)
            ORDER BY w.{$t['date']} DESC
            LIMIT ? OFFSET ?";
    return db_rows($sql, [$text, $like, $like, $limit, $offset]);
}

/** Rows for the previous/next pager. Small on purpose. */
function pager(int $total, int $perPage, int $page): array
{
    $pages = max(1, (int)ceil($total / $perPage));
    $page  = max(1, min($page, $pages));
    return ['page' => $page, 'pages' => $pages, 'offset' => ($page - 1) * $perPage, 'total' => $total];
}
