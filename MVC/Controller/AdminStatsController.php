<?php
declare(strict_types=1);

require_once __DIR__ . '/../../Database/database.php';
require_once __DIR__ . '/guard.php';

header('Content-Type: application/json; charset=utf-8');

function ok($d = [], $c = 200){ http_response_code($c); echo json_encode(['ok'=>true] + $d); exit; }
function err($m, $c = 400){ http_response_code($c); echo json_encode(['ok'=>false,'error'=>$m]); exit; }

if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$pdo = db();

$action = $_POST['action'] ?? $_GET['action'] ?? null;
if (!$action) err('No action');

/** Admin-only */
function require_admin(): void {
  if (!is_logged_in() || user_role() !== 'admin') err('Forbidden', 403);
}

/** ---------- COUNT USERS (for KPI) ---------- */
if ($action === 'count_users') {
  require_admin();

  // get role ids
  $rid = $pdo->query("SELECT id,name FROM roles")->fetchAll(PDO::FETCH_KEY_PAIR);
  $memberId    = isset($rid['member'])    ? (int)$rid['member']    : 1;
  $librarianId = isset($rid['librarian']) ? (int)$rid['librarian'] : 2;

  $stmtM = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role_id = ?");
  $stmtM->execute([$memberId]);
  $members = (int)$stmtM->fetchColumn();

  $stmtL = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role_id = ?");
  $stmtL->execute([$librarianId]);
  $librarians = (int)$stmtL->fetchColumn();

  ok(['members'=>$members, 'librarians'=>$librarians]);
}

/** ---------- LIST USERS (for Users Directory table) ---------- */
if ($action === 'list_users') {
  require_admin();

  $sql = "
    SELECT
      u.id,
      u.username,
      u.email,
      u.phone,
      r.name AS role
    FROM users u
    JOIN roles r ON r.id = u.role_id
    ORDER BY u.created_at DESC, u.id DESC
  ";
  $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
  ok(['rows' => $rows]);
}

/** ---------- DELETE USER (by id or email) ---------- */
if ($action === 'delete_user') {
  require_admin();

  $id    = isset($_POST['id']) ? (int)$_POST['id'] : 0;
  $email = trim($_POST['email'] ?? '');

  if ($id <= 0 && $email === '') err('id or email required', 422);

  try {
    if ($id > 0) {
      $st = $pdo->prepare("DELETE FROM users WHERE id = ?");
      $st->execute([$id]);
    } else {
      $st = $pdo->prepare("DELETE FROM users WHERE email = ?");
      $st->execute([$email]);
    }

    if ($st->rowCount() === 0) err('Not found', 404);
    ok(['deleted' => true]);
  } catch (PDOException $e) {
    // 1451: FK restriction (shouldn’t happen with your CASCADE/SET NULL, but guard anyway)
    if (isset($e->errorInfo[1]) && (int)$e->errorInfo[1] === 1451) {
      err('Cannot delete: user is referenced by other records', 409);
    }
    err('Server error', 500);
  }
}

err('Unknown action', 404);
