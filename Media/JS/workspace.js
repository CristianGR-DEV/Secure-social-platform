(() => {
  const shell = document.querySelector('#app-shell');
  const dialog = document.querySelector('#account-settings-dialog');
  const chatDialog = document.querySelector('#chat-settings-dialog');
  const toast = document.querySelector('#toast');
  const themeSelect = document.querySelector('#theme-select');
  const channelSelect = document.querySelector('#channel-position');
  const serverSelect = document.querySelector('#server-position');
  const memberSelect = document.querySelector('#show-members');
  const dmChatStyleSelect = document.querySelector('#dm-chat-style');
  const dmChatFontSizeSelect = document.querySelector('#dm-chat-font-size');
  const dmChatFontFamilySelect = document.querySelector('#dm-chat-font-family');
  const dmChatTextColorInput = document.querySelector('#dm-chat-text-color');
  const dmChatSettingsContact = document.querySelector('#chat-settings-contact');
  const membersToggle = document.querySelector('#members-toggle');
  const channelsToggle = document.querySelector('#channels-toggle');
  const dmNavigation = document.querySelector('#dm-navigation');
  const serverNavigation = document.querySelector('#server-navigation');
  const serverChat = document.querySelector('#server-chat');
  const dmChat = document.querySelector('#dm-chat');
  const serverMembers = document.querySelector('#server-members');
  const dmViewToggle = document.querySelector('#dm-view-toggle');
  const dmSearchToggle = document.querySelector('#dm-search-toggle');
  const dmSearchPopover = document.querySelector('#dm-search-popover');
  const dmMessageSearch = document.querySelector('#dm-message-search');
  const dmConversationJump = document.querySelector('#dm-conversation-jump');
  const storageKey = 'corebite.appearance.v1';
  const dmChatStorageKey = 'corebite.dm-chat-settings.v1';
  let activeContactId = 'sofia';
  let dmChatSettings = {};
  try {
    const savedChats = JSON.parse(localStorage.getItem(dmChatStorageKey) || '{}');
    if (savedChats && typeof savedChats === 'object' && !Array.isArray(savedChats)) dmChatSettings = savedChats;
  } catch { /* Los ajustes de chat vuelven a sus valores iniciales. */ }
  let toastTimer;

  let preferences = { theme: 'discord', channelPosition: 'left', serverPosition: 'left', showMembers: true };
  try {
    const saved = JSON.parse(localStorage.getItem(storageKey) || '{}');
    if (['discord', 'midnight', 'light'].includes(saved.theme)) preferences.theme = saved.theme;
    if (['left', 'right'].includes(saved.channelPosition)) preferences.channelPosition = saved.channelPosition;
    if (['left', 'right'].includes(saved.serverPosition)) preferences.serverPosition = saved.serverPosition;
    if (typeof saved.showMembers === 'boolean') preferences.showMembers = saved.showMembers;
  } catch { /* Usa los valores iniciales cuando el almacenamiento del navegador no esté disponible. */ }

  function applyPreferences(save = false) {
    document.body.dataset.theme = preferences.theme;
    shell.dataset.channelPosition = preferences.channelPosition;
    shell.dataset.serverPosition = preferences.serverPosition;
    shell.classList.toggle('members-hidden', !preferences.showMembers);
    themeSelect.value = preferences.theme;
    channelSelect.value = preferences.channelPosition;
    serverSelect.value = preferences.serverPosition;
    memberSelect.checked = preferences.showMembers;
    membersToggle.setAttribute('aria-pressed', String(preferences.showMembers));
    if (save) {
      try { localStorage.setItem(storageKey, JSON.stringify(preferences)); } catch { showToast('No se pudieron guardar los ajustes en este navegador.'); }
    }
  }

  function showToast(message) {
    toast.textContent = message;
    toast.classList.add('visible');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toast.classList.remove('visible'), 2800);
  }

  const chatSizes = { small: '12px', normal: '14px', large: '16px', xlarge: '18px' };
  const chatFamilies = { system: 'system-ui, sans-serif', arial: 'Arial, sans-serif', verdana: 'Verdana, sans-serif', trebuchet: '"Trebuchet MS", sans-serif', georgia: 'Georgia, serif' };
  const defaultDmChatSettings = { style: 'classic', fontSize: 'normal', fontFamily: 'system', textColor: '#f2f3f5' };
  function getDmChatSettings(contactId = activeContactId) {
    const saved = dmChatSettings[contactId] || {};
    return {
      style: ['classic', 'bubbles', 'compact'].includes(saved.style) ? saved.style : defaultDmChatSettings.style,
      fontSize: ['small', 'normal', 'large', 'xlarge'].includes(saved.fontSize) ? saved.fontSize : defaultDmChatSettings.fontSize,
      fontFamily: ['system', 'arial', 'verdana', 'trebuchet', 'georgia'].includes(saved.fontFamily) ? saved.fontFamily : defaultDmChatSettings.fontFamily,
      textColor: typeof saved.textColor === 'string' && /^#[0-9a-f]{6}$/i.test(saved.textColor) ? saved.textColor : defaultDmChatSettings.textColor,
    };
  }
  function applyDmChatSettings() {
    const settings = getDmChatSettings();
    dmChat.dataset.chatStyle = settings.style;
    dmChat.style.setProperty('--dm-chat-font-size', chatSizes[settings.fontSize]);
    dmChat.style.setProperty('--dm-chat-font-family', chatFamilies[settings.fontFamily]);
    dmChat.style.setProperty('--dm-chat-text-color', settings.textColor);
    dmChatStyleSelect.value = settings.style;
    dmChatFontSizeSelect.value = settings.fontSize;
    dmChatFontFamilySelect.value = settings.fontFamily;
    dmChatTextColorInput.value = settings.textColor;
  }
  function saveDmChatSettings() {
    dmChatSettings[activeContactId] = {
      style: dmChatStyleSelect.value,
      fontSize: dmChatFontSizeSelect.value,
      fontFamily: dmChatFontFamilySelect.value,
      textColor: dmChatTextColorInput.value,
    };
    applyDmChatSettings();
    try { localStorage.setItem(dmChatStorageKey, JSON.stringify(dmChatSettings)); }
    catch { showToast('No se pudieron guardar los ajustes de este chat en el navegador.'); }
  }

  applyPreferences();
  applyDmChatSettings();
  function openSettings(tab) {
    document.querySelectorAll('[data-settings-panel]').forEach(panel => {
      const active = panel.dataset.settingsPanel === tab;
      panel.hidden = !active;
      panel.classList.toggle('active', active);
    });
    document.querySelectorAll('[data-settings-tab]').forEach(button => button.classList.toggle('active', button.dataset.settingsTab === tab));
    if (!dialog.open) dialog.showModal();
  }
  document.querySelectorAll('.settings-trigger').forEach(button => button.addEventListener('click', () => openSettings(button.dataset.settingsStart || 'personalization')));
  document.querySelectorAll('[data-settings-tab]').forEach(button => button.addEventListener('click', () => openSettings(button.dataset.settingsTab)));
  document.querySelectorAll('[data-settings-close]').forEach(button => button.addEventListener('click', () => dialog.close()));
  themeSelect.addEventListener('change', () => { preferences.theme = themeSelect.value; applyPreferences(true); });
  channelSelect.addEventListener('change', () => { preferences.channelPosition = channelSelect.value; applyPreferences(true); });
  serverSelect.addEventListener('change', () => { preferences.serverPosition = serverSelect.value; applyPreferences(true); });
  memberSelect.addEventListener('change', () => { preferences.showMembers = memberSelect.checked; applyPreferences(true); });
  [dmChatStyleSelect, dmChatFontSizeSelect, dmChatFontFamilySelect].forEach(control => control.addEventListener('change', saveDmChatSettings));
  dmChatTextColorInput.addEventListener('input', saveDmChatSettings);
  document.querySelector('#dm-chat-settings-open').addEventListener('click', () => {
    applyDmChatSettings();
    chatDialog.showModal();
  });
  document.querySelector('#chat-settings-close').addEventListener('click', () => chatDialog.close());
  membersToggle.addEventListener('click', () => { preferences.showMembers = !preferences.showMembers; applyPreferences(true); });

  function setView(view) {
    const isDm = view === 'dm';
    if (!isDm) closeDmSearch(true);
    serverNavigation.hidden = isDm;
    dmNavigation.hidden = !isDm;
    serverChat.hidden = isDm;
    dmChat.hidden = !isDm;
    serverMembers.hidden = isDm;
    shell.classList.toggle('dm-view', isDm);
    dmViewToggle.classList.toggle('selected', isDm);
    dmViewToggle.setAttribute('aria-pressed', String(isDm));
    const isMobile = window.matchMedia('(max-width: 900px)').matches;
    shell.classList.toggle('mobile-channels-open', isMobile);
    channelsToggle.setAttribute('aria-expanded', String(isMobile));
    channelsToggle.setAttribute('aria-label', isMobile ? 'Volver al chat' : 'Abrir menú de canales');
  }
  dmViewToggle.addEventListener('click', () => setView('dm'));
  document.querySelectorAll('.server-icon').forEach(button => button.addEventListener('click', () => setView('server')));

  channelsToggle.addEventListener('click', () => {
    const open = shell.classList.toggle('mobile-channels-open');
    channelsToggle.setAttribute('aria-expanded', String(open));
    channelsToggle.setAttribute('aria-label', open ? 'Volver al chat' : 'Abrir menú de canales');
  });

  const dmMessages = {
    Sofía: [['Sofía', '¡Hola! ¿Cómo va el proyecto?', '10:20'], ['me', 'Avanzando con el diseño de Corebite 😊', '10:22'], ['Sofía', '¡Qué bien! Cuando esté listo, enséñame cómo quedan los chats.', '10:24']],
    Rodrigo: [['Rodrigo', '¿Has visto el canal nuevo?', 'Ayer'], ['me', 'Sí, está quedando muy bien.', 'Ayer']],
    Alex: [['Alex', 'Perfecto, gracias 👌', 'Lun']],
    Noa: [['Noa', 'Te he compartido una imagen para el proyecto.', 'Dom']],
  };

  function closeDmSearch(clear = false) {
    dmSearchPopover.hidden = true;
    if (clear) {
      dmMessageSearch.value = '';
      dmMessageSearch.dispatchEvent(new Event('input'));
    }
  }
  function positionDmSearchPopover() {
    if (dmSearchPopover.hidden) return;
    const button = dmSearchToggle.getBoundingClientRect();
    const width = dmSearchPopover.offsetWidth;
    const height = dmSearchPopover.offsetHeight;
    let left = button.left - width - 9;
    if (left < 8) left = Math.min(window.innerWidth - width - 8, button.right + 9);
    const top = Math.max(8, Math.min(button.top + (button.height - height) / 2, window.innerHeight - height - 8));
    dmSearchPopover.style.left = Math.max(8, left) + 'px';
    dmSearchPopover.style.top = top + 'px';
  }
  dmSearchToggle.addEventListener('click', () => {
    if (!dmSearchPopover.hidden) {
      closeDmSearch(true);
      return;
    }
    dmSearchPopover.hidden = false;
    positionDmSearchPopover();
    dmMessageSearch.focus();
  });
  document.addEventListener('pointerdown', event => {
    if (!dmSearchPopover.hidden && !dmSearchPopover.contains(event.target) && !dmSearchToggle.contains(event.target)) closeDmSearch(true);
  });
  document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && !dmSearchPopover.hidden) {
      closeDmSearch(true);
      dmSearchToggle.focus();
    }
  });
  window.addEventListener('resize', positionDmSearchPopover);

  function appendPrivateMessage(author, text, time) {
    const row = document.createElement('article'); row.className = 'message';
    const avatar = document.createElement('div'); avatar.className = 'user-avatar message-avatar';
    const authorName = author === 'me' ? document.querySelector('.user-summary strong').textContent.trim() : author;
    avatar.textContent = author === 'me' ? authorName.slice(0, 1).toUpperCase() : author.slice(0, 1);
    const content = document.createElement('div'); content.className = 'message-body';
    const meta = document.createElement('div'); meta.className = 'message-meta';
    const name = document.createElement('strong'); name.textContent = authorName;
    const timestamp = document.createElement('time'); timestamp.textContent = time;
    const message = document.createElement('p'); message.textContent = text;
    meta.append(name, timestamp); content.append(meta, message); row.append(avatar, content);
    document.querySelector('#dm-message-list').append(row);
  }

  function selectContact(button) {
    closeDmSearch(true);
    document.querySelectorAll('.dm-contact').forEach(item => item.classList.remove('active'));
    button.classList.add('active');
    const name = button.dataset.dm; const status = button.dataset.status;
    activeContactId = button.dataset.contactId || name.toLocaleLowerCase('es');
    dmChatSettingsContact.textContent = name;
    applyDmChatSettings();
    document.querySelector('#dm-overview-name').textContent = name;
    document.querySelector('#dm-overview-status').textContent = status;
    const overviewDot = document.querySelector('.dm-overview-nameblock .presence-dot');
    overviewDot.classList.toggle('away', status === 'Ausente');
    overviewDot.classList.toggle('busy', status === 'No molestar');
    document.querySelector('#dm-overview-description').textContent = button.dataset.description;
    document.querySelector('#dm-overview-avatar').alt = `Foto de perfil de ${name}`;
    const activityList = document.querySelector('#dm-activity-list'); activityList.replaceChildren();
    (button.dataset.activity || '').split('|').filter(Boolean).forEach(activity => { const item = document.createElement('li'); item.textContent = activity; activityList.append(item); });
    const conversation = document.querySelector('#dm-message-list'); conversation.replaceChildren();
    (dmMessages[name] || []).forEach(([author, text, time]) => appendPrivateMessage(author, text, time));
    dmMessageSearch.value = ''; dmMessageSearch.dispatchEvent(new Event('input'));
    document.querySelector('#dm-message-input').placeholder = `Enviar mensaje a ${name}`;
    shell.classList.remove('mobile-channels-open');
    document.querySelector('#dm-message-scroll').scrollTop = document.querySelector('#dm-message-scroll').scrollHeight;
  }
  document.querySelectorAll('.dm-contact').forEach(button => button.addEventListener('click', () => selectContact(button)));
  document.querySelector('#dm-search').addEventListener('input', event => {
    const query = event.target.value.trim().toLocaleLowerCase('es'); let visible = 0;
    document.querySelectorAll('.dm-contact').forEach(button => {
      const match = button.dataset.dm.toLocaleLowerCase('es').includes(query);
      button.hidden = !match; if (match) visible += 1;
    });
    document.querySelector('#dm-empty').hidden = visible > 0;
  });
  dmMessageSearch.addEventListener('input', () => {
    const query = dmMessageSearch.value.trim().toLocaleLowerCase('es');
    document.querySelectorAll('#dm-message-list .message').forEach(row => {
      row.hidden = query !== '' && !row.textContent.toLocaleLowerCase('es').includes(query);
    });
  });
  dmConversationJump.addEventListener('click', () => {
    document.querySelector('#dm-message-scroll').scrollTop = document.querySelector('#dm-message-scroll').scrollHeight;
    document.querySelector('#dm-message-input').focus();
  });

  document.querySelector('#dm-message-form').addEventListener('submit', event => {
    event.preventDefault();
    const input = document.querySelector('#dm-message-input'); const text = input.value.trim();
    if (!text) return;
    const time = new Intl.DateTimeFormat('es-ES', { hour: '2-digit', minute: '2-digit' }).format(new Date());
    appendPrivateMessage('me', text, time); input.value = '';
    document.querySelector('#dm-message-scroll').scrollTop = document.querySelector('#dm-message-scroll').scrollHeight;
    showToast('Mensaje de muestra: todavía no se ha enviado ni guardado.');
  });

  document.querySelectorAll('.channel-link').forEach(button => button.addEventListener('click', () => {
    document.querySelectorAll('.channel-link').forEach(item => item.classList.remove('active'));
    button.classList.add('active');
    const name = button.dataset.label;
    document.querySelector('#current-channel-name').textContent = name;
    document.querySelector('#welcome-channel').textContent = name;
    document.querySelector('#message-input').placeholder = `Enviar mensaje a #${name}`;
    shell.classList.remove('mobile-channels-open');
    channelsToggle.setAttribute('aria-expanded', 'false');
    channelsToggle.setAttribute('aria-label', 'Abrir menú de canales');
  }));

  document.querySelectorAll('.server-icon').forEach(button => button.addEventListener('click', () => {
    document.querySelectorAll('.server-icon').forEach(item => item.classList.remove('selected'));
    button.classList.add('selected');
    document.querySelector('#server-name').textContent = button.dataset.server;
    showToast('Los canales y mensajes de este servidor de muestra no son definitivos.');
  }));
  document.querySelectorAll('[data-toast]').forEach(button => button.addEventListener('click', () => showToast(button.dataset.toast)));

  document.querySelector('#message-form').addEventListener('submit', event => {
    event.preventDefault();
    const input = document.querySelector('#message-input');
    const text = input.value.trim();
    if (!text) return;
    const row = document.createElement('article'); row.className = 'message';
    const avatar = document.createElement('div'); avatar.className = 'user-avatar message-avatar'; avatar.textContent = document.querySelector('.user-summary strong').textContent.trim().slice(0, 1).toUpperCase();
    const content = document.createElement('div'); content.className = 'message-body';
    const meta = document.createElement('div'); meta.className = 'message-meta';
    const name = document.createElement('strong'); name.textContent = document.querySelector('.user-summary strong').textContent.trim();
    const time = document.createElement('time'); time.textContent = new Intl.DateTimeFormat('es-ES', { hour: '2-digit', minute: '2-digit' }).format(new Date());
    const message = document.createElement('p'); message.textContent = text;
    meta.append(name, time); content.append(meta, message); row.append(avatar, content);
    document.querySelector('#message-list').append(row); input.value = '';
    document.querySelector('#message-scroll').scrollTop = document.querySelector('#message-scroll').scrollHeight;
    showToast('Mensaje de muestra: todavía no se ha enviado a otros usuarios.');
  });
})();
