function togglePasswordVisibility(field) {
    if (field.type === "password") {
        field.type = "text";
        return "../Assets/img/icon-eye-closed.png";
    } else {
        field.type = "password";
        return "../Assets/img/icon-eye-open.png";
    }
}

module.exports = { togglePasswordVisibility };
