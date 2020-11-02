<?php
include_once("private/result.php");
include_once("private/db.php");
include_once("private/util.php");

if(signedInUserHasPrivilege("item_add")) {
    $name = $_POST["name"];
    $image = $_FILES["image"];
    $price = $_POST["price"];
    $category = $_POST["category"];

    $sourcePath = $image["tmp_name"];
    $targetFilename = $image["name"];
    $targetPath = "images/products/$targetFilename";

    if(imageUpload($sourcePath, $targetPath)) {
        addItem($name, $image["name"], $price, $category);
        successResult("Added item '$name'!");
    }
}
?>
