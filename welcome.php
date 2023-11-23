<?php
require ("linking.php");
if ($con->connect_error) {
    die("Connection Failed" . $con->connect_error);
} else {
    session_start();
    $username = $_SESSION['username'];
    if(empty($_SESSION['username']) || $_SESSION['username'] == ''){
        header("Location: index.html");
        die();
    }
    $balance_query = mysqli_query($con,"SELECT balance FROM balance WHERE username = '$username'");
    if (mysqli_num_rows($balance_query) > 0) {
        $balance_array = mysqli_fetch_all($balance_query, MYSQLI_ASSOC);
        foreach ($balance_array as $row) {
            $balance = $row["balance"];
        }
    }
    $last_deposit_query = mysqli_query($con,"SELECT deposit_date,deposit_amount FROM deposit WHERE username = '$username' AND deposit_date = (SELECT MAX(deposit_date) FROM deposit)");
    if (mysqli_num_rows($last_deposit_query) > 0) {
        $last_deposit_array = mysqli_fetch_all($last_deposit_query, MYSQLI_ASSOC);
        foreach ($last_deposit_array as $row) {
            $last_deposit_date = $row["deposit_date"];
            $last_deposit_amount = $row["deposit_amount"];
            $dep_msg = "Last Known Deposit was at $last_deposit_date with a value of $$last_deposit_amount";
        }
    } else {
        $dep_msg = "There was no previous deposits";
    }
    $last_withdraw_query = mysqli_query($con,"SELECT withdraw_date,withdraw_amount FROM withdraw WHERE username = '$username' AND withdraw_date = (SELECT MAX(withdraw_date) FROM withdraw)");
    if (mysqli_num_rows($last_withdraw_query) > 0) {
        $last_withdraw_array = mysqli_fetch_all($last_withdraw_query, MYSQLI_ASSOC);
        foreach ($last_withdraw_array as $row) {
            $last_withdraw_date = $row["withdraw_date"];
            $last_withdraw_amount = $row["withdraw_amount"];
            $with_msg = "Last Known Withdraw was at $last_withdraw_date with a value of $$last_withdraw_amount";
        }
    } else {
        $with_msg = "There was no previous withdraws";
    }
    $last_wire_query = mysqli_query($con,"SELECT wire_date,wire_amount,receiver FROM wire WHERE username = '$username' AND wire_date = (SELECT MAX(wire_date) FROM wire)");
    if (mysqli_num_rows($last_wire_query) > 0) {
        $last_wire_array = mysqli_fetch_all($last_wire_query, MYSQLI_ASSOC);
        foreach ($last_wire_array as $row) {
            $last_wire_date = $row["wire_date"];
            $last_wire_amount = $row["wire_amount"];
            $last_wire_rec = $row["receiver"];
            $wire_msg = "Last Known Wire was at $last_wire_date with a value of $$last_wire_amount to $last_wire_rec";
        }
    } else {
        $wire_msg = "There was no previous wires";
    }
    if (isset($_POST['deposit_submit'])) {
        $depval = filter_input(INPUT_POST,"depval",FILTER_SANITIZE_NUMBER_FLOAT);
        $_SESSION['depval'] = $depval;
        if (mysqli_query($con,"INSERT INTO deposit VALUES (id,'$username',now(),$depval)") && mysqli_query($con,"UPDATE balance SET balance = balance + $depval WHERE username = '$username'")) {
            $_SESSION['deposit_verif'] = "success";
            header("Location: welcome_verif.php");
            exit;
        } else {
            $_SESSION['deposit_verif'] = "fail";
            header("Location: welcome_verif.php");
            exit;
        }
    }
    if (isset($_POST['withdraw_submit'])) {
        $withval = filter_input(INPUT_POST,"withval",FILTER_SANITIZE_NUMBER_FLOAT);
        $_SESSION['withval'] = $withval;
        if (mysqli_query($con,"INSERT INTO withdraw VALUES (id,'$username',now(),$withval)") && mysqli_query($con,"UPDATE balance SET balance = balance - $withval WHERE username = '$username'")) {
            $_SESSION['withdraw_verif'] = "success";
            header("Location: welcome_verif.php");
            exit;
        } else {
            $_SESSION['withdraw_verif'] = "fail";
            header("Location: welcome_verif.php");
            exit;
        }
    }
    if (isset($_POST['wire_submit'])) {
        $wireval = filter_input(INPUT_POST,"wireval",FILTER_SANITIZE_NUMBER_FLOAT);
        $wireemail = filter_input(INPUT_POST,"wireemail",FILTER_SANITIZE_EMAIL);
        $_SESSION['wireval'] = $wireval;
        $_SESSION['wireemail'] = $wireemail;
        if (mysqli_query($con,"INSERT INTO wire VALUES (id,'$username','$wireemail',now(),$wireval)") && mysqli_query($con,"UPDATE balance SET balance = balance + $wireval WHERE email = '$wireemail'") && mysqli_query($con,"UPDATE balance SET balance = balance - $wireval WHERE username = '$username'")) {
            $_SESSION['wire_verif'] = "success";
            header("Location: welcome_verif.php");
            exit;
        } else {
            $_SESSION['wire_verif'] = "fail";
            header("Location: welcome_verif.php");
            exit;
        }
    }
    if (isset($_POST['ticket_submit'])) {
        $tickettext = filter_input(INPUT_POST,"tickettext",FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (mysqli_query($con,"INSERT INTO ticket VALUES ('$username','$tickettext')")) {
            $_SESSION['ticket_verif'] = "success";
            header("Location: welcome_verif.php");
            exit;
        } else {
            $_SESSION['ticket_verif'] = "fail";
            header("Location: welcome_verif.php");
            exit;
        }
    }
    if (isset($_POST['logout'])) {
        session_destroy();
        header("Location: index.html");
        exit;
    }
    $now = time();
    if($now > $_SESSION['expire']) { 
        session_destroy();
        $_SESSION['session_verif'] = "success";
        header("Location: welcome_verif.php");
        exit;
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
    <link rel="stylesheet" href="./css/welcome_style.css">
    <link rel="stylesheet" href="./font-awesome-4.7.0/css/font-awesome.min.css">
</head>
<body>
    <header>
        <nav class="container3">
            <ul class="container2" id="balancev">
                <img src="./data/favicon.ico" alt="" width="50px" height="50px">
                <li>WSYM Banking</li>
            </ul>
            <ul class="container1">
                <li class="navitem" id="depositv">Deposit Money</li>
                <li class="navitem" id="withdrawv">Withdraw Money</li>
                <li class="navitem" id="wirev">Wire Money</li>
                <li class="navitem" id="ticketv">Submit a Ticket</li>
                <form method="post"><input class="logoutbut" name="logout" type="submit" value="logout" onclick="return logoutconfirm()" id="log"></form>
            </ul>
        </nav>
    </header>
    <main>
        <div class="container4 balance" id="balance">
            <h1>Welcome Back</h1>
            <h2>You are logged in as <?php echo "$username"?></h2>
            <div class="push">Your Balance is : $<?php echo "$balance"?></div>
            <div style="font-size: 15px; text-align: center;"><?php echo "$dep_msg" ?></div>
            <div style="font-size: 15px; text-align: center;"><?php echo "$with_msg" ?></div>
            <div class="push2" style="font-size: 15px; text-align: center;"><?php echo "$wire_msg" ?></div>
        </div>
        <form method="post" class="container4 deposit" id="deposit">
            <h1 class="deptitle">Deposit Money</h1>
            <label class="labbor">
                <img src="./data/dollar.svg" alt="">
                <input type="text" placeholder="Value in Dollars" name="depval" id="depval" required>
            </label>
            <div>
                <span>Predefined Values : </span>
                <input type="button" value="$10" id="ten" class="vals valbuts">
                <input type="button" value="$20" id="twenty" class="vals valbuts">
                <input type="button" value="$30" id="thirty" class="vals valbuts">
                <input type="button" value="$40" id="forty" class="vals valbuts">
                <input type="button" value="$50" id="fifty" class="vals valbuts">
            </div>
            <input type="submit" value="Deposit" class="but" id="depositbut" name="deposit_submit" onclick="return depositconfirm()">
        </form>
        <form method="post" class="container4 withdraw" id="withdraw">
            <h1 class="deptitle">Withdraw Money</h1>
            <label class="labbor">
                <img src="./data/dollar.svg" alt="">
                <input type="text" placeholder="Value in Dollars" name="withval" id="withval" required>
            </label>
            <div>
                <span>Predefined Values : </span>
                <input type="button" value="$10" id="tenn" class="vals valbuts">
                <input type="button" value="$20" id="twentyy" class="vals valbuts">
                <input type="button" value="$30" id="thirtyy" class="vals valbuts">
                <input type="button" value="$40" id="fortyy" class="vals valbuts">
                <input type="button" value="$50" id="fiftyy" class="vals valbuts">
            </div>
            <input type="submit" value="Withdraw" class="but" id="withdrawbut" name="withdraw_submit" onclick="return withdrawconfirm()">
        </form>
        <form method="post" class="container4 wire" id="wire">
            <h1>Wire Money</h1>
            <div class="desc">Insert the email of the person you want to wire the money to</div>
            <label class="labbor lab">
                <img src="./data/mail.svg" alt="">
                <input type="email" placeholder="Email" name="wireemail" id="wireres" required>
            </label>
            <label class="labbor">
                <img src="./data/dollar.svg" alt="">
                <input type="text" placeholder="Value in Dollars" name="wireval" id="wireval" required>
            </label>
            <div>
                <span>Predefined Values : </span>
                <input type="button" value="$10" id="tennn" class="vals valbuts">
                <input type="button" value="$20" id="twentyyy" class="vals valbuts">
                <input type="button" value="$30" id="thirtyyy" class="vals valbuts">
                <input type="button" value="$40" id="fortyyy" class="vals valbuts">
                <input type="button" value="$50" id="fiftyyy" class="vals valbuts">
            </div>
            <input type="submit" value="Wire the Money" class="but" id="wirebut" name="wire_submit" onclick="return wireconfirm()">
        </form>
        <form method="post" class="container4 ticket" id="ticket">
            <h1 class="deptitle">Submit a ticket</h1>
            <label>
                <textarea id="tick" name="tickettext" cols="40" rows="6"></textarea>
            </label>
            <input type="submit" value="Submit Ticket" class="but" id="ticketbut" name="ticket_submit" onclick="return ticketconfirm()">
        </form>
    </main>
    <script src="./js/welcome.js"></script>
</body>
</html>