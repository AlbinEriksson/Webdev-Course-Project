<?php
include_once("private/result.php");
include_once("private/db.php");
include_once("private/util.php");

if(signedInUserHasPrivilege("category_add")) {
    $name = $_POST["name"];
    $parent = null;
    if(isset($_POST["parent"]) && $_POST["parent"] !== "") {
        $parent = $_POST["parent"];
    }

    if($parent === null || verifyCategoryExists($parent)) {
        if(verifyCategoryDoesNotExist($name)) {
            addCategory($name, $parent);
            successResult("Category '$name' added!");
        }
    }
}
?>