<?php // MVC/View/Dashboard/Librarian/approved_buy_requests.php ?>
<link rel="stylesheet" href="<?= asset('Public/Style/librarian-approvals.css') ?>?v=<?= time() ?>" />
<section class="librarian-approvals">
  <h1 class="page-title">Pending Buy Requests</h1>
  <div id="libPendingGrid" class="card-grid" aria-live="polite"></div>
  <div id="libPendingEmpty" class="empty-hint" style="display:none;">No pending requests.</div>
</section>
<script src="<?= asset('Public/JS/librarian-approvals.js') ?>?v=<?= time() ?>"></script>
