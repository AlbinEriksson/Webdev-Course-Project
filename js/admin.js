function adminReady() {
    updateTab();
    window.addEventListener("hashchange", updateTab);

    let itemRemoveImageField = document.getElementById("item-remove-image-field");
    if(itemRemoveImageField) {
        itemRemoveImageField.addEventListener('input', updateImageRemovalList);
        itemRemoveImageField.addEventListener('autocompleted', updateImageRemovalList);
    }

    let userAutocompleteFields = document.getElementsByClassName("autocomplete-username");
    for(let i = 0; i < userAutocompleteFields.length; i++) {
        autocomplete(userAutocompleteFields[i], '/api/search_usernames.php?username=');
    }

    let itemAutocompleteFields = document.getElementsByClassName("autocomplete-item");
    for(let i = 0; i < itemAutocompleteFields.length; i++) {
        autocomplete(itemAutocompleteFields[i], '/api/search_items.php?itemname=', 'id');
    }

    let manufacturerAutocompleteFields = document.getElementsByClassName("autocomplete-manufacturer");
    for(let i = 0; i < manufacturerAutocompleteFields.length; i++) {
        autocomplete(manufacturerAutocompleteFields[i], '/api/search_manufacturers.php?name=');
    }
}

function hideElement(element) {
    element.style.display = 'none';
}

function showBlockElement(element) {
    element.style.display = 'block';
}

function deselectElement(element) {
    element.classList.remove('selected');
}

function selectElement(element) {
    element.classList.add('selected');
}

function hideTabs() {
    let tabs = document.getElementsByClassName('admin-tab');
    for(let i = 0; i < tabs.length; i++) {
        hideElement(tabs.item(i));
    }

    let tabLinks = document.getElementsByClassName('nav-link');
    for(let i = 0; i < tabLinks.length; i++) {
        deselectElement(tabLinks.item(i));
    }
}

function getTabToShow() {
    return window.location.hash.substr(1) || "home";
}

function showTab(tabToShow) {
    let tab = document.getElementById(tabToShow + "-tab");
    showBlockElement(tab);

    let tabLink = document.getElementById("nav-tab-" + tabToShow);
    if(tabLink) selectElement(tabLink);
}

function updateTab() {
    hideTabs();
    let tabToShow = getTabToShow();
    showTab(tabToShow);
}

function viewTable(fields, fetchUrl, tableElement, jsonCallback=null) {
    fetch(fetchUrl)
        .then(response => {
            if(response.status == 404) {

            }
            else {
                return response.json();
            }
        })
        .then(json => {
            let row = document.createElement('tr');

            fields.forEach(field => {
                let rowElement = document.createElement('td');
                rowElement.innerText = json[field];
                row.appendChild(rowElement);
            });

            if(jsonCallback) {
                jsonCallback(row, json);
            }

            tableElement.appendChild(row);
        });
}

function viewUser() {
    const userFields = ["id", "name", "email", "first_name", "last_name", "birth_date", "delivery_address", "zipcode"];

    let usernameField = document.getElementById('view-user-field');
    let username = usernameField.value;
    let userTable = document.getElementById('view-user-table');

    viewTable(userFields, '/api/get_user.php?username=' + username, userTable, (row, json) => {
        let privilegeElement = document.createElement('td');
        if(json.privileges) {
            privilegeElement.innerText = json.privileges.join();
        }
        row.appendChild(privilegeElement);
    });
}

function viewItem() {
    const userFields = ["id", "title", "manufacturer", "description", "date_added", "image", "price", "category", "visible"];

    let itemField = document.getElementById('view-item-field');
    let itemId = itemField.value;
    let itemTable = document.getElementById('view-item-table');

    viewTable(userFields, '/api/get_item.php?itemid=' + itemId, itemTable, (row, json) => {
        let imagesElement = document.createElement('td');
        if(json.extra_images) {
            imagesElement.innerText = json.extra_images.join();
        }
        row.appendChild(imagesElement);

        let saleElement = document.createElement('td');
        if(json.sale) {
            saleElement.innerText = json.sale.percentage + '% (' + json.sale.price + 'kr) until ' + json.sale.deadline;
        }
        row.appendChild(saleElement);
    });
}

function viewCategory() {
    const userFields = ["name", "parent"];

    let categoryField = document.getElementById('view-category-field');
    let categoryName = categoryField.value;
    let categoryTable = document.getElementById('view-category-table');

    viewTable(userFields, '/api/get_category.php?name=' + categoryName, categoryTable);
}

function clearUsers() {
    clear(document.getElementById('view-user-table'), 1);
}

function updateImageRemovalList() {
    let inputField = document.getElementById("item-remove-image-field");
    let productId = inputField.value;
    let resultsList = document.getElementById("item-remove-image-list");
    clear(resultsList, 0);
    if(/^\d+$/.test(productId)) { // is number
        fetch('/api/get_item.php?itemid=' + productId)
            .then(response => {
                if(response.status == 404) {
    
                }
                else {
                    return response.json();
                }
            })
            .then(json => {
                json.extra_images.forEach(image => {
                    let option = document.createElement('option');
                    option.setAttribute('value', image);
                    option.innerText = image;
                    resultsList.appendChild(option);
                });
            });
    }
}

adminReady();
