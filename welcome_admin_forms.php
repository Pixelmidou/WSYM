<?php
require ("linking.php");
if ($con->connect_error) {
    die("Connection Failed" . $con->connect_error);
} else { 
    session_start();
    if (isset($_SESSION["verif_balance"])) {
        $verif_balance = $_SESSION["verif_balance"];
        if($verif_balance === true) {
            $verif_balance_case = $_SESSION['verif_balance_case'];
            switch ($verif_balance_case) {
                case "1":
                    $balanceemail = $_SESSION["balanceemail"];
                    $balance_query1 = mysqli_query($con,"SELECT * FROM balance WHERE email LIKE '%$balanceemail%'");
                    $balance_query2 = mysqli_query($con,"SELECT * FROM balance WHERE email LIKE '%$balanceemail%'");
                    break;
                case "2":
                    $balanceusername = $_SESSION["balanceusername"];
                    $balance_query1 = mysqli_query($con,"SELECT * FROM balance WHERE username LIKE '%$balanceusername%'");
                    $balance_query2 = mysqli_query($con,"SELECT * FROM balance WHERE username LIKE '%$balanceusername%'");
                    break;
                case "3":
                    $balanceemail = $_SESSION["balanceemail"];
                    $balanceusername = $_SESSION["balanceusername"];
                    $balance_query1 = mysqli_query($con,"SELECT * FROM balance WHERE username LIKE '%$balanceusername%' AND email LIKE '%$balanceemail%'");
                    $balance_query2 = mysqli_query($con,"SELECT * FROM balance WHERE username LIKE '%$balanceusername%' AND email LIKE '%$balanceemail%'");
                    break;
            }
            $balance_query_array_all = mysqli_fetch_all($balance_query1, MYSQLI_ASSOC);
            if (isset($_POST["balance_submit"])) {
                while ($row = mysqli_fetch_array($balance_query2, MYSQLI_ASSOC)) {
                    $username = filter_input(INPUT_POST,"username",FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                    if (in_array($username,$row)) {
                        $bval = filter_input(INPUT_POST,"bval",FILTER_SANITIZE_NUMBER_FLOAT);
                        $bval = floatval($bval);
                        mysqli_query($con,"UPDATE balance SET balance = $bval WHERE username = '$username'");
                        header("Refresh:0");
                    }
                }
            }
        }
    }
    if (isset($_SESSION["verif_deposit"])) { 
        $verif_deposit = $_SESSION["verif_deposit"];
        if($verif_deposit === true) { 
        
        }
    }
}
?>
<?php if(isset($verif_balance) && $verif_balance === true): ?>
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
                    <a href="welcome_admin.php"><input type="submit" name="returnbut" id="but" class="but text-center" value="Back to the admin page" onclick="location.reload()"></a>
                </div>
            </form>
        </div>
        <script src="./bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_deposit) && $verif_deposit === true): ?>
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
                        <th>Username</th>
                        <th>Deposit_Date</th>
                        <th>Deposit_Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <div class="mt-2">P.S. : If you just want to check records omit these inputs</div>
                <div class="d-flex gap-4">
                    <label class="labbor lab">
                    <img src="./data/calender.svg" alt="">
                    <input type="text" placeholder="Filter by date" required>
                    </label>
                    <label class="labbor lab">
                    <img src="./data/dollar.svg" alt="">
                    <input type="text" placeholder="Filter by amount" required>
                    </label>
                </div>
                <div class="d-flex gap-4 mt-2 mb-2">
                    <input type="submit" class="but text-center" id="but" value="Filter">
                    <input value="Open filtering manual" class="but text-center" type="button" data-bs-toggle="offcanvas" data-bs-target="#manual">
                    <a href="welcome_admin.php"><input type="button" id="but" class="but text-center" value="Back to the admin page" onclick="location.reload()"></a>
                </div>
                <div class="offcanvas offcanvas-start" id="manual">
                <div class="offcanvas-header">
                    <h1 class="offcanvas-title">Filtering Manual</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                </div>
                <div class="offcanvas-body">
                    <div class="mb-3 h3">Date Filtering :</div>
                    <div class="mt-3 mb-3">P.S. : At least one of the year , month or day needs to be an exact known value</div>
                    <div class="mt-3 mb-3">Date's Format is YYYY-MM-DD : use % operator for more general filterting</div>
                    <div class="mt-3 mb-3">It is Mandatory! to keep the format as it is even with the "-"</div>
                    <div class="mt-3">Examples :</div>
                    <ul>
                        <li class="mt-1">" 2023-%-% " (records during 2023 at any day or month)</li>
                        <li class="mt-1">" 2023-01-% " (records during January 2023 at any day)</li>
                        <li class="mt-1">" %-06-01 " (records during the 1st of June at any year)</li>
                        <li class="mt-1">" %-%-01 " (records during the 1st of any month at any year)</li>
                        <li class="mt-1">etc ...</li>
                    </ul>
                    <div class="mb-3 mt-5 h3">Amount Filtering :</div>
                    <div class="mt-3 mb-3">The format is " keywordnumber "</div>
                    <div class="mt-3 mb-3">Examples : sg90.8 / e120 / etc ...</div>
                    <div class="mt-3 mb-2">Keywords : sg / g / sl / l / e</div>
                    <div class="mt-3">Explication :</div>
                    <ul>
                        <li class="mt-1">sg : strictly greater than </li>
                        <li class="mt-1">g : greater than</li>
                        <li class="mt-1">sl : strictly less than</li>
                        <li class="mt-1">l : less than</li>
                        <li class="mt-1">e : equals to</li>
                    </ul>
                    <div class="mt-3 mb-2">P.S. : Keywords are not case-sensitive</div>
                </div>
                </div>
            </form>
        </div>
        <script src="./bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_balance) && $verif_balance === false || isset($verif_deposit) &&  $verif_deposit === false): ?>
    <!DOCTYPE html>
    <html lang="en">
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
    </html>
<?php endif; ?>