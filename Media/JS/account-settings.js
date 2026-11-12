(() => {
  const form = document.querySelector('#account-appearance-form');
  if (!form) return;
  const controls = {
    theme: document.querySelector('#account-theme'),
    serverPosition: document.querySelector('#account-server-position'),
    channelPosition: document.querySelector('#account-channel-position'),
    showMembers: document.querySelector('#account-show-members'),
  };
  const key = 'corebite.appearance.v1';
  const status = document.querySelector('#appearance-save-status');
  const valid = (value, allowed, fallback) => allowed.includes(value) ? value : fallback;
  let saved = {};
  try { saved = JSON.parse(localStorage.getItem(key) || '{}'); } catch { saved = {}; }
  controls.theme.value = valid(saved.theme, ['discord', 'midnight', 'light'], 'discord');
  controls.serverPosition.value = valid(saved.serverPosition, ['left', 'right'], 'left');
  controls.channelPosition.value = valid(saved.channelPosition, ['left', 'right'], 'left');
  controls.showMembers.checked = saved.showMembers !== false;

  form.addEventListener('submit', event => {
    event.preventDefault();
    const preferences = {
      theme: controls.theme.value,
      serverPosition: controls.serverPosition.value,
      channelPosition: controls.channelPosition.value,
      showMembers: controls.showMembers.checked,
    };
    try {
      localStorage.setItem(key, JSON.stringify(preferences));
      status.textContent = 'Apariencia general guardada. Se aplicará al abrir el chat.';
    } catch {
      status.textContent = 'No se pudieron guardar los cambios en este navegador.';
    }
  });
})();
