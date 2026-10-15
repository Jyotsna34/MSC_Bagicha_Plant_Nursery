/**
 * Bagicha 2.0 - Universal Show/Hide Password Toggle System
 * Works consistently across Customer and Admin sections.
 */

(function() {
  function initPasswordToggles(container) {
    const root = container || document;
    const inputs = root.querySelectorAll('input[type="password"]');

    inputs.forEach(input => {
      if (input.dataset.pwdEnhanced === 'true') return;
      input.dataset.pwdEnhanced = 'true';

      const toggleBtn = document.createElement('button');
      toggleBtn.type = 'button';
      toggleBtn.className = 'btn btn-password-toggle';
      toggleBtn.setAttribute('tabindex', '-1');
      toggleBtn.setAttribute('aria-label', 'Show password');
      toggleBtn.setAttribute('title', 'Show password');
      toggleBtn.innerHTML = '<i class="bi bi-eye"></i>';

      toggleBtn.addEventListener('click', (e) => {
        e.preventDefault();
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        toggleBtn.innerHTML = isHidden ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
        const label = isHidden ? 'Hide password' : 'Show password';
        toggleBtn.setAttribute('aria-label', label);
        toggleBtn.setAttribute('title', label);
        input.focus();
      });

      const parentGroup = input.closest('.input-group');
      if (parentGroup) {
        // If inside an existing input-group, append the button
        parentGroup.appendChild(toggleBtn);
      } else {
        // Wrap input in an input-group to maintain perfect styling
        const wrapper = document.createElement('div');
        wrapper.className = 'input-group password-toggle-group';
        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(input);
        wrapper.appendChild(toggleBtn);
      }
    });
  }

  // Run on initial page load
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => initPasswordToggles());
  } else {
    initPasswordToggles();
  }

  // Auto-enhance when any Bootstrap modal is shown
  document.addEventListener('shown.bs.modal', (e) => {
    initPasswordToggles(e.target);
  });

  // Expose globally for dynamic components
  window.initPasswordToggles = initPasswordToggles;
})();
