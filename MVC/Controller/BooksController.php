<?php
declare(strict_types=1);

require_once __DIR__ . '/../../Database/database.php';
require_once __DIR__ . '/guard.php';

header('Content-Type: application/json; charset=utf-8');

function ok($d = [], $c = 200) { http_response_code($c); echo json_encode(['ok'=>true] + $d); exit; }
function err($m, $c = 400) { http_response_code($c); echo json_encode(['ok'=>false, 'error'=>$m]); exit; }

$action = $_POST['action'] ?? $_GET['action'] ?? null;
if (!$action) err('No action');

if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$pdo = db();

/* ---------- Helpers ---------- */
function require_librarian(): void {
  if (!is_logged_in() || user_role() !== 'librarian') err('Forbidden', 403);
}

/* ---------- Add Book ---------- */
if ($action === 'add_book') {
  require_librarian();
  $user = auth_user();
  $created_by = (int)$user['id'];

  $isbn   = trim($_POST['isbn'] ?? '');
  $title  = trim($_POST['title'] ?? '');
  $author = trim($_POST['author'] ?? '');
  $cat    = trim($_POST['category'] ?? '');
  // Accept "YYYY" or "YYYY-MM-DD" from date input
  $pubRaw = trim($_POST['publication_year'] ?? '');
  $year   = 0;
  if ($pubRaw !== '') {
    if (preg_match('/^\d{4}$/', $pubRaw)) $year = (int)$pubRaw;
    else $year = (int)substr($pubRaw, 0, 4);
  }
  $price  = (float)($_POST['price'] ?? -1);

  if ($isbn==='' || $title==='' || $author==='' || $cat==='' || $year<=0 || $price<0) {
    err('All fields are required', 422);
  }

  try {
    $st = $pdo->prepare("INSERT INTO books (isbn,title,author,category,published_year,price,created_by)
                         VALUES (?,?,?,?,?,?,?)");
    $st->execute([$isbn,$title,$author,$cat,$year,$price,$created_by]);

    $row = $pdo->query("SELECT id,isbn,title,author,category,published_year,price,created_at
                        FROM books WHERE isbn=".$pdo->quote($isbn)." LIMIT 1")->fetch();
    ok(['message'=>'Book added', 'row'=>$row], 201);
  } catch (PDOException $e) {
    if (isset($e->errorInfo[1]) && (int)$e->errorInfo[1] === 1062) err('ISBN already exists', 409);
    err('Server error', 500);
  }
}

/* ---------- List Books (for both tables) ---------- */
if ($action === 'list_books') {
  $q = trim($_GET['q'] ?? '');
  if ($q !== '') {
    $st = $pdo->prepare("SELECT id,isbn,title,author,category,published_year,price,created_at
                         FROM books
                         WHERE title LIKE CONCAT(?, '%') OR isbn = ?
                         ORDER BY created_at DESC");
    $st->execute([$q, $q]);
  } else {
    $st = $pdo->query("SELECT id,isbn,title,author,category,published_year,price,created_at
                       FROM books ORDER BY created_at DESC");
  }
  ok(['rows'=>$st->fetchAll()]);
}

/* ---------- Delete Book (by id or isbn) ---------- */
if ($action === 'delete_book') {
  require_librarian();
  $id   = (int)($_POST['id'] ?? 0);
  $isbn = trim($_POST['isbn'] ?? '');

  if (!$id && $isbn==='') err('Missing id or isbn', 422);

  try {
    if ($id) {
      $st = $pdo->prepare("DELETE FROM books WHERE id=?");
      $st->execute([$id]);
    } else {
      $st = $pdo->prepare("DELETE FROM books WHERE isbn=?");
      $st->execute([$isbn]);
    }

    if ($st->rowCount() === 0) err('Not found', 404);
    ok(['deleted'=>true]);
  } catch (PDOException $e) {
    // 1451: FK restrict
    if (isset($e->errorInfo[1]) && (int)$e->errorInfo[1] === 1451) {
      err('Cannot delete: this book is used in requests/history', 409);
    }
    err('Server error', 500);
  }
}

/* ---------- Get a book by ISBN (prefill helper) ---------- */
if ($action === 'get_book') {
  $isbn = trim($_GET['isbn'] ?? '');
  if ($isbn === '') err('ISBN required', 422);

  $st = $pdo->prepare("SELECT id,isbn,title,author,category,published_year,price,created_at,updated_at
                       FROM books WHERE isbn=? LIMIT 1");
  $st->execute([$isbn]);
  $row = $st->fetch();
  if (!$row) err('Not found', 404);
  ok(['row' => $row]);
}

/* ---------- Update Book (by ISBN; ISBN immutable) ---------- */
if ($action === 'update_book') {
  require_librarian();

  $isbn   = trim($_POST['isbn'] ?? '');
  $title  = trim($_POST['title'] ?? '');
  $author = trim($_POST['author'] ?? '');
  $cat    = trim($_POST['category'] ?? '');
  $pubRaw = trim($_POST['publication_year'] ?? '');
  $price  = $_POST['price'] ?? null;

  if ($isbn==='' || $title==='' || $author==='' || $cat==='' || $pubRaw==='' || $price===null) {
    err('All fields are required', 422);
  }

  // Year parse
  if (preg_match('/^\d{4}$/', $pubRaw)) $year = (int)$pubRaw;
  else $year = (int)substr($pubRaw, 0, 4);
  if ($year <= 0) err('Invalid publication year', 422);

  $price = (float)$price;
  if (!is_finite($price) || $price < 0) err('Invalid price', 422);

  // Ensure exists
  $st = $pdo->prepare("SELECT id FROM books WHERE isbn=? LIMIT 1");
  $st->execute([$isbn]);
  $row = $st->fetch(PDO::FETCH_ASSOC);
  if (!$row) err('Book with this ISBN does not exist', 404);

  try {
    $up = $pdo->prepare("UPDATE books
                         SET title=?, author=?, category=?, published_year=?, price=?, updated_at=CURRENT_TIMESTAMP
                         WHERE isbn=?");
    $up->execute([$title,$author,$cat,$year,$price,$isbn]);

    $fresh = $pdo->prepare("SELECT id,isbn,title,author,category,published_year,price,created_at,updated_at
                            FROM books WHERE isbn=? LIMIT 1");
    $fresh->execute([$isbn]);
    ok(['message'=>'Book updated', 'row'=>$fresh->fetch()], 200);
  } catch (PDOException $e) {
    err('Server error', 500);
  }
}

/* ---------- Count Books (global + by the current librarian) ---------- */
if ($action === 'count_books') {
  require_librarian();
  $user = auth_user();
  $uid  = (int)$user['id'];

  // global count
  $stAll = $pdo->query("SELECT COUNT(*) FROM books");
  $countAll = (int)$stAll->fetchColumn();

  // my count
  $stMine = $pdo->prepare("SELECT COUNT(*) FROM books WHERE created_by = ?");
  $stMine->execute([$uid]);
  $countMine = (int)$stMine->fetchColumn();

  ok(['count_all' => $countAll, 'count_mine' => $countMine], 200);
}



err('Unknown action', 404);
