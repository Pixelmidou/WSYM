<?php
/**
 * lib/db.php - the database, with four tiny helpers.
 *
 * Every query in this app goes through db_run() / db_rows() / db_row() / db_val().
 * They use prepared statements, so a value can never become SQL. That is the whole
 * reason there is no mysqli_* call anywhere else in the code.
 */

mysqli_report(MYSQLI_REPORT_OFF);          // this code checks return values, it never wants exceptions

/** The one connection. db() gives you the mysqli object (used for transactions). */
function db(): mysqli
{
    static $con = null;
    if ($con instanceof mysqli) {
        return $con;
    }

    $con = @new mysqli(env('database_hostname', 'localhost'), env('database_username', 'root'),
                       env('database_password', ''), env('database_name', 'wsym'));

    if ($con->connect_errno) {
        http_response_code(500);
        echo '<h2 style="font-family:sans-serif">Cannot reach the database</h2>';
        echo '<p style="font-family:sans-serif">' . htmlspecialchars($con->connect_error) . '</p>';
        echo '<p style="font-family:sans-serif">Check the <code>database_*</code> lines in your '
           . '<code>.env</code> (see <a href="check.php">check.php</a>).</p>';
        exit;
    }

    $con->set_charset('utf8mb4');
    return $con;
}

/** Turn ['abc', 5, 1.5] into ['s', 'i', 'd'] - mysqli wants the types as one string. */
function db_types(array $params): string
{
    $types = '';
    foreach ($params as $value) {
        $types .= is_int($value) ? 'i' : (is_float($value) ? 'd' : 's');
    }
    return $types;
}

/** Run a statement and hand back the mysqli_stmt (or false). */
function db_query(string $sql, array $params = [])
{
    $stmt = db()->prepare($sql);
    if ($stmt === false) {
        error_log('WSYM SQL: ' . db()->error . ' <- ' . $sql);
        return false;
    }
    if ($params) {
        $stmt->bind_param(db_types($params), ...$params);
    }
    if (!$stmt->execute()) {
        error_log('WSYM SQL: ' . $stmt->error . ' <- ' . $sql);
        return false;
    }
    return $stmt;
}

/** All rows, as arrays with column names as keys. */
function db_rows(string $sql, array $params = []): array
{
    $stmt = db_query($sql, $params);
    if (!$stmt) {
        return [];
    }
    $result = $stmt->get_result();
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

/** The first row, or null. */
function db_row(string $sql, array $params = []): ?array
{
    $rows = db_rows($sql, $params);
    return $rows[0] ?? null;
}

/** One value from the first row (first column), or $default. */
function db_val(string $sql, array $params = [], $default = null)
{
    $row = db_row($sql, $params);
    return $row === null ? $default : reset($row);
}

/** INSERT / UPDATE / DELETE. Returns the number of affected rows, or -1 on error. */
function db_run(string $sql, array $params = []): int
{
    $stmt = db_query($sql, $params);
    return $stmt === false ? -1 : $stmt->affected_rows;
}
