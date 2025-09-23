// Public/JS/add_book.js
(function () {
  const form = document.getElementById('addBookForm');
  if (!form) return;

  const fields = {
    isbn: document.getElementById('isbn'),
    title: document.getElementById('title'),
    author: document.getElementById('author'),
    category: document.getElementById('category'),
    pub_year: document.getElementById('pub_year'),
    price: document.getElementById('price'),
  };
  const errs = {
    isbn: document.getElementById('isbnError'),
    title: document.getElementById('titleError'),
    author: document.getElementById('authorError'),
    category: document.getElementById('categoryError'),
    pub_year: document.getElementById('pub_yearError'),
    price: document.getElementById('priceError'),
  };

  function showError(k, msg) {
    fields[k]?.closest('.input-group')?.classList.add('error');
    if (errs[k]) { errs[k].textContent = msg; errs[k].classList.add('show'); }
  }
  function clearError(k) {
    fields[k]?.closest('.input-group')?.classList.remove('error');
    if (errs[k]) { errs[k].textContent = ''; errs[k].classList.remove('show'); }
  }
  function isFilled(v) { return v != null && String(v).trim() !== ''; }

  function validate() {
    let ok = true;
    Object.keys(fields).forEach((k) => {
      clearError(k);
      const v = fields[k]?.value || '';
      if (!isFilled(v)) { showError(k, 'This field is required'); ok = false; }
    });
    return ok;
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    if (!validate()) return;

    const fd = new FormData(form);
    fd.append('action', 'add_book');
    if (!fd.has('publication_year')) {
      fd.append('publication_year', fields.pub_year.value);
    }

    fetch('MVC/Controller/BooksController.php', { method: 'POST', body: fd })
      .then(r => r.json())
      .then(j => {
        if (!j.ok) { alert(j.error || 'Error'); return; }
        alert('Book added!');
        form.reset();

        // Refresh the librarian live table
        if (window.LIB_TABLE && typeof window.LIB_TABLE.refresh === 'function') {
          window.LIB_TABLE.refresh();
        }

        // 🔁 Refresh the "Total Books" count card as well
        if (window.TOTAL_BOOKS && typeof window.TOTAL_BOOKS.refresh === 'function') {
          window.TOTAL_BOOKS.refresh();
        }

        // after LIB_TABLE.refresh()
        if (window.MEMBER_CATALOG && typeof window.MEMBER_CATALOG.refresh === 'function') {
          window.MEMBER_CATALOG.refresh();
        }
      })
      .catch(() => alert('Network error'));
  });
})();
