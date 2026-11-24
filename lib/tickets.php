<?php
/**
 * lib/tickets.php - support as a conversation.
 *
 * There is ONE ticket row per customer (the primary key is the username). The row holds
 * the state of the current conversation; every message carries the number of the
 * conversation it belongs to, so "close, then start a new one" keeps the earlier
 * messages readable instead of deleting them.
 *
 *   ticket          username, status, subject, time_opened, time_closed, conversation
 *   ticket_message  username, sender, is_admin, body, sent_at, conversation
 */

/** The current conversation of this user (creates nothing). */
function thread_of(string $username): ?array
{
    return db_row('SELECT username, status, subject, time_opened, time_closed, conversation
                   FROM ticket WHERE username = ?', [$username]);
}

/** The messages of one conversation (default: the current one), oldest first. */
function thread_messages(string $username, ?int $conversation = null): array
{
    if ($conversation === null) {
        $thread = thread_of($username);
        $conversation = (int)($thread['conversation'] ?? 1);
    }
    return db_rows('SELECT sender, is_admin, body, sent_at FROM ticket_message
                    WHERE username = ? AND conversation = ? ORDER BY sent_at, id',
                   [$username, $conversation]);
}

/** The conversation before the current one, or null when there is none. */
function previous_conversation(string $username): ?array
{
    $thread = thread_of($username);
    if ($thread === null || (int)$thread['conversation'] <= 1) {
        return null;
    }
    $number = (int)$thread['conversation'] - 1;
    $messages = thread_messages($username, $number);
    return $messages ? ['conversation' => $number, 'messages' => $messages] : null;
}

/**
 * Write a message. Opens the conversation, or re-opens the closed one as a NEW
 * conversation (number + 1), which is what keeps the earlier messages readable.
 * Returns 'ok' | 'empty' | 'closed' (an admin cannot start somebody's conversation).
 */
/** A one-line subject for a new conversation: the first 120 characters of the message.
 *  preg with /u counts characters, so it still works on a host without mbstring. */
function short_text(string $text, int $limit = 120): string
{
    $flat = trim(preg_replace('/\s+/', ' ', $text));
    if (preg_match('/^.{0,' . $limit . '}/us', $flat, $m)) {
        return $m[0];
    }
    return substr($flat, 0, $limit);
}

function thread_post(string $username, string $sender, string $body, bool $isAdmin = false): string
{
    $body = trim($body);
    if ($body === '') {
        return 'empty';
    }

    $thread = thread_of($username);

    if ($thread === null) {
        if ($isAdmin) {
            return 'closed';
        }
        $subject = short_text($body);
        db_run("INSERT INTO ticket (username, status, subject, last_message, conversation)
                VALUES (?, 'opened', ?, NOW(6), 1)", [$username, $subject]);
    } elseif ($thread['status'] !== 'opened') {
        if ($isAdmin) {
            return 'closed';
        }
        $subject = short_text($body);
        db_run("UPDATE ticket SET status = 'opened', time_closed = NULL, subject = ?,
                       time_opened = NOW(6), conversation = conversation + 1
                WHERE username = ?", [$subject, $username]);
    }

    $thread = thread_of($username);
    db_run('INSERT INTO ticket_message (username, sender, is_admin, body, conversation)
            VALUES (?, ?, ?, ?, ?)',
           [$username, $sender, $isAdmin ? 1 : 0, $body, (int)($thread['conversation'] ?? 1)]);
    db_run('UPDATE ticket SET last_message = NOW(6) WHERE username = ?', [$username]);

    return 'ok';
}

/** End the current conversation. false when it was already closed. */
function thread_close(string $username): bool
{
    return db_run("UPDATE ticket SET status = 'closed', time_closed = NOW(6)
                   WHERE username = ? AND status = 'opened'", [$username]) === 1;
}

/** Every thread, most recent activity first. Used by the admin inbox. */
function thread_list(string $status = 'all'): array
{
    $sql = "SELECT t.username, t.status, t.subject, t.time_opened, t.time_closed, t.last_message,
                   (SELECT COUNT(*) FROM ticket_message m
                     WHERE m.username = t.username AND m.conversation = t.conversation) AS messages,
                   (SELECT m.is_admin FROM ticket_message m
                     WHERE m.username = t.username AND m.conversation = t.conversation
                     ORDER BY m.sent_at DESC, m.id DESC LIMIT 1) AS last_from
            FROM ticket t WHERE 1 = 1";
    $params = [];
    if ($status === 'opened' || $status === 'closed') {
        $sql .= ' AND t.status = ?';
        $params[] = $status;
    }
    $sql .= ' ORDER BY t.last_message DESC';
    return db_rows($sql, $params);
}

/** How many conversations nobody has answered yet (dashboard badge). */
function thread_waiting(): int
{
    return (int)db_val("SELECT COUNT(*) FROM ticket t
                        WHERE t.status = 'opened'
                          AND (SELECT m.is_admin FROM ticket_message m
                                WHERE m.username = t.username AND m.conversation = t.conversation
                                ORDER BY m.sent_at DESC, m.id DESC LIMIT 1) = 0", [], 0);
}

/** "3 min ago" - small helper for the message list. */
function ago(string $when): string
{
    $ts = strtotime($when);
    if ($ts === false) {
        return $when;
    }
    $diff = time() - $ts;
    if ($diff < 60)    return 'just now';
    if ($diff < 3600)  return (int)floor($diff / 60) . ' min ago';
    if ($diff < 86400) return (int)floor($diff / 3600) . ' h ago';
    if ($diff < 604800) {
        $d = (int)floor($diff / 86400);
        return $d . ($d > 1 ? ' days ago' : ' day ago');
    }
    return date('Y-m-d', $ts);
}
