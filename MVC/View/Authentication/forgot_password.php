<script>window.APP_BASE_URL = '<?= rtrim(BASE_URL, "/") ?>/';</script>
<link rel="stylesheet" href="<?= asset('Public/Style/login.css') ?>" />
<link rel="stylesheet" href="<?= asset('Public/Style/forgot_password.css') ?>" />
<script src="<?= asset('Public/JS/forgot_password.js') ?>?v=<?= time() ?>" defer></script>

<div class="login-container">
  <div class="login-card" role="form" aria-labelledby="forgotTitle">
    <div class="login-header">
      <h2 id="forgotTitle">Reset password</h2>
      <p>Enter your registered email and a new password.</p>
    </div>

    <form id="forgotForm" novalidate>
      <div class="form-grid">
        <!-- Email -->
        <div class="form-group">
          <div class="input-wrapper">
            <input type="email" id="email" name="email" required placeholder=" " autocomplete="email" />
            <label for="email">Email</label>
          </div>
          <span class="error-message" id="emailError" aria-live="polite"></span>
        </div>

        <!-- New Password -->
        <div class="form-group">
          <div class="input-wrapper">
            <input type="password" id="newPassword" name="newPassword" required placeholder=" " minlength="8" autocomplete="new-password" />
            <label for="newPassword">New Password</label>
            <button type="button" class="password-toggle" data-target="newPassword" aria-label="Toggle password visibility">
              <span class="toggle-icon"></span>
            </button>
          </div>
          <span class="error-message" id="newPasswordError" aria-live="polite"></span>
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
          <div class="input-wrapper">
            <input type="password" id="confirmPassword" name="confirmPassword" required placeholder=" " minlength="8" autocomplete="new-password" />
            <label for="confirmPassword">Confirm Password</label>
            <button type="button" class="password-toggle" data-target="confirmPassword" aria-label="Toggle confirm password visibility">
              <span class="toggle-icon"></span>
            </button>
          </div>
          <span class="error-message" id="confirmPasswordError" aria-live="polite"></span>
        </div>

        <!-- Submit -->
        <button type="submit" class="login-btn" id="submitBtn">
          <span class="btn-text">Update password</span>
          <span class="btn-loader"></span>
        </button>
      </div>
    </form>

    <p class="meta-text">Remembered it?
      <a href="<?= BASE_URL ?>index.php?page=login" id="toLogin">Sign in</a>
    </p>

    <div class="success-message" id="successMessage" role="status" aria-live="polite">
      <div class="success-icon">✓</div>
      <h3>Password updated!</h3>
      <p>Redirecting to sign in…</p>
    </div>
  </div>
</div>

<!-- Inline helper for toggles (same as login) -->
<script>
  document.addEventListener('click', function (e) {
    if (e.target.closest('.password-toggle')) {
      const btn = e.target.closest('.password-toggle');
      const input = document.getElementById(btn.dataset.target);
      if (!input) return;
      input.type = input.type === 'password' ? 'text' : 'password';
    }
  });
</script>
