// Public/JS/librarian-approvals.js
(function(){
  const grid  = document.getElementById('libPendingGrid');
  const empty = document.getElementById('libPendingEmpty');

  const ROOT = (
    document.querySelector('base')?.href ||
    (window.APP_BASE_URL || document.baseURI)
  ).replace(/index\.php.*$/, '').replace(/\/+$/, '') + '/';
  const endpoint = (p) => ROOT + p.replace(/^\/+/, '');
  const money = (n)=> Number(n).toFixed(2);
  const esc = (s)=> String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;')
                             .replace(/>/g,'&gt;').replace(/"/g,'&quot;')
                             .replace(/'/g,'&#39;');

  function cardEl(row){
    const total = (Number(row.unit_price) * Number(row.quantity)) || 0;
    const el = document.createElement('div');
    el.className = 'request-card';
    el.dataset.requestId = row.request_id;

    el.innerHTML = `
      <div class="card-left">
        <div class="title">${esc(row.title || '')}</div>
        <div class="meta">
          <div><span>ISBN:</span> ${esc(row.isbn || '')}</div>
          <div><span>Author:</span> ${esc(row.author || '')}</div>
          <div><span>Category:</span> ${esc(row.category || '')}</div>
          <div><span>Year:</span> ${esc(row.published_year ?? '')}</div>
          <div><span>Qty:</span> ${esc(row.quantity ?? 1)}</div>
          <div><span>Requested by:</span> ${esc(row.member_name || '')}</div>
          <div><span>Requested at:</span> ${new Date(row.requested_at).toLocaleString()}</div>
          <div class="status"><span>Status:</span> ${esc(row.status || 'pending')}</div>
        </div>
      </div>
      <div class="card-right">
        <div class="price">$${money(total)}</div>
        <div class="actions">
          <button class="btn btn-approve" type="button" data-act="approve">Approve</button>
          <button class="btn btn-reject"  type="button" data-act="reject">Reject</button>
        </div>
      </div>
    `;
    return el;
  }

  function render(rows){
    grid.innerHTML = '';
    if (!rows || rows.length === 0){
      empty.style.display = 'block';
      return;
    }
    empty.style.display = 'none';
    const frag = document.createDocumentFragment();
    rows.forEach(r => frag.appendChild(cardEl(r)));
    grid.appendChild(frag);
  }

  function load(){
    fetch(endpoint('MVC/Controller/RequestsController.php?action=list_pending_requests'))
      .then(r=>r.json())
      .then(j=>{
        if (!j || !j.ok){ alert((j && j.error) || 'Error loading pending requests'); return; }
        render(j.rows || []);
      })
      .catch(()=> alert('Network error'));
  }

  // Approve/Reject handlers (delegated)
  grid.addEventListener('click', (e) => {
    const btn = e.target.closest('.btn');
    if (!btn) return;

    const card = btn.closest('.request-card');
    const requestId = Number(card?.dataset?.requestId || 0);
    const act = btn.dataset.act;
    if (!requestId) return;

    if (act === 'approve') {
      const fd = new FormData();
      fd.append('action', 'approve_request');
      fd.append('request_id', String(requestId));

      fetch(endpoint('MVC/Controller/RequestsController.php'), { method: 'POST', body: fd })
        .then(r => r.json())
        .then(j => {
          if (!j || !j.ok){ alert((j && j.error) || 'Failed to approve'); return; }
          // Remove all cards for this request; buyer + tables will auto-refresh
          [...grid.querySelectorAll(`.request-card[data-request-id="${requestId}"]`)].forEach(n => n.remove());
          if (!grid.children.length) empty.style.display = 'block';
        })
        .catch(()=> alert('Network error'));
    }

    if (act === 'reject') {
      const fd = new FormData();
      fd.append('action', 'reject_request');
      fd.append('request_id', String(requestId));

      fetch(endpoint('MVC/Controller/RequestsController.php'), { method: 'POST', body: fd })
        .then(r => r.json())
        .then(j => {
          if (!j || !j.ok){ alert((j && j.error) || 'Failed to reject request'); return; }
          [...grid.querySelectorAll(`.request-card[data-request-id="${requestId}"]`)].forEach(n => n.remove());
          if (!grid.children.length) empty.style.display = 'block';
        })
        .catch(()=> alert('Network error'));
    }
  });

  load();
  // light polling keeps the list fresh if another librarian acts
  setInterval(load, 8000);
})();
