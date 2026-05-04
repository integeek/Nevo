let selectedAvatar = { emoji: '🐉', bg: 'linear-gradient(135deg,#f4845f,#e8623a)' };
let pinCode = '';
const SECRET_PIN = '1234';

function openModal() {
    document.getElementById('modalOverlay').classList.add('active');
    document.getElementById('profileNameInput').value = '';
    setTimeout(() => document.getElementById('profileNameInput').focus(), 100);
}

function closeModal(id) {
    document.getElementById(id).classList.remove('active');
}

function selectProfile(card, name, emoji, bg) {
    if (card.id === 'addCard') return;
    card.style.transform = 'scale(0.92)';
    setTimeout(() => { card.style.transform = ''; }, 180);
    setTimeout(() => openPinScreen(name, emoji, bg), 200);
}

function openPinScreen(name, emoji, bg) {
    pinCode = '';
    updateDots();
    document.getElementById('pinName').textContent = 'Hi, ' + name + '!';
    const av = document.getElementById('pinAvatar');
    av.style.background = bg;
    av.textContent = emoji;
    document.getElementById('pinScreen').classList.add('active');
    const card = document.getElementById('pinCard');
    card.style.animation = 'none';
    requestAnimationFrame(() => { card.style.animation = ''; });
}

function pressKey(digit) {
    if (pinCode.length >= 4){
        return;
    }
    pinCode += digit;
    updateDots();
    if (pinCode.length === 4) {
    setTimeout(checkPin, 150);
    }
}

function deleteKey() {
    pinCode = pinCode.slice(0, -1);
    updateDots();
}

function updateDots() {
    for (let i = 0; i < 4; i++) {
    const dot = document.getElementById('dot' + i);
    dot.classList.toggle('filled', i < pinCode.length);
    dot.classList.remove('error');
    }
}

function checkPin() {
    if (pinCode === SECRET_PIN) {
        for (let i = 0; i < 4; i++) {
            const dot = document.getElementById('dot' + i);
            dot.style.background = '#2cbfb1';
        }
        setTimeout(() => {
            closeModal('pinScreen');
        }, 500);
    } else {
    for (let i = 0; i < 4; i++) {
        document.getElementById('dot' + i).classList.add('error');
    }
    document.getElementById('pinCard').classList.add('shake');
    setTimeout(() => {
        document.getElementById('pinCard').classList.remove('shake');
        pinCode = '';
        updateDots();
    }, 500);
    }
}

document.addEventListener('keydown', e => {
    if (!document.getElementById('pinScreen').classList.contains('active')) {
        return;
    }
    if (e.key >= '0' && e.key <= '9') {
        pressKey(e.key);
    }
    if (e.key === 'Backspace') {
        deleteKey();
    }
    if (e.key === 'Escape') {
        closeModal('pinScreen');
    }
});

function pickAvatar(el) {
    document.querySelectorAll('.avatar-option').forEach(a => a.classList.remove('selected-avatar'));
    el.classList.add('selected-avatar');
    selectedAvatar = {
        emoji: el.dataset.emoji, 
        bg: el.style.background,
    };
}
function addProfile() {
    const name = document.getElementById('profileNameInput').value.trim();
    if (!name) {
        document.getElementById('profileNameInput').style.borderColor = '#e57373';
        setTimeout(() => document.getElementById('profileNameInput').style.borderColor = '', 800);
        return;
    }
    const secretPin = document.getElementById('secretPin');
    const confirmSecretPin = document.getElementById('confirmSecretPin');

    if (secretPin.value !== confirmSecretPin.value) {
        secretPin.style.borderColor = '#e57373';
        confirmSecretPin.style.borderColor = '#e57373';
        return;
    }

    const row = document.getElementById('profilesRow');
    const addCard = document.getElementById('addCard');
    const card = document.createElement('div');
    card.className = 'profile-card';
    card.style.animationDelay = '0s';
    card.onclick = function() { selectProfile(card, name, selectedAvatar.emoji, selectedAvatar.bg); };
    card.innerHTML = `
    <div class="avatar" style="background:${selectedAvatar.bg}">${selectedAvatar.emoji}</div>
    <div class="profile-name">${name}</div>
    `;

    row.insertBefore(card, addCard);
    closeModal('modalOverlay');

    card.style.opacity = '0';
    card.style.transform = 'scale(0.8) translateY(10px)';
    requestAnimationFrame(() => {
    card.style.transition = 'opacity 0.35s ease, transform 0.4s cubic-bezier(0.34,1.56,0.64,1)';
    card.style.opacity = '1';
    card.style.transform = '';
    });
}



document.getElementById('profileNameInput').addEventListener('keydown', e => {
    if (e.key === 'Enter') {
        addProfile();
    }
    if (e.key === 'Escape') {
        closeModal('modalOverlay');
    } 
});
