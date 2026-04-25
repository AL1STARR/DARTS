// Live clock
function updateTime() {
  const now = new Date();
  const days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
  const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
  const h = now.getHours().toString().padStart(2,'0');
  const m = now.getMinutes().toString().padStart(2,'0');
  document.getElementById('datetime').textContent =
    `${days[now.getDay()]} | ${months[now.getMonth()]} ${now.getDate()}, ${now.getFullYear()} | ${h}:${m}`;
}
updateTime();
setInterval(updateTime, 1000);

// Filters
const statusFilter   = document.getElementById('statusFilter');
const priorityFilter = document.getElementById('priorityFilter');
const rows           = document.querySelectorAll('#assignedBody tr');

function applyFilters() {
  const s = statusFilter.value.toLowerCase();
  const p = priorityFilter.value.toLowerCase();

  rows.forEach(row => {
    const status   = row.querySelector('.badge-status').textContent.toLowerCase();
    const priority = row.querySelector('.badge-priority').textContent.toLowerCase();
    const show = (!s || status.includes(s)) && (!p || priority.includes(p));
    row.style.display = show ? '' : 'none';
  });
}

statusFilter.addEventListener('change', applyFilters);
priorityFilter.addEventListener('change', applyFilters);

document.getElementById('clearFilters').addEventListener('click', () => {
  statusFilter.value = '';
  priorityFilter.value = '';
  applyFilters();
});

// Pagination buttons (UI only)
document.querySelectorAll('.page-num').forEach(btn => {
  btn.addEventListener('click', function () {
    document.querySelectorAll('.page-num').forEach(b => b.classList.remove('active'));
    this.classList.add('active');
  });
});
