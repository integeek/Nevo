const AVATAR_BG = {
  'icon-superhero': 'linear-gradient(135deg,#f4845f,#e8623a)',
  'icon-butterfly': 'linear-gradient(135deg,#64b5f6,#1976d2)',
  'icon-unicorn':   'linear-gradient(135deg,#81c784,#388e3c)',
  'icon-fish':      'linear-gradient(135deg,#f06292,#c2185b)',
  'icon-penguin':   'linear-gradient(135deg,#ffb74d,#e65100)',
};

let selectedAvatar = {
  avatar: 'icon-superhero',
  bg: 'linear-gradient(135deg,#f4845f,#e8623a)',
};

let currentEditCard = null;

async function loadChildren() {
  try {
    const res = await fetch('../../Controller/ParentDashboard/SettingFamily.php?action=getChildren');
    const data = await res.json();
    if (!data.success) {
      return;
    }
    renderChildren(data.children);
  } catch (e) {
    console.error(e);
  }
}

function renderChildren(children) {
  childrenCache = children;
  const grid = document.getElementById('heroesGrid');
  const emptyState = document.getElementById('emptyState');

  grid.querySelectorAll('.hero-card').forEach(c => c.remove());

  if (children.length === 0) {
    emptyState.style.display = '';
    return;
  }
  emptyState.style.display = 'none';

  children.forEach(child => {
    const bg = AVATAR_BG[child.avatar] || 'linear-gradient(135deg,#c084fc,#7c3aed)';
    const card = document.createElement('div');
    card.className = 'hero-card';
    card.dataset.id = child.id;
    card.dataset.disease = child.disease || '';
    card.innerHTML  = `
      <div class="hero-avatar" style="background:${bg}">
        <img src="../Assets/img/${child.avatar || 'icon-superhero'}.svg" alt="avatar" />
      </div>
      <div class="hero-body">
        <div class="hero-name">${child.fullname}</div>
        <div class="hero-sub">${child.age} y/o</div>
        <div class="hero-stats">
          <span class="hero-stat stat-xp">
            <span class="icon"><img src="../Assets/img/icon-star.svg" alt=""></span>
            ${child.xp || 0} XP
          </span>
          <span class="hero-stat stat-days">
            <span class="icon"><img src="../Assets/img/icon-fire.svg" alt=""></span>
            ${child.streak || 0} days
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
  });
}

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
    bg: el.style.background,
  };
}

async function addProfile() {
  const name = document.getElementById('profileNameInput');
  const age = document.getElementById('age');
  const disease = document.getElementById('diseaseName');
  const secretPin = document.getElementById('secretPin');
  const confirmSecretPin = document.getElementById('confirmSecretPin');

  if (!name.value.trim()) { 
    name.style.borderColor = '#e57373'; 
    return; 
  }
  if (isNaN(age.value) || age.value === '') { 
    age.style.borderColor  = '#e57373'; 
    return; 
  }
  if (isNaN(secretPin.value) || secretPin.value === '') { 
    secretPin.style.borderColor = '#e57373'; 
    return; 
  }
  if (secretPin.value !== confirmSecretPin.value) {
    secretPin.style.borderColor = '#e57373';
    confirmSecretPin.style.borderColor = '#e57373';
    return;
  }

  const body = new FormData();
  body.append('action', 'addChild');
  body.append('fullname', name.value.trim());
  body.append('age', age.value.trim());
  body.append('avatar', selectedAvatar.avatar);
  body.append('disease', disease.value.trim());
  body.append('pin', secretPin.value);

  try {
    const res = await fetch('../../Controller/ParentDashboard/SettingFamily.php', { method: 'POST', body });
    const data = await res.json();
    if (data.success) {
      name.value = ''; age.value = ''; disease.value = ''; secretPin.value = ''; confirmSecretPin.value = '';
      closeModal('modalOverlay');
      await loadChildren();
    }
  } catch (e) {
    console.error(e);
  }
}

function openEdit(card) {
  currentEditCard = card;
  document.getElementById('editNameInput').value = card.querySelector('.hero-name').textContent;
  document.getElementById('editAgeInput').value = card.querySelector('.hero-sub').textContent.replace(' y/o', '');
  document.getElementById('editDiseaseInput').value = card.dataset.disease || '';
  openModalEdit();
}

async function saveEdit() {
  const nameInput = document.getElementById('editNameInput');
  const ageInput = document.getElementById('editAgeInput');
  const diseaseInput = document.getElementById('editDiseaseInput');
  const name = nameInput.value.trim();
  const age = ageInput.value.trim();
  const disease = diseaseInput.value.trim();

  if (!name) { 
    nameInput.style.borderColor = '#e57373'; 
    return; 
  }
  if (isNaN(age) || age === '') { 
    ageInput.style.borderColor  = '#e57373'; 
    return; 
  }

  const body = new FormData();
  body.append('action', 'editChild');
  body.append('child_id', currentEditCard.dataset.id);
  body.append('fullname', name);
  body.append('age', age);
  body.append('disease', disease);

  try {
    const res = await fetch('../../Controller/ParentDashboard/SettingFamily.php', { method: 'POST', body });
    const data = await res.json();
    if (data.success) {
      currentEditCard.querySelector('.hero-name').textContent = name;
      currentEditCard.querySelector('.hero-sub').textContent = `${age} y/o`;
      currentEditCard.dataset.disease = disease;
      currentEditCard = null;
      closeModal('editModalOverlay');
    }
  } catch (e) {
    console.error(e);
  }
}

async function openDelete(card) {
  const name = card.querySelector('.hero-name').textContent;
  if (!confirm(`Do you really want to delete the profile of ${name} ? You will no longer be able to recover the data.`)) {
    return;
  }

  const body = new FormData();
  body.append('action', 'deleteChild');
  body.append('child_id', card.dataset.id);

  try {
    const res = await fetch('../../Controller/ParentDashboard/SettingFamily.php', { method: 'POST', body });
    const data = await res.json();
    if (data.success) {
      card.remove();
      if (document.querySelectorAll('.hero-card').length === 0) {
        document.getElementById('emptyState').style.display = '';
      }
    }
  } catch (e) {
    console.error(e);
  }
}

let childrenCache = [];

async function loadStaff() {
  try {
    const res = await fetch('../../Controller/ParentDashboard/SettingFamily.php?action=getStaff');
    const data = await res.json();
    if (!data.success) {
      return;
    }
    renderStaff(data.staff);
  } catch (e) {
    console.error(e);
  }
}

const staffCache = {};

function renderStaff(staffList) {
  const grid = document.getElementById('staffGrid');
  const empty = document.getElementById('staffEmptyState');
  grid.querySelectorAll('.staff-card').forEach(c => c.remove());

  if (!staffList || staffList.length === 0) {
    empty.style.display = '';
    return;
  }
  empty.style.display = 'none';

  staffList.forEach(s => {
    staffCache[s.id] = s;
    const initials = s.fullname.split(' ').map(w => w[0]).join('').slice(0,2).toUpperCase();
    const card = document.createElement('div');
    card.className = 'staff-card';
    card.dataset.id = s.id;
    const childTags = (s.children || []).map(c => `
      <span class="staff-child-tag">
        👤 ${c.fullname}
        <button onclick="removeStaffLink(${c.link_id})" title="Unlink">✕</button>
      </span>
    `).join('');
    card.innerHTML = `
      <div class="staff-avatar">${initials}</div>
      <div class="staff-body">
        <div class="staff-name">${s.fullname}</div>
        <div class="staff-speciality">${s.speciality || ''}</div>
        <div class="staff-children">${childTags}</div>
      </div>
      <div class="hero-actions">
        <button class="icon-btn" onclick="openEditStaff(${s.id})">
          <img src="../Assets/img/icon-edit.svg" alt="Edit" />
        </button>
        <button class="icon-btn delete" onclick="deleteStaff(${s.id})">
          <img src="../Assets/img/icon-delete.svg" alt="Delete" />
        </button>
      </div>
    `;
    grid.insertBefore(card, empty);
  });
}

let staffSearchTimeout = null;

function openStaffModal() {
  document.getElementById('staffSearchInput').value  = '';
  document.getElementById('staffSearchResults').innerHTML = '';
  document.getElementById('staffSelectedCard').style.display = 'none';
  document.getElementById('staffSelectedCard').innerHTML = '';
  document.getElementById('selectedStaffId').value = '';
  document.getElementById('staffChildCheckboxes').innerHTML = childrenCache.map(c => `
    <label class="child-checkbox-item">
      <input type="checkbox" value="${c.id}" />
      ${c.fullname} (${c.age} y/o)
    </label>
  `).join('');
  document.getElementById('staffModalOverlay').classList.add('active');
}

async function searchStaffMembers(q) {
  try {
    const res = await fetch(`../../Controller/ParentDashboard/SettingFamily.php?action=searchStaff&q=${encodeURIComponent(q)}`);
    const data = await res.json();
    if (!data.success) {
      return;
    }
    const box = document.getElementById('staffSearchResults');
    if (!data.staff.length) {
      box.innerHTML = '<div style="font-size:0.78rem;color:#aaa;padding:6px 2px;">No staff found. They must create an account first.</div>';
      return;
    }
    box.innerHTML = data.staff.map(s => `
      <div class="staff-result-item"
           data-id="${s.id}"
           data-name="${s.fullname.replace(/"/g,'&quot;')}"
           data-speciality="${(s.speciality||'').replace(/"/g,'&quot;')}"
           style="padding:10px 12px;border:1.5px solid #e4e0da;border-radius:10px;margin-bottom:6px;cursor:pointer;font-size:0.85rem;transition:border-color 0.15s;">
        <strong>${s.fullname}</strong>
        <span style="color:#7a9490;font-size:0.75rem;"> — ${s.speciality || 'No speciality'}</span>
        <div style="font-size:0.72rem;color:#aaa;">${s.email}</div>
      </div>
    `).join('');
    box.querySelectorAll('.staff-result-item').forEach(el => {
      el.addEventListener('mouseover', () => el.style.borderColor = '#3dbfa0');
      el.addEventListener('mouseout',  () => el.style.borderColor = '#e4e0da');
      el.addEventListener('click', () => selectStaff(el.dataset.id, el.dataset.name, el.dataset.speciality));
    });
  } catch (e) {
    console.error(e);
  }
}

function selectStaff(id, name, speciality) {
  document.getElementById('selectedStaffId').value        = id;
  document.getElementById('staffSearchInput').value       = '';
  document.getElementById('staffSearchResults').innerHTML = '';
  const card = document.getElementById('staffSelectedCard');
  card.style.display = '';
  card.innerHTML = `✓ <strong>${name}</strong> <span style="font-weight:400;color:#7a9490;font-size:0.78rem;">— ${speciality || 'No speciality'}</span>
    <button onclick="clearSelectedStaff()" style="background:none;border:none;cursor:pointer;color:#2a9d85;font-size:0.85rem;margin-left:8px;">✕</button>`;
}

function clearSelectedStaff() {
  document.getElementById('selectedStaffId').value           = '';
  document.getElementById('staffSelectedCard').style.display = 'none';
  document.getElementById('staffSelectedCard').innerHTML     = '';
}

async function addStaff() {
  const staffId = document.getElementById('selectedStaffId').value;
  const checked = [...document.querySelectorAll('#staffChildCheckboxes input:checked')].map(i => i.value);

  if (!staffId) {
    document.getElementById('staffSearchInput').style.borderColor = '#e57373';
    return;
  }
  if (checked.length === 0) {
    return;
  }

  const body = new FormData(); 
  body.append('action','linkStaff');
  body.append('staff_id', staffId);
  checked.forEach(id => body.append('child_ids[]', id));

  try {
    const res  = await fetch('../../Controller/ParentDashboard/SettingFamily.php', { method: 'POST', body });
    const data = await res.json();
    if (data.success) {
      closeModal('staffModalOverlay');
      await loadStaff();
    }
  } catch (e) {
    console.error(e);
  }
}

async function removeStaffLink(linkId) {
  const body = new FormData();
  body.append('action',  'removeStaffLink');
  body.append('link_id', linkId);
  try {
    const res = await fetch('../../Controller/ParentDashboard/SettingFamily.php', { method: 'POST', body });
    const data = await res.json();
    if (data.success) {
      await loadStaff();
    }
  } catch (e) {
    console.error(e);
  }
}

let currentEditStaffId = null;

function openEditStaff(staffId) {
  const staff = staffCache[staffId];
  if (!staff) {
    return;
  }
  currentEditStaffId = staffId;
  document.getElementById('editStaffNameInput').value = staff.fullname;
  document.getElementById('editStaffSpecialityInput').value = staff.speciality || '';
  document.getElementById('editStaffEmailInput').value = staff.email || '';
  document.getElementById('editStaffModalOverlay').classList.add('active');
}

async function saveEditStaff() {
  const name = document.getElementById('editStaffNameInput');
  const speciality = document.getElementById('editStaffSpecialityInput');
  const email = document.getElementById('editStaffEmailInput');

  if (!name.value.trim()) { 
    name.style.borderColor = '#e57373'; 
    return; 
  }

  const body = new FormData();
  body.append('action', 'editStaff');
  body.append('staff_id', currentEditStaffId);
  body.append('fullname', name.value.trim());
  body.append('speciality', speciality.value.trim());
  body.append('email', email.value.trim());

  try {
    const res = await fetch('../../Controller/ParentDashboard/SettingFamily.php', { method: 'POST', body });
    const data = await res.json();
    if (data.success) {
      closeModal('editStaffModalOverlay');
      await loadStaff();
    }
  } catch (e) {
    console.error(e);
  }
}

async function deleteStaff(staffId) {
  if (!confirm('Remove this medical staff member? This will unlink them from all children.')) {
    return;
  }
  const body = new FormData();
  body.append('action', 'deleteStaff');
  body.append('staff_id', staffId);
  try {
    const res = await fetch('../../Controller/ParentDashboard/SettingFamily.php', { method: 'POST', body });
    const data = await res.json();
    if (data.success) {
      await loadStaff();
    }
  } catch (e) {
    console.error(e);
  }
}

document.addEventListener('DOMContentLoaded', () => {
  const searchInput = document.getElementById('staffSearchInput');
  if (searchInput) {
    searchInput.addEventListener('input', function () {
      clearTimeout(staffSearchTimeout);
      const q = this.value.trim();
      if (q.length < 2) { document.getElementById('staffSearchResults').innerHTML = ''; return; }
      staffSearchTimeout = setTimeout(() => searchStaffMembers(q), 300);
    });
  }

  document.querySelectorAll('.modal-input').forEach(field => {
    field.addEventListener('input', () => { field.style.borderColor = ''; });
  });
  loadChildren();
  loadStaff();
});