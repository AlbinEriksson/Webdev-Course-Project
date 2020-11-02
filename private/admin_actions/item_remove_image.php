<?php
include_once("private/result.php");
include_once("private/db.php");
include_once("private/util.php");

if(signedInUserHasPrivilege("item_remove_image")) {
    $id = $_POST["id"];
    $image = $_POST["image"];

    $targetPath = "images/products/extra/$image";

    if(verifyFileExists($targetPath)) {
        removeExtraProductImage($id, $image);
        deleteExtraItemImageIfUnused($image);
        $name = getItemNameFromID($id);
        successResult("Removed image from '$name'!");
    }
}
?>
