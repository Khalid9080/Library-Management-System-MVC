<?php // MVC/View/Dashboard/Member/my_book_requests.php ?>
<link rel="stylesheet" href="<?= asset('Public/Style/member-my-requests.css') ?>?v=<?= time() ?>" />
<section class="member-my-requests">
  <h1 class="page-title">My Book Requests</h1>
  <div id="memberRequestsGrid" class="card-grid" aria-live="polite"></div>
  <div id="memberReqEmpty" class="empty-hint" style="display:none;">No requests yet. Select books from the Catalog and click “Order a Buy Request”.</div>
</section>
<script src="<?= asset('Public/JS/member-requests.js') ?>?v=<?= time() ?>"></script>
