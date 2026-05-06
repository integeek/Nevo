function togglePassword() {
    const input = document.getElementById('passwordInput');
    const btn = document.getElementById('toggleBtn');
    if (input.type === 'password') {
        input.type = 'text';
        btn.src = '../Assets/img/icon-eye-closed.png';
        btn.alt = 'Hide password';
    } else {
        input.type = 'password';
        btn.src = '../Assets/img/icon-eye-open.png';
        btn.alt = 'Show password';
    }
}

function handleRegister() {
    const name = document.getElementById('nameInput').value;
    const email = document.getElementById('emailInput').value;
    const password = document.getElementById('passwordInput').value;
}
