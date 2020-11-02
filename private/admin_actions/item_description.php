<?php
include_once("private/result.php");
include_once("private/db.php");
include_once("private/util.php");

if(signedInUserHasPrivilege("item_description")) {
    $id = $_POST["id"];
    $description = $_POST["description"];

    if(verifyProductIDExists($id)) {
        setItemDescription($id, $description);
        $name = getItemNameFromID($id);
        successResult("Description changed for '$name'!");
    }
}
?>
