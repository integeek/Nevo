// ── Load ──────────────────────────────────────────────────────────────────────

/**
 * Loads parent information from server and populates form fields
 * @returns {Promise<void>}
 */
async function loadParent() {
  try {
    const res  = await fetch('../../Controller/ParentDashboard/SettingParent.php?action=getParent');
    const data = await res.json();
    if (!data.success) {
      return;
    }
    document.getElementById('parentNameInput').value  = data.parent.fullname;
    document.getElementById('parentEmailInput').value = data.parent.email;
  } catch (e) {
    console.error(e);
  }
}

/**
 * Validates and submits parent profile update form
 * Saves updated parent information to server, shows a brief confirmation message if update is successful
 * @returns {Promise<void>}
 */
async function saveParent() {
  const nameInput = document.getElementById('parentNameInput');
  const name = nameInput.value.trim();

  if (!name) {
    nameInput.style.borderColor = '#e57373';
    return;
  }

  const body = new FormData();
  body.append('action', 'updateParent');
  body.append('fullname', name);

  try {
    const res = await fetch('../../Controller/ParentDashboard/SettingParent.php', { method: 'POST', body });
    const data = await res.json();
    if (data.success) {
      const btn = document.getElementById('saveParentBtn');
      btn.textContent = 'Saved ✓';
      setTimeout(() => { btn.textContent = 'Save changes'; }, 2000);
    }
  } catch (e) {
    console.error(e);
  }
}

/**
 * Shows confirmation dialog and deletes parent's account from server permanently
 * Redirects to homepage after successful deletion
 * @returns {Promise<void>}
 */
async function deleteAccount() {
  if (!confirm('Are you sure you want to delete your account ? This action is irreversible and will delete all your family data.')) {
    return;
  }

  const body = new FormData();
  body.append('action', 'deleteAccount');

  try {
    const res = await fetch('../../Controller/ParentDashboard/SettingParent.php', { method: 'POST', body });
    const data = await res.json();
    if (data.success) {
      window.location.href = '../Page/HomeLogin.html';
    }
  } catch (e) {
    console.error(e);
  }
}

document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('parentNameInput').addEventListener('input', () => {
    document.getElementById('parentNameInput').style.borderColor = '';
  });
  loadParent();
});
