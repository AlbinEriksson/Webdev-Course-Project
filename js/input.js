function inputReady() {
    let inputSuggestors = document.getElementsByClassName("input-suggest");
    for(let i = 0; i < inputSuggestors.length; i++) {
        let inputSuggestor = inputSuggestors.item(i);
        let inputField = inputSuggestor.getElementsByClassName("suggest-field").item(0);
        let suggestions = inputSuggestor.getElementsByClassName("input-suggestions").item(0);
    }
}

function addSuggestion(suggestions, inputField, suggestion) {
    let listItem = document.createElement('li');
    listItem.innerText = suggestion;
    listItem.addEventListener("click", event => {
        inputField.value = suggestion;
    });
    suggestions.appendChild(listItem);
}

function clearSuggestions() {
    let suggestionsList = document.getElementsByClassName("input-suggestions");
    for(let i = 0; i < suggestionsList.length; i++) {
        clear(suggestionsList.item(i), 0);
    }
}

function autocomplete(inputField, getUrl, valueColumn='name') {
    let currentFocus = -1;

    inputField.addEventListener('input', event => {
        let value = inputField.value;
        if(!value) {
            clearSuggestions();
            return false;
        }

        let suggestions = document.getElementById(inputField.id + '-suggestions');
        if(!suggestions) {
            suggestions = document.createElement('ul');
            suggestions.id = inputField.id + '-suggestions';
            suggestions.classList.add('input-suggestions', 'no-select', 'no-bullet');
            inputField.parentNode.appendChild(suggestions);
        }

        fetch(getUrl + value)
            .then(response => response.json())
            .then(json => {
                clearSuggestions();
                if(!json) return;
                json.forEach(suggestion => {
                    let listItem = document.createElement('li');
                    let highlight = document.createElement('strong');
                    highlight.innerText = suggestion.name.substr(0, value.length);
                    listItem.appendChild(highlight);
                    listItem.innerHTML += suggestion.name.substr(value.length);

                    listItem.addEventListener('click', event => {
                        inputField.value = suggestion[valueColumn];
                        clearSuggestions();
                        
                        inputField.dispatchEvent(new Event('autocompleted'));
                    });

                    suggestions.appendChild(listItem);
                });
            })
    });

    inputField.addEventListener('keydown', event => {
        let suggestions = document.getElementById(inputField.id + '-suggestions');
        if(suggestions) {
            suggestions = suggestions.getElementsByTagName('li');
        }
        
        if(event.keyCode == 40) { // down
            currentFocus++;
            addActive(suggestions);
            event.preventDefault();
        } else if(event.keyCode == 38) { // up
            currentFocus--;
            addActive(suggestions);
            event.preventDefault();
        } else if(event.keyCode == 13 || event.keyCode == 9) { // enter or tab
            if(currentFocus >= 0) {
                event.preventDefault();
                if(suggestions) {
                    suggestions.item(currentFocus).click();
                }
                currentFocus = -1;
            }
        } else if(event.keyCode == 27) { // escape
            clearSuggestions();
            currentFocus = -1;
        }
    });

    function addActive(suggestions) {
        if(!suggestions) {
            return false;
        }
        for(let i = 0; i < suggestions.length; i++) {
            suggestions.item(i).classList.remove('suggestion-active');
        }
        if(currentFocus >= suggestions.length) {
            currentFocus = 0;
        }
        if(currentFocus < 0) {
            currentFocus = suggestions.length - 1;
        }
        suggestions.item(currentFocus).classList.add('suggestion-active');
    }
}

inputReady();
