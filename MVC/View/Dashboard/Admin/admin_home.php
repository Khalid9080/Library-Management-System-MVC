<?php
// Admin KPI cards + section titles (mirrors librarian layout)
?>
<section class="admin-kpis" aria-labelledby="adminKpiTitle">
  <header class="admin-kpis-header">
    <h2 id="adminKpiTitle">Admin Overview</h2>
    <p class="subtitle">Quick stats across members, inventory, and sales</p>
  </header>

  <div class="admin-kpi-tiles">
    <!-- MEMBERS + LIBRARIANS -->
    <!-- MEMBERS -->
    <article class="kpi-card kpi-members" data-kpi="members">
      <div class="kpi-icon"><i class="ph-users-three"></i></div>
      <div class="kpi-meta">
        <h3>Total Members</h3>
        <p class="kpi-value" id="kpiTotalMembers">—</p>
      </div>
    </article>

    <!-- LIBRARIANS -->
    <article class="kpi-card kpi-librarians" data-kpi="librarians">
      <div class="kpi-icon"><i class="ph-user-circle"></i></div>
      <div class="kpi-meta">
        <h3>Total Librarians</h3>
        <p class="kpi-value" id="kpiTotalLibrarians">—</p>
      </div>
    </article>

    <!-- BOOKS -->
    <article class="kpi-card kpi-books" data-kpi="books">
      <div class="kpi-icon"><i class="ph-books"></i></div>
      <div class="kpi-meta">
        <h3>Total Books</h3>
        <p class="kpi-value" id="kpiTotalBooks">—</p>
      </div>
    </article>

    <!-- SALES -->
    <article class="kpi-card kpi-sales" data-kpi="sales">
      <div class="kpi-icon"><i class="ph-currency-dollar"></i></div>
      <div class="kpi-meta">
        <h3>Total Sales</h3>
        <p class="kpi-value" id="kpiTotalSales">—</p>
      </div>
    </article>
  </div>
</section>