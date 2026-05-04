// ── Live clock ──
function updateTime() {
  const now = new Date();
  const days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
  const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
  const h = now.getHours().toString().padStart(2,'0');
  const m = now.getMinutes().toString().padStart(2,'0');
  document.getElementById('datetime').textContent =
    `${days[now.getDay()]} · ${months[now.getMonth()]} ${now.getDate()}, ${now.getFullYear()} · ${h}:${m}`;
}
updateTime();
setInterval(updateTime, 1000);

// ── Toast ──
function showToast(msg, type = 'success') {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.className = `toast ${type} show`;
  setTimeout(() => t.classList.remove('show'), 3000);
}

// ── Sync identity card on name input ──
function syncCard() {
  const first = document.getElementById('firstName').value.trim();
  const last  = document.getElementById('lastName').value.trim();
  const full  = [first, last].filter(Boolean).join(' ');
  const initials = [first[0], last[0]].filter(Boolean).join('').toUpperCase();
  document.getElementById('profileNameDisplay').textContent = full || 'User';
  const avatar = document.getElementById('profileAvatar');
  if (avatar && avatar.tagName !== 'IMG') avatar.textContent = initials || '?';
}

document.getElementById('firstName').addEventListener('input', syncCard);
document.getElementById('lastName').addEventListener('input', syncCard);

// ── Password strength ──
const rules = {
  'rule-len':     pw => pw.length >= 8,
  'rule-upper':   pw => /[A-Z]/.test(pw),
  'rule-num':     pw => /[0-9]/.test(pw),
  'rule-special': pw => /[^A-Za-z0-9]/.test(pw),
};

function updateStrength(pw) {
  let score = 0;
  Object.entries(rules).forEach(([id, fn]) => {
    const el = document.getElementById(id);
    const met = fn(pw);
    el.classList.toggle('met', met);
    if (met) score++;
  });

  const colors = ['', '#ef4444', '#f97316', '#eab308', '#15803d'];
  const container = document.getElementById('pwStrength');
  container.innerHTML = '';
  for (let i = 0; i < 4; i++) {
    const bar = document.createElement('div');
    bar.className = 'pw-strength-bar';
    bar.style.background = i < score ? colors[score] : '';
    container.appendChild(bar);
  }
}

document.getElementById('newPassword').addEventListener('input', e => {
  updateStrength(e.target.value);
});

// ── Show/hide password toggles ──
document.querySelectorAll('.toggle-pw').forEach(btn => {
  btn.addEventListener('click', () => {
    const input = document.getElementById(btn.dataset.target);
    input.type = input.type === 'password' ? 'text' : 'password';
  });
});

// ── Avatar upload ──

// ── Password form client-side validation ──
document.getElementById('passwordForm').addEventListener('submit', function (e) {
  const pw = document.getElementById('newPassword').value;
  const confirm = document.getElementById('confirmPassword').value;
  const current = document.getElementById('currentPassword').value;
  const errors = [];

  if (!current) errors.push('Current password is required.');
  if (pw.length < 8) errors.push('New password must be at least 8 characters.');
  if (!/[A-Z]/.test(pw)) errors.push('New password must contain an uppercase letter.');
  if (!/[0-9]/.test(pw)) errors.push('New password must contain a number.');
  if (!/[^A-Za-z0-9]/.test(pw)) errors.push('New password must contain a special character.');
  if (pw !== confirm) errors.push('Passwords do not match.');

  if (errors.length) {
    e.preventDefault();
    showToast(errors[0], 'error');
  }
});
