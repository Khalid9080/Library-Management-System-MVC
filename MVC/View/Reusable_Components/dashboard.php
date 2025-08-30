<?php
// MVC/View/Reusable_Components/dashboard.php
require_once __DIR__ . '/../../Controller/guard.php';

// Redirects to ../Authentication/login.php if not logged in
ensure_auth();

$user = auth_user(); // ['id','username','email','role_id','role']
$role = strtolower($user['role'] ?? 'member');

// Choose which dashboard partial to include
$map = [
  'member'    => __DIR__ . '/../Dashboard/MemberDashboard.php',
  'librarian' => __DIR__ . '/../Dashboard/LibrarianDashboard.php',
  'admin'     => __DIR__ . '/../Dashboard/AdminDashboard.php',
];

$include = $map[$role] ?? $map['member'];
?>
<section class="dashboard">
  <div class="container">
    <h2>Welcome, <?= htmlspecialchars($user['username'] ?? 'User', ENT_QUOTES, 'UTF-8') ?>!</h2>
    <p class="muted">Role: <strong><?= htmlspecialchars($role, ENT_QUOTES, 'UTF-8') ?></strong></p>
    <hr/>
    <?php include $include; ?>
  </div>
</section>
