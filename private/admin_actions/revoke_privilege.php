<?php
include_once("private/result.php");
include_once("private/db.php");
include_once("private/util.php");

if(signedInUserHasPrivilege("user_revoke_privilege")) {
    $username = $_POST["username"];
    $privilege = $_POST["privilege"];

    if(verifyUsernameExists($username)) {
        if(verifyPrivilegeExists($privilege)) {
            $id = getUserIDFromName($username);

            revokePrivilege($id, $privilege);
            successResult("Revoked privilege '$privilege' for user '$username'!");
        }
    }
}
?>
