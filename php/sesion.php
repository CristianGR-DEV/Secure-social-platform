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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../Media/IMG/Logo.png" type="image/png">
    <link rel="stylesheet" href="../Media/CSS/estilo.css">
    <link rel="stylesheet" href="../Media/CSS/sesion.css">
    <title>Iniciar sesión o crear cuenta | Corebite</title>
</head>
<body class="container">
    <header>
        <a href="../Index.html"><div class="logo-box"><img src="../Media/IMG/Logo.png" alt="" height="48" width="48"><h1>Corebite</h1></div></a>
        <input type="checkbox" id="menu-check" aria-label="Abrir o cerrar el menú">
        <label for="menu-check" class="hamburger" aria-hidden="true" tabindex="-1"><span></span><span></span><span></span></label>
        <nav><div class="input"><a class="value ui-icon-button" href="../Index.html"><span class="svg-icon icon-house" aria-hidden="true"></span> Inicio</a><a class="value ui-icon-button" href="./sesion.php"><span class="svg-icon icon-user" aria-hidden="true"></span> Cuenta</a></div></nav>
    </header>
    <main class="auth-page">
        <div class="auth-wrapper">
            <input type="checkbox" id="toggle-auth" class="auth-state-check">
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
    <footer>
        <div class="cont-1"><img src="../Media/IMG/Logo.png" alt="Logo" class="logo-footer"><h3>Corebite</h3><p>© 2026 Corebite. Todos los derechos reservados.</p></div>
        <div class="cont-2"><p>Una plataforma de comunicación ligera para comunidades, actualmente en desarrollo.</p></div>
        <div class="cont-3"><div class="social-links"><a href="../terminos.html">Términos de servicio</a></div></div>
    </footer>
</body>
</html>
