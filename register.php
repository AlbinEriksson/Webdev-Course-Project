<?php
include('private/util.php');

if(isLoggedIn()) {
    header('Location: /');
}

if($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $email = $_POST["email"];
    $firstName = $_POST["first-name"];
    $lastName = $_POST["last-name"];
    $birthDate = $_POST["birth-date"];
    $address = $_POST["address"];
    $postalCode = $_POST["postal-code"];
    if(registerUser($username, $password, $email, $firstName, $lastName, $birthDate, $address, $postalCode)) {
        login($username, $password);
        header('Location: /');
    }
}

$layout_title = "Register";
$layout_childView = "private/views/_register.php";
include('private/layout.php')
?>