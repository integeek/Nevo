let currentChildId = null;
let currentEditCard = null;

const AVATAR_BG = {
  'icon-superhero': 'linear-gradient(135deg,#f4845f,#e8623a)',
  'icon-butterfly': 'linear-gradient(135deg,#64b5f6,#1976d2)',
  'icon-unicorn':   'linear-gradient(135deg,#81c784,#388e3c)',
  'icon-fish':      'linear-gradient(135deg,#f06292,#c2185b)',
  'icon-penguin':   'linear-gradient(135deg,#ffb74d,#e65100)',
  'icon-cat':       '#ede9fe',
  'icon-tiger':     '#fce7f3',
};

const makeIcon = (name) => {
  const src = name && name.startsWith('../') ? name : `../Assets/img/${name || 'icon-alarm'}.svg`;
  return `<img src="${src}" style="width:24px;height:24px;object-fit:contain;">`;
};

/**
 * Opens a modal dialog by adding "active" class to its element and focuses on it
 * @param {string} id 
 * @returns {void}
 */
function openModal(id) {
  document.getElementById(id).classList.add('active');
  setTimeout(() => document.getElementById(id).focus(), 100);
}

/**
 * Closes a modal dialog by removing "active" class from its element
 * @param {string} id 
 * @returns {void}
 */
function closeModal(id) {
  document.getElementById(id).classList.remove('active');
}

/**
 * Switches active tab in dashboard
 * @param {HTMLElement} btn 
 * @param {string} tab 
 * @returns {void}
 */
function switchTab(btn, tab) {
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById('tab-' + tab).classList.add('active');
}

/**
 * Toggle visibility of substeps for a routine item
 * @param {HTMLElement} btn 
 * @returns {void}
 */
function toggleSteps(btn) {
  const item = btn.closest('.routine-item');
  item.querySelector('.routine-substeps').classList.toggle('open');
  btn.classList.toggle('open');
}

/**
 * Fetches initial data (children list) from server and renders child pills in sidebar, then loads data for first child if there is at least one child
 * @returns {Promise<void>}
 */
async function loadInit() {
  try {
    const res = await fetch('../../Controller/ParentDashboard/ParentDashboard.php?action=init');
    const data = await res.json();
    if (!data.success) {
      return;
    }
    renderChildPills(data.children);
    if (data.children.length > 0) {
      switchChild(data.children[0].id);
    }
  } catch (e) {
    console.error(e);
  }
}

/**
 * Renders child profile pills in top bar based on provided children data, marking first child as active by default
 * @param {Array} children
 * @returns {void} 
 */
function renderChildPills(children) {
  const bar = document.getElementById('childPillsBar');
  bar.innerHTML = children.map((child, i) => {
    const bg = AVATAR_BG[child.avatar] || '#ede9fe';
    return `
      <div class="child-pill${i === 0 ? ' active' : ''}" data-child-id="${child.id}" onclick="switchChildByPill(${child.id}, this)">
        <div class="cpavatar" style="background:${bg}">
          <img src="../Assets/img/${child.avatar || 'icon-superhero'}.svg" alt="">
        </div>
        <div>
          <div class="cpname">${child.fullname}</div>
          <div class="cpage">${child.age} y/o</div>
        </div>
      </div>
    `;
  }).join('');
}

/**
 * Switches active child profile based on pill clicked - marks it as active and loads corresponding data for that child
 * @param {string} childId 
 * @param {HTMLElement} el 
 * @returns {void}
 */
function switchChildByPill(childId, el) {
  document.querySelectorAll('.child-pill').forEach(p => p.classList.remove('active'));
  el.classList.add('active');
  switchChild(childId);
}

/**
 * Loads all sections of dashboard for selected child in parallel 
 * @param {string} childId 
 * @returns {Promise<void>}
 */
async function switchChild(childId) {
  currentChildId = childId;
  await Promise.all([
    loadStats(childId),
    loadRoutines(childId),
    loadRewards(childId),
    loadFeelings(childId),
  ]);
}

/**
 * Loads statistics (completion, week, streak, routines) for selected child
 * @param {string} childId 
 * @returns {Promise<void>}
 */
async function loadStats(childId) {
  try {
    const res  = await fetch(`../../Controller/ParentDashboard/ParentDashboard.php?action=getStats&child_id=${childId}`);
    const data = await res.json();
    if (!data.success) {
      return;
    }
    document.getElementById('sCompletion').textContent = data.completion;
    document.getElementById('sWeek').innerHTML = data.week;
    document.getElementById('sStreak').textContent = data.streak;
    document.getElementById('sRoutines').textContent = data.routines;

    const lbl = document.getElementById('setupLabel');
    lbl.innerHTML = `<span>${makeIcon(data.child_avatar)}</span>&nbsp;${data.child_name}'s setup`;
  } catch (e) {
    console.error(e);
  }
}

