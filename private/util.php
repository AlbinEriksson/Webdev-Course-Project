<?php
if(session_status() == PHP_SESSION_NONE) {
    session_start();
}

include_once($_SERVER["DOCUMENT_ROOT"] . "/private/result.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/private/db.php");

function fileIsImage($file): bool {
    return getimagesize($file) !== false;
}

/**
 * Used for moving a temporary file from POST forms to a non-temporary location.
 * If this function returns true, the caller should call successResult() with proper result message.
 * This function will still return true if the target file already exists, and will add a
 * noteResult() when this happens. The caller should still call successResult() as usual.
 * @param string $sourcePath The temporary file location.
 * @param string $targetPath The destination (non-temporary).
 */
function imageUpload($sourcePath, $targetPath): bool {
    if(!fileIsImage($sourcePath)) {
        errorResult("File was not an image.");
        return false;
    }

    $fileAlreadyExists = file_exists($targetPath);

    if($fileAlreadyExists) {
        noteResult("Used already existing file.");
    } else {
        if(!move_uploaded_file($sourcePath, $targetPath)) {
            errorResult("File upload failed.");
            return false;
        }
    }

    return true;
}

/**
 * Verifies that a username exists. If it returns true, callers should call successResult().
 * @param string $username The username.
 */
function verifyUsernameExists($username): bool {
    if(!userExists($username)) {
        errorResult("User '$username' does not exist!");
        return false;
    }
    return true;
}

/**
 * Verifies that a username does not exist. If it returns true, callers should call successResult().
 * @param string $username The username.
 */
function verifyUsernameDoesNotExist($username): bool {
    if(userExists($username)) {
        errorResult("That username is taken!");
        return false;
    }
    return true;
}

/**
 * Verifies that a privilege exists. If it returns true, callers should call successResult().
 * @param string $privilege The privilege.
 */
function verifyPrivilegeExists($privilege): bool {
    if(!privilegeExists($privilege)) {
        errorResult("Privilege '$privilege' does not exist!");
        return false;
    }
    return true;
}

/**
 * Verifies that a product ID exists. If it returns true, callers should call successResult().
 * @param string $itemid The product ID.
 */
function verifyProductIDExists($itemid): bool {
    if(!itemExists($itemid)) {
        errorResult("That product does not exist!");
        return false;
    }
    return true;
}

/**
 * Verifies that a category exists. If it returns true, callers should call successResult().
 * @param string $categoryName The category name.
 */
function verifyCategoryExists($categoryName): bool {
    if(!categoryExists($categoryName)) {
        errorResult("Category '$categoryName' does not exist.");
        return false;
    }
    return true;
}

/**
 * Verifies that a category does not exist. If it returns true, callers should call successResult().
 * @param string $categoryName The category name.
 */
function verifyCategoryDoesNotExist($categoryName): bool {
    if(categoryExists($categoryName)) {
        errorResult("Category '$categoryName' already exists.");
        return false;
    }
    return true;
}

/**
 * Verifies that a manufacturer exists. If it returns true, callers should call successResult().
 * @param string $manufacturer The manufacturer name.
 */
function verifyManufacturerExists($manufacturer): bool {
    if(!manufacturerExists($manufacturer)) {
        errorResult("Manufacturer '$manufacturer' does not exist.");
        return false;
    }
    return true;
}

/**
 * Verifies that a manufacturer exists. If it returns true, callers should call successResult().
 * @param string $manufacturer The manufacturer name.
 */
function verifyManufacturerDoesNotExist($manufacturer): bool {
    if(manufacturerExists($manufacturer)) {
        errorResult("Manufacturer '$manufacturer' already exists.");
        return false;
    }
    return true;
}

/**
 * Verifies that a manufacturer exists. If it returns true, callers should call successResult().
 * @param string $manufacturer The manufacturer name.
 */
function verifyManufacturerNotInUse($manufacturer): bool {
    $result = getItemNamesFromManufacturer($manufacturer);
    if($result->num_rows > 0) {
        errorResult("There are still items from manufacturer '$manufacturer'.");
        return false;
    }
    return true;
}

/**
 * Verifies that a file exists. If it returns true, callers should call successResult().
 * @param string $filePath The file path.
 */
