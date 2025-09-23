<?php
// MVC/View/Reusable_Components/dashboard.php
require_once __DIR__ . '/../../Controller/guard.php';
ensure_auth();

$user = auth_user();
$role = strtolower($user['role'] ?? 'member');
$username = $user['username'] ?? 'User';

$roleHeading = ucfirst($role);
$isLibrarian = ($role === 'librarian');
$isMember = ($role === 'member');   // <--- ADD THIS
$isAdmin = ($role === 'admin');

// Decide if we should show a form inside the main panel
$panel = $_GET['panel'] ?? null;               // 'add_book' or 'update_book' or 'manage_users'
$showForm = $isLibrarian && in_array($panel, ['add_book', 'update_book'], true);
?>
<!-- NOTE: add a version to bust cache -->
<link rel="stylesheet" href="<?= asset('Public/Style/librarian-table.css') ?>?v=<?= time() ?>" />
<link rel="stylesheet" href="<?= asset('Public/Style/dashboard.css') ?>?v=<?= time() ?>" />
<?php if ($showForm): ?>
  <link rel="stylesheet" href="<?= asset('Public/Style/librarian-forms.css') ?>?v=<?= time() ?>" />
<?php endif; ?>
<link rel="stylesheet" href="<?= asset('Public/Style/admin.css') ?>?v=<?= time() ?>" /> <!-- ← NEW -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link
  href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
  rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/phosphor-icons@1.4.2/src/css/icons.css">

<!-- Inline, librarian-scoped fixes so they apply even if external CSS is stale -->
<style>
  /* ---- tokens ---- */
  :root {
    --dash-pad: clamp(16px, 2vw, 32px);
    --sidebar-w: 240px;
    /* keep in sync with grid/sidebar */
  }

  /* Scope tweaks to librarian bits you already had */
  .role-dashboard.role-librarian .service-section>h2 {
    color: #0b1b33 !important;
    font-weight: 800 !important;
  }

  .role-dashboard.role-librarian .welcome-title {
    color: #fff !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, .3);
  }

  .role-dashboard.role-librarian .tile.tile-remove-books {
    background: #ffe3e3 !important;
    border-color: #ffd0d0 !important;
  }

  /* Sidebar column should stretch so Logout can sit at bottom */
  .page-dashboard .role-dashboard .app-body {
    align-items: stretch;
  }

  .page-dashboard .role-dashboard .app-body-navigation {
    height: 100%;
    min-height: calc(100vh - 220px);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }

  .role-dashboard.role-librarian .logout-button {
    margin-top: auto !important;
  }

  /* ========= Full-bleed dashboard canvas (no outer gutters) ========= */
  .page-dashboard .role-dashboard {
    padding: 0 !important;
    background: transparent !important;
  }

  .page-dashboard .role-dashboard .app {
    width: 100% !important;
    /* avoid horizontal scroll; was 100vw */
    max-width: none !important;
    min-height: 100vh !important;
    padding: 0 !important;
    border: 0 !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    background: #fff !important;
  }

  /* ========= HEADER: put logo & title on the same row ========= */
  /* 2 columns: sidebar width for the logo, flexible for the title */
  .page-dashboard .role-dashboard .app-header {
    display: grid !important;
    grid-template-columns: var(--sidebar-w) 1fr !important;
    align-items: center;
    column-gap: var(--dash-pad);
    padding: 12px var(--dash-pad) !important;
  }

  /* Logo container */
  .role-dashboard .app-header-logo {
    padding: 0 !important;
    display: flex;
    justify-content: flex-start;
  }

  /* Logo pill matches sidebar width */
  .role-dashboard .app-header-logo .logo {
    width: 100%;
    max-width: var(--sidebar-w);
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    background: #2f3a48;
    border: 1px solid #445163;
    border-radius: 12px;
    color: #eaf0ff;
    box-shadow: 0 6px 16px rgba(0, 0, 0, .08);
  }

  .role-dashboard .app-header-logo .logo-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: grid;
    place-items: center;
    background: rgba(255, 255, 255, .06);
  }

  .role-dashboard .app-header-logo .logo-title {
    display: flex;
    flex-direction: column;
    line-height: 1.15;
    margin: 0;
  }

  .role-dashboard .app-header-logo .logo-title span:first-child {
    font-size: .98rem;
    font-weight: 700;
    letter-spacing: .2px;
  }

  .role-dashboard .app-header-logo .logo-title span:last-child {
    font-size: .82rem;
    opacity: .85;
  }

  /* Welcome title fills the right column (no viewport hacks) */
  .role-dashboard .welcome-title {
    margin: 0;
    padding: 16px 18px;
    background: #343a40;
    color: #fff !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, .3);
    border-radius: 12px;
    width: 100%;
    /* fill its grid column */
  }

  /* ========= MAIN GRID (sidebar + content) ========= */
  .page-dashboard .role-dashboard .app-body {
    padding: 24px var(--dash-pad) !important;
    display: grid;
    grid-template-columns: var(--sidebar-w) 1fr !important;
    column-gap: 2rem !important;
  }

  /* Sidebar panel look */
  .role-dashboard .app-body-navigation {
    background: #f2f4f7;
    border: 1px solid #e4e8f0;
    border-radius: 12px;
    padding: 16px 14px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: calc(100vh - 180px);
  }

  .role-dashboard .navigation a {
    padding: 8px 10px;
    border-radius: 8px;
  }

  .role-dashboard .navigation a:hover {
    background: rgba(0, 0, 0, .04);
  }

  /* Logout button styling */
  .role-dashboard .logout-button {
    margin-top: auto !important;
    padding: .45rem 1rem;
    border: 1px solid rgba(31, 31, 31, .15);
    background: linear-gradient(180deg, #ffe8e8, #ffdcdc);
    color: #7a1f1f;
    border-radius: 10px;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0, 0, 0, .06);
    transition: transform .15s ease, box-shadow .2s ease, background .2s ease;
    font-weight: 600;
  }

  .role-dashboard .logout-button:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(122, 31, 31, .15);
  }

  .role-dashboard .logout-button:active {
    transform: translateY(0);
    box-shadow: 0 2px 8px rgba(122, 31, 31, .10);
  }

  /* ========= Responsive: stack header on small screens ========= */
  @media (max-width: 900px) {
    .page-dashboard .role-dashboard .app-header {
      grid-template-columns: 1fr !important;
      row-gap: 10px;
    }

    .role-dashboard .app-header-logo .logo {
      max-width: 100%;
    }

    .page-dashboard .role-dashboard .app-body {
      grid-template-columns: 1fr !important;
    }
  }
