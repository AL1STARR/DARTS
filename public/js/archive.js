// ── Live clock ──
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

// ── Search on enter ──
document.getElementById('searchInput').addEventListener('keydown', e => {
  if (e.key === 'Enter') {
    const url = new URL(window.location.href);
    url.searchParams.set('search', e.target.value);
    url.searchParams.delete('page');
    window.location.href = url.toString();
  }
});

const csrfToken = () =>
  document.querySelector('meta[name="csrf-token"]')?.content ||
  document.querySelector('input[name="_token"]')?.value;

// ── Upload modal ──
const overlay    = document.getElementById('modalOverlay');
const uploadForm = document.getElementById('uploadForm');
const fileInput  = document.getElementById('fileInput');
const dropZone   = document.getElementById('dropZone');

const openModal  = () => { overlay.classList.add('open'); resetUploadForm(); };
const closeModal = () => overlay.classList.remove('open');

document.getElementById('uploadBtn').addEventListener('click', openModal);
document.getElementById('modalClose').addEventListener('click', closeModal);
document.getElementById('modalCancel').addEventListener('click', closeModal);
overlay.addEventListener('click', e => { if (e.target === overlay) closeModal(); });

dropZone.addEventListener('dragover',  e => { e.preventDefault(); dropZone.classList.add('dragover'); });
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
dropZone.addEventListener('drop', e => {
  e.preventDefault();
  dropZone.classList.remove('dragover');
  if (e.dataTransfer.files.length) setFile(e.dataTransfer.files[0]);
});
dropZone.addEventListener('click', () => fileInput.click());
fileInput.addEventListener('change', () => { if (fileInput.files[0]) setFile(fileInput.files[0]); });

function setFile(file) {
  const info = document.getElementById('selectedFile');
  info.textContent = `${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
  info.style.display = 'block';
}

function resetUploadForm() {
  uploadForm.reset();
  fileInput.value = '';
  document.getElementById('selectedFile').style.display = 'none';
  ['errTitle', 'errCategory', 'errFile'].forEach(id => document.getElementById(id).textContent = '');
  ['fTitle', 'fCategory'].forEach(id => document.getElementById(id)?.classList.remove('error'));
}

uploadForm.addEventListener('submit', async e => {
  e.preventDefault();
  const submitBtn = document.getElementById('uploadSubmit');
  submitBtn.disabled = true;
  submitBtn.textContent = 'Uploading…';

  const formData = new FormData(uploadForm);
  if (fileInput.files[0]) formData.set('file', fileInput.files[0]);

  try {
    const res = await fetch(uploadForm.dataset.action, {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
      body: formData,
    });

    if (res.ok) {
      closeModal();
      showToast('Document uploaded successfully.', 'success');
      setTimeout(() => location.reload(), 1200);
    } else if (res.status === 422) {
      const json = await res.json();
      if (json.errors?.title)    { document.getElementById('errTitle').textContent    = json.errors.title[0];    document.getElementById('fTitle').classList.add('error'); }
      if (json.errors?.category) { document.getElementById('errCategory').textContent = json.errors.category[0]; document.getElementById('fCategory').classList.add('error'); }
      if (json.errors?.file)     { document.getElementById('errFile').textContent     = json.errors.file[0]; }
    } else {
      showToast('Something went wrong.', 'error');
    }
  } catch {
    showToast('Network error.', 'error');
  } finally {
    submitBtn.disabled = false;
    submitBtn.textContent = 'Upload Document';
  }
});

// ── Print ──
document.querySelectorAll('.print-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    const iframe = document.createElement('iframe');
    iframe.style.cssText = 'position:fixed;top:-9999px;left:-9999px;width:1px;height:1px;';
    iframe.src = btn.dataset.url;
    document.body.appendChild(iframe);
    iframe.onload = () => {
      iframe.contentWindow.focus();
      iframe.contentWindow.print();
      setTimeout(() => iframe.remove(), 2000);
    };
  });
});

// ── Delete ──
document.querySelectorAll('.delete-doc-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    const url = btn.dataset.url;
    showConfirmToast('Delete this document? This cannot be undone.', async () => {
      try {
        const res = await fetch(url, {
          method: 'DELETE',
          headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
        });
        if (res.ok) {
          showToast('Document deleted.', 'success');
          setTimeout(() => location.reload(), 1200);
        } else {
          showToast('Could not delete document.', 'error');
        }
      } catch {
        showToast('Network error.', 'error');
      }
    });
  });
});

// ── Confirm toast ──
function showConfirmToast(message, onConfirm) {
  const container = document.getElementById('toast-container');
  const toast = document.createElement('div');
  toast.className = 'toast warning show';
  toast.style.cssText = 'max-width:380px;gap:12px;flex-direction:column;align-items:flex-start;';
  toast.innerHTML = `
    <div style="display:flex;align-items:center;gap:8px">
      <svg class="toast-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
      <span>${message}</span>
    </div>
    <div style="display:flex;gap:8px;align-self:flex-end">
      <button class="confirm-no"  style="padding:5px 14px;border:1px solid #fed7aa;border-radius:5px;background:#fff;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit;color:#c2410c">Cancel</button>
      <button class="confirm-yes" style="padding:5px 14px;border:none;border-radius:5px;background:#c2410c;color:#fff;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit">Confirm</button>
    </div>`;
  container.appendChild(toast);
  toast.querySelector('.confirm-yes').addEventListener('click', () => { toast.remove(); onConfirm(); });
  toast.querySelector('.confirm-no').addEventListener('click',  () => toast.remove());
}
