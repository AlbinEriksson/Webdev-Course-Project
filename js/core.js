function clear(element, startIndex) {
    let child;
    while(child = element.children.item(startIndex)) {
        child.remove();
    }
}

function deleteAll(elements) {
    let element;
    while(element = elements.item(0)) {
        element.remove();
    }
}

function searchFocus() {
    let searchForm = document.getElementById("search-form");
    searchForm.classList.add('focus');
}

function searchBlur() {
    let searchForm = document.getElementById("search-form");
    searchForm.classList.remove('focus');
}