/**
 * Loads routines for selected child and renders them
 * @param {string} childId 
 * @returns {Promise<void>}
 */
async function loadRoutines(childId) {
  try {
    const res = await fetch(`../../Controller/ParentDashboard/Routine.php?action=getRoutines&child_id=${childId}`);
    const data = await res.json();
    if (!data.success) {
      return;
    }
    renderRoutines(data.routines);
  } catch (e) {
    console.error(e);
  }
}

/**
 * Renders list of routines in dashboard
 * @param {Array} routines 
 * @returns {void}
 */
function renderRoutines(routines) {
  document.getElementById('routinesList').innerHTML = routines.map(r => `
    <div class="routine-item" data-id="${r.id}">
      <div class="routine-icon" style="background:#fff8e1">${makeIcon(r.icon || 'icon-alarm')}</div>
      <div class="routine-body">
        <div class="routine-name">${r.name}</div>
        <div class="routine-meta">
          <span class="ri">${r.xp_value} XP</span>
          <span>${(r.steps || []).length} step${(r.steps || []).length !== 1 ? 's' : ''}</span>
        </div>
      </div>
      <div class="routine-actions">
        <button class="ibt chevron" onclick="toggleSteps(this)">›</button>
        <button class="ibt del" onclick="openEdit(this.closest('.routine-item'), 'editRoutineModalOverlay')">
          <img src="../Assets/img/icon-edit.svg" alt="Edit" />
        </button>
        <button class="ibt del" onclick="confirmDeleteRoutine(this.closest('.routine-item'))">
          <img src="../Assets/img/icon-delete.svg" alt="Delete" />
        </button>
      </div>
      <div class="routine-substeps">
        ${(r.steps || []).map(s => `
          <label class="substep">
            <input type="checkbox"${s.is_completed ? ' checked' : ''} disabled/>
            <span>${s.name}</span>
          </label>
        `).join('')}
      </div>
    </div>
  `).join('');
}

/**
 * Validates and submits new routine to server
 * @returns {Promise<void>}
 */
async function saveRoutine() {
  const name = document.getElementById('nameInput');
  const xp = document.getElementById('xpInput');
  const stepsInput = document.getElementById('stepsInput');

  if (!name.value.trim()) { 
    name.style.borderColor = '#e57373'; 
    return;
  }
  if (!xp.value.trim() || isNaN(xp.value.trim())) { 
    xp.style.borderColor   = '#e57373'; 
    return; 
  }
  if (!stepsInput.value.trim()) { 
    stepsInput.style.borderColor = '#e57373'; 
    return; 
  }

  const body = new FormData();
  body.append('action', 'addRoutine');
  body.append('name', name.value.trim());
  body.append('xp_value', xp.value.trim());
  body.append('steps', stepsInput.value.trim());
  body.append('child_id', currentChildId);

  try {
    const res = await fetch('../../Controller/ParentDashboard/Routine.php', { method: 'POST', body });
    const data = await res.json();
    if (data.success) {
      name.value = ''; xp.value = ''; stepsInput.value = '';
      closeModal('newRoutineModalOverlay');
      await loadRoutines(currentChildId);
      await loadStats(currentChildId);
    }
  } catch (e) {
    console.error(e);
  }
}

/**
 * Opens edit modal for a routine and pre-fills it with current data of routine, allowing user to edit routine details
 * @param {HTMLElement} card 
 * @param {string} modalId 
 * @returns {void}
 */
function openEdit(card, modalId) {
  currentEditCard = card;
  if (modalId === 'editRoutineModalOverlay') {
    document.getElementById('editNameInput').value  = card.querySelector('.routine-name').textContent;
    document.getElementById('editXpInput').value    = card.querySelector('.ri').textContent.replace(' XP', '');
    const steps = [...card.querySelectorAll('.substep span')].map(s => s.textContent).join('\n');
    document.getElementById('editStepsInput').value = steps;
  } else if (modalId === 'editRewardModalOverlay') {
    document.getElementById('editRewardNameInput').value = card.querySelector('.reward-name').textContent;
    document.getElementById('editRewardXpInput').value   = card.querySelector('.reward-pts').textContent.replace(' XP', '');
    const cardType = card.dataset.type || 'out_app';
    document.getElementById('editRewardType').value = cardType;
    document.querySelectorAll('#editRewardModalOverlay .type-btn').forEach(b => b.classList.toggle('active', b.dataset.value === cardType));
  }
  openModal(modalId);
}

