<?php
declare(strict_types=1);

require_once __DIR__ . '/../../Database/database.php';
require_once __DIR__ . '/guard.php';

header('Content-Type: application/json; charset=utf-8');

function ok($d = [], $c = 200) { http_response_code($c); echo json_encode(['ok'=>true] + $d); exit; }
function err($m, $c = 400) { http_response_code($c); echo json_encode(['ok'=>false, 'error'=>$m]); exit; }

if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$pdo = db();

$action = $_POST['action'] ?? $_GET['action'] ?? null;
if (!$action) err('No action');

/* ---------- Role helpers ---------- */
function require_member(): void {
  if (!is_logged_in() || user_role() !== 'member') err('Forbidden', 403);
}
function require_librarian(): void {
  if (!is_logged_in() || user_role() !== 'librarian') err('Forbidden', 403);
}

/* ---------- Create request (Member) ---------- */
if ($action === 'create_request') {
  require_member();
  $user = auth_user();
  $memberId = (int)$user['id'];

  // items is a JSON array: [{book_id: number, quantity?: number}, ...]
  $raw = $_POST['items'] ?? '[]';
  $items = json_decode($raw, true);
  if (!is_array($items) || count($items) === 0) err('No items', 422);

  // sanitize
  $bookIds = [];
  $lines   = [];
  foreach ($items as $it) {
    $bid = (int)($it['book_id'] ?? 0);
    $qty = (int)($it['quantity'] ?? 1);
    if ($bid <= 0 || $qty <= 0) continue;
    $bookIds[] = $bid;
    $lines[] = ['book_id'=>$bid, 'quantity'=>$qty];
  }
  if (!$lines) err('No valid items', 422);

  try {
    $pdo->beginTransaction();

    // Create header
    $st = $pdo->prepare("INSERT INTO book_requests (member_id, status) VALUES (?, 'pending')");
    $st->execute([$memberId]);
    $reqId = (int)$pdo->lastInsertId();

    // Fetch current prices for these books
    $in = implode(',', array_fill(0, count($bookIds), '?'));
    $stb = $pdo->prepare("SELECT id, price FROM books WHERE id IN ($in)");
    $stb->execute($bookIds);
    $prices = [];
    foreach ($stb as $r) $prices[(int)$r['id']] = (float)$r['price'];

    // Insert items
    $sti = $pdo->prepare("INSERT INTO book_request_items (request_id, book_id, quantity, unit_price) VALUES (?,?,?,?)");
    foreach ($lines as $ln) {
      $bid = (int)$ln['book_id'];
      if (!isset($prices[$bid])) { $pdo->rollBack(); err('Book not found: '.$bid, 404); }
      $qty = (int)$ln['quantity'];
      $sti->execute([$reqId, $bid, $qty, $prices[$bid]]);
    }

    $pdo->commit();
    ok(['message'=>'Request created', 'request_id'=>$reqId], 201);
  } catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    err('Server error creating request', 500);
  }
}

/* ---------- List requests for current member (cards) ---------- */
if ($action === 'list_member_requests') {
  if (!is_logged_in()) err('Forbidden', 403);
  $user = auth_user();
  if (user_role() !== 'member') err('Forbidden', 403);
  $memberId = (int)$user['id'];

  $sql = "
    SELECT
      br.id AS request_id, br.status, br.requested_at,
      u.username AS member_name,
      b.id AS book_id, b.isbn, b.title, b.author, b.category, b.published_year,
      bri.quantity, bri.unit_price
    FROM book_requests br
    JOIN users u ON u.id = br.member_id
    JOIN book_request_items bri ON bri.request_id = br.id
    JOIN books b ON b.id = bri.book_id
    WHERE br.member_id = ?
    ORDER BY br.requested_at DESC, br.id DESC, b.title ASC
  ";
  $st = $pdo->prepare($sql);
  $st->execute([$memberId]);
  ok(['rows' => $st->fetchAll()]);
}

/* ---------- List PENDING requests for librarian (cards) ---------- */
if ($action === 'list_pending_requests') {
  require_librarian();

  $sql = "
    SELECT
      br.id AS request_id, br.status, br.requested_at,
      mem.username AS member_name, mem.id AS member_id,
      b.id AS book_id, b.isbn, b.title, b.author, b.category, b.published_year,
      bri.quantity, bri.unit_price
    FROM book_requests br
    JOIN users mem ON mem.id = br.member_id
    JOIN book_request_items bri ON bri.request_id = br.id
    JOIN books b ON b.id = bri.book_id
    WHERE br.status = 'pending'
    ORDER BY br.requested_at DESC, br.id DESC, b.title ASC
  ";
  $st = $pdo->query($sql);
  ok(['rows' => $st->fetchAll()]);
}

/* ---------- NEW: For the current member, return a set of book_ids in PENDING requests ---------- */
if ($action === 'member_pending_book_ids') {
  if (!is_logged_in()) err('Forbidden', 403);
  if (user_role() !== 'member') err('Forbidden', 403);
  $user = auth_user();
  $memberId = (int)$user['id'];

  $sql = "
    SELECT DISTINCT bri.book_id
    FROM book_requests br
    JOIN book_request_items bri ON bri.request_id = br.id
    WHERE br.member_id = ? AND br.status = 'pending'
  ";
  $st = $pdo->prepare($sql);
  $st->execute([$memberId]);
  $ids = array_map(fn($r) => (int)$r['book_id'], $st->fetchAll());
  ok(['book_ids' => $ids]);
}

