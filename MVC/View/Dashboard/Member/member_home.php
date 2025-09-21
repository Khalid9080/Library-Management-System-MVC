<?php
// MVC/View/Dashboard/Member/member_home.php
$css = function_exists('asset') ? asset('Public/Style/member.css') . '?v=' . time() : '/Public/Style/member.css';
$js  = function_exists('asset') ? asset('Public/JS/member.js') . '?v=' . time() : '/Public/JS/member.js';
?>
<link rel="stylesheet" href="<?= htmlspecialchars($css, ENT_QUOTES, 'UTF-8') ?>" />

<section class="member-dashboard">
  <!-- Page intro (title + subtitle) -->
  <div class="member-intro">
    <h3 class="member-title">Find Your Next Read</h3>
    <p class="member-subtitle">
      Search your favorite books below. All the books’ information is here — ready to send a request to buy new books.
    </p>
  </div>

  <!-- Search Bar (starts OPEN) -->
  <div class="member-search-wrap">
    <div class="search-wrapper active" id="memberSearch">
      <div class="input-holder">
        <input type="text" class="search-input" id="bookSearchInput" placeholder="Type to Search Books" />
        <button class="search-icon" id="searchToggleBtn" aria-label="Open search"><span></span></button>
      </div>
      <button class="close" id="searchCloseBtn" aria-label="Close search"></button>
    </div>
  </div>

  <!-- Table header (title + subtitle) -->
  <div class="member-table-head">
    <h4 class="table-title">Catalog</h4>
    <p class="table-subtitle">Browse the list and select items — then press “Order a Buy Request”.</p>
  </div>

  <!-- Results Table -->
  <div class="member-table-wrap">
    <div class="table-scroll">
      <table class="member-table" id="memberBooksTable">
        <thead>
          <tr>
            <th class="col-check">
              <!-- no visible text; keep ARIA label only -->
              <input type="checkbox" id="selectAll" aria-label="Select all rows" />
            </th>
            <th>ISBN Number</th>
            <th>Book Name</th>
            <th>Author Name</th>
            <th>Category</th>
            <th>Year of Publication</th>
            <th>Book Price</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $rows = [
            ['978-0131103627','The C Programming Language','Kernighan & Ritchie','Programming','1988-04-01',49.99],
            ['978-0262033848','Introduction to Algorithms','Cormen et al.','Algorithms','2009-07-31',89.50],
            ['978-0596009205','Head First Design Patterns','Eric Freeman','Software Engineering','2004-10-25',54.00],
            ['978-0131101630','SICP','Abelson & Sussman','CS Theory','1996-07-25',72.25],
            ['978-0201633610','Design Patterns','Gamma et al.','Software Engineering','1994-10-31',64.95],
          ];
          foreach ($rows as $r): ?>
            <tr>
              <td class="col-check"><input type="checkbox" class="row-check" aria-label="Select row" /></td>
              <td><?= htmlspecialchars($r[0]) ?></td>
              <td><?= htmlspecialchars($r[1]) ?></td>
              <td><?= htmlspecialchars($r[2]) ?></td>
              <td><?= htmlspecialchars($r[3]) ?></td>
              <td><?= htmlspecialchars($r[4]) ?></td>
              <td>$<?= number_format($r[5], 2) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Full-width action button -->
    <div class="table-action">
      <button type="button" class="buy-request-btn" id="buyRequestBtn">Order a Buy Request</button>
    </div>
  </div>
</section>

<script src="<?= htmlspecialchars($js, ENT_QUOTES, 'UTF-8') ?>" defer></script>
