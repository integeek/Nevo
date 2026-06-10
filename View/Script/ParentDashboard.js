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
  'icon-alarm':     'linear-gradient(135deg,#ffe082,#ffd54f)',
  'icon-book-solo': 'linear-gradient(135deg,#c5cae9,#9fa8da)',
  'icon-pills':  'linear-gradient(135deg,#b2dfdb,#80cbc4)',
  'icon-sport':     'linear-gradient(135deg,#ffccbc,#ffab91)',
  'drop-water':    'linear-gradient(135deg,#ce93d8,#ba68c8)',
  'icon-trophy':    'linear-gradient(135deg,#d7ccc8,#b0bec5)',
};

const makeIcon = (name) => {
  const src = name && name.startsWith('../') ? name : `../Assets/img/${name || 'icon-alarm'}.svg`;
  return `<img src="${src}" style="width:24px;height:24px;object-fit:contain;">`;
};

function openModal(id) {
  if (id === 'newRoutineModalOverlay') {
    document.getElementById('routineIcon').value = 'icon-alarm';
    document.querySelectorAll('#newRoutineModalOverlay .avatar-option').forEach((b, i) => b.classList.toggle('selected-avatar', i === 0));
  }
  document.getElementById(id).classList.add('active');
  setTimeout(() => document.getElementById(id).focus(), 100);
}

function closeModal(id) {
  document.getElementById(id).classList.remove('active');
}

function pickRoutineIcon(el, hiddenId) {
  const picker = el.closest('.avatar-picker');
  if (!picker) return;
  picker.querySelectorAll('.avatar-option').forEach(a => a.classList.remove('selected-avatar'));
  el.classList.add('selected-avatar');
  document.getElementById(hiddenId).value = el.dataset.icon;
}

function switchTab(btn, tab) {
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById('tab-' + tab).classList.add('active');
}

function toggleSteps(btn) {
  const item = btn.closest('.routine-item');
  item.querySelector('.routine-substeps').classList.toggle('open');
  btn.classList.toggle('open');
}

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

function switchChildByPill(childId, el) {
  document.querySelectorAll('.child-pill').forEach(p => p.classList.remove('active'));
  el.classList.add('active');
  switchChild(childId);
}

async function switchChild(childId) {
  currentChildId = childId;
  await Promise.all([
    loadStats(childId),
    loadRoutines(childId),
    loadRewards(childId),
    loadFeelings(childId),
    loadAnalytics(childId),
  ]);
}

