<?php
// MVC/View/Dashboard/Admin/AdminTable.php
// Reusable: render_admin_user_table($rows = null)
// Default demo rows; later replace with DB data

if (!function_exists('render_admin_user_table')) {
  function render_admin_user_table(?array $rows = null): void {
    if ($rows === null) {
      $rows = [
        ['username'=>'Khalid Ahmed', 'email'=>'khalid.member@example.com', 'phone'=>'+8801712345678', 'role'=>'member'],
        ['username'=>'Sara Hossain', 'email'=>'sara.librarian@example.com', 'phone'=>'+8801811223344', 'role'=>'librarian'],
        ['username'=>'Jahid Hasan',  'email'=>'jahid.admin@example.com',    'phone'=>'+8801999887766', 'role'=>'admin'],
        ['username'=>'Mitu Rahman',  'email'=>'mitu.member@example.com',    'phone'=>'+8801300554433', 'role'=>'member'],
      ];
    }

    // Use librarian-table.css for base responsive grid; admin.css adds tweaks
    $cssHref = function_exists('asset')
      ? asset('Public/Style/librarian-table.css') . '?v=' . time()
      : '/Public/Style/librarian-table.css';

    echo '<link rel="stylesheet" href="' . htmlspecialchars($cssHref, ENT_QUOTES, 'UTF-8') . '" />';

    ?>
    <section class="admin-users-table" aria-labelledby="adminUsersTitle">
      <div class="librarian-table-container">
        <h2 id="adminUsersTitle" class="librarian-table-title">
          Users Directory
          <small class="subtitle">Username, email & role overview</small>
        </h2>

        <ul class="responsive-table" role="table" aria-label="All Users">
          <li class="table-header" role="row">
            <div class="col col-1" role="columnheader">Username</div>
            <div class="col col-2" role="columnheader">Email</div>
            <div class="col col-3" role="columnheader">Phone Number</div>
            <div class="col col-4" role="columnheader">Role</div>
            <div class="col col-5" role="columnheader">Action</div> <!-- NEW -->
          </li>

          <?php foreach ($rows as $r): ?>
            <li class="table-row" role="row">
              <div class="col col-1" role="cell" data-label="Username">
                <?= htmlspecialchars($r['username'], ENT_QUOTES, 'UTF-8') ?>
              </div>
              <div class="col col-2" role="cell" data-label="Email">
                <?= htmlspecialchars($r['email'], ENT_QUOTES, 'UTF-8') ?>
              </div>
              <div class="col col-3" role="cell" data-label="Phone Number">
                <?= htmlspecialchars($r['phone'], ENT_QUOTES, 'UTF-8') ?>
              </div>
              <div class="col col-4" role="cell" data-label="Role">
                <span class="badge badge-role badge-<?= htmlspecialchars($r['role'], ENT_QUOTES, 'UTF-8') ?>">
                  <?= htmlspecialchars(ucfirst($r['role']), ENT_QUOTES, 'UTF-8') ?>
                </span>
              </div>
              <div class="col col-5" role="cell" data-label="Action">
                <button type="button"
                        class="row-delete-btn"
                        aria-label="Delete this user (demo only)"
                        data-email="<?= htmlspecialchars($r['email'], ENT_QUOTES, 'UTF-8') ?>">
                  Delete
                </button>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </section>

    <style>
      /* Column distribution for 5-col table (adds Action) */
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

      /* On mobile (from your librarian-table.css), the cols stack to 100% automatically. */
    </style>

    <script>
      // Demo-only: visually remove the row when Delete is clicked.
      (function(){
        document.addEventListener('click', function(e){
          const btn = e.target.closest('.row-delete-btn');
          if (!btn) return;

          const row = btn.closest('.table-row');
          if (row) {
            row.classList.add('removing');
            setTimeout(() => row.remove(), 180);
          }

          // Later: POST to your controller to delete by id/email
          // const form = new FormData();
          // form.append('action', 'delete_user');
          // form.append('email', btn.dataset.email);
          // fetch('MVC/Controller/AdminController.php', { method:'POST', body: form }).then(...)
        });
      })();
    </script>
    <?php
  }
}
