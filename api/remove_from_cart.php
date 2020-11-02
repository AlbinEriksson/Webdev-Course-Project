<?php
include('../private/util.php');

if(isEmpty($_GET["itemid"])) {
    http_response_code(400);
    die("'itemid' must be specified.");
}

session_start();
$itemid = intval($_GET['itemid']);
removeCartItem($itemid);
?>
