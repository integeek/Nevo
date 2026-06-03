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

/**
 * Fetches all children for curren parent and renders them as cards
 * @returns {Promise<void>}
 */
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

/**
 * Renders child profile cards in heroes grid
 * Shows empty state message if there are no children, otherwise creates card for each child with their avatar, name, age, XP, streak, and edit/delete buttons
 * @param {Array} children 
 * @returns {void}
 */
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

/**
 * Opens add child modal and focuses on name input field
 * Resets all input values to empty strings
 * @returns {void}
 */
function openModal() {
  document.getElementById('modalOverlay').classList.add('active');
  document.getElementById('profileNameInput').value = '';
  setTimeout(() => document.getElementById('profileNameInput').focus(), 100);
}

/**
 * Opens edit child modal
 * @returns {void}
 */
function openModalEdit() {
  document.getElementById('editModalOverlay').classList.add('active');
}

/**
 * Closes a modal by removing 'active' class from it
 * @param {string} id 
 * @returns {void}
 */
function closeModal(id) {
  document.getElementById(id).classList.remove('active');
}

/**
 * Handles avatar selection for child profile - marks clicked avatar as selected
 * @param {HTMLElement} el 
 * @returns {void}
 */
function pickAvatar(el) {
  document.querySelectorAll('.avatar-option').forEach(a => a.classList.remove('selected-avatar'));
  el.classList.add('selected-avatar');
  selectedAvatar = {
    avatar: el.dataset.avatar,
    bg: el.style.background,
  };
}

/**
 * Validate and submits add child form to server, then refreshes children list if addition is successful
 * @returns {Promise<void>}
 */
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

/**
 * Opens edit child modal and pre-fills it with child's current details (name, age, disease)
 * @param {HTMLElement} card 
 * @returns {void}
 */
function openEdit(card) {
  currentEditCard = card;
  document.getElementById('editNameInput').value = card.querySelector('.hero-name').textContent;
  document.getElementById('editAgeInput').value = card.querySelector('.hero-sub').textContent.replace(' y/o', '');
  document.getElementById('editDiseaseInput').value = card.dataset.disease || '';
  openModalEdit();
}

/**
 * Validates and submits edited child details to server, then updates child card in UI if update is successful
 * @returns {Promise<void>}
 */
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

/**
 * Shows confirmation dialog before deleting a child profile, then sends delete request to server and removes child card from UI if deletion is successful
 * @param {HTMLElement} card 
 * @returns {Promise<void>}
 */
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

/**
 * Loads staff members from server and renders them in UI
 * @returns {Promise<void>}
 */
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

/**
 * Renders list of staff cards in staff grid
 * Shows empty state message if there are no staff members, otherwise creates card for each staff member with their initials as avatar, name, speciality, linked children, and edit/delete buttons
 * @param {Array} staffList 
 * @returns {void}
 */
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

/**
 * Opens add staff modal for adding new staff member and populates it with child checkboxes
 * @returns {void}
 */
function openStaffModal() {
  const box = document.getElementById('staffChildCheckboxes');
  box.innerHTML = childrenCache.map(c => `
    <label class="child-checkbox-item">
      <input type="checkbox" value="${c.id}" />
      ${c.fullname} (${c.age} y/o)
    </label>
  `).join('');
  document.getElementById('staffNameInput').value = '';
  document.getElementById('staffSpecialityInput').value = '';
  document.getElementById('staffEmailInput').value = '';
  document.getElementById('staffModalOverlay').classList.add('active');
}

/**
 * Validates and submits add staff form to server, then refreshes staff list if addition is successful
 * @returns {Promise<void>}
 */
async function addStaff() {
  const name = document.getElementById('staffNameInput');
  const speciality = document.getElementById('staffSpecialityInput');
  const email = document.getElementById('staffEmailInput');
  const checked = [...document.querySelectorAll('#staffChildCheckboxes input:checked')].map(i => i.value);

  if (!name.value.trim()) { 
    name.style.borderColor = '#e57373'; 
    return; 
  }
  if (checked.length === 0) { 
    return; 
  }

  const body = new FormData();
  body.append('action', 'addStaff');
  body.append('fullname', name.value.trim());
  body.append('speciality', speciality.value.trim());
  body.append('email', email.value.trim());
  checked.forEach(id => body.append('child_ids[]', id));

  try {
    const res = await fetch('../../Controller/ParentDashboard/SettingFamily.php', { method: 'POST', body });
    const data = await res.json();
    if (data.success) {
      closeModal('staffModalOverlay');
      await loadStaff();
    }
  } catch (e) {
    console.error(e);
  }
}

/**
 * Removes link between a staff member and a child, then refreshes staff list if removal is successful
 * @param {string} linkId
 * @returns {Promise<void>} 
 */
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

/**
 * Opens edit staff modal for editing an existing staff member
 * Pre-fills it with staff member's current information
 * @param {string} staffId 
 * @returns {Promise<void>}
 */
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

/**
 * Validates and submits edit staff form to server, then refreshes staff list if update is successful
 * @returns {Promise<void>}
 */
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

/**
 * Shows confirmation dialog and deletes a staff member from server and refreshes staff list if deletion is successful
 * @param {string} staffId
 * @returns {Promise<void>}
 */
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
  document.querySelectorAll('.modal-input').forEach(field => {
    field.addEventListener('input', () => { field.style.borderColor = ''; });
  });
  loadChildren();
  loadStaff();
});
