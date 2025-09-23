<?php
// MVC/View/Dashboard/Librarian/LibrarianTable.php
if (!function_exists('render_librarian_table')) {
  function render_librarian_table(): void {
    $cssHref = function_exists('asset')
      ? asset('Public/Style/librarian-table.css') . '?v=' . time()
      : '/Public/Style/librarian-table.css';
    echo '<link rel="stylesheet" href="' . htmlspecialchars($cssHref, ENT_QUOTES, 'UTF-8') . '" />';
    ?>
    <section class="librarian-table-section" aria-labelledby="librarianTableTitle">
      <div class="container librarian-table-container">
        <h2 id="librarianTableTitle" class="librarian-table-title">
          All the Books/Authors Information
          <small class="subtitle">Live data from database</small>
        </h2>

        <ul class="responsive-table" role="table" aria-label="All the Books and Authors Information" id="libBooksTable">
          <li class="table-header" role="row">
            <div class="col col-1" role="columnheader">ISBN Number</div>
            <div class="col col-2" role="columnheader">Book Name</div>
            <div class="col col-3" role="columnheader">Author Name</div>
            <div class="col col-4" role="columnheader">Category</div>
            <div class="col col-5" role="columnheader">Year of Publication</div>
            <div class="col col-6" role="columnheader">Book Price</div>
            <div class="col col-7" role="columnheader">Action</div>
          </li>
          <!-- rows injected by JS -->
        </ul>

        <div id="libTableEmpty" style="display:none; padding:12px 8px; color:#666;">
          No books yet. Add your first book from “Adding New Books”.
        </div>
      </div>

      <script>
        (function(){
          const table = document.getElementById('libBooksTable');
          const emptyMsg = document.getElementById('libTableEmpty');

          function money(n){ return Number(n).toFixed(2); }

          function rowHtml(r){
            const year = r.published_year ?? '';
            return `
              <li class="table-row" role="row" data-id="${r.id}" data-isbn="${r.isbn}">
                <div class="col col-1" role="cell" data-label="ISBN Number" data-col="isbn">${escapeHtml(r.isbn)}</div>
                <div class="col col-2" role="cell" data-label="Book Name" data-col="title">${escapeHtml(r.title)}</div>
                <div class="col col-3" role="cell" data-label="Author Name" data-col="author">${escapeHtml(r.author)}</div>
                <div class="col col-4" role="cell" data-label="Category" data-col="category">${escapeHtml(r.category)}</div>
                <div class="col col-5" role="cell" data-label="Year of Publication" data-col="published_year">${year}</div>
                <div class="col col-6" role="cell" data-label="Book Price" data-col="price">$${money(r.price)}</div>
                <div class="col col-7" role="cell" data-label="Action">
                  <button type="button" class="row-delete-btn" aria-label="Delete this book">Delete</button>
                </div>
              </li>`;
          }

          function render(rows){
            // Remove old data rows
            [...table.querySelectorAll('.table-row')].forEach(el => el.remove());

            if (!rows || rows.length === 0) {
              emptyMsg.style.display = 'block';
              return;
            }
            emptyMsg.style.display = 'none';

            const frag = document.createDocumentFragment();
            rows.forEach(r => {
              const wrapper = document.createElement('div');
              wrapper.innerHTML = rowHtml(r);
              frag.appendChild(wrapper.firstElementChild);
            });
            table.appendChild(frag);
          }

          function load(){
            fetch('MVC/Controller/BooksController.php?action=list_books')
              .then(r=>r.json())
              .then(j=>{
                if(!j.ok){ alert(j.error || 'Error'); return; }
                render(j.rows || []);
              })
              .catch(()=> alert('Network error'));
          }

          function delRow(li){
            const id = li?.getAttribute('data-id');
            const isbn = li?.getAttribute('data-isbn');
            if (!id && !isbn) return;

            const fd = new FormData();
            fd.append('action','delete_book');
            if (id) fd.append('id', id); else fd.append('isbn', isbn);

            fetch('MVC/Controller/BooksController.php', { method:'POST', body: fd })
              .then(r=>r.json())
              .then(j=>{
                if(!j.ok){ alert(j.error || 'Delete failed'); return; }
                li.classList.add('removing');
                setTimeout(()=>{
                  li.remove();
                  if(table.querySelectorAll('.table-row').length===0) emptyMsg.style.display='block';
                }, 150);
              })
              .catch(()=> alert('Network error'));
          }

          // Delegate click for delete buttons
          document.addEventListener('click', (e)=>{
            const btn = e.target.closest('.row-delete-btn');
            if (!btn) return;
            const row = btn.closest('.table-row');
            if (!row) return;
            if (confirm('Delete this book? This cannot be undone.')) delRow(row);
          });

          // Export refresh() so other scripts can reload the table after changes
          window.LIB_TABLE = { refresh: load };

          // Escape helper
          function escapeHtml(s){
            return String(s)
              .replaceAll('&','&amp;')
              .replaceAll('<','&lt;')
              .replaceAll('>','&gt;')
              .replaceAll('"','&quot;')
              .replaceAll("'",'&#39;');
          }

          // initial load
          load();
        })();
      </script>
    </section>
    <?php
  }
}

if (!debug_backtrace()) { render_librarian_table(); }
