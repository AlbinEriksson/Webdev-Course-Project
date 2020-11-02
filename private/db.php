<?php
$db = new mysqli();

function connectDb(): mysqli {
    // $db = new mysqli("<ADDRESS>", "<USERNAME>", "<PASSWORD>", "<DATABASE>");
    $db = new mysqli("localhost", "root", "", "dva231_project_test");
    if($db->connect_errno) {
        die("Failed to reach DB: " . mysqli_connect_error());
    }
    return $db;
}

function prepareStmt($sql): mysqli_stmt {
    global $db;
    $stmt = $db->prepare($sql);
    if(!$stmt) {
        die("Failed to prepare SQL statement: (" . $db->errno . ") " . $db->error);
    }
    return $stmt;
}

/**
 * Executes a prepared statement.
 * @param mysqli_stmt $stmt The prepared statement.
 */
function executeStmt(mysqli_stmt $stmt, bool $close = true): ?mysqli_result {
    if(!$stmt->execute()) {
        die("Statement execution failed: (" . $stmt->errno . ") " . $stmt->error);
    }
    $result = $stmt->get_result();
    if($close) {
        $stmt->close();
    }
    if($result === false) {
        return null;
    }
    return $result;
}

$db = connectDb();

function userExists($username) {
    $stmt = prepareStmt("SELECT count(*) FROM users WHERE name=?");
    $stmt->bind_param("s", $username);
    $result = executeStmt($stmt);
    $resultValue = $result->fetch_row();
    return $resultValue[0] == 1;
}

function privilegeExists($privilege) {
    $stmt = prepareStmt("SELECT count(*) FROM privileges WHERE name=?");
    $stmt->bind_param("s", $privilege);
    $result = executeStmt($stmt);
    $resultValue = $result->fetch_row();
    return $resultValue[0] == 1;
}

function itemExists($id) {
    $stmt = prepareStmt("SELECT count(*) FROM items WHERE id=?");
    $stmt->bind_param("i", $id);
    $result = executeStmt($stmt);
    $resultValue = $result->fetch_row();
    return $resultValue[0] == 1;
}

function manufacturerExists($name) {
    $stmt = prepareStmt("SELECT count(*) FROM manufacturers WHERE name=?");
    $stmt->bind_param("s", $name);
    $result = executeStmt($stmt);
    $resultValue = $result->fetch_row();
    return $resultValue[0] == 1;
}

function categoryExists($name) {
    $stmt = prepareStmt("SELECT count(*) FROM categories WHERE name=?");
    $stmt->bind_param("s", $name);
    $result = executeStmt($stmt);
    $resultValue = $result->fetch_row();
    return $resultValue[0] == 1;
}

