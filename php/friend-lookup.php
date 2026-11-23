<?php
declare(strict_types=1);
require_once __DIR__ . '/includes.php';
start_corebite_session();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
$user = $_SESSION['account_id'] ?? null;
if (!is_string($user)) { http_response_code(401); exit; }
if (!is_string($_POST['csrf'] ?? null) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) { http_response_code(403); exit; }
$tag = $_POST['tag'] ?? null;
if (!is_string($tag) || !preg_match('/^([^#]{1,32})#([0-9]{6})$/uD', $tag, $parts)) { http_response_code(400); exit; }
$now = time();
$attempts = array_filter($_SESSION['friend_lookups'] ?? [], static fn($t) => $t > $now - 60);
if (count($attempts) >= 30) { http_response_code(429); exit; }
$attempts[] = $now;
$_SESSION['friend_lookups'] = array_values($attempts);
session_write_close();
$id = with_accounts(static function (array &$accounts) use ($parts, $user): ?string {
    foreach ($accounts as $entry) {
        if ($entry['id'] !== $user && $entry['friend_code'] === $parts[2]
            && mb_strtolower($entry['username'], 'UTF-8') === mb_strtolower($parts[1], 'UTF-8')) return $entry['id'];
    }
    return null;
});
echo json_encode(['id' => $id], JSON_THROW_ON_ERROR);
