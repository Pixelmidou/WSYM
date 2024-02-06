<?php
require ("linking.php");
if ($con->connect_error) {
    die("Connection Failed" . $con->connect_error);
} else {
    session_start();
    if (!isset($_SESSION['setting']) && !isset($_SESSION['verif_id'])) {
        if (empty($_SESSION['setting']) || $_SESSION['setting'] === "" && empty($_SESSION['verif_id']) || $_SESSION['verif_id'] === "") {
            if (isset($_SESSION['admin_username'])) {
                header("Location: admin_redirections.php");
                exit;
            } else if (isset($_SESSION['user_username'])) {
                header("Location: welcome.php");
                exit;
            } else {
                session_destroy();
                header("Location: index.php");
                exit;
            }
        } 
    }
    if (isset($_SESSION['user_username'])) {
        $user_username = $_SESSION['user_username'];
        echo "<script>var origuser = '$user_username'</script>";
    }
    if (isset($_SESSION['verif_id'])) {
        $verif_id = $_SESSION['verif_id'];
        if (isset($_POST['id_sub']) && $verif_id === false) {
            $iduser = filter_input(INPUT_POST,"id_user",FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $idmail = filter_input(INPUT_POST,"id_mail",FILTER_SANITIZE_EMAIL);
            $idpass = filter_input(INPUT_POST,"id_pass",FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $idquery = mysqli_query($con,"SELECT pass,email FROM login_credentials WHERE username = '$user_username'");
            if (mysqli_num_rows($idquery) > 0) {
                $idarray = mysqli_fetch_all($idquery, MYSQLI_ASSOC);
                foreach ($idarray as $row) {
                    $idpassdb = $row['pass'];
                    $idmaildb = $row['email'];
                }
            }
            if ($iduser === $user_username && $idmail === $idmaildb && password_verify($idpass,$idpassdb)) {
                $verif_id = true;
                $_SESSION['verif_id'] = true;
            } else {
                echo "<script>alert('Error : Check the info provided !')</script>";
            }
        }
    }
    if (isset($_SESSION["setting"])) {
        $setting = $_SESSION["setting"];
        if (isset($_POST["sub_uuser"])) {
            $uuser = filter_input(INPUT_POST,"uuser",FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $cuuser = filter_input(INPUT_POST,"cuuser",FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            if ($cuuser === $uuser && mysqli_query($con,"UPDATE login_credentials set username = '$cuuser' WHERE username = '$user_username'")) {
                session_destroy(); ?>
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
                            <h1 style="text-align: center;">Session terminated : You need to login again !</h1>
                            <div style="text-align: center; font-size: medium;">Username Changed : For security reasons , please login with your new username</div>
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
                    <meta http-equiv="refresh" content="4; url=welcome.php">
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
            <?php die(); }
        }
        if (isset($_POST["sub_mail"])) { 
            $mail = filter_input(INPUT_POST,"mail",FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $cmail = filter_input(INPUT_POST,"cmail",FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $origmail_query = mysqli_query($con,"SELECT email FROM login_credentials WHERE username = '$user_username'");
            if (mysqli_num_rows($origmail_query) > 0) {
                $origmail_array = mysqli_fetch_all($origmail_query, MYSQLI_ASSOC);
                foreach ($origmail_array as $row) {
                    $origmail = $row["email"];
                    echo "<script>var origmail = '$origmail'</script>";
                }
            }
            if ($cmail === $mail && mysqli_query($con,"UPDATE login_credentials set email = '$cmail' WHERE email = '$origmail'")) {
                session_destroy(); ?>
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
                            <h1 style="text-align: center;">Session terminated : You need to login again !</h1>
                            <div style="text-align: center; font-size: medium;">Email Changed : For security reasons , please login again</div>
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
                    <meta http-equiv="refresh" content="4; url=welcome.php">
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
        if (isset($_POST["sub_pass"])) { 
            $pass = filter_input(INPUT_POST,"pass",FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $cpass = filter_input(INPUT_POST,"cpass",FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $origpass_query = mysqli_query($con,"SELECT pass FROM login_credentials WHERE username = '$user_username'");
            if (mysqli_num_rows($origpass_query) > 0) {
                $origpass_array = mysqli_fetch_all($origpass_query, MYSQLI_ASSOC);
                foreach ($origpass_array as $row) {
                    $origpass = $row["pass"];
                }
            }
            if (password_verify($cpass,$origpass)) {
                echo "<script>alert('Error : Check the info provided !')</script>";
            } else {
                $hpass = password_hash($cpass, PASSWORD_DEFAULT);
                if ($cpass === $pass && mysqli_query($con,"UPDATE login_credentials set pass = '$hpass' WHERE pass = '$origpass'")) {
                    session_destroy(); ?>
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
                                <h1 style="text-align: center;">Session terminated : You need to login again !</h1>
                                <div style="text-align: center; font-size: medium;">Password Changed : For security reasons , please login again with your new password</div>
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
                        <meta http-equiv="refresh" content="4; url=welcome.php">
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
                <?php }
            }
        }
    }
    $_SESSION['accset'] = "accset";
}
?>
<?php if (isset($_SESSION['verif_id']) && $verif_id === false) { ?>
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
                <h3 class="text-center mt-3 text-white">Login again to confirm your identity</h3>
                <div class="mt-auto mb-auto d-flex flex-column justify-content-center align-items-center">
                <label class="labbor lab">
                    <img src="./data/user.svg" alt="">
                    <input type="text" placeholder="Username" id="" name="id_user" required>
                </label>
                <label class="labbor lab">
                    <img src="./data/mail.svg" alt="">
                    <input type="email" placeholder="Email" id="" name="id_mail" required>
                </label>
                <label class="labbor lab">
                    <img src="./data/key.svg" alt="">
                    <input type="password" placeholder="Password" id="passw1" name="id_pass" required>
                    <input type="checkbox" name="" id="passv1">
                </label>
                <span id="err" style="color: red;"></span>
                </div>
                <input type="submit" value="Login" class="but" name="id_sub" onclick="">
            </form>
        </div>
        <script src="./bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
        <script src="./js/acc.js"></script>
        <script>
            document.getElementById("passv1").addEventListener("click", function(){ func4(document.getElementById("passw1")); });
        </script>
    </body>
    </html>
<?php } else if (isset($_SESSION['verif_id']) && $verif_id === true) { ?>
    <?php if (isset($_SESSION['setting']) && $setting === "user"): ?>
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
                    <h1>Change your username</h1>
                    <div class="mt-auto mb-auto d-flex flex-column justify-content-center align-items-center">
                    <label class="labbor lab">
                        <img src="./data/user.svg" alt="">
                        <input type="text" placeholder="New Username" id="uuser" name="uuser" required>
                    </label>
                    <label class="labbor lab">
                        <img src="./data/repeat.svg" alt="">
                        <input type="text" placeholder="Confirm Username" id="cuuser" name="cuuser" required>
                    </label>
                    </div>
                    <input type="submit" value="Change your username" class="but" name="sub_uuser" onclick="return confuser()">
                </form>
            </div>
            <script src="./bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
            <script src="./js/acc.js"></script>
        </body>
        </html>
    <?php endif; ?>
    <?php if (isset($_SESSION['setting']) && $setting === "mail"): ?>
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
                    <h1>Change your email</h1>
                    <div class="mt-auto mb-auto d-flex flex-column justify-content-center align-items-center">
                    <label class="labbor lab">
                        <img src="./data/mail.svg" alt="">
                        <input type="email" placeholder="New Email" id="mail" name="mail" required>
                    </label>
                    <label class="labbor lab">
                        <img src="./data/repeat.svg" alt="">
                        <input type="email" placeholder="Confirm Email" id="cmail" name="cmail" required>
                    </label>
                    </div>
                    <input type="submit" value="Change your email" class="but" name="sub_mail" onclick="return confmail()">
                </form>
            </div>
            <script src="./bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
            <script src="./js/acc.js"></script>
        </body>
        </html>
    <?php endif; ?>
    <?php if (isset($_SESSION['setting']) && $setting === "pass"): ?>
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
    <?php endif; ?>
<?php } ?>