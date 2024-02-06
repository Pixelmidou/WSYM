<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require "phpmailer/src/Exception.php";
require "phpmailer/src/PHPMailer.php";
require "phpmailer/src/SMTP.php";
require __DIR__ . "/vendor/autoload.php";
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host = $_ENV["mailer_host"];
$mail->SMTPAuth = true;
$mail->Username = $_ENV["mailer_username"];
$mail->Password = $_ENV["mailer_password"];
$mail->SMTPSecure = "ssl";
$mail->Port = 465;
$mail->setFrom("wsymcorp@gmail.com");
$mail->isHTML(true);
return $mail
?>