<?php
include_once("private/result.php");
include_once("private/db.php");
include_once("private/util.php");

if(signedInUserHasPrivilege("item_sale_absolute")) {
    $id = $_POST["id"];
    $price = $_POST["price"];
    $date = $_POST["date"];
    $time = $_POST["time"];
    
    $timestamp = "$date $time";

    if(verifyProductIDExists($id)) {
        if(verifyProductNotOnSale($id)) {
            $item = getItemFromID($id);
            $name = $item["title"];
            $originalPrice = $item["price"];

            $percentage = calculateDiscount($originalPrice, $price);
            putItemOnSale($id, $percentage, $timestamp);
            successResult("Sale added for '$name'");
        }
    }
}
?>
