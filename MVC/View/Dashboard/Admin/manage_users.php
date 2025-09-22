<?php
// MVC/View/Dashboard/Admin/manage_users.php
// Partial view: injected inside the right-hand main content area.
// Left sidebar remains unchanged by dashboard.php.
?>
<link rel="stylesheet" href="<?= asset('Public/Style/admin-manage-users.css') ?>?v=<?= time() ?>" />

<section class="admin-manage-users" aria-labelledby="manageUsersTitle">
  <header class="amu-header">
    <h2 id="manageUsersTitle">Manage Users</h2>
    <p class="subtitle">Update a user’s basic info and role</p>
  </header>

  <div class="amu-card">
    <form class="amu-form" id="manageUsersForm" novalidate>
      <div class="amu-grid">
        <div class="amu-field">
          <label for="muEmail">Email <span class="req">*</span></label>
          <input type="email" id="muEmail" name="email" placeholder="name@example.com" required />
          <small class="error-message" id="muEmailError"></small>
        </div>

        <div class="amu-field">
          <label for="muUsername">Username <span class="req">*</span></label>
          <input type="text" id="muUsername" name="username" placeholder="e.g. Osman Goni" required />
          <small class="error-message" id="muUsernameError"></small>
        </div>

        <div class="amu-field">
          <label for="muPhone">Phone Number <span class="req">*</span></label>
          <input type="tel" id="muPhone" name="phone" placeholder="+8801XXXXXXXXX" inputmode="tel" required />
          <small class="error-message" id="muPhoneError"></small>
        </div>

        <div class="amu-field">
          <label for="muRole">Role <span class="req">*</span></label>
          <select id="muRole" name="role" required>
            <option value="">Select role</option>
            <option value="member">Member</option>
            <option value="librarian">Librarian</option>
          </select>
          <small class="error-message" id="muRoleError"></small>
        </div>
      </div>

      <div class="amu-actions">
        <a class="btn-ghost" href="<?= asset('index.php?page=dashboard') ?>">Cancel</a>
        <button type="submit" class="btn-primary">Update</button>
      </div>
    </form>
  </div>
</section>

<script src="<?= asset('Public/JS/admin-manage-users.js') ?>?v=<?= time() ?>"></script>
