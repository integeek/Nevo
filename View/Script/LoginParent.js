function togglePassword() {
    const input = document.getElementById('passwordInput');
    const btn = document.getElementById('toggleBtn');
    if (input.type === 'password') {
        input.type = 'text';
        btn.src = '../Assets/img/icon-eye-closed.png';
        btn.alt = 'Masquer le mot de passe';
    } else {
        input.type = 'password';
        btn.src = '../Assets/img/icon-eye-open.png';
        btn.alt = 'Afficher le mot de passe';
    }
}

function handleLogin() {
    const email = document.getElementById('emailInput').value;
    const password = document.getElementById('passwordInput').value;
}