</style>

<section class="role-dashboard role-<?= htmlspecialchars($role, ENT_QUOTES, 'UTF-8') ?>">
  <div class="app">
    <header class="app-header">
      <div class="app-header-logo">
        <div class="logo">
          <span class="logo-icon">
            <img src="https://assets.codepen.io/285131/almeria-logo.svg" alt="LMS logo" />
          </span>
          <h1 class="logo-title">
            <span><?= htmlspecialchars($roleHeading, ENT_QUOTES, 'UTF-8') ?></span>
            <span>Dashboard</span>
          </h1>
        </div>
      </div>

      <!-- Dynamic welcome line showing role + username -->
      <h2 class="welcome-title">
        Welcome to the <?= htmlspecialchars($roleHeading, ENT_QUOTES, 'UTF-8') ?> Dashboard,
        <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>!
      </h2>
    </header>

    <div class="app-body">
      <div class="app-body-navigation">
        <nav class="navigation" aria-label="Dashboard navigation">
          <?php if ($isLibrarian): ?>
            <a href="<?= asset('index.php?page=dashboard') ?>">
              <i class="ph-house"></i>
              <span>Dashboard</span>
            </a>
            <a href="<?= asset('index.php?page=dashboard&panel=approved_buy_requests') ?>">
              <i class="ph-browsers"></i>
              <span>Approved Buy Requests</span>
            </a>
            <a href="<?= asset('index.php?page=dashboard&panel=buy_history') ?>">
              <i class="ph-check-square"></i>
              <span>Buy History</span>
            </a>

          <?php elseif ($isMember): ?>
            <a href="<?= asset('index.php?page=dashboard') ?>">
              <i class="ph-house"></i>
              <span>Dashboard</span>
            </a>
            <a href="<?= asset('index.php?page=dashboard&panel=my_books') ?>">
              <i class="ph-book"></i>
              <span>My Books</span>
            </a>
            <a href="<?= asset('index.php?page=dashboard&panel=my_book_requests') ?>">
              <i class="ph-list-checks"></i>
              <span>My Book Request</span>
            </a>

          <?php elseif ($isAdmin): ?>
            <!-- ADMIN: three buttons on LEFT, with proper icons -->
            <a href="<?= asset('index.php?page=dashboard') ?>">
              <i class="ph-house"></i>
              <span>Dashboard</span>
            </a>
            <a href="<?= asset('index.php?page=dashboard&panel=manage_users') ?>">
              <i class="ph-users-three"></i>
              <span>Manage Users</span>
            </a>
            <a href="<?= asset('index.php?page=dashboard&panel=transactions') ?>">
              <i class="ph-receipt"></i>
              <span>Transaction History</span>
            </a>

          <?php else: ?>
            <a href="<?= asset('index.php?page=dashboard') ?>">
              <i class="ph-house"></i>
              <span>Dashboard</span>
            </a>
          <?php endif; ?>
        </nav>

        <!-- Logout stays, CSS pushes it to the bottom -->
        <button class="logout-button" id="logoutBtn" aria-label="Logout">Logout</button>
      </div>

      <div class="app-body-main-content">
        <?php if ($showForm && $panel === 'add_book'): ?>
          <?php include __DIR__ . '/../Dashboard/Librarian/add_book.php'; ?>

        <?php elseif ($showForm && $panel === 'update_book'): ?>
          <?php include __DIR__ . '/../Dashboard/Librarian/update_book.php'; ?>

        <?php elseif ($isLibrarian && $panel === 'approved_buy_requests'): ?>
          <?php include __DIR__ . '/../Dashboard/Librarian/approved_buy_requests.php'; ?>

        <?php elseif ($isLibrarian && $panel === 'buy_history'): ?>
          <?php include __DIR__ . '/../Dashboard/Librarian/buy_history.php'; ?>

        <?php elseif ($isMember && $panel === 'my_books'): ?>
          <?php include __DIR__ . '/../Dashboard/Member/my_books.php'; ?>

        <?php elseif ($isMember && $panel === 'my_book_requests'): ?>
          <?php include __DIR__ . '/../Dashboard/Member/my_book_requests.php'; ?>

        <?php else: ?>
          <?php if ($isMember): ?>
            <?php include __DIR__ . '/../Dashboard/Member/member_home.php'; ?>


          <?php elseif ($isLibrarian): ?>
            <!-- Librarian view remains as you had -->
            <section class="service-section">
              <h2>Service</h2>
              <div class="tiles">
                <article class="tile">
                  <div class="tile-header">
                    <i class="ph-lightning-light"></i>
                    <h3><a href="<?= $isLibrarian ? asset('index.php?page=dashboard&panel=add_book') : '#' ?>">
                        <span>Adding New Books</span>
                      </a></h3>
                  </div>
                  <a href="<?= $isLibrarian ? asset('index.php?page=dashboard&panel=add_book') : '#' ?>">
                    <span>Go to service</span>
                    <span class="icon-button"><i class="ph-caret-right-bold"></i></span>
                  </a>
                </article>

                <article class="tile">
                  <div class="tile-header">
                    <i class="ph-fire-simple-light"></i>
                    <h3><a href="<?= $isLibrarian ? asset('index.php?page=dashboard&panel=update_book') : '#' ?>">
                        <span>Update Books Info</span>
                      </a></h3>
                  </div>
                  <a href="<?= $isLibrarian ? asset('index.php?page=dashboard&panel=update_book') : '#' ?>">
                    <span>Go to service</span>
                    <span class="icon-button"><i class="ph-caret-right-bold"></i></span>
                  </a>
                </article>

                <article class="tile tile-remove-books">
                  <div class="tile-header">
                    <i class="ph-file-light"></i>
                    <h3>
                      <span>Total Books:</span>
                      <strong id="totalBooksCount" style="margin-left:.4rem;">…</strong>
                    </h3>
                  </div>
                </article>

              </div>
            </section>

            <?php
            require_once __DIR__ . '/../Dashboard/Librarian/LibrarianTable.php';
            render_librarian_table();
            ?>

          <?php elseif ($isAdmin): ?>
            <?php if (($panel ?? '') === 'manage_users'): ?>
              <?php include __DIR__ . '/../Dashboard/Admin/manage_users.php'; ?>

            <?php elseif (($panel ?? '') === 'transactions'): ?>
              <?php include __DIR__ . '/../Dashboard/Admin/transaction_history.php'; ?>

            <?php else: ?>
              <!-- ADMIN: KPI cards + users table -->
              <?php include __DIR__ . '/../Dashboard/Admin/admin_home.php'; ?>
              <?php
              require_once __DIR__ . '/../Dashboard/Admin/AdminTable.php';
              render_admin_user_table();
              ?>
              <script src="<?= asset('Public/JS/admin.js') ?>?v=<?= time() ?>"></script>
            <?php endif; ?>

          <?php else: ?>
            <div class="admin-placeholder">
              <p>Welcome to the Admin Dashboard.</p>
            </div>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    </div>
