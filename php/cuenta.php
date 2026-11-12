<?php
declare(strict_types=1);
require_once __DIR__ . '/includes.php';
$account = require_login();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../Media/SVG/corebite-mark.svg?v=20261112" type="image/svg+xml">
    <link rel="stylesheet" href="../Media/CSS/estilo.css">
    <link rel="stylesheet" href="../Media/CSS/site-pages.css">
    <link rel="stylesheet" href="../Media/CSS/cuenta.css">
    <script src="../Media/JS/account-settings.js" defer></script>
    <title>Cuenta | Corebite</title>
    <link rel="stylesheet" href="../Media/CSS/site-footer.css?v=20261112-footer3">
    <link rel="stylesheet" href="../Media/CSS/site-cursor.css?v=20261112-cursor2">
</head>
<body class="container auth-landing site-shell">
    <header class="landing-header">
  <a class="brand" href="../Index.html" aria-label="Corebite, volver al landing"><img src="../Media/SVG/corebite-mark.svg?v=20261112" alt="" width="42" height="42"><span>corebite</span></a>
  <div class="landing-actions"><a class="login-link" href="./sesion.php">Iniciar sesión</a><a class="button button-small" href="./sesion.php?mode=register">Crear cuenta <span aria-hidden="true">↗</span></a></div>
</header>

    <main class="profile-page">
        <div class="profile-container">
            <section class="profile-header">
                <div class="avatar-circle">
                    <img src="../Media/IMG/default-avatar.png" alt="Foto de perfil">
                </div>
                <div class="user-info">
                    <h2><?= h($account['username']) ?></h2>
                    <p>Miembro desde: <?= h(date('F Y', strtotime($account['created_at']))) ?></p>
                </div>
            </section>

            <div class="profile-body">

                <input type="radio" name="nav-tab" id="tab-info" checked>
                <input type="radio" name="nav-tab" id="tab-config">
                <input type="radio" name="nav-tab" id="tab-personalizacion">

                <aside class="profile-aside">
                    <label for="tab-info" class="tab-label">
                        Mis Datos
                        <span class="svg-icon icon-user" aria-hidden="true"></span>
                    </label>
                    <label for="tab-config" class="tab-label">
                        Seguridad
                        <span class="svg-icon icon-shield" aria-hidden="true"></span>
                    </label>
                    <label for="tab-personalizacion" class="tab-label">
                        Personalización
                        <span class="svg-icon icon-palette" aria-hidden="true"></span>
                    </label>
                    <form action="auth.php" method="post" class="logout-form"><input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>"><input type="hidden" name="action" value="logout"><button type="submit" class="tab-label logout">
                        Cerrar Sesión
                        <span class="svg-icon icon-logout" aria-hidden="true"></span>
                    </button></form>
                </aside>

                <section class="profile-content">

                    <div class="content-box box-info">
                        <h3>Información Personal</h3>
                        <p class="prototype-note">Datos de tu cuenta. Puedes consultar la información guardada.</p>
                        <form class="profile-form">
                            <fieldset>
                                <legend>Datos de Identidad</legend>

                                <label for="profile-username">Nombre de Usuario</label>
                                <input id="profile-username" type="text" value="<?= h($account['username']) ?>" readonly>

                                <label for="profile-email">Correo Electrónico</label>
                                <input id="profile-email" type="email" value="<?= h($account['email']) ?>" readonly>
                            </fieldset>

                            <fieldset>
                                <legend>Información Adicional</legend>

                                <label for="profile-birthdate">Fecha de Nacimiento</label>
                                <input id="profile-birthdate" type="date" value="<?= h($account['birthdate'] ?? '') ?>" readonly>
                            </fieldset>

                            <button type="button" class="btn-edit" disabled>Edición aún no disponible</button>
                        </form>
                    </div>

                    <div class="content-box box-config">
                        <h3>Seguridad de la Cuenta</h3>
                        <p class="prototype-note">El cambio de contraseña estará disponible cuando se conecte el sistema de cuentas.</p>
                    </div>

                    <div class="content-box box-personalizacion">
                        <h3>Personalización</h3>
                        <p class="prototype-note">Ajusta Corebite a tu gusto. Los cambios se aplican en la interfaz del chat y se guardan en este navegador.</p>
                        <form class="profile-form appearance-form" id="account-appearance-form">
                            <fieldset>
                                <legend>Colores y tema</legend>
                                <label for="account-theme"><span class="svg-icon icon-palette" aria-hidden="true"></span> Paleta de color</label>
                                <select id="account-theme"><option value="discord">Corebite oscuro</option><option value="midnight">Medianoche azul</option><option value="light">Claro</option></select>
                            </fieldset>
                            <fieldset>
                                <legend>Posición de los menús</legend>
                                <label for="account-server-position"><span class="svg-icon icon-server" aria-hidden="true"></span> Barra de servidores</label>
                                <select id="account-server-position"><option value="left">A la izquierda</option><option value="right">A la derecha</option></select>
                                <label for="account-channel-position"><span class="svg-icon icon-panel" aria-hidden="true"></span> Menú de canales y conversaciones</label>
                                <select id="account-channel-position"><option value="left">A la izquierda</option><option value="right">A la derecha</option></select>
                            </fieldset>
                            <fieldset>
                                <legend>Paneles</legend>
                                <label class="preference-check" for="account-show-members"><input type="checkbox" id="account-show-members"> Mostrar el panel lateral de miembros o contacto</label>
                            </fieldset>
                            <button type="submit" class="btn-main">Guardar apariencia</button>
                            <p class="prototype-note" id="appearance-save-status" role="status" aria-live="polite"></p>
                        </form>
                    </div>

                </section>
            </div>
        </div>
    </main>

    <footer class="site-footer" data-site-footer aria-label="Pie de página de Corebite"></footer>
    <script src="../Media/JS/site-footer.js" defer></script>
</body>
</html>
