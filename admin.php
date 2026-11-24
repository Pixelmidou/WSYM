<?php
/**
 * admin.php - the admin console. Seven tabs, one file, no copy-pasted query blocks.
 *
 *   Home            how many clients, how much money, what moved today
 *   Balances        find a client, change their balance
 *   Transactions    deposits / withdrawals / wires of one client
 *   Actions Blocking   allow or block deposit, withdraw, wire, ticket
 *   Accounts Blocking  enable or disable a whole account
 *   Info management    verify, reset password, end session, rename
 *   Tickets            the customer conversations, answer and close
 *
 * Every action posts back to this file with ?tab=... so the console stays on the tab you
 * were using, and every one of them is written to the audit log.
 */
require __DIR__ . '/bootstrap.php';
$admin = require_login(true);

$tabs = ['home', 'balances', 'transactions', 'blockactions', 'blockaccount', 'infomanagement', 'tickets'];
$tab = in_array($_GET['tab'] ?? '', $tabs, true) ? $_GET['tab'] : 'home';
$note = '';

/* ============================================================== the actions */
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $back = 'admin.php?tab=' . $tab;

    // ---- change a balance
    if (isset($_POST['balance_save'])) {
        $who = trim((string)($_POST['username'] ?? ''));
        $value = parse_amount($_POST['bval'] ?? '');
        if ($value === null && trim((string)($_POST['bval'] ?? '')) === '0') { $value = 0.0; }
        if ($value === null) {
            $note = 'Enter a positive amount (or 0) to set the balance to.';
        } elseif (db_row('SELECT username FROM balance WHERE username = ?', [$who]) === null) {
            $note = 'No such client.';
        } else {
            db_run('UPDATE balance SET balance = ? WHERE username = ?', [$value, $who]);
            audit('balance.set', $who, 'balance = ' . money($value));
            show_result('Action Success : Balance updated', e($who) . ' now has $' . money($value) . '.', $back, 4, true);
        }
    }

    // ---- allow / block one action
    $buttons = [
        'block_dep_submit'    => 'deposit',
        'block_with_submit'   => 'withdraw',
        'block_wire_submit'   => 'wire',
        'block_ticket_submit' => 'ticket',
    ];
    foreach ($buttons as $button => $field) {
        if (isset($_POST[$button])) {
            $who    = trim((string)($_POST['blockusername'] ?? ''));
            $email  = trim((string)($_POST['blockemail'] ?? ''));
            $client = $who !== ''
                    ? db_row('SELECT username FROM login_credentials WHERE username = ?', [$who])
                    : db_row('SELECT username FROM login_credentials WHERE email = ?', [$email]);
            $allow = ($_POST['actions'] ?? 'allow') === 'allow';
            if ($client === null) {
                $note = 'That client does not exist (the email or username must be exact here).';
            } else {
                db_run("UPDATE blacklist SET `$field` = ? WHERE username = ?", [$allow ? 1 : 0, $client['username']]);
                audit('perm.' . ($allow ? 'allow' : 'block'), $client['username'], $field);
                show_result('Action Success : ' . ucfirst($field) . ($allow ? ' allowed' : ' blocked'),
                            e($client['username']) . ' can ' . ($allow ? '' : 'no longer ') . $field . '.', $back, 4, true);
            }
        }
    }

    // ---- enable / disable an account
    if (isset($_POST['accsub'])) {
        $who    = trim((string)($_POST['accuser'] ?? ''));
        $email  = trim((string)($_POST['accmail'] ?? ''));
        $client = $who !== ''
                ? db_row('SELECT username FROM login_credentials WHERE username = ?', [$who])
                : db_row('SELECT username FROM login_credentials WHERE email = ?', [$email]);
        $enable = ($_POST['accactions'] ?? 'enable') === 'enable';
        if ($client === null) {
            $note = 'That client does not exist (the email or username must be exact here).';
        } else {
            db_run('UPDATE blacklist SET account = ? WHERE username = ?', [$enable ? 1 : 0, $client['username']]);
            audit('account.' . ($enable ? 'enable' : 'disable'), $client['username']);
            show_result('Action Success : Account ' . ($enable ? 'enabled' : 'disabled'),
                        e($client['username']) . ' can ' . ($enable ? '' : 'no longer ') . 'log in.', $back, 4, true);
        }
    }

    // ---- tickets: reply and close
    if (isset($_POST['admin_reply_ticket'])) {
        $who = trim((string)($_POST['ticket_user'] ?? ''));
        if (thread_post($who, $admin, (string)($_POST['reply_text'] ?? ''), true) === 'ok') {
            audit('ticket.reply', $who);
            redirect('admin.php?tab=tickets&open=' . urlencode($who));
        }
        $note = 'Write a reply first.';
    }
    if (isset($_POST['admin_close_ticket'])) {
        $who = trim((string)($_POST['ticket_user'] ?? ''));
        thread_close($who);
        audit('ticket.close', $who);
        redirect('admin.php?tab=tickets');
    }

    // ---- info management
    if (isset($_POST['info_action'])) {
        $who    = trim((string)($_POST['info_user'] ?? ''));
        $client = db_row('SELECT username, email FROM login_credentials WHERE username = ?', [$who]);
        if ($client === null) {
            $note = 'No such client.';
        }
        $action = (string)$_POST['info_action'];

        if ($client !== null && $action === 'reverify') {
            $link = account_force_reverify($who);
            audit('info.reverify', $who);
            show_result('Account marked unverified', 'Give this link to the client (valid 24 h):<br>'
                . '<input style="width:100%" value="' . e((string)$link) . '">', $back, 30, true);
        }
        if ($client !== null && $action === 'resetpass') {
            $link = account_force_reset($who);
            audit('info.resetpass', $who);
            show_result('Password reset link created', 'Single use, valid 1 h:<br>'
                . '<input style="width:100%" value="' . e((string)$link) . '">', $back, 30, true);
        }
        if ($client !== null && $action === 'end_session') {
            account_end_session($who);
            audit('info.end_session', $who);
            show_result('Session ended', e($who) . ' is signed out on their next click.', $back, 4, true);
        }
        if ($client !== null && $action === 'rename') {
            switch (account_rename($who, (string)($_POST['new_username'] ?? ''))) {
                case 'ok':
                    audit('info.rename', $who, 'renamed to ' . trim((string)$_POST['new_username']));
                    show_result('Account renamed', e($who) . ' is now renamed to ' . e(trim((string)$_POST['new_username'])) . '.',
                                'admin.php?tab=infomanagement&iq=' . urlencode(trim((string)$_POST['new_username'])), 5, true);
                case 'taken':     $note = 'That username is already taken.'; break;
                case 'invalid':   $note = 'Usernames are 3-30 characters (letters, digits, dot, dash, underscore).'; break;
                case 'unchanged': $note = 'That is already their username.'; break;
                default:          $note = 'The rename failed, nothing was changed.';
            }
        }
    }
}

