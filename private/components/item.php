<?php
include_once('private/util.php');

function comp_deletedPrice($price) { ?>
    <del><?=$price?>kr</del> <?php
}

function comp_finalPrice($price) { ?>
    <h2><?=$price?>kr</h2> <?php
}

function comp_discountDisplay($oldPrice, $discount) {
    comp_deletedPrice($oldPrice);
    comp_discountBadge($discount);
}

function comp_discountBadge($discount) { ?>
    <span class="sale">-<?=$discount?>%</span> <?php
}

function comp_squareImageFrame($imageUri) { ?>
    <div class="square-image-frame" style="background-image:url('<?=$imageUri?>')"></div> <?php
}

function comp_itemDisplay($item) {
    $originalPrice = $item["price"];
    $percentage = null;
    if(isset($item["percentage"])) {
        $percentage = $item["percentage"];
    }
    $actualPrice = calculatePrice($originalPrice, $percentage);

    ?>
    <div class="container">
        <a class="front-product-link" href="/product.php?id=<?=$item["id"]?>">
            <h3 class="ellipses">
                <?=$item["title"]?>
            </h3>
            <?php
                comp_squareImageFrame('/images/products/' . $item["image"]);
            ?>
            <div class="text-center">
                <?php
                    if($percentage !== null) {
                        comp_discountDisplay($originalPrice, $percentage);
                    }
                    comp_finalPrice($actualPrice);
                ?>
            </div>
        </a>
    </div> <?php
}

function comp_starList($rating) {
    for($i = 0; $i < ($rating - 1) / 2; $i++) { ?>
        <li class="fa-star-full"></li> <?php
    }
    if($rating % 2 == 1) { ?>
        <li class="fa-star-half"></li> <?php
    }
    for($i = 0; $i < 4 - ($rating - 1) / 2; $i++) { ?>
        <li class="fa-star-empty"></li> <?php
    }
}
function comp_review($review, $is_me) { ?>
    <div class="user-review">
        <div class="flex-row review-rating-row">
            <ul class="star-list no-bullet">
                <?=comp_starList($review["rating"])?>
            </ul>
            <span class="review-author">
                <?=$review["name"]?> <?php
                if($is_me) { ?>
                    (Your review)
                    <button class="btn small-btn red-btn" onclick="deleteReview()">Delete</button> <?php
                } ?>
            </span>
        </div>
        <div class="review-text">
            <?=$review["text"]?>
        </div>
    </div> <?php
    }
?>