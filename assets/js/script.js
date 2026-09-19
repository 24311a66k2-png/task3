/**
 * ApexPlanet 60-Day Full Stack Web Development Internship (PHP & MySQL)
 * Task 3: Backend Integration & CRUD
 * Script: assets/js/script.js
 */

document.addEventListener('DOMContentLoaded', () => {
  'use strict';

  // 1. Password Visibility Toggle (Show/Hide)
  const toggleButtons = document.querySelectorAll('.password-toggle-btn');
  toggleButtons.forEach((btn) => {
    btn.addEventListener('click', () => {
      const targetId = btn.getAttribute('data-target');
      const input = document.getElementById(targetId);
      if (!input) return;

      const icon = btn.querySelector('i');
      if (input.type === 'password') {
        input.type = 'text';
        if (icon) {
          icon.classList.remove('bi-eye');
          icon.classList.add('bi-eye-slash');
        }
        btn.setAttribute('aria-label', 'Hide password');
      } else {
        input.type = 'password';
        if (icon) {
          icon.classList.remove('bi-eye-slash');
          icon.classList.add('bi-eye');
        }
        btn.setAttribute('aria-label', 'Show password');
      }
    });
  });

  // 2. Password Strength Calculator
  const pwdInput = document.getElementById('password');
  const strengthBar = document.getElementById('strength-bar-fill');
  const strengthLabel = document.getElementById('strength-label');

  if (pwdInput && strengthBar && strengthLabel) {
    pwdInput.addEventListener('input', () => {
      const val = pwdInput.value;
      if (!val) {
        strengthBar.className = 'strength-meter-fill';
        strengthLabel.textContent = 'None';
        strengthLabel.style.color = 'var(--dc-text-muted)';
        return;
      }

      let score = 0;
      if (val.length >= 8) score++;
      if (/[a-z]/.test(val) && /[A-Z]/.test(val)) score++;
      if (/\d/.test(val)) score++;
      if (/[!@#$%^&*(),.?":{}|<>]/.test(val)) score++;
      if (val.length >= 12) score++;

      strengthBar.className = 'strength-meter-fill';

      if (score <= 2) {
        strengthBar.classList.add('weak');
        strengthLabel.textContent = 'Weak';
        strengthLabel.style.color = 'var(--dc-error)';
      } else if (score === 3 || score === 4) {
        strengthBar.classList.add('medium');
        strengthLabel.textContent = 'Medium';
        strengthLabel.style.color = 'var(--dc-warning)';
      } else {
        strengthBar.classList.add('strong');
        strengthLabel.textContent = 'Strong';
        strengthLabel.style.color = 'var(--dc-success)';
      }
    });
  }

  // 3. Confirm Password Match Checker
  const confirmInput = document.getElementById('confirm_password');
  const matchNotice = document.getElementById('password-match-status');

  const checkMatch = () => {
    if (!pwdInput || !confirmInput || !matchNotice) return;
    const pwd = pwdInput.value;
    const confirm = confirmInput.value;

    if (!confirm) {
      matchNotice.className = 'password-match-indicator';
      matchNotice.textContent = '';
      return;
    }

    if (pwd === confirm) {
      matchNotice.className = 'password-match-indicator match';
      matchNotice.innerHTML = '<i class="bi bi-check-circle-fill"></i> Passwords match.';
    } else {
      matchNotice.className = 'password-match-indicator mismatch';
      matchNotice.innerHTML = '<i class="bi bi-x-circle-fill"></i> Passwords do not match.';
    }
  };

  if (confirmInput && pwdInput) {
    confirmInput.addEventListener('input', checkMatch);
    pwdInput.addEventListener('input', () => {
      if (confirmInput.value) checkMatch();
    });
  }

  // 4. Live Image Preview on Profile Avatar Selection
  const imageInput = document.getElementById('profile_image');
  const avatarPreview = document.getElementById('avatar-preview');

  if (imageInput && avatarPreview) {
    imageInput.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (file) {
        // Validate file type on client for instant UX
        if (!file.type.startsWith('image/')) {
          alert('Please select a valid image file (JPG, PNG, WEBP).');
          imageInput.value = '';
          return;
        }
        // Validate size (2MB limit)
        if (file.size > 2 * 1024 * 1024) {
          alert('Selected image exceeds the 2MB size limit.');
          imageInput.value = '';
          return;
        }

        const reader = new FileReader();
        reader.onload = (event) => {
          avatarPreview.src = event.target.result;
        };
        reader.readAsDataURL(file);
      }
    });
  }

  // 5. Delete Confirmation Modal / Prompt
  const deleteForms = document.querySelectorAll('.confirm-delete-form');
  deleteForms.forEach((form) => {
    form.addEventListener('submit', (e) => {
      const userName = form.getAttribute('data-user-name') || 'this user';
      const confirmed = window.confirm(`Are you sure you want to permanently delete "${userName}"?\n\nThis action cannot be undone.`);
      if (!confirmed) {
        e.preventDefault();
      }
    });
  });

  // 6. Live AJAX Search for Admin User Table (Progressive Enhancement)
  const searchInput = document.getElementById('search');
  const userTableBody = document.getElementById('users-table-body');
  const roleSelect = document.getElementById('role');
  const statusSelect = document.getElementById('status');

  if (searchInput && userTableBody) {
    let debounceTimer = null;

    const performAjaxSearch = () => {
      const q = searchInput.value.trim();
      const role = roleSelect ? roleSelect.value : '';
      const status = statusSelect ? statusSelect.value : '';

      // Build dynamic endpoint path
      const currentUrl = new URL(window.location.href);
      const searchEndpoint = currentUrl.pathname.replace(/\/users\.php$/, '/search-users.php');
      const params = new URLSearchParams({ q, role, status });

      fetch(`${searchEndpoint}?${params.toString()}`)
        .then((res) => {
          if (!res.ok) throw new Error('Search network error');
          return res.json();
        })
        .then((data) => {
          if (!data || !data.success) return;
          if (data.users.length === 0) {
            userTableBody.innerHTML = `
              <tr>
                <td colspan="7" class="text-center py-5 text-secondary">
                  <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
                  No users found matching "${q.replace(/</g, '&lt;')}".
                </td>
              </tr>`;
            return;
          }

          let html = '';
          data.users.forEach((u) => {
            const roleBadge = u.role === 'admin' ? 'warning text-dark' : 'info';
            const statusBadge = u.status === 'active' ? 'success' : 'secondary';
            const idStr = String(u.id).padStart(4, '0');

            const actionButtons = u.is_self
              ? `<a href="${u.edit_url}" class="btn btn-outline-primary btn-sm py-1 px-2" title="Edit User"><i class="bi bi-pencil-square"></i></a>
                 <button class="btn btn-outline-secondary btn-sm py-1 px-2" disabled title="Cannot delete your active account"><i class="bi bi-lock"></i></button>`
              : `<a href="${u.edit_url}" class="btn btn-outline-primary btn-sm py-1 px-2" title="Edit User"><i class="bi bi-pencil-square"></i></a>
                 <form action="${u.delete_url}" method="POST" class="d-inline confirm-delete-form" data-user-name="${u.full_name}">
                   <input type="hidden" name="csrf_token" value="${u.csrf_token}">
                   <input type="hidden" name="id" value="${u.id}">
                   <button type="submit" class="btn btn-outline-danger btn-sm py-1 px-2" title="Delete User"><i class="bi bi-trash"></i></button>
                 </form>`;

            html += `
              <tr>
                <td class="font-monospace text-secondary">#${idStr}</td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <img src="${u.profile_image_url}" alt="Avatar" class="rounded-circle" width="34" height="34" style="object-fit: cover;">
                    <span class="fw-semibold text-light">${u.full_name}</span>
                  </div>
                </td>
                <td class="font-monospace small text-secondary">${u.email}</td>
                <td><span class="badge bg-${roleBadge}">${u.role.toUpperCase()}</span></td>
                <td><span class="badge bg-${statusBadge}">${u.status.toUpperCase()}</span></td>
                <td class="text-secondary small">${u.created_at}</td>
                <td class="text-end"><div class="d-inline-flex align-items-center gap-1">${actionButtons}</div></td>
              </tr>`;
          });

          userTableBody.innerHTML = html;

          // Re-bind delete confirmation handlers to dynamically rendered forms
          const newDeleteForms = userTableBody.querySelectorAll('.confirm-delete-form');
          newDeleteForms.forEach((form) => {
            form.addEventListener('submit', (e) => {
              const name = form.getAttribute('data-user-name') || 'this user';
              if (!window.confirm(`Are you sure you want to permanently delete "${name}"?\n\nThis action cannot be undone.`)) {
                e.preventDefault();
              }
            });
          });
        })
        .catch(() => {
          // Graceful fallback: do nothing, allow user to submit form normally
        });
    };

    searchInput.addEventListener('input', () => {
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(performAjaxSearch, 300);
    });

    if (roleSelect) roleSelect.addEventListener('change', performAjaxSearch);
    if (statusSelect) statusSelect.addEventListener('change', performAjaxSearch);
  }
});
