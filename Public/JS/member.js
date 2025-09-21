// Public/JS/member.js
(function () {
  const wrap   = document.getElementById('memberSearch');
  const openBt = document.getElementById('searchToggleBtn');
  const closeBt= document.getElementById('searchCloseBtn');
  const input  = document.getElementById('bookSearchInput');

  function openSearch(e){
    if (!wrap.classList.contains('active')){
      wrap.classList.add('active');
      requestAnimationFrame(()=>setTimeout(()=>input && input.focus(),220));
      e && e.preventDefault();
    }
  }
  function closeSearch(){
    if (wrap.classList.contains('active')){
      wrap.classList.remove('active');
      if (input) input.value = '';
    }
  }

  // open by default (defensive)
  if (wrap && !wrap.classList.contains('active')) wrap.classList.add('active');

  openBt && openBt.addEventListener('click', openSearch);
  // IMPORTANT: remove "click anywhere to close" behavior
  closeBt && closeBt.addEventListener('click', closeSearch);

  // Table: select-all logic
  const selectAll = document.getElementById('selectAll');
  const table = document.getElementById('memberBooksTable');
  const rowChecks = () => Array.from(table.querySelectorAll('tbody .row-check'));

  if (selectAll && table){
    selectAll.addEventListener('change', () => rowChecks().forEach(cb => cb.checked = selectAll.checked));
    table.addEventListener('change', (e) => {
      if (!e.target.classList.contains('row-check')) return;
      const rows = rowChecks();
      const all = rows.length>0 && rows.every(cb=>cb.checked);
      selectAll.indeterminate = !all && rows.some(cb=>cb.checked);
      if (!selectAll.indeterminate) selectAll.checked = all;
    });
  }

  // Client-side filter
  if (input && table){
    input.addEventListener('input', () => {
      const q = input.value.trim().toLowerCase();
      table.querySelectorAll('tbody tr').forEach(tr => {
        tr.style.display = tr.innerText.toLowerCase().includes(q) ? '' : 'none';
      });
    });
  }

  // Dummy button
  const buyBtn = document.getElementById('buyRequestBtn');
  buyBtn && buyBtn.addEventListener('click', () => {
    const selected = rowChecks().filter(cb => cb.checked).length;
    alert(selected>0
      ? `You selected ${selected} book(s). In the next step, we’ll place a buy request.`
      : 'Please select at least one book to request.');
  });
})();
