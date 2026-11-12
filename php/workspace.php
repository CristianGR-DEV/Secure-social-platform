<?php
declare(strict_types=1);
require_once __DIR__ . '/includes.php';
$account = require_login();
$csrf = h($_SESSION['csrf']);
$username = h($account['username']);
$initial = h(mb_strtoupper(mb_substr($account['username'], 0, 1, 'UTF-8'), 'UTF-8'));
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
    <link rel="stylesheet" href="../Media/CSS/estilo.css">
    <link rel="stylesheet" href="../Media/CSS/workspace.css">
    <script src="../Media/JS/workspace.js" defer></script>
    <link rel="stylesheet" href="../Media/CSS/site-cursor.css?v=20261112-cursor2">
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
                </div>
                <p class="dm-empty" id="dm-empty" hidden>No hay conversaciones que coincidan con la búsqueda.</p>
                <div class="dm-prototype-note">Conversaciones de muestra · todavía no se guardan.</div>
            </section>
            <div class="user-panel">
                <div class="user-avatar small-avatar"><?= $initial ?></div>
                <div class="user-summary"><strong><?= $username ?></strong><span>En línea</span></div>
                <button class="icon-button settings-trigger account-menu-trigger" type="button" data-settings-start="account" title="Ajustes de cuenta" aria-label="Ajustes de cuenta"><span class="svg-icon icon-settings" aria-hidden="true"></span></button>
                <form action="auth.php" method="post" class="logout-mini"><input type="hidden" name="csrf" value="<?= $csrf ?>"><input type="hidden" name="action" value="logout"><button class="icon-button" type="submit" title="Cerrar sesión" aria-label="Cerrar sesión"><span class="svg-icon icon-logout" aria-hidden="true"></span></button></form>
            </div>
        </aside>

        <main class="chat-main" id="server-chat">
            <header class="chat-header">
                <div class="current-channel"><button class="icon-button" type="button" id="channels-toggle" aria-label="Abrir menú de canales" aria-expanded="false"><span class="svg-icon icon-menu" aria-hidden="true"></span></button><span class="svg-icon icon-hash" aria-hidden="true"></span><strong id="current-channel-name">general</strong><span class="header-subtitle">Un lugar para conversar</span></div>
                <div class="chat-actions"><button class="icon-button" type="button" id="members-toggle" aria-pressed="true" title="Mostrar u ocultar miembros" aria-label="Mostrar u ocultar miembros"><span class="svg-icon icon-users" aria-hidden="true"></span></button><button class="icon-button settings-trigger" type="button" aria-label="Personalizar apariencia" title="Personalizar apariencia"><span class="svg-icon icon-settings" aria-hidden="true"></span></button></div>
            </header>
            <section class="message-scroll" id="message-scroll" aria-label="Mensajes del canal">
                <div class="channel-welcome"><div class="welcome-symbol"><span class="svg-icon icon-hash" aria-hidden="true"></span></div><h1>Te damos la bienvenida a <span id="welcome-channel">general</span></h1><p>Este es el comienzo de este canal.</p></div>
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
                    <div class="dm-overview-nameblock"><h1 id="dm-overview-name">Sofía</h1><p><i class="presence-dot"></i><span id="dm-overview-status">En línea</span></p></div>
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
                    <article class="message"><div class="user-avatar message-avatar">M</div><div class="message-body"><div class="message-meta"><strong><?= $username ?></strong><time>10:22</time></div><p>Avanzando con el diseño de Corebite 😊</p></div></article>
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
            <h2>En línea — 3</h2>
            <div class="member-group-label">Administradores — 1</div>
            <div class="member-row"><span class="user-avatar small-avatar lavender">L</span><span><strong>Luna</strong><small>Administradora</small></span><i class="online-dot"></i></div>
            <div class="member-group-label">Miembros — 2</div>
            <div class="member-row"><span class="user-avatar small-avatar mint">M</span><span><strong>Marcos</strong><small>En línea</small></span><i class="online-dot"></i></div>
            <div class="member-row"><span class="user-avatar small-avatar"><?= $initial ?></span><span><strong><?= $username ?></strong><small>En línea</small></span><i class="online-dot"></i></div>
            </section>
        </aside>
    </div>

    <div class="dm-search-popover" id="dm-search-popover" role="search" aria-label="Buscar en esta conversación" hidden>
        <label class="visually-hidden" for="dm-message-search">Buscar mensajes de esta conversación</label>
        <span class="svg-icon icon-search" aria-hidden="true"></span>
        <input id="dm-message-search" type="search" placeholder="Buscar en la conversación" autocomplete="off">
    </div>

    <dialog class="account-dialog" id="account-settings-dialog" aria-labelledby="account-settings-title">
        <button class="dialog-close" type="button" data-settings-close aria-label="Cerrar ajustes"><span class="svg-icon icon-close" aria-hidden="true"></span></button>
        <aside class="account-settings-nav">
            <div class="settings-user"><span class="user-avatar small-avatar"><?= $initial ?></span><span><strong><?= $username ?></strong><small>Ajustes de cuenta</small></span></div>
            <button class="settings-nav-button active" type="button" data-settings-tab="account"><span class="svg-icon icon-user" aria-hidden="true"></span> Mi cuenta</button>
            <button class="settings-nav-button" type="button" data-settings-tab="personalization"><span class="svg-icon icon-palette" aria-hidden="true"></span> Personalización</button>
            <button class="settings-nav-button" type="button" data-settings-tab="security"><span class="svg-icon icon-shield" aria-hidden="true"></span> Seguridad</button>
            <a class="settings-nav-link" href="cuenta.php">Abrir página de cuenta</a>
        </aside>
        <div class="account-settings-content">
            <header class="settings-content-header"><span id="account-settings-title">Mi cuenta</span><span>Corebite</span></header>
            <section class="settings-panel active" data-settings-panel="account">
                <p class="dialog-eyebrow">Tu perfil</p><h2>Mi cuenta</h2>
                <div class="account-detail"><span>Nombre de usuario</span><strong><?= $username ?></strong></div>
                <div class="account-detail"><span>Correo electrónico</span><strong><?= h($account['email']) ?></strong></div>
                <p class="settings-note">Desde aquí puedes consultar tus datos. La edición estará disponible en una próxima etapa.</p>
            </section>
            <section class="settings-panel" data-settings-panel="personalization" hidden>
                <p class="dialog-eyebrow">Tu espacio, a tu manera</p><h2>Personalización</h2>
                <label for="theme-select"><span class="svg-icon icon-palette" aria-hidden="true"></span> Paleta de color</label>
                <select id="theme-select"><option value="discord">Corebite oscuro</option><option value="midnight">Medianoche azul</option><option value="light">Claro</option></select>
                <label for="channel-position"><span class="svg-icon icon-panel" aria-hidden="true"></span> Posición del menú de canales</label>
                <select id="channel-position"><option value="left">A la izquierda</option><option value="right">A la derecha</option></select>
                <label for="server-position"><span class="svg-icon icon-server" aria-hidden="true"></span> Posición de la barra de servidores</label>
                <select id="server-position"><option value="left">A la izquierda</option><option value="right">A la derecha</option></select>
                <label class="check-setting"><input type="checkbox" id="show-members" checked><span class="svg-icon icon-users" aria-hidden="true"></span> Mostrar lista de miembros</label>
            </section>
            <section class="settings-panel" data-settings-panel="security" hidden>
                <p class="dialog-eyebrow">Protección de tu perfil</p><h2>Seguridad</h2>
                <div class="account-detail"><span>Contraseña</span><strong>••••••••••</strong></div>
                <p class="settings-note">El cambio de contraseña y la verificación en dos pasos se añadirán cuando completemos las funciones de cuenta.</p>
            </section>
        </div>
    </dialog>
    <dialog class="chat-settings-dialog" id="chat-settings-dialog" aria-labelledby="chat-settings-title">
        <button class="dialog-close" type="button" id="chat-settings-close" aria-label="Cerrar ajustes del chat"><span class="svg-icon icon-close" aria-hidden="true"></span></button>
        <div class="chat-settings-content">
            <header class="chat-settings-header">
                <p class="dialog-eyebrow">Personalización de conversación</p>
                <h2 id="chat-settings-title">Ajustes del chat</h2>
                <p>Estas opciones solo se aplican al chat con <strong id="chat-settings-contact">Sofía</strong>.</p>
            </header>
            <section class="chat-settings-grid" aria-label="Apariencia de este chat">
                <div class="chat-setting-field">
                    <label for="dm-chat-style">Estilo de mensajes</label>
                    <select id="dm-chat-style"><option value="classic">Corebite clásico</option><option value="bubbles">Mensajes en tarjetas</option><option value="compact">Compacto</option></select>
                </div>
                <div class="chat-setting-field">
                    <label for="dm-chat-font-size">Tamaño del texto</label>
                    <select id="dm-chat-font-size"><option value="small">Pequeño</option><option value="normal">Normal</option><option value="large">Grande</option><option value="xlarge">Muy grande</option></select>
                </div>
                <div class="chat-setting-field">
                    <label for="dm-chat-font-family">Tipografía</label>
                    <select id="dm-chat-font-family"><option value="system">Sistema</option><option value="arial">Arial</option><option value="verdana">Verdana</option><option value="trebuchet">Trebuchet MS</option><option value="georgia">Georgia</option></select>
                </div>
                <div class="chat-setting-field">
                    <label for="dm-chat-text-color">Color de la tipografía</label>
                    <input class="chat-color-input" id="dm-chat-text-color" type="color" value="#f2f3f5">
                </div>
            </section>
            <p class="settings-note">Los cambios se guardan automáticamente solo para esta conversación.</p>
        </div>
    </dialog>
    <div class="toast" id="toast" role="status" aria-live="polite"></div>
</body>
</html>
