function CartReady() {

}

function openCart() {
    document.body.classList.add('cart-sidebar-visible');
}

function closeCart(){
    document.body.classList.remove('cart-sidebar-visible');
}

function removeFromCart(button) {
    let listItem = button.parentNode.parentNode;
    let itemId = listItem.dataset.itemid;
    fetch('/api/remove_from_cart.php?itemid=' + itemId)
        .then(response => {
            if(response.status == 200) {
                listItem.remove();
            }
        });
}

CartReady();
