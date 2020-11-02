<?php
include_once("private/result.php");
include_once("private/db.php");
include_once("private/util.php");

if(signedInUserHasPrivilege("item_manufacturer")) {
    $id = $_POST["id"];
    $manufacturer = $_POST["manufacturer"];

    if(verifyProductIDExists($id)) {
        if(verifyManufacturerExists($manufacturer)) {
            setItemManufacturer($id, $manufacturer);
            $name = getItemNameFromID($id);
            successResult("Manufacturer changed for '$name'!");
        }
    }
}
?>
