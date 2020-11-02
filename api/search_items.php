<?php
include('../private/util.php');

if(isEmpty($_GET["itemname"])) {
    http_response_code(400);
    die("No 'itemname' provided.");
}

$name = $_GET["itemname"];
$items = searchItemsByName($name, 10);

header('Content-Type: application/json');
echo json_encode($items);
?>
