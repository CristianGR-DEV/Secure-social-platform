<?php
declare(strict_types=1);
require_once __DIR__ . '/includes.php';
start_corebite_session();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
$user = $_SESSION['account_id'] ?? null;
if (!is_string($user)) { http_response_code(401); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
if (!is_string($_POST['csrf'] ?? null) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) { http_response_code(403); exit; }
$action = $_POST['action'] ?? 'list';
if (!in_array($action, ['list','seen','ignore','accept','reject'], true)) { http_response_code(400); exit; }
session_write_close();
$result = with_accounts(static function (array &$accounts) use ($user, $action): ?array {
    $lookup = array_column($accounts, null, 'id');
    $indices = array_flip(array_column($accounts, 'id'));
    if (!isset($indices[$user])) return null;
    $me = &$accounts[$indices[$user]];
    $pending = $me['friend_requests'] ?? [];
    $keys = $me['request_keys'] ?? [];
    if (in_array($action, ['ignore','accept','reject'], true)) {
        $other = $_POST['sender'] ?? null;
        if (!is_string($other) || !isset($indices[$other]) || !in_array($other, $pending, true)
            || ($_POST['key'] ?? '') !== ($keys[$other] ?? $other)) return null;
        $me['friend_requests'] = $pending = array_values(array_diff($pending, [$other]));
        unset($me['request_keys'][$other]);
        if ($action === 'reject') $me['dismissed_requests'][] = $other;
        if ($action === 'accept') {
            $peer = &$accounts[$indices[$other]];
            $me['friends'][] = $other;
            $peer['friends'][] = $user;
            $peer['friend_notices'][bin2hex(random_bytes(12))] = $user;
        }
    }
    $entries = [];
    $add = static function ($key, $sender, $kind) use (&$entries, $lookup) {
        if (!isset($lookup[$sender])) return;
        $a = $lookup[$sender];
        $entries[] = ['key'=>$key, 'sender'=>$sender, 'kind'=>$kind, 'name'=>$a['username'], 'avatar'=>profile_image_src($a)];
    };
    foreach ($pending as $sender) $add($keys[$sender] ?? $sender, $sender, 'request');
    foreach ($me['friend_notices'] ?? [] as $key=>$sender) $add($key, $sender, 'accepted');
    $current = array_column($entries, 'key');
    $seen = $me['seen_notices'] ?? [];
    if ($action === 'seen' && is_string($_POST['keys'] ?? null)) $seen = array_merge($seen, explode(',', $_POST['keys']));
    $me['seen_notices'] = array_values(array_intersect($current, $seen));
    unset($peer);
    $outgoing = [];
    foreach ($accounts as $peer) {
        if (in_array($user, $peer['friend_requests'] ?? [], true) || in_array($user, $peer['dismissed_requests'] ?? [], true)) $outgoing[] = $peer['id'];
    }
    $contacts = [];
    $histories = [];
    $counts = json_decode(is_string($_POST['dm_counts'] ?? null) ? $_POST['dm_counts'] : '{}', true);
    if (!is_array($counts)) $counts = [];
    foreach ($me['friends'] ?? [] as $id) {
        if ($id !== $user && isset($indices[$id]) && in_array($user, $accounts[$indices[$id]]['friends'] ?? [], true)) {
            $contact = $accounts[$indices[$id]];
            $contacts[] = public_member_profile($contact);
            $count = count($me['dm_example_outbox'][$id] ?? []) + count($contact['dm_example_outbox'][$user] ?? []);
            if ($count !== ($counts['account-' . $id] ?? 0)) {
                $histories['account-' . $id] = dm_preview_history($me, $contact);
            }
        }
    }
    return ['histories'=>$histories, 'contacts'=>$contacts, 'entries'=>$entries, 'unread'=>array_values(array_diff($current, $me['seen_notices'])), 'friends'=>$me['friends'] ?? [], 'outgoing'=>$outgoing];
});
if ($result === null) { http_response_code(409); exit; }
echo json_encode($result, JSON_THROW_ON_ERROR);
