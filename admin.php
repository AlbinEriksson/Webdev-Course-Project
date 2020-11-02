<?php
include("private/util.php");

/* Kick user if logged out or no privileges are available */
if(!isLoggedIn() || !signedInUserHasAdminAccess()) {
    header('Location: /');
}

/* Perform POST action */
if(isset($_POST["action"])) {
    include("private/admin_actions/" . $_POST["action"] . ".php");
}

/* Fetch all privilege types from DB */
$privilegeData = "";
{
    $stmt = prepareStmt("SELECT * FROM privileges");
    $result = executeStmt($stmt);
    $privileges = $result->fetch_all(MYSQLI_NUM);
    foreach($privileges as $row) {
        $privilege = $row[0];
        $privilegeData .= "<option value='$privilege'>$privilege</option>";
    }
}

/* Fetch all categories from DB */
$categoryData = "";
{
    $stmt = prepareStmt("SELECT * FROM categories");
    $result = executeStmt($stmt);
    $categories = $result->fetch_all(MYSQLI_NUM);
    foreach($categories as $row) {
        $category = $row[0];
        $categoryData .= "<option value='$category'>$category</option>";
    }
}

/* Fetch all orders in DB */
$orders = array();
{
    $stmt = prepareStmt("SELECT * FROM orders ORDER BY time_added ASC");
    $result = executeStmt($stmt);
    $ordersList = $result->fetch_all(MYSQLI_ASSOC);
    foreach($ordersList as $order) {
        $orders[] = $order;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <title>ADMIN</title>
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="stylesheet" href="./css/home.css" />
        <link rel="stylesheet" href="./css/admin.css" />
        <script src="../js/hamburger.js" async></script>
    </head>
    <body>
        <nav id="nav-bar">
            <a id="hamburger" onclick="openHamburger();">
                <img src="images/icons/bars.svg"/>
            </a>
            <div class="flex-row flex-space flex-no-margin">
                <a class="logo max-height" href="#home">
                    <img src="images/icons/webshop.svg"/>
                    <div class="nav-link">Admin</div>
                </a>
                <div id="nav-link-bar">
                    <a id="sidebar-close" onclick="closeHamburger()">
                        &times;
                    </a>
                    <ul class="nav-links no-bullet inner-body">
                        <li>
                            <a class="nav-link inner-body" href="/">
                                <div class="nav-link-text">Back to main site</div>
                            </a>
                        </li>
                        <?php if(userPrivilegeStartsWith($_SESSION["user_id"], "category")) { ?>
                            <li>
                                <a id="nav-tab-categories" class="nav-link inner-body" href="#categories">
                                    <div class="nav-link-text">Categories</div>
                                </a>
                            </li>
                        <?php } if(userPrivilegeStartsWith($_SESSION["user_id"], "item")) { ?>
                            <li>
                                <a id="nav-tab-items" class="nav-link inner-body" href="#items">
                                    <div class="nav-link-text">Items</div>
                                </a>
                            </li>
                        <?php } if(userPrivilegeStartsWith($_SESSION["user_id"], "manufacturer")) { ?>
                            <li>
                                <a id="nav-tab-manufacturers" class="nav-link inner-body" href="#manufacturers">
                                    <div class="nav-link-text">Manufacturers</div>
                                </a>
                            </li>
                        <?php } if(userPrivilegeStartsWith($_SESSION["user_id"], "order")) { ?>
                            <li>
                                <a id="nav-tab-orders" class="nav-link inner-body" href="#orders">
                                    <div class="nav-link-text">Orders</div>
                                </a>
                            </li>
                        <?php } if(userPrivilegeStartsWith($_SESSION["user_id"], "user")) { ?>
                            <li>
                                <a id="nav-tab-users" class="nav-link inner-body" href="#users">
                                    <div class="nav-link-text">Users</div>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <div id="blackout" class="inner-body" onclick="closeHamburger()"></div>
        </nav>
        <div id="content">
            <?php showPostResult(); ?>
            <div class="admin-tab" id="home-tab">
                <h1>Welcome to the admin page!</h1>
            </div>
            <?php if(userPrivilegeStartsWith($_SESSION["user_id"], "category")) { ?>
                <div class="admin-tab" id="categories-tab">
                    <h1>Categories</h1>
                    <div id="view-category" class="container">
                        <div class="flex-row">
                            <div class="spaced">
                                <h2>View category</h2>
                                <div class="input-suggest">
                                    <select id="view-category-field">
                                        <?=$categoryData?>
                                    </select>
                                </div>
                                <div class="flex-row spaced">
                                    <button class="submit-ajax btn" onclick="viewCategory()">View</button>
                                    <button class="clear-ajax btn" onclick="clearCategories()">Clear</button>
                                </div>
                            </div>
                            <table id="view-category-table">
                                <thead>
                                    <th>name</th>
                                    <th>parent</th>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="flex-row">
                        <?php if(userHasPrivilege($_SESSION["user_id"], "category_add")) { ?>
                            <div class="container">
                                <h2>New category</h2>
                                <form autocomplete="off" method="post" class="spaced">
                                    <input type="text" name="name" placeholder="category name">
                                    <select name="parent">
                                        <option value="">none</option>
                                        <?=$categoryData?>
                                    </select>
                                    <label for="parent">Parent</label>
                                    <input type="submit" value="Submit">
                                    <input type="hidden" name="action" value="new_category">
                                </form>
                            </div>
                        <?php }
                        if (userHasPrivilege($_SESSION["user_id"], "category_remove")) { ?>
                            <div class="container">
                                <h2>Remove category</h2>
                                <form autocomplete="off" method="post" class="spaced">
                                    <select name="category">
                                        <?=$categoryData?>
                                    </select>
                                    <input type="submit" value="Submit">
                                    <input type="hidden" name="action" value="remove_category">
                                </form>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } if(userPrivilegeStartsWith($_SESSION["user_id"], "order")) { ?>
                <div class="admin-tab" id="orders-tab">
                    <h1>Orders</h1>
                    <table id="orders-table">
                        <thead>
                            <th>id</th>
                            <th>postal_code</th>
                            <th>delivery_address</th>
                            <th>email</th>
                            <th>full_name</th>
                            <th>time_added</th>
                            <th>products</th>
                        </thead> <?php
                        foreach($orders as $order) {
                            $orderItems = getItemsFromOrder($order["id"]);
                            $itemsString = orderItemsToJoinedString($orderItems);
                            ?>
                            <tr>
                                <td><?=$order["id"]?></td>
                                <td><?=$order["postal_code"]?></td>
                                <td><?=$order["delivery_address"]?></td>
                                <td><?=$order["email"]?></td>
                                <td><?=$order["full_name"]?></td>
                                <td><?=$order["time_added"]?></td>
                                <td><?=$itemsString?></td>
                            </tr> <?php
                        } ?>
                    </table>
                    <div class="flex-row">
                        <?php if(userHasPrivilege($_SESSION["user_id"], "order_ship")) { ?>
                            <div class="container">
                                <h2>Mark as shipped</h2>
                                <form autocomplete="off" method="post" enctype="multipart/form-data" class="spaced">
                                    <input type="text" name="id" placeholder="order id">
                                    <input type="submit" value="Submit">
                                    <input type="hidden" name="action" value="ship_order">
                                </form>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } if(userPrivilegeStartsWith($_SESSION["user_id"], "item")) { ?>
                <div class="admin-tab" id="items-tab">
                    <h1>Items</h1>
                    <div id="view-item" class="container">
                        <div class="flex-row">
                            <div class="spaced">
                                <h2>View item</h2>
                                <div class="input-suggest">
                                    <input class="autocomplete-item" id="view-item-field" type="text" placeholder="product id/title" autocomplete="off">
                                </div>
                                <div class="flex-row spaced">
                                    <button class="submit-ajax btn" onclick="viewItem()">View</button>
                                    <button class="clear-ajax btn" onclick="clearItems()">Clear</button>
                                </div>
                            </div>
                            <table id="view-item-table">
                                <thead>
                                    <th>id</th>
                                    <th>title</th>
                                    <th>manufacturer</th>
                                    <th>description</th>
                                    <th>date_added</th>
                                    <th>image</th>
                                    <th>price</th>
                                    <th>category</th>
                                    <th>visible</th>
                                    <th>extra_images</th>
                                    <th>sale</th>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="flex-row">
                        <?php if(userHasPrivilege($_SESSION["user_id"], "item_add")) { ?>
                            <div class="container">
                                <h2>Add item</h2>
                                <form autocomplete="off" method="post" enctype="multipart/form-data" class="spaced">
                                    <input type="text" name="name" placeholder="name">
                                    <input type="file" name="image" placeholder="image">
                                    <input type="text" name="price" placeholder="price">
                                    <select name="category">
                                        <?=$categoryData?>
                                    </select>
                                    <label for="category">Category</label>
                                    <input type="submit" value="Submit">
                                    <input type="hidden" name="action" value="add_item">
                                </form>
                            </div>
                        <?php }
                        if (userHasPrivilege($_SESSION["user_id"], "item_remove")) { ?>
                            <div class="container">
                                <h2>Remove item</h2>
                                <form autocomplete="off" method="post" class="spaced">
                                    <div class="input-suggest">
                                        <input class="autocomplete-item" id="remove-item-field" type="text" name="id" placeholder="product id/title">
                                    </div>
                                    <input type="submit" value="Submit">
                                    <input type="hidden" name="action" value="remove_item">
                                </form>
                            </div>
                        <?php }
                        if (userHasPrivilege($_SESSION["user_id"], "item_description")) { ?>
                            <div class="container">
                                <h2>Change item description</h2>
                                <form autocomplete="off" method="post" class="spaced">
                                    <div class="input-suggest">
                                        <input class="autocomplete-item" id="item-description-field" type="text" name="id" placeholder="product id/title">
                                    </div>
                                    <textarea name="description" placeholder="description"></textarea>
                                    <input type="submit" value="Submit">
                                    <input type="hidden" name="action" value="item_description">
                                </form>
                            </div>
                        <?php }
                        if (userHasPrivilege($_SESSION["user_id"], "item_image")) { ?>
                            <div class="container">
                                <h2>Change item image</h2>
                                <form autocomplete="off" method="post" enctype="multipart/form-data" class="spaced">
                                    <div class="input-suggest">
                                        <input class="autocomplete-item" id="item-image-field" type="text" name="id" placeholder="product id/title">
                                    </div>
                                    <input type="file" name="image" placeholder="image">
                                    <input type="submit" value="Submit">
                                    <input type="hidden" name="action" value="item_image">
                                </form>
                            </div>
                        <?php }
                        if (userHasPrivilege($_SESSION["user_id"], "item_manufacturer")) { ?>
                            <div class="container">
                                <h2>Change item manufacturer</h2>
                                <form autocomplete="off" method="post" class="spaced">
                                    <div class="input-suggest">
                                        <input class="autocomplete-item" id="item-name-manufacturer-field" type="text" name="id" placeholder="product id/title">
                                    </div>
                                    <div class="input-suggest">
                                        <input class="autocomplete-manufacturer" id="item-manufacturer-field" type="text" name="manufacturer" placeholder="manufacturer">
                                    </div>
                                    <input type="submit" value="Submit">
                                    <input type="hidden" name="action" value="item_manufacturer">
                                </form>
                            </div>
                        <?php }
                        if (userHasPrivilege($_SESSION["user_id"], "item_price")) { ?>
                            <div class="container">
                                <h2>Change item price</h2>
                                <form autocomplete="off" method="post" class="spaced">
                                    <div class="input-suggest">
                                        <input class="autocomplete-item" id="item-price-field" type="text" name="id" placeholder="product id/title">
                                    </div>
                                    <input type="text" name="price" placeholder="price">
                                    <input type="submit" value="Submit">
                                    <input type="hidden" name="action" value="item_price">
                                </form>
                            </div>
                        <?php }
                        if (userHasPrivilege($_SESSION["user_id"], "item_category")) { ?>
                            <div class="container">
                                <h2>Change item category</h2>
                                <form autocomplete="off" method="post" class="spaced">
                                    <div class="input-suggest">
                                        <input class="autocomplete-item" id="item-category-field" type="text" name="id" placeholder="product id/title">
                                    </div>
                                    <select name="category">
                                        <?=$categoryData?>
                                    </select>
                                    <input type="submit" value="Submit">
                                    <input type="hidden" name="action" value="item_category">
                                </form>
                            </div>
                        <?php }
                        if (userHasPrivilege($_SESSION["user_id"], "item_visible")) { ?>
                            <div class="container">
                                <h2>Make item visible</h2>
                                <form autocomplete="off" method="post" class="spaced">
                                    <div class="input-suggest">
                                        <input class="autocomplete-item" id="item-visible-field" type="text" name="id" placeholder="product id/title">
                                    </div>
                                    <input type="checkbox" name="visible">
                                    <label for="visible">Visible</label>
                                    <input type="submit" value="Submit">
                                    <input type="hidden" name="action" value="item_visible">
                                </form>
                            </div>
                        <?php }
                        if (userHasPrivilege($_SESSION["user_id"], "item_add_image")) { ?>
                            <div class="container">
                                <h2>Add image to item</h2>
                                <form autocomplete="off" method="post" enctype="multipart/form-data" class="spaced">
                                    <div class="input-suggest">
                                        <input class="autocomplete-item" id="item-add-image-field" type="text" name="id" placeholder="product id/title">
                                    </div>
                                    <input type="file" name="image" placeholder="image">
                                    <input type="submit" value="Submit">
                                    <input type="hidden" name="action" value="item_add_image">
                                </form>
                            </div>
                        <?php }
                        if (userHasPrivilege($_SESSION["user_id"], "item_remove_image")) { ?>
                            <div class="container">
                                <h2>Remove image from item</h2>
                                <form autocomplete="off" method="post" class="spaced">
                                    <div class="input-suggest">
                                        <input class="autocomplete-item" id="item-remove-image-field" type="text" name="id" placeholder="product id/title">
                                    </div>
                                    <select name="image" id="item-remove-image-list">
                                    </select>
                                    <input type="submit" value="Submit">
                                    <input type="hidden" name="action" value="item_remove_image">
                                </form>
                            </div>
                        <?php } if(userHasPrivilege($_SESSION["user_id"], "item_sale_percent")) { ?>
                            <div class="container">
                                <h2>Put item on sale (%)</h2>
                                <form autocomplete="off" method="post" class="spaced">
                                    <div class="input-suggest">
                                        <input class="autocomplete-item" id="sale-percent-field" type="text" name="id" placeholder="product id/title">
                                    </div>
                                    <input type="text" name="percentage" placeholder="percentage">
                                    <input type="date" name="date">
                                    <input type="time" name="time">
                                    <input type="submit" value="Submit">
                                    <input type="hidden" name="action" value="sale_percent">
                                </form>
                            </div>
                        <?php }
                        if (userHasPrivilege($_SESSION["user_id"], "item_sale_absolute")) { ?>
                            <div class="container">
                                <h2>Put item on sale (absolute)</h2>
                                <form autocomplete="off" method="post" class="spaced">
                                    <div class="input-suggest">
                                        <input class="autocomplete-item" id="sale-absolute-field" type="text" name="id" placeholder="product id/title">
                                    </div>
                                    <input type="text" name="price" placeholder="price">
                                    <input type="date" name="date">
                                    <input type="time" name="time">
                                    <input type="submit" value="Submit">
                                    <input type="hidden" name="action" value="sale_absolute">
                                </form>
                            </div>
                        <?php }
                        if (userHasPrivilege($_SESSION["user_id"], "item_sale_revoke")) { ?>
                            <div class="container">
                                <h2>Revoke sale</h2>
                                <form autocomplete="off" method="post" class="spaced">
                                    <div class="input-suggest">
                                        <input class="autocomplete-item" id="sale-revoke-field" type="text" name="id" placeholder="product id/title">
                                    </div>
                                    <input type="submit" value="Submit">
                                    <input type="hidden" name="action" value="revoke_sale">
                                </form>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } if(userPrivilegeStartsWith($_SESSION["user_id"], "manufacturer")) { ?>
                <div class="admin-tab" id="manufacturers-tab">
                    <h1>Manufacturers</h1>
                    <div class="flex-row">
                        <?php if(userHasPrivilege($_SESSION["user_id"], "manufacturer_add")) { ?>
                            <div class="container">
                                <h2>Add manufacturer</h2>
                                <form autocomplete="off" method="post" class="spaced">
                                    <input type="text" name="name" placeholder="manufacturer name">
                                    <input type="submit" value="Submit">
                                    <input type="hidden" name="action" value="add_manufacturer">
                                </form>
                            </div>
                        <?php }
                        if (userHasPrivilege($_SESSION["user_id"], "manufacturer_remove")) { ?>
                            <div class="container">
                                <h2>Remove manufacturer</h2>
                                <form autocomplete="off" method="post" class="spaced">
                                    <div class="input-suggest">
                                        <input class="autocomplete-manufacturer" id="manufacturer-remove-field" type="text" name="name" placeholder="manufacturer name">
                                    </div>
                                    <input type="submit" value="Submit">
                                    <input type="hidden" name="action" value="remove_manufacturer">
                                </form>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } if(userPrivilegeStartsWith($_SESSION["user_id"], "user")) { ?>
                <div class="admin-tab" id="users-tab">
                    <h1>Users</h1>
                    <div id="view-user" class="container">
                        <div class="flex-row">
                            <div class="spaced">
                                <h2>View user</h2>
                                <div class="input-suggest">
                                    <input class="autocomplete-username" id="view-user-field" type="text" name="username" placeholder="username" autocomplete="off">
                                </div>
                                <div class="flex-row spaced">
                                    <button class="submit-ajax btn" onclick="viewUser()">View</button>
                                    <button class="clear-ajax btn" onclick="clearUsers()">Clear</button>
                                </div>
                            </div>
                            <table id="view-user-table">
                                <thead>
                                    <th>id</th>
                                    <th>name</th>
                                    <th>email</th>
                                    <th>first_name</th>
                                    <th>last_name</th>
                                    <th>birth_date</th>
                                    <th>delivery_address</th>
                                    <th>zipcode</th>
                                    <th>privileges</th>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="flex-row">
                        <?php if(userHasPrivilege($_SESSION["user_id"], "user_add")) { ?>
                            <div class="container">
                                <h2>Add user</h2>
                                <form autocomplete="off" method="post" class="spaced">
                                    <input type="text" name="username" placeholder="username">
                                    <input type="password" name="password" placeholder="password">
                                    <input type="submit" value="Submit">
                                    <input type="hidden" name="action" value="add_user">
                                </form>
                            </div>
                        <?php }
                        if (userHasPrivilege($_SESSION["user_id"], "user_remove")) { ?>
                            <div class="container">
                                <h2>Remove user</h2>
                                <form autocomplete="off" method="post" class="spaced">
                                    <div class="input-suggest">
                                        <input class="autocomplete-username" id="remove-user-field" type="text" name="username" placeholder="username">
                                    </div>
                                    <input type="submit" value="Submit">
                                    <input type="hidden" name="action" value="remove_user">
                                </form>
                            </div>
                        <?php }
                        if (userHasPrivilege($_SESSION["user_id"], "user_grant_privilege")) { ?>
                            <div class="container">
                                <h2>Grant privilege</h2>
                                <form autocomplete="off" method="post" class="spaced">
                                    <div class="input-suggest">
                                        <input class="autocomplete-username" id="grant-privilege-field" type="text" name="username" placeholder="username">
                                    </div>
                                    <select name="privilege">
                                        <?=$privilegeData?>
                                    </select>
                                    <input type="submit" value="Submit">
                                    <input type="hidden" name="action" value="grant_privilege">
                                </form>
                            </div>
                        <?php }
                        if (userHasPrivilege($_SESSION["user_id"], "user_revoke_privilege")) { ?>
                            <div class="container">
                                <h2>Revoke privilege</h2>
                                <form autocomplete="off" method="post" class="spaced">
                                    <div class="input-suggest">
                                        <input class="autocomplete-username" id="revoke-privilege-field" type="text" name="username" placeholder="username">
                                    </div>
                                    <select name="privilege">
                                        <?=$privilegeData?>
                                    </select>
                                    <input type="submit" value="Submit">
                                    <input type="hidden" name="action" value="revoke_privilege">
                                </form>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
        <script src="js/core.js"></script>
        <script src="js/input.js"></script>
        <script src="js/admin.js"></script>
    </body>
</html>
