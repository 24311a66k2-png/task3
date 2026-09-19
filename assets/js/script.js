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
});
