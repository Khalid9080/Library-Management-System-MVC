// Public/JS/admin-manage-users-actions.js
(function () {
  const form = document.getElementById('manageUsersForm');
  if (!form) return;

  const fields = {
    id: document.getElementById('muUserId'),
    original_email: document.getElementById('muOriginalEmail'),
    email: document.getElementById('muEmail'),
    username: document.getElementById('muUsername'),
    phone: document.getElementById('muPhone'),
    role: document.getElementById('muRole'),
  };

  const errs = {
    email: document.getElementById('muEmailError'),
    username: document.getElementById('muUsernameError'),
    phone: document.getElementById('muPhoneError'),
    role: document.getElementById('muRoleError'),
  };

  const statusBar = document.getElementById('muStatus');

  const ROOT = (
    document.querySelector('base')?.href ||
    (window.APP_BASE_URL || document.baseURI)
  ).replace(/index\.php.*$/, '').replace(/\/+$/, '') + '/';
  const endpoint = (p) => ROOT + p.replace(/^\/+/, '');

  async function safeJSON(res){
    let j = null;
    try { j = await res.json(); } catch (_) {}
    if (!res.ok || !j || !j.ok) {
      const msg = (j && j.error) ? j.error : `HTTP ${res.status}`;
      throw new Error(msg);
    }
    return j;
  }

  function showError(key, msg){
    const wrap = fields[key]?.closest('.amu-field');
    if (wrap) wrap.classList.add('error');
    if (errs[key]) { errs[key].textContent = msg; errs[key].classList.add('show'); }
  }
  function clearError(key){
    const wrap = fields[key]?.closest('.amu-field');
    if (wrap) wrap.classList.remove('error');
    if (errs[key]) { errs[key].textContent = ''; errs[key].classList.remove('show'); }
  }
  function setStatus(text, type){ // type: 'ok' | 'err'
    if (!statusBar) return;
    statusBar.hidden = !text;
    statusBar.textContent = text || '';
    statusBar.classList.remove('is-ok','is-err');
    if (text) statusBar.classList.add(type === 'ok' ? 'is-ok' : 'is-err');
  }
  const isFilled = (v) => v != null && String(v).trim() !== '';

  // mirror of server email-tag check
  function emailTagRole(email){
    if (!email || !/^[^\s@]+@[^\s@]+$/.test(email)) return null;
    const local = email.split('@')[0] || '';
    const parts = local.split('.');
    const last = (parts[parts.length - 1] || '').toLowerCase();
    return ['member','librarian','admin'].includes(last) ? last : null;
  }

  // ==== Autofill on valid email ====
  function attemptAutofill(){
    const email = fields.email?.value?.trim() || '';
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) return;

    fetch(endpoint('MVC/Controller/AdminStatsController.php?action=get_user&email=' + encodeURIComponent(email)))
      .then(safeJSON)
      .then(j => {
        const u = j.user || {};
        if (fields.id) fields.id.value = String(u.id || '');
        if (fields.original_email) fields.original_email.value = u.email || '';

        if (fields.username) fields.username.value = u.username || '';
        if (fields.phone) fields.phone.value = u.phone || '';
        if (fields.role) fields.role.value = (u.role || '').toLowerCase();

        // ensure email stays editable
        fields.email && fields.email.removeAttribute('readonly');

        // clear previous errors + status
        ['email','username','phone','role'].forEach(clearError);
        setStatus('', 'ok');
      })
      .catch(err => {
        // Not found or other error – clear fields except email and show a small hint
        if (fields.id) fields.id.value = '';
        if (fields.original_email) fields.original_email.value = '';
        if (fields.username) fields.username.value = '';
        if (fields.phone) fields.phone.value = '';
        if (fields.role) fields.role.value = '';
        showError('email', err.message || 'User not found');
        setStatus(err.message || 'User not found', 'err');
      });
  }

  // Trigger autofill on blur and Enter key
  fields.email?.addEventListener('blur', attemptAutofill);
  fields.email?.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') { e.preventDefault(); attemptAutofill(); }
  });

  // Live hint if email tag mismatches selected role
  function checkEmailRoleConsistency(){
    const tag = emailTagRole(fields.email?.value?.trim());
    const chosen = (fields.role?.value || '').toLowerCase();
    if (tag && chosen && tag !== chosen) {
      showError('role', 'Email tag & role must match (e.g. khalid.member@… ⇒ Role = Member)');
    } else {
      clearError('role');
    }
  }
  fields.email?.addEventListener('input', checkEmailRoleConsistency);
  fields.role?.addEventListener('change', checkEmailRoleConsistency);

  // ==== Submit (AJAX; no navigation) ====
  form.addEventListener('submit', (e) => {
    // IMPORTANT: stop default navigation so we stay on Admin Dashboard
    e.preventDefault();

    // If validator left any visible error, do nothing
    const hasError = form.querySelector('.error-message.show');
    if (hasError) return;

    // basic presence guard (defensive)
    const emailVal = (fields.email?.value || '').trim();
    const usernameVal = (fields.username?.value || '').trim();
    const phoneVal = (fields.phone?.value || '').trim();
    const roleVal = (fields.role?.value || '').trim().toLowerCase();
    if (!isFilled(emailVal) || !isFilled(usernameVal) || !isFilled(phoneVal) || !isFilled(roleVal)) {
      setStatus('All fields are required', 'err');
      return;
    }

    // client-side tag ↔ role hint (server still enforces)
    const tag = emailTagRole(emailVal);
    if (tag && roleVal && tag !== roleVal) {
      showError('role', 'Email tag & role must match (e.g. khalid.member@… ⇒ Role = Member)');
      setStatus('Email tag and selected role must match', 'err');
      return;
    } else {
      clearError('role');
    }

    const fd = new FormData();
    fd.append('action', 'update_user');

    const idVal = fields.id?.value?.trim() || '';
    const origEmailVal = fields.original_email?.value?.trim() || '';

    if (idVal) fd.append('id', idVal);
    else if (origEmailVal) fd.append('original_email', origEmailVal);
    else fd.append('original_email', emailVal); // fallback when coming fresh

    fd.append('email', emailVal);       // NEW email (can be changed)
    fd.append('username', usernameVal);
    fd.append('phone', phoneVal);
    fd.append('role', roleVal);

    setStatus('Updating user…', 'ok');

    fetch(endpoint('MVC/Controller/AdminStatsController.php'), { method: 'POST', body: fd })
      .then(safeJSON)
      .then(() => {
        setStatus('User updated successfully', 'ok');

        // keep hidden original markers in sync with the new email
        if (fields.original_email) fields.original_email.value = emailVal;

        // Notify other widgets (Users Directory + KPIs) to refresh if present
        try { window.ADMIN_USERS && window.ADMIN_USERS.refresh && window.ADMIN_USERS.refresh(); } catch(_) {}
        try { window.ADMIN_KPIS_REFRESH && window.ADMIN_KPIS_REFRESH(); } catch(_) {}
      })
      .catch(err => {
        const msg = err.message || 'Update failed';
        if (/Email tag/.test(msg)) {
          showError('role', msg);
        } else if (/Email already in use/i.test(msg)) {
          showError('email', 'Email already in use');
        } else if (/Valid email required/i.test(msg)) {
          showError('email', 'Enter a valid email');
        } else if (/Username required/i.test(msg)) {
          showError('username', 'This field is required');
        }
        setStatus(msg, 'err');
      });
  });
})();
