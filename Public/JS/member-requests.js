// Public/JS/member-requests.js
(function(){
  const grid  = document.getElementById('memberRequestsGrid');
  const empty = document.getElementById('memberReqEmpty');

  function money(n){ return Number(n).toFixed(2); }
  function esc(s){ return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&#39;'); }

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
          <div><span>Requested at:</span> ${new Date(row.requested_at).toLocaleString()}</div>
          <div class="status"><span>Status:</span> ${esc(row.status || 'pending')}</div>
        </div>
      </div>
      <div class="card-right">
        <div class="price">$${money(total)}</div>
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
    fetch('MVC/Controller/RequestsController.php?action=list_member_requests')
      .then(r=>r.json())
      .then(j=>{
        if (!j || !j.ok){ console.error(j); return; }
        render(j.rows || []);
      })
      .catch(()=> {});
  }

  load();
  // light auto-refresh so rejections disappear without manual reload
  setInterval(load, 10000);
})();
