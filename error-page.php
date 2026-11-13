<?php
declare(strict_types=1);

if (!isset($errorCode)) {
    $requestedCode = filter_input(INPUT_GET, 'code', FILTER_VALIDATE_INT);
    $errorCode = in_array($requestedCode, [404, 500], true) ? $requestedCode : 404;
    http_response_code($errorCode);
    header('Content-Type: text/html; charset=utf-8');
}
$errorCode = in_array((int)$errorCode, [404, 500], true) ? (int)$errorCode : 500;
$isNotFound = $errorCode === 404;
$pageTitle = $isNotFound ? 'Esta página no está aquí.' : 'Algo salió mal.';
$pageMessage = $isNotFound
    ? 'No encontramos esta dirección. Revisa el enlace o vuelve al inicio.'
    : 'No pudimos completar tu solicitud por un error interno. Inténtalo de nuevo más tarde.';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#10141c">
    <meta name="robots" content="noindex, nofollow">
    <title><?= $errorCode ?> · Corebite</title>
    <link rel="icon" href="/Media/SVG/corebite-mark.svg?v=20261112" type="image/svg+xml">
    <link rel="stylesheet" href="/Media/CSS/estilo.css">
    <link rel="stylesheet" href="/Media/CSS/design-system.css?v=20261113-design8">
    <link rel="stylesheet" href="/Media/CSS/error-page.css?v=20261113-7">
</head>
<body class="container site-shell error-page">
    <canvas class="ascii-background" data-ascii-background="error" aria-hidden="true"></canvas>
    <main class="error-main" aria-labelledby="error-title">
        <p class="error-eyebrow">COREBITE <span>// ERROR <?= $errorCode ?></span></p>
        <h1 class="error-code" id="error-code" aria-label="Error <?= $errorCode ?>" data-ascii-reveal><?= $errorCode ?></h1>
        <div class="error-loader" aria-hidden="true">
            <span class="error-loader-label">BUSCANDO RUTA</span>
            <pre class="error-scan" data-ascii-scan>█······</pre>
        </div>
        <h2 id="error-title"><?= $pageTitle ?></h2>
        <p class="error-message"><?= $pageMessage ?></p>
        <a class="button error-home" href="/Index.html"><span class="error-home-arrow" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><path d="M19 12H5m0 0 7 7m-7-7 7-7"/></svg></span><span class="error-home-label">Volver al inicio</span></a>
        <p class="error-signature" aria-hidden="true">TU GENTE, SIEMPRE CERCA.</p>
    </main>
    <script src="/Media/JS/ascii-background.js?v=20261113-2" defer></script>
    <script src="/Media/JS/ascii-dissolve.js?v=20261113-4" defer></script>
    <script src="/Media/JS/error-page.js?v=20261113-6" defer></script>
</body>
</html>
