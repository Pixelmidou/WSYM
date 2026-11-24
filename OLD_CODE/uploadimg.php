<?php
require ("linking.php");
if ($con->connect_error) {
    die("Connection Failed" . $con->connect_error);
} else {
    function resultpage($message) {
        if (isset($_SESSION['admin_username'])) {
            $redirect = "admin_redirections.php";
        } else {
            $redirect = "welcome.php";
        }
        echo '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta http-equiv="refresh" content="4; url=' . $redirect . '">
            <title>WSYM Banking</title>
            <link rel="shortcut icon" href="./data/favicon.ico" type="image/x-icon">
            <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300&family=Open+Sans+Condensed:wght@300&display=swap" rel="stylesheet">
            <link rel="stylesheet" href="./css/redirections_style.css">
        </head>
        <body>
            <div class="container1">
                <div class="container2">
                    <h1 style="text-align: center;">' . $message . '</h1>
                    <div style="text-align: center; font-size: small;">You will be automatically redirected back to the welcome page in 4 seconds.</div>
                </div>
            </div>
        </body>
        </html>
        ';
    }
    function uploadsuccess() {
        resultpage("Action Success : Image Uploaded !");
    }
    function uploadfail() {
        resultpage("Error 500 : Internal Server Error");
    }
    function largefile() {
        resultpage("Action Failed : Image must not exceed 1MB !");
    }
    function typefile() {
        resultpage("Action Failed : File must be a valid image with (png, jpg, jpeg, gif) as extension !");
    }
    function mandfile() {
        resultpage("Action Failed : Choose a file !");
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
            $file_ext = strtolower(pathinfo(basename($file_name), PATHINFO_EXTENSION));
            $checkimagecontent = mime_content_type($file_tmp);
            $cit_values = explode("/", $checkimagecontent);
            if (in_array($file_ext, $allowed_ext) && $cit_values[0] === "image" && in_array($cit_values[1], $allowed_ext)) {
                if ($file_size <= 1000000) {
                    $queryx = $con->prepare("UPDATE login_credentials SET pfp = ? WHERE username = ?");
                    $queryx->bind_param("ss", $a, $username);
                    switch ($file_ext) {
                        case "png":
                            $a = "$username.png";
                            $queryx->bind_param("ss", $a, $username);
                            if (move_uploaded_file($file_tmp, $target_dir . $username . ".png") && $queryx->execute()) {
                                if (file_exists($target_dir . $username . ".jpg")) {
                                    unlink($target_dir . $username . ".jpg");
                                }
                                if (file_exists($target_dir . $username . ".jpeg")) {
                                    unlink($target_dir . $username . ".jpeg");
                                }
                                if (file_exists($target_dir . $username . ".gif")) {
                                    unlink($target_dir . $username . ".gif");
                                }
                                uploadsuccess();
                                die();
                            } else {
                                uploadfail();
                                die();
                            }
                            break;
                        case "jpg":
                            $a = "$username.jpg";
                            if (move_uploaded_file($file_tmp, $target_dir . $username . ".jpg") && $queryx->execute()) {
                                if (file_exists($target_dir . $username . ".png")) {
                                    unlink($target_dir . $username . ".png");
                                }
                                if (file_exists($target_dir . $username . ".jpeg")) {
                                    unlink($target_dir . $username . ".jpeg");
                                }
                                if (file_exists($target_dir . $username . ".gif")) {
                                    unlink($target_dir . $username . ".gif");
                                }
                                uploadsuccess();
                                die();
                            } else {
                                uploadfail();
                                die();
                            }
                            break;
                        case "jpeg":
                            $a = "$username.jpeg";
                            if (move_uploaded_file($file_tmp, $target_dir . $username . ".jpeg") && $queryx->execute()) {
                                if (file_exists($target_dir . $username . ".jpg")) {
                                    unlink($target_dir . $username . ".jpg");
                                }
                                if (file_exists($target_dir . $username . ".png")) {
                                    unlink($target_dir . $username . ".png");
                                }
                                if (file_exists($target_dir . $username . ".gif")) {
                                    unlink($target_dir . $username . ".gif");
                                }
                                uploadsuccess();
                                die();
                            } else {
                                uploadfail();
                                die();
                            }
                            break;
                        case "gif":
                            $a = "$username.gif";
                            if (move_uploaded_file($file_tmp, $target_dir . $username . ".gif") && $queryx->execute()) {
                                if (file_exists($target_dir . $username . ".jpg")) {
                                    unlink($target_dir . $username . ".jpg");
                                }
                                if (file_exists($target_dir . $username . ".jpeg")) {
                                    unlink($target_dir . $username . ".jpeg");
                                }
                                if (file_exists($target_dir . $username . ".png")) {
                                    unlink($target_dir . $username . ".png");
                                }
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
            resultpage("Action Success : Image Deleted !");
        }
        function deletefail() {
            resultpage("Error 500 : Internal Server Error");
        }
        function nonexist() {
            resultpage("Action Failed : Image is not in our database");
        }
        $target_dir = "./data/uploads/";
        $queryy = $con->prepare("UPDATE login_credentials SET pfp = 'favicon.ico' WHERE username = ?");
        $queryy->bind_param("s", $username);
        if (file_exists($target_dir . $username . ".jpg")) {
            if ($queryy->execute() && $con->affected_rows && unlink($target_dir . $username . ".jpg")) {
                deletesuccess();
                die();
            } else {
                deletefail();
                die();
            }
        } else if (file_exists($target_dir . $username . ".jpeg")) {
            if ($queryy->execute() && $con->affected_rows && unlink($target_dir . $username . ".jpeg")) {
                deletesuccess();
                die();
            } else {
                deletefail();
                die();
            }
        } else if (file_exists($target_dir . $username . ".gif")) {
            if ($queryy->execute() && $con->affected_rows && unlink($target_dir . $username . ".gif")) {
                deletesuccess();
                die();
            } else {
                deletefail();
                die();
            }
        } else if (file_exists($target_dir . $username . ".png")) {
            if ($queryy->execute() && $con->affected_rows && unlink($target_dir . $username . ".png")) {
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
