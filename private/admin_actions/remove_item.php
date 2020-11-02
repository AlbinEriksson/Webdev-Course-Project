<?php
include_once("private/result.php");
include_once("private/db.php");
include_once("private/util.php");

if(signedInUserHasPrivilege("item_remove")) {
    $id = $_POST["id"];

    if(verifyProductIDExists($id)) {
        if(verifyProductNotInAnOrder($id)) {
            $item = getItemFromID($id);
            $name = $item["title"];
            $oldImage = $item["image"];
            $extraImages = getExtraItemImages($id);
    
            revokeSale($id);
            removeItemReviews($id);
            removeAllExtraProductImages($id);
            removeItem($id);
    
            deleteItemImageIfUnused($oldImage);
            foreach($extraImages as $extraImage) {
                deleteExtraItemImageIfUnused($extraImage);
            }
    
            successResult("Item '$name' has been removed!");
        }
    }
}
?>
