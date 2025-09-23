<?php // MVC/View/Dashboard/Librarian/buy_history.php ?>
<link rel="stylesheet" href="<?= asset('Public/Style/librarian-history.css') ?>?v=<?= time() ?>" />
<section class="librarian-history">
  <h1 class="page-title">Buy History</h1>

  <div class="history-table-wrap">
    <table class="history-table" id="buyHistoryTable" aria-live="polite">
      <thead>
        <tr>
          <th>ISBN Number</th>
          <th>Book Name</th>
          <th>Author Name</th>
          <th>Category</th>
          <th>Requested By</th>
          <th>Requested At</th>
          <th>Status</th>
          <th>Quantity</th>
          <th>Approved By</th>
          <th>Line Total</th>
        </tr>
      </thead>
      <tbody id="buyHistoryBody"></tbody>
    </table>

    <div class="history-totals">
      <hr />
      <div class="totals-grid" id="buyHistoryTotals">
        <!-- injected by JS -->
      </div>
    </div>
  </div>
</section>
<script src="<?= asset('Public/JS/librarian-history.js') ?>?v=<?= time() ?>"></script>
