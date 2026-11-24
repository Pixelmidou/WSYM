<?php
require ("linking.php");
if ($con->connect_error) {
    die("Connection Failed" . $con->connect_error);
} else { 
    session_start();
    if (!isset($_SESSION['verif_balance']) && !isset($_SESSION['verif_deposit']) && !isset($_SESSION['verif_withdraw']) && !isset($_SESSION['verif_wire']) && !isset($_SESSION['verif_dep_action']) && !isset($_SESSION['verif_with_action']) && !isset($_SESSION['verif_wire_action']) && !isset($_SESSION['verif_ticket_action']) && !isset($_SESSION['verif_acc_action'])) {
        if (empty($_SESSION['verif_balance']) || $_SESSION['verif_balance'] === "" && empty($_SESSION['verif_deposit']) || $_SESSION['verif_deposit'] === "" && empty($_SESSION['verif_withdraw']) || $_SESSION['verif_withdraw'] === ""  && empty($_SESSION['verif_wire']) || $_SESSION['verif_wire'] === "" && empty($_SESSION['verif_dep_action']) || $_SESSION['verif_dep_action'] === "" && empty($_SESSION['verif_with_action']) || $_SESSION['verif_with_action'] === "" && empty($_SESSION['verif_wire_action']) || $_SESSION['verif_wire_action'] === "" && empty($_SESSION['verif_ticket_action']) || $_SESSION['verif_ticket_action'] === "" && empty($_SESSION['verif_acc_action']) || $_SESSION['verif_acc_action'] === "") {
            if (isset($_SESSION['admin_username'])) {
                header("Location: admin_redirections.php");
                exit;
            } else {
                session_destroy();
                header("Location: index.php");
                exit;
            }
        } 
    }
    if (isset($_SESSION["verif_balance"])) {
        $verif_balance = $_SESSION["verif_balance"];
        if($verif_balance === "success") {
            $verif_balance_case = $_SESSION['verif_balance_case'];
            switch ($verif_balance_case) {
                case "1":
                    $balanceemail = $_SESSION["balanceemail"];
                    $start = 0;
                    $rowsperpage = 6;
                    $recs = $con -> query("SELECT * FROM balance WHERE email LIKE '%$balanceemail%'");
                    $nbrows = $recs -> num_rows;
                    $nbpages = ceil($nbrows / $rowsperpage);
                    if (isset($_GET["pagenr"])) {
                        $start = ($_GET["pagenr"] - 1)  * $rowsperpage;
                    }
                    $balance_query1 = $con -> query("SELECT * FROM balance WHERE email LIKE '%$balanceemail%' LIMIT $start , $rowsperpage");
                    $balance_query2 = $con -> query("SELECT * FROM balance WHERE email LIKE '%$balanceemail%' LIMIT $start , $rowsperpage");
                    break;
                case "2":
                    $balanceusername = $_SESSION["balanceusername"];
                    $start = 0;
                    $rowsperpage = 6;
                    $recs = $con -> query("SELECT * FROM balance WHERE username LIKE '%$balanceusername%'");
                    $nbrows = $recs -> num_rows;
                    $nbpages = ceil($nbrows / $rowsperpage);
                    if (isset($_GET["pagenr"])) {
                        $start = ($_GET["pagenr"] - 1)  * $rowsperpage;
                    }
                    $balance_query1 = $con -> query("SELECT * FROM balance WHERE username LIKE '%$balanceusername%' LIMIT $start , $rowsperpage");
                    $balance_query2 = $con -> query("SELECT * FROM balance WHERE username LIKE '%$balanceusername%' LIMIT $start , $rowsperpage");
                    break;
                case "3":
                    $balanceemail = $_SESSION["balanceemail"];
                    $balanceusername = $_SESSION["balanceusername"];
                    $start = 0;
                    $rowsperpage = 6;
                    $recs = $con -> query("SELECT * FROM balance WHERE username LIKE '%$balanceusername%' AND email LIKE '%$balanceemail%'");
                    $nbrows = $recs -> num_rows;
                    $nbpages = ceil($nbrows / $rowsperpage);
                    if (isset($_GET["pagenr"])) {
                        $start = ($_GET["pagenr"] - 1)  * $rowsperpage;
                    }
                    $balance_query1 = $con -> query("SELECT * FROM balance WHERE username LIKE '%$balanceusername%' AND email LIKE '%$balanceemail%' LIMIT $start , $rowsperpage");
                    $balance_query2 = $con -> query("SELECT * FROM balance WHERE username LIKE '%$balanceusername%' AND email LIKE '%$balanceemail%' LIMIT $start , $rowsperpage");
                    break;
            }
            $balance_query_array_all = $balance_query1 -> fetch_all(MYSQLI_ASSOC);
            if (isset($_POST["balance_submit"])) {
                while ($row = $balance_query2 -> fetch_array(MYSQLI_ASSOC)) {
                    $username = filter_input(INPUT_POST,"username",FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                    if (in_array($username,$row)) {
                        $bval = filter_input(INPUT_POST,"bval",FILTER_SANITIZE_NUMBER_FLOAT);
                        $bval = floatval($bval);
                        $con -> query("UPDATE balance SET balance = $bval WHERE username = '$username'");
                        header("Refresh:0");
                    }
                }
            }
        }
        if (isset($_POST['balback'])) {
            header("Location: welcome_admin.php");
            unset($_SESSION['verif_balance']);
            exit;
        }
        $_SESSION["balsub"] = "balsub";
    }
    if (isset($_SESSION["verif_deposit"])) { 
        $verif_deposit = $_SESSION["verif_deposit"];
        if($verif_deposit === "success") { 
            $verif_deposit_case = $_SESSION['verif_deposit_case'];
            $start = 0;
            $rowsperpage = 6;
            $recs = $con -> query("SELECT * FROM deposit");
            $nbrows = $recs -> num_rows;
            $nbpages = ceil($nbrows / $rowsperpage);
            if (isset($_GET["pagenr"])) {
                $start = ($_GET["pagenr"] - 1)  * $rowsperpage;
            }
            switch ($verif_deposit_case) {
                case "1":
                    $transusername = $_SESSION["transusername"];

                    $deposit_query1 = $con -> query("SELECT username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit WHERE username LIKE '%$transusername%' LIMIT $start , $rowsperpage");
                    $deposit_query2 = $con -> query("SELECT username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit WHERE username LIKE '%$transusername%' LIMIT $start , $rowsperpage");
                    break;
                case "2":
                    $transemail = $_SESSION["transemail"];

                    $deposit_query1 = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') LIMIT $start , $rowsperpage");
                    $deposit_query2 = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') LIMIT $start , $rowsperpage");
                    break;
                case "3":
                    $transemail = $_SESSION["transemail"];
                    $transusername = $_SESSION["transusername"];

                    $deposit_query1 = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit.username LIKE '%$transusername%' LIMIT $start , $rowsperpage");
                    $deposit_query2 = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit.username LIKE '%$transusername%' LIMIT $start , $rowsperpage");
                    break;
            }
            $deposit_query_array_all = $deposit_query1 -> fetch_all(MYSQLI_ASSOC);
            $depficase = "initial";
            if (isset($_POST["deposit_submit"])) {
                $depfidate = filter_input(INPUT_POST, 'depfidate', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $depfihour = filter_input(INPUT_POST, 'depfihour', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $depfiamount = filter_input(INPUT_POST, 'depfiamount', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $depficase = "filter";
                if (!empty($depfidate) && empty($depfihour) && empty($depfiamount)) {
                    switch ($verif_deposit_case) { 
                        case "1":
                            $depfi_query = $con -> query("SELECT username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit WHERE username LIKE '%$transusername%' HAVING LEFT(deposit_date, 10) LIKE '$depfidate' LIMIT $start , $rowsperpage");
                            break;
                        case "2":
                            $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') HAVING LEFT(deposit_date, 10) LIKE '$depfidate' LIMIT $start , $rowsperpage");
                            break;
                        case "3":
                            $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit.username LIKE '%$transusername%' HAVING LEFT(deposit_date, 10) LIKE '$depfidate' LIMIT $start , $rowsperpage");
                            break;
                    }
                } else if (!empty($depfidate) && !empty($depfiamount) && empty($depfihour)) {
                    $i = 0;
                    $values = ["SG","G","SL","SL","E"];
                    $depfiamount_action = "";
                    while (in_array(strtoupper(substr($depfiamount,0,strpos($depfiamount," "))),$values) && $i < 2) {
                        $depfiamount_action .= trim(strtoupper($depfiamount[$i]));
                        $i += 1;
                    }
                    $depfiamount_value = floatval(substr($depfiamount,strpos($depfiamount," ") + 1));
                    switch ($depfiamount_action) {
                        case "SG":
                            switch ($verif_deposit_case) { 
                                case "1":
                                    $depfi_query = $con -> query("SELECT username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit WHERE username LIKE '%$transusername%' AND deposit_amount > $depfiamount_value HAVING LEFT(deposit_date, 10) LIKE '$depfidate' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit_amount > $depfiamount_value HAVING LEFT(deposit_date, 10) LIKE '$depfidate'  LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit.username LIKE '%$transusername%' AND deposit_amount > $depfiamount_value HAVING LEFT(deposit_date, 10) LIKE '$depfidate' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "G":
                            switch ($verif_deposit_case) { 
                                case "1":
                                    $depfi_query = $con -> query("SELECT username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit WHERE username LIKE '%$transusername%' AND deposit_amount >= $depfiamount_value HAVING LEFT(deposit_date, 10) LIKE '$depfidate' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit_amount >= $depfiamount_value HAVING LEFT(deposit_date, 10) LIKE '$depfidate' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit.username LIKE '%$transusername%' AND deposit_amount >= $depfiamount_value HAVING LEFT(deposit_date, 10) LIKE '$depfidate' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "SL":
                            switch ($verif_deposit_case) { 
                                case "1":
                                    $depfi_query = $con -> query("SELECT username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit WHERE username LIKE '%$transusername%' AND deposit_amount < $depfiamount_value HAVING LEFT(deposit_date, 10) LIKE '$depfidate' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit_amount < $depfiamount_value HAVING LEFT(deposit_date, 10) LIKE '$depfidate' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit.username LIKE '%$transusername%' AND deposit_amount < $depfiamount_value HAVING LEFT(deposit_date, 10) LIKE '$depfidate' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "L":
                            switch ($verif_deposit_case) { 
                                case "1":
                                    $depfi_query = $con -> query("SELECT username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit WHERE username LIKE '%$transusername%' AND deposit_amount <= $depfiamount_value  HAVING LEFT(deposit_date, 10) LIKE '$depfidate' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit_amount <= $depfiamount_value  HAVING LEFT(deposit_date, 10) LIKE '$depfidate' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit.username LIKE '%$transusername%' AND deposit_amount <= $depfiamount_value  HAVING LEFT(deposit_date, 10) LIKE '$depfidate' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "E":
                            switch ($verif_deposit_case) { 
                                case "1":
                                    $depfi_query = $con -> query("SELECT username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit WHERE username LIKE '%$transusername%' AND deposit_amount = $depfiamount_value HAVING LEFT(deposit_date, 10) LIKE '$depfidate' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit_amount = $depfiamount_value HAVING LEFT(deposit_date, 10) LIKE '$depfidate' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit.username LIKE '%$transusername%' AND deposit_amount = $depfiamount_value HAVING LEFT(deposit_date, 10) LIKE '$depfidate' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                    }
                } else if (!empty($depfidate) && empty($depfiamount) && !empty($depfihour)) {
                    switch ($verif_deposit_case) { 
                        case "1":
                            $depfi_query = $con -> query("SELECT username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit WHERE username LIKE '%$transusername%' HAVING LEFT(deposit_date, 10) LIKE '$depfidate' AND RIGHT(deposit_date, 8) LIKE '$depfihour' LIMIT $start , $rowsperpage");
                            break;
                        case "2":
                            $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') HAVING LEFT(deposit_date, 10) LIKE '$depfidate' AND RIGHT(deposit_date, 8) LIKE '$depfihour' LIMIT $start , $rowsperpage");
                            break;
                        case "3":
                            $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit.username LIKE '%$transusername%' HAVING LEFT(deposit_date, 10) LIKE '$depfidate' AND RIGHT(deposit_date, 8) LIKE '$depfihour' LIMIT $start , $rowsperpage");
                            break;
                    }
                } else if (empty($depfidate) && !empty($depfiamount) && empty($depfihour)) {
                    $i = 0;
                    $values = ["SG","G","SL","SL","E"];
                    $depfiamount_action = "";
                    while (in_array(strtoupper(substr($depfiamount,0,strpos($depfiamount," "))),$values) && $i < 2) {
                        $depfiamount_action .= trim(strtoupper($depfiamount[$i]));
                        $i += 1;
                    }
                    $depfiamount_value = floatval(substr($depfiamount,strpos($depfiamount," ") + 1));
                    switch ($depfiamount_action) {
                        case "SG":
                            switch ($verif_deposit_case) { 
                                case "1":
                                    $depfi_query = $con -> query("SELECT username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit WHERE username LIKE '%$transusername%' AND deposit_amount > $depfiamount_value LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit_amount > $depfiamount_value LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit.username LIKE '%$transusername%' AND deposit_amount > $depfiamount_value LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "G":
                            switch ($verif_deposit_case) { 
                                case "1":
                                    $depfi_query = $con -> query("SELECT username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit WHERE username LIKE '%$transusername%' AND deposit_amount >= $depfiamount_value LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit_amount >= $depfiamount_value LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit.username LIKE '%$transusername%' AND deposit_amount >= $depfiamount_value LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "SL":
                            switch ($verif_deposit_case) { 
                                case "1":
                                    $depfi_query = $con -> query("SELECT username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit WHERE username LIKE '%$transusername%' AND deposit_amount < $depfiamount_value LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit_amount < $depfiamount_value LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit.username LIKE '%$transusername%' AND deposit_amount < $depfiamount_value LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "L":
                            switch ($verif_deposit_case) { 
                                case "1":
                                    $depfi_query = $con -> query("SELECT username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit WHERE username LIKE '%$transusername%' AND deposit_amount <= $depfiamount_value LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit_amount <= $depfiamount_value LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit.username LIKE '%$transusername%' AND deposit_amount <= $depfiamount_value LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "E":
                            switch ($verif_deposit_case) { 
                                case "1":
                                    $depfi_query = $con -> query("SELECT username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit WHERE username LIKE '%$transusername%' AND deposit_amount = $depfiamount_value LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit_amount = $depfiamount_value LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit.username LIKE '%$transusername%' AND deposit_amount = $depfiamount_value LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                    }
                } else if (empty($depfidate) && !empty($depfiamount) && !empty($depfihour)) {
                    $i = 0;
                    $values = ["SG","G","SL","SL","E"];
                    $depfiamount_action = "";
                    while (in_array(strtoupper(substr($depfiamount,0,strpos($depfiamount," "))),$values) && $i < 2) {
                        $depfiamount_action .= trim(strtoupper($depfiamount[$i]));
                        $i += 1;
                    }
                    $depfiamount_value = floatval(substr($depfiamount,strpos($depfiamount," ") + 1));
                    switch ($depfiamount_action) {
                        case "SG":
                            switch ($verif_deposit_case) { 
                                case "1":
                                    $depfi_query = $con -> query("SELECT username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit WHERE username LIKE '%$transusername%' AND deposit_amount > $depfiamount_value HAVING RIGHT(deposit_date, 8) LIKE '$depfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit_amount > $depfiamount_value HAVING RIGHT(deposit_date, 8) LIKE '$depfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit.username LIKE '%$transusername%' AND deposit_amount > $depfiamount_value HAVING RIGHT(deposit_date, 8) LIKE '$depfihour' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "G":
                            switch ($verif_deposit_case) { 
                                case "1":
                                    $depfi_query = $con -> query("SELECT username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit WHERE username LIKE '%$transusername%' AND deposit_amount >= $depfiamount_value HAVING RIGHT(deposit_date, 8) LIKE '$depfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit_amount >= $depfiamount_value HAVING RIGHT(deposit_date, 8) LIKE '$depfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit.username LIKE '%$transusername%' AND deposit_amount >= $depfiamount_value HAVING RIGHT(deposit_date, 8) LIKE '$depfihour' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "SL":
                            switch ($verif_deposit_case) { 
                                case "1":
                                    $depfi_query = $con -> query("SELECT username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit WHERE username LIKE '%$transusername%' AND deposit_amount < $depfiamount_value HAVING RIGHT(deposit_date, 8) LIKE '$depfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit_amount < $depfiamount_value HAVING RIGHT(deposit_date, 8) LIKE '$depfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit.username LIKE '%$transusername%' AND deposit_amount < $depfiamount_value HAVING RIGHT(deposit_date, 8) LIKE '$depfihour' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "L":
                            switch ($verif_deposit_case) { 
                                case "1":
                                    $depfi_query = $con -> query("SELECT username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit WHERE username LIKE '%$transusername%' AND deposit_amount <= $depfiamount_value HAVING RIGHT(deposit_date, 8) LIKE '$depfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit_amount <= $depfiamount_value HAVING RIGHT(deposit_date, 8) LIKE '$depfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit.username LIKE '%$transusername%' AND deposit_amount <= $depfiamount_value HAVING RIGHT(deposit_date, 8) LIKE '$depfihour' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "E":
                            switch ($verif_deposit_case) { 
                                case "1":
                                    $depfi_query = $con -> query("SELECT username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit WHERE username LIKE '%$transusername%' AND deposit_amount = $depfiamount_value HAVING RIGHT(deposit_date, 8) LIKE '$depfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit_amount = $depfiamount_value HAVING RIGHT(deposit_date, 8) LIKE '$depfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit.username LIKE '%$transusername%' AND deposit_amount = $depfiamount_value HAVING RIGHT(deposit_date, 8) LIKE '$depfihour' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                    }
                } else if (empty($depfidate) && empty($depfiamount) && !empty($depfihour)) {
                    switch ($verif_deposit_case) { 
                        case "1":
                            $depfi_query = $con -> query("SELECT username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit WRIGHT(deposit_date, 8) LIKE '$depfihour' LIMIT $start , $rowsperpage");
                            break;
                        case "2":
                            $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') HAVING RIGHT(deposit_date, 8) LIKE '$depfihour' LIMIT $start , $rowsperpage");
                            break;
                        case "3":
                            $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit.username LIKE '%$transusername%' HAVING RIGHT(deposit_date, 8) LIKE '$depfihour' LIMIT $start , $rowsperpage");
                            break;
                    }
                } else if (!empty($depfidate) && !empty($depfiamount) && !empty($depfihour)) {
                    $i = 0;
                    $values = ["SG","G","SL","SL","E"];
                    $depfiamount_action = "";
                    while (in_array(strtoupper(substr($depfiamount,0,strpos($depfiamount," "))),$values) && $i < 2) {
                        $depfiamount_action .= trim(strtoupper($depfiamount[$i]));
                        $i += 1;
                    }
                    $depfiamount_value = floatval(substr($depfiamount,strpos($depfiamount," ") + 1));
                    switch ($depfiamount_action) {
                        case "SG":
                            switch ($verif_deposit_case) { 
                                case "1":
                                    $depfi_query = $con -> query("SELECT username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit WHERE username LIKE '%$transusername%' AND deposit_amount > $depfiamount_value HAVING LEFT(deposit_date, 10) LIKE '$depfidate' AND RIGHT(deposit_date, 8) LIKE '$depfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit_amount > $depfiamount_value HAVING LEFT(deposit_date, 10) LIKE '$depfidate' AND RIGHT(deposit_date, 8) LIKE '$depfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit.username LIKE '%$transusername%' AND deposit_amount > $depfiamount_value HAVING LEFT(deposit_date, 10) LIKE '$depfidate' AND RIGHT(deposit_date, 8) LIKE '$depfihour' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "G":
                            switch ($verif_deposit_case) { 
                                case "1":
                                    $depfi_query = $con -> query("SELECT username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit WHERE username LIKE '%$transusername%' AND deposit_amount >= $depfiamount_value HAVING LEFT(deposit_date, 10) LIKE '$depfidate' AND RIGHT(deposit_date, 8) LIKE '$depfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit_amount >= $depfiamount_value HAVING LEFT(deposit_date, 10) LIKE '$depfidate' AND RIGHT(deposit_date, 8) LIKE '$depfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit.username LIKE '%$transusername%' AND deposit_amount >= $depfiamount_value HAVING LEFT(deposit_date, 10) LIKE '$depfidate' AND RIGHT(deposit_date, 8) LIKE '$depfihour' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "SL":
                            switch ($verif_deposit_case) { 
                                case "1":
                                    $depfi_query = $con -> query("SELECT username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit WHERE username LIKE '%$transusername%' AND deposit_amount < $depfiamount_value HAVING LEFT(deposit_date, 10) LIKE '$depfidate' AND RIGHT(deposit_date, 8) LIKE '$depfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit_amount < $depfiamount_value HAVING LEFT(deposit_date, 10) LIKE '$depfidate' AND RIGHT(deposit_date, 8) LIKE '$depfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit.username LIKE '%$transusername%' AND deposit_amount < $depfiamount_value HAVING LEFT(deposit_date, 10) LIKE '$depfidate' AND RIGHT(deposit_date, 8) LIKE '$depfihour' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "L":
                            switch ($verif_deposit_case) { 
                                case "1":
                                    $depfi_query = $con -> query("SELECT username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit WHERE username LIKE '%$transusername%' AND deposit_amount <= $depfiamount_value  HAVING LEFT(deposit_date, 10) LIKE '$depfidate' AND RIGHT(deposit_date, 8) LIKE '$depfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit_amount <= $depfiamount_value  HAVING LEFT(deposit_date, 10) LIKE '$depfidate' AND RIGHT(deposit_date, 8) LIKE '$depfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit.username LIKE '%$transusername%' AND deposit_amount <= $depfiamount_value  HAVING LEFT(deposit_date, 10) LIKE '$depfidate' AND RIGHT(deposit_date, 8) LIKE '$depfihour' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "E":
                            switch ($verif_deposit_case) { 
                                case "1":
                                    $depfi_query = $con -> query("SELECT username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit WHERE username LIKE '%$transusername%' AND deposit_amount = $depfiamount_value HAVING LEFT(deposit_date, 10) LIKE '$depfidate' AND RIGHT(deposit_date, 8) LIKE '$depfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit_amount = $depfiamount_value HAVING LEFT(deposit_date, 10) LIKE '$depfidate' AND RIGHT(deposit_date, 8) LIKE '$depfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $depfi_query = $con -> query("SELECT deposit.username,LEFT(deposit_date, 10),RIGHT(deposit_date, 8),deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit.username LIKE '%$transusername%' AND deposit_amount = $depfiamount_value HAVING LEFT(deposit_date, 10) LIKE '$depfidate' AND RIGHT(deposit_date, 8) LIKE '$depfihour' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                    }
                }
                $depfi_query_array_all = $depfi_query -> fetch_all(MYSQLI_ASSOC);
            }
        }
        if (isset($_POST['reset_deposit_submit'])) {
            $depficase = "initial";
        }
        if (isset($_POST['depback'])) {
            header("Location: welcome_admin.php");
            unset($_SESSION['verif_deposit']);
            exit;
        }
        $_SESSION["transdep"] = "transdep";
    }
    if (isset($_SESSION["verif_withdraw"])) { 
        $verif_withdraw = $_SESSION["verif_withdraw"];
        if($verif_withdraw === "success") { 
            $start = 0;
            $rowsperpage = 6;
            $recs = $con -> query("SELECT * FROM withdraw");
            $nbrows = $recs -> num_rows;
            $nbpages = ceil($nbrows / $rowsperpage);
            if (isset($_GET["pagenr"])) {
                $start = ($_GET["pagenr"] - 1)  * $rowsperpage;
            }
            $verif_withdraw_case = $_SESSION['verif_withdraw_case'];
            switch ($verif_withdraw_case) {
                case "1":
                    $transusername = $_SESSION["transusername"];

                    $withdraw_query1 = $con -> query("SELECT username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw WHERE username LIKE '%$transusername%' LIMIT $start , $rowsperpage");
                    $withdraw_query2 = $con -> query("SELECT username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw WHERE username LIKE '%$transusername%' LIMIT $start , $rowsperpage");
                    break;
                case "2":
                    $transemail = $_SESSION["transemail"];

                    $withdraw_query1 = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') LIMIT $start , $rowsperpage");
                    $withdraw_query2 = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') LIMIT $start , $rowsperpage");
                    break;
                case "3":
                    $transemail = $_SESSION["transemail"];
                    $transusername = $_SESSION["transusername"];

                    $withdraw_query1 = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw.username LIKE '%$transusername%' LIMIT $start , $rowsperpage");
                    $withdraw_query2 = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw.username LIKE '%$transusername%' LIMIT $start , $rowsperpage");
                    break;
            }
            $withdraw_query_array_all = $withdraw_query1 -> fetch_all(MYSQLI_ASSOC);
            $withficase = "initial";
            if (isset($_POST["withdraw_submit"])) {
                $withfidate = filter_input(INPUT_POST, 'withfidate', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $withfihour = filter_input(INPUT_POST, 'withfihour', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $withfiamount = filter_input(INPUT_POST, 'withfiamount', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $withficase = "filter";
                if (!empty($withfidate) && empty($withfihour) && empty($withfiamount)) {
                    switch ($verif_withdraw_case) { 
                        case "1":
                            $withfi_query = $con -> query("SELECT username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw WHERE username LIKE '%$transusername%' HAVING LEFT(withdraw_date, 10) LIKE '$withfidate' LIMIT $start , $rowsperpage");
                            break;
                        case "2":
                            $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') HAVING LEFT(withdraw_date, 10) LIKE '$withfidate' LIMIT $start , $rowsperpage");
                            break;
                        case "3":
                            $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw.username LIKE '%$transusername%' HAVING LEFT(withdraw_date, 10) LIKE '$withfidate' LIMIT $start , $rowsperpage");
                            break;
                    }
                } else if (!empty($withfidate) && !empty($withfiamount) && empty($withfihour)) {
                    $i = 0;
                    $values = ["SG","G","SL","SL","E"];
                    $withfiamount_action = "";
                    while (in_array(strtoupper(substr($withfiamount,0,strpos($withfiamount," "))),$values) && $i < 2) {
                        $withfiamount_action .= trim(strtoupper($withfiamount[$i]));
                        $i += 1;
                    }
                    $withfiamount_value = floatval(substr($withfiamount,strpos($withfiamount," ") + 1));
                    switch ($withfiamount_action) {
                        case "SG":
                            switch ($verif_withdraw_case) { 
                                case "1":
                                    $withfi_query = $con -> query("SELECT username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw WHERE username LIKE '%$transusername%' AND withdraw_amount > $withfiamount_value HAVING LEFT(withdraw_date, 10) LIKE '$withfidate' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw_amount > $withfiamount_value HAVING LEFT(withdraw_date, 10) LIKE '$withfidate'  LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw.username LIKE '%$transusername%' AND withdraw_amount > $withfiamount_value HAVING LEFT(withdraw_date, 10) LIKE '$withfidate' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "G":
                            switch ($verif_withdraw_case) { 
                                case "1":
                                    $withfi_query = $con -> query("SELECT username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw WHERE username LIKE '%$transusername%' AND withdraw_amount >= $withfiamount_value HAVING LEFT(withdraw_date, 10) LIKE '$withfidate' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw_amount >= $withfiamount_value HAVING LEFT(withdraw_date, 10) LIKE '$withfidate' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw.username LIKE '%$transusername%' AND withdraw_amount >= $withfiamount_value HAVING LEFT(withdraw_date, 10) LIKE '$withfidate' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "SL":
                            switch ($verif_withdraw_case) { 
                                case "1":
                                    $withfi_query = $con -> query("SELECT username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw WHERE username LIKE '%$transusername%' AND withdraw_amount < $withfiamount_value HAVING LEFT(withdraw_date, 10) LIKE '$withfidate' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw_amount < $withfiamount_value HAVING LEFT(withdraw_date, 10) LIKE '$withfidate' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw.username LIKE '%$transusername%' AND withdraw_amount < $withfiamount_value HAVING LEFT(withdraw_date, 10) LIKE '$withfidate' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "L":
                            switch ($verif_withdraw_case) { 
                                case "1":
                                    $withfi_query = $con -> query("SELECT username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw WHERE username LIKE '%$transusername%' AND withdraw_amount <= $withfiamount_value  HAVING LEFT(withdraw_date, 10) LIKE '$withfidate' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw_amount <= $withfiamount_value  HAVING LEFT(withdraw_date, 10) LIKE '$withfidate' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw.username LIKE '%$transusername%' AND withdraw_amount <= $withfiamount_value  HAVING LEFT(withdraw_date, 10) LIKE '$withfidate' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "E":
                            switch ($verif_withdraw_case) { 
                                case "1":
                                    $withfi_query = $con -> query("SELECT username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw WHERE username LIKE '%$transusername%' AND withdraw_amount = $withfiamount_value HAVING LEFT(withdraw_date, 10) LIKE '$withfidate' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw_amount = $withfiamount_value HAVING LEFT(withdraw_date, 10) LIKE '$withfidate' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw.username LIKE '%$transusername%' AND withdraw_amount = $withfiamount_value HAVING LEFT(withdraw_date, 10) LIKE '$withfidate' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                    }
                } else if (!empty($withfidate) && empty($withfiamount) && !empty($withfihour)) {
                    switch ($verif_withdraw_case) { 
                        case "1":
                            $withfi_query = $con -> query("SELECT username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw WHERE username LIKE '%$transusername%' HAVING LEFT(withdraw_date, 10) LIKE '$withfidate' AND RIGHT(withdraw_date, 8) LIKE '$withfihour' LIMIT $start , $rowsperpage");
                            break;
                        case "2":
                            $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') HAVING LEFT(withdraw_date, 10) LIKE '$withfidate' AND RIGHT(withdraw_date, 8) LIKE '$withfihour' LIMIT $start , $rowsperpage");
                            break;
                        case "3":
                            $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw.username LIKE '%$transusername%' HAVING LEFT(withdraw_date, 10) LIKE '$withfidate' AND RIGHT(withdraw_date, 8) LIKE '$withfihour' LIMIT $start , $rowsperpage");
                            break;
                    }
                } else if (empty($withfidate) && !empty($withfiamount) && empty($withfihour)) {
                    $i = 0;
                    $values = ["SG","G","SL","SL","E"];
                    $withfiamount_action = "";
                    while (in_array(strtoupper(substr($withfiamount,0,strpos($withfiamount," "))),$values) && $i < 2) {
                        $withfiamount_action .= trim(strtoupper($withfiamount[$i]));
                        $i += 1;
                    }
                    $withfiamount_value = floatval(substr($withfiamount,strpos($withfiamount," ") + 1));
                    switch ($withfiamount_action) {
                        case "SG":
                            switch ($verif_withdraw_case) { 
                                case "1":
                                    $withfi_query = $con -> query("SELECT username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw WHERE username LIKE '%$transusername%' AND withdraw_amount > $withfiamount_value LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw_amount > $withfiamount_value LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw.username LIKE '%$transusername%' AND withdraw_amount > $withfiamount_value LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "G":
                            switch ($verif_withdraw_case) { 
                                case "1":
                                    $withfi_query = $con -> query("SELECT username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw WHERE username LIKE '%$transusername%' AND withdraw_amount >= $withfiamount_value LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw_amount >= $withfiamount_value LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw.username LIKE '%$transusername%' AND withdraw_amount >= $withfiamount_value LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "SL":
                            switch ($verif_withdraw_case) { 
                                case "1":
                                    $withfi_query = $con -> query("SELECT username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw WHERE username LIKE '%$transusername%' AND withdraw_amount < $withfiamount_value LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw_amount < $withfiamount_value LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw.username LIKE '%$transusername%' AND withdraw_amount < $withfiamount_value LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "L":
                            switch ($verif_withdraw_case) { 
                                case "1":
                                    $withfi_query = $con -> query("SELECT username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw WHERE username LIKE '%$transusername%' AND withdraw_amount <= $withfiamount_value LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw_amount <= $withfiamount_value LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw.username LIKE '%$transusername%' AND withdraw_amount <= $withfiamount_value LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "E":
                            switch ($verif_withdraw_case) { 
                                case "1":
                                    $withfi_query = $con -> query("SELECT username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw WHERE username LIKE '%$transusername%' AND withdraw_amount = $withfiamount_value LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw_amount = $withfiamount_value LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw.username LIKE '%$transusername%' AND withdraw_amount = $withfiamount_value LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                    }
                } else if (empty($withfidate) && !empty($withfiamount) && !empty($withfihour)) {
                    $i = 0;
                    $values = ["SG","G","SL","SL","E"];
                    $withfiamount_action = "";
                    while (in_array(strtoupper(substr($withfiamount,0,strpos($withfiamount," "))),$values) && $i < 2) {
                        $withfiamount_action .= trim(strtoupper($withfiamount[$i]));
                        $i += 1;
                    }
                    $withfiamount_value = floatval(substr($withfiamount,strpos($withfiamount," ") + 1));
                    switch ($withfiamount_action) {
                        case "SG":
                            switch ($verif_withdraw_case) { 
                                case "1":
                                    $withfi_query = $con -> query("SELECT username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw WHERE username LIKE '%$transusername%' AND withdraw_amount > $withfiamount_value HAVING RIGHT(withdraw_date, 8) LIKE '$withfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw_amount > $withfiamount_value HAVING RIGHT(withdraw_date, 8) LIKE '$withfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw.username LIKE '%$transusername%' AND withdraw_amount > $withfiamount_value HAVING RIGHT(withdraw_date, 8) LIKE '$withfihour' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "G":
                            switch ($verif_withdraw_case) { 
                                case "1":
                                    $withfi_query = $con -> query("SELECT username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw WHERE username LIKE '%$transusername%' AND withdraw_amount >= $withfiamount_value HAVING RIGHT(withdraw_date, 8) LIKE '$withfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw_amount >= $withfiamount_value HAVING RIGHT(withdraw_date, 8) LIKE '$withfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw.username LIKE '%$transusername%' AND withdraw_amount >= $withfiamount_value HAVING RIGHT(withdraw_date, 8) LIKE '$withfihour' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "SL":
                            switch ($verif_withdraw_case) { 
                                case "1":
                                    $withfi_query = $con -> query("SELECT username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw WHERE username LIKE '%$transusername%' AND withdraw_amount < $withfiamount_value HAVING RIGHT(withdraw_date, 8) LIKE '$withfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw_amount < $withfiamount_value HAVING RIGHT(withdraw_date, 8) LIKE '$withfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw.username LIKE '%$transusername%' AND withdraw_amount < $withfiamount_value HAVING RIGHT(withdraw_date, 8) LIKE '$withfihour' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "L":
                            switch ($verif_withdraw_case) { 
                                case "1":
                                    $withfi_query = $con -> query("SELECT username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw WHERE username LIKE '%$transusername%' AND withdraw_amount <= $withfiamount_value HAVING RIGHT(withdraw_date, 8) LIKE '$withfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw_amount <= $withfiamount_value HAVING RIGHT(withdraw_date, 8) LIKE '$withfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw.username LIKE '%$transusername%' AND withdraw_amount <= $withfiamount_value HAVING RIGHT(withdraw_date, 8) LIKE '$withfihour' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "E":
                            switch ($verif_withdraw_case) { 
                                case "1":
                                    $withfi_query = $con -> query("SELECT username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw WHERE username LIKE '%$transusername%' AND withdraw_amount = $withfiamount_value HAVING RIGHT(withdraw_date, 8) LIKE '$withfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw_amount = $withfiamount_value HAVING RIGHT(withdraw_date, 8) LIKE '$withfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw.username LIKE '%$transusername%' AND withdraw_amount = $withfiamount_value HAVING RIGHT(withdraw_date, 8) LIKE '$withfihour' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                    }
                } else if (empty($withfidate) && empty($withfiamount) && !empty($withfihour)) {
                    switch ($verif_withdraw_case) { 
                        case "1":
                            $withfi_query = $con -> query("SELECT username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw WRIGHT(withdraw_date, 8) LIKE '$withfihour' LIMIT $start , $rowsperpage");
                            break;
                        case "2":
                            $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') HAVING RIGHT(withdraw_date, 8) LIKE '$withfihour' LIMIT $start , $rowsperpage");
                            break;
                        case "3":
                            $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw.username LIKE '%$transusername%' HAVING RIGHT(withdraw_date, 8) LIKE '$withfihour' LIMIT $start , $rowsperpage");
                            break;
                    }
                } else if (!empty($withfidate) && !empty($withfiamount) && !empty($withfihour)) {
                    $i = 0;
                    $values = ["SG","G","SL","SL","E"];
                    $withfiamount_action = "";
                    while (in_array(strtoupper(substr($withfiamount,0,strpos($withfiamount," "))),$values) && $i < 2) {
                        $withfiamount_action .= trim(strtoupper($withfiamount[$i]));
                        $i += 1;
                    }
                    $withfiamount_value = floatval(substr($withfiamount,strpos($withfiamount," ") + 1));
                    switch ($withfiamount_action) {
                        case "SG":
                            switch ($verif_withdraw_case) { 
                                case "1":
                                    $withfi_query = $con -> query("SELECT username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw WHERE username LIKE '%$transusername%' AND withdraw_amount > $withfiamount_value HAVING LEFT(withdraw_date, 10) LIKE '$withfidate' AND RIGHT(withdraw_date, 8) LIKE '$withfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw_amount > $withfiamount_value HAVING LEFT(withdraw_date, 10) LIKE '$withfidate' AND RIGHT(withdraw_date, 8) LIKE '$withfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw.username LIKE '%$transusername%' AND withdraw_amount > $withfiamount_value HAVING LEFT(withdraw_date, 10) LIKE '$withfidate' AND RIGHT(withdraw_date, 8) LIKE '$withfihour' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "G":
                            switch ($verif_withdraw_case) { 
                                case "1":
                                    $withfi_query = $con -> query("SELECT username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw WHERE username LIKE '%$transusername%' AND withdraw_amount >= $withfiamount_value HAVING LEFT(withdraw_date, 10) LIKE '$withfidate' AND RIGHT(withdraw_date, 8) LIKE '$withfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw_amount >= $withfiamount_value HAVING LEFT(withdraw_date, 10) LIKE '$withfidate' AND RIGHT(withdraw_date, 8) LIKE '$withfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw.username LIKE '%$transusername%' AND withdraw_amount >= $withfiamount_value HAVING LEFT(withdraw_date, 10) LIKE '$withfidate' AND RIGHT(withdraw_date, 8) LIKE '$withfihour' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "SL":
                            switch ($verif_withdraw_case) { 
                                case "1":
                                    $withfi_query = $con -> query("SELECT username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw WHERE username LIKE '%$transusername%' AND withdraw_amount < $withfiamount_value HAVING LEFT(withdraw_date, 10) LIKE '$withfidate' AND RIGHT(withdraw_date, 8) LIKE '$withfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw_amount < $withfiamount_value HAVING LEFT(withdraw_date, 10) LIKE '$withfidate' AND RIGHT(withdraw_date, 8) LIKE '$withfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw.username LIKE '%$transusername%' AND withdraw_amount < $withfiamount_value HAVING LEFT(withdraw_date, 10) LIKE '$withfidate' AND RIGHT(withdraw_date, 8) LIKE '$withfihour' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "L":
                            switch ($verif_withdraw_case) { 
                                case "1":
                                    $withfi_query = $con -> query("SELECT username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw WHERE username LIKE '%$transusername%' AND withdraw_amount <= $withfiamount_value  HAVING LEFT(withdraw_date, 10) LIKE '$withfidate' AND RIGHT(withdraw_date, 8) LIKE '$withfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw_amount <= $withfiamount_value  HAVING LEFT(withdraw_date, 10) LIKE '$withfidate' AND RIGHT(withdraw_date, 8) LIKE '$withfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw.username LIKE '%$transusername%' AND withdraw_amount <= $withfiamount_value  HAVING LEFT(withdraw_date, 10) LIKE '$withfidate' AND RIGHT(withdraw_date, 8) LIKE '$withfihour' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "E":
                            switch ($verif_withdraw_case) { 
                                case "1":
                                    $withfi_query = $con -> query("SELECT username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw WHERE username LIKE '%$transusername%' AND withdraw_amount = $withfiamount_value HAVING LEFT(withdraw_date, 10) LIKE '$withfidate' AND RIGHT(withdraw_date, 8) LIKE '$withfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw_amount = $withfiamount_value HAVING LEFT(withdraw_date, 10) LIKE '$withfidate' AND RIGHT(withdraw_date, 8) LIKE '$withfihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $withfi_query = $con -> query("SELECT withdraw.username,LEFT(withdraw_date, 10),RIGHT(withdraw_date, 8),withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw.username LIKE '%$transusername%' AND withdraw_amount = $withfiamount_value HAVING LEFT(withdraw_date, 10) LIKE '$withfidate' AND RIGHT(withdraw_date, 8) LIKE '$withfihour' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                    }
                }
                $withfi_query_array_all = $withfi_query -> fetch_all(MYSQLI_ASSOC);
            }
        }
        if (isset($_POST['reset_withdraw_submit'])) {
            $withficase = "initial";
        }
        if (isset($_POST['withback'])) {
            header("Location: welcome_admin.php");
            unset($_SESSION['verif_withdraw']);
            exit;
        }
        $_SESSION["transwith"] = "transwith";
    }
    if (isset($_SESSION["verif_wire"])) { 
        $verif_wire = $_SESSION["verif_wire"];
        if($verif_wire === "success") { 
            $verif_wire_case = $_SESSION['verif_wire_case'];
            $start = 0;
            $rowsperpage = 6;
            $recs = $con -> query("SELECT * FROM wire");
            $nbrows = $recs -> num_rows;
            $nbpages = ceil($nbrows / $rowsperpage);
            if (isset($_GET["pagenr"])) {
                $start = ($_GET["pagenr"] - 1)  * $rowsperpage;
            }
            switch ($verif_wire_case) {
                case "1":
                    $transusername = $_SESSION["transusername"];

                    $wire_query1 = $con -> query("SELECT username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire WHERE username LIKE '%$transusername%' LIMIT $start , $rowsperpage");
                    $wire_query2 = $con -> query("SELECT username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire WHERE username LIKE '%$transusername%' LIMIT $start , $rowsperpage");
                    break;
                case "2":
                    $transemail = $_SESSION["transemail"];

                    $wire_query1 = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') LIMIT $start , $rowsperpage");
                    $wire_query2 = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') LIMIT $start , $rowsperpage");
                    break;
                case "3":
                    $transemail = $_SESSION["transemail"];
                    $transusername = $_SESSION["transusername"];

                    $wire_query1 = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire.username LIKE '%$transusername%' LIMIT $start , $rowsperpage");
                    $wire_query2 = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire.username LIKE '%$transusername%' LIMIT $start , $rowsperpage");
                    break;
            }
            $wire_query_array_all = $wire_query1 -> fetch_all(MYSQLI_ASSOC);
            $wireficase = "initial";
            if (isset($_POST["wire_submit"])) {
                $wirefidate = filter_input(INPUT_POST, 'wirefidate', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $wirefihour = filter_input(INPUT_POST, 'wirefihour', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $wirefiamount = filter_input(INPUT_POST, 'wirefiamount', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $wireficase = "filter";
                if (!empty($wirefidate) && empty($wirefihour) && empty($wirefiamount)) {
                    switch ($verif_wire_case) { 
                        case "1":
                            $wirefi_query = $con -> query("SELECT username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire WHERE username LIKE '%$transusername%' HAVING LEFT(wire_date, 10) LIKE '$wirefidate' LIMIT $start , $rowsperpage");
                            break;
                        case "2":
                            $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') HAVING LEFT(wire_date, 10) LIKE '$wirefidate' LIMIT $start , $rowsperpage");
                            break;
                        case "3":
                            $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire.username LIKE '%$transusername%' HAVING LEFT(wire_date, 10) LIKE '$wirefidate' LIMIT $start , $rowsperpage");
                            break;
                    }
                } else if (!empty($wirefidate) && !empty($wirefiamount) && empty($wirefihour)) {
                    $i = 0;
                    $values = ["SG","G","SL","SL","E"];
                    $wirefiamount_action = "";
                    while (in_array(strtoupper(substr($wirefiamount,0,strpos($wirefiamount," "))),$values) && $i < 2) {
                        $wirefiamount_action .= trim(strtoupper($wirefiamount[$i]));
                        $i += 1;
                    }
                    $wirefiamount_value = floatval(substr($wirefiamount,strpos($wirefiamount," ") + 1));
                    switch ($wirefiamount_action) {
                        case "SG":
                            switch ($verif_wire_case) { 
                                case "1":
                                    $wirefi_query = $con -> query("SELECT username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire WHERE username LIKE '%$transusername%' AND wire_amount > $wirefiamount_value HAVING LEFT(wire_date, 10) LIKE '$wirefidate' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire_amount > $wirefiamount_value HAVING LEFT(wire_date, 10) LIKE '$wirefidate'  LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire.username LIKE '%$transusername%' AND wire_amount > $wirefiamount_value HAVING LEFT(wire_date, 10) LIKE '$wirefidate' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "G":
                            switch ($verif_wire_case) { 
                                case "1":
                                    $wirefi_query = $con -> query("SELECT username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire WHERE username LIKE '%$transusername%' AND wire_amount >= $wirefiamount_value HAVING LEFT(wire_date, 10) LIKE '$wirefidate' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire_amount >= $wirefiamount_value HAVING LEFT(wire_date, 10) LIKE '$wirefidate' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire.username LIKE '%$transusername%' AND wire_amount >= $wirefiamount_value HAVING LEFT(wire_date, 10) LIKE '$wirefidate' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "SL":
                            switch ($verif_wire_case) { 
                                case "1":
                                    $wirefi_query = $con -> query("SELECT username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire WHERE username LIKE '%$transusername%' AND wire_amount < $wirefiamount_value HAVING LEFT(wire_date, 10) LIKE '$wirefidate' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire_amount < $wirefiamount_value HAVING LEFT(wire_date, 10) LIKE '$wirefidate' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire.username LIKE '%$transusername%' AND wire_amount < $wirefiamount_value HAVING LEFT(wire_date, 10) LIKE '$wirefidate' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "L":
                            switch ($verif_wire_case) { 
                                case "1":
                                    $wirefi_query = $con -> query("SELECT username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire WHERE username LIKE '%$transusername%' AND wire_amount <= $wirefiamount_value  HAVING LEFT(wire_date, 10) LIKE '$wirefidate' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire_amount <= $wirefiamount_value  HAVING LEFT(wire_date, 10) LIKE '$wirefidate' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire.username LIKE '%$transusername%' AND wire_amount <= $wirefiamount_value  HAVING LEFT(wire_date, 10) LIKE '$wirefidate' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "E":
                            switch ($verif_wire_case) { 
                                case "1":
                                    $wirefi_query = $con -> query("SELECT username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire WHERE username LIKE '%$transusername%' AND wire_amount = $wirefiamount_value HAVING LEFT(wire_date, 10) LIKE '$wirefidate' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire_amount = $wirefiamount_value HAVING LEFT(wire_date, 10) LIKE '$wirefidate' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire.username LIKE '%$transusername%' AND wire_amount = $wirefiamount_value HAVING LEFT(wire_date, 10) LIKE '$wirefidate' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                    }
                } else if (!empty($wirefidate) && empty($wirefiamount) && !empty($wirefihour)) {
                    switch ($verif_wire_case) { 
                        case "1":
                            $wirefi_query = $con -> query("SELECT username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire WHERE username LIKE '%$transusername%' HAVING LEFT(wire_date, 10) LIKE '$wirefidate' AND RIGHT(wire_date, 8) LIKE '$wirefihour' LIMIT $start , $rowsperpage");
                            break;
                        case "2":
                            $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') HAVING LEFT(wire_date, 10) LIKE '$wirefidate' AND RIGHT(wire_date, 8) LIKE '$wirefihour' LIMIT $start , $rowsperpage");
                            break;
                        case "3":
                            $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire.username LIKE '%$transusername%' HAVING LEFT(wire_date, 10) LIKE '$wirefidate' AND RIGHT(wire_date, 8) LIKE '$wirefihour' LIMIT $start , $rowsperpage");
                            break;
                    }
                } else if (empty($wirefidate) && !empty($wirefiamount) && empty($wirefihour)) {
                    $i = 0;
                    $values = ["SG","G","SL","SL","E"];
                    $wirefiamount_action = "";
                    while (in_array(strtoupper(substr($wirefiamount,0,strpos($wirefiamount," "))),$values) && $i < 2) {
                        $wirefiamount_action .= trim(strtoupper($wirefiamount[$i]));
                        $i += 1;
                    }
                    $wirefiamount_value = floatval(substr($wirefiamount,strpos($wirefiamount," ") + 1));
                    switch ($wirefiamount_action) {
                        case "SG":
                            switch ($verif_wire_case) { 
                                case "1":
                                    $wirefi_query = $con -> query("SELECT username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire WHERE username LIKE '%$transusername%' AND wire_amount > $wirefiamount_value LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire_amount > $wirefiamount_value LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire.username LIKE '%$transusername%' AND wire_amount > $wirefiamount_value LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "G":
                            switch ($verif_wire_case) { 
                                case "1":
                                    $wirefi_query = $con -> query("SELECT username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire WHERE username LIKE '%$transusername%' AND wire_amount >= $wirefiamount_value LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire_amount >= $wirefiamount_value LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire.username LIKE '%$transusername%' AND wire_amount >= $wirefiamount_value LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "SL":
                            switch ($verif_wire_case) { 
                                case "1":
                                    $wirefi_query = $con -> query("SELECT username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire WHERE username LIKE '%$transusername%' AND wire_amount < $wirefiamount_value LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire_amount < $wirefiamount_value LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire.username LIKE '%$transusername%' AND wire_amount < $wirefiamount_value LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "L":
                            switch ($verif_wire_case) { 
                                case "1":
                                    $wirefi_query = $con -> query("SELECT username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire WHERE username LIKE '%$transusername%' AND wire_amount <= $wirefiamount_value LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire_amount <= $wirefiamount_value LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire.username LIKE '%$transusername%' AND wire_amount <= $wirefiamount_value LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "E":
                            switch ($verif_wire_case) { 
                                case "1":
                                    $wirefi_query = $con -> query("SELECT username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire WHERE username LIKE '%$transusername%' AND wire_amount = $wirefiamount_value LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire_amount = $wirefiamount_value LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire.username LIKE '%$transusername%' AND wire_amount = $wirefiamount_value LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                    }
                } else if (empty($wirefidate) && !empty($wirefiamount) && !empty($wirefihour)) {
                    $i = 0;
                    $values = ["SG","G","SL","SL","E"];
                    $wirefiamount_action = "";
                    while (in_array(strtoupper(substr($wirefiamount,0,strpos($wirefiamount," "))),$values) && $i < 2) {
                        $wirefiamount_action .= trim(strtoupper($wirefiamount[$i]));
                        $i += 1;
                    }
                    $wirefiamount_value = floatval(substr($wirefiamount,strpos($wirefiamount," ") + 1));
                    switch ($wirefiamount_action) {
                        case "SG":
                            switch ($verif_wire_case) { 
                                case "1":
                                    $wirefi_query = $con -> query("SELECT username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire WHERE username LIKE '%$transusername%' AND wire_amount > $wirefiamount_value HAVING RIGHT(wire_date, 8) LIKE '$wirefihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire_amount > $wirefiamount_value HAVING RIGHT(wire_date, 8) LIKE '$wirefihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire.username LIKE '%$transusername%' AND wire_amount > $wirefiamount_value HAVING RIGHT(wire_date, 8) LIKE '$wirefihour' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "G":
                            switch ($verif_wire_case) { 
                                case "1":
                                    $wirefi_query = $con -> query("SELECT username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire WHERE username LIKE '%$transusername%' AND wire_amount >= $wirefiamount_value HAVING RIGHT(wire_date, 8) LIKE '$wirefihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire_amount >= $wirefiamount_value HAVING RIGHT(wire_date, 8) LIKE '$wirefihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire.username LIKE '%$transusername%' AND wire_amount >= $wirefiamount_value HAVING RIGHT(wire_date, 8) LIKE '$wirefihour' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "SL":
                            switch ($verif_wire_case) { 
                                case "1":
                                    $wirefi_query = $con -> query("SELECT username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire WHERE username LIKE '%$transusername%' AND wire_amount < $wirefiamount_value HAVING RIGHT(wire_date, 8) LIKE '$wirefihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire_amount < $wirefiamount_value HAVING RIGHT(wire_date, 8) LIKE '$wirefihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire.username LIKE '%$transusername%' AND wire_amount < $wirefiamount_value HAVING RIGHT(wire_date, 8) LIKE '$wirefihour' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "L":
                            switch ($verif_wire_case) { 
                                case "1":
                                    $wirefi_query = $con -> query("SELECT username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire WHERE username LIKE '%$transusername%' AND wire_amount <= $wirefiamount_value HAVING RIGHT(wire_date, 8) LIKE '$wirefihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire_amount <= $wirefiamount_value HAVING RIGHT(wire_date, 8) LIKE '$wirefihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire.username LIKE '%$transusername%' AND wire_amount <= $wirefiamount_value HAVING RIGHT(wire_date, 8) LIKE '$wirefihour' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "E":
                            switch ($verif_wire_case) { 
                                case "1":
                                    $wirefi_query = $con -> query("SELECT username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire WHERE username LIKE '%$transusername%' AND wire_amount = $wirefiamount_value HAVING RIGHT(wire_date, 8) LIKE '$wirefihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire_amount = $wirefiamount_value HAVING RIGHT(wire_date, 8) LIKE '$wirefihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire.username LIKE '%$transusername%' AND wire_amount = $wirefiamount_value HAVING RIGHT(wire_date, 8) LIKE '$wirefihour' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                    }
                } else if (empty($wirefidate) && empty($wirefiamount) && !empty($wirefihour)) {
                    switch ($verif_wire_case) { 
                        case "1":
                            $wirefi_query = $con -> query("SELECT username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire WRIGHT(wire_date, 8) LIKE '$wirefihour' LIMIT $start , $rowsperpage");
                            break;
                        case "2":
                            $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') HAVING RIGHT(wire_date, 8) LIKE '$wirefihour' LIMIT $start , $rowsperpage");
                            break;
                        case "3":
                            $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire.username LIKE '%$transusername%' HAVING RIGHT(wire_date, 8) LIKE '$wirefihour' LIMIT $start , $rowsperpage");
                            break;
                    }
                } else if (!empty($wirefidate) && !empty($wirefiamount) && !empty($wirefihour)) {
                    $i = 0;
                    $values = ["SG","G","SL","SL","E"];
                    $wirefiamount_action = "";
                    while (in_array(strtoupper(substr($wirefiamount,0,strpos($wirefiamount," "))),$values) && $i < 2) {
                        $wirefiamount_action .= trim(strtoupper($wirefiamount[$i]));
                        $i += 1;
                    }
                    $wirefiamount_value = floatval(substr($wirefiamount,strpos($wirefiamount," ") + 1));
                    switch ($wirefiamount_action) {
                        case "SG":
                            switch ($verif_wire_case) { 
                                case "1":
                                    $wirefi_query = $con -> query("SELECT username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire WHERE username LIKE '%$transusername%' AND wire_amount > $wirefiamount_value HAVING LEFT(wire_date, 10) LIKE '$wirefidate' AND RIGHT(wire_date, 8) LIKE '$wirefihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire_amount > $wirefiamount_value HAVING LEFT(wire_date, 10) LIKE '$wirefidate' AND RIGHT(wire_date, 8) LIKE '$wirefihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire.username LIKE '%$transusername%' AND wire_amount > $wirefiamount_value HAVING LEFT(wire_date, 10) LIKE '$wirefidate' AND RIGHT(wire_date, 8) LIKE '$wirefihour' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "G":
                            switch ($verif_wire_case) { 
                                case "1":
                                    $wirefi_query = $con -> query("SELECT username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire WHERE username LIKE '%$transusername%' AND wire_amount >= $wirefiamount_value HAVING LEFT(wire_date, 10) LIKE '$wirefidate' AND RIGHT(wire_date, 8) LIKE '$wirefihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire_amount >= $wirefiamount_value HAVING LEFT(wire_date, 10) LIKE '$wirefidate' AND RIGHT(wire_date, 8) LIKE '$wirefihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire.username LIKE '%$transusername%' AND wire_amount >= $wirefiamount_value HAVING LEFT(wire_date, 10) LIKE '$wirefidate' AND RIGHT(wire_date, 8) LIKE '$wirefihour' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "SL":
                            switch ($verif_wire_case) { 
                                case "1":
                                    $wirefi_query = $con -> query("SELECT username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire WHERE username LIKE '%$transusername%' AND wire_amount < $wirefiamount_value HAVING LEFT(wire_date, 10) LIKE '$wirefidate' AND RIGHT(wire_date, 8) LIKE '$wirefihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire_amount < $wirefiamount_value HAVING LEFT(wire_date, 10) LIKE '$wirefidate' AND RIGHT(wire_date, 8) LIKE '$wirefihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire.username LIKE '%$transusername%' AND wire_amount < $wirefiamount_value HAVING LEFT(wire_date, 10) LIKE '$wirefidate' AND RIGHT(wire_date, 8) LIKE '$wirefihour' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "L":
                            switch ($verif_wire_case) { 
                                case "1":
                                    $wirefi_query = $con -> query("SELECT username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire WHERE username LIKE '%$transusername%' AND wire_amount <= $wirefiamount_value  HAVING LEFT(wire_date, 10) LIKE '$wirefidate' AND RIGHT(wire_date, 8) LIKE '$wirefihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire_amount <= $wirefiamount_value  HAVING LEFT(wire_date, 10) LIKE '$wirefidate' AND RIGHT(wire_date, 8) LIKE '$wirefihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire.username LIKE '%$transusername%' AND wire_amount <= $wirefiamount_value  HAVING LEFT(wire_date, 10) LIKE '$wirefidate' AND RIGHT(wire_date, 8) LIKE '$wirefihour' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                        case "E":
                            switch ($verif_wire_case) { 
                                case "1":
                                    $wirefi_query = $con -> query("SELECT username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire WHERE username LIKE '%$transusername%' AND wire_amount = $wirefiamount_value HAVING LEFT(wire_date, 10) LIKE '$wirefidate' AND RIGHT(wire_date, 8) LIKE '$wirefihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "2":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire_amount = $wirefiamount_value HAVING LEFT(wire_date, 10) LIKE '$wirefidate' AND RIGHT(wire_date, 8) LIKE '$wirefihour' LIMIT $start , $rowsperpage");
                                    break;
                                case "3":
                                    $wirefi_query = $con -> query("SELECT wire.username,receiver,LEFT(wire_date, 10),RIGHT(wire_date, 8),wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire.username LIKE '%$transusername%' AND wire_amount = $wirefiamount_value HAVING LEFT(wire_date, 10) LIKE '$wirefidate' AND RIGHT(wire_date, 8) LIKE '$wirefihour' LIMIT $start , $rowsperpage");
                                    break;
                            }
                            break;
                    }
                }
                $wirefi_query_array_all = $wirefi_query -> fetch_all(MYSQLI_ASSOC);
            }
        }
        if (isset($_POST['reset_wire_submit'])) {
            $wireficase = "initial";
        }
        if (isset($_POST['wireback'])) {
            header("Location: welcome_admin.php");
            unset($_SESSION['verif_wire']);
            exit;
        }
        $_SESSION["transwire"] = "transwire";
    }
    if (isset($_SESSION["verif_dep_action"])) {
        $verif_dep_action = $_SESSION["verif_dep_action"];
        if (isset($_SESSION['actionusername'])) {
            $actionusername = $_SESSION['actionusername'];
        }
        if (isset($_SESSION['actionemail'])) {
            $actionemail = $_SESSION['actionemail'];
        }
        $_SESSION["actiondep"] = "actiondep";
    }
    if (isset($_SESSION["verif_with_action"])) {
        $verif_with_action = $_SESSION["verif_with_action"];
        if (isset($_SESSION['actionusername'])) {
            $actionusername = $_SESSION['actionusername'];
        }
        if (isset($_SESSION['actionemail'])) {
            $actionemail = $_SESSION['actionemail'];
        }
        $_SESSION["actionwith"] = "actionwith";
    }
    if (isset($_SESSION["verif_wire_action"])) {
        $verif_wire_action = $_SESSION["verif_wire_action"];
        if (isset($_SESSION['actionusername'])) {
            $actionusername = $_SESSION['actionusername'];
        }
        if (isset($_SESSION['actionemail'])) {
            $actionemail = $_SESSION['actionemail'];
        }
        $_SESSION["actionwire"] = "actionwire";
    }
    if (isset($_SESSION["verif_ticket_action"])) {
        $verif_ticket_action = $_SESSION["verif_ticket_action"];
        if (isset($_SESSION['actionusername'])) {
            $actionusername = $_SESSION['actionusername'];
        }
        if (isset($_SESSION['actionemail'])) {
            $actionemail = $_SESSION['actionemail'];
        }
        $_SESSION["actionticket"] = "actionticket";
    }
    if (isset($_SESSION["verif_acc_action"])) {
        $verif_acc_action = $_SESSION["verif_acc_action"];
        if (isset($_SESSION['accuser'])) {
            $accuser = $_SESSION['accuser'];
        }
        if (isset($_SESSION['accmail'])) {
            $accmail = $_SESSION['accmail'];
        }
        $_SESSION["accaccount"] = "accaccount";
    }
}
?>
<?php if(isset($verif_balance) && $verif_balance === "success"): ?>
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
                <?php
                    if (!isset($_GET["pagenr"])) { 
                        $page = 1;
                    } else {
                        $page = $_GET["pagenr"];
                    }
                ?>
                <div class="">Showing <?php echo $page; ?> out of <?php echo $nbpages; ?> pages</div>
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
                    <input type="submit" name="balback" id="but" class="but text-center" value="Back to the admin page" formnovalidate>
                </div>
            </form>
        </div>
        <script src="./bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_deposit) && $verif_deposit === "success"): ?>
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
                        <th>Deposit Date</th>
                        <th>Deposit Hour</th>
                        <th>Deposit Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php if ($depficase === "initial"): ?>
                            <?php foreach ($deposit_query_array_all as $sub_array): ?>
                                <tr>
                                    <?php foreach ($sub_array as $value): ?>
                                        <td><?php echo $value; ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php if ($depficase === "filter"): ?>
                            <?php foreach ($depfi_query_array_all as $sub_array): ?>
                                <tr>
                                    <?php foreach ($sub_array as $value): ?>
                                        <td><?php echo $value; ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <?php
                    if (!isset($_GET["pagenr"])) { 
                        $page = 1;
                    } else {
                        $page = $_GET["pagenr"];
                    }
                ?>
                <div class="">Showing <?php echo $page; ?> out of <?php echo $nbpages; ?> pages</div>
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
                <div class="mt-2">P.S. : If you just want to check records omit these inputs</div>
                <div class="d-flex gap-4">
                    <label class="labbor lab">
                        <img src="./data/calender.svg" alt="">
                        <input type="text" placeholder="Filter by date" id="fidate" name="depfidate">
                    </label>
                    <label class="labbor lab">
                        <img src="./data/clock.svg" alt="">
                        <input type="text" placeholder="Filter by hour" id="fihour" name="depfihour">
                    </label>
                    <label class="labbor lab">
                        <img src="./data/dollar.svg" alt="">
                        <input type="text" placeholder="Filter by amount" id="fiamount" name="depfiamount">
                    </label>
                </div>
                <div class="d-flex gap-4 mt-2 mb-2">
                    <input type="submit" class="but text-center" id="but" value="Filter" onclick="return verifsub3()" name="deposit_submit">
                    <input type="submit" class="but text-center" id="but" value="Reset filter" name="reset_deposit_submit">
                    <input value="Open filtering manual" class="but text-center" type="button" data-bs-toggle="offcanvas" data-bs-target="#manual">
                    <input type="submit" id="but" name="depback" class="but text-center" value="Back to the admin page" formnovalidate>
                </div> 
                <div class="offcanvas offcanvas-start" id="manual">
                    <div class="offcanvas-header">
                        <h1 class="offcanvas-title">Filtering Manual</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                    </div>
                    <div class="offcanvas-body">
                        <div class="mb-3 h3">Date Filtering :</div>
                        <div class="mt-3 mb-3">P.S. : </div>
                        <ul>
                            <li class="mt-1">At least one of the year , month or day needs to be an exact known value</li>
                            <li class="mt-1">Date's Format is YYYY-MM-DD : use % operator for more general filterting</li>
                            <li class="mt-1">It is Mandatory! to keep the format as it is with the 3 "-" (hyphens)</li>
                        </ul>
                        <div class="mt-3">Examples :</div>
                        <ul>
                            <li class="mt-1">" 2023-%-% " (records during 2023 at X month and X day)</li>
                            <li class="mt-1">" 2023-01-% " (records during January 2023 at X day)</li>
                            <li class="mt-1">" %-06-01 " (records during the 1st of June of X year)</li>
                            <li class="mt-1">" %-%-01 " (records during the 1st of X month of X year)</li>
                            <li class="mt-1">etc ...</li>
                        </ul>
                        <div class="mb-3 h3">Hour Filtering :</div>
                        <div class="mt-3 mb-3">P.S. : </div>
                        <ul>
                            <li class="mt-1">At least one of the hour , minutes or seconds needs to be an exact known value</li>
                            <li class="mt-1">Hour's Format is HH:MM:SS : use % operator for more general filterting</li>
                            <li class="mt-1">It is Mandatory! to keep the format as it is with the 3 ":" (colons)</li>
                        </ul>
                        <div class="mt-3">Examples :</div>
                        <ul>
                            <li class="mt-1">" 18:%:% " (records at 18 o'clock at X minutes and X seconds)</li>
                            <li class="mt-1">" 18:30:% " (records at 18:30 o'clock at X seconds)</li>
                            <li class="mt-1">" %:06:01 " (records at X hour , 6 minutes and 1 second)</li>
                            <li class="mt-1">" %:%:01 " (records at X hour , X minutes and 1 second)</li>
                            <li class="mt-1">etc ...</li>
                        </ul>
                        <div class="mb-3 mt-5 h3">Amount Filtering :</div>
                        <div class="mt-3 mb-3">The format is " keyword number "</div>
                        <div class="mt-3 mb-3">Examples : sg 90.8 / e 120 / etc ...</div>
                        <div class="mt-3 mb-2">Keywords : sg / g / sl / l / e</div>
                        <div class="mt-3">Explication :</div>
                        <ul>
                            <li class="mt-1">sg : strictly greater than </li>
                            <li class="mt-1">g : greater than</li>
                            <li class="mt-1">sl : strictly less than</li>
                            <li class="mt-1">l : less than</li>
                            <li class="mt-1">e : equals to</li>
                        </ul>
                        <div class="mt-3 mb-2">P.S.  : </div>
                        <ul>
                            <li class="mt-1">Keywords are not case-sensitive</li>
                            <li class="mt-1">The space between the keyword and the number is necessary</li>
                            <li class="mt-1">DO NOT use "," (comma) instead use "." (full stop) for float numbers</li>
                        </ul>
                        <h4 class="mt-4 mb-3">P.S. : You can filter by all the three criterias together or two by two.</h4>
                    </div>
                </div>
        </form>
    </div>
    <script src="./bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
    <script src="./js/welcome_admin.js"></script>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_withdraw) && $verif_withdraw === "success"): ?>
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
                        <th>Withdraw Date</th>
                        <th>Withdraw Hour</th>
                        <th>Withdraw Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php if ($withficase === "initial"): ?>
                            <?php foreach ($withdraw_query_array_all as $sub_array): ?>
                                <tr>
                                    <?php foreach ($sub_array as $value): ?>
                                        <td><?php echo $value; ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php if ($withficase === "filter"): ?>
                            <?php foreach ($withfi_query_array_all as $sub_array): ?>
                                <tr>
                                    <?php foreach ($sub_array as $value): ?>
                                        <td><?php echo $value; ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <?php
                    if (!isset($_GET["pagenr"])) { 
                        $page = 1;
                    } else {
                        $page = $_GET["pagenr"];
                    }
                ?>
                <div class="">Showing <?php echo $page; ?> out of <?php echo $nbpages; ?> pages</div>
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
                <div class="mt-2">P.S. : If you just want to check records omit these inputs</div>
                <div class="d-flex gap-4">
                    <label class="labbor lab">
                        <img src="./data/calender.svg" alt="">
                        <input type="text" placeholder="Filter by date" id="fidate" name="withfidate">
                    </label>
                    <label class="labbor lab">
                        <img src="./data/clock.svg" alt="">
                        <input type="text" placeholder="Filter by hour" id="fihour" name="withfihour">
                    </label>
                    <label class="labbor lab">
                        <img src="./data/dollar.svg" alt="">
                        <input type="text" placeholder="Filter by amount" id="fiamount" name="withfiamount">
                    </label>
                </div>
                <div class="d-flex gap-4 mt-2 mb-2">
                    <input type="submit" class="but text-center" id="but" value="Filter" onclick="return verifsub3()" name="withdraw_submit">
                    <input type="submit" class="but text-center" id="but" value="Reset filter" name="reset_withdraw_submit">
                    <input value="Open filtering manual" class="but text-center" type="button" data-bs-toggle="offcanvas" data-bs-target="#manual">
                    <input type="submit" id="but" name="withback" class="but text-center" value="Back to the admin page" formnovalidate>
                </div>
                <div class="offcanvas offcanvas-start" id="manual">
                    <div class="offcanvas-header">
                        <h1 class="offcanvas-title">Filtering Manual</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                    </div>
                    <div class="offcanvas-body">
                        <div class="mb-3 h3">Date Filtering :</div>
                        <div class="mt-3 mb-3">P.S. : </div>
                        <ul>
                            <li class="mt-1">At least one of the year , month or day needs to be an exact known value</li>
                            <li class="mt-1">Date's Format is YYYY-MM-DD : use % operator for more general filterting</li>
                            <li class="mt-1">It is Mandatory! to keep the format as it is with the 3 "-" (hyphens)</li>
                        </ul>
                        <div class="mt-3">Examples :</div>
                        <ul>
                            <li class="mt-1">" 2023-%-% " (records during 2023 at X month and X day)</li>
                            <li class="mt-1">" 2023-01-% " (records during January 2023 at X day)</li>
                            <li class="mt-1">" %-06-01 " (records during the 1st of June of X year)</li>
                            <li class="mt-1">" %-%-01 " (records during the 1st of X month of X year)</li>
                            <li class="mt-1">etc ...</li>
                        </ul>
                        <div class="mb-3 h3">Hour Filtering :</div>
                        <div class="mt-3 mb-3">P.S. : </div>
                        <ul>
                            <li class="mt-1">At least one of the hour , minutes or seconds needs to be an exact known value</li>
                            <li class="mt-1">Hour's Format is HH:MM:SS : use % operator for more general filterting</li>
                            <li class="mt-1">It is Mandatory! to keep the format as it is with the 3 ":" (colons)</li>
                        </ul>
                        <div class="mt-3">Examples :</div>
                        <ul>
                            <li class="mt-1">" 18:%:% " (records at 18 o'clock at X minutes and X seconds)</li>
                            <li class="mt-1">" 18:30:% " (records at 18:30 o'clock at X seconds)</li>
                            <li class="mt-1">" %:06:01 " (records at X hour , 6 minutes and 1 second)</li>
                            <li class="mt-1">" %:%:01 " (records at X hour , X minutes and 1 second)</li>
                            <li class="mt-1">etc ...</li>
                        </ul>
                        <div class="mb-3 mt-5 h3">Amount Filtering :</div>
                        <div class="mt-3 mb-3">The format is " keyword number "</div>
                        <div class="mt-3 mb-3">Examples : sg 90.8 / e 120 / etc ...</div>
                        <div class="mt-3 mb-2">Keywords : sg / g / sl / l / e</div>
                        <div class="mt-3">Explication :</div>
                        <ul>
                            <li class="mt-1">sg : strictly greater than </li>
                            <li class="mt-1">g : greater than</li>
                            <li class="mt-1">sl : strictly less than</li>
                            <li class="mt-1">l : less than</li>
                            <li class="mt-1">e : equals to</li>
                        </ul>
                        <div class="mt-3 mb-2">P.S.  : </div>
                        <ul>
                            <li class="mt-1">Keywords are not case-sensitive</li>
                            <li class="mt-1">The space between the keyword and the number is necessary</li>
                            <li class="mt-1">DO NOT use "," (comma) instead use "." (full stop) for float numbers</li>
                        </ul>
                        <h4 class="mt-4 mb-3">P.S. : You can filter by all the three criterias together or two by two.</h4>
                    </div>
                </div>
        </form>
    </div>
    <script src="./bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
    <script src="./js/welcome_admin.js"></script>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_wire) && $verif_wire === "success"): ?>
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
                        <th>Wire_receiver</th>
                        <th>Wire Date</th>
                        <th>Wire Hour</th>
                        <th>Wire Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php if ($wireficase === "initial"): ?>
                            <?php foreach ($wire_query_array_all as $sub_array): ?>
                                <tr>
                                    <?php foreach ($sub_array as $value): ?>
                                        <td><?php echo $value; ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php if ($wireficase === "filter"): ?>
                            <?php foreach ($wirefi_query_array_all as $sub_array): ?>
                                <tr>
                                    <?php foreach ($sub_array as $value): ?>
                                        <td><?php echo $value; ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <?php
                    if (!isset($_GET["pagenr"])) { 
                        $page = 1;
                    } else {
                        $page = $_GET["pagenr"];
                    }
                ?>
                <div class="">Showing <?php echo $page; ?> out of <?php echo $nbpages; ?> pages</div>
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
                <div class="mt-2">P.S. : If you just want to check records omit these inputs</div>
                <div class="d-flex gap-4">
                    <label class="labbor lab">
                        <img src="./data/calender.svg" alt="">
                        <input type="text" placeholder="Filter by date" id="fidate" name="wirefidate">
                    </label>
                    <label class="labbor lab">
                        <img src="./data/clock.svg" alt="">
                        <input type="text" placeholder="Filter by hour" id="fihour" name="wirefihour">
                    </label>
                    <label class="labbor lab">
                        <img src="./data/dollar.svg" alt="">
                        <input type="text" placeholder="Filter by amount" id="fiamount" name="wirefiamount">
                    </label>
                    <label class="labbor lab">
                        <img src="./data/user.svg" alt="">
                        <input type="text" placeholder="Filter by receiver" name="wirefirec">
                    </label>
                </div>
                <div class="d-flex gap-4 mt-2 mb-2">
                    <input type="submit" class="but text-center" id="but" value="Filter" onclick="return verifsub3()" name="wire_submit">
                    <input type="submit" class="but text-center" id="but" value="Reset filter" name="reset_wire_submit">
                    <input value="Open filtering manual" class="but text-center" type="button" data-bs-toggle="offcanvas" data-bs-target="#manual">
                    <input type="submit" id="but" name="wireback" class="but text-center" value="Back to the admin page" formnovalidate>
                </div>
                <div class="offcanvas offcanvas-start" id="manual">
                    <div class="offcanvas-header">
                        <h1 class="offcanvas-title">Filtering Manual</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                    </div>
                    <div class="offcanvas-body">
                        <div class="mb-3 h3">Date Filtering :</div>
                        <div class="mt-3 mb-3">P.S. : </div>
                        <ul>
                            <li class="mt-1">At least one of the year , month or day needs to be an exact known value</li>
                            <li class="mt-1">Date's Format is YYYY-MM-DD : use % operator for more general filterting</li>
                            <li class="mt-1">It is Mandatory! to keep the format as it is with the 3 "-" (hyphens)</li>
                        </ul>
                        <div class="mt-3">Examples :</div>
                        <ul>
                            <li class="mt-1">" 2023-%-% " (records during 2023 at X month and X day)</li>
                            <li class="mt-1">" 2023-01-% " (records during January 2023 at X day)</li>
                            <li class="mt-1">" %-06-01 " (records during the 1st of June of X year)</li>
                            <li class="mt-1">" %-%-01 " (records during the 1st of X month of X year)</li>
                            <li class="mt-1">etc ...</li>
                        </ul>
                        <div class="mb-3 h3">Hour Filtering :</div>
                        <div class="mt-3 mb-3">P.S. : </div>
                        <ul>
                            <li class="mt-1">At least one of the hour , minutes or seconds needs to be an exact known value</li>
                            <li class="mt-1">Hour's Format is HH:MM:SS : use % operator for more general filterting</li>
                            <li class="mt-1">It is Mandatory! to keep the format as it is with the 3 ":" (colons)</li>
                        </ul>
                        <div class="mt-3">Examples :</div>
                        <ul>
                            <li class="mt-1">" 18:%:% " (records at 18 o'clock at X minutes and X seconds)</li>
                            <li class="mt-1">" 18:30:% " (records at 18:30 o'clock at X seconds)</li>
                            <li class="mt-1">" %:06:01 " (records at X hour , 6 minutes and 1 second)</li>
                            <li class="mt-1">" %:%:01 " (records at X hour , X minutes and 1 second)</li>
                            <li class="mt-1">etc ...</li>
                        </ul>
                        <div class="mb-3 mt-5 h3">Amount Filtering :</div>
                        <div class="mt-3 mb-3">The format is " keyword number "</div>
                        <div class="mt-3 mb-3">Examples : sg 90.8 / e 120 / etc ...</div>
                        <div class="mt-3 mb-2">Keywords : sg / g / sl / l / e</div>
                        <div class="mt-3">Explication :</div>
                        <ul>
                            <li class="mt-1">sg : strictly greater than </li>
                            <li class="mt-1">g : greater than</li>
                            <li class="mt-1">sl : strictly less than</li>
                            <li class="mt-1">l : less than</li>
                            <li class="mt-1">e : equals to</li>
                        </ul>
                        <div class="mt-3 mb-2">P.S.  : </div>
                        <ul>
                            <li class="mt-1">Keywords are not case-sensitive</li>
                            <li class="mt-1">The space between the keyword and the number is necessary</li>
                            <li class="mt-1">DO NOT use "," (comma) instead use "." (full stop) for float numbers</li>
                        </ul>
                        <h4 class="mt-4 mb-3">P.S. : You can filter by all the three criterias together or two by two.</h4>
                    </div>
                </div>
        </form>
    </div>
    <script src="./bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
    <script src="./js/welcome_admin.js"></script>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_balance) && $verif_balance === "fail" || isset($verif_deposit) &&  $verif_deposit === "fail" || isset($verif_withdraw) && $verif_withdraw === "fail" || isset($verif_wire) && $verif_wire === "fail"): ?>
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
<?php if(isset($verif_dep_action) && $verif_dep_action === "fail1"): ?>
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
                <h1 style="text-align: center;">Action Failed : " <?php echo $actionusername; ?> " is already allowed to deposit !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_dep_action) && $verif_dep_action === "fail2"): ?>
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
                <h1 style="text-align: center;">Action Failed : " <?php echo $actionusername; ?> " is already blocked from depositing money !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_dep_action) && $verif_dep_action === "fail3"): ?>
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
                <h1 style="text-align: center;">Error 500 : Internal Server Error</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the welcome page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_dep_action) && $verif_dep_action === "fail4"): ?>
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
                <h1 style="text-align: center;">Action Failed : " <?php echo $actionemail; ?> " is already allowed to deposit !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_dep_action) && $verif_dep_action === "fail5"): ?>
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
                <h1 style="text-align: center;">Action Failed : " <?php echo $actionemail; ?> " is already blocked from depositing money !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_dep_action) && $verif_dep_action === "fail6"): ?>
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
                <h1 style="text-align: center;">Action Failed : <?php echo $actionusername; ?> ( <?php echo $actionemail; ?> ) is already allowed to deposit !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_dep_action) && $verif_dep_action === "fail7"): ?>
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
                <h1 style="text-align: center;">Action Failed : <?php echo $actionusername; ?> ( <?php echo $actionemail; ?> ) is already blocked from depositing money !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_dep_action) && $verif_dep_action === "success1"): ?>
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
                <h1 style="text-align: center;">Action Success : You allowed <?php echo $actionusername; ?> to deposit !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_dep_action) && $verif_dep_action === "success2"): ?>
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
                <h1 style="text-align: center;">Action Success : You blocked <?php echo $actionusername; ?> from depositing money !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_dep_action) && $verif_dep_action === "success3"): ?>
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
                <h1 style="text-align: center;">Action Success : You allowed <?php echo $actionemail; ?> to deposit !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_dep_action) && $verif_dep_action === "success4"): ?>
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
                <h1 style="text-align: center;">Action Success : " <?php echo $actionemail; ?> " is blocked from depositing money !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_dep_action) && $verif_dep_action === "success5"): ?>
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
                <h1 style="text-align: center;">Action Success : You allowed <?php echo $actionusername; ?> ( <?php echo $actionemail; ?> ) to deposit !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_dep_action) && $verif_dep_action === "success6"): ?>
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
                <h1 style="text-align: center;">Action Success : You blocked <?php echo $actionusername; ?> ( <?php echo $actionemail; ?> ) from depositing money !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_with_action) && $verif_with_action === "fail1"): ?>
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
                <h1 style="text-align: center;">Action Failed : " <?php echo $actionusername; ?> " is already allowed to withdraw !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_with_action) && $verif_with_action === "fail2"): ?>
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
                <h1 style="text-align: center;">Action Failed : " <?php echo $actionusername; ?> " is already blocked from withdrawing money !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_with_action) && $verif_with_action === "fail3"): ?>
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
                <h1 style="text-align: center;">Error 500 : Internal Server Error</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the welcome page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_with_action) && $verif_with_action === "fail4"): ?>
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
                <h1 style="text-align: center;">Action Failed : " <?php echo $actionemail; ?> " is already allowed to withdraw !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_with_action) && $verif_with_action === "fail5"): ?>
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
                <h1 style="text-align: center;">Action Failed : " <?php echo $actionemail; ?> " is already blocked from withdrawing money !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_with_action) && $verif_with_action === "fail6"): ?>
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
                <h1 style="text-align: center;">Action Failed : <?php echo $actionusername; ?> ( <?php echo $actionemail; ?> ) is already allowed to withdraw !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_with_action) && $verif_with_action === "fail7"): ?>
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
                <h1 style="text-align: center;">Action Failed : <?php echo $actionusername; ?> ( <?php echo $actionemail; ?> ) is already blocked from withdrawing money !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_with_action) && $verif_with_action === "success1"): ?>
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
                <h1 style="text-align: center;">Action Success : You allowed <?php echo $actionusername; ?> to withdraw !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_with_action) && $verif_with_action === "success2"): ?>
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
                <h1 style="text-align: center;">Action Success : You blocked <?php echo $actionusername; ?> from withdrawing money !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_with_action) && $verif_with_action === "success3"): ?>
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
                <h1 style="text-align: center;">Action Success : You allowed <?php echo $actionemail; ?> to withdraw !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_with_action) && $verif_with_action === "success4"): ?>
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
                <h1 style="text-align: center;">Action Success : You blocked <?php echo $actionemail; ?> from withdrawing money !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_with_action) && $verif_with_action === "success5"): ?>
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
                <h1 style="text-align: center;">Action Success : You allowed <?php echo $actionusername; ?> ( <?php echo $actionemail; ?> ) to withdraw !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_with_action) && $verif_with_action === "success6"): ?>
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
                <h1 style="text-align: center;">Action Success : You blocked <?php echo $actionusername; ?> ( <?php echo $actionemail; ?> ) from withdrwing !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_wire_action) && $verif_wire_action === "fail1"): ?>
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
                <h1 style="text-align: center;">Action Failed : " <?php echo $actionusername; ?> " is already allowed to wire money !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_wire_action) && $verif_wire_action === "fail2"): ?>
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
                <h1 style="text-align: center;">Action Failed : " <?php echo $actionusername; ?> " is already blocked from wiring money !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_wire_action) && $verif_wire_action === "fail3"): ?>
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
                <h1 style="text-align: center;">Error 500 : Internal Server Error</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the welcome page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_wire_action) && $verif_wire_action === "fail4"): ?>
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
                <h1 style="text-align: center;">Action Failed : " <?php echo $actionemail; ?> " is already allowed to wire money !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_wire_action) && $verif_wire_action === "fail5"): ?>
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
                <h1 style="text-align: center;">Action Failed : " <?php echo $actionemail; ?> " is already blocked from wiring money !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_wire_action) && $verif_wire_action === "fail6"): ?>
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
                <h1 style="text-align: center;">Action Failed : <?php echo $actionusername; ?> ( <?php echo $actionemail; ?> ) is already allowed to wire money !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_wire_action) && $verif_wire_action === "fail7"): ?>
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
                <h1 style="text-align: center;">Action Failed : <?php echo $actionusername; ?> ( <?php echo $actionemail; ?> ) is already blocked from wiring money !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_wire_action) && $verif_wire_action === "success1"): ?>
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
                <h1 style="text-align: center;">Action Success : You allowed <?php echo $actionusername; ?> to wire money !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_wire_action) && $verif_wire_action === "success2"): ?>
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
                <h1 style="text-align: center;">Action Success : You blocked <?php echo $actionusername; ?> from wiring money !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_wire_action) && $verif_wire_action === "success3"): ?>
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
                <h1 style="text-align: center;">Action Success : " You allowed <?php echo $actionemail; ?> to wire money !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_wire_action) && $verif_wire_action === "success4"): ?>
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
                <h1 style="text-align: center;">Action Success : You blocked <?php echo $actionemail; ?> from wiring money !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_wire_action) && $verif_wire_action === "success5"): ?>
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
                <h1 style="text-align: center;">Action Success : You allowed <?php echo $actionusername; ?> ( <?php echo $actionemail; ?> ) to wire !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_wire_action) && $verif_wire_action === "success6"): ?>
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
                <h1 style="text-align: center;">Action Success : You blocked <?php echo $actionusername; ?> ( <?php echo $actionemail; ?> ) from wiring money !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_ticket_action) && $verif_ticket_action === "fail1"): ?>
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
                <h1 style="text-align: center;">Action Failed : " <?php echo $actionusername; ?> " is already allowed to submit tickets !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_ticket_action) && $verif_ticket_action === "fail2"): ?>
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
                <h1 style="text-align: center;">Action Failed : " <?php echo $actionusername; ?> " is already blocked from submitting tickets !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_ticket_action) && $verif_ticket_action === "fail3"): ?>
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
                <h1 style="text-align: center;">Error 500 : Internal Server Error</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the welcome page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_ticket_action) && $verif_ticket_action === "fail4"): ?>
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
                <h1 style="text-align: center;">Action Failed : " <?php echo $actionemail; ?> " is already allowed to submit tickets !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_ticket_action) && $verif_ticket_action === "fail5"): ?>
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
                <h1 style="text-align: center;">Action Failed : " <?php echo $actionemail; ?> " is already blocked from submitting tickets !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_ticket_action) && $verif_ticket_action === "fail6"): ?>
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
                <h1 style="text-align: center;">Action Failed : <?php echo $actionusername; ?> ( <?php echo $actionemail; ?> ) is already allowed to submit tickets !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_ticket_action) && $verif_ticket_action === "fail7"): ?>
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
                <h1 style="text-align: center;">Action Failed : <?php echo $actionusername; ?> ( <?php echo $actionemail; ?> ) is already blocked from submitting tickets !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_ticket_action) && $verif_ticket_action === "success1"): ?>
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
                <h1 style="text-align: center;">Action Success : You allowed <?php echo $actionusername; ?> to submit tickets !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_ticket_action) && $verif_ticket_action === "success2"): ?>
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
                <h1 style="text-align: center;">Action Success : You blocked <?php echo $actionusername; ?> from submitting tickets !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_ticket_action) && $verif_ticket_action === "success3"): ?>
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
                <h1 style="text-align: center;">Action Success : " You allowed <?php echo $actionemail; ?> to submit tickets !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_ticket_action) && $verif_ticket_action === "success4"): ?>
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
                <h1 style="text-align: center;">Action Success : You blocked <?php echo $actionemail; ?> from submitting tickets !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_ticket_action) && $verif_ticket_action === "success5"): ?>
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
                <h1 style="text-align: center;">Action Success : You allowed <?php echo $actionusername; ?> ( <?php echo $actionemail; ?> ) to ticket !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_ticket_action) && $verif_ticket_action === "success6"): ?>
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
                <h1 style="text-align: center;">Action Success : You blocked <?php echo $actionusername; ?> ( <?php echo $actionemail; ?> ) from submitting tickets !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_acc_action) && $verif_acc_action === "fail1"): ?>
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
                <h1 style="text-align: center;">Action Failed : <?php echo $accuser; ?>'s account is already enabled !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_acc_action) && $verif_acc_action === "fail2"): ?>
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
                <h1 style="text-align: center;">Action Failed : <?php echo $accuser; ?>'s account is already disabled !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_acc_action) && $verif_acc_action === "fail3"): ?>
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
                <h1 style="text-align: center;">Action Failed : <?php echo $accmail; ?>'s account is already enabled !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_acc_action) && $verif_acc_action === "fail4"): ?>
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
                <h1 style="text-align: center;">Action Failed : <?php echo $accmail; ?>'s account is already disabled !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_acc_action) && $verif_acc_action === "fail5"): ?>
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
                <h1 style="text-align: center;">Action Failed : <?php echo $accuser; ?> ( <?php echo $accmail; ?> )'s account is already enabled !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_acc_action) && $verif_acc_action === "fail6"): ?>
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
                <h1 style="text-align: center;">Action Failed : <?php echo $accuser; ?> ( <?php echo $accmail; ?> )'s account is already disabled !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_acc_action) && $verif_acc_action === "fail7"): ?>
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
                <h1 style="text-align: center;">Error 500 : Internal Server Error</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the welcome page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_acc_action) && $verif_acc_action === "success1"): ?>
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
                <h1 style="text-align: center;">Action Success : You enabled <?php echo $accuser; ?>'s account !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_acc_action) && $verif_acc_action === "success2"): ?>
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
                <h1 style="text-align: center;">Action Success : You disabled <?php echo $accuser; ?>'s account !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_acc_action) && $verif_acc_action === "success3"): ?>
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
                <h1 style="text-align: center;">Action Success : You enabled <?php echo $accmail; ?>'s account !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_acc_action) && $verif_acc_action === "success4"): ?>
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
                <h1 style="text-align: center;">Action Success : You disabled <?php echo $accmail; ?>'s account !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_acc_action) && $verif_acc_action === "success5"): ?>
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
                <h1 style="text-align: center;">Action Success : You enabled <?php echo $accuser; ?> ( <?php echo $accmail; ?> )'s account !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>
<?php if(isset($verif_acc_action) && $verif_acc_action === "success6"): ?>
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
                <h1 style="text-align: center;">Action Success : You disabled <?php echo $accuser; ?> ( <?php echo $accmail; ?> )'s account !</h1>
                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the admin page in 4 seconds.</div>
            </div>
        </div>
    </body>
    </html>
<?php endif; ?>