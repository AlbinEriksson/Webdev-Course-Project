<?php
include_once("private/result.php");
include_once("private/db.php");
include_once("private/util.php");

if(signedInUserHasPrivilege("manufacturer_remove")) {
    $name = $_POST["name"];

    if(verifyManufacturerExists($name)) {
        if(verifyManufacturerNotInUse($name)) {
            removeManufacturer($name);
            successResult("Removed manufacturer '$name'!");
        }
    }
}
?>
