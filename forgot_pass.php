<?php
if ( $_SERVER['REQUEST_METHOD']=='GET' && realpath(__FILE__) == realpath( $_SERVER['SCRIPT_FILENAME'] ) ) {        
    header( 'HTTP/1.0 403 Forbidden', TRUE, 403 );
    die( header( 'location: index.html' ) );
}
require ("linking.php");
if ($con->connect_error) {
    die("Connection Failed" . $con->connect_error);
} else {
    if (isset($_POST['forgot_pass_submit'])) {
        $email = filter_input(INPUT_POST, 'email3', FILTER_SANITIZE_EMAIL);
        if (mysqli_num_rows(mysqli_query($con, "SELECT email FROM login_credentials WHERE email = '$email'")) > 0 && mysqli_num_rows(mysqli_query($con, "SELECT email FROM forgot_password WHERE email = '$email' AND forgotpass = '1'")) === 0) {
            if(mysqli_query($con, "INSERT INTO forgot_password VALUES ('$email', '1')")) { ?>
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
                            <h1 style="text-align: center;">Success : Request has been submited !</h1>
                            <h4 style="text-align: center;">We will contact you as soon as possible with a new password</h4>
                            <div style="text-align: center; font-size: small;">You will be automatically redirected to the welcome page in 4 seconds.</div>
                        </div>
                    </div>
                </body>
                </html>
                <?php $msg = "A Request has been made to change your account's password \n if it is you ignore this message nothing will happen, \n else contact us via the ticket section immediately.";
                $msg = wordwrap($msg,70);
                mail("$email","Reset Password Request",$msg);
            } else { ?>
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
        } else { ?>
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
                        <h1 style="text-align: center;">Action Failed : Check the provided info !</h1>
                        <h4 style="text-align: center;">Possible Problems : Wrong email provided / Request has already been made</h4>
                        <div style="text-align: center; font-size: small;">You will be automatically redirected back to the login page in 4 seconds.</div>
                    </div>
                </div>
            </body>
            </html>
        <?php }
    }
}
?>