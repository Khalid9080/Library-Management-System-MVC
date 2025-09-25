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

function require_librarian(): void {
  if (!is_logged_in() || user_role() !== 'librarian') err('Forbidden', 403);
}
function require_member(): void {
  if (!is_logged_in() || user_role() !== 'member') err('Forbidden', 403);
}
/** NEW: allow librarian OR admin (read-only endpoints) */
function require_staff(): void {
  if (!is_logged_in()) err('Forbidden', 403);
  $role = user_role();
  if ($role !== 'librarian' && $role !== 'admin') err('Forbidden', 403);
}

/* ===========================================================
   PENDING REQUESTS (for the librarian approvals page)
   =========================================================== */
if ($action === 'list_pending_requests') {
  require_librarian();

  $sql = "
    SELECT
      br.id            AS request_id,
      br.status,
      br.requested_at,
      u.username       AS member_name,
      b.isbn, b.title, b.author, b.category, b.published_year,
      bri.quantity, bri.unit_price
    FROM book_requests br
    JOIN users u ON u.id = br.member_id
    JOIN book_request_items bri ON bri.request_id = br.id
    JOIN books b ON b.id = bri.book_id
    WHERE br.status = 'pending'
    ORDER BY br.requested_at ASC, br.id ASC
  ";
  $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
  ok(['rows' => $rows]);
}