/* ============================================================ tab contents */
$stats = [
    'clients' => (int)db_val('SELECT COUNT(*) FROM login_credentials', [], 0),
    'money'   => (float)db_val('SELECT COALESCE(SUM(balance), 0) FROM balance', [], 0),
    'deposit' => (int)db_val('SELECT COUNT(*) FROM deposit WHERE deposit_date >= NOW() - INTERVAL 1 DAY', [], 0),
    'withdraw'=> (int)db_val('SELECT COUNT(*) FROM withdraw WHERE withdraw_date >= NOW() - INTERVAL 1 DAY', [], 0),
    'wire'    => (int)db_val('SELECT COUNT(*) FROM wire WHERE wire_date >= NOW() - INTERVAL 1 DAY', [], 0),
];

// balances / transactions share the same search box
$searchUser  = trim((string)($_POST['balanceusername'] ?? $_POST['transusername'] ?? $_GET['q'] ?? ''));
$searchEmail = trim((string)($_POST['balanceemail'] ?? $_POST['transemail'] ?? ''));
$matches = [];
if ($tab === 'balances' && ($searchUser !== '' || $searchEmail !== '' || isset($_POST['balance_submit']))) {
    $like = '%' . ($searchUser !== '' ? $searchUser : $searchEmail) . '%';
    $matches = db_rows('SELECT l.username, l.email, b.balance FROM login_credentials l
                        LEFT JOIN balance b ON b.username = l.username
                        WHERE l.username LIKE ? OR l.email LIKE ? ORDER BY l.username LIMIT 20',
                       [$like, $like]);
}

$ledger = null;
if ($tab === 'transactions') {
    foreach (['deposit', 'withdraw', 'wire'] as $kind) {
        if (isset($_POST[$kind . '_transactions_submit'])) {
            $ledger = ['kind' => $kind, 'rows' => history_search($kind, $searchUser !== '' ? $searchUser : $searchEmail, 20, 0)];
        }
    }
}

