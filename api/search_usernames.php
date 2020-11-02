<?php
include('../private/util.php');
if(isEmpty($_GET["username"])) {
    http_response_code(400);
    die("No 'username' provided.");
}

$username = $_GET["username"];
$usernames = searchUsernames($username, 10);

header('Content-Type: application/json');
echo json_encode($usernames);
?>
