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

    // Support both new keys and fallback if API still sends older names
    const totalMembers  = Number(t.total_members ?? 0);
    const distinctAuth  = Number(t.distinct_authors ?? 0);
    const totalBooks    = Number(t.total_books ?? t.total_quantity ?? 0);
    const totalAmount   = Number(t.total_amount ?? 0);

    totals.innerHTML = `
      <div class="totals-item"><strong>Total Members:</strong> ${totalMembers}</div>
      <div class="totals-item"><strong>Total Distinct Authors:</strong> ${distinctAuth}</div>
      <div class="totals-item"><strong>Total Books:</strong> ${totalBooks}</div>
      <div class="totals-item"><strong>Total Amount:</strong> $${money(totalAmount)}</div>
    `;
  }

  function load(){
    fetch(endpoint('MVC/Controller/RequestsController.php?action=list_buy_history'))
      .then(r=>r.json())
      .then(j=>{
        if (!j || !j.ok){ console.error(j); return; }
        render(j.rows || [], j.totals || {total_members:0,distinct_authors:0,total_books:0,total_amount:0});
      })
      .catch(()=>{ /* silent */ });
  }

  load();
  setInterval(load, 8000); // smooth realtime-ish updates
})();
