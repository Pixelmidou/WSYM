<?php
require ("linking.php");
if ($con->connect_error) {
    die("Connection Failed" . $con->connect_error);
} else {
    session_start();
    if(empty($_SESSION['user_username']) || $_SESSION['user_username'] == ''){
        session_destroy();
        header("Location: index.php");
        die();
    }
    if (isset($_POST['logout'])) {
        session_destroy();
        header("Location: index.php");
        exit;
    }
    $now = time();
    if($now > $_SESSION['expire']) { 
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
                    <h1 style="text-align: center;">Session expired : You need to login again !</h1>
                    <div style="text-align: center; font-size: medium;">Any action attempted was stopped and interrupted.</div>
                    <div style="text-align: center; font-size: small;">You will be automatically redirected back to the login page in 4 seconds.</div>
                </div>
            </div>
        </body>
        </html>
    <?php die(); } else {
        $page_load = true;
        $user_username = $_SESSION['user_username'];
        $pfp_query = mysqli_query($con,"SELECT pfp FROM login_credentials WHERE username = '$user_username'");
        if (mysqli_num_rows($pfp_query) > 0) {
            $pfp_array = mysqli_fetch_all($pfp_query, MYSQLI_ASSOC);
            foreach ($pfp_array as $row) {
                $pfp = $row["pfp"];
            }
        }
        $balance_query = mysqli_query($con,"SELECT balance FROM balance WHERE username = '$user_username'");
        if (mysqli_num_rows($balance_query) > 0) {
            $balance_array = mysqli_fetch_all($balance_query, MYSQLI_ASSOC);
            foreach ($balance_array as $row) {
                $balance = $row["balance"];
            }
        }
        $last_deposit_query = mysqli_query($con,"SELECT deposit_date,deposit_amount FROM deposit WHERE username = '$user_username' AND deposit_date = (SELECT MAX(deposit_date) FROM deposit)");
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
        $last_withdraw_query = mysqli_query($con,"SELECT withdraw_date,withdraw_amount FROM withdraw WHERE username = '$user_username' AND withdraw_date = (SELECT MAX(withdraw_date) FROM withdraw)");
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
        $last_wire_query = mysqli_query($con,"SELECT wire_date,wire_amount,receiver FROM wire WHERE username = '$user_username' AND wire_date = (SELECT MAX(wire_date) FROM wire)");
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
        $email_verif_query = mysqli_query($con,"SELECT email_verif FROM login_credentials WHERE username = '$user_username'");
        if (mysqli_num_rows($email_verif_query) > 0) {
            $email_verif_query_array = mysqli_fetch_all($email_verif_query, MYSQLI_ASSOC);
            foreach ($email_verif_query_array as $row) {
                $email_verif = $row["email_verif"];
            }
        }
        if (isset($_POST['deposit_submit'])) {
            $depval = filter_input(INPUT_POST,"depval",FILTER_SANITIZE_NUMBER_FLOAT);
            $_SESSION['depval'] = $depval;
            $dep_query = mysqli_query($con,"SELECT deposit FROM blacklist WHERE username = '$user_username'");
            if (mysqli_num_rows($dep_query) > 0) {
                $dep_query_array = mysqli_fetch_all($dep_query, MYSQLI_ASSOC);
                foreach ($dep_query_array as $row) {
                    $dep = $row["deposit"];
                }
            }
            if ($email_verif === "1" && $dep === "1" && mysqli_query($con,"INSERT INTO deposit VALUES (id,'$user_username',now(),$depval)") && mysqli_query($con,"UPDATE balance SET balance = balance + $depval WHERE username = '$user_username'")) {
                $_SESSION['deposit_verif'] = "success";
                header("Location: welcome_verif.php");
                exit;
            } else if ($dep === "0" && $email_verif === "0") {
                $_SESSION['deposit_verif'] = "failbv";
                header("Location: welcome_verif.php");
                exit;
            } else if ($dep === "0") {
                $_SESSION['deposit_verif'] = "failb";
                header("Location: welcome_verif.php");
                exit;
            } else if ($email_verif === "0") {
                $_SESSION['deposit_verif'] = "failv";
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
            $with_query = mysqli_query($con,"SELECT withdraw FROM blacklist WHERE username = '$user_username'");
            if (mysqli_num_rows($with_query) > 0) {
                $with_query_array = mysqli_fetch_all($with_query, MYSQLI_ASSOC);
                foreach ($with_query_array as $row) {
                    $with = $row["withdraw"];
                }
            }
            if ($email_verif === "1" && $with === "1" && mysqli_query($con,"INSERT INTO withdraw VALUES (id,'$user_username',now(),$withval)") && mysqli_query($con,"UPDATE balance SET balance = balance - $withval WHERE username = '$user_username'")) {
                $_SESSION['withdraw_verif'] = "success";
                header("Location: welcome_verif.php");
                exit;
            } else if ($with === "0" && $email_verif === "0") {
                $_SESSION['withdraw_verif'] = "failbv";
                header("Location: welcome_verif.php");
                exit;
            } else if ($with === "0") {
                $_SESSION['withdraw_verif'] = "failb";
                header("Location: welcome_verif.php");
                exit;
            } else if ($email_verif === "0") {
                $_SESSION['withdraw_verif'] = "failv";
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
            $wire_query = mysqli_query($con,"SELECT wire FROM blacklist WHERE username = '$user_username'");
            if (mysqli_num_rows($wire_query) > 0) {
                $wire_query_array = mysqli_fetch_all($wire_query, MYSQLI_ASSOC);
                foreach ($wire_query_array as $row) {
                    $wire = $row["wire"];
                }
            }
            if ($email_verif === "1" && $wire === "1" && mysqli_query($con,"INSERT INTO wire VALUES (id,'$user_username','$wireemail',now(),$wireval)") && mysqli_query($con,"UPDATE balance SET balance = balance + $wireval WHERE email = '$wireemail'") && mysqli_query($con,"UPDATE balance SET balance = balance - $wireval WHERE username = '$user_username'")) {
                $_SESSION['wire_verif'] = "success";
                header("Location: welcome_verif.php");
                exit;
            } else if ($wire === "0" && $email_verif === "0") {
                $_SESSION['wire_verif'] = "failbv";
                header("Location: welcome_verif.php");
                exit;
            } else if ($wire === "0") {
                $_SESSION['wire_verif'] = "failb";
                header("Location: welcome_verif.php");
                exit;
            } else if ($email_verif === "0") {
                $_SESSION['wire_verif'] = "failv";
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
            $ticket_query = mysqli_query($con,"SELECT ticket FROM blacklist WHERE username = '$user_username'");
            if (mysqli_num_rows($ticket_query) > 0) {
                $ticket_query_array = mysqli_fetch_all($ticket_query, MYSQLI_ASSOC);
                foreach ($ticket_query_array as $row) {
                    $ticket = $row["ticket"];
                }
            }
            if ($ticket === "1" && mysqli_query($con,"INSERT INTO ticket VALUES ('$user_username','opened',now(),DEFAULT,'$tickettext')")) {
                $_SESSION['ticket_verif'] = "success";
                header("Location: welcome_verif.php");
                exit;
            } else if ($ticket === "0") {
                $_SESSION['ticket_verif'] = "failb";
                header("Location: welcome_verif.php");
                exit;
            } else {
                $_SESSION['ticket_verif'] = "fail";
                header("Location: welcome_verif.php");
                exit;
            }
        }
        if (isset($_POST['sub_user'])) {
            $_SESSION['verif_id'] = false;
            $_SESSION['setting'] = "user";
            header("Location: account_settings.php");
            exit;
        }
        if (isset($_POST['sub_mail'])) {
            $_SESSION['verif_id'] = false;
            $_SESSION['setting'] = "mail";
            header("Location: account_settings.php");
            exit;
        }
        if (isset($_POST['sub_pass'])) {
            $_SESSION['verif_id'] = false;
            $_SESSION['setting'] = "pass";
            header("Location: account_settings.php");
            exit;
        }
        if (isset($_POST['dephis_submit'])) {
            $dephis_query = mysqli_query($con,"SELECT deposit_date,deposit_amount FROM deposit WHERE username LIKE '$user_username'");
            if (mysqli_num_rows($dephis_query) > 0) {
                $_SESSION["dephis_show"] = "success";
                header("Location: welcome_verif.php");
                exit;
            } else {
                $_SESSION['dephis_show'] = "fail";
                header("Location: welcome_verif.php");
                exit;
            }
        }
        if (isset($_POST['withhis_submit'])) {
            $withhis_query = mysqli_query($con,"SELECT withdraw_date,withdraw_amount FROM withdraw WHERE username LIKE '$user_username'");
            if (mysqli_num_rows($withhis_query) > 0) {
                $_SESSION["withhis_show"] = "success";
                header("Location: welcome_verif.php");
                exit;
            } else {
                $_SESSION['withhis_show'] = "fail";
                header("Location: welcome_verif.php");
                exit;
            }
        }
        if (isset($_POST['wirehis_submit'])) {
            $wirehis_query = mysqli_query($con,"SELECT receiver,wire_date,wire_amount FROM wire WHERE username LIKE '$user_username'");
            if (mysqli_num_rows($wirehis_query) > 0) {
                $_SESSION["wirehis_show"] = "success";
                header("Location: welcome_verif.php");
                exit;
            } else {
                $_SESSION['wirehis_show'] = "fail";
                header("Location: welcome_verif.php");
                exit;
            }
        }
        switch ($_SESSION) {
            case isset($_SESSION['dep']):
                unset($_SESSION['deposit_verif']);
                unset($_SESSION['dep']);
                break;
            case isset($_SESSION['withd']):
                unset($_SESSION['withdraw_verif']);
                unset($_SESSION['withd']);
                break;
            case isset($_SESSION['wire']):
                unset($_SESSION['wire_verif']);
                unset($_SESSION['wire']);
                break;
            case isset($_SESSION['tick']):
                unset($_SESSION['ticket_verif']);
                unset($_SESSION['tick']);
                break;
            case isset($_SESSION['accset']):
                unset($_SESSION['verif_id']);
                unset($_SESSION['setting']);
                unset($_SESSION['accset']);
                break;
            case isset($_SESSION['dephis']):
                unset($_SESSION['dephis_show']);
                unset($_SESSION['dephis']);
                break;
            case isset($_SESSION['withhis']):
                unset($_SESSION['withhis_show']);
                unset($_SESSION['withhis']);
                break;
            case isset($_SESSION['wirehis']):
                unset($_SESSION['wirehis_show']);
                unset($_SESSION['wirehis']);
                break;
        }
    }
}
?>
<?php if(isset($page_load) && $page_load === true): ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WSYM Banking</title>
    <link rel="shortcut icon" href="./data/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="./css/welcome_style.css">
    <link rel="stylesheet" href="./font-awesome-4.7.0/css/font-awesome.min.css">
    <link href="./bootstrap-5.0.2-dist/css/bootstrap.min.css" rel="stylesheet">
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
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle dr" data-bs-toggle="dropdown" aria-expanded=""fail"">
                    <img src="./data/uploads/<?php echo $pfp ?>" alt="pfp" width="32" height="32" class="rounded-circle me-2" id="output">
                    <strong><?php echo "$user_username"; ?></strong>
                </a>
                <ul class="dropdown-menu text-small shadow">
                    <li><form method="post"><input class="dropdown-item" name="sub_user" type="submit" value="Change Your Username"></form></li>
                    <li><form method="post"><input class="dropdown-item" name="sub_mail" type="submit" value="Change Your Email"></form></li>
                    <li><form method="post"><input class="dropdown-item" name="sub_pass" type="submit" value="Change Your Password"></form></li>
                    <li>
                        <form method="post" action="uploadimg.php" enctype="multipart/form-data" class="dropdown-item">
                            <hr>
                            <label for="lb" class="wh">
                                <div>
                                    <div class="mb-2">Upload Your Photo :</div>
                                    <input id="lb" type="file" accept="image/png, image/jpeg, image/jpg, image/gif" name="imgupload" required>
                                </div>
                                <div class="d-flex gap-2 mt-2 align-items-center justify-content-center">
                                    <input type="submit" value="Upload Image" name="imgsub">
                                    <input type="submit" value="Delete Image" name="imgdel" formnovalidate>
                                </div>
                            </label>
                            <hr>
                        </form>
                    </li>
                    <?php if (isset($_SESSION['admin_username'])): ?>
                        <li><a href="admin_redirections.php" class="dropdown-item">Go Back to Redirections</a></li>
                    <?php endif; ?>
                    <?php if ($email_verif === "0"): ?>
                        <li><form action="resend_email_verif.php" method="post"><input class="dropdown-item" name="resend_sub" type="submit" value="Resend Email Verification" onclick="return resendconfirm()"></form></li>
                    <?php endif; ?>
                    <li><form method="post"><input class="dropdown-item" name="logout" type="submit" value="Sign Out" onclick="return logoutconfirm()" id="log"></form></li>
                </ul>
            </ul>
        </nav>
    </header>
    <main>
        <div class="container4 balance" id="balance">
            <h1>Welcome Back</h1>
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
            <div class="d-flex gap-3 mt-auto">
                <style>
                    .x1 {
                        width: 250px;
                    }
                </style>
                <input type="submit" value="Deposit" class="but x1" id="depositbut" name="deposit_submit" onclick="return depositconfirm()">
                <input type="submit" value="Deposit History" class="but x1" id="" name="dephis_submit" formnovalidate>
            </div>
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
            <div class="d-flex gap-3 mt-auto">
                <style>
                    .x2 {
                        width: 250px;
                    }
                </style>
                <input type="submit" value="Withdraw" class="but x2" id="withdrawbut" name="withdraw_submit" onclick="return withdrawconfirm()">
                <input type="submit" value="Withdraw History" class="but x2" id="" name="withhis_submit" formnovalidate>
            </div>
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
            <div class="d-flex gap-3 mt-auto">
                <style>
                    .x3 {
                        width: 250px;
                    }
                </style>
                <input type="submit" value="Wire the Money" class="but x3" id="wirebut" name="wire_submit" onclick="return wireconfirm()">
                <input type="submit" value="Wire History" class="but x3" id="" name="wirehis_submit" formnovalidate>
            </div>
        </form>
        <form method="post" class="container4 ticket" id="ticket">
            <h1 class="deptitle">Submit a ticket</h1>
            <div class="desc">P.S. : Only one ticket at time can be opened</div>
            <label>
                <textarea id="tick" name="tickettext" cols="40" rows="6"></textarea>
            </label>
            <input type="submit" value="Submit Ticket" class="but" id="ticketbut" name="ticket_submit" onclick="return ticketconfirm()">
        </form>
    </main>
    <script src="./js/welcome.js"></script>
    <script src="./bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php endif; ?>