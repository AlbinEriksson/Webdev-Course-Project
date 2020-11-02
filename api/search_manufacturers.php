<?php
include('../private/util.php');

if(isEmpty($_GET["name"])) {
    http_response_code(400);
    die("No 'name' provided.");
}

$name = $_GET["name"];
$manufacturers = searchManufacturersByName($name, 10);

header('Content-Type: application/json');
echo json_encode($manufacturers);
?>
