function openModal() {
  document.getElementById('modalOverlay').classList.add('active');
  document.getElementById('profileNameInput').value = '';
  setTimeout(() => document.getElementById('profileNameInput').focus(), 100);
}

function openModalEdit() {
  document.getElementById('editModalOverlay').classList.add('active');
}

function closeModal(id) {
  document.getElementById(id).classList.remove('active');
}

function pickAvatar(el) {
  document.querySelectorAll('.avatar-option').forEach(a => a.classList.remove('selected-avatar'));
  el.classList.add('selected-avatar');
  selectedAvatar = {
    avatar: el.dataset.avatar,
    img: el.querySelector('img').src,
    bg: el.style.background,
  }; 
}

function addProfile() {
  const name = document.getElementById('profileNameInput');
  if (!name.value.trim()) {
    name.style.borderColor = '#e57373';
    return;
  }
  const age = document.getElementById('age');
  const secretPin = document.getElementById('secretPin');
  const confirmSecretPin = document.getElementById('confirmSecretPin');

  if (isNaN(secretPin.value) || secretPin.value === '' ) {
    secretPin.style.borderColor = '#e57373';
    return;
  }

  if (isNaN(age.value.trim()) || age.value === '' ) {
    age.style.borderColor = '#e57373';
    return;
  }

  if (isNaN(confirmSecretPin.value) || confirmSecretPin.value === '' ) {
    confirmSecretPin.style.borderColor = '#e57373';
    return;
  }

  if (secretPin.value !== confirmSecretPin.value) {
    secretPin.style.borderColor = '#e57373';
    confirmSecretPin.style.borderColor = '#e57373';
    return;
  }

  const grid = document.getElementById('heroesGrid');
  const emptyState = document.getElementById('emptyState');
  const card = document.createElement('div');
  card.className = 'hero-card';
  card.innerHTML = `
    <div class="hero-avatar" style="background:${selectedAvatar.bg}">
      <img src="${selectedAvatar.img}" alt="avatar" />
      </div>
      <div class="hero-body">
        <div class="hero-name">${name.value}</div>
        <div class="hero-sub">${age.value} y/o</div>
        <div class="hero-stats">
          <span class="hero-stat stat-xp">
            <span class="icon"><img src="../Assets/img/icon-star.svg" alt=""></span>
            0 XP
          </span>
          <span class="hero-stat stat-days">
            <span class="icon"><img src="../Assets/img/icon-fire.svg" alt=""></span>
            0 days
          </span>
        </div>
      </div>
    <div class="hero-actions">
      <button class="icon-btn" onclick="openEdit(this.closest('.hero-card'))">
        <img src="../Assets/img/icon-edit.svg" alt="Edit icon" />
      </button>
      <button class="icon-btn delete" onclick="openDelete(this.closest('.hero-card'))">
        <img src="../Assets/img/icon-delete.svg" alt="Delete icon" />
      </button>
    </div>
  `;

  grid.insertBefore(card, emptyState);
  emptyState.style.display = 'none';

  card.style.opacity = '0';
  card.style.transform = 'scale(0.8) translateY(10px)';
  requestAnimationFrame(() => {
    card.style.transition = 'opacity 0.35s ease, transform 0.4s ease';
    card.style.opacity = '1';
    card.style.transform = '';
  });

  closeModal('modalOverlay');
  name.value = '';
  secretPin.value = '';
  confirmSecretPin.value = '';
}

let currentEditCard = null;

function openEdit(card) {
  const name = card.querySelector('.hero-name').textContent;
  const age  = card.querySelector('.hero-sub').textContent.replace(' y/o', '');
  document.getElementById('editNameInput').value = name;
  document.getElementById('editAgeInput').value  = age;
  openModalEdit();
}

function saveEdit() {
  const nameInput = document.getElementById('editNameInput');
  const ageInput  = document.getElementById('editAgeInput');
  const name = nameInput.value.trim();
  const age  = ageInput.value.trim();

  if (!name) {
    nameInput.style.borderColor = '#e57373';
    return;
  }

  if (isNaN(age) || age === '') {
    ageInput.style.borderColor = '#e57373';
    return;
  }

  currentEditCard.querySelector('.hero-name').textContent = name;
  currentEditCard.querySelector('.hero-sub').textContent  = `${age} y/o`;
  currentEditCard = null;
  closeModal('editModalOverlay');
}

function openDelete(card) {
  currentEditCard = card;
  const name = card.querySelector('.hero-name').textContent;
  if (confirm(`Do you really want to delete the profile of ${name} ? You will no longer be able to recover the data.`)) {
    currentEditCard.remove();
    currentEditCard = null;
  }
}   