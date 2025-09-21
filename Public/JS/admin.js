// Demo-only: populate KPIs. Replace with fetch() to your endpoints later.
document.addEventListener('DOMContentLoaded', () => {
  // Example: you may call your PHP endpoint here to get real counts
  // fetch('MVC/Controller/AdminStatsController.php').then(r=>r.json()).then(...)

  const demo = {
    members:  1287,
    books:    5634,
    sales:    97250, // total currency units
  };

  const fmt = (n) => n.toLocaleString(undefined);

  const elMembers = document.getElementById('kpiTotalMembers');
  const elBooks   = document.getElementById('kpiTotalBooks');
  const elSales   = document.getElementById('kpiTotalSales');

  if (elMembers) elMembers.textContent = fmt(demo.members);
  if (elBooks)   elBooks.textContent   = fmt(demo.books);
  if (elSales)   elSales.textContent   = '$ ' + fmt(demo.sales); // BDT symbol since your TZ is Dhaka
});
