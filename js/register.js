function matchPassword(repeatField) {
    let mainField = document.getElementById("register-password");
    if(repeatField.value !== mainField.value) {
        repeatField.setCustomValidity("The passwords do not match.");
    } else {
        repeatField.setCustomValidity("");
    }
}
