<?php
include_once("private/result.php");
include_once("private/db.php");
include_once("private/util.php");

if(signedInUserHasPrivilege("item_category")) {
    $id = $_POST["id"];
    $category = $_POST["category"];

    if(verifyProductIDExists($id)) {
        if(verifyCategoryExists($category)) {
            setItemCategory($id, $category);
            $name = getItemNameFromID($id);
            successResult("Category changed for '$name'!");
        }
    }
}
?>
