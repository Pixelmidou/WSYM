<?php
if ( $_SERVER['REQUEST_METHOD']=='GET' && realpath(__FILE__) == realpath( $_SERVER['SCRIPT_FILENAME'] ) ) {        
    header( 'HTTP/1.0 403 Forbidden', TRUE, 403 );
    die( header( 'location: index.php' ) );
}
require ("linking.php");
if ($con->connect_error) {
    die("Connection Failed" . $con->connect_error);
} else {
    session_start();
    $_SESSION['start'] = time(); 
    $_SESSION['expire'] = $_SESSION['start'] + (90 * 60);
    if (isset($_POST['login_submit'])) {
        $username = filter_input(INPUT_POST, 'username1', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, 'password1', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $user_query = mysqli_query($con,"SELECT username,pass FROM login_credentials WHERE username = '$username'");
        $admin_query = mysqli_query($con,"SELECT username,pass FROM login_credentials WHERE username = '$username' AND rank IN (SELECT rank FROM ranks WHERE rank <> 'none')");
        $acc_query = mysqli_query($con,"SELECT account FROM blacklist WHERE username = '$username'");
        if (mysqli_num_rows($acc_query) > 0) {
            $acc_query_array = mysqli_fetch_all($acc_query, MYSQLI_ASSOC);
            foreach ($acc_query_array as $row) {
                $acc = $row["account"];
            }
        }
        if (mysqli_num_rows($admin_query) > 0) { 
            while ($row_admin = mysqli_fetch_array($admin_query, MYSQLI_ASSOC)) { 
                if (password_verify($password,$row_admin['pass']) && $acc === "1") {
                    mysqli_query($con,"UPDATE login_credentials set lastlogin = now() WHERE username = '$username'");
                    $_SESSION['admin_username'] = $username;
                    header("Location: admin_redirections.php");
                    exit;
                } else { ?>
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
                                <h1 style="text-align: center;">Access Denied : Check the provided info !</h1>
                                <h4 style="text-align: center;">Possible Problems : Wrong Password or Username / Account does not exist / Account Disabled</h4>
                                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the login page in 4 seconds.</div>
                            </div>
                        </div>
                    </body>
                    </html>
                <?php die();}
            }
        } else if (mysqli_num_rows($user_query) > 0) {
            while ($row_user = mysqli_fetch_array($user_query, MYSQLI_ASSOC)) {
                if (password_verify($password,$row_user['pass']) && $acc === "1") {
                    mysqli_query($con,"UPDATE login_credentials set lastlogin = now() WHERE username = '$username'");
                    $_SESSION['user_username'] = $username;
                    header("Location: welcome.php");
                    exit;
                } else { ?>
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
                                <h1 style="text-align: center;">Access Denied : Check the provided info !</h1>
                                <h4 style="text-align: center;">Possible Problems : Wrong Password or Username / Account does not exist / Account Disabled</h4>
                                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the login page in 4 seconds.</div>
                            </div>
                        </div>
                    </body>
                    </html>
                <?php die(); }
            }
        } else { ?>
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
                        <h1 style="text-align: center;">Access Denied : Check the provided info !</h1>
                        <h4 style="text-align: center;">Possible Problems : Wrong Password or Username / Account does not exist / Account Disabled</h4>
                        <div style="text-align: center; font-size: small;">You will be automatically redirected back to the login page in 4 seconds.</div>
                    </div>
                </div>
            </body>
            </html>
        <?php die(); }
    }
}
?>