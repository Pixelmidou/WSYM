<?php
/**
 * admin_redirect.php - "Which page do you want?" for admins.
 * Three choices: the admin console, the user page, or sign out.
 */
require __DIR__ . '/bootstrap.php';
$username = require_login(true);
?>
<?php page_top('WSYM Banking', ['redirections_style']); ?>
    <style>
        .container1 { display:flex; justify-content:center; align-items:center; height:100vh; }
        .panel { background-color: lightgray; width: 700px; height: 420px; border-top: 10px solid #87cefa;
                 border-bottom: 10px solid #87cefa; display:flex; flex-direction:column; align-items:center; }
        .panel h2, .panel h4 { color: whitesmoke; }
        .but { width: 200px; padding: 10px; border-radius: 5px; background-color: lightskyblue;
               transition: all .2s ease-in-out; cursor: pointer; border: none; outline: none;
               color: white; opacity: .7; }
        .but:hover { opacity: 1; }
    </style>
    <div class="container1">
        <div class="panel">
            <h2 class="mt-auto">Admin Redirections</h2>
            <h4 class="mt-1 mb-auto">Welcome Back <?php echo e($username); ?> !</h4>
            <div class="mb-auto d-flex gap-3">
                <a class="but text-center text-decoration-none" href="admin.php">Your Admin Page</a>
                <a class="but text-center text-decoration-none" href="welcome.php">Your User Page</a>
                <a class="but text-center text-decoration-none" href="logout.php">Sign Out</a>
            </div>
        </div>
    </div>
<?php page_bottom(); ?>
