// Public/JS/member.js
(function () {
  const wrap   = document.getElementById('memberSearch');
  const openBt = document.getElementById('searchToggleBtn');
  const closeBt= document.getElementById('searchCloseBtn');
  const input  = document.getElementById('bookSearchInput');

  // A safe root for all endpoints (works from any panel/page)
  const ROOT = (
    document.querySelector('base')?.href ||
    (window.APP_BASE_URL || document.baseURI)
  ).replace(/index\.php.*$/, '').replace(/\/+$/, '') + '/';

  // ---- small helpers --------------------------------------------------------
  const endpoint = (p) => ROOT + p.replace(/^\/+/, '');
  const safeJSON = async (res) => {
    let j = null;
    try { j = await res.json(); } catch (_) {}
    if (!res.ok || !j || !j.ok) {
      const msg = (j && j.error) ? j.error : `HTTP ${res.status}`;
      throw new Error(msg);
    }
    return j;
  };

  // open-by-default search
  if (wrap && !wrap.classList.contains('active')) wrap.classList.add('active');
  function openSearch(e){ if (!wrap.classList.contains('active')){ wrap.classList.add('active'); requestAnimationFrame(()=>setTimeout(()=>input && input.focus(),220)); e && e.preventDefault(); } }
  function closeSearch(){ if (wrap && wrap.classList.contains('active')){ wrap.classList.remove('active'); if (input) input.value=''; filter(); } }
  openBt && openBt.addEventListener('click', openSearch);
  closeBt && closeBt.addEventListener('click', closeSearch);

  const table     = document.getElementById('memberBooksTable');
  const tbody     = document.getElementById('memberBooksTbody');
  const empty     = document.getElementById('memberEmpty');
  const selectAll = document.getElementById('selectAll');

  let rowsCache = [];
  // book_ids that are LOCKED for this member (either pending OR approved)
  let lockedSet = new Set();

  const money = (n) => Number(n).toFixed(2);
  const makeCell = (text, cls) => {
    const td = document.createElement('td');
    if (cls) td.className = cls;
    td.textContent = text;
    return td;
  };

  function applyDisableState(tr, cb){
    const bookId = Number(tr.getAttribute('data-id'));
    if (lockedSet.has(bookId)) {
      cb.checked = false;
      cb.disabled = true;
      cb.title = 'This book is already requested/approved';
      tr.classList.add('is-pending'); // reuse styling
    } else {
      cb.disabled = false;
      cb.title = '';
      tr.classList.remove('is-pending');
    }
  }

  function render(data){
    while (tbody.firstChild) tbody.removeChild(tbody.firstChild);

    if (!data || data.length === 0){
      if (empty) empty.style.display = 'block';
      return;
    }
    if (empty) empty.style.display = 'none';

    const frag = document.createDocumentFragment();
    data.forEach(r => {
      const tr = document.createElement('tr');
      tr.dataset.id = r.id;
      tr.dataset.isbn = r.isbn;

      const tdCheck = document.createElement('td');
      tdCheck.className = 'col-check';
      const cb = document.createElement('input');
      cb.type = 'checkbox';
      cb.className = 'row-check';
      cb.setAttribute('aria-label','Select row');
      tdCheck.appendChild(cb);
      tr.appendChild(tdCheck);

      tr.appendChild(makeCell(r.isbn ?? ''));
      tr.appendChild(makeCell(r.title ?? ''));
      tr.appendChild(makeCell(r.author ?? ''));
      tr.appendChild(makeCell(r.category ?? ''));
      tr.appendChild(makeCell(r.published_year ?? ''));
      tr.appendChild(makeCell(`$${money(r.price ?? 0)}`));

      // lock or unlock this row depending on current locked set
      applyDisableState(tr, cb);

      frag.appendChild(tr);
    });
    tbody.appendChild(frag);

    if (selectAll) { selectAll.checked = false; selectAll.indeterminate = false; }
    filter();
  }

  // --- fetchers --------------------------------------------------------------
  // NEW: get both pending AND approved to lock forever once approved
  function fetchLocked(){
    return fetch(endpoint('MVC/Controller/RequestsController.php?action=member_locked_book_ids'), { method: 'GET' })
      .then(safeJSON)
      .then(j => { lockedSet = new Set((j.book_ids || []).map(Number)); });
  }

  function fetchBooks(){
    return fetch(endpoint('MVC/Controller/BooksController.php?action=list_books'), { method: 'GET' })
      .then(safeJSON)
      .then(j => { rowsCache = j.rows || []; render(rowsCache); });
  }

  // Load LOCKED first, then BOOKS, so rendering knows what to disable.
  function refreshAll(){
    return fetchLocked().then(fetchBooks).catch(err => {
      console.error(err);
      alert(err.message || 'Network error');
    });
  }

  // Light polling to keep lockout "real-time" (e.g., after librarian acts)
  function reapplyLockedOnly(){
    fetchLocked()
      .then(() => {
        // Re-apply without refetching books
        Array.from(tbody.querySelectorAll('tr')).forEach(tr => {
          const cb = tr.querySelector('.row-check');
          if (cb) applyDisableState(tr, cb);
        });
      })
      .catch(()=>{ /* ignore transient errors */ });
  }
  // every 8 seconds feels snappy without being noisy
  setInterval(reapplyLockedOnly, 8000);

  // Expose a refresh for other scripts if needed
  window.MEMBER_CATALOG = { refresh: refreshAll };

  // select-all that ignores disabled rows
  const rowChecks = () => Array.from(table.querySelectorAll('tbody .row-check'));
  if (selectAll && table){
    selectAll.addEventListener('change', () =>
      rowChecks().forEach(cb => { if(!cb.disabled) cb.checked = selectAll.checked; })
    );
    table.addEventListener('change', (e) => {
      if (!e.target.classList.contains('row-check')) return;
      const active = rowChecks().filter(cb => !cb.disabled);
      const all = active.length>0 && active.every(cb=>cb.checked);
      selectAll.indeterminate = !all && active.some(cb=>cb.checked);
      if (!selectAll.indeterminate) selectAll.checked = all;
    });
  }

  function filter(){
    const q = (input?.value || '').trim().toLowerCase();
    if (!tbody) return;
    Array.from(tbody.children).forEach(tr => {
      tr.style.display = q ? (tr.innerText.toLowerCase().includes(q) ? '' : 'none') : '';
    });
  }
  input && input.addEventListener('input', filter);

  // ---- Submit "Order a Buy Request" ----------------------------------------
  const buyBtn = document.getElementById('buyRequestBtn');
  buyBtn && buyBtn.addEventListener('click', () => {
    const selectedRows = Array.from(tbody.querySelectorAll('tr')).filter(tr => {
      const cb = tr.querySelector('.row-check');
      return cb && cb.checked && !cb.disabled;
    });

    if (selectedRows.length === 0){
      alert('Please select at least one book to request.');
      return;
    }

    const items = selectedRows.map(tr => ({
      book_id: Number(tr.getAttribute('data-id')),
      quantity: 1
    }));

    const fd = new FormData();
    fd.append('action', 'create_request');
    fd.append('items', JSON.stringify(items));

    // Robust fetch: parse JSON safely; show real server message on error; only redirect if success
    fetch(endpoint('MVC/Controller/RequestsController.php'), { method: 'POST', body: fd })
      .then(safeJSON)
      .then(() => {
        // after the server *really* saved, lock rows and redirect
        return refreshAll().then(() => {
          window.location = endpoint('index.php?page=dashboard&panel=my_book_requests');
        });
      })
      .catch(err => {
        alert(err.message || 'Network error');
      });
  });

  // initial load in the correct order
  refreshAll();

  // subtle visual cue for locked/pending rows (optional styling hook)
  const style = document.createElement('style');
  style.textContent = `
    .member-table tr.is-pending { opacity: .55; }
    .member-table tr.is-pending .row-check { cursor: not-allowed; }
  `;
  document.head.appendChild(style);
})();
