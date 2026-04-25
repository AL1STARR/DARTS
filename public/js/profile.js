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

// ── Sync identity card ──
function syncCard() {
  const first = document.getElementById('firstName').value.trim();
  const last  = document.getElementById('lastName').value.trim();
  const full  = [first, last].filter(Boolean).join(' ');
  const initials = [first[0], last[0]].filter(Boolean).join('').toUpperCase();

  document.getElementById('profileNameDisplay').textContent = full || 'User';
  document.getElementById('profileAvatar').textContent = initials || '?';
  document.getElementById('displayName').value = full;
}

// ── Personal Information ──
const originalPersonal = {
  firstName: document.getElementById('firstName').value,
  lastName:  document.getElementById('lastName').value,
};

function savePersonal() {
  const first = document.getElementById('firstName').value.trim();
  const last  = document.getElementById('lastName').value.trim();

  if (!first || !last) {
    showToast('First and last name are required.', 'error');
    return;
  }

  syncCard();
  originalPersonal.firstName = first;
  originalPersonal.lastName  = last;
  showToast('Personal information updated successfully.');
}

function resetPersonal() {
  document.getElementById('firstName').value   = originalPersonal.firstName;
  document.getElementById('lastName').value    = originalPersonal.lastName;
  document.getElementById('displayName').value = `${originalPersonal.firstName} ${originalPersonal.lastName}`;
}

// Live sync display name as user types
document.getElementById('firstName').addEventListener('input', syncCard);
document.getElementById('lastName').addEventListener('input', syncCard);

// ── Email ──
const originalEmail = document.getElementById('currentEmail').value;

function saveEmail() {
  const current = document.getElementById('currentEmail').value.trim();
  const next    = document.getElementById('newEmail').value.trim();
  const confirm = document.getElementById('confirmEmail').value.trim();

  const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  if (!next) { showToast('Please enter a new email address.', 'error'); return; }
  if (!emailRe.test(next)) { showToast('Please enter a valid email address.', 'error'); return; }
  if (next === current) { showToast('New email must be different from current email.', 'error'); return; }
  if (next !== confirm) { showToast('Email addresses do not match.', 'error'); return; }

  document.getElementById('currentEmail').value = next;
  document.getElementById('profileEmailDisplay').textContent = next;
  document.getElementById('newEmail').value    = '';
  document.getElementById('confirmEmail').value = '';
  showToast('Email address updated successfully.');
}

function resetEmail() {
  document.getElementById('currentEmail').value = originalEmail;
  document.getElementById('newEmail').value     = '';
  document.getElementById('confirmEmail').value = '';
}

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
  const labels = ['', 'Weak', 'Fair', 'Good', 'Strong'];
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

// ── Password save ──
function savePassword() {
  const current = document.getElementById('currentPassword').value;
  const next    = document.getElementById('newPassword').value;
  const confirm = document.getElementById('confirmPassword').value;

  if (!current) { showToast('Please enter your current password.', 'error'); return; }
  if (!next)    { showToast('Please enter a new password.', 'error'); return; }

  const allMet = Object.values(rules).every(fn => fn(next));
  if (!allMet) { showToast('Password does not meet all requirements.', 'error'); return; }
  if (next !== confirm) { showToast('Passwords do not match.', 'error'); return; }

  document.getElementById('currentPassword').value = '';
  document.getElementById('newPassword').value     = '';
  document.getElementById('confirmPassword').value = '';
  document.getElementById('pwStrength').innerHTML  = '';
  Object.keys(rules).forEach(id => document.getElementById(id).classList.remove('met'));
  showToast('Password updated successfully.');
}

function resetPassword() {
  document.getElementById('currentPassword').value = '';
  document.getElementById('newPassword').value     = '';
  document.getElementById('confirmPassword').value = '';
  document.getElementById('pwStrength').innerHTML  = '';
  Object.keys(rules).forEach(id => document.getElementById(id).classList.remove('met'));
}

// ── Show/hide password toggles ──
document.querySelectorAll('.toggle-pw').forEach(btn => {
  btn.addEventListener('click', () => {
    const input = document.getElementById(btn.dataset.target);
    input.type = input.type === 'password' ? 'text' : 'password';
  });
});

// ── Avatar color cycle ──
const avatarColors = ['#0066cc','#7c3aed','#0891b2','#059669','#c2410c','#be185d'];
let colorIdx = 0;
document.getElementById('avatarEditBtn').addEventListener('click', () => {
  colorIdx = (colorIdx + 1) % avatarColors.length;
  document.getElementById('profileAvatar').style.background = avatarColors[colorIdx];
});
