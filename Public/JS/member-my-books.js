// Public/JS/member-my-books.js
(function(){
  const grid  = document.getElementById('memberMyBooksGrid');
  const empty = document.getElementById('memberMyBooksEmpty');

  const ROOT = (
    document.querySelector('base')?.href ||
    (window.APP_BASE_URL || document.baseURI)
  ).replace(/index\.php.*$/, '').replace(/\/+$/, '') + '/';
  const endpoint = (p) => ROOT + p.replace(/^\/+/, '');
  const money = (n)=> Number(n).toFixed(2);
  const esc = (s)=> String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;')
                             .replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');

  function cardEl(row){
    const total = (Number(row.unit_price) * Number(row.quantity)) || 0;
    const el = document.createElement('div');
    el.className = 'book-card';
    el.innerHTML = `
      <div class="book-title">${esc(row.title || '')}</div>
      <div class="book-meta">
        <div><span>ISBN:</span> ${esc(row.isbn || '')}</div>
        <div><span>Author:</span> ${esc(row.author || '')}</div>
        <div><span>Category:</span> ${esc(row.category || '')}</div>
        <div><span>Year of Publication:</span> ${esc(row.published_year ?? '')}</div>
        <div><span>Quantity:</span> ${esc(row.quantity ?? 1)}</div>
        <div><span>Book Price:</span> $${money(row.unit_price || 0)}</div>
        <div><span>Line Total:</span> $${money(total)}</div>
        <div><span>Approved By:</span> ${esc(row.librarian_name || '—')}</div>
        <div><span>Approved At:</span> ${row.decided_at ? new Date(row.decided_at).toLocaleString() : '—'}</div>
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
    fetch(endpoint('MVC/Controller/RequestsController.php?action=list_member_approved_books'))
      .then(r=>r.json())
      .then(j=>{
        if (!j || !j.ok){ console.error(j); return; }
        render(j.rows || []);
      })
      .catch(()=>{ /* ignore transient */ });
  }

  load();
  setInterval(load, 8000); // realtime-ish refresh
})();
