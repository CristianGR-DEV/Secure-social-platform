<?php
declare(strict_types=1);
require_once __DIR__ . '/includes.php';
$account = require_login();

function profile_reply(int $status, array $payload): never
{
    $acceptsJson = str_contains((string)($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json');
    if (!$acceptsJson) {
        $state = $status >= 200 && $status < 300 ? 'saved' : 'error';
        header('Location: workspace.php?profile=' . $state . '#account');
        exit;
    }
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') profile_reply(405, ['ok' => false, 'message' => 'Solicitud no válida.']);
$csrf = $_POST['csrf'] ?? '';
if (!is_string($csrf) || !hash_equals((string)($_SESSION['csrf'] ?? ''), $csrf)) {
    profile_reply(403, ['ok' => false, 'message' => 'La sesión ha caducado. Recarga la página e inténtalo de nuevo.']);
}

$username = trim((string)($_POST['username'] ?? ''));
$usernameLength = preg_match_all('/./us', $username);
if ($usernameLength === false || $usernameLength < 4 || $usernameLength > 32 || !preg_match('/^[\p{L}\p{N}_ .-]+$/u', $username)) {
    profile_reply(422, ['ok' => false, 'message' => 'El nombre debe tener entre 4 y 32 caracteres y usar letras, números, espacios, puntos, guiones o guiones bajos.']);
}
$about = trim((string)($_POST['about'] ?? ''));
if (mb_strlen($about, 'UTF-8') > 300) profile_reply(422, ['ok' => false, 'message' => 'El texto «Sobre mí» no puede superar los 300 caracteres.']);
$activityText = trim((string)($_POST['activities'] ?? ''));
$activities = $activityText === '' ? [] : array_values(array_filter(array_map('trim', preg_split('/\R/u', $activityText) ?: []), static fn(string $line): bool => $line !== ''));
if (count($activities) > 3) profile_reply(422, ['ok' => false, 'message' => 'Puedes guardar hasta tres notas de actividad, una por línea.']);
foreach ($activities as $activity) {
    if (mb_strlen($activity, 'UTF-8') > 90) profile_reply(422, ['ok' => false, 'message' => 'Cada nota de actividad puede tener hasta 90 caracteres.']);
}

$upload = $_FILES['avatar'] ?? null;
$newImageFile = null;
$newImagePath = null;
if (is_array($upload) && ($upload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
    if (($upload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || !is_uploaded_file((string)($upload['tmp_name'] ?? ''))) {
        profile_reply(422, ['ok' => false, 'message' => 'No se pudo recibir la imagen. El tamaño máximo es de 5 MB.']);
    }
    if ((int)($upload['size'] ?? 0) > 5 * 1024 * 1024) profile_reply(422, ['ok' => false, 'message' => 'La imagen no puede superar los 5 MB.']);
    $imageInfo = @getimagesize((string)$upload['tmp_name']);
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file((string)$upload['tmp_name']);
    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if ($imageInfo === false || !isset($extensions[$mime]) || ($imageInfo[0] * $imageInfo[1]) > 20000000) {
        profile_reply(422, ['ok' => false, 'message' => 'Usa una imagen PNG, JPG o WebP válida de hasta 20 megapíxeles.']);
    }
    $directory = dirname(__DIR__) . '/Media/IMG/profiles';
    if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
        profile_reply(500, ['ok' => false, 'message' => 'No se pudo preparar el espacio para la imagen.']);
    }
    $newImageFile = bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
    $newImagePath = $directory . DIRECTORY_SEPARATOR . $newImageFile;
    if (!move_uploaded_file((string)$upload['tmp_name'], $newImagePath)) {
        profile_reply(500, ['ok' => false, 'message' => 'No se pudo guardar la imagen.']);
    }
    @chmod($newImagePath, 0644);
}

try {
    $result = with_accounts(static function (array &$accounts) use ($account, $username, $about, $activities, $newImageFile): array {
        $current = null;
        foreach ($accounts as $index => $entry) {
            if (($entry['id'] ?? null) === ($account['id'] ?? null)) $current = $index;
            elseif (strcasecmp((string)($entry['username'] ?? ''), $username) === 0) return ['ok' => false, 'reason' => 'username'];
        }
        if ($current === null) return ['ok' => false, 'reason' => 'account'];
        $oldImage = profile_image_file($accounts[$current]);
        $accounts[$current]['username'] = $username;
        $accounts[$current]['about'] = $about;
        $accounts[$current]['recent_activities'] = $activities;
        if ($newImageFile !== null) $accounts[$current]['profile_image'] = $newImageFile;
        return ['ok' => true, 'old_image' => $oldImage, 'profile' => $accounts[$current]];
    });
    if (!$result['ok']) {
        if ($newImagePath !== null) @unlink($newImagePath);
        if ($result['reason'] === 'username') profile_reply(409, ['ok' => false, 'message' => 'Ese nombre de usuario ya está en uso.']);
        profile_reply(404, ['ok' => false, 'message' => 'No se encontró la cuenta. Vuelve a iniciar sesión.']);
    }
    $profile = $result['profile'];
    $oldImage = $result['old_image'];
    if ($newImageFile !== null && $oldImage !== null && $oldImage !== $newImageFile) {
        $oldPath = dirname(__DIR__) . '/Media/IMG/profiles/' . $oldImage;
        if (is_file($oldPath)) @unlink($oldPath);
    }
    profile_reply(200, [
        'ok' => true,
        'message' => 'Perfil guardado.',
        'profile' => [
            'username' => $profile['username'],
            'initial' => mb_strtoupper(mb_substr($profile['username'], 0, 1, 'UTF-8'), 'UTF-8'),
            'about' => $profile['about'],
            'activities' => $profile['recent_activities'],
            'avatar' => profile_image_src($profile),
            'hasCustomAvatar' => profile_image_file($profile) !== null,
        ],
    ]);
} catch (Throwable $error) {
    if ($newImagePath !== null && is_file($newImagePath)) @unlink($newImagePath);
    error_log('Corebite profile update error: ' . $error->getMessage());
    profile_reply(500, ['ok' => false, 'message' => 'No se pudo guardar el perfil. Inténtalo de nuevo.']);
}
