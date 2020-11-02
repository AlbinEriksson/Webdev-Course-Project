<?php
include('private/util.php');

if($_SERVER["REQUEST_METHOD"] === "POST") {
    $itemid = intval($_POST['itemid']);
    $quantity = intval($_POST['quantity']);

    if(!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }
    
    $_SESSION['cart'][$itemid] = $quantity;

    // Post/Redirect/Get pattern to avoid form resubmission
    header('Location: /product.php?id=' . $itemid);
    return;
}

if(!isset($_GET["id"]) || !itemExists($_GET["id"])) {
    header('Location: /');
    return;
}

$item = getItemFromID($_GET["id"]);
$sale = getItemSale($_GET["id"]);
$extraImages = getExtraItemImages($_GET["id"]);
$current_price = $item["price"];
if($sale !== null) {
    $current_price = calculatePrice($current_price, $sale["percentage"]);
}

$layout_title = $item["title"];
$layout_childView = "private/views/_product.php";
include('private/layout.php');
?>

