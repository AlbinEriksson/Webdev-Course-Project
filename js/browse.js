function browseReady() {
    setCurrentCategory(null, '');
    filterItems();
}

function closeAllCategories() {
    let categoryLabels = document.getElementsByClassName("category-label");
    for(let i = 0; i < categoryLabels.length; i++) {
        categoryLabels[i].classList.remove('open');
    }
}

function toggleCategorySelected(label) {
    if(label.classList.contains('open')) {
        deselectCategoryItem(label);
    } else {
        selectCategoryItem(label);
    }
}

function getCategoryFromLabel(label) {
    let parent = label.parentNode.parentNode.parentNode;
}

function selectCategoryItem(label) {
    closeAllCategories();
    label.classList.add('open');

    let categoryName = label.getElementsByClassName('category-name').item(0);
    setCurrentCategory(label, categoryName.innerText);

    let parent = label.parentNode.parentNode.parentNode;
    while(parent.classList.contains('category-item')) {
        let parentLabel = parent.getElementsByClassName('category-label').item(0);
        parentLabel.classList.add('open');
        parent = parent.parentNode.parentNode;
    }
}

function deselectCategoryItem(label) {
    label.classList.remove('open');

    let parent = label.parentNode.parentNode.parentNode;
    if(parent.classList.contains('category-item')) {
        let parentLabel = parent.getElementsByClassName('category-label').item(0);
        let parentName = parentLabel.getElementsByClassName('category-name').item(0);
        setCurrentCategory(parentLabel, parentName.innerText);
    } else {
        setCurrentCategory(null, '');
    }
}

function getCategoryChildren(label) {
    let result = [];
    let children;

    if(label == null) {
        children = [document.getElementById("category-root")];
    } else {
        let categoryName = label.getElementsByClassName('category-name').item(0);
        result = [categoryName.innerText];
        children = label.parentNode.getElementsByClassName("categories");
    }

    if(children.length > 0) {
        children = children[0];
        let items = children.getElementsByClassName("category-item");
        for(let i = 0; i < items.length; i++) {
            let childLabel = items.item(i).getElementsByClassName("category-label").item(0);
            result.push(...getCategoryChildren(childLabel));
        }
    }

    return result;
}

function setCurrentCategory(label, category) {
    window.categoryLabel = label;
    window.category = category;
    updateManufacturers();
    updatePriceSlider();
}

function updateManufacturers() {
    let manufacturers = document.getElementById("manufacturers");
    clear(manufacturers, 0);
    fetch('/api/get_manufacturers_in_categories.php', {
        method: 'post',
        body: JSON.stringify(getCategoryChildren(window.categoryLabel))
    })
        .then(response => response.json())
        .then(json => {
            json.forEach(manufacturer => {
                let listItem = document.createElement("li");
                let label = document.createElement("label");
                let checkbox = document.createElement("input");
                checkbox.setAttribute('type', 'checkbox');
                checkbox.setAttribute('value', manufacturer || '');
                checkbox.setAttribute('onclick', 'updatePriceSlider()');
                checkbox.classList.add('manufacturer-checkbox');
                label.appendChild(checkbox);
                label.innerHTML += manufacturer || 'Other';
                listItem.appendChild(label);
                manufacturers.appendChild(listItem);
            });
        });
}

function getSelectedManufacturers() {
    result = [];
    let checkboxes = document.getElementsByClassName("manufacturer-checkbox");
    for(let i = 0; i < checkboxes.length; i++) {
        if(checkboxes.item(i).checked) {
            result.push(checkboxes.item(i).value);
        }
    }
    return result;
}

function updatePriceSlider() {
    let priceSlider = document.getElementById("price-slider");
    let categories = getCategoryChildren(window.categoryLabel);
    let manufacturers = getSelectedManufacturers();

    fetch('/api/get_price_range.php', {
        method: 'post',
        body: JSON.stringify({
            categories: categories,
            manufacturers: manufacturers
        })
    })
        .then(response => response.json())
        .then(json => {
            priceSlider.min = json.min || 0;
            priceSlider.max = json.max || 0
            priceSlider.value = priceSlider.max;
            let min = document.getElementById("price-min");
            let max = document.getElementById("price-max");
            let label = document.getElementById("price-label");
            min.innerText = priceSlider.min;
            max.innerText = priceSlider.max;
            label.innerText = priceSlider.value;
        })
}

