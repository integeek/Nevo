let selectedFeeling = null;
let selectedIconName = null;

document.querySelectorAll('.feeling-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.feeling-btn').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
    selectedFeeling = btn.dataset.feeling;
    selectedIconName = btn.dataset.emoji;
  });
});

document.getElementById('shareBtn').addEventListener('click', () => {
  if (!selectedFeeling || !selectedIconName) {
    return;
  }

  const note = document.getElementById('noteArea').value.trim();
  addEntry(selectedIconName, selectedFeeling, note || selectedFeeling);

  document.querySelectorAll('.feeling-btn').forEach(b => b.classList.remove('selected'));
  document.getElementById('noteArea').value = '';
  selectedFeeling = null;
  selectedIconName = null;
});

function addEntry(iconName, feeling, note) {
  const list = document.getElementById('recentList');
  const div = document.createElement('div');
  div.className = 'feeling-entry';
  div.style.animationDelay = '0s';
  div.innerHTML = `
    <div class="entry-emoji">
    <img src="../Assets/img/${iconName}.svg" alt="${feeling} icon" class="icon-feelings" />
    </div>
    <div class="entry-body">
    <div class="entry-date">Just now</div>
    <div class="entry-note">${note}</div>
    </div>
  `;
  list.prepend(div);
}