async function loadStats(childId) {
  try {
    const res = await fetch(`../../Controller/ParentDashboard/ParentDashboard.php?action=getStats&child_id=${childId}`);
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

function renderRoutines(routines) {
  document.getElementById('routinesList').innerHTML = routines.map(r => `
    <div class="routine-item" data-id="${r.id}" data-icon="${r.icon || 'icon-alarm'}">
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
  body.append('icon', document.getElementById('routineIcon').value);
  body.append('xp_value', xp.value.trim());
  body.append('steps', stepsInput.value.trim());
  body.append('child_id', currentChildId);

  try {
    const res = await fetch('../../Controller/ParentDashboard/Routine.php', { method: 'POST', body });
    const data = await res.json();
    if (data.success) {
      name.value = ''; xp.value = ''; stepsInput.value = '';
      document.getElementById('routineIcon').value = 'icon-alarm';
      document.querySelectorAll('#newRoutineModalOverlay .avatar-option').forEach((b, i) => b.classList.toggle('selected-avatar', i === 0));
      closeModal('newRoutineModalOverlay');
      await loadRoutines(currentChildId);
      await loadStats(currentChildId);
    }
  } catch (e) {
    console.error(e);
  }
}

function openEdit(card, modalId) {
  currentEditCard = card;
  if (modalId === 'editRoutineModalOverlay') {
    document.getElementById('editNameInput').value  = card.querySelector('.routine-name').textContent;
    document.getElementById('editXpInput').value    = card.querySelector('.ri').textContent.replace(' XP', '');
    const steps = [...card.querySelectorAll('.substep span')].map(s => s.textContent).join('\n');
    document.getElementById('editStepsInput').value = steps;
    const icon = card.dataset.icon || 'icon-alarm';
    document.getElementById('editRoutineIcon').value = icon;
    document.querySelectorAll('#editRoutineIconPicker .avatar-option').forEach(b => b.classList.toggle('selected-avatar', b.dataset.icon === icon));
  } else if (modalId === 'editRewardModalOverlay') {
    document.getElementById('editRewardNameInput').value = card.querySelector('.reward-name').textContent;
    document.getElementById('editRewardXpInput').value   = card.querySelector('.reward-pts').textContent.replace(' XP', '');
    const cardType = card.dataset.type || 'out_app';
    document.getElementById('editRewardType').value = cardType;
    document.querySelectorAll('#editRewardModalOverlay .type-btn').forEach(b => b.classList.toggle('active', b.dataset.value === cardType));
  }
  openModal(modalId);
}

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
  body.append('icon', document.getElementById('editRoutineIcon').value);
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

function selectRewardType(btn, hiddenId) {
  btn.closest('.type-toggle').querySelectorAll('.type-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById(hiddenId).value = btn.dataset.value;
}

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

async function loadFeelings(childId) {
  try {
    const res  = await fetch(`../../Controller/ParentDashboard/ParentDashboard.php?action=getFeelings&child_id=${childId}`);
    const data = await res.json();
    if (!data.success) return;
    renderFeelings(data.feelings);
  } catch (e) {
    console.error(e);
  }
}

function renderFeelings(feelings) {
  if (feelings.length === 0) {
    document.getElementById('feelingsList').innerHTML = '<p style="color:#aaa;text-align:center;padding:24px;">No feelings recorded yet.</p>';
    return;
  }
  document.getElementById('feelingsList').innerHTML = feelings.map(f => `
    <div class="feeling-row">
      <img src="../Assets/img/${f.emoji}.svg" alt="${f.emoji}" style="width:32px;height:32px;flex-shrink:0;">
      <div class="fbody">
        <div class="ftag">${f.emoji.replace('icon-', '')}</div>
        <div class="ftext">${f.text || '—'}</div>
        <div class="ftime">${formatDate(f.created_at)}</div>
      </div>
    </div>
  `).join('');
}

async function loadAnalytics(childId) {
  try {
    const res = await fetch(`../../Controller/ParentDashboard/ParentDashboard.php?action=getAnalytics&child_id=${childId}`);
    const data = await res.json();
    if (!data.success) {
      return;
    }
    renderAnalytics(data);
  } catch (e) {
    console.error(e);
  }
}

function renderAnalytics(data) {
  const FEELING_LABELS = {
    'icon-awesome': 'Awesome', 'icon-good': 'Good', 'icon-lost': 'Lost',
    'icon-hurt': 'Hurt', 'icon-sad': 'Sad', 'icon-angry': 'Angry'
  };

  const COLORS = {
    'icon-awesome':'#3dbfa0','icon-good':'#60a5fa','icon-lost':'#a78bfa',
    'icon-hurt':'#f87171','icon-sad':'#fbbf24','icon-angry':'#f97316'
  };

  const done = data.routines_done;
  const pending = data.routines_pending;
  const total = data.routines_total;
  const donePct = total > 0 ? Math.round(done / total * 100) : 0;
  const pendingPct = total > 0 ? Math.round(pending / total * 100) : 0;

  document.getElementById('routineChart').innerHTML = total === 0
    ? '<p style="color:#aaa;font-size:0.82rem;padding:4px 0;">No routines yet.</p>'
    : `
    <div style="display:flex;gap:20px;margin-bottom:18px;">
      <div style="text-align:center;">
        <div style="font-size:1.6rem;font-weight:800;color:#3dbfa0;">${donePct}%</div>
        <div style="font-size:0.7rem;color:#7a9490;font-weight:600;">Done</div>
      </div>
      <div style="text-align:center;">
        <div style="font-size:1.6rem;font-weight:800;color:#f59e0b;">${pendingPct}%</div>
        <div style="font-size:0.7rem;color:#7a9490;font-weight:600;">Pending</div>
      </div>
      <div style="text-align:center;">
        <div style="font-size:1.6rem;font-weight:800;color:#555;">${total}</div>
        <div style="font-size:0.7rem;color:#7a9490;font-weight:600;">Total</div>
      </div>
    </div>
    <div class="cat-row">
      <div class="cname">✓ Done</div>
      <div class="ctrack"><div class="cfill" style="width:${donePct}%;background:#3dbfa0;"></div></div>
      <div class="cpct">${done}</div>
    </div>
    <div class="cat-row">
      <div class="cname">⏳ Pending</div>
      <div class="ctrack"><div class="cfill" style="width:${pendingPct}%;background:#f59e0b;"></div></div>
      <div class="cpct">${pending}</div>
    </div>`;

  const counts = data.feelings_counts || {};
  const ftotal = data.feelings_total || 0;
  const entries = Object.entries(counts);
  document.getElementById('feelingsChart').innerHTML = entries.length === 0
    ? '<p style="color:#aaa;font-size:0.82rem;padding:4px 0;">No feelings logged yet.</p>'
    : `<div style="font-size:0.75rem;color:#7a9490;margin-bottom:12px;">${ftotal} feelings recorded</div>` +
      entries.map(([emoji, count]) => {
        const pct = ftotal > 0 ? Math.round(count / ftotal * 100) : 0;
        const label = FEELING_LABELS[emoji] || emoji.replace('icon-','');
        const color = COLORS[emoji] || '#aaa';
        return `
        <div class="cat-row">
          <div class="cname">
            <img src="../Assets/img/${emoji}.svg" style="width:15px;height:15px;flex-shrink:0;">
            ${label}
          </div>
          <div class="ctrack"><div class="cfill" style="width:${pct}%;background:${color};"></div></div>
          <div class="cpct">${pct}%</div>
        </div>`;
      }).join('');
}

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