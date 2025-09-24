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

/* ---------- Guards ---------- */
function require_librarian(): void {
  if (!is_logged_in() || user_role() !== 'librarian') err('Forbidden', 403);
}
function require_member(): void {
  if (!is_logged_in() || user_role() !== 'member') err('Forbidden', 403);
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
   BUY HISTORY (for the librarian history table)
   =========================================================== */
if ($action === 'list_buy_history') {
  require_librarian();

  // Rows for the table
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
    /* If you want only approved in history, uncomment below line and remove others:
       WHERE br.status = 'approved'
    */
    ORDER BY br.requested_at DESC, br.id DESC, bri.id DESC
  ";

  $rows = $pdo->query($rowsSql)->fetchAll(PDO::FETCH_ASSOC);

  // Totals
  $totalsSql = "
    SELECT
      COUNT(DISTINCT br.member_id)                 AS total_members,
      COUNT(DISTINCT b.author)                     AS distinct_authors,
      COALESCE(SUM(bri.quantity), 0)               AS total_books,
      COALESCE(SUM(bri.quantity * bri.unit_price), 0) AS total_amount
    FROM book_request_items bri
    JOIN book_requests br ON br.id = bri.request_id
    JOIN books b          ON b.id  = bri.book_id
    /* Match the WHERE clause with rows if you filter status */
  ";
  $tot = $pdo->query($totalsSql)->fetch(PDO::FETCH_ASSOC) ?: [
    'total_members' => 0,
    'distinct_authors' => 0,
    'total_books' => 0,
    'total_amount' => 0.0
  ];

  // Backward-compatibility (if any older code expects total_quantity)
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

err('Unknown action', 404);
