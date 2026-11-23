(() => {
  const home = document.querySelector('#friends-view');
  const folders = [...home.querySelectorAll('[data-folder]')];
  const panels = [...home.querySelectorAll('[data-folder-panel]')];
  const bell = document.querySelector('#home-bell');
  const inbox = document.querySelector('#home-inbox');
  const feedback = document.querySelector('#inbox-feedback');
  let active = 'people';
  let version = 0;
  let displayed = '';
  let unread = new Set();
  let marking = false;
  const dmCounts = Object.fromEntries(Object.entries(JSON.parse(document.querySelector('#dm-examples').textContent)).map(([id, history]) => [id, history.length]));

  function openFolder(name) {
    active = name;
    for (const button of folders) {
      if (button.dataset.folder === name) button.setAttribute('aria-current', 'page');
      else button.removeAttribute('aria-current');
    }
    for (const panel of panels) panel.hidden = panel.dataset.folderPanel !== name;
    if (name === 'inbox') refresh();
  }
  folders.forEach(button => button.addEventListener('click', () => openFolder(button.dataset.folder)));
  home.addEventListener('home-open', () => openFolder('people'));
  bell.addEventListener('click', () => {
    document.querySelector('#friends-toggle').click();
    openFolder('inbox');
    folders[2].focus();
  });
  const servers = [...home.querySelectorAll('[data-directory-server]')];
  document.querySelector('#server-query').addEventListener('input', event => {
    const q = event.target.value.trim().toLocaleLowerCase('es');
    servers.forEach(button => { button.hidden = !button.textContent.toLocaleLowerCase('es').includes(q); });
    document.querySelector('#server-empty').hidden = servers.some(button => !button.hidden);
  });
  servers.forEach(button => button.addEventListener('click', () => {
    [...document.querySelectorAll('.server-icon')].find(icon => icon.dataset.server === button.dataset.directoryServer)?.click();
  }));

  async function request(action, extra = {}) {
    const response = await fetch('home-inbox.php', {
      method: 'POST',
      body: new URLSearchParams({action, dm_counts: JSON.stringify(dmCounts), csrf: document.querySelector('#csrf-token').value, ...extra})
    });
    if (!response.ok) throw new Error();
    return response.json();
  }
  function card(entry) {
    const row = document.createElement('article');
    row.className = 'friend-result';
    row.dataset.key = entry.key;
    const avatar = document.createElement('img');
    avatar.src = entry.avatar;
    avatar.alt = '';
    avatar.width = avatar.height = 40;
    const identity = document.createElement('div');
    const title = document.createElement('strong');
    title.textContent = entry.name;
    const caption = document.createElement('small');
    caption.textContent = entry.kind === 'request' ? 'Te ha enviado una solicitud de amistad' : 'Ha aceptado tu solicitud';
    identity.append(title, caption);
    row.append(avatar, identity);
    if (entry.kind === 'request') {
      const actions = document.createElement('div');
      actions.className = 'inbox-actions';
      for (const [action, label] of Object.entries({accept:'Aceptar', ignore:'Ignorar', reject:'Rechazar'})) {
        const button = document.createElement('button');
        button.type = 'button';
        button.textContent = label;
        button.addEventListener('click', async () => {
          for (const control of actions.children) control.disabled = true;
          try {
            const data = await request(action, {sender:entry.sender, key:entry.key});
            version++;
            render(data);
            feedback.textContent = {accept:'Solicitud aceptada.', ignore:'Solicitud ignorada.', reject:'Solicitud rechazada.'}[action];
          } catch {
            for (const control of actions.children) control.disabled = false;
            feedback.textContent = 'No se pudo actualizar la solicitud.';
            refresh();
          }
        });
        actions.append(button);
      }
      row.append(actions);
    }
    return row;
  }
  function render(data) {
    for (const [id, history] of Object.entries(data.histories)) dmCounts[id] = history.length;
    const contacts = new Set(data.contacts.map(contact => 'account-' + contact.id));
    for (const id of Object.keys(dmCounts)) if (!contacts.has(id)) delete dmCounts[id];
    home.dispatchEvent(new CustomEvent('dm-friends-updated', {detail: data}));
    unread = new Set(data.unread);
    bell.hidden = !unread.size;
    bell.querySelector('b').textContent = String(unread.size);
    bell.setAttribute('aria-label', 'Notificaciones sin leer: ' + unread.size);
    const signature = JSON.stringify(data.entries);
    if (signature !== displayed) {
      observer.disconnect();
      displayed = signature;
      inbox.replaceChildren(...data.entries.map(card));
      if (!data.entries.length) inbox.textContent = 'No tienes notificaciones.';
      for (const item of inbox.children) observer.observe(item);
    }
    const incoming = new Set(data.entries.filter(entry => entry.kind === 'request').map(entry => entry.sender));
    for (const button of home.querySelectorAll('[data-request-to]')) {
      const id = button.dataset.requestTo;
      const label = data.friends.includes(id) ? 'Amigos' : incoming.has(id) ? 'Solicitud recibida' : data.outgoing.includes(id) ? 'Solicitud enviada' : '';
      button.disabled = Boolean(label);
      button.textContent = label || 'Enviar solicitud de amistad';
    }
    markVisible();
  }
  async function markVisible() {
    if (marking || document.hidden || home.hidden || active !== 'inbox') return;
    const bounds = home.getBoundingClientRect();
    const keys = [...inbox.children].filter(row => {
      const rect = row.getBoundingClientRect();
      return unread.has(row.dataset.key) && rect.height > 0 && rect.bottom > bounds.top && rect.top < bounds.bottom;
    }).map(row => row.dataset.key);
    if (!keys.length) return;
    marking = true;
    try {
      const data = await request('seen', {keys:keys.join(',')});
      version++;
      render(data);
    } catch { feedback.textContent = 'No se pudo guardar la lectura.'; }
    finally { marking = false; }
  }
  const observer = new IntersectionObserver(markVisible, {root:home});
  async function refresh() {
    if (document.hidden) return;
    const token = ++version;
    try {
      const data = await request('list');
      if (token === version) render(data);
    } catch { if (active === 'inbox') feedback.textContent = 'No se pudieron cargar las notificaciones.'; }
  }
  refresh();
  setInterval(refresh, 8000);
  document.addEventListener('visibilitychange', refresh);
})();