/**
 * Validates and submits edited routine details to server, then refreshes routine list and stats if update is successful
 * @returns {Promise<void>}
 */
async function editRoutine() {
  const name = document.getElementById('editNameInput');
  const xp = document.getElementById('editXpInput');
  const stepsInput = document.getElementById('editStepsInput');

  if (!name.value.trim()) { 
    name.style.borderColor = '#e57373'; 
    return; 
  }
  if (!xp.value.trim() || isNaN(xp.value.trim())) { 
    xp.style.borderColor = '#e57373'; 
    return; 
  }
  if (!stepsInput.value.trim()) { 
    stepsInput.style.borderColor = '#e57373'; 
    return; 
  }

  const body = new FormData();
  body.append('action', 'editRoutine');
  body.append('routine_id', currentEditCard.dataset.id);
  body.append('name', name.value.trim());
  body.append('xp_value', xp.value.trim());
  body.append('steps', stepsInput.value.trim());
  body.append('child_id', currentChildId);

  try {
    const res = await fetch('../../Controller/ParentDashboard/Routine.php', { method: 'POST', body });
    const data = await res.json();
    if (data.success) {
      closeModal('editRoutineModalOverlay');
      await loadRoutines(currentChildId);
      await loadStats(currentChildId);
    }
  } catch (e) {
    console.error(e);
  }
}

/**
 * Shows a confirmation dialog before deleting a routine, then sends delete request to server and refreshes routine list and stats if deletion is successful
 * @param {HTMLElement} card 
 * @returns {Promise<void>}
 */
async function confirmDeleteRoutine(card) {
  const name = card.querySelector('.routine-name').textContent;
  if (!confirm(`Do you really want to delete the routine "${name}" ? You will no longer be able to recover the data.`)) {
    return;
  }

  const body = new FormData();
  body.append('action', 'deleteRoutine');
  body.append('routine_id', card.dataset.id);
  body.append('child_id', currentChildId);

  try {
    const res = await fetch('../../Controller/ParentDashboard/Routine.php', { method: 'POST', body });
    const data = await res.json();
    if (data.success) {
      await loadRoutines(currentChildId);
      await loadStats(currentChildId);
    }
  } catch (e) {
    console.error(e);
  }
}

/**
 * Fetches rewards for selected child from server and renders them in rewards section of dashboard, allowing parent to view and manage rewards for that child
 * @param {string} childId 
 * @returns {Promise<void>}
 */
async function loadRewards(childId) {
  try {
    const res = await fetch(`../../Controller/ParentDashboard/Reward.php?action=getRewards&child_id=${childId}`);
    const data = await res.json();
    if (!data.success) {
      return;
    }
    renderRewards(data.rewards);
  } catch (e) {
    console.error(e);
  }
}

/**
 * Renders reward cards in rewards list
 * @param {Array} rewards 
 * @returns {void}
 */
function renderRewards(rewards) {
  document.getElementById('rewardsList').innerHTML = rewards.map(r => `
    <div class="reward-card" data-id="${r.id}" data-type="${r.type || 'out_app'}">
      <div class="reward-icon">${makeIcon(r.icon || 'icon-star')}</div>
      <div class="reward-info">
        <div class="reward-name">${r.name}</div>
        <div class="reward-pts">${r.xp_cost} XP</div>
        <div class="reward-status">
          <span class="spill ${r.is_completed ? 'sdone' : 'stodo'}">${r.is_completed ? '✓ Done' : 'To do'}</span>
        </div>
      </div>
      <div class="rew-actions">
        <button class="rew-edit" onclick="openEdit(this.closest('.reward-card'), 'editRewardModalOverlay')">
          <img src="../Assets/img/icon-edit.svg" alt="Edit"/>
        </button>
        <button class="rew-delete" onclick="confirmDeleteReward(this.closest('.reward-card'))">
          <img src="../Assets/img/icon-delete.svg" alt="Delete"/>
        </button>
      </div>
    </div>
  `).join('');
}

/**
 * Handles clicking reward type button and updates hidden input value
 * @param {HTMLElement} btn 
 * @param {string} hiddenId 
 * @returns {void}
 */
