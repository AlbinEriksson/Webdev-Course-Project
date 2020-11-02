<?php
include_once("private/result.php");
include_once("private/db.php");
include_once("private/util.php");

if(signedInUserHasPrivilege("user_add")) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    addUser($username, $password);
    successResult("User '$username' added!");
}
?>
