// Public/JS/update_book.js
(function () {
  var form = document.getElementById('updateBookForm');
  if (!form) return;

  var fields = {
    isbn: document.getElementById('isbn'),
    title: document.getElementById('title'),
    author: document.getElementById('author'),
    category: document.getElementById('category'),
    pub_year: document.getElementById('pub_year'),
    price: document.getElementById('price'),
  };

  var errs = {
    isbn: document.getElementById('isbnError'),
    title: document.getElementById('titleError'),
    author: document.getElementById('authorError'),
    category: document.getElementById('categoryError'),
    pub_year: document.getElementById('pub_yearError'),
    price: document.getElementById('priceError'),
  };

  function showError(k, msg) {
    var g = fields[k]?.closest('.input-group');
    if (g) g.classList.add('error');
    if (errs[k]) {
      errs[k].textContent = msg;
      errs[k].classList.add('show');
    }
  }
  function clearError(k) {
    var g = fields[k]?.closest('.input-group');
    if (g) g.classList.remove('error');
    if (errs[k]) {
      errs[k].textContent = '';
      errs[k].classList.remove('show');
    }
  }
  function isFilled(v) { return v != null && String(v).trim() !== ''; }

  function validate() {
    var ok = true;
    Object.keys(fields).forEach(function (k) {
      clearError(k);
      var v = fields[k]?.value || '';
      if (!isFilled(v)) {
        showError(k, 'This field is required');
        ok = false;
      }
    });
    return ok;
  }

  // Update row in the librarian table (UL/LI structure)
  function updateRowInTable(updated) {
    var list = document.getElementById('libBooksTable');
    if (!list) return;

    var row = list.querySelector('li.table-row[data-isbn="' + CSS.escape(updated.isbn) + '"]');
    if (!row) return;

    var c = {
      title:  row.querySelector('[data-col="title"]'),
      author: row.querySelector('[data-col="author"]'),
      category: row.querySelector('[data-col="category"]'),
      year:   row.querySelector('[data-col="published_year"]'),
      price:  row.querySelector('[data-col="price"]')
    };
    if (c.title)  c.title.textContent  = updated.title;
    if (c.author) c.author.textContent = updated.author;
    if (c.category) c.category.textContent = updated.category;
    if (c.year)   c.year.textContent   = updated.published_year;
    if (c.price)  c.price.textContent  = '$' + Number(updated.price).toFixed(2);
  }

  // Optional: Prefill form on ISBN blur
  fields.isbn?.addEventListener('blur', function () {
    var isbn = fields.isbn.value.trim();
    if (!isbn) return;

    // Use relative path: this file is rendered within index.php layout
    var url = 'MVC/Controller/BooksController.php?action=get_book&isbn=' + encodeURIComponent(isbn);
    fetch(url)
      .then(r => r.json())
      .then(data => {
        if (data && data.ok && data.row) {
          fields.title.value    = data.row.title || '';
          fields.author.value   = data.row.author || '';
          fields.category.value = data.row.category || '';
          var yyyy = String(data.row.published_year || '').padStart(4, '0');
          fields.pub_year.value = yyyy ? (yyyy + '-01-01') : '';
          fields.price.value    = data.row.price != null ? Number(data.row.price) : '';
        } else if (data && data.error) {
          showError('isbn', data.error);
        }
      })
      .catch(() => { /* ignore */ });
  });

  form.addEventListener('submit', function (e) {
    if (!validate()) {
      e.preventDefault();
      e.stopPropagation();
      return;
    }
    e.preventDefault();

    var endpoint = 'MVC/Controller/BooksController.php';
    var body = new FormData();
    body.append('action', 'update_book');
    body.append('isbn', fields.isbn.value.trim());
    body.append('title', fields.title.value.trim());
    body.append('author', fields.author.value.trim());
    body.append('category', fields.category.value.trim());
    body.append('publication_year', fields.pub_year.value.trim());
    body.append('price', fields.price.value.trim());

    fetch(endpoint, { method: 'POST', body })
      .then(r => r.json())
      .then(data => {
        if (!data || !data.ok) {
          var msg = (data && data.error) ? data.error : 'Update failed';
          showError('isbn', msg);
          return;
        }

        alert('✅ ' + (data.message || 'Book updated'));

        // Update visible list row in real-time
        if (data.row) updateRowInTable(data.row);

        // Also let the full table reload if needed elsewhere
        if (window.LIB_TABLE && typeof window.LIB_TABLE.refresh === 'function') {
          window.LIB_TABLE.refresh();
        }
      })
      .catch(() => {
        showError('isbn', 'Network error');
      });
  });
})();
