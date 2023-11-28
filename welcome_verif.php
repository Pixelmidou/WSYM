<?php
session_start();
if (isset($_SESSION['deposit_verif'])) {
    unset($_SESSION['withdraw_verif']);
    unset($_SESSION['wire_verif']);
    unset($_SESSION['ticket_verif']);
    unset($_SESSION['session_verif']);
    $deposit_verif = $_SESSION['deposit_verif'];
    if ($deposit_verif === "success") {
        $depval = $_SESSION['depval'];
    }
}
if (isset($_SESSION['withdraw_verif'])) {
    unset($_SESSION['deposit_verif']);
    unset($_SESSION['wire_verif']);
    unset($_SESSION['ticket_verif']);
    unset($_SESSION['session_verif']);
    $withdraw_verif = $_SESSION['withdraw_verif'];
    if ($withdraw_verif === "success") {
        $withval = $_SESSION['withval'];
    }
}
if (isset($_SESSION['wire_verif'])) {
    unset($_SESSION['withdraw_verif']);
    unset($_SESSION['deposit_verif']);
    unset($_SESSION['ticket_verif']);
    unset($_SESSION['session_verif']);
    $wire_verif = $_SESSION['wire_verif'];
    if ($wire_verif === "success") {
        $wireval = $_SESSION['wireval'];
        $wireemail = $_SESSION['wireemail'];
    }
}
if (isset($_SESSION['ticket_verif'])) {
    unset($_SESSION['withdraw_verif']);
    unset($_SESSION['wire_verif']);
    unset($_SESSION['deposit_verif']);
    unset($_SESSION['session_verif']);
    $ticket_verif = $_SESSION['ticket_verif'];
}
if (isset($_SESSION['session_verif'])) {
    unset($_SESSION['withdraw_verif']);
    unset($_SESSION['wire_verif']);
    unset($_SESSION['ticket_verif']);
    unset($_SESSION['deposit_verif']);
    $ticket_verif = $_SESSION['ticket_verif'];
}
?>
<!DOCTYPE html>
<html lang="en">
<?php if (isset($_SESSION['deposit_verif']) && $deposit_verif === "success") {  ?>
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
<?php } else if (isset($_SESSION['deposit_verif']) && $deposit_verif === "fail") { ?>
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
<?php } ?>
<?php if (isset($_SESSION['withdraw_verif']) && $withdraw_verif === "success") {  ?>
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
            <h1 style="text-align: center;">Action Success : Money was withdrawn in your account !</h1>
            <h4 style="text-align: center;">A value of $<?php echo "$withval"; ?> was withdrawn from your account</h4>
            <div style="text-align: center; font-size: small;">You will be automatically redirected back to the welcome page in 4 seconds.</div>
        </div>
    </div>
</body>
<?php } else if (isset($_SESSION['withdraw_verif']) && $withdraw_verif === "fail") { ?>
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
<?php } ?>
<?php if (isset($_SESSION['wire_verif']) && $wire_verif === "success") {  ?>
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
<?php } else if (isset($_SESSION['wire_verif']) && $wire_verif === "fail") { ?>
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
<?php } ?>
<?php if (isset($_SESSION['ticket_verif']) && $ticket_verif === "success") {  ?>
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
<?php } else if (isset($_SESSION['ticket_verif']) && $ticket_verif === "fail") { ?>
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
<?php } ?>
<?php if (isset($_SESSION['session_verif']) && $session_verif === "success") {  ?>
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
            <h1 style="text-align: center;">Session expired : You need to login again !</h1>
            <div style="text-align: center; font-size: small;">You will be automatically redirected back to the login page in 4 seconds.</div>
        </div>
    </div>
</body>
<?php } ?>
</html>