<?php
include('../private/util.php');

session_start();
if(!isLoggedIn()) {
    http_response_code(400);
    die("Not logged in.");
}

$input = getPostRequestBody();
$json = json_decode($input, true);

/* Input format:
{
    "item_id": 123
}
*/

$itemid = $json["item_id"];
$userid = $_SESSION["user_id"];

if(!reviewExists($userid, $itemid)) {
    http_response_code(204);
    die("That review does not exist.");
}

removeReview($userid, $itemid);
?>
