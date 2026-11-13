<?php
declare(strict_types=1);
require_once __DIR__ . '/includes.php';
start_corebite_session();
if (!empty($_SESSION['account_id'])) { header('Location: workspace.php'); exit; }
$errors = [
    'login' => 'Usuario o contraseña incorrectos.',
    'exists' => 'El usuario o el correo ya está registrado.',
    'invalid' => 'Revisa los campos: algunos datos no son válidos o las contraseñas no coinciden.',
    'request' => 'La solicitud ha caducado. Recarga la página e inténtalo de nuevo.',
    'storage' => 'No se pudieron guardar los datos. Comprueba los permisos del servidor.',
];
$error = $errors[$_GET['error'] ?? ''] ?? '';
$startInRegistration = ($_GET['mode'] ?? '') === 'register';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../Media/SVG/corebite-mark.svg?v=20261112" type="image/svg+xml">
    <link rel="stylesheet" href="../Media/CSS/estilo.css?v=20261113-scrollbars1">
    <link rel="stylesheet" href="../Media/CSS/sesion.css">
    <link rel="stylesheet" href="../Media/CSS/auth-landing.css">
    <link rel="stylesheet" href="../Media/CSS/site-pages.css">
    <title>Iniciar sesión o crear cuenta | Corebite</title>
    <link rel="stylesheet" href="../Media/CSS/site-footer.css?v=20261112-footer3">
    <link rel="stylesheet" href="../Media/CSS/site-cursor.css?v=20261112-cursor2">
    <link rel="stylesheet" href="../Media/CSS/design-system.css?v=20261113-design12">
</head>
<body class="container auth-landing site-shell">
    <canvas class="ascii-background" data-ascii-background aria-hidden="true"></canvas>
    <header class="landing-header">
  <a class="brand" href="../Index.html" aria-label="Corebite, volver al landing"><img src="../Media/SVG/corebite-mark.svg?v=20261112" alt="" width="42" height="42"><span>corebite</span></a>
  <div class="landing-actions"><a class="login-link" href="./sesion.php">Iniciar sesión</a><a class="button button-small" href="./sesion.php?mode=register">Crear cuenta <span aria-hidden="true">↗</span></a></div>
