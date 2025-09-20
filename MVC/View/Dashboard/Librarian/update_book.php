<?php
// MVC/View/Dashboard/Librarian/update_book.php
// PARTIAL ONLY: included inside dashboard main content.
// No header/footer/sidebar/auth here.
?>
<section class="service-section">
  <div class="librarian-form-wrap">
    <div class="librarian-form-header">
      <h2>Update Book</h2>
      <p>Modify existing book information below.</p>
    </div>

    <form id="updateBookForm" class="librarian-form" novalidate>
      <div class="input-group">
        <label for="isbn">Book ISBN Number</label>
        <input type="text" id="isbn" name="isbn" placeholder="e.g. 978-3-16-148410-0" required />
        <span class="error-message" id="isbnError"></span>
      </div>

      <div class="input-group">
        <label for="title">Book Name</label>
        <input type="text" id="title" name="title" placeholder="e.g. The Pragmatic Programmer" required />
        <span class="error-message" id="titleError"></span>
      </div>

      <div class="input-group">
        <label for="author">Author Name</label>
        <input type="text" id="author" name="author" placeholder="e.g. Andrew Hunt" required />
        <span class="error-message" id="authorError"></span>
      </div>

      <div class="input-group">
        <label for="category">Category</label>
        <select id="category" name="category" required>
          <option value="">Select a category</option>
          <option>Fiction</option>
          <option>Non-Fiction</option>
          <option>Science</option>
          <option>Technology</option>
          <option>History</option>
          <option>Biography</option>
          <option>Children</option>
          <option>Mystery</option>
          <option>Romance</option>
          <option>Fantasy</option>
          <option>Self-Help</option>
        </select>
        <span class="error-message" id="categoryError"></span>
      </div>

      <div class="input-group">
        <label for="customer">Customer Name</label>
        <input type="text" id="customer" name="customer" placeholder="e.g. John Doe" required />
        <span class="error-message" id="customerError"></span>
      </div>

      <div class="input-group">
        <label for="price">Price</label>
        <input type="number" step="0.01" id="price" name="price" placeholder="e.g. 19.99" required />
        <span class="error-message" id="priceError"></span>
      </div>

      <div class="form-actions full">
        <a class="btn-ghost" href="<?= asset('index.php?page=dashboard') ?>">Cancel</a>
        <button type="submit" class="btn-primary">Update Book</button>
      </div>
    </form>
  </div>
</section>

<script src="<?= asset('Public/JS/update_book.js') ?>?v=<?= time() ?>"></script>
