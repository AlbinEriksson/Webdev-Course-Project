<?php
include_once("private/result.php");
include_once("private/db.php");
include_once("private/util.php");

if(signedInUserHasPrivilege("item_sale_revoke")) {
    $id = $_POST["id"];

    if(verifyProductIDExists($id)) {
        if(verifyProductOnSale($id)) {
            revokeSale($id);
            $name = getItemNameFromID($id);
            successResult("Sale revoked from '$name'");
        }
    }
}
?>
