<script>window.APP_BASE_URL = '<?= rtrim(BASE_URL, "/") ?>/';</script>
<link rel="stylesheet" href="<?= asset('Public/Style/login.css') ?>" />
<script src="<?= asset('Public/JS/login.js') ?>?v=<?= time() ?>" defer></script>

<div class="login-container">
  <!-- (rest of your markup unchanged) -->
  <div class="login-card" role="form" aria-labelledby="loginTitle">
    <div class="login-header">
      <h2 id="loginTitle">Sign in</h2>
      <p>Welcome back. Please enter your details.</p>
    </div>

    <form id="loginForm" novalidate>
      <div class="form-grid">
        <!-- Email -->
        <div class="form-group">
          <div class="input-wrapper">
            <input type="email" id="email" name="email" required placeholder=" " autocomplete="email" />
            <label for="email">Email</label>
          </div>
          <span class="error-message" id="emailError" aria-live="polite"></span>
        </div>

        <!-- Password -->
        <div class="form-group">
          <div class="input-wrapper">
            <input type="password" id="password" name="password" required placeholder=" " minlength="8"
              autocomplete="current-password" />
            <label for="password">Password</label>
            <button type="button" class="password-toggle" data-target="password"
              aria-label="Toggle password visibility">
              <span class="toggle-icon"></span>
            </button>
          </div>
          <span class="error-message" id="passwordError" aria-live="polite"></span>
        </div>

        <!-- Forgot password (placed above the Sign in button) -->
        <p class="meta-text" style="text-align:left;margin-top:-12px;">
          <a href="<?= BASE_URL ?>index.php?page=forgot" id="toForgot">Forgot password?</a>
        </p>


        <!-- Submit -->
        <button type="submit" class="login-btn" id="submitBtn">
          <span class="btn-text">Sign in</span>
          <span class="btn-loader"></span>
        </button>
      </div>
    </form>

    <p class="meta-text">Don’t have an account?
      <a href="<?= BASE_URL ?>index.php?page=register" id="toRegister">Create one</a>
    </p>

    <div class="success-message" id="successMessage" role="status" aria-live="polite">
      <div class="success-icon">✓</div>
      <h3>Signed in!</h3>
      <p>Redirecting to your dashboard…</p>
    </div>
  </div>
</div>

<!-- Small inline helper for password toggle (no extra JS file needed) -->
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