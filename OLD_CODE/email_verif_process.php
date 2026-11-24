<?php
require ("linking.php");
if ($con->connect_error) {
    die("Connection Failed" . $con->connect_error);
} else {
    session_start();
    session_destroy();
    $token = filter_input(INPUT_GET,"token",FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $token_hash = hash("sha256",$token);
    $token_find_query = $con -> query("SELECT * FROM login_credentials WHERE email_verif_token = '$token_hash'");
    $user = $token_find_query -> fetch_array();
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
    if (strtotime($user["email_verif_expire"]) <= time()) {?>
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
    $con -> query("UPDATE login_credentials SET email_verif = TRUE ,email_verif_token = NULL, email_verif_expire = NULL WHERE email_verif_token = '$token_hash'");
}
?>
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
            <h1 style="text-align: center;">Action Success : Email Verified !</h1>
            <div style="text-align: center; font-size: small;">You will be automatically redirected back to the login page in 4 seconds.</div>
        </div>
    </div>
</body>
</html>