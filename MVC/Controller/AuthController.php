<?php
declare(strict_types=1);

require_once __DIR__ . '/../../Database/database.php';
require_once __DIR__ . '/../Model/User.php';
require_once __DIR__ . '/guard.php';

// Always return JSON (and avoid stray output)
header('Content-Type: application/json; charset=utf-8');
ob_start();

/** ---------- JSON helpers ---------- */
function json_ok(array $data = [], int $code = 200): void {
  http_response_code($code);
  echo json_encode(['ok' => true] + $data);
  ob_end_flush();
  exit;
}
function json_err(string $message, int $code = 400, array $extra = []): void {
  http_response_code($code);
  echo json_encode(['ok' => false, 'error' => $message] + $extra);
  ob_end_flush();
  exit;
}

/** ---------- Role helpers ---------- */
/** Derive role name from local-part tag: name.role@domain (e.g. khalid.admin@gmail.com) */
function role_from_email(string $email): ?string {
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return null;
  [$local] = explode('@', $email, 2);
  $parts = explode('.', $local);
  $last = strtolower(end($parts));
  return in_array($last, ['member', 'librarian', 'admin'], true) ? $last : null;
}

/** Resolve role_id from DB by name */
function role_id(PDO $pdo, string $roleName): ?int {
  $stmt = $pdo->prepare('SELECT id FROM roles WHERE name = ? LIMIT 1');
  $stmt->execute([$roleName]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  return $row ? (int)$row['id'] : null;
}

/** ---------- Routing ---------- */
$action = $_POST['action'] ?? $_GET['action'] ?? null;
if (!$action) json_err('No action', 400);

// Base URL used by the frontend for redirect targets
$baseUrl = rtrim($_POST['baseUrl'] ?? '/', '/') . '/';

/** ---------- Register ---------- */
if ($action === 'register') {
  $username = trim($_POST['username'] ?? '');
  $email    = trim($_POST['email'] ?? '');
  $phone    = trim($_POST['phone'] ?? '');
  $password = $_POST['password'] ?? '';
  $confirm  = $_POST['confirmPassword'] ?? '';

  if ($username === '' || $email === '' || $phone === '' || $password === '' || $confirm === '') {
    json_err('All fields are required', 422);
  }
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_err('Enter a valid email address', 422);
  }
  if (strlen($password) < 8) {
    json_err('Password must be at least 8 characters', 422);
  }
  if ($password !== $confirm) {
    json_err("Passwords don't match", 422);
  }

  // Role from email tag (e.g. .admin / .member / .librarian)
  $roleName = role_from_email($email);
  if (!$roleName) {
    json_err('Email must include .member / .librarian / .admin before @', 422);
  }

  $pdo = db();
  $rid = role_id($pdo, $roleName);
  if (!$rid) json_err('Configured role not found', 500);

  try {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (username, email, phone, password_hash, role_id) VALUES (?,?,?,?,?)');
    $stmt->execute([$username, $email, $phone, $hash, $rid]);

    // Important: return JSON (the frontend will navigate using this URL)
    json_ok([
      // 'redirect' => $baseUrl . 'MVC/View/Authentication/login.php'
      'redirect' => $baseUrl . 'index.php?page=login'
    ]);
  } catch (PDOException $e) {
    // Duplicate email
    if (isset($e->errorInfo[1]) && (int)$e->errorInfo[1] === 1062) {
      json_err('Email already registered', 409);
    }
    json_err('Server error during registration', 500);
  }
}

/** ---------- Login ---------- */
if ($action === 'login') {
  $email    = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  if ($email === '' || $password === '') {
    json_err('Email and password are required', 422);
  }

  $pdo = db();
  $stmt = $pdo->prepare('SELECT id, username, email, password_hash, role_id FROM users WHERE email = ? LIMIT 1');
  $stmt->execute([$email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$user || !password_verify($password, $user['password_hash'])) {
    json_err('Invalid email or password', 401);
  }

  if (session_status() !== PHP_SESSION_ACTIVE) session_start();
  $_SESSION['user_id']   = (int)$user['id'];
  $_SESSION['role_id']   = (int)$user['role_id'];
  $_SESSION['username']  = $user['username'];
  $_SESSION['email']     = $user['email']; // Added this line optional
  $_SESSION['logged_in'] = true;

  // Go to the role-switching dashboard (PHP includes the correct dashboard by role)
  json_ok([
    'redirect' => $baseUrl . 'index.php?page=dashboard'
    //'redirect' => $baseUrl . 'MVC/View/Reusable_Components/dashboard.php'
  ]);
}

/** ---------- Logout ---------- */
if ($action === 'logout') {
  if (session_status() !== PHP_SESSION_ACTIVE) session_start();
  $_SESSION = [];
  if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
  }
  session_destroy();

  json_ok([
    // 'redirect' => $baseUrl . 'MVC/View/Authentication/login.php'
    'redirect' => $baseUrl . 'index.php?page=login'
  ]);
}

/** ---------- Forgot Password (Reset) ---------- */
if ($action === 'forgot_password') {
  $email    = trim($_POST['email'] ?? '');
  $new      = $_POST['newPassword'] ?? '';
  $confirm  = $_POST['confirmPassword'] ?? '';

  if ($email === '' || $new === '' || $confirm === '') {
    json_err('All fields are required', 422);
  }
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_err('Enter a valid email address', 422);
  }
  if (strlen($new) < 8) {
    json_err('Password must be at least 8 characters', 422);
  }
  if ($new !== $confirm) {
    json_err("Passwords don't match", 422);
  }

  $pdo = db();
  $stmt = $pdo->prepare('SELECT id, password_hash FROM users WHERE email = ? LIMIT 1');
  $stmt->execute([$email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$user) {
    // email not found
    json_err('Invalid email address', 404);
  }

  // Ensure the new password is not the same as the current password
  if (password_verify($new, $user['password_hash'])) {
    json_err('Please choose a different password than your current one', 422);
  }

  // Update the password
  $newHash = password_hash($new, PASSWORD_DEFAULT);
  $up = $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
  $up->execute([$newHash, (int)$user['id']]);

  json_ok([
    'redirect' => $baseUrl . 'index.php?page=login'
  ]);
}

/** ---------- Fallback ---------- */
json_err('Unknown action', 404);
