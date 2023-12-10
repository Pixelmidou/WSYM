<?php
session_start();
if (!isset($_SESSION['deposit_verif']) && !isset($_SESSION['withdraw_verif']) && !isset($_SESSION['wire_verif']) && !isset($_SESSION['ticket_verif'])) {
    if (empty($_SESSION['deposit_verif']) || $_SESSION['deposit_verif'] === "" && empty($_SESSION['withdraw_verif']) || $_SESSION['withdraw_verif'] === "" && empty($_SESSION['wire_verif']) || $_SESSION['wire_verif'] === ""  && empty($_SESSION['ticket_verif']) || $_SESSION['ticket_verif'] === "") {
        session_destroy();
        header("Location: index.html");
        exit;
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