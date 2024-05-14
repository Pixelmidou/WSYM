<?php
require ("linking.php");
if ($con->connect_error) {
    die("Connection Failed" . $con->connect_error);
} else {
    session_start();
    if (!isset($_SESSION['deposit_verif']) && !isset($_SESSION['withdraw_verif']) && !isset($_SESSION['wire_verif']) && !isset($_SESSION['ticket_verif']) && !isset($_SESSION['dephis_show']) && !isset($_SESSION['withhis_show']) && !isset($_SESSION['wirehis_show'])) {
        if (empty($_SESSION['deposit_verif']) || $_SESSION['deposit_verif'] === "" && empty($_SESSION['withdraw_verif']) || $_SESSION['withdraw_verif'] === "" && empty($_SESSION['wire_verif']) || $_SESSION['wire_verif'] === ""  && empty($_SESSION['ticket_verif']) || $_SESSION['ticket_verif'] === "" && empty($_SESSION['withhis_show']) || $_SESSION['withhis_show'] === "" && empty($_SESSION['wirehis_show']) || $_SESSION['wirehis_show'] === "") {
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
    if (isset($_SESSION['deposit_verif'])) {
        $deposit_verif = $_SESSION['deposit_verif'];
        if ($deposit_verif === "success") {
            $depval = $_SESSION['depval'];
        }
        $_SESSION['dep'] = "dep";
    }
    if (isset($_SESSION['withdraw_verif'])) {
        $withdraw_verif = $_SESSION['withdraw_verif'];
        if ($withdraw_verif === "success") {
            $withval = $_SESSION['withval'];
        }
        $_SESSION['withd'] = "withd";
    }
    if (isset($_SESSION['wire_verif'])) {
        $wire_verif = $_SESSION['wire_verif'];
        if ($wire_verif === "success") {
            $wireval = $_SESSION['wireval'];
            $wireemail = $_SESSION['wireemail'];
        }
        $_SESSION['wire'] = "wire";
    }
    if (isset($_SESSION['ticket_verif'])) {
        $ticket_verif = $_SESSION['ticket_verif'];
        $_SESSION['tick'] = "tick";
    }
    if (isset($_SESSION['dephis_show'])) {
        $dephis_show = $_SESSION['dephis_show'];
        if ($dephis_show === "success") {
            $user_username = $_SESSION['user_username'];
            $start = 0;
            $rowsperpage = 6;
            $recs = $con -> prepare("SELECT * FROM deposit WHERE username = ?");
            $recs -> bind_param("s", $user_username);
            $recs_res = $recs -> get_result();
            $nbrows = $recs_res -> num_rows;
            $nbpages = ceil($nbrows / $rowsperpage);
            if (isset($_GET["pagenr"])) {
                $start = ($_GET["pagenr"] - 1)  * $rowsperpage;
            }
            $dephis_query_date_desc = $con -> prepare("SELECT deposit_date,deposit_amount FROM deposit WHERE username LIKE '%?%' ORDER BY deposit_date DESC LIMIT $start , $rowsperpage");
            $dephis_query_date_desc -> bind_param("s", $user_username);
            $dephis_query_date_desc_res = $dephis_query_date_desc -> get_result();
            $dephis_query_array_all = $dephis_query_date_desc_res -> fetch_all(MYSQLI_ASSOC);
        }
        $_SESSION['dephis'] = "dephis";
        if (isset($_POST['dephisback'])) {
            header("Location: welcome.php");
            unset($_SESSION['dephis_show']);
            exit;
        }
    }
    if (isset($_SESSION['withhis_show'])) {
        $withhis_show = $_SESSION['withhis_show'];
        if ($withhis_show === "success") {
            $user_username = $_SESSION['user_username'];
            $start = 0;
            $rowsperpage = 6;
            $recs = $con -> prepare("SELECT * FROM deposit WHERE username = ?");
            $recs -> bind_param("s", $user_username);
            $recs_res = $recs -> get_result();
            $nbrows = $recs_res -> num_rows;
            $nbpages = ceil($nbrows / $rowsperpage);
            if (isset($_GET["pagenr"])) {
                $start = ($_GET["pagenr"] - 1)  * $rowsperpage;
            }
            $withhis_query_date_desc = $con -> prepare("SELECT withdraw_date,withdraw_amount FROM withdraw WHERE username LIKE '%?%' ORDER BY withdraw_date DESC LIMIT $start , $rowsperpage");
            $withhis_query_date_desc -> bind_param("s", $user_username);
            $withhis_query_date_desc_res = $withhis_query_date_desc -> get_result();
            $withhis_query_array_all = $withhis_query_date_desc_res -> fetch_all(MYSQLI_ASSOC);
        }
        $_SESSION['withhis'] = "withhis";
        if (isset($_POST['withhisback'])) {
            header("Location: welcome.php");
            unset($_SESSION['withhis_show']);
            exit;
        }
    }
    if (isset($_SESSION['wirehis_show'])) {
        $wirehis_show = $_SESSION['wirehis_show'];
        if ($wirehis_show === "success") {
            $user_username = $_SESSION['user_username'];
            $start = 0;
            $rowsperpage = 6;
            $recs = $con -> prepare("SELECT * FROM deposit WHERE username = ?");
            $recs -> bind_param("s", $user_username);
            $recs_res = $recs -> get_result();
            $nbrows = $recs_res -> num_rows;
            $nbpages = ceil($nbrows / $rowsperpage);
            if (isset($_GET["pagenr"])) {
                $start = ($_GET["pagenr"] - 1)  * $rowsperpage;
            }
            $wirehis_query_date_desc = $con -> prepare("SELECT receiver,wire_date,wire_amount FROM wire WHERE username LIKE '%?%' ORDER BY wire_date DESC LIMIT $start , $rowsperpage");
            $wirehis_query_date_desc -> bind_param("s", $user_username);
            $wirehis_query_date_desc_res = $wirehis_query_date_desc -> get_result();
            $wirehis_query_array_all = $wirehis_query_date_desc_res -> fetch_all(MYSQLI_ASSOC);
        }
        $_SESSION['wirehis'] = "wirehis";
        if (isset($_POST['wirehisback'])) {
            header("Location: welcome.php");
            unset($_SESSION['wirehis_show']);
            exit;
        }
    }
}
?>
<?php if (isset($_SESSION['deposit_verif']) && $deposit_verif === "success") {  ?>
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
                <h1 style="text-align: center;">Action Success : Money was deposited in your account !</h1>
                <h4 style="text-align: center;">A value of $<?php echo "$depval"; ?> was added to your account</h4>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the welcome page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php } else if (isset($_SESSION['deposit_verif']) && $deposit_verif === "fail") { ?>
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
<?php } else if (isset($_SESSION['deposit_verif']) && $deposit_verif === "failb") { ?>
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
                <h1 style="text-align: center;">Action Failed : You are blocked from depositing money</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the welcome page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php } else if (isset($_SESSION['deposit_verif']) && $deposit_verif === "failv") { ?>
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
                <h1 style="text-align: center;">Action Failed : Email is not verified</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the welcome page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php } else if (isset($_SESSION['deposit_verif']) && $deposit_verif === "failbv") { ?>
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
                <h1 style="text-align: center;">Action Failed : You are blocked from depositing money & your email is not verified</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the welcome page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php } ?>
<?php if (isset($_SESSION['withdraw_verif']) && $withdraw_verif === "success") {  ?>
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
                <h1 style="text-align: center;">Action Success : Money was withdrawn from your account !</h1>
                <h4 style="text-align: center;">A value of $<?php echo "$withval"; ?> was withdrawn from your account</h4>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the welcome page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php } else if (isset($_SESSION['withdraw_verif']) && $withdraw_verif === "fail") { ?>
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
<?php } else if (isset($_SESSION['withdraw_verif']) && $withdraw_verif === "failb") { ?>
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
                <h1 style="text-align: center;">Action Failed : You are blocked from withdrawing money</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the welcome page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php } else if (isset($_SESSION['withdraw_verif']) && $withdraw_verif === "failv") { ?>
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
                <h1 style="text-align: center;">Action Failed : Email is not verified</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the welcome page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php } else if (isset($_SESSION['withdraw_verif']) && $withdraw_verif === "failbv") { ?>
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
                <h1 style="text-align: center;">Action Failed : You are blocked from withdrawing money & your email is not verified</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the welcome page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php } else if (isset($_SESSION['withdraw_verif']) && $withdraw_verif === "faili") { ?>
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
                <h1 style="text-align: center;">Action Failed : Insufficient funds </h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the welcome page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php } ?>
<?php if (isset($_SESSION['wire_verif']) && $wire_verif === "success") {  ?>
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
                <h1 style="text-align: center;">Action Success : Money was successfully wired !</h1>
                <h4 style="text-align: center;">A value of $<?php echo "$wireval"; ?> was wired to <?php echo "$wireemail"; ?></h4>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the welcome page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php } else if (isset($_SESSION['wire_verif']) && $wire_verif === "fail") { ?>
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
<?php } else if (isset($_SESSION['wire_verif']) && $wire_verif === "failb") { ?>
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
                <h1 style="text-align: center;">Action Failed : You are blocked from wiring money</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the welcome page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php } else if (isset($_SESSION['wire_verif']) && $wire_verif === "failv") { ?>
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
                <h1 style="text-align: center;">Action Failed : Email is not verified</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the welcome page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php } else if (isset($_SESSION['wire_verif']) && $wire_verif === "failbv") { ?>
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
                <h1 style="text-align: center;">Action Failed : You are blocked from wiring money & your email is not verified</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the welcome page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php } else if (isset($_SESSION['wire_verif']) && $wire_verif === "faili") { ?>
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
                <h1 style="text-align: center;">Action Failed : Insufficient funds </h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the welcome page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php } ?>
<?php if (isset($_SESSION['ticket_verif']) && $ticket_verif === "success") {  ?>
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
                <h1 style="text-align: center;">Action Success : Ticket was submited successfully !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the welcome page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php } else if (isset($_SESSION['ticket_verif']) && $ticket_verif === "fail") { ?>
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
<?php } else if (isset($_SESSION['ticket_verif']) && $ticket_verif === "failb") { ?>
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
                <h1 style="text-align: center;">Action Failed : You are blocked from submitting tickets</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the welcome page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php } ?>
<?php if (isset($_SESSION['dephis_show']) && $dephis_show === "success") {  ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>WSYM Banking</title>
        <link rel="shortcut icon" href="./data/favicon.ico" type="image/x-icon">
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300&family=Open+Sans+Condensed:wght@300&display=swap" rel="stylesheet">
        <link href="./bootstrap-5.0.2-dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="./css/welcome_admin_forms.css" rel="stylesheet">
    </head>
    <body>
        <div class="container1">
            <form method="post" class="container2">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Deposit Date</th>
                        <th>Deposit Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dephis_query_array_all as $sub_array): ?>
                            <tr>
                                <?php foreach ($sub_array as $value): ?>
                                    <td><?php echo $value; ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php
                    if (!isset($_GET["pagenr"])) { 
                        $page = 1;
                    } else {
                        $page = $_GET["pagenr"];
                    }
                ?>
                <div class="d-flex gap-3 align-items-center">
                    <div>Showing <?php echo $page; ?> out of <?php echo $nbpages; ?> pages</div>
                    <select class="form-select-sm" name="filters" id="filters">
                        <option value="">No Filter</option>
                        <option value="Date : Most Recent">Date : Most Recent</option>
                        <option value="Date : Oldest">Date : Oldest</option>
                        <option value="Amount : High to Low">Amount : High to Low</option>
                        <option value="Amount : Low to High">Amount : Low to High</option>
                    </select>
                </div>
                <ul class="pagination mt-3">
                    <li class="page-item"><a class="page-link" style="background-color: lightskyblue; border-color: lightgray; opacity: 0.9;" href="?pagenr=1">First</a></li>
                    <?php
                        if (isset($_GET["pagenr"]) && $_GET["pagenr"] > 1 ) { ?>
                            <li class="page-item"><a class="page-link" style="background-color: lightskyblue; border-color: lightgray; opacity: 0.9;" href="?pagenr=<?php echo $_GET["pagenr"] - 1; ?>">Previous</a></li>
                        <?php } else { ?>
                            <li class="page-item"><a class="page-link" style="background-color: lightskyblue; border-color: lightgray; opacity: 0.9;" href="">Previous</a></li>
                       <?php }
                    ?>
                    <?php
                        for ($i = 1; $i <= $nbpages; $i++) { ?>
                            <li class="page-item"><a class="page-link" style="background-color: lightskyblue; border-color: lightgray; opacity: 0.9;" href="?pagenr=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                        <?php }
                    ?>
                    <?php
                        if (!isset($_GET["pagenr"])) { ?>
                            <li class="page-item"><a class="page-link" style="background-color: lightskyblue; border-color: lightgray; opacity: 0.9;" href="?pagenr=2">Next</a></li>
                        <?php } else if ($_GET["pagenr"] >= $nbpages) { ?>
                            <li class="page-item"><a class="page-link" style="background-color: lightskyblue; border-color: lightgray; opacity: 0.9;" href="">Next</a></li>
                       <?php } else { ?>
                        <li class="page-item"><a class="page-link" style="background-color: lightskyblue; border-color: lightgray; opacity: 0.9;" href="?pagenr=<?php echo $_GET["pagenr"] + 1; ?>">Next</a></li>
                       <?php }
                    ?>
                    <li class="page-item"><a class="page-link" style="background-color: lightskyblue; border-color: lightgray; opacity: 0.9;" href="?pagenr=<?php echo $nbpages; ?>">Last</a></li>
                </ul>
                <style>
                    .x {
                        width: 250px;
                    }
                </style>
                <input type="submit" id="but" name="dephisback" class="x but text-center" value="Back to the welcome page" formnovalidate>
        </form>
    </div>
    <script src="./bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
<?php } else if (isset($_SESSION['dephis_show']) && $dephis_show === "fail") { ?>
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
                <h1 style="text-align: center;">Error 404 : Not Found</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php } ?>
<?php if (isset($_SESSION['withhis_show']) && $withhis_show === "success") {  ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>WSYM Banking</title>
        <link rel="shortcut icon" href="./data/favicon.ico" type="image/x-icon">
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300&family=Open+Sans+Condensed:wght@300&display=swap" rel="stylesheet">
        <link href="./bootstrap-5.0.2-dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="./css/welcome_admin_forms.css" rel="stylesheet">
    </head>
    <body>
        <div class="container1">
            <form method="post" class="container2">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Withdraw Date</th>
                        <th>Withdraw Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($withhis_query_array_all as $sub_array): ?>
                            <tr>
                                <?php foreach ($sub_array as $value): ?>
                                    <td><?php echo $value; ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php
                    if (!isset($_GET["pagenr"])) { 
                        $page = 1;
                    } else {
                        $page = $_GET["pagenr"];
                    }
                ?>
                <div class="d-flex gap-3 align-items-center">
                    <div>Showing <?php echo $page; ?> out of <?php echo $nbpages; ?> pages</div>
                    <select class="form-select-sm" name="filters" id="filters">
                        <option value="">No Filter</option>
                        <option value="Date : Most Recent">Date : Most Recent</option>
                        <option value="Date : Oldest">Date : Oldest</option>
                        <option value="Amount : High to Low">Amount : High to Low</option>
                        <option value="Amount : Low to High">Amount : Low to High</option>
                    </select>
                </div>
                <ul class="pagination mt-3">
                    <li class="page-item"><a class="page-link" style="background-color: lightskyblue; border-color: lightgray; opacity: 0.9;" href="?pagenr=1">First</a></li>
                    <?php
                        if (isset($_GET["pagenr"]) && $_GET["pagenr"] > 1 ) { ?>
                            <li class="page-item"><a class="page-link" style="background-color: lightskyblue; border-color: lightgray; opacity: 0.9;" href="?pagenr=<?php echo $_GET["pagenr"] - 1; ?>">Previous</a></li>
                        <?php } else { ?>
                            <li class="page-item"><a class="page-link" style="background-color: lightskyblue; border-color: lightgray; opacity: 0.9;" href="">Previous</a></li>
                       <?php }
                    ?>
                    <?php
                        for ($i = 1; $i <= $nbpages; $i++) { ?>
                            <li class="page-item"><a class="page-link" style="background-color: lightskyblue; border-color: lightgray; opacity: 0.9;" href="?pagenr=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                        <?php }
                    ?>
                    <?php
                        if (!isset($_GET["pagenr"])) { ?>
                            <li class="page-item"><a class="page-link" style="background-color: lightskyblue; border-color: lightgray; opacity: 0.9;" href="?pagenr=2">Next</a></li>
                        <?php } else if ($_GET["pagenr"] >= $nbpages) { ?>
                            <li class="page-item"><a class="page-link" style="background-color: lightskyblue; border-color: lightgray; opacity: 0.9;" href="">Next</a></li>
                       <?php } else { ?>
                        <li class="page-item"><a class="page-link" style="background-color: lightskyblue; border-color: lightgray; opacity: 0.9;" href="?pagenr=<?php echo $_GET["pagenr"] + 1; ?>">Next</a></li>
                       <?php }
                    ?>
                    <li class="page-item"><a class="page-link" style="background-color: lightskyblue; border-color: lightgray; opacity: 0.9;" href="?pagenr=<?php echo $nbpages; ?>">Last</a></li>
                </ul>
                <style>
                    .x {
                        width: 250px;
                    }
                </style>
                <input type="submit" id="but" name="withhisback" class="x but text-center" value="Back to the welcome page" formnovalidate>
        </form>
    </div>
    <script src="./bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
<?php } else if (isset($_SESSION['withhis_show']) && $withhis_show === "fail") { ?>
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
                <h1 style="text-align: center;">Error 404 : Not Found</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php } ?>
<?php if (isset($_SESSION['wirehis_show']) && $wirehis_show === "success") {  ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>WSYM Banking</title>
        <link rel="shortcut icon" href="./data/favicon.ico" type="image/x-icon">
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300&family=Open+Sans+Condensed:wght@300&display=swap" rel="stylesheet">
        <link href="./bootstrap-5.0.2-dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="./css/welcome_admin_forms.css" rel="stylesheet">
    </head>
    <body>
        <div class="container1">
            <form method="post" class="container2">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Wire Receiver</th>
                        <th>Wire Date</th>
                        <th>Wire Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($wirehis_query_array_all as $sub_array): ?>
                            <tr>
                                <?php foreach ($sub_array as $value): ?>
                                    <td><?php echo $value; ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php
                    if (!isset($_GET["pagenr"])) { 
                        $page = 1;
                    } else {
                        $page = $_GET["pagenr"];
                    }
                ?>
                <div class="d-flex gap-3 align-items-center">
                    <div>Showing <?php echo $page; ?> out of <?php echo $nbpages; ?> pages</div>
                    <select class="form-select-sm" name="filters" id="filters">
                        <option value="">No Filter</option>
                        <option value="Date : Most Recent">Date : Most Recent</option>
                        <option value="Date : Oldest">Date : Oldest</option>
                        <option value="Amount : High to Low">Amount : High to Low</option>
                        <option value="Amount : Low to High">Amount : Low to High</option>
                    </select>
                </div>
                <ul class="pagination mt-3">
                    <li class="page-item"><a class="page-link" style="background-color: lightskyblue; border-color: lightgray; opacity: 0.9;" href="?pagenr=1">First</a></li>
                    <?php
                        if (isset($_GET["pagenr"]) && $_GET["pagenr"] > 1 ) { ?>
                            <li class="page-item"><a class="page-link" style="background-color: lightskyblue; border-color: lightgray; opacity: 0.9;" href="?pagenr=<?php echo $_GET["pagenr"] - 1; ?>">Previous</a></li>
                        <?php } else { ?>
                            <li class="page-item"><a class="page-link" style="background-color: lightskyblue; border-color: lightgray; opacity: 0.9;" href="">Previous</a></li>
                       <?php }
                    ?>
                    <?php
                        for ($i = 1; $i <= $nbpages; $i++) { ?>
                            <li class="page-item"><a class="page-link" style="background-color: lightskyblue; border-color: lightgray; opacity: 0.9;" href="?pagenr=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                        <?php }
                    ?>
                    <?php
                        if (!isset($_GET["pagenr"])) { ?>
                            <li class="page-item"><a class="page-link" style="background-color: lightskyblue; border-color: lightgray; opacity: 0.9;" href="?pagenr=2">Next</a></li>
                        <?php } else if ($_GET["pagenr"] >= $nbpages) { ?>
                            <li class="page-item"><a class="page-link" style="background-color: lightskyblue; border-color: lightgray; opacity: 0.9;" href="">Next</a></li>
                       <?php } else { ?>
                        <li class="page-item"><a class="page-link" style="background-color: lightskyblue; border-color: lightgray; opacity: 0.9;" href="?pagenr=<?php echo $_GET["pagenr"] + 1; ?>">Next</a></li>
                       <?php }
                    ?>
                    <li class="page-item"><a class="page-link" style="background-color: lightskyblue; border-color: lightgray; opacity: 0.9;" href="?pagenr=<?php echo $nbpages; ?>">Last</a></li>
                </ul>
                <style>
                    .x {
                        width: 250px;
                    }
                </style>
                <input type="submit" id="but" name="wirehisback" class="x but text-center" value="Back to the welcome page" formnovalidate>
        </form>
    </div>
    <script src="./bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
<?php } else if (isset($_SESSION['wirehis_show']) && $wirehis_show === "fail") { ?>
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
                <h1 style="text-align: center;">Error 404 : Not Found</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php } ?>