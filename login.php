<?php
if ( $_SERVER['REQUEST_METHOD']=='GET' && realpath(__FILE__) == realpath( $_SERVER['SCRIPT_FILENAME'] ) ) {        
    header( 'HTTP/1.0 403 Forbidden', TRUE, 403 );
    die( header( 'location: index.html' ) );
}
require ("linking.php");
if ($con->connect_error) {
    die("Connection Failed" . $con->connect_error);
} else {
    session_start();
    $_SESSION['start'] = time(); 
    $_SESSION['expire'] = $_SESSION['start'] + (1 * 10);
    if (isset($_POST['login_submit'])) {
        $username = filter_input(INPUT_POST, 'username1', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, 'password1', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $query = mysqli_query($con,"SELECT username,pass FROM login_credentials");
        if (mysqli_num_rows($query) > 0) {
            while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
                if (in_array($username,$row)) {
                    if (password_verify($password,$row['pass'])) {
                        $_SESSION['username'] = $username;
                        header("Location: welcome.php");
                        exit;
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
                                    <h1 style="text-align: center;">Access Denied : Check the provided info !</h1>
                                    <h4 style="text-align: center;">Possible Problems : Wrong Password or Username / Account does not exist</h4>
                                    <div style="text-align: center; font-size: small;">You will be automatically redirected back to the login page in 4 seconds.</div>
                                </div>
                            </div>
                        </body>
                        </html>
                    <?php }
                }
            }
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
                        <h1 style="text-align: center;">Access Denied : Check the provided info !</h1>
                        <h4 style="text-align: center;">Possible Problems : Wrong Password or Username / Account does not exist</h4>
                        <div style="text-align: center; font-size: small;">You will be automatically redirected back to the login page in 4 seconds.</div>
                    </div>
                </div>
            </body>
            </html>
        <?php }
    }
}
?>