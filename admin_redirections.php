<?php
session_start();
$admin_username = $_SESSION['admin_username'];
$_SESSION['user_username'] = $admin_username;
if(empty($_SESSION['admin_username']) || $_SESSION['admin_username'] == ''){
    session_destroy();
    header("Location: index.php");
    exit;
}
if (isset($_POST["adminpage"])) {
    header("Location: welcome_admin.php");
    exit;
}
if (isset($_POST["userpage"])) {
    header("Location: welcome.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WSYM Banking</title>
    <link rel="shortcut icon" href="./data/favicon.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300&family=Open+Sans+Condensed:wght@300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/redirections_style.css">
    <link href="./bootstrap-5.0.2-dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <style>
        .container2 {
            all: unset;
        }
        .container2 {
            background-color: lightgray;
            width: 700px;
            height: 420px;
            border-top: 10px solid #87cefa;
            border-bottom: 10px solid #87cefa;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .but {
            width: 200px;
            margin-bottom: auto;
            padding: 10px;
            border-radius: 5px;
            background-color: lightskyblue;
            transition: all 0.2s ease-in-out;
            cursor: pointer;
            border: none;
            outline: none;
            color: white;
            opacity: 0.7;
        }
        .but:hover {
            opacity: 1;
        }
        h2,h4 {
            color: whitesmoke;
        }
    </style>
    <div class="container1">
        <div class="container2">
            <h2 class="mt-auto">Admin Redirections</h2>
            <h4 class="mt-1 mb-auto">Welcome Back <?php echo $admin_username; ?> !</h4>
            <form method="post" class="mb-auto d-flex gap-3">
                <input class="but" type="submit" name="adminpage" value="Your Admin Page">
                <input class="but" type="submit" name="userpage" value="Your User Page">
            </form>
        </div>
    </div>
    <script src="./bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>