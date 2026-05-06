// Enable and filter roles when department is selected
document.getElementById('department').addEventListener('change', function () {
  const roleSelect = document.getElementById('role');
  const selected = this.value;
  roleSelect.disabled = !selected;
  roleSelect.value = '';
  Array.from(roleSelect.options).forEach(opt => {
    if (!opt.value) return;
    const match = opt.dataset.dept === selected;
    opt.disabled = !match;
    opt.hidden   = !match;
  });
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

  if (!terms) { document.getElementById('termsError').textContent = 'You must agree to the terms.'; valid = false; }
  else document.getElementById('termsError').textContent = '';

  if (valid) {
    document.getElementById('registerForm').submit();
  }
});
