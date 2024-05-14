<?php
require ("linking.php");
if ($con->connect_error) {
    die("Connection Failed" . $con->connect_error);
} else { 
  session_start();
  if(empty($_SESSION['admin_username']) || $_SESSION['admin_username'] == ''){
      session_destroy();
      header("Location: index.php");
      exit;
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
  <?php } else {
    $page_load = true;
    $admin_username = $_SESSION['admin_username'];
    if(empty($_SESSION['admin_username']) || $_SESSION['admin_username'] == ''){
      header("Location: index.php");
      exit;
    }
    $pfp_query = $con -> query("SELECT pfp FROM login_credentials WHERE username = '$admin_username'");
    if ($pfp_query -> num_rows > 0) {
        $pfp_array = $pfp_query -> fetch_all(MYSQLI_ASSOC);
        foreach ($pfp_array as $row) {
            $pfp = $row["pfp"];
        }
    }
    $admin_type_query = $con -> query("SELECT rank FROM login_credentials WHERE username = '$admin_username'");
    if ($admin_type_query -> num_rows > 0) {
        $admin_type_array = $admin_type_query -> fetch_all(MYSQLI_ASSOC);
        foreach ($admin_type_array as $row) {
            $admin_type = $row["rank"];
        }
    }
    $clients_number_query = $con -> query("SELECT count(*) AS clients_number FROM login_credentials");
    if ($clients_number_query -> num_rows > 0) {
        $clients_number_array = $clients_number_query -> fetch_all(MYSQLI_ASSOC);
        foreach ($clients_number_array as $row) {
            $clients_number = $row["clients_number"];
        }
    }
    $stored_money_query = $con -> query("SELECT SUM(balance) AS stored_money from balance");
    if ($stored_money_query -> num_rows > 0) {
        $stored_money_array = $stored_money_query -> fetch_all(MYSQLI_ASSOC);
        foreach ($stored_money_array as $row) {
            $stored_money = $row["stored_money"];
        }
    }
    $today_deposits_query = $con -> query("SELECT count(deposit_date) AS today_deposits FROM deposit WHERE year(deposit_date) = year(now()) AND month(deposit_date) = month(now()) AND day(deposit_date) = day(now())");
    if ($today_deposits_query -> num_rows > 0) {
        $today_deposits_array = $today_deposits_query -> fetch_all(MYSQLI_ASSOC);
        foreach ($today_deposits_array as $row) {
            $today_deposits = $row["today_deposits"];
            $today_deposits_msg = "In the last 24 Hours, there has been $today_deposits deposits";
        }
    }
    $today_withdraws_query = $con -> query("SELECT count(withdraw_date) AS today_withdraws FROM withdraw WHERE year(withdraw_date) = year(now()) AND month(withdraw_date) = month(now()) AND day(withdraw_date) = day(now())");
    if ($today_withdraws_query -> num_rows > 0) {
        $today_withdraws_array = $today_withdraws_query -> fetch_all(MYSQLI_ASSOC);
        foreach ($today_withdraws_array as $row) {
            $today_withdraws = $row["today_withdraws"];
            $today_withdraws_msg = "In the last 24 Hours, there has been $today_withdraws withdraws";
        }
    }
    $today_wires_query = $con -> query("SELECT count(wire_date) AS today_wires FROM wire WHERE year(wire_date) = year(now()) AND month(wire_date) = month(now()) AND day(wire_date) = day(now())");
    if ($today_wires_query -> num_rows > 0) {
        $today_wires_array = $today_wires_query -> fetch_all(MYSQLI_ASSOC);
        foreach ($today_wires_array as $row) {
            $today_wires = $row["today_wires"];
            $today_wires_msg = "In the last 24 Hours, there has been $today_wires wires";
        }
    }
    if (isset($_POST['balance_submit'])) {
      $balanceemail = filter_input(INPUT_POST, 'balanceemail', FILTER_SANITIZE_EMAIL);
      $balanceusername = filter_input(INPUT_POST, 'balanceusername', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
      if (!empty($balanceemail) && empty($balanceusername)) {
        $_SESSION['balanceemail'] = $balanceemail;
        $balance_query = $con -> query("SELECT * FROM balance WHERE email LIKE '%$balanceemail%'");
        if ($balance_query -> num_rows > 0) {
          $_SESSION['verif_balance'] = "success";
          $_SESSION['verif_balance_case'] = "1";
          header("Location: welcome_admin_forms.php");
          exit;
        } else {
          $_SESSION['verif_balance'] = "fail";
          header("Location: welcome_admin_forms.php");
          exit;
        }
      } else if (!empty($balanceusername) && empty($balanceemail)) {
        $_SESSION['balanceusername'] = $balanceusername;
        $balance_query = $con -> query("SELECT * FROM balance WHERE username LIKE '%$balanceusername%'");
        if ($balance_query -> num_rows > 0) {
          $_SESSION['verif_balance'] = "success";
          $_SESSION['verif_balance_case'] = "2";
          header("Location: welcome_admin_forms.php");
          exit;
        } else {
          $_SESSION['verif_balance'] = "fail";
          header("Location: welcome_admin_forms.php");
          exit;
        }
      } else if (!empty($balanceemail) && !empty($balanceusername)) {
        $_SESSION['balanceemail'] = $balanceemail;
        $_SESSION['balanceusername'] = $balanceusername;
        $balance_query = $con -> query("SELECT * FROM balance WHERE username LIKE '%$balanceusername%' AND email LIKE '%$balanceemail%'");
        if ($balance_query -> num_rows > 0) {
          $_SESSION['verif_balance'] = "success";
          $_SESSION['verif_balance_case'] = "3";
          header("Location: welcome_admin_forms.php");
          exit;
        } else {
          $_SESSION['verif_balance'] = "fail";
          header("Location: welcome_admin_forms.php");
          exit;
        }
      }
    }
    if (isset($_POST['deposit_transactions_submit'])) { 
      $transemail = filter_input(INPUT_POST, 'transemail', FILTER_SANITIZE_EMAIL);
      $transusername = filter_input(INPUT_POST, 'transusername', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

      if (!empty($transusername) && empty($transemail)) {
        $_SESSION['transusername'] = $transusername;
        $deposit_query = $con -> query("SELECT username,deposit_date,deposit_amount FROM deposit WHERE username LIKE '%$transusername%'");
        if ($deposit_query -> num_rows > 0) {
          $_SESSION['verif_deposit'] = "success";
          $_SESSION['verif_deposit_case'] = "1";
          header("Location: welcome_admin_forms.php");
          exit;
        } else {
          $_SESSION['verif_deposit'] = "fail";
          header("Location: welcome_admin_forms.php");
          exit;
        }
        } else if (!empty($transemail) && empty($transusername)) {
          $_SESSION['transemail'] = $transemail;
          $deposit_query = $con -> query("SELECT deposit.username,email,deposit_date,deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%')");
          if ($deposit_query -> num_rows > 0) {
            $_SESSION['verif_deposit'] = "success";
            $_SESSION['verif_deposit_case'] = "2";
            header("Location: welcome_admin_forms.php");
            exit;
          } else {
            $_SESSION['verif_deposit'] = "fail";
            header("Location: welcome_admin_forms.php");
            exit;
          }
        } else if (!empty($transemail) && !empty($transusername)) {
          $_SESSION['transusername'] = $transusername;
          $_SESSION['transemail'] = $transemail;
          $deposit_query = $con -> query("SELECT deposit.username,email,deposit_date,deposit_amount FROM deposit,login_credentials WHERE login_credentials.username = deposit.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND deposit.username LIKE '%$transusername%'");
          if ($deposit_query -> num_rows > 0) {
            $_SESSION['verif_deposit'] = "success";
            $_SESSION['verif_deposit_case'] = "3";
            header("Location: welcome_admin_forms.php");
            exit;
          } else {
            $_SESSION['verif_deposit'] = "fail";
            header("Location: welcome_admin_forms.php");
            exit;
          }
        }
    }
    if (isset($_POST['withdraw_transactions_submit'])) { 
      $transemail = filter_input(INPUT_POST, 'transemail', FILTER_SANITIZE_EMAIL);
      $transusername = filter_input(INPUT_POST, 'transusername', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

      if (!empty($transusername) && empty($transemail)) {
        $_SESSION['transusername'] = $transusername;
        $withdraw_query = $con -> query("SELECT username,withdraw_date,withdraw_amount FROM withdraw WHERE username LIKE '%$transusername%'");
        if ($withdraw_query -> num_rows > 0) {
          $_SESSION['verif_withdraw'] = "success";
          $_SESSION['verif_withdraw_case'] = "1";
          header("Location: welcome_admin_forms.php");
          exit;
        } else {
          $_SESSION['verif_withdraw'] = "fail";
          header("Location: welcome_admin_forms.php");
          exit;
        }
        } else if (!empty($transemail) && empty($transusername)) {
          $_SESSION['transemail'] = $transemail;
          $withdraw_query = $con -> query("SELECT withdraw.username,email,withdraw_date,withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%')");
          if ($withdraw_query -> num_rows > 0) {
            $_SESSION['verif_withdraw'] = "success";
            $_SESSION['verif_withdraw_case'] = "2";
            header("Location: welcome_admin_forms.php");
            exit;
          } else {
            $_SESSION['verif_withdraw'] = "fail";
            header("Location: welcome_admin_forms.php");
            exit;
          }
        } else if (!empty($transemail) && !empty($transusername)) {
          $_SESSION['transusername'] = $transusername;
          $_SESSION['transemail'] = $transemail;
          $withdraw_query = $con -> query("SELECT withdraw.username,email,withdraw_date,withdraw_amount FROM withdraw,login_credentials WHERE login_credentials.username = withdraw.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND withdraw.username LIKE '%$transusername%'");
          if ($withdraw_query -> num_rows > 0) {
            $_SESSION['verif_withdraw'] = "success";
            $_SESSION['verif_withdraw_case'] = "3";
            header("Location: welcome_admin_forms.php");
            exit;
          } else {
            $_SESSION['verif_withdraw'] = "fail";
            header("Location: welcome_admin_forms.php");
            exit;
          }
        }
    }
    if (isset($_POST['wire_transactions_submit'])) { 
      $transemail = filter_input(INPUT_POST, 'transemail', FILTER_SANITIZE_EMAIL);
      $transusername = filter_input(INPUT_POST, 'transusername', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

      if (!empty($transusername) && empty($transemail)) {
        $_SESSION['transusername'] = $transusername;
        $wire_query = $con -> query("SELECT username,wire_date,wire_amount FROM wire WHERE username LIKE '%$transusername%'");
        if ($wire_query -> num_rows > 0) {
          $_SESSION['verif_wire'] = "success";
          $_SESSION['verif_wire_case'] = "1";
          header("Location: welcome_admin_forms.php");
          exit;
        } else {
          $_SESSION['verif_wire'] = "fail";
          header("Location: welcome_admin_forms.php");
          exit;
        }
        } else if (!empty($transemail) && empty($transusername)) {
          $_SESSION['transemail'] = $transemail;
          $wire_query = $con -> query("SELECT wire.username,email,wire_date,wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%')");
          if ($wire_query -> num_rows > 0) {
            $_SESSION['verif_wire'] = "success";
            $_SESSION['verif_wire_case'] = "2";
            header("Location: welcome_admin_forms.php");
            exit;
          } else {
            $_SESSION['verif_wire'] = "fail";
            header("Location: welcome_admin_forms.php");
            exit;
          }
        } else if (!empty($transemail) && !empty($transusername)) {
          $_SESSION['transusername'] = $transusername;
          $_SESSION['transemail'] = $transemail;
          $wire_query = $con -> query("SELECT wire.username,email,wire_date,wire_amount FROM wire,login_credentials WHERE login_credentials.username = wire.username AND email IN (SELECT email FROM login_credentials WHERE email LIKE '%$transemail%') AND wire.username LIKE '%$transusername%'");
          if ($wire_query -> num_rows > 0) {
            $_SESSION['verif_wire'] = "success";
            $_SESSION['verif_wire_case'] = "3";
            header("Location: welcome_admin_forms.php");
            exit;
          } else {
            $_SESSION['verif_wire'] = "fail";
            header("Location: welcome_admin_forms.php");
            exit;
          }
        }
    }
    if (isset($_POST['block_dep_submit'])) {
      $actionemail = filter_input(INPUT_POST, 'blockemail', FILTER_SANITIZE_EMAIL);
      $actionusername = filter_input(INPUT_POST, 'blockusername', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
      $actionradio = filter_input(INPUT_POST, 'actions', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
      if (!empty($actionusername) && empty($actionemail)) { 
        $action_query = $con -> query("SELECT deposit FROM blacklist WHERE username = '$actionusername'");
        if ($action_query -> num_rows > 0) {
          $_SESSION['actionusername'] = $actionusername;
          $action_query_array = $action_query -> fetch_all(MYSQLI_ASSOC);
          foreach ($action_query_array as $row) {
              $actionsql = $row["deposit"];
          }
          if ($actionradio === "allow" && $actionsql === "1") {
            $_SESSION["verif_dep_action"] = "fail1";
            header("Location: welcome_admin_forms.php");
            exit;
          } else if ($actionradio === "block" && $actionsql === "0") {
            $_SESSION["verif_dep_action"] = "fail2";
            header("Location: welcome_admin_forms.php");
            exit;
          } else if ($actionradio === "allow" && $actionsql === "0") {
            $con -> query("UPDATE blacklist SET deposit = 1 WHERE username = '$actionusername'");
            if ($con -> affected_rows > 0) {
              $_SESSION["verif_dep_action"] = "success1";
              header("Location: welcome_admin_forms.php");
              exit;
            } else {
              $_SESSION["verif_dep_action"] = "fail3";
              header("Location: welcome_admin_forms.php");
              exit;
            }
          } else if ($actionradio === "block" && $actionsql === "1") {
            $con -> query("UPDATE blacklist SET deposit = 0 WHERE username = '$actionusername'");
            if ($con -> affected_rows > 0) {
              $_SESSION["verif_dep_action"] = "success2";
              header("Location: welcome_admin_forms.php");
              exit;
            } else {
              $_SESSION["verif_dep_action"] = "fail3";
              header("Location: welcome_admin_forms.php");
              exit;
            }
          }
        } else {
          $_SESSION["verif_dep_action"] = "fail3";
          header("Location: welcome_admin_forms.php");
          exit;
        }
      } else if (empty($actionusername) && !empty($actionemail)) { 
        $action_query = $con -> query("SELECT deposit FROM blacklist b,login_credentials l WHERE l.username = b.username AND email IN (SELECT email FROM login_credentials WHERE email = '$actionemail')");
        if ($action_query -> num_rows > 0) {
          $_SESSION['actionemail'] = $actionemail;
          $action_query_array = $action_query -> fetch_all(MYSQLI_ASSOC);
          foreach ($action_query_array as $row) {
              $actionsql = $row["deposit"];
          }
          if ($actionradio === "allow" && $actionsql === "1") {
            $_SESSION["verif_dep_action"] = "fail4";
            header("Location: welcome_admin_forms.php");
            exit;
          } else if ($actionradio === "block" && $actionsql === "0") {
            $_SESSION["verif_dep_action"] = "fail5";
            header("Location: welcome_admin_forms.php");
            exit;
          } else if ($actionradio === "allow" && $actionsql === "0") {
            $con -> query("UPDATE blacklist,login_credentials SET deposit = 1 WHERE login_credentials.username = blacklist.username AND login_credentials.email IN (SELECT email FROM login_credentials WHERE email = '$actionemail')");
            if ($con -> affected_rows > 0) {
              $_SESSION["verif_dep_action"] = "success3";
              header("Location: welcome_admin_forms.php");
              exit;
            } else {
              $_SESSION["verif_dep_action"] = "fail3";
              header("Location: welcome_admin_forms.php");
              exit;
            }
          } else if ($actionradio === "block" && $actionsql === "1") {
            $con -> query("UPDATE blacklist,login_credentials SET deposit = 0 WHERE login_credentials.username = blacklist.username AND login_credentials.email IN (SELECT email FROM login_credentials WHERE email = '$actionemail')");
            if ($con -> affected_rows > 0) {
              $_SESSION["verif_dep_action"] = "success4";
              header("Location: welcome_admin_forms.php");
              exit;
            } else {
              $_SESSION["verif_dep_action"] = "fail3";
              header("Location: welcome_admin_forms.php");
              exit;
            }
          }
        } else {
          $_SESSION["verif_dep_action"] = "fail3";
          header("Location: welcome_admin_forms.php");
          exit;
        }
      } else if (!empty($actionusername) && !empty($actionemail)) { 
        $action_query = $con -> query("SELECT deposit FROM blacklist b,login_credentials l WHERE l.username = b.username AND email IN (SELECT email FROM login_credentials WHERE email = '$actionemail') AND b.username = '$actionusername'");
        if ($action_query -> num_rows > 0) {
          $_SESSION['actionusername'] = $actionusername;
          $_SESSION['actionemail'] = $actionemail;
          $action_query_array = $action_query -> fetch_all(MYSQLI_ASSOC);
          foreach ($action_query_array as $row) {
              $actionsql = $row["deposit"];
          }
          if ($actionradio === "allow" && $actionsql === "1") {
            $_SESSION["verif_dep_action"] = "fail6";
            header("Location: welcome_admin_forms.php");
            exit;
          } else if ($actionradio === "block" && $actionsql === "0") {
            $_SESSION["verif_dep_action"] = "fail7";
            header("Location: welcome_admin_forms.php");
            exit;
          } else if ($actionradio === "allow" && $actionsql === "0") {
            $con -> query("UPDATE blacklist,login_credentials SET deposit = 1 WHERE login_credentials.username = blacklist.username AND login_credentials.email IN (SELECT email FROM login_credentials WHERE email = '$actionemail') AND blacklist.username = '$actionusername'");
            if ($con -> affected_rows > 0) {
              $_SESSION["verif_dep_action"] = "success5";
              header("Location: welcome_admin_forms.php");
              exit;
            } else {
              $_SESSION["verif_dep_action"] = "fail3";
              header("Location: welcome_admin_forms.php");
              exit;
            }
          } else if ($actionradio === "block" && $actionsql === "1") {
            $con -> query("UPDATE blacklist,login_credentials SET deposit = 0 WHERE login_credentials.username = blacklist.username AND login_credentials.email IN (SELECT email FROM login_credentials WHERE email = '$actionemail') AND blacklist.username = '$actionusername'");
            if ($con -> affected_rows > 0) {
              $_SESSION["verif_dep_action"] = "success6";
              header("Location: welcome_admin_forms.php");
              exit;
            } else {
              $_SESSION["verif_dep_action"] = "fail3";
              header("Location: welcome_admin_forms.php");
              exit;
            }
          }
        } else {
          $_SESSION["verif_dep_action"] = "fail3";
          header("Location: welcome_admin_forms.php");
          exit;
        }
      }
    }
    if (isset($_POST['block_with_submit'])) {
      $actionemail = filter_input(INPUT_POST, 'blockemail', FILTER_SANITIZE_EMAIL);
      $actionusername = filter_input(INPUT_POST, 'blockusername', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
      $actionradio = filter_input(INPUT_POST, 'actions', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
      if (!empty($actionusername) && empty($actionemail)) { 
        $action_query = $con -> query("SELECT withdraw FROM blacklist WHERE username = '$actionusername'");
        if ($action_query -> num_rows > 0) {
          $_SESSION['actionusername'] = $actionusername;
          $action_query_array = $action_query -> fetch_all(MYSQLI_ASSOC);
          foreach ($action_query_array as $row) {
              $actionsql = $row["withdraw"];
          }
          if ($actionradio === "allow" && $actionsql === "1") {
            $_SESSION["verif_with_action"] = "fail1";
            header("Location: welcome_admin_forms.php");
            exit;
          } else if ($actionradio === "block" && $actionsql === "0") {
            $_SESSION["verif_with_action"] = "fail2";
            header("Location: welcome_admin_forms.php");
            exit;
          } else if ($actionradio === "allow" && $actionsql === "0") {
            $con -> query("UPDATE blacklist SET withdraw = 1 WHERE username = '$actionusername'");
            if ($con -> affected_rows > 0) {
              $_SESSION["verif_with_action"] = "success1";
              header("Location: welcome_admin_forms.php");
              exit;
            } else {
              $_SESSION["verif_with_action"] = "fail3";
              header("Location: welcome_admin_forms.php");
              exit;
            }
          } else if ($actionradio === "block" && $actionsql === "1") {
            $con -> query("UPDATE blacklist SET withdraw = 0 WHERE username = '$actionusername'");
            if ($con -> affected_rows > 0) {
              $_SESSION["verif_with_action"] = "success2";
              header("Location: welcome_admin_forms.php");
              exit;
            } else {
              $_SESSION["verif_with_action"] = "fail3";
              header("Location: welcome_admin_forms.php");
              exit;
            }
          }
        } else {
          $_SESSION["verif_with_action"] = "fail3";
          header("Location: welcome_admin_forms.php");
          exit;
        }
      } else if (empty($actionusername) && !empty($actionemail)) { 
        $action_query = $con -> query("SELECT withdraw FROM blacklist b,login_credentials l WHERE l.username = b.username AND email IN (SELECT email FROM login_credentials WHERE email = '$actionemail')");
        if ($action_query -> num_rows > 0) {
          $_SESSION['actionemail'] = $actionemail;
          $action_query_array = $action_query -> fetch_all(MYSQLI_ASSOC);
          foreach ($action_query_array as $row) {
              $actionsql = $row["withdraw"];
          }
          if ($actionradio === "allow" && $actionsql === "1") {
            $_SESSION["verif_with_action"] = "fail4";
            header("Location: welcome_admin_forms.php");
            exit;
          } else if ($actionradio === "block" && $actionsql === "0") {
            $_SESSION["verif_with_action"] = "fail5";
            header("Location: welcome_admin_forms.php");
            exit;
          } else if ($actionradio === "allow" && $actionsql === "0") {
            $con -> query("UPDATE blacklist,login_credentials SET withdraw = 1 WHERE login_credentials.username = blacklist.username AND login_credentials.email IN (SELECT email FROM login_credentials WHERE email = '$actionemail')");
            if ($con -> affected_rows > 0) {
              $_SESSION["verif_with_action"] = "success3";
              header("Location: welcome_admin_forms.php");
              exit;
            } else {
              $_SESSION["verif_with_action"] = "fail3";
              header("Location: welcome_admin_forms.php");
              exit;
            }
          } else if ($actionradio === "block" && $actionsql === "1") {
            $con -> query("UPDATE blacklist,login_credentials SET withdraw = 0 WHERE login_credentials.username = blacklist.username AND login_credentials.email IN (SELECT email FROM login_credentials WHERE email = '$actionemail')");
            if ($con -> affected_rows > 0) {
              $_SESSION["verif_with_action"] = "success4";
              header("Location: welcome_admin_forms.php");
              exit;
            } else {
              $_SESSION["verif_with_action"] = "fail3";
              header("Location: welcome_admin_forms.php");
              exit;
            }
          }
        } else {
          $_SESSION["verif_with_action"] = "fail3";
          header("Location: welcome_admin_forms.php");
          exit;
        }
      } else if (!empty($actionusername) && !empty($actionemail)) { 
        $action_query = $con -> query("SELECT withdraw FROM blacklist b,login_credentials l WHERE l.username = b.username AND email IN (SELECT email FROM login_credentials WHERE email = '$actionemail') AND b.username = '$actionusername'");
        if ($action_query -> num_rows > 0) {
          $_SESSION['actionusername'] = $actionusername;
          $_SESSION['actionemail'] = $actionemail;
          $action_query_array = $action_query -> fetch_all(MYSQLI_ASSOC);
          foreach ($action_query_array as $row) {
              $actionsql = $row["withdraw"];
          }
          if ($actionradio === "allow" && $actionsql === "1") {
            $_SESSION["verif_with_action"] = "fail6";
            header("Location: welcome_admin_forms.php");
            exit;
          } else if ($actionradio === "block" && $actionsql === "0") {
            $_SESSION["verif_with_action"] = "fail7";
            header("Location: welcome_admin_forms.php");
            exit;
          } else if ($actionradio === "allow" && $actionsql === "0") {
            $con -> query("UPDATE blacklist,login_credentials SET withdraw = 1 WHERE login_credentials.username = blacklist.username AND login_credentials.email IN (SELECT email FROM login_credentials WHERE email = '$actionemail') AND blacklist.username = '$actionusername'");
            if ($con -> affected_rows > 0) {
              $_SESSION["verif_with_action"] = "success5";
              header("Location: welcome_admin_forms.php");
              exit;
            } else {
              $_SESSION["verif_with_action"] = "fail3";
              header("Location: welcome_admin_forms.php");
              exit;
            }
          } else if ($actionradio === "block" && $actionsql === "1") {
            $con -> query("UPDATE blacklist,login_credentials SET withdraw = 0 WHERE login_credentials.username = blacklist.username AND login_credentials.email IN (SELECT email FROM login_credentials WHERE email = '$actionemail') AND blacklist.username = '$actionusername'");
            if ($con -> affected_rows > 0) {
              $_SESSION["verif_with_action"] = "success6";
              header("Location: welcome_admin_forms.php");
              exit;
            } else {
              $_SESSION["verif_with_action"] = "fail3";
              header("Location: welcome_admin_forms.php");
              exit;
            }
          }
        } else {
          $_SESSION["verif_with_action"] = "fail3";
          header("Location: welcome_admin_forms.php");
          exit;
        }
      }
    }
    if (isset($_POST['block_wire_submit'])) {
      $actionemail = filter_input(INPUT_POST, 'blockemail', FILTER_SANITIZE_EMAIL);
      $actionusername = filter_input(INPUT_POST, 'blockusername', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
      $actionradio = filter_input(INPUT_POST, 'actions', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
      if (!empty($actionusername) && empty($actionemail)) { 
        $action_query = $con -> query("SELECT wire FROM blacklist WHERE username = '$actionusername'");
        if ($action_query -> num_rows > 0) {
          $_SESSION['actionusername'] = $actionusername;
          $action_query_array = $action_query -> fetch_all(MYSQLI_ASSOC);
          foreach ($action_query_array as $row) {
              $actionsql = $row["wire"];
          }
          if ($actionradio === "allow" && $actionsql === "1") {
            $_SESSION["verif_wire_action"] = "fail1";
            header("Location: welcome_admin_forms.php");
            exit;
          } else if ($actionradio === "block" && $actionsql === "0") {
            $_SESSION["verif_wire_action"] = "fail2";
            header("Location: welcome_admin_forms.php");
            exit;
          } else if ($actionradio === "allow" && $actionsql === "0") {
            $con -> query("UPDATE blacklist SET wire = 1 WHERE username = '$actionusername'");
            if ($con -> affected_rows > 0) {
              $_SESSION["verif_wire_action"] = "success1";
              header("Location: welcome_admin_forms.php");
              exit;
            } else {
              $_SESSION["verif_wire_action"] = "fail3";
              header("Location: welcome_admin_forms.php");
              exit;
            }
          } else if ($actionradio === "block" && $actionsql === "1") {
            $con -> query("UPDATE blacklist SET wire = 0 WHERE username = '$actionusername'");
            if ($con -> affected_rows > 0) {
              $_SESSION["verif_wire_action"] = "success2";
              header("Location: welcome_admin_forms.php");
              exit;
            } else {
              $_SESSION["verif_wire_action"] = "fail3";
              header("Location: welcome_admin_forms.php");
              exit;
            }
          }
        } else {
          $_SESSION["verif_wire_action"] = "fail3";
          header("Location: welcome_admin_forms.php");
          exit;
        }
      } else if (empty($actionusername) && !empty($actionemail)) { 
        $action_query = $con -> query("SELECT wire FROM blacklist b,login_credentials l WHERE l.username = b.username AND email IN (SELECT email FROM login_credentials WHERE email = '$actionemail')");
        if ($action_query -> num_rows > 0) {
          $_SESSION['actionemail'] = $actionemail;
          $action_query_array = $action_query -> fetch_all(MYSQLI_ASSOC);
          foreach ($action_query_array as $row) {
              $actionsql = $row["wire"];
          }
          if ($actionradio === "allow" && $actionsql === "1") {
            $_SESSION["verif_wire_action"] = "fail4";
            header("Location: welcome_admin_forms.php");
            exit;
          } else if ($actionradio === "block" && $actionsql === "0") {
            $_SESSION["verif_wire_action"] = "fail5";
            header("Location: welcome_admin_forms.php");
            exit;
          } else if ($actionradio === "allow" && $actionsql === "0") {
            $con -> query("UPDATE blacklist b,login_credentials l SET wire = 1 WHERE l.username = b.username AND l.email IN (SELECT email FROM login_credentials WHERE email = '$actionemail')");
            if ($con -> affected_rows > 0) {
              $_SESSION["verif_wire_action"] = "success3";
              header("Location: welcome_admin_forms.php");
              exit;
            } else {
              $_SESSION["verif_wire_action"] = "fail3";
              header("Location: welcome_admin_forms.php");
              exit;
            }
          } else if ($actionradio === "block" && $actionsql === "1") {
            $con -> query("UPDATE blacklist,login_credentials SET wire = 0 WHERE login_credentials.username = blacklist.username AND login_credentials.email IN (SELECT email FROM login_credentials WHERE email = '$actionemail')");
            if ($con -> affected_rows > 0) {
              $_SESSION["verif_wire_action"] = "success4";
              header("Location: welcome_admin_forms.php");
              exit;
            } else {
              $_SESSION["verif_wire_action"] = "fail3";
              header("Location: welcome_admin_forms.php");
              exit;
            }
          }
        } else {
          $_SESSION["verif_wire_action"] = "fail3";
          header("Location: welcome_admin_forms.php");
          exit;
        }
      } else if (!empty($actionusername) && !empty($actionemail)) { 
        $action_query = $con -> query("SELECT wire FROM blacklist b,login_credentials l WHERE l.username = b.username AND email IN (SELECT email FROM login_credentials WHERE email = '$actionemail') AND b.username = '$actionusername'");
        if ($action_query -> num_rows > 0) {
          $_SESSION['actionusername'] = $actionusername;
          $_SESSION['actionusername'] = $actionusername;
          $_SESSION['actionemail'] = $actionemail;
          $action_query_array = $action_query -> fetch_all(MYSQLI_ASSOC);
          foreach ($action_query_array as $row) {
              $actionsql = $row["wire"];
          }
          if ($actionradio === "allow" && $actionsql === "1") {
            $_SESSION["verif_wire_action"] = "fail6";
            header("Location: welcome_admin_forms.php");
            exit;
          } else if ($actionradio === "block" && $actionsql === "0") {
            $_SESSION["verif_wire_action"] = "fail7";
            header("Location: welcome_admin_forms.php");
            exit;
          } else if ($actionradio === "allow" && $actionsql === "0") {
            $con -> query("UPDATE blacklist,login_credentials SET wire = 1 WHERE login_credentials.username = blacklist.username AND login_credentials.email IN (SELECT email FROM login_credentials WHERE email = '$actionemail') AND blacklist.username = '$actionusername'");
            if ($con -> affected_rows > 0) {
              $_SESSION["verif_wire_action"] = "success5";
              header("Location: welcome_admin_forms.php");
              exit;
            } else {
              $_SESSION["verif_wire_action"] = "fail3";
              header("Location: welcome_admin_forms.php");
              exit;
            }
          } else if ($actionradio === "block" && $actionsql === "1") {
            $con -> query("UPDATE blacklist,login_credentials SET wire = 0 WHERE login_credentials.username = blacklist.username AND login_credentials.email IN (SELECT email FROM login_credentials WHERE email = '$actionemail') AND blacklist.username = '$actionusername'");
            if ($con -> affected_rows > 0) {
              $_SESSION["verif_wire_action"] = "success6";
              header("Location: welcome_admin_forms.php");
              exit;
            } else {
              $_SESSION["verif_wire_action"] = "fail3";
              header("Location: welcome_admin_forms.php");
              exit;
            }
          }
        } else {
          $_SESSION["verif_wire_action"] = "fail3";
          header("Location: welcome_admin_forms.php");
          exit;
        }
      }
    }
    if (isset($_POST['block_ticket_submit'])) { 
      $actionemail = filter_input(INPUT_POST, 'blockemail', FILTER_SANITIZE_EMAIL);
      $actionusername = filter_input(INPUT_POST, 'blockusername', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
      $actionradio = filter_input(INPUT_POST, 'actions', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
      if (!empty($actionusername) && empty($actionemail)) { 
        $action_query = $con -> query("SELECT ticket FROM blacklist WHERE username = '$actionusername'");
        if ($action_query -> num_rows > 0) {
          $_SESSION['actionusername'] = $actionusername;
          $action_query_array = $action_query -> fetch_all(MYSQLI_ASSOC);
          foreach ($action_query_array as $row) {
              $actionsql = $row["ticket"];
          }
          if ($actionradio === "allow" && $actionsql === "1") {
            $_SESSION["verif_ticket_action"] = "fail1";
            header("Location: welcome_admin_forms.php");
            exit;
          } else if ($actionradio === "block" && $actionsql === "0") {
            $_SESSION["verif_ticket_action"] = "fail2";
            header("Location: welcome_admin_forms.php");
            exit;
          } else if ($actionradio === "allow" && $actionsql === "0") {
            $con -> query("UPDATE blacklist SET ticket = 1 WHERE username = '$actionusername'");
            if ($con -> affected_rows > 0) {
              $_SESSION["verif_ticket_action"] = "success1";
              header("Location: welcome_admin_forms.php");
              exit;
            } else {
              $_SESSION["verif_ticket_action"] = "fail3";
              header("Location: welcome_admin_forms.php");
              exit;
            }
          } else if ($actionradio === "block" && $actionsql === "1") {
            $con -> query("UPDATE blacklist SET ticket = 0 WHERE username = '$actionusername'");
            if ($con -> affected_rows > 0) {
              $_SESSION["verif_ticket_action"] = "success2";
              header("Location: welcome_admin_forms.php");
              exit;
            } else {
              $_SESSION["verif_ticket_action"] = "fail3";
              header("Location: welcome_admin_forms.php");
              exit;
            }
          }
        } else {
          $_SESSION["verif_ticket_action"] = "fail3";
          header("Location: welcome_admin_forms.php");
          exit;
        }
      } else if (empty($actionusername) && !empty($actionemail)) { 
        $action_query = $con -> query("SELECT ticket FROM blacklist b,login_credentials l WHERE l.username = b.username AND email IN (SELECT email FROM login_credentials WHERE email = '$actionemail')");
        if ($action_query -> num_rows > 0) {
          $_SESSION['actionemail'] = $actionemail;
          $action_query_array = $action_query -> fetch_all(MYSQLI_ASSOC);
          foreach ($action_query_array as $row) {
              $actionsql = $row["ticket"];
          }
          if ($actionradio === "allow" && $actionsql === "1") {
            $_SESSION["verif_ticket_action"] = "fail4";
            header("Location: welcome_admin_forms.php");
            exit;
          } else if ($actionradio === "block" && $actionsql === "0") {
            $_SESSION["verif_ticket_action"] = "fail5";
            header("Location: welcome_admin_forms.php");
            exit;
          } else if ($actionradio === "allow" && $actionsql === "0") {
            $con -> query("UPDATE blacklist b,login_credentials l SET ticket = 1 WHERE l.username = b.username AND l.email IN (SELECT email FROM login_credentials WHERE email = '$actionemail')");
            if ($con -> affected_rows > 0) {
              $_SESSION["verif_ticket_action"] = "success3";
              header("Location: welcome_admin_forms.php");
              exit;
            } else {
              $_SESSION["verif_ticket_action"] = "fail3";
              header("Location: welcome_admin_forms.php");
              exit;
            }
          } else if ($actionradio === "block" && $actionsql === "1") {
            $con -> query("UPDATE blacklist,login_credentials SET ticket = 0 WHERE login_credentials.username = blacklist.username AND login_credentials.email IN (SELECT email FROM login_credentials WHERE email = '$actionemail')");
            if ($con -> affected_rows > 0) {
              $_SESSION["verif_ticket_action"] = "success4";
              header("Location: welcome_admin_forms.php");
              exit;
            } else {
              $_SESSION["verif_ticket_action"] = "fail3";
              header("Location: welcome_admin_forms.php");
              exit;
            }
          }
        } else {
          $_SESSION["verif_ticket_action"] = "fail3";
          header("Location: welcome_admin_forms.php");
          exit;
        }
      } else if (!empty($actionusername) && !empty($actionemail)) { 
        $action_query = $con -> query("SELECT ticket FROM blacklist b,login_credentials l WHERE l.username = b.username AND email IN (SELECT email FROM login_credentials WHERE email = '$actionemail') AND b.username = '$actionusername'");
        if ($action_query -> num_rows > 0) {
          $_SESSION['actionusername'] = $actionusername;
          $_SESSION['actionusername'] = $actionusername;
          $_SESSION['actionemail'] = $actionemail;
          $action_query_array = $action_query -> fetch_all(MYSQLI_ASSOC);
          foreach ($action_query_array as $row) {
              $actionsql = $row["ticket"];
          }
          if ($actionradio === "allow" && $actionsql === "1") {
            $_SESSION["verif_ticket_action"] = "fail6";
            header("Location: welcome_admin_forms.php");
            exit;
          } else if ($actionradio === "block" && $actionsql === "0") {
            $_SESSION["verif_ticket_action"] = "fail7";
            header("Location: welcome_admin_forms.php");
            exit;
          } else if ($actionradio === "allow" && $actionsql === "0") {
            $con -> query("UPDATE blacklist,login_credentials SET ticket = 1 WHERE login_credentials.username = blacklist.username AND login_credentials.email IN (SELECT email FROM login_credentials WHERE email = '$actionemail') AND blacklist.username = '$actionusername'");
            if ($con -> affected_rows > 0) {
              $_SESSION["verif_ticket_action"] = "success5";
              header("Location: welcome_admin_forms.php");
              exit;
            } else {
              $_SESSION["verif_ticket_action"] = "fail3";
              header("Location: welcome_admin_forms.php");
              exit;
            }
          } else if ($actionradio === "block" && $actionsql === "1") {
            $con -> query("UPDATE blacklist,login_credentials SET ticket = 0 WHERE login_credentials.username = blacklist.username AND login_credentials.email IN (SELECT email FROM login_credentials WHERE email = '$actionemail') AND blacklist.username = '$actionusername'");
            if ($con -> affected_rows > 0) {
              $_SESSION["verif_ticket_action"] = "success6";
              header("Location: welcome_admin_forms.php");
              exit;
            } else {
              $_SESSION["verif_ticket_action"] = "fail3";
              header("Location: welcome_admin_forms.php");
              exit;
            }
          }
        } else {
          $_SESSION["verif_ticket_action"] = "fail3";
          header("Location: welcome_admin_forms.php");
          exit;
        }
      }
    }
    if (isset($_POST['accsub'])) { 
      $accmail = filter_input(INPUT_POST, 'accmail', FILTER_SANITIZE_EMAIL);
      $accuser = filter_input(INPUT_POST, 'accuser', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
      $accactions = filter_input(INPUT_POST, 'accactions', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
      if (!empty($accuser) && empty($accmail)) { 
        $accaction_query = $con -> query("SELECT account FROM blacklist WHERE username = '$accuser'");
        if ($accaction_query -> num_rows > 0) {
          $_SESSION['accuser'] = $accuser;
          $accaction_query_array = $accaction_query -> fetch_all(MYSQLI_ASSOC);
          foreach ($accaction_query_array as $row) {
              $accactionsql = $row["account"];
          }
          if ($accactions === "enable" && $accactionsql === "1") {
            $_SESSION["verif_acc_action"] = "fail1";
            header("Location: welcome_admin_forms.php");
            exit;
          } else if ($accactions === "disable" && $accactionsql === "0") {
            $_SESSION["verif_acc_action"] = "fail2";
            header("Location: welcome_admin_forms.php");
            exit;
          } else if ($accactions === "enable" && $accactionsql === "0") {
            $con -> query("UPDATE blacklist SET account = 1 , deposit = 1 , withdraw = 1 , wire = 1, ticket = 1 WHERE username = '$accuser'");
            if ($con -> affected_rows > 0) {
              $_SESSION["verif_acc_action"] = "success1";
              header("Location: welcome_admin_forms.php");
              exit;
            } else {
              $_SESSION["verif_acc_action"] = "fail7";
              header("Location: welcome_admin_forms.php");
              exit;
            }
          } else if ($accactions === "disable" && $accactionsql === "1") {
            $con -> query("UPDATE blacklist SET account = 0 , deposit = 0 , withdraw = 0 , wire = 0, ticket = 0 WHERE username = '$accuser'");
            if ($con -> affected_rows > 0) {
              $_SESSION["verif_acc_action"] = "success2";
              header("Location: welcome_admin_forms.php");
              exit;
            } else {
              $_SESSION["verif_acc_action"] = "fail7";
              header("Location: welcome_admin_forms.php");
              exit;
            }
          }
        } else {
          $_SESSION["verif_acc_action"] = "fail7";
          header("Location: welcome_admin_forms.php");
          exit;
        }
      } else if (empty($accuser) && !empty($accmail)) {
        $accaction_query = $con -> query("SELECT account FROM blacklist b,login_credentials l WHERE l.username = b.username AND email IN (SELECT email FROM login_credentials WHERE email = '$accmail')");
        if ($accaction_query -> num_rows > 0) {
          $_SESSION['accmail'] = $accmail;
          $accaction_query_array = $accaction_query -> fetch_all(MYSQLI_ASSOC);
          foreach ($accaction_query_array as $row) {
              $accactionsql = $row["account"];
          }
          if ($accactions === "enable" && $accactionsql === "1") {
            $_SESSION["verif_acc_action"] = "fail3";
            header("Location: welcome_admin_forms.php");
            exit;
          } else if ($accactions === "disable" && $accactionsql === "0") {
            $_SESSION["verif_acc_action"] = "fail4";
            header("Location: welcome_admin_forms.php");
            exit;
          } else if ($accactions === "enable" && $accactionsql === "0") {
            $con -> query("UPDATE blacklist b,login_credentials l SET account = 1 , deposit = 1 , withdraw = 1 , wire = 1, ticket = 1 WHERE l.username = b.username AND email IN (SELECT email FROM login_credentials WHERE email = '$accmail')");
            if ($con -> affected_rows > 0) {
              $_SESSION["verif_acc_action"] = "success3";
              header("Location: welcome_admin_forms.php");
              exit;
            } else {
              $_SESSION["verif_acc_action"] = "fail7";
              header("Location: welcome_admin_forms.php");
              exit;
            }
          } else if ($accactions === "disable" && $accactionsql === "1") {
            $con -> query("UPDATE blacklist b,login_credentials l SET account = 0 , deposit = 0 , withdraw = 0 , wire = 0, ticket = 0 WHERE l.username = b.username AND email IN (SELECT email FROM login_credentials WHERE email = '$accmail')");
            if ($con -> affected_rows > 0) {
              $_SESSION["verif_acc_action"] = "success4";
              header("Location: welcome_admin_forms.php");
              exit;
            } else {
              $_SESSION["verif_acc_action"] = "fail7";
              header("Location: welcome_admin_forms.php");
              exit;
            }
          }
        } else {
          $_SESSION["verif_acc_action"] = "fail7";
          header("Location: welcome_admin_forms.php");
          exit;
        }
      } else if (!empty($accuser) && !empty($accmail)) {
        $accaction_query = $con -> query("SELECT account FROM blacklist b,login_credentials l WHERE l.username = b.username AND email IN (SELECT email FROM login_credentials WHERE email = '$accmail') AND b.username = '$accuser'");
        if ($accaction_query -> num_rows > 0) {
          $_SESSION['accuser'] = $accuser;
          $_SESSION['accmail'] = $accmail;
          $accaction_query_array = $accaction_query -> fetch_all(MYSQLI_ASSOC);
          foreach ($accaction_query_array as $row) {
              $accactionsql = $row["account"];
          }
          if ($accactions === "enable" && $accactionsql === "1") {
            $_SESSION["verif_acc_action"] = "fail5";
            header("Location: welcome_admin_forms.php");
            exit;
          } else if ($accactions === "disable" && $accactionsql === "0") {
            $_SESSION["verif_acc_action"] = "fail6";
            header("Location: welcome_admin_forms.php");
            exit;
          } else if ($accactions === "enable" && $accactionsql === "0") {
            $con -> query("UPDATE blacklist b,login_credentials l SET account = 1 , deposit = 1 , withdraw = 1 , wire = 1, ticket = 1 WHERE l.username = b.username AND email IN (SELECT email FROM login_credentials WHERE email = '$accmail') AND b.username = '$accuser'");
            if ($con -> affected_rows > 0) {
              $_SESSION["verif_acc_action"] = "success5";
              header("Location: welcome_admin_forms.php");
              exit;
            } else {
              $_SESSION["verif_acc_action"] = "fail7";
              header("Location: welcome_admin_forms.php");
              exit;
            }
          } else if ($accactions === "disable" && $accactionsql === "1") {
            $con -> query("UPDATE blacklist b,login_credentials l SET account = 0 , deposit = 0 , withdraw = 0 , wire = 0, ticket = 0 WHERE l.username = b.username AND email IN (SELECT email FROM login_credentials WHERE email = '$accmail') AND b.username = '$accuser'");
            if ($con -> affected_rows > 0) {
              $_SESSION["verif_acc_action"] = "success6";
              header("Location: welcome_admin_forms.php");
              exit;
            } else {
              $_SESSION["verif_acc_action"] = "fail7";
              header("Location: welcome_admin_forms.php");
              exit;
            }
          }
        } else {
          $_SESSION["verif_acc_action"] = "fail7";
          header("Location: welcome_admin_forms.php");
          exit;
        }
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
    switch ($_SESSION) {
      case isset($_SESSION["balsub"]):
          unset($_SESSION["verif_balance"]);
          unset($_SESSION["balsub"]);
          break;
      case isset($_SESSION["transdep"]):
          unset($_SESSION["verif_deposit"]);
          unset($_SESSION["transdep"]);
          break;
      case isset($_SESSION["transwith"]):
          unset($_SESSION["verif_withdraw"]);
          unset($_SESSION["transwith"]);
          break;
      case isset($_SESSION["transwire"]):
          unset($_SESSION["verif_wire"]);
          unset($_SESSION["transwire"]);
          break;
      case isset($_SESSION["actiondep"]):
          unset($_SESSION["verif_dep_action"]);
          unset($_SESSION["actiondep"]);
          break;
      case isset($_SESSION["actionwith"]):
          unset($_SESSION["verif_with_action"]);
          unset($_SESSION["actionwith"]);
          break;
      case isset($_SESSION["actionwire"]):
          unset($_SESSION["verif_wire_action"]);
          unset($_SESSION["actionwire"]);
          break;
      case isset($_SESSION["actionticket"]):
          unset($_SESSION["verif_ticket_action"]);
          unset($_SESSION["actionticket"]);
          break;
      case isset($_SESSION["accaccount"]):
          unset($_SESSION["verif_acc_action"]);
          unset($_SESSION["accaccount"]);
          break;
      case isset($_SESSION['accset']):
            unset($_SESSION['verif_id']);
            unset($_SESSION['setting']);
            unset($_SESSION['accset']);
            break;
    }
  }
}
?>
<?php if(isset($page_load) && $page_load === true): ?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">
  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WSYM Banking</title>
    <link rel="shortcut icon" href="./data/favicon.ico" type="image/x-icon">
    <link href="./bootstrap-5.0.2-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/welcome_admin.css">

    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }

      .b-example-divider {
        width: 100%;
        height: 3rem;
        background-color: rgba(0, 0, 0, .1);
        border: solid rgba(0, 0, 0, .15);
        border-width: 1px 0;
        box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
      }

      .b-example-vr {
        flex-shrink: 0;
        width: 1.5rem;
        height: 100vh;
      }

      .bi {
        vertical-align: -.125em;
        fill: currentColor;
      }

      .nav-scroller {
        position: relative;
        z-index: 2;
        height: 2.75rem;
        overflow-y: hidden;
      }

      .nav-scroller .nav {
        display: flex;
        flex-wrap: nowrap;
        padding-bottom: 1rem;
        margin-top: -1px;
        overflow-x: auto;
        text-align: center;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
      }

      .btn-bd-primary {
        --bd-violet-bg: #712cf9;
        --bd-violet-rgb: 112.520718, 44.062154, 249.437846;

        --bs-btn-font-weight: 600;
        --bs-btn-color: var(--bs-white);
        --bs-btn-bg: var(--bd-violet-bg);
        --bs-btn-border-color: var(--bd-violet-bg);
        --bs-btn-hover-color: var(--bs-white);
        --bs-btn-hover-bg: #6528e0;
        --bs-btn-hover-border-color: #6528e0;
        --bs-btn-focus-shadow-rgb: var(--bd-violet-rgb);
        --bs-btn-active-color: var(--bs-btn-hover-color);
        --bs-btn-active-bg: #5a23c8;
        --bs-btn-active-border-color: #5a23c8;
      }

      .bd-mode-toggle {
        z-index: 1500;
      }

      .bd-mode-toggle .dropdown-menu .active .bi {
        display: block !important;
      }
    </style>

    
    <!-- Custom styles for this template -->
    <link href="./css/sidebars.css" rel="stylesheet">
  </head>
  <body>
    
  <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
    <symbol id="wsym" viewBox="0 0 250 200">
      <path d="M0 0 C6.1620587 4.46749256 10.57414782 9.57457701 11.921875 17.19921875 C12.30431277 23.9662427 10.35830718 29.28520498 6.5 34.8125 C1.65261633 40.18868916 -4.5794904 40.97402958 -11.5 41.8125 C-7.12804058 46.35794418 -7.12804058 46.35794418 -1.3125 48.0625 C-0.384375 47.98 0.54375 47.8975 1.5 47.8125 C4.0825839 48.6733613 5.15246301 49.27561528 6.6328125 51.5625 C7.69926565 54.32951357 7.50979978 56.13802169 7.125 59.0625 C7.01414063 59.95453125 6.90328125 60.8465625 6.7890625 61.765625 C6.69367188 62.44109375 6.59828125 63.1165625 6.5 63.8125 C7.4075 63.565 8.315 63.3175 9.25 63.0625 C12.5 62.8125 12.5 62.8125 14.9375 64.375 C16.5 66.8125 16.5 66.8125 16.25 69.9375 C15.87875 71.360625 15.87875 71.360625 15.5 72.8125 C15.5 75.73182117 16.77461989 77.80293277 18 80.4375 C18.4640625 81.44039063 18.928125 82.44328125 19.40625 83.4765625 C19.7671875 84.24742188 20.128125 85.01828125 20.5 85.8125 C16.92987824 84.79246521 14.45636233 83.78043967 11.4375 81.5625 C10.468125 80.985 9.49875 80.4075 8.5 79.8125 C5.79534033 80.65234424 3.38965138 81.86605154 0.84375 83.1015625 C-6.81246228 86.69829959 -13.09745026 87.42236248 -21.5 86.8125 C-21.52940458 89.97914666 -21.54693315 93.14575747 -21.5625 96.3125 C-21.57506836 97.6428125 -21.57506836 97.6428125 -21.58789062 99 C-21.95313503 109.62788135 -21.95313503 109.62788135 -17.75 119 C-6.95711122 134.02183318 -5.34734388 153.97825916 -7.93505859 171.81054688 C-11.98553644 193.52429917 -11.98553644 193.52429917 -18.44140625 198.2265625 C-22.45953595 199.37024268 -26.37497694 199.35403463 -30.5 198.8125 C-33.62325867 197.055667 -34.99010195 195.62370145 -36.875 192.625 C-38.39715624 185.77529691 -36.12825089 178.49939443 -34.5 171.8125 C-35.15742188 172.73869141 -35.15742188 172.73869141 -35.828125 173.68359375 C-42.94863987 183.19208617 -52.95404826 192.13447925 -64.5 195.8125 C-68.72616851 196.37416259 -71.86953705 195.95550496 -75.39453125 193.484375 C-78.48877242 190.38288736 -79.90519619 187.64056713 -80.1875 183.25 C-79.890396 179.52039654 -78.92623898 176.9669027 -76.25 174.20703125 C-73.36322681 171.90663387 -70.19049098 170.13767832 -66.99609375 168.30078125 C-58.0247331 162.95166011 -53.57025415 156.08844985 -50.625 146.125 C-48.15631321 135.11409584 -50.06696697 124.11979219 -54.328125 113.80859375 C-58.38542562 103.96367314 -57.63765518 93.28958847 -57.5 82.8125 C-57.9846875 83.40417969 -58.469375 83.99585937 -58.96875 84.60546875 C-65.7650474 92.6172578 -72.35327272 98.03147157 -83.1484375 99.06640625 C-94.5983948 99.53221816 -105.7535905 96.40854666 -114.5 88.8125 C-120.23363402 82.952078 -121.53006465 76.04485939 -121.75 68.0625 C-121.36678584 53.50036181 -117.99389987 39.44959149 -107.5 28.8125 C-96.08646719 18.9793025 -81.75067772 15.33763373 -66.890625 15.96484375 C-54.06105038 17.48871856 -42.04557407 22.17173096 -30.5 27.8125 C-30.995 26.3275 -30.995 26.3275 -31.5 24.8125 C-32.48340813 17.15596531 -30.98392844 11.11907267 -26.5 4.8125 C-19.29306089 -2.06326685 -9.28237435 -3.14937701 0 0 Z M-76.5 36.8125 C-76.87125 37.451875 -77.2425 38.09125 -77.625 38.75 C-80.26066053 41.64922658 -82.49893403 41.27414826 -86.2734375 41.55078125 C-89.33684151 41.91086558 -91.1838307 42.78585187 -93.5 44.8125 C-94.93077145 47.04574267 -94.93077145 47.04574267 -94.5 49.8125 C-92.3677675 53.03818506 -89.99274861 56.0661257 -86.5 57.8125 C-86.5 62.4325 -86.5 67.0525 -86.5 71.8125 C-89.46223958 70.54296875 -92.42447917 69.2734375 -95.38671875 68.00390625 C-98.11972529 66.95802545 -100.62042337 66.27729252 -103.5 65.8125 C-102.51206088 68.31913276 -101.8573248 69.58738538 -99.546875 71.04296875 C-95.19926986 72.85447089 -91.23419431 74.32694161 -86.5 74.8125 C-86.17 76.4625 -85.84 78.1125 -85.5 79.8125 C-83.52 79.3175 -83.52 79.3175 -81.5 78.8125 C-81.5 77.4925 -81.5 76.1725 -81.5 74.8125 C-79.953125 74.7196875 -79.953125 74.7196875 -78.375 74.625 C-73.61537173 73.62701343 -71.96407037 72.10759133 -68.5 68.8125 C-68.81002146 65.61982289 -69.07146535 64.19910367 -71.49609375 62.01171875 C-72.30175781 61.51285156 -73.10742188 61.01398437 -73.9375 60.5 C-75.14212891 59.73236328 -75.14212891 59.73236328 -76.37109375 58.94921875 C-78.39181403 57.65549726 -78.39181403 57.65549726 -80.5 57.8125 C-79.84 53.8525 -79.18 49.8925 -78.5 45.8125 C-74.5 47.8125 -74.5 47.8125 -73.5 49.8125 C-72.18 49.8125 -70.86 49.8125 -69.5 49.8125 C-69.84879632 46.55706771 -70.52133261 45.79376893 -73.0625 43.5625 C-73.866875 42.985 -74.67125 42.4075 -75.5 41.8125 C-74.84 40.1625 -74.18 38.5125 -73.5 36.8125 C-74.49 36.8125 -75.48 36.8125 -76.5 36.8125 Z M-41.5 47.8125 C-41.5 51.4425 -41.5 55.0725 -41.5 58.8125 C-41.17 58.8125 -40.84 58.8125 -40.5 58.8125 C-40.5 55.1825 -40.5 51.5525 -40.5 47.8125 C-40.83 47.8125 -41.16 47.8125 -41.5 47.8125 Z M-28.5 49.8125 C-28.5 52.1225 -28.5 54.4325 -28.5 56.8125 C-28.17 56.8125 -27.84 56.8125 -27.5 56.8125 C-27.5 54.5025 -27.5 52.1925 -27.5 49.8125 C-27.83 49.8125 -28.16 49.8125 -28.5 49.8125 Z M-27.5 57.8125 C-26.5 61.8125 -26.5 61.8125 -26.5 61.8125 Z M-40.5 59.8125 C-39.5 63.8125 -39.5 63.8125 -39.5 63.8125 Z M-26.5 62.8125 C-25.5 64.8125 -25.5 64.8125 -25.5 64.8125 Z M-39.5 64.8125 C-38.5 66.8125 -38.5 66.8125 -38.5 66.8125 Z M-25.5 64.8125 C-24.5 66.8125 -24.5 66.8125 -24.5 66.8125 Z M-38.5 67.8125 C-37.5 69.8125 -37.5 69.8125 -37.5 69.8125 Z M-17.5 73.8125 C-17.5 74.1425 -17.5 74.4725 -17.5 74.8125 C-15.52 74.8125 -13.54 74.8125 -11.5 74.8125 C-11.5 74.4825 -11.5 74.1525 -11.5 73.8125 C-13.48 73.8125 -15.46 73.8125 -17.5 73.8125 Z M-34.5 75.8125 C-33.5 77.8125 -33.5 77.8125 -33.5 77.8125 Z " fill="#818181" transform="translate(150.5,1.1875)"/>
    <path d="M0 0 C3.42430894 1.23655601 6.40112619 2.40112619 9 5 C9.4375 7.5625 9.4375 7.5625 9 10 C5.3558226 12.4294516 3.28758728 12.16179575 -1 12 C-1.02712066 10.56260487 -1.04645067 9.12506137 -1.0625 7.6875 C-1.07410156 6.88699219 -1.08570312 6.08648437 -1.09765625 5.26171875 C-1 3 -1 3 0 0 Z " fill="#858585" transform="translate(69,62)"/>
    <path d="M0 0 C1.134375 0.020625 2.26875 0.04125 3.4375 0.0625 C2.7775 3.0325 2.1175 6.0025 1.4375 9.0625 C0.1175 9.0625 -1.2025 9.0625 -2.5625 9.0625 C-4.71059171 5.84036243 -4.85636153 4.78474599 -4.5625 1.0625 C-3.5625 0.0625 -3.5625 0.0625 0 0 Z " fill="#818181" transform="translate(65.5625,44.9375)"/>
    </symbol>
    <symbol id="home" viewBox="0 0 16 16">
      <path d="M8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4.5a.5.5 0 0 0 .5-.5v-4h2v4a.5.5 0 0 0 .5.5H14a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146zM2.5 14V7.707l5.5-5.5 5.5 5.5V14H10v-4a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5v4H2.5z"/>
    </symbol>
    <symbol id="dollar" viewBox="0 0 16 16">
    <path d="M4 10.781c.148 1.667 1.513 2.85 3.591 3.003V15h1.043v-1.216c2.27-.179 3.678-1.438 3.678-3.3 0-1.59-.947-2.51-2.956-3.028l-.722-.187V3.467c1.122.11 1.879.714 2.07 1.616h1.47c-.166-1.6-1.54-2.748-3.54-2.875V1H7.591v1.233c-1.939.23-3.27 1.472-3.27 3.156 0 1.454.966 2.483 2.661 2.917l.61.162v4.031c-1.149-.17-1.94-.8-2.131-1.718H4zm3.391-3.836c-1.043-.263-1.6-.825-1.6-1.616 0-.944.704-1.641 1.8-1.828v3.495l-.2-.05zm1.591 1.872c1.287.323 1.852.859 1.852 1.769 0 1.097-.826 1.828-2.2 1.939V8.73l.348.086z"/>
    </symbol>
    <symbol id="bank" viewBox="0 0 16 16">
      <path d="m8 0 6.61 3h.89a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5H15v7a.5.5 0 0 1 .485.38l.5 2a.498.498 0 0 1-.485.62H.5a.498.498 0 0 1-.485-.62l.5-2A.501.501 0 0 1 1 13V6H.5a.5.5 0 0 1-.5-.5v-2A.5.5 0 0 1 .5 3h.89L8 0ZM3.777 3h8.447L8 1 3.777 3ZM2 6v7h1V6H2Zm2 0v7h2.5V6H4Zm3.5 0v7h1V6h-1Zm2 0v7H12V6H9.5ZM13 6v7h1V6h-1Zm2-1V4H1v1h14Zm-.39 9H1.39l-.25 1h13.72l-.25-1Z"/>
    </symbol>
    <symbol id="info" viewBox="0 0 16 16">
      <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
      <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
    </symbol>
    <symbol id="stop" viewBox="0 0 16 16">
      <path d="M3.16 10.08c-.931 0-1.447-.493-1.494-1.132h.653c.065.346.396.583.891.583.524 0 .83-.246.83-.62 0-.303-.203-.467-.637-.572l-.656-.164c-.61-.147-.978-.51-.978-1.078 0-.706.597-1.184 1.444-1.184.853 0 1.386.475 1.436 1.087h-.645c-.064-.32-.352-.542-.797-.542-.472 0-.77.246-.77.6 0 .261.196.437.553.522l.654.161c.673.164 1.06.487 1.06 1.11 0 .736-.574 1.228-1.544 1.228Zm3.427-3.51V10h-.665V6.57H4.753V6h3.006v.568H6.587Z"/>
      <path fill-rule="evenodd" d="M11.045 7.73v.544c0 1.131-.636 1.805-1.661 1.805-1.026 0-1.664-.674-1.664-1.805V7.73c0-1.136.638-1.807 1.664-1.807 1.025 0 1.66.674 1.66 1.807Zm-.674.547v-.553c0-.827-.422-1.234-.987-1.234-.572 0-.99.407-.99 1.234v.553c0 .83.418 1.237.99 1.237.565 0 .987-.408.987-1.237Zm1.15-2.276h1.535c.82 0 1.316.55 1.316 1.292 0 .747-.501 1.289-1.321 1.289h-.865V10h-.665V6.001Zm1.436 2.036c.463 0 .735-.272.735-.744s-.272-.741-.735-.741h-.774v1.485h.774Z"/>
      <path fill-rule="evenodd" d="M4.893 0a.5.5 0 0 0-.353.146L.146 4.54A.5.5 0 0 0 0 4.893v6.214a.5.5 0 0 0 .146.353l4.394 4.394a.5.5 0 0 0 .353.146h6.214a.5.5 0 0 0 .353-.146l4.394-4.394a.5.5 0 0 0 .146-.353V4.893a.5.5 0 0 0-.146-.353L11.46.146A.5.5 0 0 0 11.107 0H4.893ZM1 5.1 5.1 1h5.8L15 5.1v5.8L10.9 15H5.1L1 10.9V5.1Z"/>    
    </symbol>
    <symbol id="user" viewBox="0 0 16 16">
      <path d="M6.5 2a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1zM11 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
      <path d="M4.5 0A2.5 2.5 0 0 0 2 2.5V14a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2.5A2.5 2.5 0 0 0 11.5 0zM3 2.5A1.5 1.5 0 0 1 4.5 1h7A1.5 1.5 0 0 1 13 2.5v10.795a4.2 4.2 0 0 0-.776-.492C11.392 12.387 10.063 12 8 12s-3.392.387-4.224.803a4.2 4.2 0 0 0-.776.492z"/>
    </symbol>
    <symbol id="ticket" viewBox="0 0 16 16">
      <path d="M0 4.5A1.5 1.5 0 0 1 1.5 3h13A1.5 1.5 0 0 1 16 4.5V6a.5.5 0 0 1-.5.5 1.5 1.5 0 0 0 0 3 .5.5 0 0 1 .5.5v1.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 11.5V10a.5.5 0 0 1 .5-.5 1.5 1.5 0 1 0 0-3A.5.5 0 0 1 0 6V4.5ZM1.5 4a.5.5 0 0 0-.5.5v1.05a2.5 2.5 0 0 1 0 4.9v1.05a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-1.05a2.5 2.5 0 0 1 0-4.9V4.5a.5.5 0 0 0-.5-.5h-13Z"/>
    </symbol>

  </svg>


<main class="d-flex flex-nowrap">
  <div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary containerdiv" style="width: 280px;">
    <a href="" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
      <svg class="bi pe-none me-2" width="40" height="32"><use xlink:href="#wsym"/></svg>
      <span class="fs-4">WSYM Banking</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
      <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="pill" href="#homesec">
          <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#home"/></svg>
          Home
        </a>
      </li>
      <li class="nav-item">
        <a data-bs-toggle="pill" class="nav-link link-body-emphasis" href="#balance">
          <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#dollar"/></svg>
          Balances
        </a>
      </li>
      <li class="nav-item">
        <a data-bs-toggle="pill" class="nav-link link-body-emphasis" href="#transactions">
          <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#bank"/></svg>
          Transactions
        </a>
      </li>
      <?php if ($admin_type === "superadmin"): ?>
      <li class="nav-item">
        <a class="nav-link link-body-emphasis" data-bs-toggle="pill" href="#blockactions">
          <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#stop"/></svg>
          Actions Blocking
        </a>
      </li>
      <?php endif; ?>
      <?php if ($admin_type === "superadmin"): ?>
      <li class="nav-item">
        <a class="nav-link link-body-emphasis" data-bs-toggle="pill" href="#blockaccount">
          <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#user"/></svg>
          Accounts Blocking
        </a>
      </li>
      <?php endif; ?>
      <?php if ($admin_type === "superadmin"): ?>
      <li class="nav-item">
        <a data-bs-toggle="pill" class="nav-link link-body-emphasis" href="#infomanagment">
          <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#info"/></svg>
          Info managment
        </a>
      </li>
      <?php endif; ?>
      <li class="nav-item">
        <a data-bs-toggle="pill" class="nav-link link-body-emphasis" href="#tickets">
          <svg class="bi pe-none me-2" width="16" height="16"><use xlink:href="#ticket"/></svg>
          Tickets
        </a>
      </li>
    </ul>
    <hr>
    <div class="dropdown">
      <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded=""fail"">
        <img src="./data/uploads/<?php echo $pfp ?>" alt="pfp" width="32" height="32" class="rounded-circle me-2" id="output">
        <strong><?php echo "$admin_username"; ?></strong>
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
        <li><a href="admin_redirections.php" class="dropdown-item">Go Back to Redirections</a></li>
        <li><form method="post"><input class="dropdown-item" name="logout" type="submit" value="Sign Out" onclick="return logoutconfirm()"></form></li>
      </ul>
    </div>
  </div>
  <div class="tab-content container-fluid d-flex justify-content-center align-items-center">
    <div class="tab-pane active" id="homesec">
      <div class="homeseccont d-flex flex-column justify-content-center align-items-center">
        <h1 class="h2 mb-3 text-center">Welcome Back</h1>
        <div class="mt-auto mb-auto">
          <span class="push">Clients Number : <?php echo "$clients_number"; ?></span>
          <span class="push">Stored Money : $<?php echo "$stored_money"; ?></span>
        </div>
        <div class="" style="font-size: 15px;"><?php echo "$today_deposits_msg"; ?></div>
        <div class="" style="font-size: 15px;"><?php echo "$today_withdraws_msg"; ?></div>
        <div class="mb-3" style="font-size: 15px;"><?php echo "$today_wires_msg"; ?></div>
      </div>
    </div>
    <div class="tab-pane fade" id="balance">
      <form method="post" class="balancecont d-flex flex-column justify-content-center align-items-center">
        <h1>Check/Edit Balances</h1>
        <div class="desc text-center" style="font-size: 15px;">Insert the email or the username of the person you want to check/edit the money of (Information doesn't have to be percise) <br> (at least one of the inputs needs to be filled)</div>
        <div class="mt-auto mb-auto d-flex flex-column justify-content-center align-items-center">
          <label class="labbor lab">
              <img src="./data/mail.svg" alt="">
              <input type="text" placeholder="Email without (@example.ex)" id="mail" name="balanceemail">
          </label>
          <label class="labbor lab">
            <img src="./data/user.svg" alt="">
            <input type="text" placeholder="Username" id="user" name="balanceusername">
          </label>
        </div>
        <input type="submit" value="Search" class="but" name="balance_submit" onclick="return verifsub()">
      </form>
    </div>
    <div class="tab-pane fade" id="transactions">
      <form method="post" class="transactionscont d-flex flex-column justify-content-center align-items-center" style="height: 450px;">
      <h1>Check Transactions</h1>
        <div class="desc text-center" style="font-size: 15px;">Insert the email or the username of the person you want to check the transactions of (Information doesn't have to be percise) <br> (at least one of the email/username inputs needs to be filled)</div>
        <div class="mt-auto mb-auto d-flex flex-column justify-content-center align-items-center">
          <label class="labbor lab">
              <img src="./data/mail.svg" alt="">
              <input type="text" placeholder="Email without (@example.ex)" id="mail2" name="transemail">
          </label>
          <label class="labbor lab">
            <img src="./data/user.svg" alt="">
            <input type="text" placeholder="Username" id="user2" name="transusername">
          </label>
        </div>
        <div class="d-flex gap-4 mt-2 mb-3">
            <input type="submit" class="but text-center t-but" id="but" value="Deposit Search" name="deposit_transactions_submit" onclick="return verifsub2()">
            <input type="submit" class="but text-center t-but" id="but" value="Withdraw Search" name="withdraw_transactions_submit" onclick="return verifsub2()">
            <input type="submit" class="but text-center t-but" id="but" value="Wire Search" name="wire_transactions_submit" onclick="return verifsub2()">
        </div>
      </form>
    </div>
    <div class="tab-pane fade" id="blockactions">
      <form method="post" class="blockactionscont d-flex flex-column justify-content-center align-items-center">
        <h1>Allow / Block Actions</h1>
        <div class="desc text-center" style="font-size: 15px;">Insert the email or the username of the person you want to block/allow the actions of (Information MUST be percise!) <br> (at least one of the inputs needs to be filled)</div>
        <div class="mt-auto mb-auto d-flex flex-column justify-content-center align-items-center">
          <label class="labbor lab">
              <img src="./data/mail.svg" alt="">
              <input type="text" placeholder="Exact Email" id="mailb" name="blockemail">
          </label>
          <label class="labbor lab">
            <img src="./data/user.svg" alt="">
            <input type="text" placeholder="Exact Username" id="userb" name="blockusername">
          </label>
        </div>
        <div class="mt-auto mb-auto d-flex gap-3">
          <label>
              <input type="radio" name="actions" id="" value="allow" required>
              <span style="color: white;">Allow</span>
          </label>
          <label>
              <input type="radio" name="actions" id="" value="block" required>
              <span style="color: white;">Block</span>
          </label>
        </div>
        <style>
          .x {
            width: 180px;
          }
          .xx {
            width: 200px;
          }
        </style>
        <div class="d-flex gap-2 mb-2 mt-2">
          <input type="submit" value="Deposit B/A" class="but x" name="block_dep_submit" onclick="return verifsub4()">
          <input type="submit" value="Withdraw B/A" class="but x" name="block_with_submit" onclick="return verifsub4()">
        </div>
        <div class="d-flex gap-2 mb-2">
          <input type="submit" value="Wire B/A" class="but xx" name="block_wire_submit" onclick="return verifsub4()">
          <input type="submit" value="Ticket B/A" class="but xx" name="block_ticket_submit" onclick="return verifsub4()">
        </div>
      </form>
    </div>
    <div class="tab-pane fade" id="blockaccount">
      <form method="post" class="blockaccountcont d-flex flex-column justify-content-center align-items-center">
        <h1>Disable / Enable account</h1>
        <div class="desc text-center" style="font-size: 15px;">Insert the email or the username of the person you want to disable/enable the account of (Information MUST be percise!) <br> (at least one of the inputs needs to be filled)</div>
        <div class="mt-auto mb-auto d-flex flex-column justify-content-center align-items-center">
          <label class="labbor lab">
              <img src="./data/mail.svg" alt="">
              <input type="text" placeholder="Exact Email" id="mailbacc" name="accmail">
          </label>
          <label class="labbor lab">
            <img src="./data/user.svg" alt="">
            <input type="text" placeholder="Exact Username" id="userbacc" name="accuser">
          </label>
        </div>
        <div class="mt-auto mb-auto d-flex gap-3">
          <label>
              <input type="radio" name="accactions" id="" value="enable" required>
              <span style="color: white;">Enable</span>
          </label>
          <label>
              <input type="radio" name="accactions" id="" value="disable" required>
              <span style="color: white;">Disable</span>
          </label>
        </div>
        <style>
          .x2 {
            width: 300px;
          }
        </style>
        <div class="d-flex gap-3 mb-2">
          <input type="submit" value="Account D/E" class="but x2" name="accsub" onclick="return verifsub5()">
        </div>
      </form>
    </div>
    <div class="tab-pane fade" id="infomanagment">
      <form class="infomanagmentcont d-flex flex-column justify-content-center align-items-center">

      </form>
    </div>
    <div class="tab-pane fade" id="tickets">
      <form class="ticketscont d-flex flex-column justify-content-center align-items-center">

      </form>
    </div>
  </div>
  </main>
    <script src="./bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
    <script src="./js/welcome_admin.js"></script>
    <script src="./js/sidebars.js"></script>
  </body>
</html>
<?php endif; ?>