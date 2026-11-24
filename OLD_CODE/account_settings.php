<?php
require ("linking.php");
if ($con->connect_error) {
    die("Connection Failed" . $con->connect_error);
} else {
    session_start();
    if (!isset($_SESSION['setting']) && !isset($_SESSION['verif_id'])) {
        if (empty($_SESSION['setting']) || $_SESSION['setting'] === "" && empty($_SESSION['verif_id']) || $_SESSION['verif_id'] === "") {
            if (isset($_SESSION['admin_username'])) {
                header("Location: admin_redirections.php");
                exit;
            } else if (isset($_SESSION['user_username'])) {
                header("Location: welcome.php");
                exit;
            } else {
                session_destroy();
                header("Location: index.php");
                exit;
            }
        } 
    }
    if (isset($_SESSION['user_username'])) {
        $user_username = $_SESSION['user_username'];
        echo "<script>var origuser = '$user_username'</script>";
    }
    if (isset($_SESSION['verif_id'])) {
        $verif_id = $_SESSION['verif_id'];
        if (isset($_POST['id_sub']) && $verif_id === false) {
            $iduser = filter_input(INPUT_POST,"id_user",FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $idmail = filter_input(INPUT_POST,"id_mail",FILTER_SANITIZE_EMAIL);
            $idpass = filter_input(INPUT_POST,"id_pass",FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $idquery = $con -> prepare("SELECT pass,email FROM login_credentials WHERE username = ?");
            $idquery -> bind_param("s", $user_username);
            $idquery -> execute();
            $idquery_result = $idquery -> get_result();
            if ($idquery_result -> num_rows > 0) {
                $idarray = $idquery_result -> fetch_all(MYSQLI_ASSOC);
                foreach ($idarray as $row) {
                    $idpassdb = $row['pass'];
                    $idmaildb = $row['email'];
                }
            }
            if ($iduser === $user_username && $idmail === $idmaildb && password_verify($idpass,$idpassdb)) {
                $verif_id = true;
                $_SESSION['verif_id'] = true;
            } else {
                echo "<script>alert('Error : Check the info provided !')</script>";
            }
        }
    }
    if (isset($_SESSION["setting"])) {
        $setting = $_SESSION["setting"];
        if (isset($_POST["sub_uuser"])) {
            $uuser = filter_input(INPUT_POST,"uuser",FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $cuuser = filter_input(INPUT_POST,"cuuser",FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $userchange = $con -> prepare("UPDATE login_credentials set username = ? WHERE username = ?");
            $userchange -> bind_param("ss", $cuuser, $user_username);
            if ($cuuser === $uuser && $userchange -> execute()) {
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
                            <h1 style="text-align: center;">Session terminated : You need to login again !</h1>
                            <div style="text-align: center; font-size: medium;">Username Changed : For security reasons , please login with your new username</div>
                            <div style="text-align: center; font-size: small;">You will be automatically redirected back to the login page in 4 seconds.</div>
                        </div>
                    </div>
                </body>
                </html>
            <?php die(); } else { ?>
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
            <?php die(); }
        }
        if (isset($_POST["sub_mail"])) { 
            $mailx = filter_input(INPUT_POST,"mail",FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $cmail = filter_input(INPUT_POST,"cmail",FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $origmail_query = $con -> prepare("SELECT email FROM login_credentials WHERE username = ?");
            $origmail_query -> bind_param("s", $user_username);
            $origmail_query -> execute();
            $origmail_result = $origmail_query -> get_result();
            if ($origmail_result -> num_rows > 0) {
                $origmail_array = $origmail_result -> fetch_all(MYSQLI_ASSOC);
                foreach ($origmail_array as $row) {
                    $origmail = $row["email"];
                    echo "<script>var origmail = '$origmail'</script>";
                }
            }
            $emailchange = $con -> prepare("UPDATE login_credentials SET email_verif = 0 , email_verif_token = ?, email_verif_expire = ?, email = ? WHERE email = ?");
            $emailchange -> bind_param("ssss", $token_hash, $token_expire, $cmail, $origmail);
            $token = bin2hex(random_bytes(16));
            $token_hash = hash("sha256",$token);
            $token_expire = date("Y-m-d H:i:s", time() + 60 * 30);
            require("mailer.php");
            $mail->setFrom($_ENV["email_verif_address"]);
            $mail->addAddress($cmail);
            $mail->Subject = "Email Verification";
            $mail->Body = <<<END
            <!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
            <head>
            <!--[if gte mso 9]>
            <xml>
            <o:OfficeDocumentSettings>
                <o:AllowPNG/>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
            </xml>
            <![endif]-->
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta name="x-apple-disable-message-reformatting">
            <!--[if !mso]><!--><meta http-equiv="X-UA-Compatible" content="IE=edge"><!--<![endif]-->
            <title></title>

                <style type="text/css">
                @media only screen and (min-width: 620px) {
            .u-row {
                width: 600px !important;
            }
            .u-row .u-col {
                vertical-align: top;
            }

            .u-row .u-col-47p83 {
                width: 286.98px !important;
            }

            .u-row .u-col-52p17 {
                width: 313.02px !important;
            }

            .u-row .u-col-100 {
                width: 600px !important;
            }

            }

            @media (max-width: 620px) {
            .u-row-container {
                max-width: 100% !important;
                padding-left: 0px !important;
                padding-right: 0px !important;
            }
            .u-row .u-col {
                min-width: 320px !important;
                max-width: 100% !important;
                display: block !important;
            }
            .u-row {
                width: 100% !important;
            }
            .u-col {
                width: 100% !important;
            }
            .u-col > div {
                margin: 0 auto;
            }
            }
            body {
            margin: 0;
            padding: 0;
            }

            table,
            tr,
            td {
            vertical-align: top;
            border-collapse: collapse;
            }

            p {
            margin: 0;
            }

            .ie-container table,
            .mso-container table {
            table-layout: fixed;
            }

            * {
            line-height: inherit;
            }

            a[x-apple-data-detectors='true'] {
            color: inherit !important;
            text-decoration: none !important;
            }

            table, td { color: #000000; } #u_body a { color: #161a39; text-decoration: underline; }
                </style>



            <!--[if !mso]><!--><link href="https://fonts.googleapis.com/css?family=Lato:400,700&display=swap" rel="stylesheet" type="text/css"><link href="https://fonts.googleapis.com/css?family=Lato:400,700&display=swap" rel="stylesheet" type="text/css"><!--<![endif]-->

            </head>

            <body class="clean-body u_body" style="margin: 0;padding: 0;-webkit-text-size-adjust: 100%;background-color: #f9f9f9;color: #000000">
            <!--[if IE]><div class="ie-container"><![endif]-->
            <!--[if mso]><div class="mso-container"><![endif]-->
            <table id="u_body" style="border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;min-width: 320px;Margin: 0 auto;background-color: #f9f9f9;width:100%" cellpadding="0" cellspacing="0">
            <tbody>
            <tr style="vertical-align: top">
                <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top">
                <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td align="center" style="background-color: #f9f9f9;"><![endif]-->
                


            <div class="u-row-container" style="padding: 0px;background-color: #f9f9f9">
            <div class="u-row" style="margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #f9f9f9;">
                <div style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">
                <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding: 0px;background-color: #f9f9f9;" align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px;"><tr style="background-color: #f9f9f9;"><![endif]-->
                
            <!--[if (mso)|(IE)]><td align="center" width="600" style="width: 600px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;" valign="top"><![endif]-->
            <div class="u-col u-col-100" style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
            <div style="height: 100%;width: 100% !important;">
            <!--[if (!mso)&(!IE)]><!--><div style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;"><!--<![endif]-->

            <table style="font-family:'Lato',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
            <tbody>
                <tr>
                <td style="overflow-wrap:break-word;word-break:break-word;padding:15px;font-family:'Lato',sans-serif;" align="left">
                    
            <table height="0px" align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;border-top: 1px solid #f9f9f9;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%">
                <tbody>
                <tr style="vertical-align: top">
                    <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top;font-size: 0px;line-height: 0px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%">
                    <span>&#160;</span>
                    </td>
                </tr>
                </tbody>
            </table>

                </td>
                </tr>
            </tbody>
            </table>

            <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
            </div>
            </div>
            <!--[if (mso)|(IE)]></td><![endif]-->
                <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                </div>
            </div>
            </div>





            <div class="u-row-container" style="padding: 0px;background-color: transparent">
            <div class="u-row" style="margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #161a39;">
                <div style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">
                <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding: 0px;background-color: transparent;" align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px;"><tr style="background-color: #161a39;"><![endif]-->
                
            <!--[if (mso)|(IE)]><td align="center" width="600" style="width: 600px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;" valign="top"><![endif]-->
            <div class="u-col u-col-100" style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
            <div style="height: 100%;width: 100% !important;">
            <!--[if (!mso)&(!IE)]><!--><div style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;"><!--<![endif]-->

            <table style="font-family:'Lato',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
            <tbody>
                <tr>
                <td style="overflow-wrap:break-word;word-break:break-word;padding:35px 10px 10px;font-family:'Lato',sans-serif;" align="left">
                </td>
                </tr>
            </tbody>
            </table>

            <table style="font-family:'Lato',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
            <tbody>
                <tr>
                <td style="overflow-wrap:break-word;word-break:break-word;padding:0px 10px 30px;font-family:'Lato',sans-serif;" align="left">
                    
            <div style="font-size: 14px; line-height: 140%; text-align: left; word-wrap: break-word;">
                <p style="font-size: 14px; line-height: 140%; text-align: center;"><span style="font-size: 28px; line-height: 39.2px; color: #ffffff; font-family: Lato, sans-serif;">Please verify your email </span></p>
            </div>

                </td>
                </tr>
            </tbody>
            </table>

            <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
            </div>
            </div>
            <!--[if (mso)|(IE)]></td><![endif]-->
                <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                </div>
            </div>
            </div>





            <div class="u-row-container" style="padding: 0px;background-color: transparent">
            <div class="u-row" style="margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #ffffff;">
                <div style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">
                <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding: 0px;background-color: transparent;" align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px;"><tr style="background-color: #ffffff;"><![endif]-->
                
            <!--[if (mso)|(IE)]><td align="center" width="600" style="width: 600px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;" valign="top"><![endif]-->
            <div class="u-col u-col-100" style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
            <div style="height: 100%;width: 100% !important;">
            <!--[if (!mso)&(!IE)]><!--><div style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;"><!--<![endif]-->

            <table style="font-family:'Lato',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
            <tbody>
                <tr>
                <td style="overflow-wrap:break-word;word-break:break-word;padding:40px 40px 30px;font-family:'Lato',sans-serif;" align="left">
                    
            <div style="font-size: 14px; line-height: 140%; text-align: left; word-wrap: break-word;">
                <p style="font-size: 14px; line-height: 140%;"><span style="font-size: 18px; line-height: 25.2px; color: #666666;">Hello,</span></p>
            <p style="font-size: 14px; line-height: 140%;"> </p>
            <p style="font-size: 14px; line-height: 140%;"><span style="font-size: 18px; line-height: 25.2px; color: #666666;">We have sent you this email in response to verify your email on WSYM Corporation.</span></p>
            <p style="font-size: 14px; line-height: 140%;"> </p>
            <p style="font-size: 14px; line-height: 140%;"><span style="font-size: 18px; line-height: 25.2px; color: #666666;">To verify your email, please follow the link below: </span></p>
            </div>

                </td>
                </tr>
            </tbody>
            </table>

            <table style="font-family:'Lato',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
            <tbody>
                <tr>
                <td style="overflow-wrap:break-word;word-break:break-word;padding:0px 40px;font-family:'Lato',sans-serif;" align="left">
                    
            <!--[if mso]><style>.v-button {background: transparent !important;}</style><![endif]-->
            <div align="left">
            <!--[if mso]><v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="http://localhost/work/WSYM_work/email_verif_process.php?token=$token" style="height:52px; v-text-anchor:middle; width:205px;" arcsize="2%"  stroke="f" fillcolor="#18163a"><w:anchorlock/><center style="color:#FFFFFF;"><![endif]-->
                <a href="http://localhost/login_panel/email_verif_process.php?token=$token" target="_blank" class="v-button" style="box-sizing: border-box;display: inline-block;text-decoration: none;-webkit-text-size-adjust: none;text-align: center;color: #FFFFFF; background-color: #18163a; border-radius: 1px;-webkit-border-radius: 1px; -moz-border-radius: 1px; width:auto; max-width:100%; overflow-wrap: break-word; word-break: break-word; word-wrap:break-word; mso-border-alt: none;font-size: 14px;">
                <span style="display:block;padding:15px 40px;line-height:120%;"><span style="font-size: 18px; line-height: 21.6px;">Verify Email</span></span>
                </a>
                <!--[if mso]></center></v:roundrect><![endif]-->
            </div>

                </td>
                </tr>
            </tbody>
            </table>

            <table style="font-family:'Lato',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
            <tbody>
                <tr>
                <td style="overflow-wrap:break-word;word-break:break-word;padding:40px 40px 30px;font-family:'Lato',sans-serif;" align="left">
                    
            <div style="font-size: 14px; line-height: 140%; text-align: left; word-wrap: break-word;">
                <p style="font-size: 14px; line-height: 140%;"><span style="color: #888888; font-size: 14px; line-height: 19.6px;"><em><span style="font-size: 16px; line-height: 22.4px;"><span style="color: #888888; font-size: 14px; line-height: 19.6px;"><span style="font-size: 16px; line-height: 22.4px;">If  you feel that someone has accessed your account without your knowledge please do not hesitate to contact us.</span></span></span></em></span></p>
                <p style="font-size: 14px; line-height: 140%;"><span style="color: #888888; font-size: 14px; line-height: 19.6px;"><em><span style="font-size: 16px; line-height: 22.4px;">Please ignore this email if you did not request an email verification.</span></em></span><br /><span style="color: #888888; font-size: 14px; line-height: 19.6px;"><em><span style="font-size: 16px; line-height: 22.4px;"> </span></em></span></p>
                <p style="font-size: 14px; line-height: 140%;"><span style="color: #888888; font-size: 14px; line-height: 19.6px;"><em><span style="font-size: 16px; line-height: 22.4px;">This request is going to be expired in 30 minutes.</span></em></span><br /><span style="color: #888888; font-size: 14px; line-height: 19.6px;"><em><span style="font-size: 16px; line-height: 22.4px;"> </span></em></span></p>
            </div>

                </td>
                </tr>
            </tbody>
            </table>

            <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
            </div>
            </div>
            <!--[if (mso)|(IE)]></td><![endif]-->
                <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                </div>
            </div>
            </div>





            <div class="u-row-container" style="padding: 0px;background-color: transparent">
            <div class="u-row" style="margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #18163a;">
                <div style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">
                <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding: 0px;background-color: transparent;" align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px;"><tr style="background-color: #18163a;"><![endif]-->
                
            <!--[if (mso)|(IE)]><td align="center" width="313" style="width: 313px;padding: 20px 20px 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;" valign="top"><![endif]-->
            <div class="u-col u-col-52p17" style="max-width: 320px;min-width: 313.02px;display: table-cell;vertical-align: top;">
            <div style="height: 100%;width: 100% !important;">
            <!--[if (!mso)&(!IE)]><!--><div style="box-sizing: border-box; height: 100%; padding: 20px 20px 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;"><!--<![endif]-->

            <table style="font-family:'Lato',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
            <tbody>
                <tr>
                <td style="overflow-wrap:break-word;word-break:break-word;padding:5px 10px 10px;font-family:'Lato',sans-serif;" align="left">
                    
            <div style="font-size: 14px; line-height: 140%; text-align: left; word-wrap: break-word;">
                <p style="line-height: 140%; font-size: 14px;"><span style="font-size: 14px; line-height: 19.6px;"><span style="color: #ecf0f1; font-size: 14px; line-height: 19.6px;"><span style="line-height: 19.6px; font-size: 14px;">WSYM ©  All Rights Reserved</span></span></span></p>
            </div>

                </td>
                </tr>
            </tbody>
            </table>

            <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
            </div>
            </div>
            <!--[if (mso)|(IE)]></td><![endif]-->
            <!--[if (mso)|(IE)]><td align="center" width="286" style="width: 286px;padding: 0px 0px 0px 20px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;" valign="top"><![endif]-->
            <div class="u-col u-col-47p83" style="max-width: 320px;min-width: 286.98px;display: table-cell;vertical-align: top;">
            <div style="height: 100%;width: 100% !important;">
            <!--[if (!mso)&(!IE)]><!--><div style="box-sizing: border-box; height: 100%; padding: 0px 0px 0px 20px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;"><!--<![endif]-->

            <table style="font-family:'Lato',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
            <tbody>
                <tr>
                <td style="overflow-wrap:break-word;word-break:break-word;padding:25px 15px 10px 10px;font-family:'Lato',sans-serif;" align="left">
                    


                </td>
                </tr>
            </tbody>
            </table>

            <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
            </div>
            </div>
            <!--[if (mso)|(IE)]></td><![endif]-->
                <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                </div>
            </div>
            </div>





            <div class="u-row-container" style="padding: 0px;background-color: #f9f9f9">
            <div class="u-row" style="margin: 0 auto;min-width: 320px;max-width: 600px;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #1c103b;">
                <div style="border-collapse: collapse;display: table;width: 100%;height: 100%;background-color: transparent;">
                <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="padding: 0px;background-color: #f9f9f9;" align="center"><table cellpadding="0" cellspacing="0" border="0" style="width:600px;"><tr style="background-color: #1c103b;"><![endif]-->
                
            <!--[if (mso)|(IE)]><td align="center" width="600" style="width: 600px;padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;" valign="top"><![endif]-->
            <div class="u-col u-col-100" style="max-width: 320px;min-width: 600px;display: table-cell;vertical-align: top;">
            <div style="height: 100%;width: 100% !important;">
            <!--[if (!mso)&(!IE)]><!--><div style="box-sizing: border-box; height: 100%; padding: 0px;border-top: 0px solid transparent;border-left: 0px solid transparent;border-right: 0px solid transparent;border-bottom: 0px solid transparent;"><!--<![endif]-->

            <table style="font-family:'Lato',sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
            <tbody>
                <tr>
                <td style="overflow-wrap:break-word;word-break:break-word;padding:15px;font-family:'Lato',sans-serif;" align="left">
                    
            <table height="0px" align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;table-layout: fixed;border-spacing: 0;mso-table-lspace: 0pt;mso-table-rspace: 0pt;vertical-align: top;border-top: 1px solid #1c103b;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%">
                <tbody>
                <tr style="vertical-align: top">
                    <td style="word-break: break-word;border-collapse: collapse !important;vertical-align: top;font-size: 0px;line-height: 0px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%">
                    <span>&#160;</span>
                    </td>
                </tr>
                </tbody>
            </table>

                </td>
                </tr>
            </tbody>
            </table>

            <!--[if (!mso)&(!IE)]><!--></div><!--<![endif]-->
            </div>
            </div>
            <!--[if (mso)|(IE)]></td><![endif]-->
                <!--[if (mso)|(IE)]></tr></table></td></tr></table><![endif]-->
                </div>
            </div>
            </div>



                <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
                </td>
            </tr>
            </tbody>
            </table>
            <!--[if mso]></div><![endif]-->
            <!--[if IE]></div><![endif]-->
            </body>

            </html>
END;
            if ($cmail === $mailx && $emailchange -> execute() && $mail -> send() && $con -> affected_rows) {
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
                            <h1 style="text-align: center;">Session terminated : You need to login again !</h1>
                            <div style="text-align: center; font-size: medium;">Email Changed : For security reasons , please login again</div>
                            <div style="text-align: center; font-size: medium;">An email with a verification link has been sent</div>
                            <div style="text-align: center; font-size: small;">You will be automatically redirected back to the login page in 4 seconds.</div>
                        </div>
                    </div>
                </body>
                </html>
            <?php die(); } else { ?>
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
            <?php die();}
        }
        if (isset($_POST["sub_pass"])) { 
            $pass = filter_input(INPUT_POST,"pass",FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $cpass = filter_input(INPUT_POST,"cpass",FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $origpass_query = $con -> prepare("SELECT pass FROM login_credentials WHERE username = ?");
            $origpass_query -> bind_param("s", $user_username);
            $origpass_query -> execute();
            $origpass_result = $origpass_query -> get_result();
            if ($origpass_result -> num_rows > 0) {
                $origpass_array = $origpass_result -> fetch_all(MYSQLI_ASSOC);
                foreach ($origpass_array as $row) {
                    $origpass = $row["pass"];
                }
            }
            if (password_verify($cpass,$origpass)) {
                echo "<script>alert('Error : Check the info provided !')</script>";
            } else {
                $hpass = password_hash($cpass, PASSWORD_DEFAULT);
                $passchange = $con -> prepare("UPDATE login_credentials set pass = ? WHERE pass = ?");
                $passchange -> bind_param("ss", $hpass, $origpass);
                if ($cpass === $pass && $passchange -> execute()) {
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
                                <h1 style="text-align: center;">Session terminated : You need to login again !</h1>
                                <div style="text-align: center; font-size: medium;">Password Changed : For security reasons , please login again with your new password</div>
                                <div style="text-align: center; font-size: small;">You will be automatically redirected back to the login page in 4 seconds.</div>
                            </div>
                        </div>
                    </body>
                    </html>
                <?php die(); } else { ?>
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
                <?php }
            }
        }
    }
    $_SESSION['accset'] = "accset";
}
?>
<?php if (isset($_SESSION['verif_id']) && $verif_id === false) { ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>WSYM Banking</title>
        <link rel="shortcut icon" href="./data/favicon.ico" type="image/x-icon">
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300&family=Open+Sans+Condensed:wght@300&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="./css/account_settings.css">
        <link href="./bootstrap-5.0.2-dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="cont1">
            <form method="post" class="cont2 d-flex flex-column justify-content-center align-items-center">
                <h3 class="text-center mt-3 text-white">Login again to confirm your identity</h3>
                <div class="mt-auto mb-auto d-flex flex-column justify-content-center align-items-center">
                <label class="labbor lab">
                    <img src="./data/user.svg" alt="">
                    <input type="text" placeholder="Username" id="" name="id_user" required>
                </label>
                <label class="labbor lab">
                    <img src="./data/mail.svg" alt="">
                    <input type="email" placeholder="Email" id="" name="id_mail" required>
                </label>
                <label class="labbor lab">
                    <img src="./data/key.svg" alt="">
                    <input type="password" placeholder="Password" id="passw1" name="id_pass" required>
                    <input type="checkbox" name="" id="passv1">
                </label>
                <span id="err" style="color: red;"></span>
                </div>
                <input type="submit" value="Login" class="but" name="id_sub" onclick="">
            </form>
        </div>
        <script src="./bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
        <script src="./js/acc.js"></script>
        <script>
            document.getElementById("passv1").addEventListener("click", function(){ func4(document.getElementById("passw1")); });
        </script>
    </body>
    </html>
<?php } else if (isset($_SESSION['verif_id']) && $verif_id === true) { ?>
    <?php if (isset($_SESSION['setting']) && $setting === "user"): ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>WSYM Banking</title>
            <link rel="shortcut icon" href="./data/favicon.ico" type="image/x-icon">
            <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300&family=Open+Sans+Condensed:wght@300&display=swap" rel="stylesheet">
            <link rel="stylesheet" href="./css/account_settings.css">
            <link href="./bootstrap-5.0.2-dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body>
            <div class="cont1">
                <form method="post" class="cont2 d-flex flex-column justify-content-center align-items-center">
                    <h1>Change your username</h1>
                    <div class="mt-auto mb-auto d-flex flex-column justify-content-center align-items-center">
                    <label class="labbor lab">
                        <img src="./data/user.svg" alt="">
                        <input type="text" placeholder="New Username" id="uuser" name="uuser" required>
                    </label>
                    <label class="labbor lab">
                        <img src="./data/repeat.svg" alt="">
                        <input type="text" placeholder="Confirm Username" id="cuuser" name="cuuser" required>
                    </label>
                    </div>
                    <input type="submit" value="Change your username" class="but" name="sub_uuser" onclick="return confuser()">
                </form>
            </div>
            <script src="./bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
            <script src="./js/acc.js"></script>
        </body>
        </html>
    <?php endif; ?>
    <?php if (isset($_SESSION['setting']) && $setting === "mail"): ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>WSYM Banking</title>
            <link rel="shortcut icon" href="./data/favicon.ico" type="image/x-icon">
            <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300&family=Open+Sans+Condensed:wght@300&display=swap" rel="stylesheet">
            <link rel="stylesheet" href="./css/account_settings.css">
            <link href="./bootstrap-5.0.2-dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body>
            <div class="cont1">
                <form method="post" class="cont2 d-flex flex-column justify-content-center align-items-center">
                    <h1>Change your email</h1>
                    <div class="mt-auto mb-auto d-flex flex-column justify-content-center align-items-center">
                    <label class="labbor lab">
                        <img src="./data/mail.svg" alt="">
                        <input type="email" placeholder="New Email" id="mail" name="mail" required>
                    </label>
                    <label class="labbor lab">
                        <img src="./data/repeat.svg" alt="">
                        <input type="email" placeholder="Confirm Email" id="cmail" name="cmail" required>
                    </label>
                    </div>
                    <input type="submit" value="Change your email" class="but" name="sub_mail" onclick="return confmail()">
                </form>
            </div>
            <script src="./bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
            <script src="./js/acc.js"></script>
        </body>
        </html>
    <?php endif; ?>
    <?php if (isset($_SESSION['setting']) && $setting === "pass"): ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>WSYM Banking</title>
            <link rel="shortcut icon" href="./data/favicon.ico" type="image/x-icon">
            <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300&family=Open+Sans+Condensed:wght@300&display=swap" rel="stylesheet">
            <link rel="stylesheet" href="./css/account_settings.css">
            <link href="./bootstrap-5.0.2-dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body>
            <div class="cont1">
                <form method="post" class="cont2 d-flex flex-column justify-content-center align-items-center">
                    <h1>Change your password</h1>
                    <div class="mt-auto mb-auto d-flex flex-column justify-content-center align-items-center">
                    <label class="labbor lab">
                        <img src="./data/key.svg" alt="">
                        <input type="password" placeholder="New Password" id="pass" name="pass">
                        <input type="checkbox" name="" id="passv2">
                    </label>
                    <label class="labbor lab">
                        <img src="./data/repeat.svg" alt="">
                        <input type="password" placeholder="Confirm Password" id="cpass" name="cpass">
                        <input type="checkbox" name="" id="passv3">
                    </label>
                    </div>
                    <input type="submit" value="Change your password" class="but" name="sub_pass" onclick="return confpass()">
                </form>
            </div>
            <script src="./bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
            <script src="./js/acc.js"></script>
        </body>
        </html>
    <?php endif; ?>
<?php } ?>