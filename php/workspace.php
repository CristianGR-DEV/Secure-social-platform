<?php
declare(strict_types=1);
require_once __DIR__ . '/includes.php';
$account = require_login();
$csrf = h($_SESSION['csrf']);
$username = h($account['username']);
$initial = h(mb_strtoupper(mb_substr($account['username'], 0, 1, 'UTF-8'), 'UTF-8'));
$profileAvatarSrc = profile_image_src($account);
$hasCustomAvatar = profile_image_file($account) !== null;
$currentPresence = account_presence($account);
$currentPresenceLabel = presence_label($currentPresence);
$currentPresenceClass = presence_class($currentPresence);
$publicMembers = with_accounts(static function (array &$accounts): array {
    $members = [];
    foreach ($accounts as $entry) {
        if (!is_array($entry) || !is_string($entry['id'] ?? null) || !is_string($entry['username'] ?? null)) continue;
        $members[] = [
            'id' => $entry['id'],
            'username' => $entry['username'],
            'initial' => mb_strtoupper(mb_substr($entry['username'], 0, 1, 'UTF-8'), 'UTF-8'),
            'avatar' => profile_image_src($entry),
            'hasCustomAvatar' => profile_image_file($entry) !== null,
            'about' => is_string($entry['about'] ?? null) ? $entry['about'] : '',
            'activities' => is_array($entry['recent_activities'] ?? null) ? array_values(array_filter($entry['recent_activities'], 'is_string')) : [],
            'joined' => is_string($entry['created_at'] ?? null) ? $entry['created_at'] : '',
            'presence' => account_presence($entry),
            'status' => account_presence($entry),
            'statusLabel' => presence_label(account_presence($entry)),
            'statusClass' => presence_class(account_presence($entry)),
        ];
    }
    return $members;
});
$servers = [
    ['name' => 'Corebite', 'badge' => 'C'],
    ['name' => 'Comunidad', 'badge' => 'C'],
    ['name' => 'Gaming', 'badge' => 'G'],
];
$channels = [
    'INFORMACIÓN' => [['rules', 'Reglas'], ['news', 'Novedades']],
    'GENERAL' => [['general', 'general'], ['memes', 'memes'], ['media', 'imágenes-y-vídeos']],
    'VOZ' => [['voice-general', 'Voz general'], ['gaming-room', 'Partida en grupo']],
];
$messages = [
    ['name' => 'Luna', 'initial' => 'L', 'color' => 'lavender', 'time' => '10:42', 'text' => '¡Bienvenidos al servidor! Este es el canal general para charlar y compartir ideas.'],
    ['name' => 'Marcos', 'initial' => 'M', 'color' => 'mint', 'time' => '10:46', 'text' => '¡Qué bien tener un espacio para la comunidad!'],
    ['name' => 'Luna', 'initial' => 'L', 'color' => 'lavender', 'time' => '10:49', 'text' => 'Podemos organizar canales por temas y dejar las conversaciones más ordenadas.'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#17191f">
    <title>Chat | Corebite</title>
    <link rel="icon" href="../Media/SVG/corebite-mark.svg?v=20261112" type="image/svg+xml">
    <link rel="stylesheet" href="../Media/CSS/estilo.css?v=20261113-scrollbars1">
    <link rel="stylesheet" href="../Media/CSS/workspace.css?v=20261113-presence3">
    <script src="../Media/JS/workspace.js?v=20261113-presence2" defer></script>
    <link rel="stylesheet" href="../Media/CSS/site-cursor.css?v=20261112-cursor2">
    <link rel="stylesheet" href="../Media/CSS/design-system.css?v=20261113-design10">
</head>
<body class="chat-page" data-theme="discord" data-channel-position="left" data-server-position="left">
    <div class="app-shell" id="app-shell">
        <aside class="server-rail" aria-label="Servidores">
            <a class="home-server" href="workspace.php" aria-label="Inicio de Corebite"><img src="../Media/SVG/corebite-mark.svg?v=20261112" alt=""></a>
            <button class="dm-rail-button" type="button" id="dm-view-toggle" aria-label="Mensajes directos" title="Mensajes directos"><span class="svg-icon icon-messages" aria-hidden="true"></span></button>
            <span class="rail-divider"></span>
            <?php foreach ($servers as $index => $server): ?>
                <button class="server-icon <?= $index === 0 ? 'selected' : '' ?>" type="button" data-server="<?= h($server['name']) ?>" title="<?= h($server['name']) ?>" aria-label="Servidor <?= h($server['name']) ?>"><span><?= h($server['badge']) ?></span></button>
            <?php endforeach; ?>
            <button class="server-add" type="button" data-toast="La creación de servidores llegará en una próxima etapa." aria-label="Añadir servidor"><span class="svg-icon icon-plus" aria-hidden="true"></span></button>
        </aside>

        <aside class="channel-sidebar" aria-label="Navegación">
            <div class="server-navigation" id="server-navigation">
            <button class="server-heading" type="button" data-toast="Ajustes del servidor próximamente"><span id="server-name">Corebite</span><span class="svg-icon icon-chevron" aria-hidden="true"></span></button>
            <button class="server-search" type="button" data-toast="La búsqueda del servidor estará disponible más adelante"><span class="svg-icon icon-search" aria-hidden="true"></span> Buscar en el servidor</button>
            <nav class="channel-list" aria-label="Canales">
                <?php foreach ($channels as $category => $items): ?>
                    <div class="channel-category"><span><?= h($category) ?></span><button type="button" data-toast="La creación de canales llegará en una próxima etapa." aria-label="Añadir canal a <?= h($category) ?>"><span class="svg-icon icon-plus" aria-hidden="true"></span></button></div>
                    <?php foreach ($items as $index => [$id, $label]): ?>
                        <button class="channel-link <?= $id === 'general' ? 'active' : '' ?>" type="button" data-channel="<?= h($id) ?>" data-label="<?= h($label) ?>"><span class="svg-icon <?= $category === 'VOZ' ? 'icon-headphones' : 'icon-hash' ?>" aria-hidden="true"></span><span><?= h($label) ?></span></button>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </nav>
            </div>
            <section class="dm-navigation" id="dm-navigation" aria-label="Conversaciones privadas" hidden>
                <div class="dm-sidebar-heading"><h2>Mensajes directos</h2><button type="button" class="icon-button" data-toast="Iniciar una nueva conversación estará disponible más adelante" aria-label="Nueva conversación"><span class="svg-icon icon-plus" aria-hidden="true"></span></button></div>
                <label class="visually-hidden" for="dm-search">Buscar conversaciones</label>
                <input class="dm-search" id="dm-search" type="search" placeholder="Buscar o iniciar conversación">
                <div class="dm-list" id="dm-list">
                    <button type="button" class="dm-contact active" data-contact-id="sofia" data-dm="Sofía" data-status="En línea" data-initial="S" data-color="lavender" data-description="Diseñadora y aficionada a las comunidades. Siempre dispuesta a ayudar." data-activity="Diseñando una comunidad nueva|Compartió una idea en general">
                        <span class="user-avatar dm-avatar lavender">S<i class="presence-dot"></i></span><span class="dm-contact-copy"><strong>Sofía</strong><small>¡Nos vemos luego!</small></span><span class="dm-time">10:24</span>
                    </button>
                    <button type="button" class="dm-contact" data-contact-id="rodrigo" data-dm="Rodrigo" data-status="Ausente" data-initial="R" data-color="mint" data-description="Amante de los videojuegos y las charlas tranquilas." data-activity="Jugó en el canal de voz|Visitó el canal general">
                        <span class="user-avatar dm-avatar mint">R<i class="presence-dot away"></i></span><span class="dm-contact-copy"><strong>Rodrigo</strong><small>¿Has visto el canal nuevo?</small></span><span class="dm-time">Ayer</span>
                    </button>
                    <button type="button" class="dm-contact" data-contact-id="alex" data-dm="Alex" data-status="No molestar" data-initial="A" data-color="blue" data-description="Desarrollador. Le interesan el código abierto y Linux." data-activity="Actualizó su perfil|Compartió un enlace">
                        <span class="user-avatar dm-avatar blue">A<i class="presence-dot busy"></i></span><span class="dm-contact-copy"><strong>Alex</strong><small>Perfecto, gracias 👌</small></span><span class="dm-time">Lun</span>
                    </button>
                    <button type="button" class="dm-contact" data-contact-id="noa" data-dm="Noa" data-status="En línea" data-initial="N" data-color="rose" data-description="Le gusta compartir ilustraciones y descubrir música." data-activity="Compartió una imagen|Se unió a Corebite">
                        <span class="user-avatar dm-avatar rose">N<i class="presence-dot"></i></span><span class="dm-contact-copy"><strong>Noa</strong><small>Compartió una imagen</small></span><span class="dm-time">Dom</span>
                    </button>
                    <?php foreach ($publicMembers as $member): ?>
                        <?php if (($member['id'] ?? '') === ($account['id'] ?? null)) continue; ?>
                        <?php $contactProfile = $member; unset($contactProfile['id']); $contactProfileJson = json_encode($contactProfile, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP); ?>
                        <button type="button" class="dm-contact" data-contact-id="account-<?= h($member['id']) ?>" data-dm="<?= h($member['username']) ?>" data-status="<?= h($member['statusLabel']) ?>" data-status-key="<?= h($member['status']) ?>" data-contact-profile="<?= h($contactProfileJson === false ? '{}' : $contactProfileJson) ?>">
                            <span class="user-avatar dm-avatar lavender"><span data-avatar-initial <?= $member['hasCustomAvatar'] ? 'hidden' : '' ?>><?= h($member['initial']) ?></span><img data-avatar-image src="<?= h($member['avatar']) ?>" alt="" <?= $member['hasCustomAvatar'] ? '' : 'hidden' ?>><i class="presence-dot <?= h($member['statusClass']) ?>"></i></span>
                            <span class="dm-contact-copy"><strong><?= h($member['username']) ?></strong><small><?= h($member['statusLabel']) ?></small></span><span class="dm-time">Perfil</span>
                        </button>
                    <?php endforeach; ?>
                </div>
                <p class="dm-empty" id="dm-empty" hidden>No hay conversaciones que coincidan con la búsqueda.</p>
                <div class="dm-prototype-note">Conversaciones de muestra · todavía no se guardan.</div>
            </section>
            <div class="user-panel" id="user-panel">
                <input type="hidden" id="csrf-token" value="<?= $csrf ?>">
                <button class="user-avatar small-avatar user-status-trigger" id="user-status-trigger" type="button" data-self-avatar aria-haspopup="true" aria-expanded="false" aria-controls="user-status-menu" aria-label="Cambiar estado (<?= h($currentPresenceLabel) ?>)" title="Cambiar estado">
                    <span data-avatar-initial <?= $hasCustomAvatar ? 'hidden' : '' ?>><?= $initial ?></span><img data-avatar-image src="<?= h($profileAvatarSrc) ?>" alt="" <?= $hasCustomAvatar ? '' : 'hidden' ?>><i class="presence-dot <?= h($currentPresenceClass) ?> user-current-presence-dot" data-current-presence-dot aria-hidden="true"></i>
                </button>
                <div class="user-summary"><strong data-current-username><?= $username ?></strong><span data-current-presence-label><?= h($currentPresenceLabel) ?></span></div>
                <div class="user-status-menu" id="user-status-menu" role="menu" aria-label="Estado para los demás" hidden>
                    <p>Mostrar como</p>
                    <?php foreach ([['online', 'En línea'], ['away', 'Ausente'], ['busy', 'No molestar'], ['offline', 'Invisible']] as [$presenceValue, $presenceText]): ?>
                        <button type="button" role="menuitemradio" aria-checked="<?= $currentPresence === $presenceValue ? 'true' : 'false' ?>" data-presence-option="<?= h($presenceValue) ?>" class="user-status-option <?= $currentPresence === $presenceValue ? 'selected' : '' ?>"><i class="presence-dot <?= h(presence_class($presenceValue)) ?>" aria-hidden="true"></i><span><?= h($presenceText) ?></span><span class="status-option-check" aria-hidden="true">✓</span></button>
                    <?php endforeach; ?>
                </div>
                <button class="icon-button settings-trigger account-menu-trigger" type="button" data-settings-start="account" title="Ajustes de cuenta" aria-label="Ajustes de cuenta"><span class="svg-icon icon-settings" aria-hidden="true"></span></button>
                <form action="auth.php" method="post" class="logout-mini"><input type="hidden" name="csrf" value="<?= $csrf ?>"><input type="hidden" name="action" value="logout"><button class="icon-button" type="submit" title="Cerrar sesión" aria-label="Cerrar sesión"><span class="svg-icon icon-logout" aria-hidden="true"></span></button></form>
            </div>
        </aside>

        <main class="chat-main" id="server-chat">
            <header class="chat-header">
                <div class="current-channel"><button class="icon-button" type="button" id="channels-toggle" aria-label="Abrir menú de canales" aria-expanded="false"><span class="svg-icon icon-menu" aria-hidden="true"></span></button><span class="svg-icon icon-hash" aria-hidden="true"></span><strong id="current-channel-name">general</strong><span class="header-subtitle">Un lugar para conversar</span></div>
                <div class="chat-actions"><button class="icon-button" type="button" id="members-toggle" aria-controls="member-sidebar" aria-pressed="true" title="Mostrar u ocultar miembros" aria-label="Mostrar u ocultar miembros"><span class="svg-icon icon-users" aria-hidden="true"></span></button><button class="icon-button settings-trigger" type="button" aria-label="Personalizar apariencia" title="Personalizar apariencia"><span class="svg-icon icon-settings" aria-hidden="true"></span></button></div>
            </header>
            <section class="message-scroll" id="message-scroll" aria-label="Mensajes del canal">
                <div class="channel-welcome"><div class="welcome-symbol"><span class="svg-icon icon-hash" aria-hidden="true"></span></div><h1 data-ascii-reveal>Te damos la bienvenida a <span id="welcome-channel">general</span></h1><p>Este es el comienzo de este canal.</p></div>
                <div class="message-list" id="message-list">
                    <?php foreach ($messages as $message): ?>
                        <article class="message"><div class="user-avatar message-avatar <?= h($message['color']) ?>"><?= h($message['initial']) ?></div><div class="message-body"><div class="message-meta"><strong><?= h($message['name']) ?></strong><time><?= h($message['time']) ?></time></div><p><?= h($message['text']) ?></p></div></article>
                    <?php endforeach; ?>
                </div>
            </section>
            <form class="message-composer" id="message-form">
                <button class="icon-button attach-button" type="button" data-toast="Los archivos adjuntos estarán disponibles más adelante" aria-label="Adjuntar archivo"><span class="svg-icon icon-plus" aria-hidden="true"></span></button>
                <label class="visually-hidden" for="message-input">Escribe un mensaje</label>
                <input id="message-input" name="message" autocomplete="off" maxlength="1000" placeholder="Enviar mensaje a #general" required>
                <button class="send-button" type="submit" aria-label="Enviar mensaje" title="Enviar"><span class="svg-icon icon-send" aria-hidden="true"></span></button>
            </form>
            <p class="prototype-hint" id="prototype-hint">Vista previa: los mensajes aún no se guardan en el servidor.</p>
        </main>

        <main class="chat-main dm-main" id="dm-chat" hidden>
            <div class="dm-top-shell">
            <section class="dm-profile-overview" aria-label="Información del contacto">
                <div class="dm-overview-person">
                    <img class="dm-overview-avatar" id="dm-overview-avatar" src="../Media/IMG/default-avatar.png" alt="Foto de perfil de Sofía">
                <div class="dm-overview-nameblock"><h1 id="dm-overview-name" data-ascii-reveal>Sofía</h1><p><i class="presence-dot"></i><span id="dm-overview-status">En línea</span></p></div>
                </div>
                <section class="dm-overview-card dm-about-card"><h2>Sobre mí</h2><p id="dm-overview-description">Diseñadora y aficionada a las comunidades. Siempre dispuesta a ayudar.</p></section>
                <section class="dm-overview-card dm-activity-card"><h2>Actividad reciente</h2><ul id="dm-activity-list"><li>Diseñando una comunidad nueva</li><li>Compartió una idea en general</li></ul></section>
                <div class="dm-chat-tools" aria-label="Herramientas de conversación">
                    <button class="icon-button" type="button" id="dm-search-toggle" aria-label="Buscar en conversación" title="Buscar"><span class="svg-icon icon-search" aria-hidden="true"></span></button>
                    <button class="icon-button" type="button" id="dm-call-button" data-toast="Las llamadas estarán disponibles más adelante" aria-label="Llamar" title="Llamar"><span class="svg-icon icon-phone" aria-hidden="true"></span></button>
                    <button class="icon-button" type="button" id="dm-chat-settings-open" aria-label="Ajustes de este chat" title="Ajustes de este chat"><span class="svg-icon icon-settings" aria-hidden="true"></span></button>
                    <button class="icon-button" type="button" id="dm-conversation-jump" aria-label="Ir a los mensajes" title="Ir a los mensajes"><span class="svg-icon icon-messages" aria-hidden="true"></span></button>
                </div>
            </section>
            </div>
            <section class="message-scroll dm-message-scroll" id="dm-message-scroll" aria-label="Mensajes privados">
                <div class="message-list" id="dm-message-list">
                    <article class="message"><div class="user-avatar message-avatar lavender">S</div><div class="message-body"><div class="message-meta"><strong>Sofía</strong><time>10:20</time></div><p>¡Hola! ¿Cómo va el proyecto?</p></div></article>
                    <article class="message"><div class="user-avatar message-avatar" data-self-avatar><span data-avatar-initial <?= $hasCustomAvatar ? 'hidden' : '' ?>><?= $initial ?></span><img data-avatar-image src="<?= h($profileAvatarSrc) ?>" alt="" <?= $hasCustomAvatar ? '' : 'hidden' ?>></div><div class="message-body"><div class="message-meta"><strong data-current-username><?= $username ?></strong><time>10:22</time></div><p>Avanzando con el diseño de Corebite 😊</p></div></article>
                    <article class="message"><div class="user-avatar message-avatar lavender">S</div><div class="message-body"><div class="message-meta"><strong>Sofía</strong><time>10:24</time></div><p>¡Qué bien! Cuando esté listo, enséñame cómo quedan los chats.</p></div></article>
                </div>
            </section>
            <form class="message-composer" id="dm-message-form">
                <button class="icon-button attach-button" type="button" data-toast="Los archivos adjuntos estarán disponibles más adelante" aria-label="Adjuntar archivo"><span class="svg-icon icon-plus" aria-hidden="true"></span></button>
                <label class="visually-hidden" for="dm-message-input">Escribe un mensaje privado</label>
                <input id="dm-message-input" name="message" autocomplete="off" maxlength="1000" placeholder="Enviar mensaje a Sofía" required>
                <button class="send-button" type="submit" aria-label="Enviar mensaje" title="Enviar"><span class="svg-icon icon-send" aria-hidden="true"></span></button>
            </form>
            <p class="prototype-hint">Vista previa: estos mensajes no se envían ni se guardan.</p>
        </main>

        <aside class="member-sidebar" id="member-sidebar" aria-label="Miembros del servidor">
            <section id="server-members">
            <h2>En línea — <?= count($publicMembers) + 2 ?></h2>
            <div class="member-group-label">Administradores — 1</div>
            <div class="member-row"><span class="user-avatar small-avatar lavender">L</span><span><strong>Luna</strong><small>Administradora</small></span><i class="online-dot"></i></div>
            <div class="member-group-label">Miembros — <?= count($publicMembers) + 1 ?></div>
            <div class="member-row"><span class="user-avatar small-avatar mint">M</span><span><strong>Marcos</strong><small>En línea</small></span><i class="online-dot"></i></div>
            <?php foreach ($publicMembers as $member): ?>
                <?php $profileCardData = $member; unset($profileCardData['id']); $publicProfileJson = json_encode($profileCardData, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP); ?>
                <button class="member-row profile-member-button" type="button" data-user-profile="<?= h($publicProfileJson === false ? '{}' : $publicProfileJson) ?>" <?= ($member['id'] ?? '') === ($account['id'] ?? null) ? 'data-current-profile-card' : '' ?> aria-label="Ver perfil de <?= h($member['username']) ?>">
                    <span class="user-avatar small-avatar" <?= ($member['id'] ?? '') === ($account['id'] ?? null) ? 'data-self-avatar' : '' ?>>
                        <?php if (($member['id'] ?? '') === ($account['id'] ?? null) && !$member['hasCustomAvatar']): ?><span data-avatar-initial><?= h($member['initial']) ?></span>
                        <?php elseif (!$member['hasCustomAvatar']): ?><?= h($member['initial']) ?><?php endif; ?>
                        <img <?= ($member['id'] ?? '') === ($account['id'] ?? null) ? 'data-avatar-image' : '' ?> src="<?= h($member['avatar']) ?>" alt="" <?= $member['hasCustomAvatar'] ? '' : 'hidden' ?>>
                    </span>
                    <span><strong <?= ($member['id'] ?? '') === ($account['id'] ?? null) ? 'data-current-username' : '' ?>><?= h($member['username']) ?></strong><small <?= ($member['id'] ?? '') === ($account['id'] ?? null) ? 'data-current-presence-label' : '' ?>><?= h($member['statusLabel']) ?></small></span><i class="online-dot <?= h($member['statusClass']) ?> <?= ($member['id'] ?? '') === ($account['id'] ?? null) ? 'user-current-presence-dot' : '' ?>" <?= ($member['id'] ?? '') === ($account['id'] ?? null) ? 'data-current-presence-dot' : '' ?>></i>
                </button>
            <?php endforeach; ?>
            </section>
        </aside>
    </div>

    <div class="dm-search-popover" id="dm-search-popover" role="search" aria-label="Buscar en esta conversación" hidden>
        <label class="visually-hidden" for="dm-message-search">Buscar mensajes de esta conversación</label>
        <span class="svg-icon icon-search" aria-hidden="true"></span>
        <input id="dm-message-search" type="search" placeholder="Buscar en la conversación" autocomplete="off">
    </div>

    <dialog class="public-profile-dialog" id="public-profile-dialog" aria-labelledby="public-profile-name">
        <button class="dialog-close" type="button" id="public-profile-close" aria-label="Cerrar perfil"><span class="svg-icon icon-close" aria-hidden="true"></span></button>
        <div class="public-profile-identity"><img id="public-profile-avatar" src="../Media/IMG/default-avatar.png" alt=""><div><h2 id="public-profile-name"></h2><p id="public-profile-member-since">Miembro de Corebite</p></div></div>
        <section class="public-profile-section"><h3>Sobre mí</h3><p id="public-profile-about"></p></section>
        <section class="public-profile-section"><h3>Actividad reciente</h3><ul id="public-profile-activities"></ul></section>
    </dialog>
    <dialog class="account-dialog" id="account-settings-dialog" aria-labelledby="account-settings-title">
        <button class="dialog-close settings-window-close" type="button" data-settings-close aria-label="Cerrar ajustes"><span class="dialog-close-glyph" aria-hidden="true">×</span></button>
        <span class="visually-hidden" id="account-settings-title">Ajustes de perfil</span>
        <header class="settings-profile-header">
            <div class="settings-profile-avatar"><img data-current-profile-photo src="<?= h($profileAvatarSrc) ?>" alt=""></div>
            <div class="settings-profile-info"><h2 data-current-username><?= $username ?></h2><p>Tu perfil público</p></div>
        </header>
        <aside class="account-settings-nav" aria-label="Secciones de ajustes">
            <button class="settings-nav-button active" type="button" data-settings-tab="account"><span class="svg-icon icon-user" aria-hidden="true"></span> Mi perfil</button>
            <button class="settings-nav-button" type="button" data-settings-tab="security"><span class="svg-icon icon-shield" aria-hidden="true"></span> Seguridad</button>
            <button class="settings-nav-button" type="button" data-settings-tab="personalization"><span class="svg-icon icon-palette" aria-hidden="true"></span> Personalización</button>
            <form action="auth.php" method="post" class="settings-logout-form"><input type="hidden" name="csrf" value="<?= $csrf ?>"><input type="hidden" name="action" value="logout"><button class="settings-logout-button" type="submit"><span>Cerrar sesión</span><span class="svg-icon icon-logout" aria-hidden="true"></span></button></form>
        </aside>
        <div class="account-settings-content">
            <section class="settings-panel active" id="account" data-settings-panel="account">
                <p class="dialog-eyebrow">Tu espacio en Corebite</p><h2>Mi perfil</h2>
                <div class="account-data-box">
                    <button class="account-data-toggle" id="account-data-toggle" type="button" aria-expanded="false" aria-controls="account-data-details"><span class="svg-icon icon-eye" aria-hidden="true"></span><span class="account-data-toggle-label">Mostrar datos de la cuenta</span></button>
                    <dl class="account-data-details" id="account-data-details" hidden>
                        <div><dt>Correo electrónico</dt><dd><?= h((string)($account['email'] ?? '')) ?></dd></div>
                        <div><dt>Teléfono</dt><dd><?= h((string)($account['phone'] ?? '')) ?: 'No indicado' ?></dd></div>
                        <div><dt>Fecha de nacimiento</dt><dd><?= !empty($account['birthdate']) ? h(date('d/m/Y', strtotime((string)$account['birthdate']))) : 'No indicada' ?></dd></div>
                        <div><dt>Miembro desde</dt><dd><?= !empty($account['created_at']) ? h(date('F Y', strtotime((string)$account['created_at']))) : '—' ?></dd></div>
                    </dl>
                </div>
                <p class="profile-public-note">El nombre, la imagen, «Sobre mí» y las notas aparecerán en tu ficha pública para los demás miembros.</p>
                <form class="profile-edit-form" id="profile-form" action="profile-update.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="csrf" value="<?= $csrf ?>">
                    <fieldset class="profile-edit-group">
                        <legend>Datos públicos</legend>
                        <label for="profile-username">Nombre de usuario</label>
                        <input id="profile-username" name="username" type="text" value="<?= $username ?>" minlength="4" maxlength="32" autocomplete="nickname" required>
                        <label for="profile-avatar">Imagen de perfil</label>
                        <div class="profile-avatar-field">
                            <img id="profile-avatar-preview" data-saved-src="<?= h($profileAvatarSrc) ?>" src="<?= h($profileAvatarSrc) ?>" alt="Vista previa de la imagen de perfil">
                            <label class="profile-file-button" for="profile-avatar">Elegir imagen</label>
                            <input id="profile-avatar" name="avatar" type="file" accept="image/png,image/jpeg,image/webp">
                            <small>PNG, JPG o WebP · máximo 5 MB</small>
                        </div>
                        <label for="profile-about">Sobre mí</label>
                        <textarea id="profile-about" name="about" rows="3" maxlength="300" placeholder="Cuéntales algo sobre ti (máximo 300 caracteres)"><?= h(is_string($account['about'] ?? null) ? $account['about'] : '') ?></textarea>
                        <label for="profile-activities">Notas de actividad reciente</label>
                        <textarea id="profile-activities" name="activities" rows="3" maxlength="273" placeholder="Añade hasta tres notas, una por línea"><?= h(implode("\n", is_array($account['recent_activities'] ?? null) ? array_slice(array_filter($account['recent_activities'], 'is_string'), 0, 3) : [])) ?></textarea>
                        <small class="profile-field-hint">Hasta 3 notas, con un máximo de 90 caracteres cada una.</small>
                    </fieldset>
                    <button class="profile-save-button" type="submit">Guardar perfil</button>
                    <p class="profile-save-status" id="profile-save-status" role="status" aria-live="polite"></p>
                </form>
            </section>
            <section class="settings-panel" data-settings-panel="personalization" hidden>
                <p class="dialog-eyebrow">Tu espacio, a tu manera</p><h2>Personalización</h2>
                <h3 class="settings-section-heading">Colores</h3>
                <fieldset class="theme-card-grid" aria-label="Paleta de color">
                    <legend class="visually-hidden">Selecciona un estilo de color</legend>
                    <label class="theme-card is-selected" data-theme-card>
                        <input type="radio" name="theme-choice" value="discord" checked>
                        <span class="theme-preview" style="--preview-bg:#10141c;--preview-panel:#191b27;--preview-accent:#7966ac;--preview-text:#f7f4fc" aria-hidden="true"><i></i><i></i><b></b></span>
                        <strong>Corebite oscuro</strong><small>La paleta original</small>
                    </label>
                    <label class="theme-card" data-theme-card>
                        <input type="radio" name="theme-choice" value="midnight">
                        <span class="theme-preview" style="--preview-bg:#17212d;--preview-panel:#111b27;--preview-accent:#4c9aff;--preview-text:#e8f1fb" aria-hidden="true"><i></i><i></i><b></b></span>
                        <strong>Medianoche azul</strong><small>Azul profundo</small>
                    </label>
                    <label class="theme-card" data-theme-card>
                        <input type="radio" name="theme-choice" value="light">
                        <span class="theme-preview" style="--preview-bg:#f2f3f5;--preview-panel:#e5e7eb;--preview-accent:#5865f2;--preview-text:#25272c" aria-hidden="true"><i></i><i></i><b></b></span>
                        <strong>Claro</strong><small>Superficies luminosas</small>
                    </label>
                    <div class="theme-card theme-card-custom" data-theme-card>
                        <label class="theme-card-custom-title">
                            <input type="radio" name="theme-choice" value="custom">
                            <span class="theme-preview" data-theme-preview="custom" style="--preview-bg:#10141c;--preview-panel:#191b27;--preview-accent:#ac9bdb;--preview-text:#f7f4fc" aria-hidden="true"><i></i><i></i><b></b></span>
                            <span><strong>Personalizado</strong><small>Elige tus propios colores</small></span>
                        </label>
                        <div class="theme-color-grid">
                            <label for="theme-color-background"><span>Fondo</span><input id="theme-color-background" type="color" data-theme-color="background" value="#10141c"></label>
                            <label for="theme-color-surface"><span>Paneles</span><input id="theme-color-surface" type="color" data-theme-color="surface" value="#191b27"></label>
                            <label for="theme-color-accent"><span>Acento</span><input id="theme-color-accent" type="color" data-theme-color="accent" value="#ac9bdb"></label>
                            <label for="theme-color-text"><span>Texto</span><input id="theme-color-text" type="color" data-theme-color="text" value="#f7f4fc"></label>
                        </div>
                    </div>
                </fieldset>
                <h3 class="settings-section-heading">Distribución de los menús</h3>
                <fieldset class="settings-choice-group" aria-label="Posición del menú de canales">
                    <legend><span class="svg-icon icon-panel" aria-hidden="true"></span> Posición del menú de canales</legend>
                    <div class="layout-choice-grid">
                        <label class="layout-choice is-selected" data-layout-card>
                            <input type="radio" name="channel-position" value="left" checked>
                            <svg class="layout-mini" viewBox="0 0 100 50" aria-hidden="true" focusable="false"><rect class="mini-shell" x="2" y="2" width="96" height="46" rx="4"/><rect class="mini-servers" x="5" y="5" width="9" height="40" rx="2"/><rect class="mini-channels is-target" x="17" y="5" width="20" height="40" rx="2"/><rect class="mini-chat" x="40" y="5" width="55" height="40" rx="2"/><path class="mini-lines" d="M45 14h30M45 20h42M45 27h35M45 34h25"/></svg>
                            <span><strong>Izquierda</strong><small>Junto a la barra de servidores</small></span>
                        </label>
                        <label class="layout-choice" data-layout-card>
                            <input type="radio" name="channel-position" value="right">
                            <svg class="layout-mini" viewBox="0 0 100 50" aria-hidden="true" focusable="false"><rect class="mini-shell" x="2" y="2" width="96" height="46" rx="4"/><rect class="mini-servers" x="5" y="5" width="9" height="40" rx="2"/><rect class="mini-chat" x="17" y="5" width="55" height="40" rx="2"/><rect class="mini-channels is-target" x="75" y="5" width="20" height="40" rx="2"/><path class="mini-lines" d="M22 14h30M22 20h42M22 27h35M22 34h25"/></svg>
                            <span><strong>Derecha</strong><small>Al otro lado del chat</small></span>
                        </label>
                    </div>
                </fieldset>
                <fieldset class="settings-choice-group" aria-label="Posición de la barra de servidores">
                    <legend><span class="svg-icon icon-server" aria-hidden="true"></span> Posición de la barra de servidores</legend>
                    <div class="layout-choice-grid">
                        <label class="layout-choice is-selected" data-layout-card>
                            <input type="radio" name="server-position" value="left" checked>
                            <svg class="layout-mini" viewBox="0 0 100 50" aria-hidden="true" focusable="false"><rect class="mini-shell" x="2" y="2" width="96" height="46" rx="4"/><rect class="mini-servers is-target" x="5" y="5" width="9" height="40" rx="2"/><rect class="mini-channels" x="17" y="5" width="20" height="40" rx="2"/><rect class="mini-chat" x="40" y="5" width="55" height="40" rx="2"/><path class="mini-lines" d="M45 14h30M45 20h42M45 27h35M45 34h25"/></svg>
                            <span><strong>Izquierda</strong><small>Al inicio de las barras</small></span>
                        </label>
                        <label class="layout-choice" data-layout-card>
                            <input type="radio" name="server-position" value="right">
                            <svg class="layout-mini" viewBox="0 0 100 50" aria-hidden="true" focusable="false"><rect class="mini-shell" x="2" y="2" width="96" height="46" rx="4"/><rect class="mini-channels" x="5" y="5" width="20" height="40" rx="2"/><rect class="mini-chat" x="28" y="5" width="55" height="40" rx="2"/><rect class="mini-servers is-target" x="86" y="5" width="9" height="40" rx="2"/><path class="mini-lines" d="M33 14h30M33 20h42M33 27h35M33 34h25"/></svg>
                            <span><strong>Derecha</strong><small>Al final de las barras</small></span>
                        </label>
                    </div>
                </fieldset>
                <label class="check-setting"><input type="checkbox" id="show-members" checked><span class="svg-icon icon-users" aria-hidden="true"></span> Mostrar lista de miembros</label>
                <h3 class="settings-section-heading">Tamaño de la tarjeta en chats privados</h3>
                <fieldset class="settings-choice-group dm-card-size-group" aria-label="Tamaño de la tarjeta del usuario en chats privados">
                    <legend class="visually-hidden">Elige entre tamaño normal y reducido</legend>
                    <div class="dm-card-size-grid">
                        <label class="layout-choice dm-card-size-choice is-selected" data-dm-card-choice>
                            <input type="radio" name="dm-card-size" value="standard" checked>
                            <span class="dm-card-size-preview" data-preview-size="standard" aria-hidden="true"><i></i><i></i><b></b></span>
                            <span><strong>Normal</strong><small>Diseño actual · predeterminado</small></span>
                        </label>
                        <label class="layout-choice dm-card-size-choice" data-dm-card-choice>
                            <input type="radio" name="dm-card-size" value="compact">
                            <span class="dm-card-size-preview" data-preview-size="compact" aria-hidden="true"><i></i><i></i><b></b></span>
                            <span><strong>Reducido</strong><small>Perfil y controles en una fila</small></span>
                        </label>
                    </div>
                </fieldset>
            </section>
            <section class="settings-panel" data-settings-panel="security" hidden>
                <p class="dialog-eyebrow">Protección de tu perfil</p><h2>Seguridad</h2>
                <div class="account-detail"><span>Contraseña</span><strong>••••••••••</strong></div>
                <p class="settings-note">El cambio de contraseña y la verificación en dos pasos se añadirán cuando completemos las funciones de cuenta.</p>
            </section>
        </div>
    </dialog>
    <dialog class="chat-settings-dialog" id="chat-settings-dialog" aria-labelledby="chat-settings-title">
        <button class="dialog-close settings-window-close" type="button" id="chat-settings-close" aria-label="Cerrar ajustes del chat"><span class="dialog-close-glyph" aria-hidden="true">×</span></button>
        <header class="chat-settings-header">
            <span class="chat-settings-avatar"><span class="svg-icon icon-messages" aria-hidden="true"></span></span>
            <div class="chat-settings-heading">
                <p class="dialog-eyebrow">Personalización de conversación</p>
                <h2 id="chat-settings-title">Ajustes del chat</h2>
                <p>Estas opciones solo se aplican al chat con <strong id="chat-settings-contact">Sofía</strong>.</p>
            </div>
        </header>
        <div class="chat-settings-content">
            <fieldset class="chat-setting-section">
                <legend>Estilo de los mensajes</legend>
                <div class="chat-choice-grid chat-style-choices">
                    <label class="chat-choice is-selected"><input type="radio" name="dm-chat-style" value="classic" checked><span class="chat-style-preview" data-style-preview="classic" aria-hidden="true"><i></i><i></i></span><strong>Corebite</strong><small>Clásico</small></label>
                    <label class="chat-choice"><input type="radio" name="dm-chat-style" value="bubbles"><span class="chat-style-preview" data-style-preview="bubbles" aria-hidden="true"><i></i><i></i></span><strong>Tarjetas</strong><small>Mensajes en bloques</small></label>
                    <label class="chat-choice"><input type="radio" name="dm-chat-style" value="compact"><span class="chat-style-preview" data-style-preview="compact" aria-hidden="true"><i></i><i></i></span><strong>Compacto</strong><small>Más conversación</small></label>
                </div>
            </fieldset>
            <fieldset class="chat-setting-section">
                <legend>Tamaño del texto</legend>
                <div class="chat-choice-grid chat-size-choices">
                    <label class="chat-choice"><input type="radio" name="dm-chat-font-size" value="small"><span class="chat-size-sample" style="--sample-size:11px">Aa</span><strong>Pequeño</strong></label>
                    <label class="chat-choice is-selected"><input type="radio" name="dm-chat-font-size" value="normal" checked><span class="chat-size-sample" style="--sample-size:14px">Aa</span><strong>Normal</strong></label>
                    <label class="chat-choice"><input type="radio" name="dm-chat-font-size" value="large"><span class="chat-size-sample" style="--sample-size:17px">Aa</span><strong>Grande</strong></label>
                    <label class="chat-choice"><input type="radio" name="dm-chat-font-size" value="xlarge"><span class="chat-size-sample" style="--sample-size:20px">Aa</span><strong>Muy grande</strong></label>
                </div>
            </fieldset>
            <fieldset class="chat-setting-section">
                <legend>Tipografía</legend>
                <div class="chat-choice-grid chat-font-choices">
                    <label class="chat-choice is-selected"><input type="radio" name="dm-chat-font-family" value="system" checked><span class="chat-font-sample" style="font-family:system-ui,sans-serif">Ag</span><strong>Sistema</strong></label>
                    <label class="chat-choice"><input type="radio" name="dm-chat-font-family" value="arial"><span class="chat-font-sample" style="font-family:Arial,sans-serif">Ag</span><strong>Arial</strong></label>
                    <label class="chat-choice"><input type="radio" name="dm-chat-font-family" value="verdana"><span class="chat-font-sample" style="font-family:Verdana,sans-serif">Ag</span><strong>Verdana</strong></label>
                    <label class="chat-choice"><input type="radio" name="dm-chat-font-family" value="trebuchet"><span class="chat-font-sample" style="font-family:'Trebuchet MS',sans-serif">Ag</span><strong>Trebuchet</strong></label>
                    <label class="chat-choice"><input type="radio" name="dm-chat-font-family" value="georgia"><span class="chat-font-sample" style="font-family:Georgia,serif">Ag</span><strong>Georgia</strong></label>
                </div>
            </fieldset>
            <fieldset class="chat-setting-section chat-color-section">
                <legend>Color del texto</legend>
                <label class="chat-color-control" for="dm-chat-text-color"><span class="chat-color-swatch" aria-hidden="true"></span><span><strong>Elige un color</strong><small id="dm-chat-text-color-value">#F2F3F5</small></span><input class="chat-color-input" id="dm-chat-text-color" type="color" value="#f2f3f5" aria-label="Color del texto del chat"></label>
            </fieldset>
            <p class="settings-note">Los cambios se guardan automáticamente solo para esta conversación.</p>
        </div>
    </dialog>
    <div class="toast" id="toast" role="status" aria-live="polite"></div>
    <script src="../Media/JS/ascii-dissolve.js?v=20261113-3" defer></script>
</body>
</html>
