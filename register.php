<?php
if ( $_SERVER['REQUEST_METHOD']=='GET' && realpath(__FILE__) == realpath( $_SERVER['SCRIPT_FILENAME'] ) ) {        
    header( 'HTTP/1.0 403 Forbidden', TRUE, 403 );
    die( header( 'location: index.html' ) );
}
require ("linking.php");
if ($con->connect_error) {
    die("Connection Failed" . $con->connect_error);
} else {
    if (isset($_POST['register_submit'])) {
        $username = filter_input(INPUT_POST, 'username2', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (mysqli_num_rows(mysqli_query($con, "SELECT username FROM login_credentials WHERE username = '$username'")) > 0) { ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <meta http-equiv="refresh" content="4; url=index.html">
                <title>WSYM Banking</title>
                <link rel="shortcut icon" href="./data/favicon.ico" type="image/x-icon">
                <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300&family=Open+Sans+Condensed:wght@300&display=swap" rel="stylesheet">
                <link rel="stylesheet" href="./css/redirections_style.css">
            </head>
            <body>
                <div class="container1">
                    <div class="container2">
                        <h1 style="text-align: center;">Action Failed : Account already exists !</h1>
                        <div style="text-align: center; font-size: small;">You will be automatically redirected back to the login page in 4 seconds.</div>
                    </div>
                </div>
            </body>
            </html>
        <?php } else {
            $password = password_hash(filter_input(INPUT_POST, 'password2', FILTER_SANITIZE_FULL_SPECIAL_CHARS), PASSWORD_DEFAULT);
            $email = filter_input(INPUT_POST, 'email2', FILTER_SANITIZE_EMAIL);
            if (mysqli_query($con,"INSERT INTO login_credentials VALUES ('$username','$password','$email',now(),'none')") && mysqli_query($con,"INSERT INTO balance VALUES ('$username','$email','0')") && mysqli_query($con,"INSERT INTO blacklist VALUES ('$username', '1', '1', '1','1','1')")) { ?>
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <meta http-equiv="refresh" content="4; url=index.html">
                    <title>WSYM Banking</title>
                    <link rel="shortcut icon" href="./data/favicon.ico" type="image/x-icon">
                    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300&family=Open+Sans+Condensed:wght@300&display=swap" rel="stylesheet">
                    <link rel="stylesheet" href="./css/redirections_style.css">
                </head>
                <body>
                    <div class="container1">
                        <div class="container2">
                            <h1 style="text-align: center;">Account Created : Welcome Aboard :) !</h1>
                            <div style="text-align: center; font-size: small;">You will be automatically redirected to the login page in 4 seconds.</div>
                        </div>
                    </div>
                </body>
                </html>
            <?php } else { ?>
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <meta http-equiv="refresh" content="4; url=index.html">
                    <title>WSYM Banking</title>
                    <link rel="shortcut icon" href="./data/favicon.ico" type="image/x-icon">
                    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300&family=Open+Sans+Condensed:wght@300&display=swap" rel="stylesheet">
                    <link rel="stylesheet" href="./css/redirections_style.css">
                </head>
                <body>
                    <div class="container1">
                        <div class="container2">
                            <h1 style="text-align: center;">Error 500 : Internal Server Error</h1>
                            <div style="text-align: center; font-size: small;">You will be automatically redirected back to the login page in 4 seconds.</div>
                        </div>
                    </div>
                </body>
                </html>
            <?php }
        }
    }
}
?>