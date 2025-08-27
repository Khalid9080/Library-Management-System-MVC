<?php
// index.php (in project root)

// Define base constants for robust paths/URLs
define('ROOT_PATH', __DIR__);                        
define('VIEW_PATH', ROOT_PATH . '/MVC/View');        
define('PARTIALS_PATH', VIEW_PATH . '/Reusable_Components'); // ✅ match your folder name exactly!

// If you deploy under a subfolder, set BASE_URL accordingly (e.g., '/Library-Management-System')
$baseUrlGuess = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
define('BASE_URL', $baseUrlGuess === '' ? '/' : $baseUrlGuess . '/');

// Simple whitelist router
$allowedPages = [
  'home'     => VIEW_PATH . '/Reusable_Components/main.php',
  'login'    => VIEW_PATH . '/Authentication/login.php',
  'register' => VIEW_PATH . '/Authentication/register.php',
];

$page = $_GET['page'] ?? 'home';
$viewFile = $allowedPages[$page] ?? $allowedPages['home'];

// Helper to build asset URLs safely
function asset($path) {
  return BASE_URL . ltrim($path, '/');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Library Management System — Modern Landing Page</title>
  <meta name="description" content="A clean, responsive landing page for an LMS." />
  <link rel="stylesheet" href="<?= asset('Public/Style/index.css') ?>" />
</head>
<body>
  <a href="#main" class="skip-link">Skip to content</a>

  <?php include PARTIALS_PATH . '/header.php'; ?>

  <main id="main">
    <?php include $viewFile; ?>
  </main>

  <?php include PARTIALS_PATH . '/footer.php'; ?>
</body>
</html>
