function hasDigit(val)     { return /[0-9]/.test(val); }
function hasUppercase(val) { return /[A-Z]/.test(val); }
function hasLowercase(val) { return /[a-z]/.test(val); }
function hasMinLength(val) { return val.length >= 8; }
function isValidPassword(val) {
    return hasDigit(val) && hasUppercase(val) && hasLowercase(val) && hasMinLength(val);
}

module.exports = { hasDigit, hasUppercase, hasLowercase, hasMinLength, isValidPassword };
