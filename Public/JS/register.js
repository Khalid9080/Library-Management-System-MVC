 
    (function(){
      const form = document.getElementById('registerForm');
      const submitBtn = document.getElementById('submitBtn');

      const fields = {
        username: document.getElementById('username'),
        email: document.getElementById('email'),
        phone: document.getElementById('phone'),
        role: document.getElementById('role'),
        password: document.getElementById('password'),
        confirmPassword: document.getElementById('confirmPassword')
      };

      const errors = {
        username: document.getElementById('usernameError'),
        email: document.getElementById('emailError'),
        phone: document.getElementById('phoneError'),
        role: document.getElementById('roleError'),
        password: document.getElementById('passwordError'),
        confirmPassword: document.getElementById('confirmPasswordError')
      };

      // Password toggles
      document.querySelectorAll('.password-toggle').forEach(btn => {
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          e.stopPropagation();
          const id = btn.getAttribute('data-target');
          let input = id ? document.getElementById(id) : btn.closest('.input-wrapper')?.querySelector('input');
          if (!input) return;
          const toType = input.type === 'password' ? 'text' : 'password';
          input.type = toType;
          btn.querySelector('.toggle-icon')?.classList.toggle('show-password', toType === 'text');
          const val = input.value; input.focus(); input.setSelectionRange(val.length, val.length);
        }, { passive: true });
      });

      // Simple validators
      const validators = {
        username: v => v.trim().length >= 2 || 'User name must be at least 2 characters',
        email: v => (/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) || 'Enter a valid email address'),
        phone: v => (/^[0-9()+\-\s]{7,20}$/.test(v) || 'Enter a valid phone number'),
        role: v => (v && v !== '' || 'Please choose a role'),
        password: v => (v.length >= 8 || 'Password must be at least 8 characters'),
        confirmPassword: (v, all) => (v === all.password || "Passwords don't match")
      };

      // Attach realtime validation
      Object.entries(fields).forEach(([key, input]) => {
        const handler = () => validateField(key);
        input.addEventListener('input', handler);
        if (input.tagName === 'SELECT') input.addEventListener('change', handler);
      });

      function validateField(key){
        const input = fields[key];
        const val = input.value;
        const res = validators[key](val, {
          username: fields.username.value,
          email: fields.email.value,
          phone: fields.phone.value,
          role: fields.role.value,
          password: fields.password.value,
          confirmPassword: fields.confirmPassword.value
        });
        const group = input.closest('.form-group');
        if (res !== true){
          group.classList.add('error');
          errors[key].textContent = res;
          errors[key].classList.add('show');
          return false;
        } else {
          group.classList.remove('error');
          errors[key].textContent = '';
          errors[key].classList.remove('show');
          return true;
        }
      }

      function validateAll(){
        let ok = true;
        for (const key of Object.keys(fields)){
          ok = validateField(key) && ok;
        }
        return ok;
      }

      form.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (!validateAll()) return;

        // Simulate loading & success
        submitBtn.classList.add('loading');
        await new Promise(r => setTimeout(r, 900));
        submitBtn.classList.remove('loading');
        document.getElementById('successMessage').classList.add('show');

        // Here you can POST to your backend
        // const payload = Object.fromEntries(new FormData(form).entries());
        // await fetch('/api/register', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload)});
      });
    })();
