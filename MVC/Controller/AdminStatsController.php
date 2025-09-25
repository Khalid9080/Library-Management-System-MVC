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

/** Role helpers (same behavior as AuthController) */
function role_from_email_local(string $email): ?string {
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return null;
  [$local] = explode('@', $email, 2);
  $parts = explode('.', $local);
  $last = strtolower(end($parts));
  return in_array($last, ['member', 'librarian', 'admin'], true) ? $last : null;
}
function role_id(PDO $pdo, string $roleName): ?int {
  $st = $pdo->prepare('SELECT id FROM roles WHERE name = ? LIMIT 1');
  $st->execute([$roleName]);
  $r = $st->fetch(PDO::FETCH_ASSOC);
  return $r ? (int)$r['id'] : null;
}

/** ---------- COUNT USERS (for KPI) ---------- */
if ($action === 'count_users') {
  require_admin();

  // Map role names to IDs
  $rid = $pdo->query("SELECT id,name FROM roles")->fetchAll(PDO::FETCH_KEY_PAIR);
  $memberId    = isset($rid['member'])    ? (int)$rid['member']    : 1;
  $librarianId = isset($rid['librarian']) ? (int)$rid['librarian'] : 2;
  $adminId     = isset($rid['admin'])     ? (int)$rid['admin']     : 3;

  // Count members
  $stmtM = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role_id = ?");
  $stmtM->execute([$memberId]);
  $members = (int)$stmtM->fetchColumn();

  // Count librarians
  $stmtL = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role_id = ?");
  $stmtL->execute([$librarianId]);
  $librarians = (int)$stmtL->fetchColumn();

  // NEW: count admins
  $stmtA = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role_id = ?");
  $stmtA->execute([$adminId]);
  $admins = (int)$stmtA->fetchColumn();

  ok(['members'=>$members, 'librarians'=>$librarians, 'admins'=>$admins]);
}

/** ---------- LIST USERS (Users Directory) ---------- */
if ($action === 'list_users') {
  require_admin();

  $sql = "
    SELECT u.id, u.username, u.email, u.phone, r.name AS role
    FROM users u
    JOIN roles r ON r.id = u.role_id
    ORDER BY u.created_at DESC, u.id DESC
  ";
  $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
  ok(['rows' => $rows]);
}

/** ---------- GET USER BY EMAIL (Manage Users autofill) ---------- */
if ($action === 'get_user') {
  require_admin();

  $email = trim($_GET['email'] ?? '');
  if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    err('Valid email required', 422);
  }

  $st = $pdo->prepare("
    SELECT u.id, u.username, u.email, u.phone, u.role_id, r.name AS role
    FROM users u
    JOIN roles r ON r.id = u.role_id
    WHERE u.email = ?
    LIMIT 1
  ");
  $st->execute([$email]);
  $row = $st->fetch(PDO::FETCH_ASSOC);
  if (!$row) err('Not found', 404);

  ok(['user' => [
    'id'       => (int)$row['id'],
    'username' => $row['username'],
    'email'    => $row['email'],
    'phone'    => $row['phone'],
    'role'     => $row['role'],
    'role_id'  => (int)$row['role_id'],
  ]]);
}

/** ---------- UPDATE USER (Manage Users submit) ---------- */
if ($action === 'update_user') {
  require_admin();

  // You can target by id (preferred) or original_email
  $id             = isset($_POST['id']) ? (int)$_POST['id'] : 0;
  $original_email = trim($_POST['original_email'] ?? '');

  $email    = trim($_POST['email'] ?? '');
  $username = trim($_POST['username'] ?? '');
  $phone    = trim($_POST['phone'] ?? '');
  $roleName = strtolower(trim($_POST['role'] ?? ''));

  if (!$id && $original_email === '') err('id or original_email required', 422);
  if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) err('Valid email required', 422);
  if ($username === '') err('Username required', 422);
  if ($roleName === '' || !in_array($roleName, ['member','librarian','admin'], true)) err('Invalid role', 422);

  // Enforce your rule: email tag must match selected role
  $emailRole = role_from_email_local($email);
  if ($emailRole !== $roleName) {
    err('Email tag must match selected role (e.g. name.member@… ⇒ role = Member)', 422);
  }

  // Resolve role_id
  $rid = role_id($pdo, $roleName);
  if (!$rid) err('Role not configured', 500);

  // Ensure target user exists
  $findSql = $id
    ? "SELECT id FROM users WHERE id = ? LIMIT 1"
    : "SELECT id FROM users WHERE email = ? LIMIT 1";
  $find = $pdo->prepare($findSql);
  $find->execute([$id ?: $original_email]);
  $row = $find->fetch(PDO::FETCH_ASSOC);
  if (!$row) err('Target user not found', 404);
  $targetId = (int)$row['id'];

  try {
    $up = $pdo->prepare("
      UPDATE users
      SET username = ?, email = ?, phone = ?, role_id = ?
      WHERE id = ?
    ");
    $up->execute([$username, $email, $phone, $rid, $targetId]);

    ok(['updated' => true, 'id' => $targetId]);
  } catch (PDOException $e) {
    // duplicate email
    if (isset($e->errorInfo[1]) && (int)$e->errorInfo[1] === 1062) {
      err('Email already in use', 409);
    }
    err('Server error', 500);
  }
}

/** ---------- DELETE USER (Users Directory) ---------- */
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
    if (isset($e->errorInfo[1]) && (int)$e->errorInfo[1] === 1451) {
      err('Cannot delete: user is referenced by other records', 409);
    }
    err('Server error', 500);
  }
}

err('Unknown action', 404);