</section>

<script>
  // Logout (unchanged)
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('#logoutBtn');
    if (!btn) return;
    const root =
      (document.querySelector('base')?.href ||
        (window.APP_BASE_URL || document.baseURI))
        .replace(/index\.php.*$/, '')
        .replace(/\/+$/, '') + '/';

    const endpoint = root + 'MVC/Controller/AuthController.php';
    const body = new FormData();
    body.append('action', 'logout');
    body.append('baseUrl', root);

    fetch(endpoint, { method: 'POST', body })
      .then(r => r.json())
      .then(data => {
        if (data && data.ok && data.redirect) {
          window.location = data.redirect;
        } else {
          window.location = root + 'index.php?page=login';
        }
      })
      .catch(() => { window.location = root + 'index.php?page=login'; });
  });
</script>
<script>
  // Fetch and render total books created by the current librarian
  (function () {
    const el = document.getElementById('totalBooksCount');
    if (!el) return;

    function loadTotal() {
      fetch('MVC/Controller/BooksController.php?action=count_books')
        .then(r => r.json())
        .then(j => {
          if (j && j.ok) {
            el.textContent = j.count;
          } else {
            el.textContent = '—';
          }
        })
        .catch(() => { el.textContent = '—'; });
    }

    // Expose for other scripts (e.g., after add_book)
    window.TOTAL_BOOKS = { refresh: loadTotal };

    loadTotal();
  })();
</script>
