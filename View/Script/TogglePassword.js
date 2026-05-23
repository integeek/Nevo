document.querySelectorAll(".toggle-pass").forEach((toggle) => {
    toggle.addEventListener("click", function() {
        let passwordField = this.previousElementSibling;
        if (passwordField.type === "password") {
            passwordField.type = "text";
            this.src = "../Assets/img/icon-eye-closed.png"; 
        } else {
            passwordField.type = "password";
            this.src = "../Assets/img/icon-eye-open.png"; 
        }
    });
});