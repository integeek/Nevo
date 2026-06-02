const rows = Array.from(document.querySelectorAll('.patient-row'));
const searchInput = document.getElementById('searchInput');
let activeFilter = 'all';

function applyFilters() {
  const q = searchInput.value.toLowerCase();
    rows.forEach(row => {
    const text = row.innerText.toLowerCase();
    const matchSearch = !q || text.includes(q);
    const show = matchSearch;
    row.style.display = show ? 'flex' : 'none';
  });
}
searchInput.addEventListener('input', applyFilters);