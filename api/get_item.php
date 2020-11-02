<?php
include("../private/util.php");

if(isEmpty($_GET["itemid"])) {
    http_response_code(400);
    die("No 'itemid' provided.");
}

$itemid = $_GET['itemid'];

$item = getItemFromID($itemid);
if($item === null) {
    http_response_code(404);
    die("'$itemid' not found.");
}

$outJson = $item;
$outJson['extra_images'] = getExtraItemImages($itemid);

$sale = getItemSale($itemid);
if($sale !== null)
{
    $newPrice = calculatePrice($item["price"], $sale["percentage"]);

    $outJson['sale'] = array();
    $outJson['sale']['percentage'] = $sale['percentage'];
    $outJson['sale']['price'] = $newPrice;
    $outJson['sale']['deadline'] = $sale['deadline'];
}

header('Content-Type: application/json');
echo json_encode($outJson);
?>
