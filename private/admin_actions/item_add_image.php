<?php
include_once("private/result.php");
include_once("private/db.php");
include_once("private/util.php");

if(signedInUserHasPrivilege("item_add_image")) {
    $id = $_POST["id"];
    $image = $_FILES["image"];

    $sourcePath = $image["tmp_name"];
    $targetFilename = $image["name"];
    $targetPath = "images/products/extra/$targetFilename";

    if(imageUpload($sourcePath, $targetPath)) {
        addExtraProductImage($id, $targetFilename);
        $name = getItemNameFromID($id);
        successResult("Added image to '$name'!");
    }
}
?>
