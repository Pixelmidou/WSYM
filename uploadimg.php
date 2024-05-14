<?php
require ("linking.php");
if ($con->connect_error) {
    die("Connection Failed" . $con->connect_error);
} else {
    function uploadsuccess() {
        if (isset($_SESSION['admin_username'])) {
            echo '
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <meta http-equiv="refresh" content="4; url=admin_redirections.php">
                <title>WSYM Banking</title>
                <link rel="shortcut icon" href="./data/favicon.ico" type="image/x-icon">
                <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300&family=Open+Sans+Condensed:wght@300&display=swap" rel="stylesheet">
                <link rel="stylesheet" href="./css/redirections_style.css">
            </head>
            <body>
                <div class="container1">
                    <div class="container2">
                        <h1 style="text-align: center;">Action Success : Image Uploaded !</h1>
                        <div style="text-align: center; font-size: small;">You will be automatically redirected back to the welcome page in 4 seconds.</div>
                    </div>
                </div>
            </body>
            </html>
            ';
        } else if (isset($_SESSION['user_username'])) {
            echo ' 
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
                        <h1 style="text-align: center;">Action Success : Image Uploaded !</h1>
                        <div style="text-align: center; font-size: small;">You will be automatically redirected back to the welcome page in 4 seconds.</div>
                    </div>
                </div>
            </body>
            </html>
            ';
        }
    }
    function uploadfail() {
        if (isset($_SESSION['admin_username'])) {
            echo '
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <meta http-equiv="refresh" content="4; url=admin_redirections.php">
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
            ';
        } else if (isset($_SESSION['user_username'])) {
            echo ' 
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
            ';
        }    
    }
    function largefile() {
        if (isset($_SESSION['admin_username'])) {
            echo '
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <meta http-equiv="refresh" content="4; url=admin_redirections.php">
                <title>WSYM Banking</title>
                <link rel="shortcut icon" href="./data/favicon.ico" type="image/x-icon">
                <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300&family=Open+Sans+Condensed:wght@300&display=swap" rel="stylesheet">
                <link rel="stylesheet" href="./css/redirections_style.css">
            </head>
            <body>
                <div class="container1">
                    <div class="container2">
                        <h1 style="text-align: center;">Action Failed : Image must not exceed 1MB !</h1>
                        <div style="text-align: center; font-size: small;">You will be automatically redirected back to the welcome page in 4 seconds.</div>
                    </div>
                </div>
            </body>
            </html>
            ';
        } else if (isset($_SESSION['user_username'])) {
            echo ' 
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
                        <h1 style="text-align: center;">Action Failed : Image must not exceed 1MB !</h1>
                        <div style="text-align: center; font-size: small;">You will be automatically redirected back to the welcome page in 4 seconds.</div>
                    </div>
                </div>
            </body>
            </html>
            ';
        }    
    }
    function typefile() {
        if (isset($_SESSION['admin_username'])) {
            echo '
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <meta http-equiv="refresh" content="4; url=admin_redirections.php">
                <title>WSYM Banking</title>
                <link rel="shortcut icon" href="./data/favicon.ico" type="image/x-icon">
                <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300&family=Open+Sans+Condensed:wght@300&display=swap" rel="stylesheet">
                <link rel="stylesheet" href="./css/redirections_style.css">
            </head>
            <body>
                <div class="container1">
                    <div class="container2">
                        <h1 style="text-align: center;">Action Failed : File must be a valid image with (png, jpg, jpeg, gif) as extension !</h1>
                        <div style="text-align: center; font-size: small;">You will be automatically redirected back to the welcome page in 4 seconds.</div>
                    </div>
                </div>
            </body>
            </html>
            ';
        } else if (isset($_SESSION['user_username'])) {
            echo ' 
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
                        <h1 style="text-align: center;">Action Failed : File must be a valid image with (png, jpg, jpeg, gif) as extension !</h1>
                        <div style="text-align: center; font-size: small;">You will be automatically redirected back to the welcome page in 4 seconds.</div>
                    </div>
                </div>
            </body>
            </html>
            ';
        }    
    }
    function mandfile() {
        if (isset($_SESSION['admin_username'])) {
            echo '
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <meta http-equiv="refresh" content="4; url=admin_redirections.php">
                <title>WSYM Banking</title>
                <link rel="shortcut icon" href="./data/favicon.ico" type="image/x-icon">
                <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300&family=Open+Sans+Condensed:wght@300&display=swap" rel="stylesheet">
                <link rel="stylesheet" href="./css/redirections_style.css">
            </head>
            <body>
                <div class="container1">
                    <div class="container2">
                        <h1 style="text-align: center;">Action Failed : Choose a file !</h1>
                        <div style="text-align: center; font-size: small;">You will be automatically redirected back to the welcome page in 4 seconds.</div>
                    </div>
                </div>
            </body>
            </html>
            ';
        } else if (isset($_SESSION['user_username'])) {
            echo ' 
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
                        <h1 style="text-align: center;">Action Failed : Choose a file !</h1>
                        <div style="text-align: center; font-size: small;">You will be automatically redirected back to the welcome page in 4 seconds.</div>
                    </div>
                </div>
            </body>
            </html>
            ';
        }    
    }
    session_start();
    if (isset($_SESSION['user_username'])) {
        $username = $_SESSION['user_username'];
    }
    if (isset($_SESSION['admin_username'])) {
        $username = $_SESSION['admin_username'];
    }
    if (isset($_POST["imgsub"])) {
        if (!empty($_FILES['imgupload']['name'])) {
            $allowed_ext = ['png', 'jpg', 'jpeg', 'gif'];
            $file_name = $_FILES['imgupload']['name'];
            $file_size = $_FILES['imgupload']['size'];
            $file_tmp = $_FILES['imgupload']['tmp_name'];
            $target_dir = "./data/uploads/";
            $file_ext = strtolower(pathinfo(basename($file_name),PATHINFO_EXTENSION));
            $checkimagecontent = mime_content_type($file_tmp);
            $cit_values = explode("/", $checkimagecontent);
            if (in_array($file_ext, $allowed_ext) && $cit_values[0] === "image" && in_array($cit_values[1],$allowed_ext)) {
                if ($file_size <= 1000000) {
                    $queryx = $con -> prepare("UPDATE login_credentials SET pfp = ? WHERE username = ?");
                    $queryx -> bind_param("ss", $a, $username);
                    switch ($file_ext) {
                        case "png":
                            if (file_exists($target_dir . $username . ".jpg")) {
                                unlink($target_dir . $username . ".jpg");
                            }
                            if (file_exists($target_dir . $username . ".jpeg")) {
                                unlink($target_dir . $username . ".jpeg");
                            }
                            if (file_exists($target_dir . $username . ".gif")) {
                                unlink($target_dir . $username . ".gif");
                            }
                            $a = "$username.png";
                            $queryx -> bind_param("ss", $a, $username);
                            if ($queryx -> execute() && $con -> affected_rows && move_uploaded_file($file_tmp, $target_dir . $username . ".png")) {
                                uploadsuccess();
                                die();
                            } else {
                                uploadfail();
                                die();
                            }
                            break;
                        case "jpg":
                            if (file_exists($target_dir . $username . ".png")) {
                                unlink($target_dir . $username . ".png");
                            }
                            if (file_exists($target_dir . $username . ".jpeg")) {
                                unlink($target_dir . $username . ".jpeg");
                            }
                            if (file_exists($target_dir . $username . ".gif")) {
                                unlink($target_dir . $username . ".gif");
                            }
                            $a = "$username.jpg";
                            if ($queryx -> execute() && $con -> affected_rows && move_uploaded_file($file_tmp, $target_dir . $username . ".jpg")) {
                                uploadsuccess();
                                die();
                            } else {
                                uploadfail();
                                die();
                            }
                            break;
                        case "jpeg":
                            if (file_exists($target_dir . $username . ".jpg")) {
                                unlink($target_dir . $username . ".jpg");
                            }
                            if (file_exists($target_dir . $username . ".png")) {
                                unlink($target_dir . $username . ".png");
                            }
                            if (file_exists($target_dir . $username . ".gif")) {
                                unlink($target_dir . $username . ".gif");
                            }
                            $a = "$username.jpeg";
                            if ($queryx -> execute() && $con -> affected_rows && move_uploaded_file($file_tmp, $target_dir . $username . ".jpeg")) {
                                uploadsuccess();
                                die();
                            } else {
                                uploadfail();
                                die();
                            }
                            break;
                        case "gif":
                            if (file_exists($target_dir . $username . ".jpg")) {
                                unlink($target_dir . $username . ".jpg");
                            }
                            if (file_exists($target_dir . $username . ".jpeg")) {
                                unlink($target_dir . $username . ".jpeg");
                            }
                            if (file_exists($target_dir . $username . ".png")) {
                                unlink($target_dir . $username . ".png");
                            }
                            $a = "$username.gif";
                            if ($queryx -> execute() && $con -> affected_rows && move_uploaded_file($file_tmp, $target_dir . $username . ".gif")) {
                                uploadsuccess();
                                die();
                            } else {
                                uploadfail();
                                die();
                            }
                            break;
                    }
                } else {
                    largefile();
                    die();
                }
            } else {
                typefile();
                die();
            }
        } else {
            mandfile();
            die();
        }
    } else if (isset($_POST["imgdel"])) {
        function deletesuccess() {
            if (isset($_SESSION['admin_username'])) {
                echo '
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <meta http-equiv="refresh" content="4; url=admin_redirections.php">
                    <title>WSYM Banking</title>
                    <link rel="shortcut icon" href="./data/favicon.ico" type="image/x-icon">
                    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300&family=Open+Sans+Condensed:wght@300&display=swap" rel="stylesheet">
                    <link rel="stylesheet" href="./css/redirections_style.css">
                </head>
                <body>
                    <div class="container1">
                        <div class="container2">
                            <h1 style="text-align: center;">Action Success : Image Deleted !</h1>
                            <div style="text-align: center; font-size: small;">You will be automatically redirected back to the welcome page in 4 seconds.</div>
                        </div>
                    </div>
                </body>
                </html>
                ';
            } else if (isset($_SESSION['user_username'])) {
                echo ' 
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
                            <h1 style="text-align: center;">Action Success : Image Deleted !</h1>
                            <div style="text-align: center; font-size: small;">You will be automatically redirected back to the welcome page in 4 seconds.</div>
                        </div>
                    </div>
                </body>
                </html>
                ';
            }
        }
        function deletefail() {
            if (isset($_SESSION['admin_username'])) {
                echo '
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <meta http-equiv="refresh" content="4; url=admin_redirections.php">
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
                ';
            } else if (isset($_SESSION['user_username'])) {
                echo ' 
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
                ';
            }    
        }
        function nonexist() {
            if (isset($_SESSION['admin_username'])) {
                echo '
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <meta http-equiv="refresh" content="4; url=admin_redirections.php">
                    <title>WSYM Banking</title>
                    <link rel="shortcut icon" href="./data/favicon.ico" type="image/x-icon">
                    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300&family=Open+Sans+Condensed:wght@300&display=swap" rel="stylesheet">
                    <link rel="stylesheet" href="./css/redirections_style.css">
                </head>
                <body>
                    <div class="container1">
                        <div class="container2">
                            <h1 style="text-align: center;">Action Failed : Image is not in our database</h1>
                            <div style="text-align: center; font-size: small;">You will be automatically redirected back to the welcome page in 4 seconds.</div>
                        </div>
                    </div>
                </body>
                </html>
                ';
            } else if (isset($_SESSION['user_username'])) {
                echo ' 
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
                            <h1 style="text-align: center;">Action Failed : Image is not in our database</h1>
                            <div style="text-align: center; font-size: small;">You will be automatically redirected back to the welcome page in 4 seconds.</div>
                        </div>
                    </div>
                </body>
                </html>
                ';
            }    
        }
        $target_dir = "./data/uploads/";
        $queryy = $con -> prepare("UPDATE login_credentials SET pfp = 'favicon.ico' WHERE username = ?");
        $queryy -> bind_param("s", $username);
        if (file_exists($target_dir . $username . ".jpg")) {
            if ($queryy -> execute() && $con -> affected_rows && unlink($target_dir . $username . ".jpg")) {
                deletesuccess();
                die();
            } else {
                deletefail();
                die();
            }
        } else if (file_exists($target_dir . $username . ".jpeg")) {
            if ($queryy -> execute() && $con -> affected_rows && unlink($target_dir . $username . ".jpeg")) {
                deletesuccess();
                die();
            } else {
                deletefail();
                die();
            }
        } else if (file_exists($target_dir . $username . ".gif")) {
            if ($queryy -> execute() && $con -> affected_rows && unlink($target_dir . $username . ".gif")) {
                deletesuccess();
                die();
            } else {
                deletefail();
                die();
            }
        } else if (file_exists($target_dir . $username . ".png")) {
            if ($queryy -> execute() && $con -> affected_rows && unlink($target_dir . $username . ".png")) {
                deletesuccess();
                die();
            } else {
                deletefail();
                die();
            }
        } else {
            nonexist();
            die();
        }
    } else {
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
?>