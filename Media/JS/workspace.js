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
  let activeContactId = null;
  const dmContacts = new Map();
  const dmList = document.querySelector('#dm-list');
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
  const mobileLayout = window.matchMedia('(max-width: 900px)');
  const accountPanel = userStatusTrigger.closest('.user-panel');
  const accountSidebar = accountPanel.parentElement;
  const accountRail = document.querySelector('.server-rail');
  const accountActions = accountPanel.querySelector('.account-actions');

  function placeAccountPanel() {
    closeUserStatusMenu();
    (mobileLayout.matches ? accountRail : accountSidebar).append(accountPanel);
    (mobileLayout.matches ? userStatusMenu : accountPanel).append(accountActions);
  }
  placeAccountPanel();
  mobileLayout.addEventListener('change', placeAccountPanel);
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
  const defaultDmChatSettings = { style: 'classic', fontSize: 'normal', fontFamily: 'system', textColor: null };
  function getDmChatSettings(contactId = activeContactId) {
    const saved = dmChatSettings[contactId] || {};
    return {
      style: ['classic', 'bubbles', 'compact'].includes(saved.style) ? saved.style : defaultDmChatSettings.style,
      fontSize: ['small', 'normal', 'large', 'xlarge'].includes(saved.fontSize) ? saved.fontSize : defaultDmChatSettings.fontSize,
      fontFamily: ['system', 'arial', 'verdana', 'trebuchet', 'georgia'].includes(saved.fontFamily) ? saved.fontFamily : defaultDmChatSettings.fontFamily,
      textColor: typeof saved.textColor === 'string' && (saved.customTextColor || saved.textColor.toLowerCase() !== '#f2f3f5') && /^#[0-9a-f]{6}$/i.test(saved.textColor) ? saved.textColor : defaultDmChatSettings.textColor,
    };
  }
  function applyDmChatSettings() {
    const settings = getDmChatSettings();
    dmChat.dataset.chatStyle = settings.style;
    dmChat.style.setProperty('--dm-chat-font-size', chatSizes[settings.fontSize]);
    dmChat.style.setProperty('--dm-chat-font-family', chatFamilies[settings.fontFamily]);
    dmChat.style.setProperty('--dm-chat-text-color', settings.textColor || 'var(--text)');
    [[dmChatStyleChoices, settings.style], [dmChatFontSizeChoices, settings.fontSize], [dmChatFontFamilyChoices, settings.fontFamily]].forEach(([choices, selected]) => choices.forEach(input => {
      input.checked = input.value === selected;
      input.closest('.chat-choice')?.classList.toggle('is-selected', input.checked);
    }));
    const effectiveColor = settings.textColor || getComputedStyle(document.body).getPropertyValue('--text').trim();
    dmChatTextColorInput.value = effectiveColor;
    document.querySelector('#dm-chat-text-color-value').textContent = effectiveColor.toUpperCase();
    document.querySelector('.chat-color-swatch').style.backgroundColor = effectiveColor;
  }
  function saveDmChatSettings(changeTextColor = false) {
    dmChatSettings[activeContactId] = {
      style: dmChatStyleChoices.find(input => input.checked)?.value || defaultDmChatSettings.style,
      fontSize: dmChatFontSizeChoices.find(input => input.checked)?.value || defaultDmChatSettings.fontSize,
      fontFamily: dmChatFontFamilyChoices.find(input => input.checked)?.value || defaultDmChatSettings.fontFamily,
      textColor: changeTextColor ? dmChatTextColorInput.value : getDmChatSettings().textColor,
      customTextColor: changeTextColor || Boolean(getDmChatSettings().textColor),
    };
    applyDmChatSettings();
    try { localStorage.setItem(dmChatStorageKey, JSON.stringify(dmChatSettings)); }
    catch { showToast('No se pudieron guardar los ajustes de este chat en el navegador.'); }
  }

  applyPreferences();
  applyDmChatSettings();
  function openSettings(tab) {
    if (!userStatusMenu.hidden) {
      closeUserStatusMenu();
      userStatusTrigger.focus();
    }
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
  dmChatTextColorInput.addEventListener('input', () => saveDmChatSettings(true));
  document.querySelector('#dm-chat-settings-open').addEventListener('click', () => {
    applyDmChatSettings();
    chatDialog.showModal();
  });
  document.querySelector('#chat-settings-close').addEventListener('click', () => chatDialog.close());
  membersToggle.addEventListener('click', () => { preferences.showMembers = !preferences.showMembers; applyPreferences(true); });

  const friendsToggle = document.querySelector('#friends-toggle');
  const friendView = document.querySelector('#friends-view');
  const friendQuery = document.querySelector('#friend-query');
  const feedback = document.querySelector('#friend-feedback');
  const codeHelp = document.querySelector('#friend-code-help');
  const codeTip = document.querySelector('#friend-code-tip');
  function setCodeHelp(open) {
    codeTip.hidden = !open;
    codeHelp.setAttribute('aria-expanded', String(open));
  }
  codeHelp.addEventListener('click', () => setCodeHelp(codeTip.hidden));
  document.addEventListener('click', event => {
    if (!codeHelp.contains(event.target) && !codeTip.contains(event.target)) setCodeHelp(false);
  });
  document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && !codeTip.hidden) {
      setCodeHelp(false);
      codeHelp.focus();
    }
  });
  let searchRevision = 0;
  async function filterFriends() {
    const revision = ++searchRevision;
    const query = friendQuery.value.trim().toLocaleLowerCase('es');
    const tagged = query.includes('#');
    let matchedId = null;
    if (tagged) {
      for (const card of friendView.querySelectorAll('.friend-results .friend-result')) card.hidden = true;
      if (!/^.+#[0-9]{6}$/.test(query)) {
        feedback.textContent = 'Escribe el nombre y los seis dígitos del código.';
        return;
      }
      feedback.textContent = 'Buscando…';
      try {
        const response = await fetch('friend-lookup.php', {
          method: 'POST',
          body: new URLSearchParams({tag:query, csrf:document.querySelector('#csrf-token').value})
        });
        if (!response.ok) throw new Error();
        matchedId = (await response.json()).id;
      } catch {
        if (revision === searchRevision) feedback.textContent = 'No se pudo completar la búsqueda.';
        return;
      }
      if (revision !== searchRevision) return;
    }
    let count = 0;
    for (const card of friendView.querySelectorAll('.friend-results .friend-result')) {
      const name = card.dataset.name.toLocaleLowerCase('es');
      const matches = tagged ? card.querySelector('[data-request-to]').dataset.requestTo === matchedId : name.includes(query);
      card.hidden = !query || !matches;
      if (!card.hidden) count++;
    }
    feedback.textContent = query ? (count ? '' : 'No se encontraron usuarios.') : 'Escribe un nombre para buscar.';
  }
  friendsToggle.addEventListener('click', () => {
    setView('friends');
    filterFriends();
    friendView.dispatchEvent(new Event('home-open'));
  });
  friendQuery.addEventListener('input', filterFriends);
  friendView.addEventListener('click', async event => {
    const button = event.target.closest('[data-request-to]');
    if (!button || button.disabled) return;
    button.disabled = true;
    try {
      const response = await fetch('friend-request.php', {
        method: 'POST',
        body: new URLSearchParams({target: button.dataset.requestTo, csrf: document.querySelector('#csrf-token').value})
      });
      if (!response.ok) throw new Error();
      const result = await response.json();
      button.textContent = result.friends ? 'Amigos' : result.incoming ? 'Solicitud recibida' : 'Solicitud enviada';
      if (result.incoming) button.closest('article').dataset.incoming = '1';
    } catch {
      button.disabled = false;
      feedback.textContent = 'No se pudo enviar la solicitud. Vuelve a intentarlo.';
    }
  });

  function setView(view) {
    const finding = view === 'friends';
    const isDm = finding || view === 'dm';
    friendView.hidden = !finding;
    friendsToggle.setAttribute('aria-pressed', String(finding));
    if (finding) closeDmSearch(true);
    if (!isDm) closeDmSearch(true);
    serverNavigation.hidden = isDm;
    dmNavigation.hidden = !isDm;
    serverChat.hidden = isDm;
    dmChat.hidden = finding || !isDm;
    serverMembers.hidden = isDm;
    shell.classList.toggle('dm-view', isDm);
    dmViewToggle.classList.toggle('selected', view === 'dm');
    dmViewToggle.setAttribute('aria-pressed', String(view === 'dm'));
    const isMobile = window.matchMedia('(max-width: 900px)').matches;
    shell.classList.toggle('mobile-channels-open', isMobile && !finding);
    channelsToggle.setAttribute('aria-expanded', String(isMobile));
    channelsToggle.setAttribute('aria-label', isMobile ? 'Volver al chat' : 'Abrir menú de canales');
  }
  dmViewToggle.addEventListener('click', () => {
    if (!dmContacts.size) {
      friendsToggle.click();
      showToast('Acepta una solicitud de amistad para iniciar un chat privado.');
      return;
    }
    if (!activeContactId) selectContact(dmList.firstElementChild);
    setView('dm');
  });
  document.querySelector('#dm-new').addEventListener('click', () => {
    if (dmContacts.size) document.querySelector('#dm-search').focus();
    else friendsToggle.click();
  });
  document.querySelectorAll('.server-icon').forEach(button => button.addEventListener('click', () => setView('server')));

  channelsToggle.addEventListener('click', () => {
    const open = shell.classList.toggle('mobile-channels-open');
    channelsToggle.setAttribute('aria-expanded', String(open));
    channelsToggle.setAttribute('aria-label', open ? 'Volver al chat' : 'Abrir menú de canales');
  });

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

  const dmExamples = JSON.parse(document.querySelector('#dm-examples').textContent);
  const dmTime = new Intl.DateTimeFormat('es-ES', {hour: '2-digit', minute: '2-digit'});
  const sentByContact = new Map();
  const minute = 60_000;
  const readKeyPrefix = 'corebite.dm-read.' + dmList.dataset.accountId + '.';
  const readThrough = new Map();

  function readTime(id) {
    if (!readThrough.has(id)) {
      let stored = 0;
      try { stored = Number(localStorage.getItem(readKeyPrefix + id)) || 0; } catch {}
      readThrough.set(id, stored);
    }
    return readThrough.get(id);
  }

  function updateDmSummary(button) {
    const id = button.dataset.contactId;
    const history = dmExamples[id] || [];
    const last = history.at(-1);
    const sent = sentByContact.get(id)?.at(-1);
    const sentIsLatest = sent && (!last || sent[3] >= last.sentAt);
    button.dataset.lastMessageAt = String(sentIsLatest ? sent[3] : last?.sentAt ?? -1);
    const preview = button.querySelector('.dm-preview');
    preview.textContent = sentIsLatest ? 'Tú: ' + sent[1] : last ? (last.own ? 'Tú: ' : '') + last.text : 'Sin mensajes';
    preview.title = preview.textContent;
    const since = readTime(id);
    const count = history.filter(message => !message.own && message.sentAt > since).length;
    const badge = button.querySelector('.dm-unread');
    badge.hidden = count === 0;
    badge.textContent = String(count);
    badge.setAttribute('aria-label', count === 1 ? '1 mensaje sin leer' : count + ' mensajes sin leer');
  }

  function sortDmContacts() {
    const ordered = [...dmList.children].sort((a, b) => Number(b.dataset.lastMessageAt) - Number(a.dataset.lastMessageAt));
    ordered.forEach((button, index) => {
      const current = dmList.children[index];
      if (current !== button) dmList.insertBefore(button, current);
    });
  }

  function markVisibleDmRead() {
    if (!activeContactId || document.hidden || !dmChat.getClientRects().length) return;
    const button = dmList.querySelector('.active');
    if (!button || button.dataset.contactId !== activeContactId) return;
    const latest = (dmExamples[activeContactId] || []).findLast(message => !message.own);
    if (!latest || latest.sentAt <= readTime(activeContactId)) return;
    readThrough.set(activeContactId, latest.sentAt);
    try { localStorage.setItem(readKeyPrefix + activeContactId, String(latest.sentAt)); } catch {}
    updateDmSummary(button);
  }

  new IntersectionObserver(markVisibleDmRead).observe(dmChat);
  document.addEventListener('visibilitychange', markVisibleDmRead);
  window.addEventListener('storage', event => {
    if (!event.key?.startsWith(readKeyPrefix)) return;
    const id = event.key.slice(readKeyPrefix.length);
    readThrough.delete(id);
    const button = dmList.querySelector('[data-contact-id="' + CSS.escape(id) + '"]');
    if (button) updateDmSummary(button);
  });

  function appendPrivateMessage(author, text, time, sentAt) {
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
    const list = document.querySelector('#dm-message-list');
    let card = list.lastElementChild;
    const canJoin = Number.isFinite(sentAt) && card?.dataset.author === author
      && sentAt >= Number(card.dataset.lastAt)
      && sentAt - Number(card.dataset.lastAt) < minute
      && sentAt - Number(card.dataset.firstAt) < 10 * minute;
    if (!canJoin) {
      card = document.createElement('div');
      card.className = 'dm-message-group';
      card.dataset.author = author;
      if (Number.isFinite(sentAt)) card.dataset.firstAt = String(sentAt);
      list.append(card);
    }
    if (Number.isFinite(sentAt)) card.dataset.lastAt = String(sentAt);
    card.append(row);
  }

  function selectContact(button) {
    const contactProfile = dmContacts.get(button.dataset.contactId);
    if (!contactProfile) return;
    setView('dm');
    closeDmSearch(true);
    dmList.querySelector('.active')?.classList.remove('active');
    button.classList.add('active');
    const name = contactProfile.username;
    const statusKey = contactProfile.status;
    const status = contactProfile.statusLabel;
    activeContactId = button.dataset.contactId;
    dmChatSettingsContact.textContent = name;
    applyDmChatSettings();
    document.querySelector('#dm-overview-name').textContent = name;
    document.querySelector('#dm-overview-status').textContent = status;
    const overviewDot = document.querySelector('.dm-overview-nameblock .presence-dot');
    overviewDot.classList.remove('away', 'busy', 'offline');
    if (statusKey !== 'online') overviewDot.classList.add(statusKey);
    document.querySelector('#dm-overview-description').textContent = contactProfile.about || 'Este usuario todavía no ha escrito una presentación.';
    const overviewAvatar = document.querySelector('#dm-overview-avatar');
    overviewAvatar.src = contactProfile.avatar;
    overviewAvatar.alt = `Foto de perfil de ${name}`;
    const activityList = document.querySelector('#dm-activity-list'); activityList.replaceChildren();
    const activities = contactProfile.activities;
    (Array.isArray(activities) ? activities : []).slice(0, 3).forEach(activity => { const item = document.createElement('li'); item.textContent = activity; activityList.append(item); });
    renderDmMessages();
    dmMessageSearch.value = ''; dmMessageSearch.dispatchEvent(new Event('input'));
    document.querySelector('#dm-message-input').placeholder = `Enviar mensaje a ${name}`;
    shell.classList.remove('mobile-channels-open');
    document.querySelector('#dm-message-scroll').scrollTop = document.querySelector('#dm-message-scroll').scrollHeight;
    requestAnimationFrame(markVisibleDmRead);
  }
  function renderDmMessages() {
    const name = dmContacts.get(activeContactId).username;
    const messages = (dmExamples[activeContactId] || []).map(message => [
      message.own ? 'me' : name, message.text, dmTime.format(message.sentAt), message.sentAt
    ]);
    messages.push(...(sentByContact.get(activeContactId) || []));
    messages.sort((a, b) => a[3] - b[3]);
    document.querySelector('#dm-message-list').replaceChildren();
    for (const args of messages) appendPrivateMessage(...args);
  }

  const dmSearch = document.querySelector('#dm-search');
  function filterDmContacts() {
    const query = dmSearch.value.trim().toLocaleLowerCase('es');
    let visible = 0;
    for (const button of dmList.children) {
      button.hidden = !dmContacts.get(button.dataset.contactId).username.toLocaleLowerCase('es').includes(query);
      if (!button.hidden) visible++;
    }
    const empty = document.querySelector('#dm-empty');
    empty.textContent = dmContacts.size ? 'Ningún amigo coincide con la búsqueda.' : 'Tus amigos aparecerán aquí cuando se acepte una solicitud.';
    empty.hidden = visible > 0;
  }
  let lastContactsSnapshot;
  function updateDmContacts(profiles, histories = {}) {
    const snapshot = JSON.stringify(profiles);
    if (snapshot === lastContactsSnapshot && !Object.keys(histories).length) return;
    Object.assign(dmExamples, histories);
    lastContactsSnapshot = snapshot;
    const allowed = new Set(profiles.map(profile => 'account-' + profile.id));
    for (const id of dmContacts.keys()) {
      if (allowed.has(id)) continue;
      dmContacts.delete(id);
      delete dmExamples[id];
      sentByContact.delete(id);
      dmList.querySelector(`[data-contact-id="${CSS.escape(id)}"]`)?.remove();
      if (id === activeContactId) {
        activeContactId = null;
        document.querySelector('#dm-message-list').replaceChildren();
        document.querySelector('#dm-message-input').value = '';
        if (chatDialog.open) chatDialog.close();
        if (!dmChat.hidden) setView('friends');
      }
    }
    for (const profile of profiles) {
      const id = 'account-' + profile.id;
      dmContacts.set(id, profile);
      let button = dmList.querySelector(`[data-contact-id="${CSS.escape(id)}"]`);
      if (!button) {
        button = document.querySelector('#dm-contact-template').content.firstElementChild.cloneNode(true);
        button.dataset.contactId = id;
        dmList.append(button);
      }
      button.querySelector('strong').textContent = profile.username;
      button.querySelector('small').textContent = profile.statusLabel;
      updateDmSummary(button);
      const initial = button.querySelector('[data-avatar-initial]');
      initial.textContent = profile.initial;
      initial.hidden = profile.hasCustomAvatar;
      const avatar = button.querySelector('img');
      if (avatar.getAttribute('src') !== profile.avatar) avatar.src = profile.avatar;
      avatar.hidden = !profile.hasCustomAvatar;
      button.querySelector('.presence-dot').className = 'presence-dot ' + profile.statusClass;
    }
    sortDmContacts();
    filterDmContacts();
    if (activeContactId && histories[activeContactId]) {
      renderDmMessages();
      dmMessageSearch.dispatchEvent(new Event('input'));
      markVisibleDmRead();
    }
  }
  dmList.addEventListener('click', event => {
    const button = event.target.closest('.dm-contact');
    if (button) selectContact(button);
  });
  dmSearch.addEventListener('input', filterDmContacts);
  friendView.addEventListener('dm-friends-updated', event => updateDmContacts(event.detail.contacts, event.detail.histories));
  updateDmContacts(JSON.parse(document.querySelector('#dm-friends').textContent));
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
    if (!text || dmChat.hidden || !dmContacts.has(activeContactId)) return;
    const sentAt = Date.now();
    const args = ['me', text, dmTime.format(sentAt), sentAt];
    if (!sentByContact.has(activeContactId)) sentByContact.set(activeContactId, []);
    sentByContact.get(activeContactId).push(args);
    updateDmSummary(dmList.querySelector('.active'));
    sortDmContacts();
    appendPrivateMessage(...args);
    dmMessageSearch.dispatchEvent(new Event('input'));
    input.value = '';
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
  setView('friends');
  filterFriends();
})();
