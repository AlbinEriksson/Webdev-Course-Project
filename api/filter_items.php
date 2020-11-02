<?php
/* Input format:
{
    "search": "keyboard",
    "categories": [
        "Computer Peripherals", "Keyboards", "Gaming Keyboards"
    ],
    "manufacturers": [
        "HP", "HyperX"
    ],
    "price_max": 3000,
    "page": 1
}
*/

/* Output format:
{
    "items": [
        {
            "id": 123,
            "title": "Banana",
            "image": "Banana.jpg",
            "price": "5.50",
            "percentage": "10.00",
            "actual_price": "4.95"
        },
        {
            "id": 456,
            "title": "Orange",
            "image": "Orange.jpg",
            "price": "4.00",
            "percentage": null
        }
    ],
    "count": 2
}
*/

include('../private/util.php');

$input = getPostRequestBody();
$json = json_decode($input, true);
$outJson = array();

$searchTerm     = $json["search"];
$maxPrice       = $json["price_max"];
$page           = $json["page"];
$categories     = $json["categories"];
$manufacturers  = $json["manufacturers"];

$outJson["items"] = getFilteredItemPage($searchTerm, $maxPrice, $page, $categories, $manufacturers);
$outJson['count'] = countItemsInFilter($searchTerm, $maxPrice, $categories, $manufacturers);

header('Content-Type: application/json');
echo json_encode($outJson);
?>
