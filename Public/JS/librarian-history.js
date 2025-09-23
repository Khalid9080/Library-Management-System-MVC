// Public/JS/librarian-history.js
(function(){
  const ROOT = (
    document.querySelector('base')?.href ||
    (window.APP_BASE_URL || document.baseURI)
  ).replace(/index\.php.*$/, '').replace(/\/+$/, '') + '/';
  const endpoint = (p) => ROOT + p.replace(/^\/+/, '');
  const tbody  = document.getElementById('buyHistoryBody');
  const totals = document.getElementById('buyHistoryTotals');
  const money  = (n)=> Number(n).toFixed(2);

  function esc(s){ return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;')
                             .replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }

  function render(rows, t){
    tbody.innerHTML = '';
    const frag = document.createDocumentFragment();
    rows.forEach(r => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${esc(r.isbn || '')}</td>
        <td>${esc(r.title || '')}</td>
        <td>${esc(r.author || '')}</td>
        <td>${esc(r.category || '')}</td>
        <td>${esc(r.requested_by || '')}</td>
        <td>${new Date(r.requested_at).toLocaleString()}</td>
        <td>${esc(r.status || '')}</td>
        <td>${esc(r.quantity ?? 0)}</td>
        <td>${esc(r.librarian_name || '')}</td>
        <td>$${money(r.line_total || 0)}</td>
      `;
      frag.appendChild(tr);
    });
    tbody.appendChild(frag);

    totals.innerHTML = `
      <div><strong>Total distinct authors:</strong> ${t.distinct_authors}</div>
      <div><strong>Total quantity:</strong> ${t.total_quantity}</div>
      <div><strong>Total amount:</strong> $${money(t.total_amount)}</div>
    `;
  }

  function load(){
    fetch(endpoint('MVC/Controller/RequestsController.php?action=list_buy_history'))
      .then(r=>r.json())
      .then(j=>{
        if (!j || !j.ok){ console.error(j); return; }
        render(j.rows || [], j.totals || {distinct_authors:0,total_quantity:0,total_amount:0});
      })
      .catch(()=>{ /* silent */ });
  }

  load();
  setInterval(load, 8000); // smooth realtime-ish updates
})();
