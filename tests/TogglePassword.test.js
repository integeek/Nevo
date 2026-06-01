const { togglePasswordVisibility } = require('./toggleRules');

test('field type changes from password to text when toggled', () => {
    const field = { type: 'password' };
    togglePasswordVisibility(field);
    expect(field.type).toBe('text');
});

test('field type changes from text to password when toggled back', () => {
    const field = { type: 'text' };
    togglePasswordVisibility(field);
    expect(field.type).toBe('password');
});

test('returns closed eye icon when password becomes visible', () => {
    const field = { type: 'password' };
    const icon = togglePasswordVisibility(field);
    expect(icon).toBe('../Assets/img/icon-eye-closed.png');
});

test('returns open eye icon when password becomes hidden', () => {
    const field = { type: 'text' };
    const icon = togglePasswordVisibility(field);
    expect(icon).toBe('../Assets/img/icon-eye-open.png');
});
