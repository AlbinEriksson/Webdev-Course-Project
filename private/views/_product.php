<?php
include('private/components/item.php');
?>
<script src="/js/product.js"></script>
<div class="flex-row product-review-row">
    <div class="col-50">
        <div class="container">
            <div
                id="product-image"
                class="square-image-frame"
                style="background-image:url('/images/products/<?=$item["image"]?>')">
            </div>
            <div class="flex-row image-row">
                <div
                    class="square-image-frame"
                    onmouseover="smallImageHover(this)"
                    style="background-image:url('/images/products/<?=$item["image"]?>')">
                </div>
                <?php
                    if($extraImages !== null) {
                        foreach($extraImages as $extraImage) { ?>
                            <div
                                class="square-image-frame"
                                onmouseover="smallImageHover(this)"
                                style="background-image:url('/images/products/extra/<?=$extraImage?>')">
                            </div> <?php
                        }
                    }
                ?>
            </div>
        </div>
    </div> 
    <div class="col-50">
        <div class="container">
            <h1><?=$item["title"]?></h1>
            <?php
                list($average, $reviewCount) = getAverageRatingOnItem($item["id"]); ?>
                <span>
                    <ul id="average-review" class="star-list no-bullet">
                        <?=comp_starList($average);?>
                        <li><?=$reviewCount?> review(s)</li>
                    </ul>
                </span> <?php
            ?>
            <p>
                <?php 
                if($item["description"] != "") {
                    echo str_replace("\n", "<br>", $item["description"]);
                } else { ?>
                    There is no description for this product.
                <?php } ?>
                <br> 
                <div class="text-center"> <?php
                    if($sale !== null) {
                        comp_discountDisplay($item["price"], $sale["percentage"]);
                    }
                    comp_finalPrice($current_price); ?>
                </div>
            </p>
        </div>
        <form class="container" action="/product.php" method="post">
            <h2>Purchase</h2>
            <div class="flex-row flex-center">
                <h2>
                    <span id="purchase-price"><?=$current_price?></span>kr
                    &times;
                    <input id="purchase-quantity" name="quantity" type="number" value="1" min="1" max="999" onchange="updateCost()"></input>
                    =
                </h2>
                <h1 id="total-price">
                    <?=$current_price?>kr
                </h1>
            </div>
            <input type="hidden" name="itemid" value="<?=$item["id"]?>">
            <input type="submit" value="Add to Cart" class="col-100 btn">
        </form>
    </div>
</div>
<div class="container">
    <h2>User Reviews</h2>
    <div id="user-reviews">
        <?php
            $stmt;
            if(isLoggedIn()) {
                $myReview = getReview($item["id"], $user["id"]);
                if($myReview !== null) {
                    comp_review($myReview, true);
                }
            }

            $reviews;
            if(isLoggedIn()) {
                $reviews = getReviewsFromOtherUsers($item["id"], $user["id"]);
            } else {
                $reviews = getReviewsOnItem($item["id"]);
            }
            foreach($reviews as $review) {
                comp_review($review, false);
            }
        ?>
    </div>
    <div class="flex-row">
        <div id="review-stars">
            <ul class="star-list no-bullet">
                <li class="review-star fa-star-full"></li>
                <li class="review-star fa-star-full"></li>
                <li class="review-star fa-star-full"></li>
                <li class="review-star fa-star-full"></li>
                <li class="review-star fa-star-full"></li>
            </ul>
            <input type="hidden" id="review-rating-value" value="10">
            <?php if(isLoggedIn()) { ?>
                <input type="hidden" id="review-author-field" value="<?=$user["name"]?>">
                <button onclick="submitReview()" class="btn" id="btn-review">Review</button>
            <?php } else { ?>
                <button class="btn disabled" id="btn-review" disabled>Review</button>
            <?php } ?>
        </div>
        <?php if(isLoggedIn()) { ?>
        <textarea id="review-textarea" name="review" placeholder="Tell us what you think."></textarea>
        <?php } else { ?>
        <textarea id="review-textarea" name="review" placeholder="You must be logged in to review items." disabled></textarea>
        <?php } ?>
    </div>
</div>
<script>
productReady();
</script>
