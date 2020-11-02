<?php
include('private/util.php');
if(cartEmpty()) {
    header('Location: /');
    die;
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = $_POST["fullname"];
    $email = $_POST["email"];
    $address = $_POST["address"];
    $postalCode = $_POST["postal"];
    $cart = getCartItems();

    $orderid = createNewOrder($postalCode, $address, $email, $fullName);

    $stmt = prepareStmt("INSERT INTO order_products(order_id,item_id,quantity,product_price) VALUES(?,?,?,?)");
    foreach($cart as $item) {
        $stmt->bind_param("iiis", $orderid, $item["id"], $item["quantity"], $item["price"]);
        executeStmt($stmt, false);
    }
    $stmt->close();

    clearCart();
    header('Location: /');
    die;
}

$layout_title = "Check-out";
$layout_childView = "private/views/_checkout.php";
include('private/layout.php')
?>