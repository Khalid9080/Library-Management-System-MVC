<?php // MVC/View/Dashboard/Member/my_books.php ?>
<link rel="stylesheet" href="<?= asset('Public/Style/member-my-books.css') ?>?v=<?= time() ?>" />
<section class="member-my-books">
  <h1 class="page-title">My Books</h1>
  <div id="memberMyBooksGrid" class="my-books-grid" aria-live="polite"></div>
  <div id="memberMyBooksEmpty" class="empty-hint" style="display:none;">No approved books yet.</div>
</section>
<script src="<?= asset('Public/JS/member-my-books.js') ?>?v=<?= time() ?>"></script>
