const form = document.getElementById('loginForm');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const emailError = document.getElementById('emailError');
const passwordError = document.getElementById('passwordError');
const togglePw = document.getElementById('togglePw');
const eyeIcon = document.getElementById('eyeIcon');

const eyeOpen  = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>`;
const eyeClosed = `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>`;

togglePw.addEventListener('click', () => {
  const show = passwordInput.type === 'password';
  passwordInput.type = show ? 'text' : 'password';
  eyeIcon.innerHTML = show ? eyeClosed : eyeOpen;
});

function validate() {
  let valid = true;

  const emailVal = emailInput.value.trim();
  if (!emailVal || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailVal)) {
    emailError.textContent = 'Enter a valid email address.';
    emailInput.classList.add('error');
    valid = false;
  } else {
    emailError.textContent = '';
    emailInput.classList.remove('error');
  }

  if (!passwordInput.value) {
    passwordError.textContent = 'Password is required.';
    passwordInput.classList.add('error');
    valid = false;
  } else {
    passwordError.textContent = '';
    passwordInput.classList.remove('error');
  }

  return valid;
}

form.addEventListener('submit', (e) => {
  if (!validate()) e.preventDefault();
});

// ── Forgot Password Modal ──
const forgotModal = document.getElementById('forgotModal');
document.getElementById('forgotLink').addEventListener('click', (e) => {
  e.preventDefault();
  forgotModal.classList.add('show');
});
document.getElementById('forgotClose').addEventListener('click', () => {
  forgotModal.classList.remove('show');
});
forgotModal.addEventListener('click', (e) => {
  if (e.target === forgotModal) forgotModal.classList.remove('show');
});