/* Approve request (entire request) */
if ($action === 'approve_request') {
  require_librarian();
  $rid = (int)($_POST['request_id'] ?? 0);
  if ($rid <= 0) err('request_id required', 422);

  $user = auth_user();
  $librarian_id = (int)$user['id'];

  $st = $pdo->prepare("UPDATE book_requests
                       SET status='approved', decided_by=?, decided_at=CURRENT_TIMESTAMP
                       WHERE id=? AND status='pending'");
  $st->execute([$librarian_id, $rid]);
  if ($st->rowCount() === 0) err('Not found or already decided', 404);

  ok(['approved' => true]);
}

/* Reject request (entire request) */
if ($action === 'reject_request') {
  require_librarian();
  $rid = (int)($_POST['request_id'] ?? 0);
  if ($rid <= 0) err('request_id required', 422);

  $user = auth_user();
  $librarian_id = (int)$user['id'];

  $st = $pdo->prepare("UPDATE book_requests
                       SET status='rejected', decided_by=?, decided_at=CURRENT_TIMESTAMP
                       WHERE id=? AND status='pending'");
  $st->execute([$librarian_id, $rid]);
  if ($st->rowCount() === 0) err('Not found or already decided', 404);

  ok(['rejected' => true]);
}

/* ===========================================================
   BUY HISTORY (used by Librarian AND Admin Transaction History)
   =========================================================== */
if ($action === 'list_buy_history') {
  // allow librarians OR admins to view this dataset
  require_staff();

  $rowsSql = "
    SELECT
      b.isbn, b.title, b.author, b.category,
      u.username               AS requested_by,
      br.requested_at,
      br.status,
      bri.quantity,
      lib.username             AS librarian_name,
      (bri.quantity * bri.unit_price) AS line_total
    FROM book_request_items bri
    JOIN book_requests br ON br.id = bri.request_id
    JOIN books b          ON b.id  = bri.book_id
    JOIN users u          ON u.id  = br.member_id
    LEFT JOIN users lib   ON lib.id = br.decided_by
    ORDER BY br.requested_at DESC, br.id DESC, bri.id DESC
  ";
  $rows = $pdo->query($rowsSql)->fetchAll(PDO::FETCH_ASSOC);

  // >>> CHANGED: Total amount should ONLY include APPROVED lines
  $totalsSql = "
    SELECT
      COUNT(DISTINCT br.member_id)                                  AS total_members,
      COUNT(DISTINCT b.author)                                      AS distinct_authors,
      COALESCE(SUM(bri.quantity), 0)                                AS total_books,
      COALESCE(SUM(CASE WHEN br.status = 'approved'
                        THEN bri.quantity * bri.unit_price
                        ELSE 0 END), 0)                             AS total_amount
    FROM book_request_items bri
    JOIN book_requests br ON br.id = bri.request_id
    JOIN books b          ON b.id  = bri.book_id
  ";
  $tot = $pdo->query($totalsSql)->fetch(PDO::FETCH_ASSOC) ?: [
    'total_members' => 0,
    'distinct_authors' => 0,
    'total_books' => 0,
    'total_amount' => 0.0
  ];
  $tot['total_quantity'] = $tot['total_books'];

  ok(['rows' => $rows, 'totals' => $tot]);
}

/* ===========================================================
   MEMBER "MY BOOKS" (approved items for the logged-in member)
   =========================================================== */
if ($action === 'list_member_approved_books') {
  require_member();

  $user = auth_user();
  $mid  = (int)$user['id'];

  $sql = "
    SELECT
      b.isbn, b.title, b.author, b.category, b.published_year,
      bri.quantity, bri.unit_price,
      lib.username    AS librarian_name,
      br.decided_at
    FROM book_request_items bri
    JOIN book_requests br ON br.id = bri.request_id
    JOIN books b          ON b.id  = bri.book_id
    LEFT JOIN users lib   ON lib.id = br.decided_by
    WHERE br.member_id = ? AND br.status = 'approved'
    ORDER BY br.decided_at DESC, br.id DESC, bri.id DESC
  ";
  $st = $pdo->prepare($sql);
  $st->execute([$mid]);
  $rows = $st->fetchAll(PDO::FETCH_ASSOC);

  ok(['rows' => $rows]);
}

/* ===========================================================
   MEMBER LOCKED BOOK IDS (pending + approved)
   =========================================================== */
if ($action === 'member_locked_book_ids') {
  require_member();

  $user = auth_user();
  $mid  = (int)$user['id'];

  $sql = "
    SELECT DISTINCT bri.book_id
    FROM book_request_items bri
    JOIN book_requests br ON br.id = bri.request_id
    WHERE br.member_id = ? AND br.status IN ('pending','approved')
  ";
  $st = $pdo->prepare($sql);
  $st->execute([$mid]);
  $ids = array_map('intval', array_column($st->fetchAll(PDO::FETCH_ASSOC), 'book_id'));

  ok(['book_ids' => $ids]);
}

/* ===========================================================
   MEMBER CREATE REQUEST
   =========================================================== */
if ($action === 'create_request') {
  require_member();

  $user = auth_user();
  $mid  = (int)$user['id'];

  $raw = $_POST['items'] ?? '[]';
  $items = json_decode($raw, true);
  if (!is_array($items) || empty($items)) err('No items', 422);

  $clean = [];
  foreach ($items as $it) {
    $bid = (int)($it['book_id'] ?? 0);
    $qty = (int)($it['quantity'] ?? 1);
    if ($bid <= 0 || $qty <= 0) err('Invalid items', 422);
    $clean[$bid] = ($clean[$bid] ?? 0) + $qty;
  }
  $bookIds = array_keys($clean);

  try {
    $pdo->beginTransaction();

    $in = implode(',', array_fill(0, count($bookIds), '?'));
    $params = array_merge([$mid], $bookIds);
    $checkSql = "
      SELECT DISTINCT bri.book_id
      FROM book_request_items bri
      JOIN book_requests br ON br.id = bri.request_id
      WHERE br.member_id = ? AND br.status IN ('pending','approved')
        AND bri.book_id IN ($in)
    ";
    $chk = $pdo->prepare($checkSql);
    $chk->execute($params);
    $already = array_map('intval', array_column($chk->fetchAll(PDO::FETCH_ASSOC), 'book_id'));
    if (!empty($already)) {
      $pdo->rollBack();
      err('Some books are already requested/approved: ' . implode(',', $already), 409);
    }

    $priceSql = "SELECT id, price FROM books WHERE id IN ($in)";
    $pstmt = $pdo->prepare($priceSql);
    $pstmt->execute($bookIds);
    $priceMap = [];
    foreach ($pstmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
      $priceMap[(int)$r['id']] = (float)$r['price'];
    }
    foreach ($bookIds as $bid) {
      if (!isset($priceMap[$bid])) {
        $pdo->rollBack();
        err("Book not found: $bid", 404);
      }
    }

    $hdr = $pdo->prepare("INSERT INTO book_requests (member_id, status) VALUES (?, 'pending')");
    $hdr->execute([$mid]);
    $rid = (int)$pdo->lastInsertId();

    $line = $pdo->prepare("
      INSERT INTO book_request_items (request_id, book_id, quantity, unit_price)
      VALUES (?,?,?,?)
    ");
    foreach ($bookIds as $bid) {
      $line->execute([$rid, $bid, $clean[$bid], $priceMap[$bid]]);
    }

    $pdo->commit();
    ok(['request_id' => $rid], 201);
  } catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    err('Server error', 500);
  }
}

/* ===========================================================
   MEMBER LIST OF REQUESTS
   =========================================================== */
if ($action === 'list_member_requests') {
  require_member();

  $user = auth_user();
  $mid  = (int)$user['id'];

  $sql = "
    SELECT
      br.id            AS request_id,
      br.status,
      br.requested_at,
      b.isbn, b.title, b.author, b.category, b.published_year,
      bri.quantity, bri.unit_price
    FROM book_request_items bri
    JOIN book_requests br ON br.id = bri.request_id
    JOIN books b          ON b.id  = bri.book_id
    WHERE br.member_id = ?
      AND br.status IN ('pending','approved','rejected')
    ORDER BY br.requested_at DESC, br.id DESC, bri.id DESC
  ";
  $st = $pdo->prepare($sql);
  $st->execute([$mid]);
  $rows = $st->fetchAll(PDO::FETCH_ASSOC);

  ok(['rows' => $rows]);
}

err('Unknown action', 404);
