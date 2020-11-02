function productReady() {
    let reviewStars = document.getElementsByClassName("review-star");
    let reviewValue = document.getElementById("review-rating-value");
    for(let i = 0; i < 5; i++) {
        let star = reviewStars.item(i);
        star.addEventListener('click', event => {
            let width = star.clientWidth;
            let x = event.clientX - star.getBoundingClientRect().left;

            star.classList.remove('fa-star-full', 'fa-star-half', 'fa-star-empty');
            if(x > width / 2) {
                reviewValue.value = 2*i + 2;
                star.classList.add('fa-star-full');
            } else {
                reviewValue.value = 2*i + 1;
                star.classList.add('fa-star-half');
            }
            
            for(let j = i + 1; j < 5; j++) {
                let futureStar = reviewStars.item(j);
                futureStar.classList.remove('fa-star-full', 'fa-star-half');
                futureStar.classList.add('fa-star-empty');
            }

            for(let j = 0; j < i; j++) {
                let pastStar = reviewStars.item(j);
                pastStar.classList.remove('fa-star-half', 'fa-star-empty');
                pastStar.classList.add('fa-star-full');
            }
        });
    }
}

function smallImageHover(smallImage) {
    let bigImage = document.getElementById("product-image");
    bigImage.style.backgroundImage = smallImage.style.backgroundImage;
}

function updateCost() {
    let quantity = document.getElementById("purchase-quantity");
    let totalPrice = document.getElementById("total-price");
    let price = document.getElementById("purchase-price");
    let newPrice = price.innerText * quantity.value;
    totalPrice.innerText = newPrice.toFixed(2) + "kr";
}

function getItemID() {
    let url = window.location.href;
    let match = url.match(/[?&]id=(\d+)/);
    return match[1];
}

function addToCart() {
    let itemId = getItemID();
    let quantity = document.getElementById("purchase-quantity").value;

    fetch('/api/add_to_cart.php?itemid=' + itemId + '&quantity=' + quantity);
}

async function submitReview() {
    let reviewValue = parseInt(document.getElementById("review-rating-value").value);
    let reviewText = document.getElementById("review-textarea").value;
    let reviewAuthor = document.getElementById("review-author-field").value;
    let itemId = parseInt(getItemID());
    let userReviews = document.getElementById("user-reviews");

    await deleteReview();
    
    fetch('/api/submit_review.php', {
        method: 'post',
        body: JSON.stringify({
            'item_id': itemId,
            'rating': reviewValue,
            'text': reviewText
        })
    })
        .then(response => {
            if(response.status == 200) {
                let userReview = document.createElement("div");
                userReview.classList.add("user-review");

                let ratingRow = document.createElement("div");
                ratingRow.classList.add("flex-row", "review-rating-row");

                let starList = document.createElement("ul");
                starList.classList.add("star-list", 'no-bullet');

                for(let i = 0; i < 5; i++) {
                    let star = document.createElement("li");
                    let value = 2 * (i + 1);
                    if(value <= reviewValue) {
                        star.classList.add("fa-star-full");
                    } else if(value - 1 == reviewValue) {
                        star.classList.add("fa-star-half");
                    } else {
                        star.classList.add("fa-star-empty");
                    }

                    starList.appendChild(star);
                }

                let reviewTextBox = document.createElement("div");
                reviewTextBox.classList.add("review-text");
                reviewTextBox.innerText = reviewText;

                let authorSpan = document.createElement("span");
                authorSpan.classList.add("review-author");
                authorSpan.innerText = reviewAuthor + " (Your review)";

                let deleteButton = document.createElement("button");
                deleteButton.classList.add("btn", "small-btn", "red-btn");
                deleteButton.addEventListener('click', deleteReview);
                deleteButton.innerText = "Delete";

                authorSpan.appendChild(deleteButton);

                ratingRow.appendChild(starList);
                ratingRow.appendChild(authorSpan);

                userReview.appendChild(ratingRow);
                userReview.appendChild(reviewTextBox);

                userReviews.insertBefore(userReview, userReviews.firstChild);
            }
        })
}

async function deleteReview() {
    let reviewToDelete = document.getElementsByClassName("user-review").item(0);
    let itemId = parseInt(getItemID());

    await fetch('/api/delete_review.php', {
        method: 'post',
        body: JSON.stringify({
            'item_id': itemId
        })
    })
        .then(response => {
            if(response.status == 200 && reviewToDelete != null) {
                reviewToDelete.remove();
            }
        });
}
