// Public/JS/register.js
(function () {
  console.log('[register.js] ready');

  var form = document.getElementById('registerForm');
  var submitBtn = document.getElementById('submitBtn');

  if (!form) {
    console.error('[register.js] #registerForm not found');
    return;
  }

  var fields = {
    username: document.getElementById('username'),
    email: document.getElementById('email'),
    phone: document.getElementById('phone'),
    password: document.getElementById('password'),
    confirmPassword: document.getElementById('confirmPassword'),
  };

  var errors = {
    username: document.getElementById('usernameError'),
    email: document.getElementById('emailError'),
    phone: document.getElementById('phoneError'),
    password: document.getElementById('passwordError'),
    confirmPassword: document.getElementById('confirmPasswordError')
  };

  function on(el, ev, fn) { if (el) el.addEventListener(ev, fn); }

  var touched = {};

  // Password toggles
  var toggles = document.querySelectorAll('.password-toggle');
  for (var i = 0; i < toggles.length; i++) {
    (function (btn) {
      on(btn, 'click', function (e) {
        e.preventDefault();
        var id = btn.getAttribute('data-target');
        var input = id ? document.getElementById(id) : null;
        if (!input) return;
        input.type = (input.type === 'password') ? 'text' : 'password';
        var v = input.value;
        input.focus();
        try { input.setSelectionRange(v.length, v.length); } catch (_) { }
      });
    })(toggles[i]);
  }

  // Validators
  function isFilled(v) { return v != null && String(v).trim() !== ''; }

  function validate_username(v) {
    if (!isFilled(v)) return 'This field is required';
    if (String(v).trim().length < 2) return 'User name must be at least 2 characters';
    return true;
  }
  function validate_email(v) {
    if (!isFilled(v)) return 'This field is required';
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) return 'Enter a valid email address';
    return true;
  }
  function validate_phone(v) {
    if (!isFilled(v)) return 'This field is required';
    if (!/^[0-9()+\-\s]{7,20}$/.test(v)) return 'Enter a valid phone number';
    return true;
  }
  function validate_password(v) {
    if (!isFilled(v)) return 'This field is required';
    if (String(v).length < 8) return 'Password must be at least 8 characters';
    return true;
  }
  function validate_confirmPassword(v, all) {
    if (!isFilled(v)) return 'This field is required';
    if (v !== all.password) return "Passwords don't match";
    return true;
  }

  var validators = {
    username: validate_username,
    email: validate_email,
    phone: validate_phone,
    password: validate_password,
    confirmPassword: function (v) { return validate_confirmPassword(v, getValues()); }
  };

  function getValues() {
    return {
      username: fields.username ? fields.username.value : '',
      email: fields.email ? fields.email.value : '',
      phone: fields.phone ? fields.phone.value : '',
      password: fields.password ? fields.password.value : '',
      confirmPassword: fields.confirmPassword ? fields.confirmPassword.value : ''
    };
  }

  function showError(key, message) {
    var input = fields[key];
    var errEl = errors[key];
    if (!input) return;
    var group = input.closest ? input.closest('.form-group') : null;
    if (group) group.classList.add('error');
    if (errEl) {
      errEl.textContent = message;
      errEl.classList.add('show');
      errEl.style.display = 'block';
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
      errEl.style.display = '';
    }
    input.removeAttribute('aria-invalid');
  }

  function validateField(key, force) {
    var input = fields[key];
    if (!input) return true;
    if (!force && !touched[key]) return true;

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

  for (var key in fields) {
    if (!fields.hasOwnProperty(key)) continue;
    (function (k) {
      var input = fields[k];
      if (!input) return;

      on(input, 'blur', function () {
        touched[k] = true;
        validateField(k, true);
        if (k === 'password' && (fields.confirmPassword && (fields.confirmPassword.value || touched.confirmPassword))) {
          validateField('confirmPassword', true);
        }
      });

      on(input, 'input', function () {
        if (touched[k]) validateField(k, true);
        if (k === 'password' && touched.confirmPassword) {
          validateField('confirmPassword', true);
        }
      });
    })(key);
  }

  on(form, 'submit', function (e) {
    e.preventDefault();
    for (var k in fields) if (fields.hasOwnProperty(k)) touched[k] = true;
    if (!validateAll(true)) return;

    if (submitBtn) submitBtn.classList.add('loading');

    var formData = new FormData();
    formData.append('action', 'register');
    formData.append('username', fields.username.value.trim());
    formData.append('email', fields.email.value.trim());
    formData.append('phone', fields.phone.value.trim());
    formData.append('password', fields.password.value);
    formData.append('confirmPassword', fields.confirmPassword.value);

    // Derive root and endpoint WITHOUT using PHP in JS
    var root =
      (document.querySelector('base')?.href ||
       (window.APP_BASE_URL || document.baseURI))
        .replace(/index\.php.*$/, '')
        .replace(/\/+$/, '') + '/';

    formData.append('baseUrl', root);

    var endpoint = root + 'MVC/Controller/AuthController.php';

    fetch(endpoint, {
      method: 'POST',
      body: formData
    })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (submitBtn) submitBtn.classList.remove('loading');
        if (data.ok) {
          var ok = document.getElementById('successMessage');
          if (ok) ok.classList.add('show');
          window.location = data.redirect;
        } else {
          showError('email', data.error || 'Registration failed');
        }
      })
      .catch(function () {
        if (submitBtn) submitBtn.classList.remove('loading');
        showError('email', 'Network error. Try again.');
      });

     
  });

})();
