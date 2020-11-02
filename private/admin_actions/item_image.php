<?php
include_once("private/result.php");
include_once("private/db.php");
include_once("private/util.php");

if(signedInUserHasPrivilege("item_image")) {
    $id = $_POST["id"];
    $image = $_FILES["image"];

    $sourcePath = $image["tmp_name"];
    $targetFilename = $image["name"];
    $targetPath = "images/products/$targetFilename";

    if(imageUpload($sourcePath, $targetPath)) {
        $item = getItemFromID($id);
        $name = $item["title"];
        $oldImage = $item["image"];

        setItemImage($id, $targetFilename);
        successResult("Changed image for '$name'!");
        
        deleteItemImageIfUnused($oldImage);
    }
}
?>
