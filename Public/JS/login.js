// Public/JS/login.js
(function () {
  console.log('[login.js] ready');

  var form = document.getElementById('loginForm');
  var submitBtn = document.getElementById('submitBtn');

  if (!form) {
    console.error('[login.js] #loginForm not found');
    return;
  }

  var fields = {
    email: document.getElementById('email'),
    password: document.getElementById('password'),
  };

  var errors = {
    email: document.getElementById('emailError'),
    password: document.getElementById('passwordError'),
  };

  function on(el, ev, fn) { if (el) el.addEventListener(ev, fn); }

  // Track blur state so we don’t spam errors while typing first time
  var touched = {};

  // Password toggle(s)
  var toggles = document.querySelectorAll('.password-toggle');
  for (var i = 0; i < toggles.length; i++) {
    (function (btn) {
      on(btn, 'click', function (e) {
        e.preventDefault();
        var id = btn.getAttribute('data-target');
        var input = id ? document.getElementById(id) : null;
        if (!input) return;
        input.type = (input.type === 'password') ? 'text' : 'password';
        // keep caret at end
        var v = input.value;
        input.focus();
        try { input.setSelectionRange(v.length, v.length); } catch (_) {}
      });
    })(toggles[i]);
  }

  // Validators (required first, then specific rule)
  function isFilled(v) { return v != null && String(v).trim() !== ''; }

  function validate_email(v) {
    if (!isFilled(v)) return 'This field is required';
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) return 'Enter a valid email address';
    return true;
  }

  function validate_password(v) {
    if (!isFilled(v)) return 'This field is required';
    if (String(v).length < 8) return 'Password must be at least 8 characters';
    return true;
  }

  var validators = {
    email: validate_email,
    password: validate_password,
  };

  function showError(key, message) {
    var input = fields[key];
    var errEl = errors[key];
    if (!input) return;
    var group = input.closest ? input.closest('.form-group') : null;
    if (group) group.classList.add('error');
    if (errEl) {
      errEl.textContent = message;
      errEl.classList.add('show');
      errEl.style.display = 'block'; // ensure visible in layout if needed
    }
    input.setAttribute('aria-invalid', 'true');
  }

  function clearError(key) {
    var input = fields[key];
    var errEl = errors[key];
    if (!input) return;
    var group = input.closest ? input.closest('.form-group') : null;
    if (group) group.classList.remove('error');
    if (errEl) {
      errEl.textContent = '';
      errEl.classList.remove('show');
      // errEl.style.display = 'block'; // keep collapsed
    }
    input.setAttribute('aria-invalid', 'false');
  }

  function validateField(key, force) {
    var input = fields[key];
    if (!input) return true;
    if (!force && !touched[key]) return true; // don’t validate until first blur unless forced

    var v = input.value;
    var res = validators[key](v);
    if (res === true) {
      clearError(key);
      return true;
    } else {
      showError(key, res);
      return false;
    }
  }

  function validateAll(force) {
    var ok = true;
    for (var key in fields) {
      if (fields.hasOwnProperty(key)) {
        ok = validateField(key, force) && ok;
      }
    }
    return ok;
  }

  // Attach listeners
  for (var key in fields) {
    if (!fields.hasOwnProperty(key)) continue;
    (function (k) {
      var input = fields[k];
      if (!input) return;

      on(input, 'blur', function () {
        touched[k] = true;
        validateField(k, true);
      });

      on(input, 'input', function () {
        if (touched[k]) validateField(k, true);
      });
    })(key);
  }

  on(form, 'submit', function (e) {
    e.preventDefault();
    // mark all touched and validate
    for (var k in fields) if (fields.hasOwnProperty(k)) touched[k] = true;
    if (!validateAll(true)) return;

    // Simulate request/spinner then show success (replace with real fetch later)
    if (submitBtn) submitBtn.classList.add('loading');
    setTimeout(function () {
      if (submitBtn) submitBtn.classList.remove('loading');
      var ok = document.getElementById('successMessage');
      if (ok) ok.classList.add('show');
      // TODO: window.location = '<?= BASE_URL ?>index.php?page=dashboard';
    }, 900);
  });
})();
