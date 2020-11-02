<?php
include_once("private/result.php");
include_once("private/db.php");
include_once("private/util.php");

if(signedInUserHasPrivilege("item_sale_percent")) {
    $id = $_POST["id"];
    $percentage = $_POST["percentage"];
    $date = $_POST["date"];
    $time = $_POST["time"];

    $timestamp = "$date $time";

    if(verifyProductIDExists($id)) {
        if(verifyProductNotOnSale($id)) {
            $item = getItemFromID($id);
            $name = $item["title"];

            putItemOnSale($id, $percentage, $timestamp);
            successResult("Sale added for '$name'");
        }
    }
}
?>
