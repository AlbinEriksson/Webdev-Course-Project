<?php
if(session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once('private/util.php');
$user = array();
if(isLoggedIn()) {
    $user = getUserFromID($_SESSION["user_id"]);
}
?>
<!DOCTYPE html>
<html lang="en">
<html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <title><?=$layout_title?></title>
        <meta name="description" content="" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="stylesheet" href="/css/home.css" />
        <script src="/js/hamburger.js" async></script>
        <script src="/js/Cart.js" async></script>
        <script src="/js/core.js"></script>
    </head>
    <body>
        <nav id="nav-bar" class="no-select">
            <a id="hamburger" onclick="openHamburger(); closeCart()">
                <img src="images/icons/bars.svg"/>
            </a>
            <div class="flex-row flex-space flex-no-margin">
                <a class="logo max-height" href="/">
                    <img src="images/icons/webshop.svg"/>
                </a>
                <div id="nav-link-bar">
                    <a id="sidebar-close" onclick="closeHamburger()">
                        &times;
                    </a>
                    <ul class="nav-links no-bullet inner-body">
                        <li>
                            <a class="nav-link inner-body" href="/browse.php">
                                <div class="nav-link-text">Shop</div>
                            </a>
                        </li>
                        <li>
                            <a class="nav-link inner-body" href="/about.php">
                                <div class="nav-link-text">About</div>
                            </a>
                        </li>
                        <?php if(signedInUserHasAdminAccess()) { ?>
                            <li>
                                <a class="nav-link inner-body" href="/admin.php">
                                    <div class="nav-link-text">Admin</div>
                                </a>
                            </li>
                        <?php } ?>
                        <?php if(isLoggedIn()) { ?>
                            <li>
                                <a class="nav-link inner-body" href="/logout.php?redirect=<?=$_SERVER["REQUEST_URI"]?>">
                                    <div class="nav-link-text">
                                        Logout
                                        <br>
                                        <div class="small">Logged in as '<?=$user["name"]?>'</div>
                                    </div>
                                </a>
                            </li>
                        <?php } else { ?>
                            <li>
                                <a class="nav-link inner-body" href="/login.php?redirect=<?=$_SERVER["REQUEST_URI"]?>">
                                    <div class="nav-link-text">Login</div>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
                <a id="cart" onclick="openCart(); closeHamburger()">
                    <img class = "cart-icon" src="images/icons/cart.svg"/>
                </a>
            </div>
            <div id="cart-bar">
                <a id="cart-bar-close" onclick="closeCart()">
                    &times;
                </a>
                <ul class="products no-bullet">
                    <?php
                        $cartItemIDs = "";
                        if(!cartEmpty()) {
                            $cartItems = getCartItems();

                            $updatedCart = array();
                            foreach($cartItems as $cartItem) {
                                $itemid = $cartItem["id"];
                                $quantity = $_SESSION["cart"][$itemid];
                                $price = $cartItem["price"];
                                $updatedCart[$itemid] = $quantity; ?>
                                <li class="product-cart" data-itemid="<?=$cartItem["id"]?>">
                                    <a
                                        href="/product.php?id=<?=$cartItem["id"]?>"
                                        class="square-image-frame"
                                        style="background-image:url('/images/products/<?=$cartItem['image'];?>')">
                                    </a>
                                    <div class="cart-sidetext">
                                        <div class="cart-text">
                                            <?php if($quantity > 1) { ?>
                                                <?=$quantity?>x
                                            <?php } ?>
                                            <?=$cartItem['title'];?>
                                        </div>
                                        <div class="cart-text">
                                            <?=$price?>kr
                                        </div>
                                        <button class="btn small-btn red-btn" onclick="removeFromCart(this)">Remove</button>
                                    </div>
                                </li> <?php
                            }
                            $_SESSION["cart"] = $updatedCart;
                        }
                    ?>
                </ul>
                <div id="checkout">
                    <?php if(cartEmpty()) { ?>
                        <a class="disabled" id="checkout-btn">
                            <div class="checkout-btn-text">Checkout</div>
                        </a>
                    <?php } else { ?>
                        <a id="checkout-btn" href="/checkout.php">
                            <div class="checkout-btn-text">Checkout</div>
                        </a>
                    <?php } ?>
                </div>
            </div>
            <div id="blackout" class="inner-body" onclick="closeHamburger(); closeCart()"></div>
        </nav>
        <form id="search-form" method="post" action="/browse.php">
            <input type="search" name="query" placeholder="search" id="search-bar" onfocus="searchFocus()" onblur="searchBlur()">
            <button type="submit">
                <img src="images/icons/search.svg" class="search-submit" />
            </button>
        </form>
        <div id="content">
            <?php include($layout_childView); ?>
        </div>
    </body>
</html>