/* ---------- NEW: Librarian rejects a whole request (delete it) ---------- */
if ($action === 'reject_request') {
  require_librarian();
  $reqId = (int)($_POST['request_id'] ?? 0);
  if ($reqId <= 0) err('Invalid request_id', 422);

  // Option A (hard delete): remove header; items cascade delete by FK
  $del = $pdo->prepare("DELETE FROM book_requests WHERE id = ? LIMIT 1");
  $del->execute([$reqId]);

  if ($del->rowCount() === 0) err('Request not found', 404);

  ok(['deleted' => true, 'request_id' => $reqId]);
}

/* ---------- Librarian approves an entire request (all its items) ---------- */
if ($action === 'approve_request') {
  require_librarian();
  $reqId = (int)($_POST['request_id'] ?? 0);
  if ($reqId <= 0) err('Invalid request_id', 422);

  // ensure exists and still pending
  $chk = $pdo->prepare("SELECT id, status FROM book_requests WHERE id=? LIMIT 1");
  $chk->execute([$reqId]);
  $row = $chk->fetch(PDO::FETCH_ASSOC);
  if (!$row) err('Request not found', 404);
  if ($row['status'] !== 'pending') err('Already decided', 409);

  $user = auth_user();
  $librarianId = (int)$user['id'];

  $up = $pdo->prepare("
    UPDATE book_requests
      SET status='approved', decided_by=?, decided_at=CURRENT_TIMESTAMP
    WHERE id=? LIMIT 1
  ");
  $up->execute([$librarianId, $reqId]);

  ok(['approved' => true, 'request_id' => $reqId]);
}

/* ---------- Librarian Buy History (approved items, table-friendly) ---------- */
if ($action === 'list_buy_history') {
  require_librarian();

  $sql = "
    SELECT
      br.id                   AS request_id,
      br.requested_at,
      br.status,
      mem.username            AS requested_by,
      lib.username            AS librarian_name,
      b.isbn, b.title, b.author, b.category, b.published_year,
      bri.quantity,
      bri.unit_price,
      (bri.quantity * bri.unit_price) AS line_total
    FROM book_requests br
    JOIN users mem ON mem.id = br.member_id
    LEFT JOIN users lib ON lib.id = br.decided_by
    JOIN book_request_items bri ON bri.request_id = br.id
    JOIN books b ON b.id = bri.book_id
    WHERE br.status = 'approved'
    ORDER BY br.decided_at DESC, br.id DESC, b.title ASC
  ";
  $rows = $pdo->query($sql)->fetchAll();

  // Totals
  $totals = [
    'distinct_authors' => 0,
    'total_quantity'   => 0,
    'total_amount'     => 0.0,
  ];
  $authors = [];
  foreach ($rows as $r) {
    $authors[$r['author']] = true;
    $totals['total_quantity'] += (int)$r['quantity'];
    $totals['total_amount']   += (float)$r['line_total'];
  }
  $totals['distinct_authors'] = count($authors);

  ok(['rows' => $rows, 'totals' => $totals]);
}

/* ---------- Member: list approved books (cards for My Books) ---------- */
if ($action === 'list_member_approved_books') {
  if (!is_logged_in()) err('Forbidden', 403);
  if (user_role() !== 'member') err('Forbidden', 403);

  $user = auth_user();
  $memberId = (int)$user['id'];

  $sql = "
    SELECT
      br.id                   AS request_id,
      br.requested_at,
      br.decided_at,
      lib.username            AS librarian_name,
      b.isbn, b.title, b.author, b.category, b.published_year,
      bri.quantity,
      bri.unit_price,
      (bri.quantity * bri.unit_price) AS line_total
    FROM book_requests br
    LEFT JOIN users lib ON lib.id = br.decided_by
    JOIN book_request_items bri ON bri.request_id = br.id
    JOIN books b ON b.id = bri.book_id
    WHERE br.member_id = ?
      AND br.status = 'approved'
    ORDER BY br.decided_at DESC, br.id DESC, b.title ASC
  ";
  $st = $pdo->prepare($sql);
  $st->execute([$memberId]);
  ok(['rows' => $st->fetchAll()]);
}

/* ---------- NEW: For the current member, return book_ids that are LOCKED
      (= either pending OR approved) so they can't be requested again ---------- */
if ($action === 'member_locked_book_ids') {
  if (!is_logged_in()) err('Forbidden', 403);
  if (user_role() !== 'member') err('Forbidden', 403);
  $user = auth_user();
  $memberId = (int)$user['id'];

  $sql = "
    SELECT DISTINCT bri.book_id
    FROM book_requests br
    JOIN book_request_items bri ON bri.request_id = br.id
    WHERE br.member_id = ?
      AND br.status IN ('pending','approved')
  ";
  $st = $pdo->prepare($sql);
  $st->execute([$memberId]);
  $ids = array_map(fn($r) => (int)$r['book_id'], $st->fetchAll());
  ok(['book_ids' => $ids]);
}

err('Unknown action', 404);
