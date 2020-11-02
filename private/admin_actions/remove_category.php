<?php
include_once("private/result.php");
include_once("private/db.php");
include_once("private/util.php");

if(signedInUserHasPrivilege("category_remove")) {
    $category = $_POST["category"];

    if(verifyCategoryExists($category)) {
        removeCategory($category);
        successResult("Category '$category' removed!");
    }
}
?>