function verifyFileExists($filePath): bool {
    if(!file_exists($filePath)) {
        errorResult("That file does not exist.");
        return false;
    }

    return true;
}

/**
 * Verifies that a product is on sale. If it returns true, callers should call successResult().
 * @param string $itemid The product ID.
 */
function verifyProductOnSale($itemid): bool {
    if(!isItemOnSale($itemid)) {
        errorResult("That product is not on sale.");
        return false;
    }

    return true;
}

/**
 * Verifies that a product is NOT on sale. If it returns true, callers should call successResult().
 * @param string $itemid The product ID.
 */
function verifyProductNotOnSale($itemid): bool {
    if(isItemOnSale($itemid)) {
        errorResult("That product is already on sale.");
        return false;
    }

    return true;
}

/**
 * Verifies that an order exists. If it returns true, callers should call successResult().
 * @param string $orderid The order ID.
 */
function verifyOrderExists($orderid): bool {
    if(!orderExists($orderid)) {
        errorResult("That order ID is invalid.");
        return false;
    }

    return true;
}

/**
 * Verifies that a product is not about to be shipped. If it returns true, callers should call successResult().
 * @param string $orderid The order ID.
 */
function verifyProductNotInAnOrder($itemid): bool {
    if(isItemInAnyOrder($itemid)) {
        errorResult("That product has been ordered by someone.");
        return false;
    }

    return true;
}

function deleteItemImageIfUnused($filename): void {
    if(itemImageUsageCount($filename) == 0) {
        unlink("images/products/$filename");
    }
}

function deleteExtraItemImageIfUnused($filename): void {
    if(extraItemImageUsageCount($filename) == 0) {
        unlink("images/products/extra/$filename");
    }
}

function calculateDiscount($old, $new): float {
    $ratio = $new / $old;
    return 100 * (1 - $ratio);
}

function calculatePrice($price, $discount): string {
    $actualPrice = $price;

    if($discount !== null) {
        $actualPrice *= 1 - ($discount / 100);
    }

    return number_format($actualPrice, 2, '.', '');
}

function getPostRequestBody(): string {
    return file_get_contents('php://input');
}

/**
 * Creates a string for use in an SQL query IN-operator.
 * As an example, see the IN-operation in the following query:
 * ```sql
 * SELECT name FROM user WHERE name IN ('john','jane','foobar')"
 * ```
 * 
 * @param array $array Array of strings to make a list out of.
 */
function constructSqlQueryStringList(array $array): string {
    $output = "(";
    foreach($array as $element) {
        $output .= "'$element',";
    }
    $output = substr($output, 0, -1);
    $output .= ")";

    return $output;
}

/**
 * Creates an optional manufacturer clause for use in an SQL query WHERE-statement.  
 * If the array is empty, then no clause is added:  
 * `SELECT title FROM items WHERE name LIKE '%hello%'`  
 * However, if there is at least one manufacturer in the array, then:  
 * `SELECT title FROM items WHERE name LIKE '%hello%' AND manufacturer IN ('IKEA')`  
 * Everything from the "AND" and onwards is returned.
 * 
 * @param array $array Array of manufacturers (strings) to make a clause out of.
 */
function constructManufacturerFilterClause(array $manufacturers): string {
    if(count($manufacturers) > 0) {
        $manufacturerList = constructSqlQueryStringList($manufacturers);
        return "AND manufacturer in $manufacturerList";
    }
    return "";
}

function constructItemIDListFromCart(): string {
    $output = "(";
    if(!cartEmpty()) {
        foreach($_SESSION["cart"] as $itemid => $quantity) {
            $output .= "'$itemid',";
        }
        $output = substr($output, 0, -1);
    }
    $output .= ")";

    return $output;
}

function calculateDiscountOnItem(&$item): void {
    if($item["percentage"] !== null) {
        $item['actual_price'] = calculatePrice($item["price"], $item["percentage"]);
    }
}

function prepareFilterClauses($categories, $manufacturers): array {
    $categoryList = constructSqlQueryStringList($categories);
    $manufacturerClause = constructManufacturerFilterClause($manufacturers);

    return array($categoryList, $manufacturerClause);
}

function getFilterStartIndex(int $page): int {
    return $page * 8;
}

