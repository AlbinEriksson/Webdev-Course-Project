<?php
include('../private/util.php');

$input = getPostRequestBody();
$json = json_decode($input, true);

if(count($json) == 0) {
    header('Content-Type: application/json');
    die("[]");
}

$manufacturers = getManufacturersInCategories($json);

header('Content-Type: application/json');
echo json_encode($manufacturers);
?>
