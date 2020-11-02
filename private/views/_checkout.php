<?php
$autofillFullName = "";
$autofillEmail = "";
$autofillAddress = "";
$autofillPostalCode = "";

if(isLoggedIn()) {
    $firstName =    $user["first_name"];
    $lastName =     $user["last_name"];
    $email =        $user["email"];
    $address =      $user["delivery_address"];
    $postalCode =   $user["zipcode"];

    if($firstName !== null) {
        $autofillFullName = $firstName;
        if($lastName !== null) {
            $autofillFullName .= " $lastName";
        }
    }

    if($email !== null) {
        $autofillEmail = $email;
    }

    if($address !== null) {
        $autofillAddress = $address;
    }

    if($postalCode !== null) {
        $autofillPostalCode = $postalCode;
    }
}
?>
<h1 class="text-center">Safe Checkout</h1>
<div class="flex-row flex-column-phone">
    <div class="col-25">
        <div class="container">
            <h3 class="text-center">
                Cart
            </h3>
            <?php
                $sum = 0;
                $items = getCartItems();
                foreach($items as $item) {
                    $price = $item["price"];
                    $sum += $price; ?>
                    <p>
                        <?=$item["quantity"]?>x <?=$item["title"]?>
                        <span class="float-right grey">
                            <?=$price?>kr
                        </span>
                    </p> <?php
                }
                $sum = number_format($sum, 2, '.', '');
            ?>
            <p>
                Total
                <span class="float-right">
                    <b><?=$sum?>kr</b>
                </span>
            </p>
        </div>
    </div>
    <div class="col-75">
        <div class="container">
            <form action="/checkout.php" method="post">
                <div class="flex-row">
                    <div class="col-50">
                        <h3 class="text-center">Shipping Address</h3>
                        <label class="label-above" for="fullname"><i class="fa-user"></i>Full Name</label>
                        <input type="text" id="fname" name="fullname" placeholder="John M. Doe"
                            required value="<?=$autofillFullName?>">

                        <label class="label-above" for="email"><i class="fa-envelope"></i> Email</label>
                        <input type="text" id="email" name="email"
                            placeholder="john69Doe420@gmail.com" required value="<?=$autofillEmail?>">

                        <label class="label-above" for="address"><i class="fa-address-card"></i> Address</label>
                        <input type="text" id="adr" name="address" placeholder="452 Doe Street"
                            required value="<?=$autofillAddress?>">

                        <label class="label-above" for="postal"><i class="fa-institution"></i> Postal Code</label>
                        <input type="text" id="postal" name="postal" placeholder="10001" required
                            value="<?=$autofillPostalCode?>">
                    </div>
                    <div class="col-50">
                        <h3 class="text-center">Payment</h3>
                        <label class="label-above">Accepted Cards</label>
                        <i class="fa-cc-visa"></i>
                        <i class="fa-cc-amex"></i>
                        <i class="fa-cc-mastercard"></i>
                        <i class="fa-cc-discover"></i>

                        <label class="label-above"  for="cardnumber">Name on Card</label>
                        <input type="text" id="cnam" name="cardnumber" placeholder="John More Done" required>

                        <label class="label-above"  for="cardnumber">Credit Card</label>
                        <input type="text" id="ccnum" name="cardnumber" placeholder="1111-2222-3333-4444" required>

                        <label class="label-above"  for="expmonth">Exp Month</label>
                        <input type="text" id="expmonth" name="expmonth" placeholder="September" required>

                        <label class="label-above"  for="expyear">Exp Year</label>
                        <input type="text" id="expyear" name="expyear" placeholder="352" required>

                        <label class="label-above"  for="cvv">CVV</label>
                        <input type="text" id="cvv" name="cvv" placeholder="532" required>
                    </div>
                </div>
                <div class="spaced">
                    <input type="submit" value="Place order" class="btn col-100">
                </div>
            </form>
        </div>
    </div>
</div>