function getFilteredItemPage(string $searchTerm, string $maxPrice, int $page, array $categories,
    array $manufacturers): array {
    
    $startIndex = getFilterStartIndex($page);
    list($categoryList, $manufacturerClause) = prepareFilterClauses($categories, $manufacturers);

    $result = filterItems($searchTerm, $maxPrice, $startIndex, 8, $categoryList, $manufacturerClause);
    $output = array();
    while($item = $result->fetch_assoc()) {
        calculateDiscountOnItem($item);
        $output[] = $item;
    }

    return $output;
}

function countItemsInFilter(string $searchTerm, string $maxPrice, array $categories,
    array $manufacturers): int {

    list($categoryList, $manufacturerClause) = prepareFilterClauses($categories, $manufacturers);
    
    return filterItemCount($searchTerm, $maxPrice, $categoryList, $manufacturerClause);
}

function getPriceRangeInFilter(array $categories, array $manufacturers): array {
    list($categoryList, $manufacturerClause) = prepareFilterClauses($categories, $manufacturers);
    return filterPriceRange($categoryList, $manufacturerClause);
}

function isEmpty(?string &$value): bool {
    return !isset($value) || $value === "";
}

function removeCartItem(int $itemid): void {
    if(isset($_SESSION['cart']) && isset($_SESSION['cart'][$itemid])) {
        unset($_SESSION['cart'][$itemid]);
    }
}

function submitReview(int $userid, int $itemid, int $rating, string $reviewText): void {
    if(reviewExists($userid, $itemid)) {
        updateReview($userid, $itemid, $rating, $reviewText);
    } else {
        createNewReview($userid, $itemid, $rating, $reviewText);
    }
}

function cartEmpty(): bool {
    return !isset($_SESSION["cart"]) || count($_SESSION["cart"]) == 0;
}

function getCartItems(): array {
    $cartItemIDs = constructItemIDListFromCart();
    $result = getItemsInCart($cartItemIDs);

    $output = array();
    while($item = $result->fetch_assoc()) {
        $itemid = $item["id"];
        $price = $item["price"];
        $discount = $item["percentage"];
        $quantity = $_SESSION["cart"][$itemid];

        $item["quantity"] = $quantity;
        $item["price"] = calculatePrice($price * $quantity, $discount);

        $output[] = $item;
    }
    return $output;
}

function login($username, $password): bool {
    if(isPasswordLengthAcceptable($password)) {
        $stmt = prepareStmt("SELECT id,password FROM users WHERE name=?");
        $stmt->bind_param("s", $username);
        $result = executeStmt($stmt);
        if($row = $result->fetch_assoc()) {
            $id = $row['id'];
            $pw_hashed = $row['password'];
            if(verifyPassword($password, $pw_hashed)) {
                $_SESSION["logged_in"] = true;
                $_SESSION["user_id"] = $id;
                return true;
            } 
        }
    }

    errorResult("Invalid username or password.");
    return false;
}

function getAverageRatingOnItem($itemid): array {
    $rating = computeAverageRating($itemid);

    $average = $rating[0];
    $reviewCount = $rating[1];

    if($average === null) {
        $average = 0;
    } else {
        $average = round($average);
    }

    return array($average, $reviewCount);
}

function clearCart(): void {
    if(isset($_SESSION["cart"])) {
        $_SESSION["cart"] = array();
    }
}

function orderItemsToJoinedString($orderItems): string {
    $output = "";
    foreach($orderItems as $orderItem) {
        $quantity = $orderItem["quantity"];
        $title = $orderItem["title"];
        $itemid = $orderItem["id"];
        $output .= $quantity . "x $title (id: $itemid), ";
    }
    return substr($output, 0, -2);
}

function isPasswordLengthAcceptable(string $password): bool {
    $length = strlen($password);
    return $length >= 8 && $length <= 128;
}

function registerUser($username, $password, $email, $firstName, $lastName, $birthDate, $address, $postalCode): bool {
    if(verifyUsernameDoesNotExist($username)) {
        if(isPasswordLengthAcceptable($password)) {
            addUserDetailed($username, $password, $email, $firstName, $lastName, $birthDate, $address, $postalCode);
            return true;
        } else {
            errorResult("The password must be between 8 and 128 characters long.");
        }
    }
    return false;
}
?>
