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
      Search your favorite books below. All the books’ information is live from the database — ready to send a request to buy new books.
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
      <table class="member-table" id="memberBooksTable" aria-live="polite">
        <thead>
          <tr>
            <th class="col-check">
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
        <tbody id="memberBooksTbody">
          <!-- rows injected by JS -->
        </tbody>
      </table>
    </div>

    <!-- Full-width action button -->
    <div class="table-action">
      <button type="button" class="buy-request-btn" id="buyRequestBtn">Order a Buy Request</button>
    </div>

    <!-- empty state -->
    <div id="memberEmpty" style="display:none; padding:12px 8px; color:#666;">
      No books yet. Ask the librarian to add some!
    </div>
  </div>
</section>

<script src="<?= htmlspecialchars($js, ENT_QUOTES, 'UTF-8') ?>" defer></script>