function selectRewardType(btn, hiddenId) {
  btn.closest('.type-toggle').querySelectorAll('.type-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById(hiddenId).value = btn.dataset.value;
}

/**
 * Validates and submits new reward form
 * @returns {Promise<void>}
 */
async function saveReward() {
  const name = document.getElementById('nameRewardInput');
  const xp = document.getElementById('xpRewardInput');
  const type = document.getElementById('rewardType').value;

  if (!name.value.trim()) { 
    name.style.borderColor = '#e57373'; 
    return; 
  }
  if (!xp.value.trim() || isNaN(xp.value.trim())) { 
    xp.style.borderColor   = '#e57373'; 
    return; 
  }

  const body = new FormData();
  body.append('action', 'addReward');
  body.append('name', name.value.trim());
  body.append('xp_cost', xp.value.trim());
  body.append('type', type);
  body.append('child_id', currentChildId);

  try {
    const res = await fetch('../../Controller/ParentDashboard/Reward.php', { method: 'POST', body });
    const data = await res.json();
    if (data.success) {
      name.value = ''; xp.value = '';
      document.getElementById('rewardType').value = 'out_app';
      document.querySelectorAll('#newRewardModalOverlay .type-btn').forEach((b, i) => b.classList.toggle('active', i === 0));
      closeModal('newRewardModalOverlay');
      await loadRewards(currentChildId);
    }
  } catch (e) {
    console.error(e);
  }
}

/**
 * Validates and submits edited reward details to server, then refreshes rewards list if update is successful
 * @returns {Promise<void>}
 */
async function editReward() {
  const name = document.getElementById('editRewardNameInput');
  const xp = document.getElementById('editRewardXpInput');
  const type = document.getElementById('editRewardType').value;

  if (!name.value.trim()) { 
    name.style.borderColor = '#e57373'; 
    return; 
  }
  if (!xp.value.trim() || isNaN(xp.value.trim())) { 
    xp.style.borderColor   = '#e57373'; 
    return; 
  }

  const body = new FormData();
  body.append('action', 'editReward');
  body.append('reward_id', currentEditCard.dataset.id);
  body.append('name', name.value.trim());
  body.append('xp_cost', xp.value.trim());
  body.append('type', type);
  body.append('child_id', currentChildId);

  try {
    const res = await fetch('../../Controller/ParentDashboard/Reward.php', { method: 'POST', body });
    const data = await res.json();
    if (data.success) {
      closeModal('editRewardModalOverlay');
      await loadRewards(currentChildId);
    }
  } catch (e) {
    console.error(e);
  }
}

/**
 * Shows confirmation dialog for deleting a reward and sends delete request to server
 * @param {HTMLElement} card 
 * @returns {Promise<void>}
 */
async function confirmDeleteReward(card) {
  const name = card.querySelector('.reward-name').textContent;
  if (!confirm(`Do you really want to delete the reward "${name}" ? You will no longer be able to recover the data.`)) {
    return;
  }

  const body = new FormData();
  body.append('action', 'deleteReward');
  body.append('reward_id', card.dataset.id);
  body.append('child_id', currentChildId);

  try {
    const res = await fetch('../../Controller/ParentDashboard/Reward.php', { method: 'POST', body });
    const data = await res.json();
    if (data.success) {
      await loadRewards(currentChildId);
    }
  } catch (e) {
    console.error(e);
  }
}

/**
 * Fetches feelings for child and renders them
 * @param {string} childId 
 * @returns {Promise<void>}
 */
async function loadFeelings(childId) {
  try {
    const res = await fetch(`../../Controller/HomePageChild/Feeling.php?action=getFeelings&child_id=${childId}`);
    const data = await res.json();
    if (!data.success) {
      return;
    }
    renderFeelings(data.feelings);
  } catch (e) {
    console.error(e);
  }
}

/**
 * Renders list of feelings in dashboard or shows a placeholder if there are none
 * @param {Array} feelings 
 * @returns {void}
 */
function renderFeelings(feelings) {
  if (feelings.length === 0) {
    document.getElementById('feelingsList').innerHTML = '<p style="color:#aaa;text-align:center;padding:24px;">No feelings recorded yet.</p>';
    return;
  }
  document.getElementById('feelingsList').innerHTML = feelings.map(f => `
    <div class="feeling-row">
      <div class="fmoji" style="font-size:1.5rem">${f.emoji}</div>
      <div class="fbody">
        <div class="ftext">${f.text || ''}</div>
        <div class="ftime">${formatDate(f.created_at)}</div>
      </div>
    </div>
  `).join('');
}

/**
 * Formats a date string into a readable English format (Mon dd, hh:mm)
 * @param {string} dateStr 
 * @returns {string}
 */
function formatDate(dateStr) {
  if (!dateStr) {
    return '';
  }
  return new Date(dateStr).toLocaleDateString('en-US', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
}

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.modal-input').forEach(field => {
    field.addEventListener('input', () => { field.style.borderColor = ''; });
  });
  loadInit();
});
