<?php
include_once("private/result.php");
include_once("private/db.php");
include_once("private/util.php");

if(signedInUserHasPrivilege("manufacturer_add")) {
    $name = $_POST["name"];

    if(verifyManufacturerDoesNotExist($name)) {
        addManufacturer($name);
        successResult("Added manufacturer '$name'!");
    }
}
?>
