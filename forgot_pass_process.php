<?php
require ("linking.php");
if ($con->connect_error) {
    die("Connection Failed" . $con->connect_error);
} else {
    session_start();
    session_destroy();
    $token = filter_input(INPUT_GET,"token",FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $token_hash = hash("sha256",$token);
    $token_find_query = mysqli_query($con,"SELECT * FROM login_credentials WHERE token = '$token_hash'");
    $user = mysqli_fetch_array($token_find_query);
    if ($user === null) { ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta http-equiv="refresh" content="4; url=index.php">
            <title>WSYM Banking</title>
            <link rel="shortcut icon" href="./data/favicon.ico" type="image/x-icon">
            <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300&family=Open+Sans+Condensed:wght@300&display=swap" rel="stylesheet">
            <link rel="stylesheet" href="./css/redirections_style.css">
        </head>
        <body>
            <div class="container1">
                <div class="container2">
                    <h1 style="text-align: center;">Error : Token not found</h1>
                    <div style="text-align: center; font-size: small;">You will be automatically redirected back to the login page in 4 seconds.</div>
                </div>
            </div>
        </body>
        </html>
    <?php die(); }
    if (strtotime($user["token_expire"]) <= time()) {?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta http-equiv="refresh" content="4; url=index.php">
            <title>WSYM Banking</title>
            <link rel="shortcut icon" href="./data/favicon.ico" type="image/x-icon">
            <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300&family=Open+Sans+Condensed:wght@300&display=swap" rel="stylesheet">
            <link rel="stylesheet" href="./css/redirections_style.css">
        </head>
        <body>
            <div class="container1">
                <div class="container2">
                    <h1 style="text-align: center;">Error : Token has expired</h1>
                    <div style="text-align: center; font-size: small;">You will be automatically redirected back to the login page in 4 seconds.</div>
                </div>
            </div>
        </body>
        </html>
    <?php die();}
    if (isset($_POST["sub_pass"])) {
        $pass = filter_input(INPUT_POST,"pass",FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $cpass = filter_input(INPUT_POST,"cpass",FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $hpass = password_hash($cpass, PASSWORD_DEFAULT);
        if ($cpass === $pass && mysqli_query($con,"UPDATE login_credentials set pass = '$hpass' WHERE token = '$token_hash'") && mysqli_query($con,"UPDATE login_credentials SET token = NULL, token_expire = NULL WHERE token = '$token_hash'")) { ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <meta http-equiv="refresh" content="4; url=index.php">
                <title>WSYM Banking</title>
                <link rel="shortcut icon" href="./data/favicon.ico" type="image/x-icon">
                <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300&family=Open+Sans+Condensed:wght@300&display=swap" rel="stylesheet">
                <link rel="stylesheet" href="./css/redirections_style.css">
            </head>
            <body>
                <div class="container1">
                    <div class="container2">
                        <h1 style="text-align: center;">Action success : Password Changed !</h1>
                        <div style="text-align: center; font-size: small;">You will be automatically redirected back to the login page in 4 seconds.</div>
                    </div>
                </div>
            </body>
            </html>
        <?php die(); } else { ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <meta http-equiv="refresh" content="4; url=index.php">
                <title>WSYM Banking</title>
                <link rel="shortcut icon" href="./data/favicon.ico" type="image/x-icon">
                <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300&family=Open+Sans+Condensed:wght@300&display=swap" rel="stylesheet">
                <link rel="stylesheet" href="./css/redirections_style.css">
            </head>
            <body>
                <div class="container1">
                    <div class="container2">
                        <h1 style="text-align: center;">Error 500 : Internal Server Error</h1>
                        <div style="text-align: center; font-size: small;">You will be automatically redirected back to the welcome page in 4 seconds.</div>
                    </div>
                </div>
            </body>
            </html>
        <?php die();}
    }
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
    <link rel="stylesheet" href="./css/account_settings.css">
    <link href="./bootstrap-5.0.2-dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="cont1">
        <form method="post" class="cont2 d-flex flex-column justify-content-center align-items-center">
            <h1>Change your password</h1>
            <div class="mt-auto mb-auto d-flex flex-column justify-content-center align-items-center">
            <label class="labbor lab">
                <img src="./data/key.svg" alt="">
                <input type="password" placeholder="New Password" id="pass" name="pass">
                <input type="checkbox" name="" id="passv2">
            </label>
            <label class="labbor lab">
                <img src="./data/repeat.svg" alt="">
                <input type="password" placeholder="Confirm Password" id="cpass" name="cpass">
                <input type="checkbox" name="" id="passv3">
            </label>
            </div>
            <input type="submit" value="Change your password" class="but" name="sub_pass" onclick="return confpass()">
        </form>
    </div>
    <script src="./bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
    <script src="./js/acc.js"></script>
</body>
</html>