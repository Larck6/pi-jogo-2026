document.addEventListener('DOMContentLoaded', () => {
  const accountButton = document.getElementById('openAccount');
  const accountPanel = document.getElementById('accountPanel');
  const accountBackdrop = document.getElementById('accountBackdrop');
  const closeAccount = document.getElementById('closeAccount');

  const openPanel = () => {
    if (!accountPanel || !accountBackdrop || !accountButton) return;

    accountPanel.classList.add('is-open');
    accountBackdrop.classList.add('is-open');
    accountPanel.setAttribute('aria-hidden', 'false');
    accountButton.setAttribute('aria-expanded', 'true');
  };

  const closePanel = () => {
    if (!accountPanel || !accountBackdrop || !accountButton) return;

    accountPanel.classList.remove('is-open');
    accountBackdrop.classList.remove('is-open');
    accountPanel.setAttribute('aria-hidden', 'true');
    accountButton.setAttribute('aria-expanded', 'false');
  };

  if (accountButton) {
    accountButton.addEventListener('click', (event) => {
      event.stopPropagation();
      openPanel();
    });
  }

  if (closeAccount) {
    closeAccount.addEventListener('click', closePanel);
  }

  if (accountBackdrop) {
    accountBackdrop.addEventListener('click', closePanel);
  }

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      closePanel();
    }
  });

  document.querySelectorAll('[data-toggle-password]').forEach((button) => {
    const input = document.querySelector(button.dataset.togglePassword);
    const icon = button.querySelector('i');

    if (!input) return;

    button.addEventListener('click', () => {
      const showingPassword = input.type === 'text';

      input.type = showingPassword ? 'password' : 'text';
      button.setAttribute('aria-label', showingPassword ? 'Mostrar senha' : 'Ocultar senha');

      if (icon) {
        icon.classList.toggle('fa-eye', showingPassword);
        icon.classList.toggle('fa-eye-slash', !showingPassword);
      }
    });
  });
});
