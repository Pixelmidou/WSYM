<?php
require ("linking.php");
if ($con->connect_error) {
    die("Connection Failed" . $con->connect_error);
} else { 
    session_start();
    $verif_balance = $_SESSION["verif_balance"];
    if($verif_balance === "success") {
        $balance_query_array_all = $_SESSION['balance_query_array_all'];
        $verif_balance_case = $_SESSION['verif_balance_case'];
        switch ($verif_balance_case) {
            case "1":
                $balanceemail = $_SESSION["balanceemail"];
                $balance_query = mysqli_query($con,"SELECT * FROM balance WHERE email LIKE '%$balanceemail%'");
                break;
            case "2":
                $balanceusername = $_SESSION["balanceusername"];
                $balance_query = mysqli_query($con,"SELECT * FROM balance WHERE username LIKE '%$balanceusername%'");
                break;
            case "3":
                $balanceemail = $_SESSION["balanceemail"];
                $balanceusername = $_SESSION["balanceusername"];
                $balance_query = mysqli_query($con,"SELECT * FROM balance WHERE username LIKE '%$balanceusername%' OR email LIKE '%$balanceemail%'");
                break;
        }
        if (isset($_POST["balance_submit"])) {
            while ($row = mysqli_fetch_array($balance_query, MYSQLI_ASSOC)) {
                $username = filter_input(INPUT_POST,"username",FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                if (in_array($username,$row)) {
                    $bval = filter_input(INPUT_POST,"bval",FILTER_SANITIZE_NUMBER_FLOAT);
                    $bval = floatval($bval);
                    mysqli_query($con,"UPDATE balance SET balance = $bval WHERE username = '$username'");
                }
            }
        }

    }
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
    <link href="./bootstrap-5.0.2-dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <style>
        body {
            background: url(./data/background.jpg);
        }
        .container1 {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container2 {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #474646;
            background-color: lightgray;
            padding: 10px 100px 10px 100px;
            width: 700px;
            min-height: max-content;
            border-top: 10px solid #87cefa;
            border-bottom: 10px solid #87cefa;
        }
        .but {
            width: calc(100% - 180px);
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
        input[type=text]:focus,input[type=email]:focus,.valbuts:hover {
            background: none;
            border: 0;
            caret-color: lightskyblue;
            outline: 0;
            color: lightskyblue;
        }
        input[type=text],.valbuts,input[type=email] {
            background: none;
            border: 0;
            margin-left: 5px;
            margin-top: 10px;
            margin-bottom: 10px;
            padding: 10px;
            color: white;
            font-size: medium;
        }
        .labbor {
            border-bottom: 1px solid #87cefa;
            width: 300px;
            height: 50px;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .labbor img {
            width: 20px;
            height: 20px;
        }
        ::placeholder {
            color: white;
            font-size: medium;
        }
        #but {
            width: 100%;
            padding-left: 50px;
            padding-right: 50px;
        }
    </style>
    <div class="container1">
        <?php if($verif_balance === "success"): ?>
        <form method="post" class="container2">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Balance</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($balance_query_array_all as $sub_array): ?>
                    <tr>
                        <?php foreach ($sub_array as $value): ?>
                            <td><?php echo $value; ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <div class="mb-2 mt-2">P.S. : If you just want to check balances omit these inputs</div>
            <div class="d-flex gap-4">
                <label class="labbor lab">
                  <img src="./data/user.svg" alt="">
                  <input type="text" placeholder="Username" name="username" required>
                </label>
                <label class="labbor lab">
                  <img src="./data/dollar.svg" alt="">
                  <input type="text" placeholder="Value in Dollars" name="bval" required>
                </label>
            </div>
            <div class="d-flex gap-4 mt-2 mb-2">
                <input type="submit" class="but text-center" id="but" value="Submit Change" name="balance_submit">
                <a href="welcome_admin.php"><input type="button" id="but" class="but text-center" value="Back to the admin page" onclick="location.reload()"></a>
            </div>
        </form>
        <?php endif; ?>
        <?php if($verif_balance === "fail"): ?>
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <meta http-equiv="refresh" content="4; url=welcome_admin.php">
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
        <?php endif; ?>
    </div>
    <script src="./bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>