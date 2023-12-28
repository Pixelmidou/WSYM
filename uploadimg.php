<?php
require ("linking.php");
if ($con->connect_error) {
    die("Connection Failed" . $con->connect_error);
} else {
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
                            if (mysqli_query($con, "UPDATE login_credentials SET pfp ='$username.png' WHERE username = '$username'") && move_uploaded_file($file_tmp, $target_dir . $username . ".png")) {
                                echo '<p style="color: green;">File uploaded!</p>';
                            } else {
    
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
                            if (mysqli_query($con, "UPDATE login_credentials SET pfp ='$username.jpg' WHERE username = '$username'") && move_uploaded_file($file_tmp, $target_dir . $username . ".jpg")) {
                                echo '<p style="color: green;">File uploaded!</p>';
                            } else {
    
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
                            if (mysqli_query($con, "UPDATE login_credentials SET pfp ='$username.jpeg' WHERE username = '$username'") && move_uploaded_file($file_tmp, $target_dir . $username . ".jpeg")) {
                                echo '<p style="color: green;">File uploaded!</p>';
                            } else {
    
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
                            if (mysqli_query($con, "UPDATE login_credentials SET pfp ='$username.gif' WHERE username = '$username'") && move_uploaded_file($file_tmp, $target_dir . $username . ".gif")) {
                                echo '<p style="color: green;">File uploaded!</p>';
                            } else {
    
                            }
                            break;
                    }
                } else {
                    echo '<p style="color: red;">File too large!</p>';
                }
            } else {
                echo '<p style="color: red;">Invalid file type!</p>';
            }
        } else {
            echo '<p style="color: red;">Please choose a file</p>';
        }
    } else if (isset($_POST["imgdel"])) {
        $target_dir = "./data/uploads/";
        if (file_exists($target_dir . $username . ".jpg")) {
            if (mysqli_query($con, "UPDATE login_credentials SET pfp ='favicon.ico' WHERE username = '$username'") && unlink($target_dir . $username . ".jpg")) {
                echo '<p style="color: green;">Image Deleted</p>';
            } else {
                
            }
        }
        if (file_exists($target_dir . $username . ".jpeg")) {
            if (mysqli_query($con, "UPDATE login_credentials SET pfp ='favicon.ico' WHERE username = '$username'") && unlink($target_dir . $username . ".jpeg")) {
                echo '<p style="color: green;">Image Deleted</p>';
            } else {

            }
        }
        if (file_exists($target_dir . $username . ".gif")) {
            if (mysqli_query($con, "UPDATE login_credentials SET pfp ='favicon.ico' WHERE username = '$username'" && unlink($target_dir . $username . ".gif"))) {
                echo '<p style="color: green;">Image Deleted</p>';
            } else {

            }
        }
        if (file_exists($target_dir . $username . ".png")) {
            if (mysqli_query($con, "UPDATE login_credentials SET pfp ='favicon.ico' WHERE username = '$username'") && unlink($target_dir . $username . ".png")) {
                echo '<p style="color: green;">Image Deleted</p>';
            } else {

            }
        }
    } else {
        session_destroy();
        header("Location: index.html");
        exit;
    }
}
?>