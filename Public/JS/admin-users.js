// Public/JS/admin-users.js
// Drives the Admin → Users Directory table. No conflict with admin-manage-users.js.

(function(){
  const table = document.getElementById('adminUsersTable');
  const empty = document.getElementById('adminUsersEmpty');
  if (!table) return;

  // Build a robust base root (same pattern as your other scripts)
  const ROOT = (
    document.querySelector('base')?.href ||
    (window.APP_BASE_URL || document.baseURI)
  ).replace(/index\.php.*$/, '').replace(/\/+$/, '') + '/';

  const endpoint = (p) => ROOT + p.replace(/^\/+/, '');
  const apiList  = 'MVC/Controller/AdminStatsController.php?action=list_users';
  const apiDel   = 'MVC/Controller/AdminStatsController.php';

  const esc = (s) => String(s)
    .replace(/&/g,'&amp;')
    .replace(/</g,'&lt;')
    .replace(/>/g,'&gt;')
    .replace(/"/g,'&quot;')
    .replace(/'/g,'&#39;');

  function badge(role) {
    const r = String(role || '').toLowerCase();
    const cls = (r === 'admin') ? 'badge-admin' : (r === 'librarian' ? 'badge-librarian' : 'badge-member');
    const txt = r ? r.charAt(0).toUpperCase() + r.slice(1) : '';
    return `<span class="badge badge-role ${cls}">${esc(txt)}</span>`;
  }

  function rowEl(user){
    const li = document.createElement('li');
    li.className = 'table-row';
    li.setAttribute('role','row');
    li.dataset.userId = user.id;

    li.innerHTML = `
      <div class="col col-1" role="cell" data-label="Username">${esc(user.username ?? '')}</div>
      <div class="col col-2" role="cell" data-label="Email">${esc(user.email ?? '')}</div>
      <div class="col col-3" role="cell" data-label="Phone Number">${esc(user.phone ?? '')}</div>
      <div class="col col-4" role="cell" data-label="Role">${badge(user.role)}</div>
      <div class="col col-5" role="cell" data-label="Action">
        <button type="button"
                class="row-delete-btn"
                aria-label="Delete this user"
                data-id="${String(user.id)}"
                data-email="${esc(user.email ?? '')}">
          Delete
        </button>
      </div>
    `;
    return li;
  }

  function render(rows){
    // Remove old rows (keep header)
    Array.from(table.querySelectorAll('.table-row')).forEach(n => n.remove());

    if (!rows || rows.length === 0){
      if (empty) empty.style.display = 'block';
      return;
    }
    if (empty) empty.style.display = 'none';

    const frag = document.createDocumentFragment();
    rows.forEach(u => frag.appendChild(rowEl(u)));
    table.appendChild(frag);
  }

  async function safeJSON(res){
    let j = null;
    try { j = await res.json(); } catch (_) {}
    if (!res.ok || !j || !j.ok) {
      const msg = (j && j.error) ? j.error : `HTTP ${res.status}`;
      throw new Error(msg);
    }
    return j;
  }

  function load(){
    return fetch(endpoint(apiList))
      .then(safeJSON)
      .then(j => render(j.rows || []))
      .catch(err => {
        console.error('[admin-users] load failed:', err);
        // show empty if something goes wrong
        if (empty) empty.style.display = 'block';
      });
  }

  // Delete handler (event delegation)
  table.addEventListener('click', (e) => {
    const btn = e.target.closest('.row-delete-btn');
    if (!btn) return;

    const id = Number(btn.dataset.id || 0);
    const email = btn.dataset.email || '';

    if (!id && !email) return;

    if (!confirm('Delete this user permanently?')) return;

    const row = btn.closest('.table-row');
    if (row) row.classList.add('removing');

    const fd = new FormData();
    fd.append('action', 'delete_user');
    if (id) fd.append('id', String(id)); else fd.append('email', email);

    fetch(endpoint(apiDel), { method: 'POST', body: fd })
      .then(safeJSON)
      .then(() => {
        // remove row immediately; then refresh list to ensure sync
        if (row) row.remove();
        load();
      })
      .catch(err => {
        alert(err.message || 'Failed to delete user');
        if (row) row.classList.remove('removing');
      });
  });

  // Initial load + periodic polling for “real-time” feel
  load();
  setInterval(load, 8000);

  // Optional global hook to refresh from elsewhere if you ever need it
  window.ADMIN_USERS = { refresh: load };
})();
