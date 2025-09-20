<?php
// index.php
if (session_status() === PHP_SESSION_NONE) session_start();

define('ROOT_PATH', __DIR__);
define('VIEW_PATH', ROOT_PATH . '/MVC/View');
define('PARTIALS_PATH', VIEW_PATH . '/Reusable_Components');

$baseUrlGuess = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
define('BASE_URL', $baseUrlGuess === '' ? '/' : $baseUrlGuess . '/');

$allowedPages = [
  'home'      => VIEW_PATH . '/Reusable_Components/main.php',
  'login'     => VIEW_PATH . '/Authentication/login.php',
  'register'  => VIEW_PATH . '/Authentication/register.php',
  'dashboard' => VIEW_PATH . '/Reusable_Components/dashboard.php',
  'forgot'    => VIEW_PATH . '/Authentication/forgot_password.php',
  // NEW:
  'librarian_add_book'    => VIEW_PATH . '/Dashboard/Librarian/add_book.php',
  'librarian_update_book' => VIEW_PATH . '/Dashboard/Librarian/update_book.php',
];

$page = $_GET['page'] ?? 'home';
$viewFile = $allowedPages[$page] ?? $allowedPages['home'];

// simple guard: protect dashboard
//($page === 'dashboard' && empty($_SESSION['user']))
if ($page === 'dashboard' && empty($_SESSION['logged_in'])) {
  header("Location: " . BASE_URL . "index.php?page=login");
  exit;
}

function asset($path) { return BASE_URL . ltrim($path, '/'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Libraria — Library Management System</title>
  <link rel="stylesheet" href="<?= asset('Public/Style/index.css') ?>?v=<?= time() ?>" />

</head>
<body class="page-<?= htmlspecialchars($page) ?>">
  <a href="#main" class="skip-link">Skip to content</a>
  <?php include PARTIALS_PATH . '/header.php'; ?>
  <main id="main">
    <?php include $viewFile; ?>
  </main>
  <?php include PARTIALS_PATH . '/footer.php'; ?>
</body>
</html>
