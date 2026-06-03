const AVATAR_BG = {
  'icon-superhero': 'linear-gradient(135deg,#f4845f,#e8623a)',
  'icon-butterfly': 'linear-gradient(135deg,#64b5f6,#1976d2)',
  'icon-unicorn':   'linear-gradient(135deg,#81c784,#388e3c)',
  'icon-fish':      'linear-gradient(135deg,#f06292,#c2185b)',
  'icon-penguin':   'linear-gradient(135deg,#ffb74d,#e65100)',
  'icon-cat':       'linear-gradient(135deg,#fbbf24,#d97706)',
  'icon-tiger':     'linear-gradient(135deg,#f87171,#dc2626)',
};

let allPatients = [];

async function loadInit() {
  try {
    const res  = await fetch('../../Controller/MedicalDashboard/MedicalDashboard.php?action=init');
    const data = await res.json();
    if (!data.success) return;

    document.getElementById('greetingTitle').textContent    = `Welcome back, ${data.staff.fullname}`;
    document.getElementById('greetingSubtitle').textContent = `You're following ${data.patients.length} patient${data.patients.length !== 1 ? 's' : ''}`;

    allPatients = data.patients;
    renderPatients(data.patients);
  } catch (e) {
    console.error(e);
  }
}

function renderPatients(patients) {
  const list = document.getElementById('patientsList');

  if (patients.length === 0) {
    list.innerHTML = '<div style="padding:24px;text-align:center;color:#aaa;font-size:0.85rem;">No patients assigned yet.</div>';
    return;
  }

  list.innerHTML = patients.map(p => {
    const bg      = AVATAR_BG[p.avatar] || 'linear-gradient(135deg,#c084fc,#7c3aed)';
    const disease = p.disease ? `<span class="badge" style="background:#dbeafe;color:#1d4ed8">${p.disease}</span>` : '';
    return `
      <div class="patient-row" onclick="window.location='PatientDetail.php?child_id=${p.id}'">
        <div class="patient-avatar" style="background:${bg}">
          <img src="../Assets/img/${p.avatar || 'icon-superhero'}.svg" alt="avatar" style="width:26px;height:26px;">
        </div>
        <div class="patient-info">
          <div class="patient-name-line">
            <span class="patient-name">${p.fullname}</span>
            <span class="patient-age">· ${p.age} y/o</span>
            ${disease}
          </div>
          <div class="patient-meta">⭐ ${p.xp ?? 0} XP &nbsp;·&nbsp; 🔥 ${p.streak ?? 0} days streak</div>
        </div>
        <div class="chevron">›</div>
      </div>
    `;
  }).join('');
}

function applySearch() {
  const q = document.getElementById('searchInput').value.toLowerCase().trim();
  if (!q) { renderPatients(allPatients); return; }
  renderPatients(allPatients.filter(p =>
    p.fullname.toLowerCase().includes(q) ||
    (p.disease || '').toLowerCase().includes(q)
  ));
}

document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('searchInput').addEventListener('input', applySearch);
  loadInit();
});
