<?php
$autofillEmail = "";
$autofillUsername = "";
$autofillFirstName = "";
$autofillLastName = "";
$autofillBirthDate = "";
$autofillAddress = "";
$autofillPostalCode = "";

if($_SERVER["REQUEST_METHOD"] === "POST") {
    $email =        $_POST["email"];
    $username =     $_POST["username"];
    $firstName =    $_POST["first-name"];
    $lastName =     $_POST["last-name"];
    $birthDate =    $_POST["birth-date"];
    $address =      $_POST["address"];
    $postalCode =   $_POST["postal-code"];

    if($email       !== null) $autofillEmail        = $email;
    if($username    !== null) $autofillUsername     = $username;
    if($firstName   !== null) $autofillFirstName    = $firstName;
    if($lastName    !== null) $autofillLastName     = $lastName;
    if($birthDate   !== null) $autofillBirthDate    = $birthDate;
    if($address     !== null) $autofillAddress      = $address;
    if($postalCode  !== null) $autofillPostalCode   = $postalCode;
}
?>

<script src="/js/register.js"></script>
<?php showPostResult(); ?>
<h1>Register</h1>
<form method="post">
    <div class="flex-row">
        <div class="col-50">
            <div class="container">
                <h2>Basic Information</h4>
                <div class="flex-row">
                    <div class="col-50 spaced">
                        <input type="text" name="email" placeholder="email" value="<?=$autofillEmail?>" required>
                        <input type="text" name="username" placeholder="username" value="<?=$autofillUsername?>" required>
                    </div>
                    <div class="col-50 spaced">
                        <input type="password" id="register-password" name="password" placeholder="password" required>
                        <input type="password" placeholder="repeat password" required oninput="matchPassword(this)">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-50">
            <div class="container">
                <h2>Optional Information</h4>
                <div class="flex-row">
                    <div class="col-50 spaced">
                        <input type="text" name="first-name" placeholder="first name" value="<?=$autofillFirstName?>">
                        <input type="text" name="last-name" placeholder="last name" value="<?=$autofillLastName?>">
                        <label class="label-above" for="birth-date">Date of Birth</label>
                        <input type="date" name="birth-date" value="<?=$autofillBirthDate?>">
                    </div>
                    <div class="col-50 spaced">
                        <input type="text" name="address" placeholder="delivery address" value="<?=$autofillAddress?>">
                        <input type="text" name="postal-code" placeholder="postal code" value="<?=$autofillPostalCode?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input class="center spaced" type="submit" value="Register">
</form>
