// Public/JS/admin.js
document.addEventListener('DOMContentLoaded', () => {
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

  // NEW cards
  const elBuyers      = document.getElementById('kpiBookBuyers');
  const elAdmins      = document.getElementById('kpiTotalAdmins');

  // 1) Total Members + Librarians + Admins
  function loadUsers(){
    return fetch(endpoint('MVC/Controller/AdminStatsController.php?action=count_users'))
      .then(r => r.json())
      .then(j => {
        if (!j || !j.ok) throw new Error(j?.error || 'count_users failed');
        if (elMembers)    elMembers.textContent    = fmtInt(j.members);
        if (elLibrarians) elLibrarians.textContent = fmtInt(j.librarians);
        if (elAdmins)     elAdmins.textContent     = fmtInt(j.admins);
      })
      .catch(() => {
        if (elMembers)    elMembers.textContent    = '—';
        if (elLibrarians) elLibrarians.textContent = '—';
        if (elAdmins)     elAdmins.textContent     = '—';
      });
  }

  // 2) Total Books
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

  // 3) Total Sales + Book Buyers (from buy history totals)
  function loadSalesAndBuyers(){
    return fetch(endpoint('MVC/Controller/RequestsController.php?action=list_buy_history'))
      .then(r => r.json())
      .then(j => {
        if (!j || !j.ok) throw new Error(j?.error || 'list_buy_history failed');
        const totalAmount = j?.totals?.total_amount ?? 0;
        const totalBuyers = j?.totals?.total_members ?? 0;
        if (elSales)  elSales.textContent  = '$ ' + fmtMoney(totalAmount);
        if (elBuyers) elBuyers.textContent = fmtInt(totalBuyers);
      })
      .catch(() => {
        if (elSales)  elSales.textContent  = '—';
        if (elBuyers) elBuyers.textContent = '—';
      });
  }

  // Global refresh hook
  window.ADMIN_KPIS_REFRESH = () => {
    loadUsers();
    loadBooks();
    loadSalesAndBuyers();
  };

  // Initial + periodic refresh
  Promise.all([loadUsers(), loadBooks(), loadSalesAndBuyers()]).catch(() => {});
  setInterval(() => { loadUsers(); loadBooks(); loadSalesAndBuyers(); }, 10000);
});
