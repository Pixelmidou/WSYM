<?php
/**
 * index.php - the front door: Log in / Create account / Forgot password.
 * The three forms are full-screen panels switched by js/index.js. Nothing here touches
 * the database: each form posts to its own small page.
 */
require __DIR__ . '/bootstrap.php';
session_boot();

if (!empty($_SESSION['user'])) {
    redirect(!empty($_SESSION['admin']) ? 'admin_redirect.php' : 'welcome.php');
}

$notice = '';
if (isset($_GET['ended']))   { $notice = 'Your session was ended by an administrator. Please log in again.'; }
if (isset($_GET['expired'])) { $notice = 'You were inactive for 90 minutes. Please log in again.'; }
if (isset($_GET['bye']))     { $notice = 'You are signed out.'; }

$css = ['style_index'];
page_top('WSYM Banking', $css, false);
?>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300&family=Open+Sans+Condensed:wght@300&display=swap" rel="stylesheet">
    <?php if ($notice !== ''): ?>
        <p style="position:fixed;top:0;left:0;right:0;margin:0;padding:10px;background:#9FCAE5;color:#1c2340;text-align:center;font-family:sans-serif;z-index:9"><?php echo e($notice); ?></p>
    <?php endif; ?>

    <div class="container1" id="loginpage">
        <form action="login.php" method="post">
            <?php echo csrf_field(); ?>
            <h2 class="ti">Log in</h2>
            <h2 class="qu">Welcome to <span class="abt">WSYM</span> &mdash; A Banking System Simulator</h2>
            <div class="push2">
                <div class="container2">
                    <label class="labbor">
                        <img src="<?php echo url('data/user.svg'); ?>" alt="">
                        <input type="text" placeholder="Username" name="username1" id="user1" required>
                    </label>
                </div>
                <div class="container3">
                    <label class="labbor">
                        <img src="<?php echo url('data/key.svg'); ?>" alt="">
                        <input type="password" placeholder="Password" name="password1" id="passw1" required>
                        <input type="checkbox" id="passv1">
                    </label>
                </div>
            </div>
            <div class="push">
                <div class="container4">
                    <label>
                        <input type="checkbox" name="remember_me" value="1" id="remember">
                        <span style="color: white;">Remember Me</span>
                    </label>
                </div>
                <div class="container5">
                    <input type="submit" value="Login" class="but" name="login_submit">
                    <input type="button" value="Create a new account" class="but" onclick="func1()">
                </div>
                <div class="container6">
                    <input type="button" value="I forgot my password" class="but butt" onclick="func2()">
                </div>
            </div>
            <div class="container7 testacc">
                <p><b>New here?</b> Create an account in a few seconds.</p>
                <p>The sample logins are in the README.</p>
            </div>
        </form>
    </div>

    <div id="createaccount" class="createaccount container1">
        <form action="register.php" method="post">
            <?php echo csrf_field(); ?>
            <h2 class="ti">Create Account</h2>
            <h2 class="qu">Welcome to <span class="abt">WSYM</span> &mdash; A Banking System Simulator</h2>
            <div class="push2">
                <div class="container2">
                    <label class="labbor">
                        <img src="<?php echo url('data/user.svg'); ?>" alt="">
                        <input type="text" placeholder="Username" name="username2" id="user2" required>
                    </label>
                </div>
                <div class="container3">
                    <label class="labbor">
                        <img src="<?php echo url('data/key.svg'); ?>" alt="">
                        <input type="password" placeholder="Password" name="password2" id="passw2" required>
                        <input type="checkbox" id="passv2">
                    </label>
                </div>
                <div class="container4 contem">
                    <label class="labbor">
                        <img src="<?php echo url('data/mail.svg'); ?>" alt="">
                        <input type="email" placeholder="Email" name="email2" id="email2" required>
                    </label>
                </div>
            </div>
            <div class="push">
                <div class="container5">
                    <input type="submit" value="Create a new account" class="but" name="register_submit">
                    <input type="button" value="Back to the Login Page" class="but" onclick="func3()">
                </div>
            </div>
        </form>
    </div>

    <div id="forgotpass" class="forgotpass container1">
        <form action="forgot.php" method="post">
            <?php echo csrf_field(); ?>
            <h2 class="ti">Request a New Password</h2>
            <h2 class="qu">Welcome to <span class="abt">WSYM</span> &mdash; A Banking System Simulator</h2>
            <div class="push2">
                <div class="container4 contem">
                    <label class="labbor">
                        <img src="<?php echo url('data/mail.svg'); ?>" alt="">
                        <input type="email" placeholder="Email" name="email3" id="email3" required>
                    </label>
                </div>
            </div>
            <div class="push">
                <div class="container5">
                    <input type="submit" value="Submit your request" class="but" name="forgot_pass_submit">
                    <input type="button" value="Back to the Login Page" class="but" onclick="func3()">
                </div>
            </div>
        </form>
    </div>
    <script src="<?php echo url('js/index.js'); ?>"></script>
<?php page_bottom(); ?>