function getUserFromID($id) {
    $stmt = prepareStmt("SELECT id,name,email,first_name,last_name,birth_date,delivery_address,zipcode FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    $result = executeStmt($stmt);
    return $result->fetch_assoc();
}

function getUserFromName($username): ?array {
    $stmt = prepareStmt("SELECT id,name,email,first_name,last_name,birth_date,delivery_address,zipcode FROM users WHERE name=?");
    $stmt->bind_param("s", $username);
    $result = executeStmt($stmt);
    if($result->num_rows == 0) {
        return null;
    } else {
        return $result->fetch_assoc();
    }
}

function getUserIDFromName($username): int {
    $stmt = prepareStmt("SELECT id FROM users WHERE name=?");
    $stmt->bind_param("s", $username);
    $result = executeStmt($stmt);
    if($result->num_rows == 1) {
        return $result->fetch_row()[0];
    } else {
        return -1;
    }
}

function userHasPrivileges($id) {
    $stmt = prepareStmt("SELECT count(*) FROM user_privileges WHERE user_id=?");
    $stmt->bind_param("i", $id);
    $result = executeStmt($stmt);
    $resultValue = $result->fetch_row();
    return $resultValue[0] >= 1;
}

function userHasPrivilege($id, $privilege) {
    $stmt = prepareStmt("SELECT count(*) FROM user_privileges WHERE user_id=? AND (privilege=? OR privilege='root')");
    $stmt->bind_param("is", $id, $privilege);
    $result = executeStmt($stmt);
    $resultValue = $result->fetch_row();
    return $resultValue[0] >= 1;
}

function userPrivilegeStartsWith($id, $privilege) {
    $stmt = prepareStmt("SELECT count(*) FROM user_privileges WHERE user_id=? AND (privilege LIKE CONCAT(?, '%') OR privilege='root')");
    $stmt->bind_param("is", $id, $privilege);
    $result = executeStmt($stmt);
    $resultValue = $result->fetch_row();
    return $resultValue[0] >= 1;
}

function signedInUserHasPrivilege($privilege) {
    return isset($_SESSION["user_id"]) && userHasPrivilege($_SESSION["user_id"], $privilege);
}

function signedInUserHasAdminAccess() {
    return isset($_SESSION["user_id"]) && userHasPrivileges($_SESSION["user_id"]);
}

function getItemFromID($id): ?array {
    $stmt = prepareStmt("SELECT id,title,manufacturer,description,date_added,image,price,category,visible FROM items WHERE id=?");
    $stmt->bind_param("i", $id);
    $result = executeStmt($stmt);
    if($result->num_rows == 0) {
        return null;
    } else {
        return $result->fetch_assoc();
    }
}

function getItemNameFromID($id): ?string {
    $stmt = prepareStmt("SELECT title FROM items WHERE id=?");
    $stmt->bind_param("i", $id);
    $result = executeStmt($stmt);
    if($result->num_rows == 1) {
        return $result->fetch_row()[0];
    } else {
        return null;
    }
}

function isItemOnSale($id) {
    $stmt = prepareStmt("SELECT COUNT(*) FROM sales WHERE item_id=?");
    $stmt->bind_param("i", $id);
    $result = executeStmt($stmt);
    $resultValue = $result->fetch_row();
    return $resultValue[0] == 1;
}

function getItemSale($id): ?array {
    $stmt = prepareStmt("SELECT percentage,deadline FROM sales WHERE item_id=? AND deadline > NOW()");
    $stmt->bind_param("i", $id);
    $result = executeStmt($stmt);
    if($result->num_rows == 0) {
        return null;
    } else {
        return $result->fetch_assoc();
    }
}

function getExtraItemImages($id) {
    $stmt = prepareStmt("SELECT image FROM item_images WHERE item_id=?");
    $stmt->bind_param("i", $id);
    $result = executeStmt($stmt);
    $resultValue = array();
    while($row = $result->fetch_row()) {
        $resultValue[] = $row[0];
    }
    return $resultValue;
}

function isLoggedIn() {
    return isset($_SESSION["logged_in"]) && $_SESSION["logged_in"];
}

function reviewExists($userid, $itemid) {
    $stmt = prepareStmt("SELECT COUNT(*) FROM reviews WHERE user_id=? AND item_id=?");
    $stmt->bind_param("ii", $userid, $itemid);
    $result = executeStmt($stmt);
    $resultValue = $result->fetch_row();
    return $resultValue[0] == 1;
}

function addItem($name, $imageName, $price, $category) {
    $stmt = prepareStmt("INSERT INTO items(title,image,price,category) VALUES (?,?,?,?)");
    $stmt->bind_param("ssss", $name, $imageName, $price, $category);
    executeStmt($stmt);
}

function addManufacturer($name) {
    $stmt = prepareStmt("INSERT INTO manufacturers(name) VALUES(?)");
    $stmt->bind_param("s", $name);
    executeStmt($stmt);
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

function addUser($username, $password) {
    $stmt = prepareStmt("INSERT INTO users(name,password) VALUES (?,?)");
    $pw_hashed = hashPassword($password);
    $stmt->bind_param("ss", $username, $pw_hashed);
    executeStmt($stmt);
}

function grantPrivilege($userid, $privilege) {
    $stmt = prepareStmt("INSERT INTO user_privileges(user_id,privilege) VALUES (?,?)");
    $stmt->bind_param("is", $userid, $privilege);
    executeStmt($stmt);
}

function addExtraProductImage($itemid, $filename) {
    $stmt = prepareStmt("INSERT INTO item_images(item_id,image) VALUES(?,?)");
    $stmt->bind_param("is", $itemid, $filename);
    executeStmt($stmt);
}

function removeExtraProductImage($itemid, $filename) {
    $stmt = prepareStmt("DELETE FROM item_images WHERE item_id=? AND image=?");
    $stmt->bind_param("is", $itemid, $filename);
    executeStmt($stmt);
}

function removeAllExtraProductImages($itemid) {
    $stmt = prepareStmt("DELETE FROM item_images WHERE item_id=?");
    $stmt->bind_param("i", $itemid);
    executeStmt($stmt);
}

function setItemImage($itemid, $filename) {
    $stmt = prepareStmt("UPDATE items SET image=? WHERE id=?");
    $stmt->bind_param("si", $filename, $itemid);
    executeStmt($stmt);
}

function setItemCategory($itemid, $category) {
    $stmt = prepareStmt("UPDATE items SET category=? WHERE id=?");
    $stmt->bind_param("si", $category, $itemid);
    executeStmt($stmt);
}

function setItemDescription($itemid, $description) {
    $stmt = prepareStmt("UPDATE items SET description=? WHERE id=?");
    $stmt->bind_param("si", $description, $itemid);
    executeStmt($stmt);
}

function itemImageUsageCount($filename): int {
    $stmt = prepareStmt("SELECT count(*) FROM items WHERE image=?");
    $stmt->bind_param("s", $filename);
    $result = executeStmt($stmt);
    $resultValue = $result->fetch_row();
    return $resultValue[0];
}

function extraItemImageUsageCount($filename): int {
    $stmt = prepareStmt("SELECT count(*) FROM item_images WHERE image=?");
    $stmt->bind_param("s", $filename);
    $result = executeStmt($stmt);
    $resultValue = $result->fetch_row();
    return $resultValue[0];
}

function setItemManufacturer($itemid, $manufacturer): void {
    $stmt = prepareStmt("UPDATE items SET manufacturer=? WHERE id=?");
    $stmt->bind_param("si", $manufacturer, $itemid);
    executeStmt($stmt);
}

function setItemPrice($itemid, $price): void {
    $stmt = prepareStmt("UPDATE items SET price=? WHERE id=?");
    $stmt->bind_param("si", $price, $itemid);
    executeStmt($stmt);
}

function setItemVisibility($itemid, $visible): void {
    $stmt = prepareStmt("UPDATE items SET visible=? WHERE id=?");
    $stmt->bind_param("ii", $visible, $itemid);
    executeStmt($stmt);
}

function addCategory($name, $parent): void {
    $stmt = prepareStmt("INSERT INTO categories(name,parent) VALUES(?,?)");
    $stmt->bind_param("ss", $name, $parent);
    executeStmt($stmt);
}

function removeCategory($name): void {
    $stmt = prepareStmt("DELETE FROM categories WHERE name=?");
    $stmt->bind_param("s", $name);
    executeStmt($stmt);
}

function getCategory(string $name): ?array {
    $stmt = prepareStmt("SELECT name,parent FROM categories WHERE name=?");
    $stmt->bind_param("s", $name);
    $result = executeStmt($stmt);

    if($result->num_rows == 0) {
        return null;
    } else {
        return $result->fetch_assoc();
    }
}

function removeItem($itemid): void {
    $stmt = prepareStmt("DELETE FROM items WHERE id=?");
    $stmt->bind_param("i", $itemid);
    executeStmt($stmt);
}

function removeManufacturer($name): void {
    $stmt = prepareStmt("DELETE FROM manufacturers WHERE name=?");
    $stmt->bind_param("s", $name);
    executeStmt($stmt);
}

function removeUserReviews($userid): void {
    $stmt = prepareStmt("DELETE FROM reviews WHERE user_id=?");
    $stmt->bind_param("i", $userid);
    executeStmt($stmt);
}

function removeItemReviews($itemid): void {
    $stmt = prepareStmt("DELETE FROM reviews WHERE item_id=?");
    $stmt->bind_param("i", $itemid);
    executeStmt($stmt);
}

function removeReview($userid, $itemid): void {
    $stmt = prepareStmt("DELETE FROM reviews WHERE item_id=? AND user_id=?");
    $stmt->bind_param("ii", $itemid, $userid);
    executeStmt($stmt);
}

function removeUserPrivileges($userid): void {
    $stmt = prepareStmt("DELETE FROM user_privileges WHERE user_id=?");
    $stmt->bind_param("i", $userid);
    executeStmt($stmt);
}

function removeUser($userid): void {
    $stmt = prepareStmt("DELETE FROM users WHERE id=?");
    $stmt->bind_param("s", $userid);
    executeStmt($stmt);
}

function revokePrivilege($userid, $privilege): void {
    $stmt = prepareStmt("DELETE FROM user_privileges WHERE user_id=? AND privilege=?");
    $stmt->bind_param("is", $userid, $privilege);
    executeStmt($stmt);
}

function revokeSale($itemid): void {
    $stmt = prepareStmt("DELETE FROM sales WHERE item_id=?");
    $stmt->bind_param("i", $itemid);
    executeStmt($stmt);
}

function putItemOnSale($itemid, $percentage, $deadline): void {
    $stmt = prepareStmt("INSERT INTO sales(item_id,percentage,deadline) VALUES(?,?,?)");
    $stmt->bind_param("iss", $itemid, $percentage, $deadline);
    executeStmt($stmt);
}

function getChildrenOfCategory($category): mysqli_result {
    $stmt = prepareStmt("SELECT name FROM categories WHERE parent=? ORDER BY name");
    $stmt->bind_param("s", $category);
    return executeStmt($stmt);
}

function getItemNamesFromManufacturer($manufacturer): mysqli_result {
    $stmt = prepareStmt("SELECT title FROM items WHERE manufacturer=?");
    $stmt->bind_param("s", $manufacturer);
    return executeStmt($stmt);
}

function filterItems(string $searchTerm, string $maxPrice, int $startIndex, int $howMany,
    string $categoryList, string $manufacturerClause): mysqli_result {
    $stmt = prepareStmt("
        SELECT id,title,image,price,percentage
        FROM items
        LEFT JOIN sales
        ON item_id=id
        AND deadline > NOW()
        WHERE visible=TRUE
        AND title LIKE CONCAT('%', ?, '%')
        AND category IN $categoryList
        $manufacturerClause
        AND price <= ?
        LIMIT ?,?
    ");
    $stmt->bind_param("sdii", $searchTerm,  $maxPrice, $startIndex, $howMany);
    return executeStmt($stmt);
}

function filterItemCount(string $searchTerm, string $maxPrice, string $categoryList,
    string $manufacturerClause): int {
    
    $stmt = prepareStmt("
        SELECT COUNT(*)
        FROM items
        WHERE visible=TRUE
        AND title LIKE CONCAT('%', ?, '%')
        AND category IN $categoryList
        $manufacturerClause
        AND price <= ?
    ");
    $stmt->bind_param("sd", $searchTerm, $maxPrice);
    $result = executeStmt($stmt);
    return $result->fetch_row()[0];
}

function filterPriceRange(string $categoryList, string $manufacturerClause): array {
    $stmt = prepareStmt("
        SELECT FLOOR(MIN(price)) as min,CEIL(MAX(price)) as max
        FROM items
        WHERE visible=TRUE
        AND category IN $categoryList
        $manufacturerClause
    ");
    $result = executeStmt($stmt);
    return $result->fetch_assoc();
}

function getManufacturersInCategories(array $categoryList): array {
    $categories = constructSqlQueryStringList($categoryList);
    $stmt = prepareStmt("SELECT DISTINCT manufacturer FROM items WHERE category IN $categories");
    $result = executeStmt($stmt);

    $output = array();
    while($row = $result->fetch_row()) {
        $output[] = $row[0];
    }

    return $output;
}

function getPrivilegesForUser(int $userid): array {
    $stmt = prepareStmt("SELECT privilege FROM user_privileges WHERE user_id=?");
    $stmt->bind_param("i", $userid);
    $result = executeStmt($stmt);

    $output = array();
    while($row = $result->fetch_row()) {
        $output[] = $row[0];
    }

    return $output;
}

function searchItemsByName(string $name, int $limit): array {
    $stmt = prepareStmt("SELECT title AS name,id FROM items WHERE title LIKE CONCAT(?, '%') LIMIT ?");
    $stmt->bind_param("si", $_GET['itemname'], $limit);
    $result = executeStmt($stmt);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function searchManufacturersByName(string $name, int $limit): array {
    $stmt = prepareStmt("SELECT name FROM manufacturers WHERE name LIKE CONCAT(?, '%') LIMIT ?");
    $stmt->bind_param("si", $name, $limit);
    $result = executeStmt($stmt);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function searchUsernames(string $name, int $limit): array {
    $stmt = prepareStmt("SELECT name,id FROM users WHERE name LIKE CONCAT(?, '%') LIMIT ?");
    $stmt->bind_param("si", $name, $limit);
    $result = executeStmt($stmt);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function updateReview(int $userid, int $itemid, int $rating, string $reviewText): void {
    $stmt = prepareStmt("UPDATE reviews SET rating=?, text=? WHERE item_id=? AND user_id=?");
    $stmt->bind_param("isii", $rating, $reviewText, $itemid, $userid);
    executeStmt($stmt);
}

function createNewReview(int $userid, int $itemid, int $rating, string $reviewText): void {
    $stmt = prepareStmt("INSERT INTO reviews(rating,text,item_id,user_id) VALUES(?,?,?,?)");
    $stmt->bind_param("isii", $rating, $reviewText, $itemid, $userid);
    executeStmt($stmt);
}

function getItemsInCart($cartItemIDs) {
    $stmt = prepareStmt("SELECT id,title,image,price,percentage FROM items LEFT JOIN sales ON item_id=id AND deadline > NOW() WHERE id IN $cartItemIDs");
    return executeStmt($stmt);
}

function addUserDetailed($username, $password, $email, $firstName, $lastName, $birthDate, $address, $postalCode) {
    $stmt = prepareStmt("INSERT INTO users(name,password,email,first_name,last_name,birth_date,delivery_address,zipcode) values (?,?,?,?,?,?,?,?)");
    $pw_hashed = hashPassword($password);
    $stmt->bind_param("ssssssss", $username, $pw_hashed, $email, $firstName, $lastName, $birthDate, $address, $postalCode);
    executeStmt($stmt);
}

function getFrontPageItems($orderedBy, bool $mustBeOnSale): array {
    $joinType = $mustBeOnSale ? "INNER" : "LEFT";
    $stmt = prepareStmt("
        SELECT id,title,image,price,percentage
        FROM items
        $joinType JOIN sales
        ON item_id=id
        AND deadline > NOW()
        WHERE visible=TRUE
        ORDER BY $orderedBy DESC
        LIMIT 4
    ");
    $result = executeStmt($stmt);
    $output = array();
    while($item = $result->fetch_assoc()) {
        $output[] = $item;
    }
    return $output;
}

function computeAverageRating($itemid): array {
    $stmt = prepareStmt("SELECT AVG(rating),COUNT(*) FROM reviews WHERE item_id=?");
    $stmt->bind_param("i", $itemid);
    $result = executeStmt($stmt);
    return $result->fetch_row();
}

function getReview($itemid, $userid): ?array {
    $stmt = prepareStmt("SELECT name,rating,text FROM reviews LEFT JOIN users ON user_id=id WHERE item_id=? AND user_id=?");
    $stmt->bind_param("ii", $itemid, $userid);
    $result = executeStmt($stmt);
    if($result->num_rows == 0) {
        return null;
    } else {
        return $result->fetch_assoc();
    }
}

function getReviewsFromOtherUsers($itemid, $userid): array {
    $stmt = prepareStmt("SELECT name,rating,text FROM reviews LEFT JOIN users ON user_id=id WHERE item_id=? AND NOT user_id=?");
    $stmt->bind_param("ii", $itemid, $userid);
    $result = executeStmt($stmt);
    $output = array();
    while($item = $result->fetch_assoc()) {
        $output[] = $item;
    }
    return $output;
}

function getReviewsOnItem($itemid): array {
    $stmt = prepareStmt("SELECT name,rating,text FROM reviews LEFT JOIN users ON user_id=id WHERE item_id=?");
    $stmt->bind_param("i", $itemid);
    $result = executeStmt($stmt);
    $output = array();
    while($item = $result->fetch_assoc()) {
        $output[] = $item;
    }
    return $output;
}

function createNewOrder($postalCode, $address, $email, $fullName): int {
    global $db;
    $stmt = prepareStmt("INSERT INTO orders(postal_code,delivery_address,email,full_name) VALUES(?,?,?,?)");
    $stmt->bind_param("ssss", $postalCode, $address, $email, $fullName);
    executeStmt($stmt);
    return mysqli_insert_id($db);
}

function removeOrderItems($orderid): void {
    $stmt = prepareStmt("DELETE FROM order_products WHERE order_id=?");
    $stmt->bind_param("i", $orderid);
    executeStmt($stmt);
}

function removeOrder($orderid): void {
    $stmt = prepareStmt("DELETE FROM orders WHERE id=?");
    $stmt->bind_param("i", $orderid);
    executeStmt($stmt);
}

function orderExists($orderid): bool {
    $stmt = prepareStmt("SELECT COUNT(*) FROM orders WHERE id=?");
    $stmt->bind_param("i", $orderid);
    $result = executeStmt($stmt);
    return $result->fetch_row()[0] == 1;
}

function getItemsFromOrder($orderid): array {
    $stmt = prepareStmt("SELECT id,title,quantity FROM order_products LEFT JOIN items ON item_id=id WHERE order_id=?");
    $stmt->bind_param("i", $orderid);
    $result = executeStmt($stmt);
    $output = array();
    while($item = $result->fetch_assoc()) {
        $output[] = $item;
    }
    return $output;
}

function isItemInAnyOrder($itemid): bool {
    $stmt = prepareStmt("SELECT COUNT(*) FROM order_products WHERE item_id=?");
    $stmt->bind_param("i", $itemid);
    $result = executeStmt($stmt);
    $resultValue = $result->fetch_row();
    return $resultValue[0] > 0;
}
?>
