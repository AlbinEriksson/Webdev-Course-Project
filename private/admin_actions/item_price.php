<?php
include_once("private/result.php");
include_once("private/db.php");
include_once("private/util.php");

if(signedInUserHasPrivilege("item_price")) {
    $id = $_POST["id"];
    $price = $_POST["price"];

    if(verifyProductIDExists($id)) {
        setItemPrice($id, $price);
        $name = getItemNameFromID($id);
        successResult("Price changed for '$name'!");
    }
}
?>
