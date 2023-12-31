<?php
if ( $_SERVER['REQUEST_METHOD']=='GET' && realpath(__FILE__) == realpath( $_SERVER['SCRIPT_FILENAME'] ) ) {        
    header( 'HTTP/1.0 403 Forbidden', TRUE, 403 );
    die( header( 'location: index.php' ) );
}
// true means its running locally , false means its running on the webhost
$linking_var = true;
if ($linking_var) {
    $con = new mysqli("localhost", "root", "", "wsym");
} else {
    $con = new mysqli("", "", "", "");
}
?>