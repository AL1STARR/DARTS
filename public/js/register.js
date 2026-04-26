const eyeOpen  = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>`;
const eyeClosed = `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>`;

function setupToggle(btnId, inputId, iconId) {
  document.getElementById(btnId).addEventListener('click', () => {
    const input = document.getElementById(inputId);
    const show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    document.getElementById(iconId).innerHTML = show ? eyeClosed : eyeOpen;
  });
}

setupToggle('togglePw1', 'regPassword', 'eyeIcon1');
setupToggle('togglePw2', 'confirmPassword', 'eyeIcon2');

// Password strength hints
const pwInput = document.getElementById('regPassword');
const hintLen   = document.getElementById('hint-len');
const hintUpper = document.getElementById('hint-upper');
const hintNum   = document.getElementById('hint-num');

pwInput.addEventListener('input', () => {
  const v = pwInput.value;
  hintLen.classList.toggle('met',   v.length >= 8);
  hintUpper.classList.toggle('met', /[A-Z]/.test(v));
  hintNum.classList.toggle('met',   /[0-9]/.test(v));
});

// Validation
function setError(id, msg) {
  const el = document.getElementById(id);
  el.textContent = msg;
  if (msg) el.previousElementSibling?.classList?.add('error');
  else el.previousElementSibling?.classList?.remove('error');
}

function clearError(id) { setError(id, ''); }

document.getElementById('registerForm').addEventListener('submit', e => {
  e.preventDefault();
  let valid = true;

  const firstName = document.getElementById('firstName').value.trim();
  const lastName  = document.getElementById('lastName').value.trim();
  const email     = document.getElementById('regEmail').value.trim();
  const dept      = document.getElementById('department').value;
  const role      = document.getElementById('role').value;
  const pw        = document.getElementById('regPassword').value;
  const cpw       = document.getElementById('confirmPassword').value;
  const terms     = document.getElementById('terms').checked;

  if (!firstName) { setError('firstNameError', 'First name is required.'); valid = false; }
  else clearError('firstNameError');

  if (!lastName) { setError('lastNameError', 'Last name is required.'); valid = false; }
  else clearError('lastNameError');

  if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { setError('regEmailError', 'Enter a valid email address.'); valid = false; }
  else clearError('regEmailError');

  if (!dept) { setError('departmentError', 'Select a department.'); valid = false; }
  else clearError('departmentError');

  if (!role) { setError('roleError', 'Select a role.'); valid = false; }
  else clearError('roleError');

  if (!pw || pw.length < 8) { setError('regPasswordError', 'Password must be at least 8 characters.'); valid = false; }
  else clearError('regPasswordError');

  if (pw !== cpw) { setError('confirmPasswordError', 'Passwords do not match.'); valid = false; }
  else clearError('confirmPasswordError');

  if (!terms) { document.getElementById('termsError').textContent = 'You must agree to the terms.'; valid = false; }
  else document.getElementById('termsError').textContent = '';

  if (valid) {
    document.getElementById('registerForm').submit();
  }
});
