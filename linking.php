<?php
// true means its running locally , false means its running on the webhost
$linking_var = true;
if ($linking_var) {
    $con = new mysqli("localhost", "root", "", "wsym");
} else {
    $con = new mysqli("localhost", "id21461070_wsymdatabase", "@Loginpagephase2", "id21461070_wsym");
}
?>