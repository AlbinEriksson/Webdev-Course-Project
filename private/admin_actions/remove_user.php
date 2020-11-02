<?php
include_once("private/result.php");
include_once("private/db.php");
include_once("private/util.php");

if(signedInUserHasPrivilege("user_remove")) {
    $username = $_POST["username"];
    if(verifyUsernameExists($username)) {
        $id = getUserIDFromName($username);

        removeUserReviews($id);
        removeUserPrivileges($id);
        removeUser($id);

        successResult("User '$username' removed!");
    }
}
?>