</header>
    <main class="auth-page">
        <div class="auth-wrapper">
            <pre class="ascii-art ascii-form" aria-hidden="true"> [o]---[o]
   \   /
    \_/
    [c]</pre>
            <input type="checkbox" id="toggle-auth" class="auth-state-check" <?= $startInRegistration ? 'checked' : '' ?>>
            <div class="auth-card">
                <?php if ($error !== ''): ?><p class="prototype-note" role="alert"><?= h($error) ?></p><?php endif; ?>
                <div class="form-content login-form">
                    <h2 data-ascii-reveal>Iniciar Sesión</h2>
                    <form action="auth.php" method="post">
                        <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>">
                        <input type="hidden" name="action" value="login">
                        <label for="login-user">Nombre de Usuario</label>
                        <input type="text" id="login-user" name="username" placeholder="Tu usuario" autocomplete="username" required>
                        <label for="login-pass">Contraseña</label>
                        <div class="password-field">
                            <input type="password" id="login-pass" name="password" placeholder="••••••••" autocomplete="current-password" required data-password-input>
                            <button type="button" class="password-toggle" data-password-toggle aria-label="Mostrar contraseña" aria-pressed="false">
                                <svg data-eye-open viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg data-eye-closed viewBox="0 0 24 24" aria-hidden="true" focusable="false" hidden><path d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8"/><path d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.4 0 10 7 10 7a15 15 0 0 1-3.2 3.9M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7c1.2 0 2.3-.2 3.3-.6"/></svg>
                            </button>
                        </div>
                        <button type="submit" class="btn-main">Iniciar sesión</button>
                    </form>
                </div>
                <div class="form-content register-form">
                    <h2 data-ascii-reveal>Crear Cuenta</h2>
                    <form action="auth.php" method="post">
                        <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>">
                        <input type="hidden" name="action" value="register">
                        <fieldset><legend>Datos de Usuario</legend>
                            <label for="username">Nombre de Usuario:</label>
                            <input type="text" id="username" name="username" placeholder="Mínimo 4 caracteres" autocomplete="username" required minlength="4" maxlength="32">
                            <label for="email">Correo Electrónico:</label>
                            <input type="email" id="email" name="email" placeholder="correo@ejemplo.com" autocomplete="email" required>
                        </fieldset>
                        <fieldset><legend>Seguridad</legend>
                            <label for="password">Contraseña:</label>
                            <div class="password-field">
                                <input type="password" id="password" name="password" placeholder="Mínimo 8 caracteres" autocomplete="new-password" required minlength="8" data-password-input>
                                <button type="button" class="password-toggle" data-password-toggle aria-label="Mostrar contraseña" aria-pressed="false">
                                    <svg data-eye-open viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <svg data-eye-closed viewBox="0 0 24 24" aria-hidden="true" focusable="false" hidden><path d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8"/><path d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.4 0 10 7 10 7a15 15 0 0 1-3.2 3.9M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7c1.2 0 2.3-.2 3.3-.6"/></svg>
                                </button>
                            </div>
                            <label for="confirm-password">Confirmar Contraseña:</label>
                            <div class="password-field">
                                <input type="password" id="confirm-password" name="confirm-password" placeholder="Repite tu contraseña" autocomplete="new-password" required minlength="8" data-password-input>
                                <button type="button" class="password-toggle" data-password-toggle aria-label="Mostrar contraseña" aria-pressed="false">
                                    <svg data-eye-open viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <svg data-eye-closed viewBox="0 0 24 24" aria-hidden="true" focusable="false" hidden><path d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8"/><path d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.4 0 10 7 10 7a15 15 0 0 1-3.2 3.9M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7c1.2 0 2.3-.2 3.3-.6"/></svg>
                                </button>
                            </div>
                        </fieldset>
                        <fieldset><legend>Contacto y Preferencias</legend>
                            <label for="phone">Número de Teléfono (Opcional):</label>
                            <input type="tel" id="phone" name="phone" placeholder="+34 000 000 000" pattern="\+?[0-9\s-]{9,18}">
                            <label for="birthdate-trigger">Fecha de Nacimiento:</label>
                            <div class="birthdate-picker" data-birthdate-picker>
                                <input type="hidden" id="birthdate" name="birthdate" value="">
                                <button type="button" id="birthdate-trigger" class="date-picker-trigger" data-calendar-toggle aria-haspopup="dialog" aria-expanded="false" aria-controls="birthdate-calendar">
                                    <span data-date-value>Selecciona una fecha</span>
                                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>
                                </button>
                                <div class="date-picker-popover" id="birthdate-calendar" data-calendar-popover popover="manual" role="dialog" aria-label="Selecciona tu fecha de nacimiento">
                                    <div class="date-picker-heading">
                                        <button type="button" class="calendar-nav" data-calendar-prev aria-label="Mes anterior"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg></button>
                                        <div class="calendar-period">
                                            <div class="calendar-select" data-calendar-select="month">
                                                <button type="button" class="calendar-select-trigger" data-select-toggle aria-haspopup="listbox" aria-expanded="false"><span data-select-value></span><svg viewBox="0 0 16 16" aria-hidden="true"><path d="m4 6 4 4 4-4"/></svg></button>
                                                <div class="calendar-select-menu" data-select-menu role="listbox" aria-label="Mes" hidden></div>
                                            </div>
                                            <div class="calendar-select" data-calendar-select="year">
                                                <button type="button" class="calendar-select-trigger" data-select-toggle aria-haspopup="listbox" aria-expanded="false"><span data-select-value></span><svg viewBox="0 0 16 16" aria-hidden="true"><path d="m4 6 4 4 4-4"/></svg></button>
                                                <div class="calendar-select-menu calendar-year-menu" data-select-menu role="listbox" aria-label="Año" hidden></div>
                                            </div>
                                        </div>
                                        <button type="button" class="calendar-nav" data-calendar-next aria-label="Mes siguiente"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg></button>
                                    </div>
                                    <div class="calendar-weekdays" aria-hidden="true"><span>L</span><span>M</span><span>X</span><span>J</span><span>V</span><span>S</span><span>D</span></div>
                                    <div class="calendar-days" data-calendar-days role="grid" aria-label="Días del mes"></div>
                                    <div class="calendar-footer"><button type="button" data-calendar-clear>Limpiar fecha</button></div>
                                </div>
                            </div>
                            <div class="checkbox-group">
                                <input type="checkbox" id="terms" name="terms" value="accepted" required>
                                <label for="terms">Acepto los <a href="../terminos.html">Términos y Condiciones</a></label>
                            </div>
                        </fieldset>
                        <button type="submit" class="btn-main">Crear cuenta</button>
                    </form>
                </div>
                <div class="auth-footer"><label for="toggle-auth" class="toggle-label"><span class="text-to-register">¿No tienes cuenta? Crear cuenta</span><span class="text-to-login">¿Ya tienes cuenta? Iniciar sesión</span></label></div>
            </div>
        </div>
    </main>
    <footer class="site-footer" data-site-footer aria-label="Pie de página de Corebite"></footer>
<script src="../Media/JS/ascii-background.js?v=20261113-3" defer></script>
    <script src="../Media/JS/ascii-dissolve.js?v=20261113-3" defer></script>
    <script src="../Media/JS/site-footer.js" defer></script>
    <script src="../Media/JS/password-visibility.js?v=20261113-1" defer></script>
    <script src="../Media/JS/birthdate-picker.js?v=20261113-2" defer></script>
</body>
</html>
