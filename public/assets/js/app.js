if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/public/sw.js').catch(() => {});
  });
}

window.addEventListener('DOMContentLoaded', () => {
  const phoneInput = document.querySelector('input[name="phone"]');
  const dniInput = document.querySelector('input[name="dni"]');
  const toggleButtons = document.querySelectorAll('[data-password-toggle]');

  if (phoneInput) {
    const sanitizePhone = () => {
      phoneInput.value = phoneInput.value.replace(/[^0-9+\s()-]/g, '');
    };

    phoneInput.addEventListener('input', sanitizePhone);
    phoneInput.addEventListener('paste', () => {
      window.setTimeout(sanitizePhone, 0);
    });
  }

  if (dniInput) {
    const sanitizeDni = () => {
      dniInput.value = dniInput.value.replace(/[^0-9]/g, '');
    };

    dniInput.addEventListener('input', sanitizeDni);
    dniInput.addEventListener('paste', () => {
      window.setTimeout(sanitizeDni, 0);
    });
  }

  toggleButtons.forEach((button) => {
    button.addEventListener('click', () => {
      const field = button.closest('.password-field');
      const input = field ? field.querySelector('[data-password-input]') : null;
      if (!input) {
        return;
      }

      const isHidden = input.type === 'password';
      input.type = isHidden ? 'text' : 'password';
      button.setAttribute('aria-label', isHidden ? 'Ocultar contraseña' : 'Mostrar contraseña');
      button.textContent = isHidden ? '🙈' : '👁';
    });
  });
});
