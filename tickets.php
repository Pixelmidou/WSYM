<?php
/**
 * tickets.php - the customer's conversation with support.
 * Chat bubbles, reply in place, close, and the previous conversation stays readable.
 */
require __DIR__ . '/bootstrap.php';
$username = require_login();

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (isset($_POST['close_ticket'])) {
        thread_close($username);
        redirect('tickets.php');
    }
    if (isset($_POST['send_message']) || isset($_POST['message_text'])) {
        if (!allowed('ticket', $username)) {
            show_result('Action Failed : You are blocked from messaging support', '', 'tickets.php', 4, false);
        }
        $result = thread_post($username, $username, (string)($_POST['message_text'] ?? ''));
        if ($result !== 'ok') {
            show_result('Action Failed : Empty message', 'Write something before sending.', 'tickets.php', 4, false);
        }
        redirect('tickets.php');
    }
}

$thread   = thread_of($username);
$messages = $thread ? thread_messages($username) : [];
$previous = previous_conversation($username);
$open     = $thread !== null && $thread['status'] === 'opened';
$me       = user_row($username);

page_top('WSYM Banking', ['welcome_style']);
?>
    <header>
        <nav class="container3">
            <ul class="container2">
                <img src="<?php echo url('data/favicon.ico'); ?>" alt="" width="50px" height="50px">
                <li>WSYM Banking</li>
            </ul>
            <ul class="container1">
                <li class="navitem"><a href="welcome.php" style="text-decoration:none;color:inherit">Back to the welcome page</a></li>
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle dr" data-bs-toggle="dropdown">
                    <img src="<?php echo avatar_url($me['pfp'] ?? null); ?>" alt="pfp" width="32" height="32" class="rounded-circle me-2" style="object-fit:cover">
                    <strong><?php echo e($username); ?></strong>
                </a>
            </ul>
        </nav>
    </header>
    <main>
        <div class="container4" style="height:auto; min-height:418px">
            <div class="d-flex w-100 align-items-start justify-content-between">
                <div>
                    <h1 style="font-size:26px">Messages with support</h1>
                    <div style="font-size:14px">One conversation at a time. Write as much as you need here &mdash;
                        support answers in the same place.</div>
                </div>
                <span class="badge <?php echo $open ? 'bg-success' : 'bg-secondary'; ?>">
                    <?php echo $open ? 'open' : 'no open conversation'; ?>
                </span>
            </div>

            <div class="thread" style="width:100%; max-height:320px; overflow-y:auto; margin:14px 0;">
                <?php if (!$messages): ?>
                    <div class="text-center" style="font-size:14px">
                        You have no open conversation.<br>Write your message below to start one.
                    </div>
                <?php endif; ?>
                <?php foreach ($messages as $m): ?>
                    <div class="bubble-row <?php echo ((int)$m['is_admin'] === 1) ? 'from-admin' : 'from-me'; ?>">
                        <div class="bubble">
                            <div class="who"><?php echo ((int)$m['is_admin'] === 1) ? 'Support' : 'You'; ?></div>
                            <div class="text"><?php echo nl2br(e($m['body'])); ?></div>
                            <div class="stamp"><?php echo ago($m['sent_at']); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <form method="post" class="w-100 ticketform">
                <?php echo csrf_field(); ?>
                <textarea name="message_text" rows="4"
                          placeholder="<?php echo $open ? 'Write your message' : 'Write your message to start a conversation'; ?>"></textarea>
                <div class="d-flex gap-2 mt-2">
                    <input type="submit" value="<?php echo $open ? 'Send' : 'Start conversation'; ?>"
                           class="but" style="width:200px" name="send_message">
                    <?php if ($open): ?>
                        <input type="submit" value="Close conversation" class="but" style="width:200px"
                               name="close_ticket" formnovalidate
                               onclick="return confirm('Close this conversation? You can start a new one afterwards.')">
                    <?php endif; ?>
                </div>
            </form>

            <?php if ($previous !== null): ?>
                <details class="past" style="width:100%; margin-top:16px;">
                    <summary><b>Earlier conversation</b>
                        &middot; <?php echo count($previous['messages']); ?> message<?php echo count($previous['messages']) === 1 ? '' : 's'; ?>
                    </summary>
                    <?php foreach ($previous['messages'] as $m): ?>
                        <div class="bubble-row <?php echo ((int)$m['is_admin'] === 1) ? 'from-admin' : 'from-me'; ?>">
                            <div class="bubble old">
                                <div class="who"><?php echo ((int)$m['is_admin'] === 1) ? 'Support' : 'You'; ?></div>
                                <div class="text"><?php echo nl2br(e($m['body'])); ?></div>
                                <div class="stamp"><?php echo ago($m['sent_at']); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </details>
            <?php endif; ?>
        </div>
    </main>
    <style>
        /* scoped to this page: the global .desc / form rules in welcome_style.css must not leak in */
        .bubble-row { display: flex; margin: 4px 0; }
        .bubble-row.from-me { justify-content: flex-end; }
        .bubble-row.from-admin { justify-content: flex-start; }
        .bubble { max-width: 75%; padding: 6px 10px; border-radius: 12px; background: #ffffff;
                  box-shadow: 0 1px 3px rgba(0,0,0,.15); font-size: 14px; }
        .from-me .bubble { background: #b7e2fb; }
        .bubble.old { opacity: .75; }
        .bubble .who { font-weight: 600; font-size: 12px; opacity: .7; line-height: 1.25; }
        .bubble .text { white-space: pre-wrap; }
        .bubble .stamp { font-size: 11px; opacity: .6; margin-top: 2px; line-height: 1.2; }
        .past summary { cursor: pointer; font-size: 14px; }
        .past .bubble { max-width: 70%; }
        /* the message box is a white field, not a transparent one: the global rule paints
           text white because it was written for fields sitting on the grey card */
        .ticketform textarea { background: #ffffff; color: #474646; caret-color: #474646;
                               border: 1px solid #c9c9c9; border-radius: 6px; padding: 10px; width: 100%; }
        .ticketform textarea::placeholder { color: #8a8a8a; }
    </style>
<?php page_bottom(); ?>
