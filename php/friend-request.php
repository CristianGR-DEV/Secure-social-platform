<?php
declare(strict_types=1);
require_once __DIR__ . '/includes.php';
start_corebite_session();
header('Content-Type: application/json; charset=utf-8');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
$sender = $_SESSION['account_id'] ?? null;
if (!is_string($sender)) { http_response_code(401); exit; }
$csrf = $_POST['csrf'] ?? null;
if (!is_string($csrf) || !hash_equals($_SESSION['csrf'], $csrf)) { http_response_code(403); exit; }
$target = $_POST['target'] ?? null;
if (!is_string($target) || $target === $sender) { http_response_code(400); exit; }
$result = with_accounts(static function (array &$accounts) use ($sender, $target): ?array {
    $ids = array_column($accounts, 'id');
    $from = array_search($sender, $ids, true);
    $to = array_search($target, $ids, true);
    if ($from === false || $to === false) return null;
    $incoming = in_array($target, $accounts[$from]['friend_requests'] ?? [], true);
    if (in_array($target, $accounts[$from]['friends'] ?? [], true)) return ['friends' => true];
    if (in_array($sender, $accounts[$to]['dismissed_requests'] ?? [], true)) return ['incoming' => false];
    if (!$incoming && !in_array($sender, $accounts[$to]['friend_requests'] ?? [], true)) {
        $accounts[$to]['friend_requests'][] = $sender;
        $accounts[$to]['request_keys'][$sender] = bin2hex(random_bytes(12));
    }
    return ['incoming' => $incoming];
});
if ($result === null) { http_response_code(404); exit; }
echo json_encode($result);
