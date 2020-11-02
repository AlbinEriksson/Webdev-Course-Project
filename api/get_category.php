<?php
include('../private/util.php');

if(isEmpty($_GET['name'])) {
    http_response_code(400);
    die("No 'name' provided.");
}

$name = $_GET['name'];
$category = getCategory($name);

if($category === null) {
    http_response_code(404);
    die("'$name' not found.");
}

header('Content-Type: application/json');
echo json_encode($category);
?>
