// CHILD_ID is injected by PatientDetail.php as a global variable

const AVATAR_BG = {
  'icon-superhero': 'linear-gradient(135deg,#f4845f,#e8623a)',
  'icon-butterfly': 'linear-gradient(135deg,#64b5f6,#1976d2)',
  'icon-unicorn':   'linear-gradient(135deg,#81c784,#388e3c)',
  'icon-fish':      'linear-gradient(135deg,#f06292,#c2185b)',
  'icon-penguin':   'linear-gradient(135deg,#ffb74d,#e65100)',
  'icon-cat':       'linear-gradient(135deg,#fbbf24,#d97706)',
  'icon-tiger':     'linear-gradient(135deg,#f87171,#dc2626)',
};

// ── Tab switching ─────────────────────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
      btn.classList.add('active');
      document.getElementById('tab-' + btn.dataset.tab).classList.add('active');
    });
  });

  loadPatient();
  loadRoutines();
  loadFeelings();
});

// ── Patient header ────────────────────────────────────────────────────────────

async function loadPatient() {
  try {
    const res  = await fetch(`../../Controller/MedicalDashboard/PatientDetail.php?action=getPatient&child_id=${CHILD_ID}`);
    const data = await res.json();
    if (!data.success) return;

    const c  = data.child;
    const bg = AVATAR_BG[c.avatar] || 'linear-gradient(135deg,#c084fc,#7c3aed)';

    document.getElementById('pageTitle').textContent = `${c.fullname}'s profile`;

    document.getElementById('patientAvatar').innerHTML = `
      <div style="width:56px;height:56px;border-radius:16px;background:${bg};display:flex;align-items:center;justify-content:center;">
        <img src="../Assets/img/${c.avatar || 'icon-superhero'}.svg" style="width:34px;height:34px;" alt="avatar">
      </div>`;

    const disease = c.disease ? `<span class="badge" style="background:#dbeafe;color:#1d4ed8">${c.disease}</span>` : '';
    document.getElementById('patientName').innerHTML =
      `${c.fullname} <span style="font-size:0.85rem;font-weight:500;color:#7a9490;">· ${c.age} y/o</span> ${disease}`;

    document.getElementById('patientMeta').textContent = `⭐ ${c.xp ?? 0} XP · 🔥 ${c.streak ?? 0} days streak`;

    document.getElementById('statRoutines').textContent = data.routines + ' routines · ' + data.completion;
    document.getElementById('statStreak').textContent   = (c.streak ?? 0) + ' days';
  } catch (e) {
    console.error(e);
  }
}

// ── Routines ──────────────────────────────────────────────────────────────────

async function loadRoutines() {
  try {
    const res  = await fetch(`../../Controller/MedicalDashboard/PatientDetail.php?action=getRoutines&child_id=${CHILD_ID}`);
    const data = await res.json();
    if (!data.success) return;
    renderRoutines(data.routines);
  } catch (e) {
    console.error(e);
  }
}

function renderRoutines(routines) {
  const list = document.getElementById('routinesList');

  if (!routines || routines.length === 0) {
    list.innerHTML = '<p style="color:#aaa;font-size:0.85rem;padding:8px 0;">No routines assigned.</p>';
    return;
  }

  list.innerHTML = routines.map(r => {
    const steps    = r.steps || [];
    const done     = steps.filter(s => s.is_completed).length;
    const allDone  = r.is_completed || (steps.length > 0 && done === steps.length);
    const stepsHtml = steps.map(s => `
      <div class="activity-item">
        <div class="activity-dot" style="background:${s.is_completed ? '#dcfce7' : '#f4f2ee'};">
          ${s.is_completed ? '✓' : '·'}
        </div>
        <div class="activity-text">
          <div class="atitle" style="${s.is_completed ? 'text-decoration:line-through;color:#aaa' : ''}">${s.name}</div>
        </div>
      </div>`).join('');
    return `
      <div style="background:#fafaf8;border:1px solid #e4e0da;border-radius:14px;padding:13px 15px;margin-bottom:10px;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:${steps.length ? '10px' : '0'};">
          <div style="width:36px;height:36px;border-radius:10px;background:${allDone ? '#dcfce7' : '#fff8e1'};display:flex;align-items:center;justify-content:center;">
            <img src="../Assets/img/${r.icon || 'icon-alarm'}.svg" style="width:20px;height:20px;" alt="">
          </div>
          <div style="flex:1;">
            <div style="font-size:0.88rem;font-weight:700;${allDone ? 'text-decoration:line-through;color:#aaa' : ''}">${r.name}</div>
            <div style="font-size:0.72rem;color:#7a9490;">${r.xp_value} XP · ${done}/${steps.length} steps</div>
          </div>
          ${allDone ? '<span class="badge" style="background:#dcfce7;color:#16a34a;">✓ Done</span>' : ''}
        </div>
        <div class="activity-list">${stepsHtml}</div>
      </div>`;
  }).join('');
}

// ── Feelings ──────────────────────────────────────────────────────────────────

async function loadFeelings() {
  try {
    const res  = await fetch(`../../Controller/MedicalDashboard/PatientDetail.php?action=getFeelings&child_id=${CHILD_ID}`);
    const data = await res.json();
    if (!data.success) return;
    renderFeelings(data.feelings);
  } catch (e) {
    console.error(e);
  }
}

function renderFeelings(feelings) {
  const list = document.getElementById('feelingsList');

  if (!feelings || feelings.length === 0) {
    list.innerHTML = '<p style="color:#aaa;font-size:0.85rem;padding:8px 0;">No feelings logged yet.</p>';
    return;
  }

  list.innerHTML = feelings.map(f => {
    const date = new Date(f.created_at);
    const time = date.toLocaleDateString('en-GB', { day: 'numeric', month: 'short' })
               + ', ' + date.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });
    return `
      <div class="journal-item">
        <div class="journal-emoji">
          <img src="../Assets/img/${f.emoji}.svg" alt="${f.emoji}" style="width:32px;height:32px;">
        </div>
        <div class="journal-body">
          <div class="jtext">${f.text || '—'}</div>
          <div class="jtime">${time}</div>
        </div>
      </div>`;
  }).join('');
}
