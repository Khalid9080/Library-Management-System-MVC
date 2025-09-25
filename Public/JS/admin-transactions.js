// Public/JS/admin-transactions.js
(function(){
  const ROOT = (
    document.querySelector('base')?.href ||
    (window.APP_BASE_URL || document.baseURI)
  ).replace(/index\.php.*$/, '').replace(/\/+$/, '') + '/';
  const endpoint = (p) => ROOT + p.replace(/^\/+/, '');

  const tbody  = document.getElementById('adminTxnBody');
  const totals = document.getElementById('adminTxnTotals');
  const money  = (n)=> Number(n).toFixed(2);

  function esc(s){ return String(s)
    .replace(/&/g,'&amp;').replace(/</g,'&lt;')
    .replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }

  function render(rows, t){
    if (tbody) {
      tbody.innerHTML = '';
      const frag = document.createDocumentFragment();
      rows.forEach(r => {
        const tr = document.createElement('tr');

        // status badge
        let statusHtml = '';
        if (r.status === 'approved') {
          statusHtml = `<span class="badge-status badge-approved">Approved</span>`;
        } else if (r.status === 'rejected') {
          statusHtml = `<span class="badge-status badge-rejected">Rejected</span>`;
        } else {
          statusHtml = esc(r.status || '');
        }

        tr.innerHTML = `
          <td>${esc(r.isbn || '')}</td>
          <td>${esc(r.title || '')}</td>
          <td>${esc(r.author || '')}</td>
          <td>${esc(r.category || '')}</td>
          <td>${esc(r.requested_by || '')}</td>
          <td>${r.requested_at ? new Date(r.requested_at).toLocaleString() : ''}</td>
          <td>${statusHtml}</td>
          <td>${esc(r.quantity ?? 0)}</td>
          <td>${esc(r.librarian_name || '')}</td>
          <td>$${money(r.line_total || 0)}</td>
        `;
        frag.appendChild(tr);
      });
      tbody.appendChild(frag);
    }

    const totalMembers  = Number(t.total_members ?? 0);
    const distinctAuth  = Number(t.distinct_authors ?? 0);
    const totalBooks    = Number(t.total_books ?? t.total_quantity ?? 0);
    const totalAmount   = Number(t.total_amount ?? 0);

    if (totals) {
      totals.innerHTML = `
        <div class="totals-item"><strong>Book Buyers:</strong> ${totalMembers}</div>
        <div class="totals-item"><strong>Total Distinct Authors:</strong> ${distinctAuth}</div>
        <div class="totals-item"><strong>Books Summary:</strong> ${totalBooks}</div>
        <div class="totals-item"><strong>Total Amount:</strong> $${money(totalAmount)}</div>
      `;
    }
  }

  function load(){
    fetch(endpoint('MVC/Controller/RequestsController.php?action=list_buy_history'))
      .then(r => r.json())
      .then(j => {
        if (!j || !j.ok) return;
        render(j.rows || [], j.totals || { total_members:0, distinct_authors:0, total_books:0, total_amount:0 });
      })
      .catch(()=>{ /* ignore transient errors */ });
  }

  load();
  setInterval(load, 8000);

  window.ADMIN_TXN = { refresh: load };
})();
