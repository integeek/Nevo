const { hasDigit, hasUppercase, hasLowercase, hasMinLength, isValidPassword } = require('./passwordRules');

test('hasDigit returns true when password contains a digit', () => {
    expect(hasDigit('abc1')).toBe(true);
});

test('hasDigit returns false when password has no digit', () => {
    expect(hasDigit('abcDEF')).toBe(false);
});

test('hasUppercase returns true when password contains an uppercase letter', () => {
    expect(hasUppercase('abcD')).toBe(true);
});

test('hasUppercase returns false when password has no uppercase letter', () => {
    expect(hasUppercase('abcd')).toBe(false);
});

test('hasLowercase returns true when password contains a lowercase letter', () => {
    expect(hasLowercase('ABCd')).toBe(true);
});

test('hasLowercase returns false when password has no lowercase letter', () => {
    expect(hasLowercase('ABCD')).toBe(false);
});

test('hasMinLength returns true when password is 8 characters or more', () => {
    expect(hasMinLength('abcdefgh')).toBe(true);
});

test('hasMinLength returns false when password is less than 8 characters', () => {
    expect(hasMinLength('abc')).toBe(false);
});

test('isValidPassword returns true when all rules are met', () => {
    expect(isValidPassword('Abcdef1g')).toBe(true);
});

test('isValidPassword returns false when a rule is missing', () => {
    expect(isValidPassword('abcdefgh')).toBe(false); 
});
