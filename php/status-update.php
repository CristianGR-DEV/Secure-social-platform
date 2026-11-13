<?php
declare(strict_types=1);
require_once __DIR__ . '/includes.php';
$account = require_login();

function presence_reply(int $status, array $payload): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') presence_reply(405, ['ok' => false, 'message' => 'Solicitud no válida.']);
$csrf = $_POST['csrf'] ?? '';
if (!is_string($csrf) || !hash_equals((string)($_SESSION['csrf'] ?? ''), $csrf)) {
    presence_reply(403, ['ok' => false, 'message' => 'La sesión ha caducado. Recarga la página e inténtalo de nuevo.']);
}
$status = $_POST['status'] ?? '';
if (!is_string($status) || !in_array($status, ['online', 'away', 'busy', 'offline'], true)) {
    presence_reply(422, ['ok' => false, 'message' => 'El estado seleccionado no es válido.']);
}
try {
    $updated = with_accounts(static function (array &$accounts) use ($account, $status): bool {
        foreach ($accounts as &$entry) {
            if (($entry['id'] ?? null) !== ($account['id'] ?? null)) continue;
            $entry['presence'] = $status;
            unset($entry);
            return true;
        }
        unset($entry);
        return false;
    });
    if (!$updated) presence_reply(404, ['ok' => false, 'message' => 'No se encontró la cuenta. Vuelve a iniciar sesión.']);
    presence_reply(200, ['ok' => true, 'status' => $status, 'label' => presence_label($status)]);
} catch (Throwable $error) {
    error_log('Corebite presence update error: ' . $error->getMessage());
    presence_reply(500, ['ok' => false, 'message' => 'No se pudo guardar el estado. Inténtalo de nuevo.']);
}
