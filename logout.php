<?php
session_start();
if(session_status() === PHP_SESSION_ACTIVE) {
    $_SESSION["logged_in"] = false;
    unset($_SESSION["user_id"]);
}
$backUrl = '/';
if(isset($_GET["redirect"])) {
    $backUrl = $_GET["redirect"];
}
header("Location: $backUrl");
?>
