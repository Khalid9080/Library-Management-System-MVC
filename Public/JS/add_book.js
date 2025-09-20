// Simple "required" validation like register.js vibe
(function () {
  var form = document.getElementById('addBookForm');
  if (!form) return;

  var fields = {
    isbn: document.getElementById('isbn'),
    title: document.getElementById('title'),
    author: document.getElementById('author'),
    category: document.getElementById('category'),
    customer: document.getElementById('customer'),
    price: document.getElementById('price'),
  };

  var errs = {
    isbn: document.getElementById('isbnError'),
    title: document.getElementById('titleError'),
    author: document.getElementById('authorError'),
    category: document.getElementById('categoryError'),
    customer: document.getElementById('customerError'),
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

  form.addEventListener('submit', function (e) {
    if (!validate()) {
      e.preventDefault();
      e.stopPropagation();
    }
  });
})();
