(() => {
  const shell = document.querySelector('#app-shell');
  const dialog = document.querySelector('#account-settings-dialog');
  const chatDialog = document.querySelector('#chat-settings-dialog');
  const toast = document.querySelector('#toast');
  const themeChoices = [...document.querySelectorAll('input[name="theme-choice"]')];
  const themeColorInputs = Object.fromEntries([...document.querySelectorAll('[data-theme-color]')].map(input => [input.dataset.themeColor, input]));
  const customThemePreview = document.querySelector('[data-theme-preview="custom"]');
  const channelChoices = [...document.querySelectorAll('input[name="channel-position"]')];
  const serverChoices = [...document.querySelectorAll('input[name="server-position"]')];
  const dmCardSizeChoices = [...document.querySelectorAll('input[name="dm-card-size"]')];
  const memberSelect = document.querySelector('#show-members');
  const profileForm = document.querySelector('#profile-form');
  const profileSaveStatus = document.querySelector('#profile-save-status');
  const profileAvatarInput = document.querySelector('#profile-avatar');
  const profileAvatarPreview = document.querySelector('#profile-avatar-preview');
  const accountDataToggle = document.querySelector('#account-data-toggle');
  const accountDataDetails = document.querySelector('#account-data-details');
  const userStatusTrigger = document.querySelector('#user-status-trigger');
  const userStatusMenu = document.querySelector('#user-status-menu');
  const presenceOptions = [...document.querySelectorAll('[data-presence-option]')];
  const publicProfileDialog = document.querySelector('#public-profile-dialog');
  const dmChatStyleChoices = [...document.querySelectorAll('input[name="dm-chat-style"]')];
  const dmChatFontSizeChoices = [...document.querySelectorAll('input[name="dm-chat-font-size"]')];
  const dmChatFontFamilyChoices = [...document.querySelectorAll('input[name="dm-chat-font-family"]')];
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

  const defaultThemeColors = { background: '#10141c', surface: '#191b27', accent: '#ac9bdb', text: '#f7f4fc' };
  let preferences = { theme: 'discord', channelPosition: 'left', serverPosition: 'left', showMembers: true, dmCardSize: 'standard', colors: { ...defaultThemeColors } };
  try {
    const saved = JSON.parse(localStorage.getItem(storageKey) || '{}');
    if (['discord', 'midnight', 'light', 'custom'].includes(saved.theme)) preferences.theme = saved.theme;
    if (['left', 'right'].includes(saved.channelPosition)) preferences.channelPosition = saved.channelPosition;
    if (['left', 'right'].includes(saved.serverPosition)) preferences.serverPosition = saved.serverPosition;
    if (typeof saved.showMembers === 'boolean') preferences.showMembers = saved.showMembers;
    if (['standard', 'compact'].includes(saved.dmCardSize)) preferences.dmCardSize = saved.dmCardSize;
    if (saved.colors && typeof saved.colors === 'object') {
      Object.keys(defaultThemeColors).forEach(key => {
        if (typeof saved.colors[key] === 'string' && /^#[0-9a-f]{6}$/i.test(saved.colors[key])) preferences.colors[key] = saved.colors[key];
      });
    }
  } catch { /* Usa los valores iniciales cuando el almacenamiento del navegador no esté disponible. */ }

  function applyPreferences(save = false) {
    document.body.dataset.theme = preferences.theme;
    const customProperties = ['--app-bg', '--panel', '--panel-deep', '--raised', '--line', '--text', '--muted', '--accent', '--input'];
    if (preferences.theme === 'custom') {
      const colors = preferences.colors;
      document.body.style.setProperty('--app-bg', colors.background);
      document.body.style.setProperty('--panel', colors.surface);
      document.body.style.setProperty('--panel-deep', `color-mix(in srgb, ${colors.background} 58%, ${colors.surface})`);
      document.body.style.setProperty('--raised', `color-mix(in srgb, ${colors.surface} 78%, ${colors.accent})`);
      document.body.style.setProperty('--line', `color-mix(in srgb, ${colors.accent} 36%, ${colors.surface})`);
      document.body.style.setProperty('--text', colors.text);
      document.body.style.setProperty('--muted', `color-mix(in srgb, ${colors.text} 64%, ${colors.surface})`);
      document.body.style.setProperty('--accent', colors.accent);
      document.body.style.setProperty('--input', `color-mix(in srgb, ${colors.background} 58%, ${colors.surface})`);
      document.body.style.colorScheme = getLuminance(colors.background) > .58 ? 'light' : 'dark';
    } else {
      customProperties.forEach(property => document.body.style.removeProperty(property));
      document.body.style.removeProperty('color-scheme');
    }
    shell.dataset.channelPosition = preferences.channelPosition;
    shell.dataset.serverPosition = preferences.serverPosition;
    shell.dataset.dmCardSize = preferences.dmCardSize;
    shell.classList.toggle('members-hidden', !preferences.showMembers);
    themeChoices.forEach(input => {
      input.checked = input.value === preferences.theme;
      input.closest('[data-theme-card]')?.classList.toggle('is-selected', input.checked);
    });
    channelChoices.forEach(input => {
      input.checked = input.value === preferences.channelPosition;
      input.closest('[data-layout-card]')?.classList.toggle('is-selected', input.checked);
    });
    serverChoices.forEach(input => {
      input.checked = input.value === preferences.serverPosition;
      input.closest('[data-layout-card]')?.classList.toggle('is-selected', input.checked);
    });
    dmCardSizeChoices.forEach(input => {
      input.checked = input.value === preferences.dmCardSize;
      input.closest('[data-dm-card-choice]')?.classList.toggle('is-selected', input.checked);
    });
    Object.entries(themeColorInputs).forEach(([key, input]) => { input.value = preferences.colors[key]; });
    if (customThemePreview) {
      customThemePreview.style.setProperty('--preview-bg', preferences.colors.background);
      customThemePreview.style.setProperty('--preview-panel', preferences.colors.surface);
      customThemePreview.style.setProperty('--preview-accent', preferences.colors.accent);
      customThemePreview.style.setProperty('--preview-text', preferences.colors.text);
    }
    memberSelect.checked = preferences.showMembers;
    membersToggle.setAttribute('aria-pressed', String(preferences.showMembers));
    if (save) {
      try { localStorage.setItem(storageKey, JSON.stringify(preferences)); } catch { showToast('No se pudieron guardar los ajustes en este navegador.'); }
    }
  }

  function applyCurrentProfile(profile) {
    document.querySelectorAll('[data-current-username]').forEach(element => { element.textContent = profile.username; });
    document.querySelectorAll('[data-self-avatar]').forEach(avatar => {
      const image = avatar.querySelector('[data-avatar-image]');
      const initial = avatar.querySelector('[data-avatar-initial]');
      if (image) { image.src = profile.avatar; image.hidden = !profile.hasCustomAvatar; }
      if (initial) { initial.textContent = profile.initial; initial.hidden = profile.hasCustomAvatar; }
      else if (!profile.hasCustomAvatar && avatar.children.length === 0) avatar.textContent = profile.initial;
    });
    document.querySelectorAll('[data-current-profile-photo]').forEach(image => { image.src = profile.avatar; });
    if (profileAvatarPreview.src.startsWith('blob:')) URL.revokeObjectURL(profileAvatarPreview.src);
    profileAvatarPreview.src = profile.avatar;
    profileAvatarPreview.dataset.savedSrc = profile.avatar;
    const ownCard = document.querySelector('[data-current-profile-card]');
    if (ownCard) {
      const card = JSON.parse(ownCard.dataset.userProfile || '{}');
      ownCard.dataset.userProfile = JSON.stringify({ ...card, username: profile.username, initial: profile.initial, avatar: profile.avatar, hasCustomAvatar: profile.hasCustomAvatar, about: profile.about, activities: profile.activities });
      const name = ownCard.querySelector('[data-current-username]');
      if (name) name.textContent = profile.username;
    }
  }

  const presenceLabels = { online: 'En línea', away: 'Ausente', busy: 'No molestar', offline: 'Invisible' };
  function paintPresence(status) {
    const label = presenceLabels[status] || presenceLabels.online;
    const statusClass = status === 'online' ? '' : status;
    document.querySelectorAll('[data-current-presence-label]').forEach(element => { element.textContent = label; });
    document.querySelectorAll('[data-current-presence-dot]').forEach(dot => {
      dot.classList.remove('away', 'busy', 'offline');
      if (statusClass) dot.classList.add(statusClass);
    });
    userStatusTrigger.setAttribute('aria-label', `Cambiar estado (${label})`);
    presenceOptions.forEach(option => {
      const selected = option.dataset.presenceOption === status;
      option.classList.toggle('selected', selected);
      option.setAttribute('aria-checked', String(selected));
    });
  }
  function closeUserStatusMenu() {
    userStatusMenu.hidden = true;
    userStatusTrigger.setAttribute('aria-expanded', 'false');
  }
  async function choosePresence(status) {
    const csrf = document.querySelector('#csrf-token').value;
    presenceOptions.forEach(option => { option.disabled = true; });
    try {
      const response = await fetch('status-update.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8', Accept: 'application/json' },
        body: new URLSearchParams({ csrf, status }),
      });
      const result = await response.json();
      if (!response.ok || !result.ok) throw new Error(result.message || 'No se pudo actualizar el estado.');
      paintPresence(result.status);
      closeUserStatusMenu();
    } catch (error) {
      showToast(error.message || 'No se pudo actualizar el estado.');
    } finally {
      presenceOptions.forEach(option => { option.disabled = false; });
    }
  }
  userStatusTrigger.addEventListener('click', () => {
    const open = userStatusMenu.hidden;
    userStatusMenu.hidden = !open;
    userStatusTrigger.setAttribute('aria-expanded', String(open));
  });
  presenceOptions.forEach(option => option.addEventListener('click', () => choosePresence(option.dataset.presenceOption)));
  document.addEventListener('click', event => {
    if (!userStatusMenu.hidden && !userStatusMenu.contains(event.target) && !userStatusTrigger.contains(event.target)) closeUserStatusMenu();
  });
  document.addEventListener('keydown', event => { if (event.key === 'Escape' && !userStatusMenu.hidden) closeUserStatusMenu(); });

  function showPublicProfile(profile) {
    document.querySelector('#public-profile-name').textContent = profile.username || 'Miembro';
    document.querySelector('#public-profile-avatar').src = profile.avatar || '../Media/IMG/default-avatar.png';
    document.querySelector('#public-profile-avatar').alt = profile.username ? `Foto de perfil de ${profile.username}` : '';
    document.querySelector('#public-profile-about').textContent = profile.about || 'Este miembro todavía no ha escrito una presentación.';
    const joined = profile.joined ? new Date(profile.joined) : null;
    document.querySelector('#public-profile-member-since').textContent = joined && !Number.isNaN(joined.getTime()) ? `Miembro desde ${joined.toLocaleDateString('es-ES', { month: 'long', year: 'numeric' })}` : 'Miembro de Corebite';
    const list = document.querySelector('#public-profile-activities');
    list.replaceChildren();
    const activities = Array.isArray(profile.activities) ? profile.activities.slice(0, 3) : [];
    if (activities.length === 0) {
      const empty = document.createElement('li');
      empty.textContent = 'Todavía no hay notas de actividad.';
      list.append(empty);
    } else activities.forEach(activity => {
      const item = document.createElement('li');
      item.textContent = activity;
      list.append(item);
    });
    if (!publicProfileDialog.open) publicProfileDialog.showModal();
  }

  function getLuminance(hex) {
    const channels = hex.slice(1).match(/.{2}/g).map(value => parseInt(value, 16) / 255);
    const linear = channels.map(value => value <= .04045 ? value / 12.92 : ((value + .055) / 1.055) ** 2.4);
    return linear[0] * .2126 + linear[1] * .7152 + linear[2] * .0722;
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
    [[dmChatStyleChoices, settings.style], [dmChatFontSizeChoices, settings.fontSize], [dmChatFontFamilyChoices, settings.fontFamily]].forEach(([choices, selected]) => choices.forEach(input => {
      input.checked = input.value === selected;
      input.closest('.chat-choice')?.classList.toggle('is-selected', input.checked);
    }));
    dmChatTextColorInput.value = settings.textColor;
    document.querySelector('#dm-chat-text-color-value').textContent = settings.textColor.toUpperCase();
    document.querySelector('.chat-color-swatch').style.backgroundColor = settings.textColor;
  }
  function saveDmChatSettings() {
    dmChatSettings[activeContactId] = {
      style: dmChatStyleChoices.find(input => input.checked)?.value || defaultDmChatSettings.style,
      fontSize: dmChatFontSizeChoices.find(input => input.checked)?.value || defaultDmChatSettings.fontSize,
      fontFamily: dmChatFontFamilyChoices.find(input => input.checked)?.value || defaultDmChatSettings.fontFamily,
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
  accountDataToggle.addEventListener('click', () => {
    const reveal = accountDataDetails.hidden;
    accountDataDetails.hidden = !reveal;
    accountDataToggle.setAttribute('aria-expanded', String(reveal));
    accountDataToggle.querySelector('.account-data-toggle-label').textContent = reveal ? 'Ocultar datos de la cuenta' : 'Mostrar datos de la cuenta';
  });
  profileAvatarInput.addEventListener('change', () => {
    const file = profileAvatarInput.files?.[0];
    if (!file) return;
    if ((file.type && !['image/png', 'image/jpeg', 'image/webp'].includes(file.type)) || file.size > 5 * 1024 * 1024) {
      profileAvatarInput.value = '';
      profileAvatarPreview.src = profileAvatarPreview.dataset.savedSrc;
      profileSaveStatus.textContent = 'Elige una imagen PNG, JPG o WebP de hasta 5 MB.';
      return;
    }
    if (profileAvatarPreview.src.startsWith('blob:')) URL.revokeObjectURL(profileAvatarPreview.src);
    profileAvatarPreview.src = URL.createObjectURL(file);
    profileSaveStatus.textContent = '';
  });
  profileForm.addEventListener('submit', async event => {
    event.preventDefault();
    profileSaveStatus.textContent = 'Guardando perfil…';
    const submit = profileForm.querySelector('button[type="submit"]');
    submit.disabled = true;
    try {
      const response = await fetch(profileForm.action, { method: 'POST', body: new FormData(profileForm), headers: { Accept: 'application/json' } });
      const result = await response.json();
      if (!response.ok || !result.ok) throw new Error(result.message || 'No se pudo guardar el perfil.');
      applyCurrentProfile(result.profile);
      profileSaveStatus.textContent = result.message;
      showToast(result.message);
    } catch (error) {
      profileSaveStatus.textContent = error.message || 'No se pudo guardar el perfil. Inténtalo de nuevo.';
    } finally {
      submit.disabled = false;
    }
  });
  document.querySelectorAll('.profile-member-button').forEach(button => button.addEventListener('click', () => {
    try { showPublicProfile(JSON.parse(button.dataset.userProfile || '{}')); }
    catch { showToast('No se pudo cargar el perfil.'); }
  }));
  document.querySelector('#public-profile-close').addEventListener('click', () => publicProfileDialog.close());
  publicProfileDialog.addEventListener('click', event => { if (event.target === publicProfileDialog) publicProfileDialog.close(); });
  themeChoices.forEach(input => input.addEventListener('change', () => {
    if (!input.checked) return;
    preferences.theme = input.value;
    applyPreferences(true);
  }));
  Object.entries(themeColorInputs).forEach(([key, input]) => input.addEventListener('input', () => {
    preferences.theme = 'custom';
    preferences.colors[key] = input.value;
    applyPreferences(true);
  }));
  channelChoices.forEach(input => input.addEventListener('change', () => {
    if (!input.checked) return;
    preferences.channelPosition = input.value;
    applyPreferences(true);
  }));
  serverChoices.forEach(input => input.addEventListener('change', () => {
    if (!input.checked) return;
    preferences.serverPosition = input.value;
    applyPreferences(true);
  }));
  dmCardSizeChoices.forEach(input => input.addEventListener('change', () => {
    if (!input.checked) return;
    preferences.dmCardSize = input.value;
    applyPreferences(true);
  }));
  memberSelect.addEventListener('change', () => { preferences.showMembers = memberSelect.checked; applyPreferences(true); });
  [...dmChatStyleChoices, ...dmChatFontSizeChoices, ...dmChatFontFamilyChoices].forEach(control => control.addEventListener('change', () => {
    if (!control.checked) return;
    saveDmChatSettings();
  }));
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
    const name = button.dataset.dm;
    const contactProfile = button.dataset.contactProfile ? JSON.parse(button.dataset.contactProfile) : null;
    const statusKey = contactProfile?.status || button.dataset.statusKey || ({ 'Ausente': 'away', 'No molestar': 'busy' }[button.dataset.status] || 'online');
    const status = contactProfile?.statusLabel || button.dataset.status || presenceLabels[statusKey] || presenceLabels.online;
    activeContactId = button.dataset.contactId || name.toLocaleLowerCase('es');
    dmChatSettingsContact.textContent = name;
    applyDmChatSettings();
    document.querySelector('#dm-overview-name').textContent = name;
    document.querySelector('#dm-overview-status').textContent = status;
    const overviewDot = document.querySelector('.dm-overview-nameblock .presence-dot');
    overviewDot.classList.remove('away', 'busy', 'offline');
    if (statusKey !== 'online') overviewDot.classList.add(statusKey);
    document.querySelector('#dm-overview-description').textContent = contactProfile?.about || button.dataset.description || 'Este usuario todavía no ha escrito una presentación.';
    const overviewAvatar = document.querySelector('#dm-overview-avatar');
    overviewAvatar.src = contactProfile?.avatar || button.dataset.avatar || '../Media/IMG/default-avatar.png';
    overviewAvatar.alt = `Foto de perfil de ${name}`;
    const activityList = document.querySelector('#dm-activity-list'); activityList.replaceChildren();
    const activities = contactProfile ? contactProfile.activities : (button.dataset.activity || '').split('|').filter(Boolean);
    (Array.isArray(activities) ? activities : []).slice(0, 3).forEach(activity => { const item = document.createElement('li'); item.textContent = activity; activityList.append(item); });
    const conversation = document.querySelector('#dm-message-list'); conversation.replaceChildren();
    const conversationMessages = contactProfile ? [] : (dmMessages[name] || []);
    conversationMessages.forEach(([author, text, time]) => appendPrivateMessage(author, text, time));
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
