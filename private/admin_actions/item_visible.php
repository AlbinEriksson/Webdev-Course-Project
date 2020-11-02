<?php
include_once("private/result.php");
include_once("private/db.php");
include_once("private/util.php");

if(signedInUserHasPrivilege("item_visible")) {
    $id = $_POST["id"];
    $visible = isset($_POST["visible"]);

    if(verifyProductIDExists($id)) {
        setItemVisibility($id, $visible);

        $name = getItemNameFromID($id);
        if($visible) {
            successResult("'$name' is now visible!");
        } else {
            successResult("'$name' is now hidden!");
        }
    }
}
?>
