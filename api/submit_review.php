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
    "item_id": 123,
    "rating": 9,
    "text": "This was really, really good."
}
*/

$itemid = $json["item_id"];
$userid = $_SESSION["user_id"];
$rating = $json["rating"];
$reviewText = $json["text"];

submitReview($userid, $itemid, $rating, $reviewText);
?>
