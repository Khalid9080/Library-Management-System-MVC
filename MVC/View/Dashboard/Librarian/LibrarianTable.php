<?php
// MVC/View/Dashboard/LibrarianTable.php
// Reusable component: render a librarian-only responsive table
// Usage:
//   include __DIR__ . '/LibrarianTable.php';
//   render_librarian_table($rows); // $rows optional (falls back to demo data)

if (!function_exists('render_librarian_table')) {
  function render_librarian_table(?array $rows = null): void {
    // Demo rows if none passed (purely UI demo; replace with DB data later)
    if ($rows === null) {
      $rows = [
        [
          'isbn'      => '978-0131103627',
          'book'      => 'The C Programming Language',
          'author'    => 'Kernighan & Ritchie',
          'category'  => 'Programming',
          'customer'  => 'Alice Johnson',
          'price'     => 49.99,
        ],
        [
          'isbn'      => '978-0262033848',
          'book'      => 'Introduction to Algorithms',
          'author'    => 'Cormen et al.',
          'category'  => 'Algorithms',
          'customer'  => 'Bob Williams',
          'price'     => 89.50,
        ],
        [
          'isbn'      => '978-0596009205',
          'book'      => 'Head First Design Patterns',
          'author'    => 'Eric Freeman',
          'category'  => 'Software Engineering',
          'customer'  => 'Clara Mendes',
          'price'     => 54.00,
        ],
        [
          'isbn'      => '978-0131101630',
          'book'      => 'Structure and Interpretation of Computer Programs',
          'author'    => 'Abelson & Sussman',
          'category'  => 'CS Theory',
          'customer'  => 'David Kim',
          'price'     => 72.25,
        ],
      ];
    }

    // Base URL helper if available; fall back gracefully
    $cssHref = function_exists('asset')
      ? asset('Public/Style/librarian-table.css') . '?v=' . time()
      : '/Public/Style/librarian-table.css';

    // Include stylesheet (safe even if already added)
    echo '<link rel="stylesheet" href="' . htmlspecialchars($cssHref, ENT_QUOTES, 'UTF-8') . '" />';

    ?>
    <section class="librarian-table-section" aria-labelledby="librarianTableTitle">
      <div class="container librarian-table-container">
        <h2 id="librarianTableTitle" class="librarian-table-title">
          All the Books/Authors Information
          <small class="subtitle">Responsive list table</small>
        </h2>

        <ul class="responsive-table" role="table" aria-label="All the Books and Authors Information">
          <li class="table-header" role="row">
            <div class="col col-1" role="columnheader">ISBN Number</div>
            <div class="col col-2" role="columnheader">Book Name</div>
            <div class="col col-3" role="columnheader">Author Name</div>
            <div class="col col-4" role="columnheader">Category</div>
            <div class="col col-5" role="columnheader">Customer Name</div>
            <div class="col col-6" role="columnheader">Book Price</div>
            <div class="col col-7" role="columnheader">Action</div>
          </li>

          <?php foreach ($rows as $r): ?>
            <li class="table-row" role="row" data-isbn="<?= htmlspecialchars($r['isbn'], ENT_QUOTES, 'UTF-8') ?>">
              <div class="col col-1" role="cell" data-label="ISBN Number">
                <?= htmlspecialchars($r['isbn'], ENT_QUOTES, 'UTF-8') ?>
              </div>
              <div class="col col-2" role="cell" data-label="Book Name">
                <?= htmlspecialchars($r['book'], ENT_QUOTES, 'UTF-8') ?>
              </div>
              <div class="col col-3" role="cell" data-label="Author Name">
                <?= htmlspecialchars($r['author'], ENT_QUOTES, 'UTF-8') ?>
              </div>
              <div class="col col-4" role="cell" data-label="Category">
                <?= htmlspecialchars($r['category'], ENT_QUOTES, 'UTF-8') ?>
              </div>
              <div class="col col-5" role="cell" data-label="Customer Name">
                <?= htmlspecialchars($r['customer'], ENT_QUOTES, 'UTF-8') ?>
              </div>
              <div class="col col-6" role="cell" data-label="Book Price">
                $<?= number_format((float)$r['price'], 2) ?>
              </div>
              <div class="col col-7" role="cell" data-label="Action">
                <button type="button"
                        class="row-delete-btn"
                        aria-label="Delete this row (demo only)"
                        data-isbn="<?= htmlspecialchars($r['isbn'], ENT_QUOTES, 'UTF-8') ?>">
                  Delete
                </button>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <!-- Demo-only behavior: remove row from DOM. Real DB delete will be wired later. -->
      <script>
        (function(){
          document.addEventListener('click', function(e){
            const btn = e.target.closest('.row-delete-btn');
            if (!btn) return;

            // Demo: remove the row visually (no server call yet)
            const row = btn.closest('.table-row');
            if (row) {
              row.classList.add('removing');
              setTimeout(() => row.remove(), 180);
            }

            // Later: POST to your controller to delete by ISBN or row id
            // fetch(endpoint, { method:'POST', body:formData }) ...
          });
        })();
      </script>
    </section>
    <?php
  }
}

// Auto-render once if included directly (optional)
if (!debug_backtrace()) {
  render_librarian_table();
}
