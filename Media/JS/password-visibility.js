document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-password-toggle]').forEach((button) => {
    const field = button.closest('.password-field');
    const input = field?.querySelector('[data-password-input]');
    const eyeOpen = button.querySelector('[data-eye-open]');
    const eyeClosed = button.querySelector('[data-eye-closed]');
    if (!input || !eyeOpen || !eyeClosed) return;

    button.addEventListener('click', () => {
      const reveal = input.type === 'password';
      input.type = reveal ? 'text' : 'password';
      button.setAttribute('aria-pressed', String(reveal));
      button.setAttribute('aria-label', reveal ? 'Ocultar contraseña' : 'Mostrar contraseña');
      eyeOpen.hidden = reveal;
      eyeClosed.hidden = !reveal;
      input.focus({ preventScroll: true });
    });
  });
});