function priceSliderUpdated(slider) {
    let label = document.getElementById("price-label");
    label.innerText = slider.value;
}

function firstPage(forceReload) {
    let pageField = document.getElementById("browse-page");
    if(pageField.value != 1 || forceReload) {
        pageField.value = 1;
        loadPage();
    }
}

function lastPage() {
    let pageField = document.getElementById("browse-page");
    let pageCount = document.getElementById("page-count");
    if(pageField.value != pageCount.innerText) {
        pageField.value = pageCount.innerText;
        loadPage();
    }
}

function nextPage() {
    let pageField = document.getElementById("browse-page");
    let pageCount = document.getElementById("page-count");
    pageField.value++;
    if(pageField.value > pageCount.innerText) {
        pageField.value = pageCount.innerText;
    } else {
        loadPage();
    }
}

function prevPage() {
    let pageField = document.getElementById("browse-page");
    pageField.value--;
    if(pageField.value < 1) {
        pageField.value = 1;
    } else {
        loadPage();
    }
}

function loadPage() {
    let pageField = document.getElementById("browse-page");
    let pageCount = document.getElementById("page-count");
    if(pageField.value < 1) {
        pageField.value = 1;
    } else if(pageField.value > pageCount.innerText) {
        pageField.value = pageCount.innerText;
    }

    let shopGrid = document.getElementById('shop-grid');
    shopGrid.scrollIntoView();
    filterItems();
}

function filterItems() {
    let searchTerm = document.getElementById("filter-search").value;
    let categories = getCategoryChildren(window.categoryLabel);
    let manufacturers = getSelectedManufacturers();
    let maxPrice = document.getElementById("price-slider").value;
    let page = document.getElementById("browse-page").value - 1;

    let body = {
        search: searchTerm,
        categories: categories,
        manufacturers: manufacturers,
        price_max: maxPrice,
        page: page
    };

    // console.log(body);

    let shopGrid = document.getElementById("shop-grid");
    let pageCount = document.getElementById("page-count");

    fetch('/api/filter_items.php', {
        method: 'post',
        body: JSON.stringify(body)
    })
        .then(response => response.json())
        .then(json => {
            clear(shopGrid, 0);
            shopGrid.innerText = '';
            if(json) {
                shopGrid.classList.remove('empty');

                if(json.items.length > 0) {
                    json.items.forEach(item => {
                        let productDiv = document.createElement("div");
                        productDiv.classList.add('product', 'square-image-frame');
                        productDiv.style.backgroundImage = "url('/images/products/" + item.image + "')";

                        let anchor = document.createElement("a");
                        anchor.classList.add("link", "white", "fill-parent");
                        anchor.href = "/product.php?id=" + item.id;
                        
                        let textHolder = document.createElement("div");
                        textHolder.classList.add('product-text', 'anchor-bottom');

                        let titleDiv = document.createElement("div");
                        titleDiv.classList.add('label');
                        titleDiv.innerText = item.title;

                        let priceDiv = document.createElement("div");
                        priceDiv.classList.add('label', 'bg-cyan', 'align-right', 'max-content');
                        if(item.percentage) {
                            let originalPrice = document.createElement("del");
                            originalPrice.innerText = item.price + "kr";
                            priceDiv.appendChild(originalPrice);

                            priceDiv.innerHTML += " " + item.actual_price + "kr";

                            let discount = document.createElement("div");
                            discount.classList.add('label', 'bg-red', 'align-right', 'max-content');
                            discount.innerText = "-" + Math.round(item.percentage) + "%";
                            textHolder.appendChild(discount);
                        } else {
                            priceDiv.innerText = item.price + "kr";
                        }

                        textHolder.appendChild(priceDiv);
                        textHolder.appendChild(titleDiv);

                        anchor.appendChild(textHolder);

                        productDiv.appendChild(anchor);

                        shopGrid.appendChild(productDiv);
                    });
                } else {
                    shopGrid.innerText = "No products were found. Try another search.";
                    shopGrid.classList.add('empty');
                }

                pageCount.innerText = Math.ceil(json.count / 8);
            }
        });
}

browseReady();
