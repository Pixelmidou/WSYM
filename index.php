<?php
session_start();
if (isset($_SESSION['admin_username'])) {
    header("Location: admin_redirections.php");
    exit;
} else if (isset($_SESSION['user_username'])) {
    header("Location: welcome.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WSYM Banking</title>
    <link rel="shortcut icon" href="./data/favicon.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300&family=Open+Sans+Condensed:wght@300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/style_index.css">
</head>
<body>
    <div class="container1" id="loginpage">
        <form action="login.php" method="post">
            <h2 class="ti">Log in</h2>
            <h2 class="qu">Welcome to <a href="./about_us.php" class="abt">"We Steal Your Money"</a> Banking System</h2>
            <div class="push2">
                <div class="container2">
                    <label class="labbor">
                        <img src="./data/user.svg" alt="">
                        <input type="text" placeholder="Username" name="username1" id="user1" required>
                    </label>
                </div>
                <div class="container3">
                    <label class="labbor">
                        <img src="./data/key.svg" alt="">
                        <input type="password" placeholder="Password" name="password1" id="passw1" required>
                        <input type="checkbox" name="" id="passv1">
                    </label>
                </div>
            </div>
            <div class="push">
                <div class="container4">
                    <label>
                        <input type="checkbox" name="" id="">
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
        </form>
    </div>
    <div id="createaccount" class="createaccount container1">
        <form action="register.php" method="post">
            <h2 class="ti">Create Account</h2>
            <h2 class="qu">Welcome to <a href="./about_us.html" class="abt">"We Steal Your Money"</a> Banking System</h2>
            <div class="push2">
                <div class="container2">
                    <label class="labbor">
                        <img src="./data/user.svg" alt="">
                        <input type="text" placeholder="Username" name="username2" id="user2" required>
                    </label>
                </div>
                <div class="container3">
                    <label class="labbor">
                        <img src="./data/key.svg" alt="">
                        <input type="password" placeholder="Password" name="password2" id="passw2" required>
                        <input type="checkbox" name="" id="passv2">
                    </label>
                </div>
                <div class="container4 contem">
                    <label class="labbor">
                        <img src="./data/mail.svg" alt="">
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
    <div id="forgotpass" class="forgotpass container1" action="forgot_pass.php">
        <form action="forgot_pass.php" method="post">
            <h2 class="ti">Request a New Password</h2>
            <h2 class="qu">Welcome to <a href="./about_us.html" class="abt">"We Steal Your Money"</a> Banking System</h2>
            <div class="push2">
                <div class="container4 contem">
                    <label class="labbor">
                        <img src="./data/mail.svg" alt="">
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
    <script src="./js/index.js"></script>
</body>
</html>