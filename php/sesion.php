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
    <link rel="stylesheet" href="../Media/CSS/estilo.css">
    <link rel="stylesheet" href="../Media/CSS/sesion.css">
    <link rel="stylesheet" href="../Media/CSS/auth-landing.css">
    <link rel="stylesheet" href="../Media/CSS/site-pages.css">
    <title>Iniciar sesión o crear cuenta | Corebite</title>
    <link rel="stylesheet" href="../Media/CSS/site-footer.css?v=20261112-footer3">
    <link rel="stylesheet" href="../Media/CSS/site-cursor.css?v=20261112-cursor2">
</head>
<body class="container auth-landing site-shell">
    <header class="landing-header">
  <a class="brand" href="../Index.html" aria-label="Corebite, volver al landing"><img src="../Media/SVG/corebite-mark.svg?v=20261112" alt="" width="42" height="42"><span>corebite</span></a>
  <div class="landing-actions"><a class="login-link" href="./sesion.php">Iniciar sesión</a><a class="button button-small" href="./sesion.php?mode=register">Crear cuenta <span aria-hidden="true">↗</span></a></div>
</header>
    <main class="auth-page">
        <div class="auth-wrapper">
            <input type="checkbox" id="toggle-auth" class="auth-state-check" <?= $startInRegistration ? 'checked' : '' ?>>
            <div class="auth-card">
                <?php if ($error !== ''): ?><p class="prototype-note" role="alert"><?= h($error) ?></p><?php endif; ?>
                <div class="form-content login-form">
                    <h2>Iniciar Sesión</h2>
                    <form action="auth.php" method="post">
                        <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>">
                        <input type="hidden" name="action" value="login">
                        <label for="login-user">Nombre de Usuario</label>
                        <input type="text" id="login-user" name="username" placeholder="Tu usuario" autocomplete="username" required>
                        <label for="login-pass">Contraseña</label>
                        <input type="password" id="login-pass" name="password" placeholder="••••••••" autocomplete="current-password" required>
                        <button type="submit" class="btn-main">Iniciar sesión</button>
                    </form>
                </div>
                <div class="form-content register-form">
                    <h2>Crear Cuenta</h2>
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
                            <input type="password" id="password" name="password" placeholder="Mínimo 8 caracteres" autocomplete="new-password" required minlength="8">
                            <label for="confirm-password">Confirmar Contraseña:</label>
                            <input type="password" id="confirm-password" name="confirm-password" placeholder="Repite tu contraseña" autocomplete="new-password" required minlength="8">
                        </fieldset>
                        <fieldset><legend>Contacto y Preferencias</legend>
                            <label for="phone">Número de Teléfono (Opcional):</label>
                            <input type="tel" id="phone" name="phone" placeholder="+34 000 000 000" pattern="\+?[0-9\s-]{9,18}">
                            <label for="birthdate">Fecha de Nacimiento:</label>
                            <input type="date" id="birthdate" name="birthdate">
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
    <script src="../Media/JS/site-footer.js" defer></script>
</body>
</html>
