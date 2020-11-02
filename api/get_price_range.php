<?php
include('../private/util.php');

$input = getPostRequestBody();
$json = json_decode($input, true);

if(count($json["categories"]) == 0) {
    header('Content-Type: application/json');
    die('{"min":0,"max":100000}');
}

$range = getPriceRangeInFilter($json["categories"], $json["manufacturers"]);

header('Content-Type: application/json');
echo json_encode($range);
?>
