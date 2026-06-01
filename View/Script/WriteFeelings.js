let selectedFeeling = null;
let selectedIconName = null;

document.querySelectorAll('.feeling-btn').forEach(btn => {
  btn.addEventListener('click', (e) => {
    e.preventDefault();
    document.querySelectorAll('.feeling-btn').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
    selectedFeeling = btn.dataset.feeling;
    selectedIconName = btn.dataset.emoji;
  });
});

document.getElementById('shareBtn').addEventListener('click', async () => {
  if (!selectedFeeling || !selectedIconName) {
    return;
  }

  const note = document.getElementById('noteArea').value.trim();
  const fullText = note || selectedFeeling;

  const formData = new FormData();
  formData.append('feeling', selectedFeeling);
  formData.append('emoji', selectedIconName);
  formData.append('note', note);

  try {
    const response = await fetch('../../Controller/HomePageChild/Feeling.php', {
      method: 'POST',
      body: formData
    });

    const data = await response.json();

    if (!data.success) {
      console.error('Erreur BDD :', data.message);
      return;
    }
  } catch (err) {
    console.error('Erreur réseau :', err);
    return;
  }

  await loadFeelings();

  document.querySelectorAll('.feeling-btn').forEach(b => b.classList.remove('selected'));
  document.getElementById('noteArea').value = '';
  selectedFeeling = null;
  selectedIconName = null;
});

/**
 * Fetches recent feelings from server and renders them in recent feelings list, displaying emoji, note, and formatted date for each feeling
 * @returns {Promise<void>}
 */
async function loadFeelings() {
  try {
    const response = await fetch('../../Controller/HomePageChild/Feeling.php?action=getFeelings');
    const data = await response.json();

    if (!data.success) {
      console.error('Erreur :', data.message);
      return;
    }

    const list = document.getElementById('recentList');
    list.innerHTML = '';

    data.feelings.forEach(feeling => {
      const div = document.createElement('div');
      div.className = 'feeling-entry';
      div.innerHTML = `
        <div class="entry-emoji">
          <img src="../Assets/img/${feeling.emoji}.svg" alt="${feeling.text} icon" class="icon-feelings" />
        </div>
        <div class="entry-body">
          <div class="entry-date">${formatDate(feeling.created_at)}</div>
          <div class="entry-note">${feeling.text}</div>
        </div>
      `;
      list.appendChild(div);
    });

  } catch (err) {
    console.error('Erreur réseau :', err);
  }
}

/**
 * Formats date string into "DD MMM HH:mm" format for display in recent feelings list
 * @param {*} dateStr 
 * @returns 
 */
function formatDate(dateStr) {
  const date = new Date(dateStr);
  return date.toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: 'short',
    hour: '2-digit',
    minute: '2-digit'
  });
}

document.addEventListener('DOMContentLoaded', loadFeelings);