<?php
declare(strict_types=1);

function corebite_render_error_page(int $code): never
{
    if (headers_sent()) exit;
    $errorCode = in_array($code, [404, 500], true) ? $code : 500;
    http_response_code($errorCode);
    header('Content-Type: text/html; charset=utf-8');
    require dirname(__DIR__) . '/error-page.php';
    exit;
}

set_exception_handler(static function (Throwable $exception): void {
    error_log('Corebite unhandled exception: ' . $exception->getMessage());
    corebite_render_error_page(500);
});

register_shutdown_function(static function (): void {
    $lastError = error_get_last();
    $fatalTypes = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR];
    if ($lastError === null || !in_array($lastError['type'], $fatalTypes, true)) return;
    error_log('Corebite fatal error: ' . $lastError['message']);
    corebite_render_error_page(500);
});

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

function profile_image_file(array $account): ?string
{
    $file = $account['profile_image'] ?? null;
    return is_string($file) && preg_match('/^[a-f0-9]{32}\.(?:png|jpg|webp)$/', $file) ? $file : null;
}

function profile_image_src(array $account, string $prefix = '../Media/IMG'): string
{
    $file = profile_image_file($account);
    return $file === null ? $prefix . '/default-avatar.png' : $prefix . '/profiles/' . rawurlencode($file);
}

function account_presence(array $account): string
{
    $presence = $account['presence'] ?? 'online';
    return is_string($presence) && in_array($presence, ['online', 'away', 'busy', 'offline'], true) ? $presence : 'online';
}

function presence_label(string $presence): string
{
    return match ($presence) {
        'away' => 'Ausente',
        'busy' => 'No molestar',
        'offline' => 'Invisible',
        default => 'En línea',
    };
}

function presence_class(string $presence): string
{
    return in_array($presence, ['away', 'busy', 'offline'], true) ? $presence : '';
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

function new_friend_code(array $used): string
{
    $start = random_int(0, 999999);
    for ($i = 0; $i < 1000000; $i++) {
        $code = sprintf('%06d', ($start + $i) % 1000000);
        if (!isset($used[$code])) return $code;
    }
    throw new RuntimeException('No quedan códigos disponibles.');
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
        $used = array_fill_keys(array_column($accounts, 'friend_code'), true);
        foreach ($accounts as &$entry) {
            if (!isset($entry['friend_code'])) {
                $entry['friend_code'] = new_friend_code($used);
                $used[$entry['friend_code']] = true;
            }
        }
        unset($entry);
        $result = $callback($accounts);
        $json = json_encode($accounts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        if ($raw === $json . PHP_EOL) return $result;
        if (file_put_contents($path, $json . PHP_EOL, LOCK_EX) === false) throw new RuntimeException('No se pudieron guardar los datos.');
        @chmod($path, 0600);
        return $result;
    } finally {
        flock($lock, LOCK_UN);
        fclose($lock);
    }
}

function dm_preview_history(array $account, array $friend): array
{
    $history = [];
    foreach ($account['dm_example_outbox'][$friend['id']] ?? [] as $message) {
        $history[] = $message + ['own' => true];
    }
    foreach ($friend['dm_example_outbox'][$account['id']] ?? [] as $message) {
        $history[] = $message + ['own' => false];
    }
    usort($history, static fn($a, $b) => $a['sentAt'] <=> $b['sentAt']);
    return $history;
}

function public_member_profile(array $entry): array
{
    return [
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