// info management: the client list, or one client
$infoQuery = trim((string)($_GET['iq'] ?? ''));
$clients = db_rows('SELECT l.username, l.email, l.email_verif, b.balance
                    FROM login_credentials l LEFT JOIN balance b ON b.username = l.username
                    WHERE ? = "" OR l.username LIKE ? OR l.email LIKE ?
                    ORDER BY l.username LIMIT 40',
                   [$infoQuery, '%' . $infoQuery . '%', '%' . $infoQuery . '%']);
$focus = $infoQuery !== '' ? db_row('SELECT l.username, l.email, l.email_verif, b.balance
                                     FROM login_credentials l LEFT JOIN balance b ON b.username = l.username
                                     WHERE l.username = ?', [$infoQuery]) : null;

// tickets
$status = in_array($_GET['tstatus'] ?? '', ['opened', 'closed'], true) ? $_GET['tstatus'] : 'all';
$threads = $tab === 'tickets' ? thread_list($status) : [];
$openWith = (string)($_GET['open'] ?? '');
$openMessages = $openWith !== '' ? thread_messages($openWith) : [];

$me = user_row($admin);
$waiting = thread_waiting();

/* small helpers so the markup below stays readable */
$panel = function (string $extra, string $title, string $blurb = '') {
    echo '<section class="adminpanel ' . $extra . ' d-flex flex-column justify-content-center align-items-center">';
    echo '<h1>' . e($title) . '</h1>';
    if ($blurb !== '') {
        echo '<p class="lead">' . $blurb . '</p>';
    }
};
$panel_end = function () { echo '</section>'; };
$mail_icon = '<img src="' . url('data/mail.svg') . '" alt="">';
$user_icon = '<img src="' . url('data/user.svg') . '" alt="">';

page_top('WSYM Banking - admin', ['sidebars', 'welcome_admin']);
?>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300&family=Open+Sans+Condensed:wght@300&display=swap" rel="stylesheet">
<div class="d-flex adminwrap">
    <?php admin_sidebar($tab, $admin, $me['pfp'] ?? null); ?>

    <div class="tabarea">
        <?php // one token for every form: copied into any form that was built without one ?>
        <form id="csrf-slot" style="display:none"><?php echo csrf_field(); ?></form>
        <script>
            window.addEventListener('load', function () {
                var token = document.querySelector('#csrf-slot input').value;
                Array.prototype.forEach.call(document.querySelectorAll('form'), function (form) {
                    if (form.id === 'csrf-slot') { return; }
                    if (form.querySelector('input[name="csrf"]')) { return; }
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'csrf';
                    input.value = token;
                    form.appendChild(input);
                });
            });
        </script>

        <?php if ($tab === 'home'): ?>
            <?php $panel('homeseccont', 'Welcome Back'); ?>
                <div class="chips">
                    <span class="push">Clients Number : <?php echo $stats['clients']; ?></span>
                    <span class="push">Stored Money : $<?php echo money($stats['money']); ?></span>
                </div>
                <div class="mt-4 text-center" style="font-size:16px">
                    <div>In the last 24 Hours, there has been <?php echo $stats['deposit']; ?> deposits</div>
                    <div>In the last 24 Hours, there has been <?php echo $stats['withdraw']; ?> withdraws</div>
                    <div>In the last 24 Hours, there has been <?php echo $stats['wire']; ?> wires</div>
                    <div class="mt-2"><a href="admin.php?tab=tickets"><?php echo $waiting; ?> conversation(s) waiting for an answer</a></div>
                </div>
            <?php $panel_end(); ?>
        <?php endif; ?>

        <?php if ($tab === 'balances'): ?>
            <?php $panel('balancecont', 'Check/Edit Balances',
                'Insert the email or the username of the person you want to check/edit the money of (information doesn\'t have to be precise)<br>(at least one of the inputs needs to be filled)'); ?>
                <form method="post" action="admin.php?tab=balances" style="width:100%; max-width:700px">
                    <?php echo csrf_field(); ?>
                    <label class="labbor lab d-flex align-items-center mx-auto"><?php echo $mail_icon; ?>
                        <input type="text" placeholder="Email without (@example.ex)" name="balanceemail"></label>
                    <label class="labbor lab d-flex align-items-center mx-auto"><?php echo $user_icon; ?>
                        <input type="text" placeholder="Username" name="balanceusername"></label>
                    <input type="submit" value="Search" class="but d-block mx-auto" style="width:300px" name="balance_submit">
                </form>
                <?php if ($matches): ?>
                    <div class="adminlist mt-3">
                        <?php foreach ($matches as $c): ?>
                            <div class="adminrow">
                                <div class="flex-grow-1">
                                    <b><?php echo e($c['username']); ?></b>
                                    <span class="muted"><?php echo e($c['email']); ?></span>
                                </div>
                                <form method="post" action="admin.php?tab=balances" class="d-flex gap-2 align-items-center">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="username" value="<?php echo e($c['username']); ?>">
                                    <span class="amount">$<?php echo money((float)$c['balance']); ?></span>
                                    <input type="text" class="smallfield" name="bval" value="<?php echo money((float)$c['balance']); ?>"
                                           aria-label="New balance for <?php echo e($c['username']); ?>">
                                    <input type="submit" class="minibut" value="Save" name="balance_save">
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php elseif ($searchUser !== '' || $searchEmail !== ''): ?>
                    <p class="mt-3">No client matches that search.</p>
                <?php endif; ?>
            <?php $panel_end(); ?>
        <?php endif; ?>

        <?php if ($tab === 'transactions'): ?>
            <?php $panel('transactionscont', 'Check Transactions',
                'Search a client\'s deposits, withdrawals or wires (information doesn\'t have to be precise)'); ?>
                <form method="post" action="admin.php?tab=transactions" style="width:100%; max-width:760px">
                    <?php echo csrf_field(); ?>
                    <label class="labbor lab d-flex align-items-center mx-auto"><?php echo $mail_icon; ?>
                        <input type="text" placeholder="Email without (@example.ex)" name="transemail"></label>
                    <label class="labbor lab d-flex align-items-center mx-auto"><?php echo $user_icon; ?>
                        <input type="text" placeholder="Username" name="transusername"></label>
                    <div class="d-flex gap-2 justify-content-center mt-2">
                        <input type="submit" value="Deposit" class="but" style="width:150px" name="deposit_transactions_submit">
                        <input type="submit" value="Withdraw" class="but" style="width:150px" name="withdraw_transactions_submit">
                        <input type="submit" value="Wire" class="but" style="width:150px" name="wire_transactions_submit">
                    </div>
                </form>
                <?php if ($ledger !== null): ?>
                    <h2 class="h5 mt-4"><?php echo ucfirst($ledger['kind']); ?> results (<?php echo count($ledger['rows']); ?>)</h2>
                    <div class="adminlist">
                        <div class="adminrow head"><span>Username</span><span>Date</span><span>Amount</span><?php if ($ledger['kind'] === 'wire'): ?><span>To</span><?php endif; ?></div>
                        <?php if (!$ledger['rows']): ?>
                            <div class="adminrow"><span class="muted">Nothing matches that search.</span></div>
                        <?php endif; ?>
                        <?php foreach ($ledger['rows'] as $row): ?>
                            <div class="adminrow">
                                <span class="flex-grow-1"><b><?php echo e($row['username']); ?></b></span>
                                <span><?php echo e(date('Y-m-d H:i:s', strtotime($row['moved_at']))); ?></span>
                                <span class="amount">$<?php echo money((float)$row['amount']); ?></span>
                                <?php if ($ledger['kind'] === 'wire'): ?><span class="muted"><?php echo e($row['receiver']); ?></span><?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php $panel_end(); ?>
        <?php endif; ?>

        <?php if ($tab === 'blockactions'): ?>
            <?php $panel('blockactionscont', 'Allow / Block Actions',
                'The email or username MUST be exact here. Pick Allow or Block, then the action.'); ?>
                <form method="post" action="admin.php?tab=blockactions" style="width:100%; max-width:700px">
                    <?php echo csrf_field(); ?>
                    <label class="labbor lab d-flex align-items-center mx-auto"><?php echo $mail_icon; ?>
                        <input type="text" placeholder="Exact Email" name="blockemail"></label>
                    <label class="labbor lab d-flex align-items-center mx-auto"><?php echo $user_icon; ?>
                        <input type="text" placeholder="Exact Username" name="blockusername"></label>
                    <div class="text-center mb-2">
                        <label class="me-4"><input type="radio" name="actions" value="allow" checked> Allow</label>
                        <label><input type="radio" name="actions" value="block"> Block</label>
                    </div>
                    <div class="d-flex gap-2 justify-content-center">
                        <input type="submit" value="Deposit" class="but" style="width:140px" name="block_dep_submit">
                        <input type="submit" value="Withdraw" class="but" style="width:140px" name="block_with_submit">
                        <input type="submit" value="Wire" class="but" style="width:140px" name="block_wire_submit">
                        <input type="submit" value="Ticket" class="but" style="width:140px" name="block_ticket_submit">
                    </div>
                </form>
            <?php $panel_end(); ?>
        <?php endif; ?>

        <?php if ($tab === 'blockaccount'): ?>
            <?php $panel('blockaccountcont', 'Disable / Enable account',
                'A disabled account cannot log in at all. The email or username MUST be exact.'); ?>
                <form method="post" action="admin.php?tab=blockaccount" style="width:100%; max-width:700px">
                    <?php echo csrf_field(); ?>
                    <label class="labbor lab d-flex align-items-center mx-auto"><?php echo $mail_icon; ?>
                        <input type="text" placeholder="Exact Email" name="accmail"></label>
                    <label class="labbor lab d-flex align-items-center mx-auto"><?php echo $user_icon; ?>
                        <input type="text" placeholder="Exact Username" name="accuser"></label>
                    <div class="text-center mb-2">
                        <label class="me-4"><input type="radio" name="accactions" value="enable" checked> Enable</label>
                        <label><input type="radio" name="accactions" value="disable"> Disable</label>
                    </div>
                    <input type="submit" value="Apply" class="but d-block mx-auto" style="width:300px" name="accsub">
                </form>
            <?php $panel_end(); ?>
        <?php endif; ?>

        <?php if ($tab === 'infomanagement'): ?>
            <?php $panel('infomanagmentcont', 'Info managment',
                'Look a client up, then change the things an administrator is allowed to change.<br>Every action here is written to the <a href="admin_audit.php">audit log</a>.'); ?>
                <form method="get" action="admin.php" class="d-flex gap-2 justify-content-center mb-3" style="width:100%; max-width:700px">
                    <input type="hidden" name="tab" value="infomanagement">
                    <input type="text" class="bigsearch" style="flex:1" name="iq" placeholder="Username or email" value="<?php echo e($infoQuery); ?>">
                    <input type="submit" class="but mb-0" style="width:130px" value="Search">
                    <a class="showing" href="admin.php?tab=infomanagement">show all (<?php echo $stats['clients']; ?>)</a>
                </form>

                <?php if ($focus !== null): ?>
                    <div class="adminlist mb-3" style="width:100%">
                        <div class="adminrow">
                            <span class="flex-grow-1">
                                <b><?php echo e($focus['username']); ?></b>
                                <span class="muted"><?php echo e($focus['email']); ?></span>
                                <?php if ((int)$focus['email_verif'] !== 1): ?><span class="tag">unverified</span><?php endif; ?>
                            </span>
                            <span class="amount">$<?php echo money((float)$focus['balance']); ?></span>
                        </div>
                        <div class="adminrow wrap">
                            <?php foreach (['reverify' => 'Force re-verification', 'resetpass' => 'Force password reset',
                                            'end_session' => 'End session'] as $action => $label): ?>
                                <form method="post" action="admin.php?tab=infomanagement">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="info_user" value="<?php echo e($focus['username']); ?>">
                                    <input type="hidden" name="info_action" value="<?php echo $action; ?>">
                                    <input type="submit" class="minibut" value="<?php echo $label; ?>">
                                </form>
                            <?php endforeach; ?>
                            <form method="post" action="admin.php?tab=infomanagement" class="d-flex gap-2">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="info_user" value="<?php echo e($focus['username']); ?>">
                                <input type="hidden" name="info_action" value="rename">
                                <input type="text" class="smallfield" name="new_username" placeholder="New username" required>
                                <input type="submit" class="minibut" value="Rename">
                            </form>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="muted mb-1" style="align-self:flex-start; width:100%"><?php echo count($clients); ?> clients</div>
                <div class="adminlist" style="width:100%">
                    <?php foreach ($clients as $c): ?>
                        <div class="adminrow">
                            <span class="flex-grow-1">
                                <b><?php echo e($c['username']); ?></b>
                                <span class="muted"><?php echo e($c['email']); ?></span>
                                <?php if ((int)$c['email_verif'] !== 1): ?><span class="tag">unverified</span><?php endif; ?>
                            </span>
                            <span class="amount">$<?php echo money((float)$c['balance']); ?></span>
                            <a class="minibut" href="admin.php?tab=infomanagement&iq=<?php echo urlencode($c['username']); ?>">Open</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php $panel_end(); ?>
        <?php endif; ?>

        <?php if ($tab === 'tickets'): ?>
            <?php $panel('ticketscont', 'Tickets',
                $waiting . ' conversation(s) waiting for an answer. Answering keeps a conversation open, closing it lets the customer start a new one.'); ?>
                <div class="d-flex gap-2 mb-3">
                    <?php foreach (['opened' => 'Open', 'closed' => 'Closed', 'all' => 'All'] as $key => $label): ?>
                        <a class="minibut <?php echo $status === $key ? 'is-on' : ''; ?>"
                           href="admin.php?tab=tickets&tstatus=<?php echo $key; ?>"><?php echo $label; ?></a>
                    <?php endforeach; ?>
                </div>

                <?php if ($openWith !== ''): ?>
                    <div class="adminlist mb-3" style="width:100%">
                        <div class="adminrow head">Conversation with <?php echo e($openWith); ?></div>
                        <div class="adminrow thread">
                            <?php if (!$openMessages): ?><span class="muted">No messages.</span><?php endif; ?>
                            <?php foreach ($openMessages as $m): ?>
                                <?php $mine = (int)$m['is_admin'] === 1; ?>
                                <div class="bubble <?php echo $mine ? 'bubble-me' : 'bubble-them'; ?>">
                                    <span class="bubble-who"><?php echo $mine ? 'Support' : e($m['sender']); ?></span>
                                    <div class="bubble-body"><?php echo nl2br(e($m['body'])); ?></div>
                                    <span class="bubble-when"><?php echo ago($m['sent_at']); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <form method="post" action="admin.php?tab=tickets" class="adminrow wrap">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="ticket_user" value="<?php echo e($openWith); ?>">
                            <textarea name="reply_text" rows="3" class="admintextarea" placeholder="Write your answer"></textarea>
                            <div class="d-flex gap-2 mt-2">
                                <input type="submit" class="minibut" value="Send reply" name="admin_reply_ticket">
                                <input type="submit" class="minibut" value="Close conversation" name="admin_close_ticket" formnovalidate>
                                <a class="minibut" href="admin.php?tab=tickets">Back to the list</a>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>

                <div class="adminlist" style="width:100%">
                    <?php if (!$threads): ?>
                        <div class="adminrow"><span class="muted">No <?php echo $status === 'all' ? '' : $status . ' '; ?>conversations.</span></div>
                    <?php endif; ?>
                    <?php foreach ($threads as $t): ?>
                        <div class="adminrow">
                            <span class="flex-grow-1">
                                <b><?php echo e($t['username']); ?></b>
                                <span class="tag <?php echo $t['status'] === 'opened' ? 'tag-open' : ''; ?>"><?php echo $t['status'] === 'opened' ? 'open' : 'closed'; ?></span>
                                <?php if ($t['status'] === 'opened' && (int)$t['last_from'] === 0): ?><span class="tag tag-wait">waiting for us</span><?php endif; ?>
                                <div class="muted" style="font-size:14px"><?php echo e($t['subject'] !== '' ? $t['subject'] : '(no subject)'); ?>
                                    &middot; <?php echo (int)$t['messages']; ?> message(s) &middot; last activity <?php echo ago($t['last_message']); ?></div>
                            </span>
                            <a class="minibut" href="admin.php?tab=tickets&tstatus=<?php echo $status; ?>&open=<?php echo urlencode($t['username']); ?>">Open conversation</a>
                            <?php if ($t['status'] === 'opened'): ?>
                                <form method="post" action="admin.php?tab=tickets">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="ticket_user" value="<?php echo e($t['username']); ?>">
                                    <input type="submit" class="minibut" value="Close" name="admin_close_ticket">
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php $panel_end(); ?>
        <?php endif; ?>

    </div>
</div>
<?php if ($note !== ''): ?>
    <div class="adminnote" style="position:fixed;bottom:0;left:260px;right:0"><?php echo e($note); ?></div>
<?php endif; ?>
<?php page_bottom(); ?>
