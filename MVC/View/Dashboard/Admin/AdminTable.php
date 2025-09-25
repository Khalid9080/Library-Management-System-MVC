<?php
// MVC/View/Dashboard/Admin/AdminTable.php
// Renders the Users Directory table shell; rows are injected by JS.

if (!function_exists('render_admin_user_table')) {
  function render_admin_user_table(): void {
    // Use existing responsive table CSS
    $cssHref = function_exists('asset')
      ? asset('Public/Style/librarian-table.css') . '?v=' . time()
      : '/Public/Style/librarian-table.css';

    echo '<link rel="stylesheet" href="' . htmlspecialchars($cssHref, ENT_QUOTES, 'UTF-8') . '" />';

    ?>
    <section class="admin-users-table" aria-labelledby="adminUsersTitle">
      <div class="librarian-table-container">
        <h2 id="adminUsersTitle" class="librarian-table-title">
          Users Directory
          <small class="subtitle">Username, email &amp; role overview</small>
        </h2>

        <ul class="responsive-table" role="table" aria-label="All Users" id="adminUsersTable">
          <li class="table-header" role="row">
            <div class="col col-1" role="columnheader">Username</div>
            <div class="col col-2" role="columnheader">Email</div>
            <div class="col col-3" role="columnheader">Phone Number</div>
            <div class="col col-4" role="columnheader">Role</div>
            <div class="col col-5" role="columnheader">Action</div>
          </li>
          <!-- rows injected by JS -->
        </ul>

        <div id="adminUsersEmpty" style="display:none; color:#666; padding:12px 8px;">
          No users found.
        </div>
      </div>
    </section>

    <style>
      /* Column distribution for 5-col table (with Action) */
      .admin-users-table .responsive-table .col-1 { flex-basis: 22%; }
      .admin-users-table .responsive-table .col-2 { flex-basis: 30%; }
      .admin-users-table .responsive-table .col-3 { flex-basis: 22%; }
      .admin-users-table .responsive-table .col-4 { flex-basis: 14%; justify-content: flex-start; }
      .admin-users-table .responsive-table .col-5 { flex-basis: 12%; justify-content: flex-end; }

      .badge-role {
        display:inline-block; padding:6px 10px; border-radius:999px; font-weight:700; font-size:12px;
        border:1px solid rgba(0,0,0,.08);
      }
      .badge-role.badge-admin     { background:#ffe8e8; color:#7a1f1f; border-color:#ffc9c9; }
      .badge-role.badge-librarian { background:#e8f5ff; color:#0b3a6b; border-color:#c6e1ff; }
      .badge-role.badge-member    { background:#e9faef; color:#14532d; border-color:#c9f4d8; }

      .row-delete-btn {
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid rgba(31,31,31,.15);
        background: linear-gradient(180deg, #ffe8e8, #ffdcdc);
        color: #7a1f1f;
        font-weight: 700;
        cursor: pointer;
      }
      .row-delete-btn:hover { background: linear-gradient(180deg, #ffdede, #ffcccc); }
      .table-row.removing   { opacity: .45; transform: translateX(6px); transition: .18s ease; }
    </style>

    <?php
      // include the live data loader for the table
      $jsHref = function_exists('asset')
        ? asset('Public/JS/admin-users.js') . '?v=' . time()
        : '/Public/JS/admin-users.js';
      echo '<script src="' . htmlspecialchars($jsHref, ENT_QUOTES, 'UTF-8') . '"></script>';
    ?>
    <?php
  }
}
