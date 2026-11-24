<?php
/** logout.php - end the session and go back to the front door. */
require __DIR__ . '/bootstrap.php';
session_boot();
logout_user();
redirect('index.php?bye=1');
