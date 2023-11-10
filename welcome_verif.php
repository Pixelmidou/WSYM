<?php
session_start();
$deposit_verif = filter_input(INPUT_GET,"deposit_verif",FILTER_SANITIZE_FULL_SPECIAL_CHARS);
if ($deposit_verif == "success") {
    $depval = $_SESSION['depval'];
}
$withdraw_verif = filter_input(INPUT_GET,"withdraw_verif",FILTER_SANITIZE_FULL_SPECIAL_CHARS);
if ($withdraw_verif == "success") {
    $withval = $_SESSION['withval'];
}
$wire_verif = filter_input(INPUT_GET,"wire_verif",FILTER_SANITIZE_FULL_SPECIAL_CHARS);
if ($wire_verif == "success") {
    $wireval = $_SESSION['wireval'];
    $wireemail = $_SESSION['wireemail'];
}
$ticket_verif = filter_input(INPUT_GET,"ticket_verif",FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$session_verif = filter_input(INPUT_GET,"session_verif",FILTER_SANITIZE_FULL_SPECIAL_CHARS);
?>
<!DOCTYPE html>
<html lang="en">
<?php if ($deposit_verif == "success") {  ?>
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
<?php } else if ($deposit_verif == "fail") { ?>
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
<?php if ($withdraw_verif == "success") {  ?>
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
<?php } else if ($withdraw_verif == "fail") { ?>
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
<?php if ($wire_verif == "success") {  ?>
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
<?php } else if ($wire_verif == "fail") { ?>
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
<?php if ($ticket_verif == "success") {  ?>
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
<?php } else if ($ticket_verif == "fail") { ?>
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
<?php if ($session_verif == "true") {  ?>
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