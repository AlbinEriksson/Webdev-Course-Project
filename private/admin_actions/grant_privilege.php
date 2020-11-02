<?php
include_once("private/result.php");
include_once("private/db.php");
include_once("private/util.php");

if(signedInUserHasPrivilege("user_grant_privilege")) {
    $username = $_POST["username"];
    $privilege = $_POST["privilege"];

    if(verifyUsernameExists($username)) {
        if(verifyPrivilegeExists($privilege)) {
            $userid = getUserIDFromName($username);
            grantPrivilege($userid, $privilege);
            successResult("Granted privilege '$privilege' for user '$username'!");
        }
    }
}
?>
