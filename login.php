<?php
include('private/util.php');

$redirectUrl = "/";
if(isset($_REQUEST["redirect"])) {
    $redirectUrl = $_REQUEST["redirect"];
}

if($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    if($username === "" || $password === "") {
        errorResult("Please enter a username and password.");
    } else {
        if(login($_POST["username"], $_POST["password"])) {
            header('Location: ' . $redirectUrl);
        }
    }
}

$layout_title = "Login";
$layout_childView = "private/views/_login.php";
include('private/layout.php')
?>