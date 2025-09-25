// Public/JS/admin.js
document.addEventListener('DOMContentLoaded', () => {
  // Build a stable base URL (same pattern as other scripts)
  const ROOT = (
    document.querySelector('base')?.href ||
    (window.APP_BASE_URL || document.baseURI)
  ).replace(/index\.php.*$/, '').replace(/\/+$/, '') + '/';

  const endpoint = (p) => ROOT + p.replace(/^\/+/, '');
  const fmtInt   = (n) => Number(n || 0).toLocaleString(undefined);
  const fmtMoney = (n) => Number(n || 0).toLocaleString(
    undefined,
    { minimumFractionDigits: 2, maximumFractionDigits: 2 }
  );

  const elMembers     = document.getElementById('kpiTotalMembers');
  const elLibrarians  = document.getElementById('kpiTotalLibrarians');
  const elBooks       = document.getElementById('kpiTotalBooks');
  const elSales       = document.getElementById('kpiTotalSales');

  // 1) Total Members + Librarians (new controller)
  function loadUsers(){
    return fetch(endpoint('MVC/Controller/AdminStatsController.php?action=count_users'))
      .then(r => r.json())
      .then(j => {
        if (!j || !j.ok) throw new Error(j?.error || 'count_users failed');
        if (elMembers)    elMembers.textContent    = fmtInt(j.members);
        if (elLibrarians) elLibrarians.textContent = fmtInt(j.librarians);
      })
      .catch(() => {
        if (elMembers)    elMembers.textContent    = '—';
        if (elLibrarians) elLibrarians.textContent = '—';
      });
  }

  // 2) Total Books (use BooksController)
  function loadBooks(){
    return fetch(endpoint('MVC/Controller/BooksController.php?action=count_books'))
      .then(r => r.json())
      .then(j => {
        if (!j || !j.ok) throw new Error(j?.error || 'count_books failed');
        const total = typeof j.count_all === 'number' ? j.count_all : (j.count ?? 0);
        if (elBooks) elBooks.textContent = fmtInt(total);
      })
      .catch(() => { if (elBooks) elBooks.textContent = '—'; });
  }

  // 3) Total Sales (pull from Buy History totals)
  function loadSales(){
    return fetch(endpoint('MVC/Controller/RequestsController.php?action=list_buy_history'))
      .then(r => r.json())
      .then(j => {
        if (!j || !j.ok) throw new Error(j?.error || 'list_buy_history failed');
        const totalAmount = j?.totals?.total_amount ?? 0;
        if (elSales) elSales.textContent = '$ ' + fmtMoney(totalAmount);
      })
      .catch(() => { if (elSales) elSales.textContent = '—'; });
  }

  // Expose a global hook so other pages (e.g., librarian approvals)
  // can trigger an immediate KPI refresh after approve/reject/add.
  window.ADMIN_KPIS_REFRESH = () => {
    loadUsers();
    loadBooks();
    loadSales();
  };

  // Initial load + lightweight refresh
  Promise.all([loadUsers(), loadBooks(), loadSales()]).catch(() => {});
  setInterval(() => { loadUsers(); loadBooks(); loadSales(); }, 10000);
});
