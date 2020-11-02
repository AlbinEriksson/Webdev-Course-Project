<?php
include_once("private/result.php");
include_once("private/db.php");
include_once("private/util.php");

if(signedInUserHasPrivilege("ship_order")) {
    $orderid = $_POST["id"];

    if(verifyOrderExists($orderid)) {
        removeOrderItems($orderid);
        removeOrder($orderid);

        successResult("Order has been marked as shipped!");
    }
}
?>
