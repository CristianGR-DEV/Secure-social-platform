<?php
declare(strict_types=1);

function start_corebite_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) return;
    $sessionDirectory = dirname(storage_path()) . DIRECTORY_SEPARATOR . 'sessions';
    if (!is_dir($sessionDirectory) && !mkdir($sessionDirectory, 0700, true) && !is_dir($sessionDirectory)) {
        throw new RuntimeException('No se pudo crear la carpeta privada de sesiones.');
    }
    ini_set('session.save_path', $sessionDirectory);
    ini_set('session.use_strict_mode', '1');
    session_set_cookie_params([
        'httponly' => true,
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'samesite' => 'Lax',
        'path' => '/',
    ]);
    session_start();
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

function h(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function storage_path(): string
{
    // Fuera de la carpeta pública del sitio para que accounts.json no sea descargable.
    $directory = getenv('COREBITE_DATA_DIR') ?: dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'corebite-data';
    if (!is_dir($directory) && !mkdir($directory, 0700, true) && !is_dir($directory)) {
        throw new RuntimeException('No se pudo crear la carpeta privada de datos.');
    }
    return $directory . DIRECTORY_SEPARATOR . 'accounts.json';
}

function with_accounts(callable $callback): mixed
{
    $path = storage_path();
    $lock = fopen($path . '.lock', 'c');
    if ($lock === false || !flock($lock, LOCK_EX)) throw new RuntimeException('No se pudo bloquear el archivo de cuentas.');
    try {
        $raw = is_file($path) ? file_get_contents($path) : false;
        $accounts = $raw === false || trim($raw) === '' ? [] : json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($accounts)) throw new RuntimeException('El archivo de cuentas JSON no tiene un formato válido.');
        $result = $callback($accounts);
        $json = json_encode($accounts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        if (file_put_contents($path, $json . PHP_EOL, LOCK_EX) === false) throw new RuntimeException('No se pudieron guardar los datos.');
        @chmod($path, 0600);
        return $result;
    } finally {
        flock($lock, LOCK_UN);
        fclose($lock);
    }
}

function require_login(): array
{
    start_corebite_session();
    $id = $_SESSION['account_id'] ?? null;
    if (!is_string($id)) { header('Location: sesion.php?error=login'); exit; }
    $account = with_accounts(static function (array &$accounts) use ($id): ?array {
        foreach ($accounts as $entry) if (($entry['id'] ?? null) === $id) return $entry;
        return null;
    });
    if ($account === null) { session_destroy(); header('Location: sesion.php?error=login'); exit; }
    return $account;
}
