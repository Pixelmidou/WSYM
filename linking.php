<?php
require __DIR__ . "/vendor/autoload.php";
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$con = new mysqli($_ENV["database_hostname"],$_ENV["database_username"],$_ENV["database_password"],$_ENV["database_name"]);
?>