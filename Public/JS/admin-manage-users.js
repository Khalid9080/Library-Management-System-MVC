// Public/JS/admin-manage-users.js
(function () {
  const form = document.getElementById("manageUsersForm");
  if (!form) return;

  const fields = {
    email: document.getElementById("muEmail"),
    username: document.getElementById("muUsername"),
    phone: document.getElementById("muPhone"),
    role: document.getElementById("muRole"),
  };

  const errs = {
    email: document.getElementById("muEmailError"),
    username: document.getElementById("muUsernameError"),
    phone: document.getElementById("muPhoneError"),
    role: document.getElementById("muRoleError"),
  };

  function showError(key, msg) {
    const wrap = fields[key]?.closest(".amu-field");
    if (wrap) wrap.classList.add("error");
    if (errs[key]) {
      errs[key].textContent = msg;
      errs[key].classList.add("show");
    }
  }

  function clearError(key) {
    const wrap = fields[key]?.closest(".amu-field");
    if (wrap) wrap.classList.remove("error");
    if (errs[key]) {
      errs[key].textContent = "";
      errs[key].classList.remove("show");
    }
  }

  const isFilled = (v) => v != null && String(v).trim() !== "";

  // Accepts +8801XXXXXXXXX or 01XXXXXXXXX (Bangladesh mobile)
  const phonePattern = /^(\+?8801\d{9}|01\d{9})$/;

  function validate() {
    let ok = true;

    // Email (required)
    clearError("email");
    if (!isFilled(fields.email?.value)) {
      showError("email", "This field is required");
      ok = false;
    }

    // Username (required)
    clearError("username");
    if (!isFilled(fields.username?.value)) {
      showError("username", "This field is required");
      ok = false;
    }

    // Role (required)
    clearError("role");
    if (!isFilled(fields.role?.value)) {
      showError("role", "This field is required");
      ok = false;
    }

    // Phone (required + pattern)
    clearError("phone");
    const p = fields.phone?.value || "";
    if (!isFilled(p)) {
      showError("phone", "This field is required");
      ok = false;
    } else if (!phonePattern.test(p)) {
      showError("phone", "Enter a valid phone like +8801XXXXXXXXX");
      ok = false;
    }

    return ok;
  }

  // Live-clear on input/change
  [["email","input"],["username","input"],["phone","input"],["role","change"]].forEach(([k,ev])=>{
    const el = fields[k]; if (!el) return;
    el.addEventListener(ev, () => {
      if (k === "phone") {
        const v = el.value;
        if (!isFilled(v)) {
          showError("phone", "This field is required");
        } else if (phonePattern.test(v)) {
          clearError("phone");
        } else {
          showError("phone", "Enter a valid phone like +8801XXXXXXXXX");
        }
        return;
      }
      if (el.hasAttribute("required")) {
        if (isFilled(el.value)) clearError(k);
      } else {
        clearError(k);
      }
    });
  });

  form.addEventListener("submit", (e) => {
    if (!validate()) {
      e.preventDefault();
      e.stopPropagation();
    }
  });
})();
