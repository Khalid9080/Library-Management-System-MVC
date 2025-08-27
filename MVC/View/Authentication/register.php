<link rel="stylesheet" href="<?= BASE_URL ?>Public/Style/register.css" />
<script src="<?= BASE_URL ?>Public/JS/register.js" defer></script>

<div class="register-container">
  <div class="register-card" role="form" aria-labelledby="registerTitle">
    <div class="register-header">
      <h2 id="registerTitle">Create Account</h2>
      <p>Fill in your details to get started</p>
    </div>

    <form id="registerForm" novalidate>
      <div class="form-grid two">
        <div class="form-group">
          <div class="input-wrapper">
            <input type="text" id="username" name="username" required minlength="2" maxlength="40" placeholder=" " autocomplete="username" />
            <label for="username">User Name</label>
          </div>
          <span class="error-message" id="usernameError" aria-live="polite"></span>
        </div>

        <div class="form-group">
          <div class="input-wrapper">
            <input type="email" id="email" name="email" required placeholder=" " autocomplete="email" />
            <label for="email">Email</label>
          </div>
          <span class="error-message" id="emailError" aria-live="polite"></span>
        </div>
      </div>

      <div class="form-grid two">
        <div class="form-group">
          <div class="input-wrapper">
            <input type="tel" id="phone" name="phone" required placeholder=" " autocomplete="tel" pattern="^[0-9()+\-\s]{7,20}$" />
            <label for="phone">Phone Number</label>
          </div>
          <span class="error-message" id="phoneError" aria-live="polite"></span>
        </div>

        <div class="form-group select-wrapper">
          <div class="input-wrapper">
            <select id="role" name="role" required>
              <option value="" disabled selected hidden></option>
              <option value="Member">Member</option>
              <option value="Librarian">Librarian</option>
              <option value="Admin">Admin</option>
            </select>
            <label for="role">Register As</label>
          </div>
          <span class="select-caret" aria-hidden="true"></span>
          <span class="error-message" id="roleError" aria-live="polite"></span>
        </div>
      </div>

      <div class="form-grid two">
        <div class="form-group">
          <div class="input-wrapper">
            <input type="password" id="password" name="password" required placeholder=" " minlength="8" autocomplete="new-password" />
            <label for="password">Password</label>
            <button type="button" class="password-toggle" data-target="password" aria-label="Toggle password visibility">
              <span class="toggle-icon"></span>
            </button>
          </div>
          <span class="error-message" id="passwordError" aria-live="polite"></span>
        </div>

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
      </div>

      <button type="submit" class="register-btn" id="submitBtn">
        <span class="btn-text">Create Account</span>
        <span class="btn-loader"></span>
      </button>
    </form>

    <p class="meta-text">Already have an account? <a href="<?= BASE_URL ?>index.php?page=login" id="toLogin">Sign in</a></p>

    <div class="success-message" id="successMessage" role="status" aria-live="polite">
      <div class="success-icon">✓</div>
      <h3>Account created!</h3>
      <p>Welcome aboard. Redirecting to your dashboard…</p>
    </div>
  </div>
</div>
