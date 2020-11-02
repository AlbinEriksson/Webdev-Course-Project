<?php
include('../private/util.php');

if(isEmpty($_GET["username"])) {
    http_response_code(400);
    die("No 'username' provided.");
}

$username = $_GET['username'];

$user = getUserFromName($username);
if($user === null) {
    http_response_code(404);
    die("'$username' not found.");
}

$outJson = $user;
$outJson['privileges'] = getPrivilegesForUser($user["id"]);

header('Content-Type: application/json');
echo json_encode($outJson);
?>
