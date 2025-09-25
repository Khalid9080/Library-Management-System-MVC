<?php // MVC/View/Dashboard/Admin/transaction_history.php ?>
<link rel="stylesheet" href="<?= asset('Public/Style/admin-transactions.css') ?>?v=<?= time() ?>" />

<section class="admin-transactions">
  <h1 class="page-title">Transaction History</h1>

  <div class="history-table-wrap">
    <table class="history-table" id="adminTxnTable" aria-live="polite">
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
      <tbody id="adminTxnBody"></tbody>
    </table>

    <div class="history-totals">
      <hr />
      <div class="totals-grid" id="adminTxnTotals">
        <!-- injected by JS -->
      </div>
    </div>
  </div>
</section>

<style>
  /* === Status badges (mirrors role badges) === */
  .badge-status {
    display:inline-block;
    padding:6px 10px;
    border-radius:999px;
    font-weight:700;
    font-size:12px;
    border:1px solid rgba(0,0,0,.08);
    text-transform: capitalize;
  }
  .badge-status.badge-approved {
    background:#e9faef;
    color:#14532d;
    border-color:#c9f4d8;
  }
  .badge-status.badge-rejected {
    background:#ffe8e8;
    color:#7a1f1f;
    border-color:#ffc9c9;
  }
</style>

<script src="<?= asset('Public/JS/admin-transactions.js') ?>?v=<?= time() ?>"></script>
