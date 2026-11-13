<?php
declare(strict_types=1);

function corebite_not_found(): bool
{
    http_response_code(404);
    $accept = (string)($_SERVER['HTTP_ACCEPT'] ?? '');
    if (str_contains($accept, 'text/html')) {
        header('Content-Type: text/html; charset=utf-8');
        $errorCode = 404;
        require __DIR__ . '/error-page.php';
    } else {
        header('Content-Type: text/plain; charset=utf-8');
        echo '404 Not Found';
    }
    return true;
}

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$path = rawurldecode(is_string($path) ? $path : '/');
$path = str_replace('\\', '/', $path);

if ($path === '/') {
    header('Content-Type: text/html; charset=utf-8');
    readfile(__DIR__ . '/Index.html');
    return true;
}

$root = realpath(__DIR__);
$target = realpath(__DIR__ . DIRECTORY_SEPARATOR . ltrim($path, '/'));
if ($root === false || $target === false || !str_starts_with($target, $root . DIRECTORY_SEPARATOR) || !is_file($target)) {
    return corebite_not_found();
}

$relative = substr($target, strlen($root) + 1);
$parts = explode('/', str_replace('\\', '/', $relative));
if (in_array('corebite-data', $parts, true) || in_array('.git', $parts, true) || in_array('.vscode', $parts, true)) {
    return corebite_not_found();
}

return false;
