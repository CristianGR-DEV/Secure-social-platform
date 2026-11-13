<?php
declare(strict_types=1);
require_once __DIR__ . '/includes.php';
start_corebite_session();

function fail(string $code): never { header('Location: sesion.php?error=' . rawurlencode($code)); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: sesion.php'); exit; }
$csrf = $_POST['csrf'] ?? '';
if (!is_string($csrf) || !hash_equals($_SESSION['csrf'], $csrf)) fail('request');
$action = $_POST['action'] ?? '';

try {
    if ($action === 'register') {
        $username = trim((string)($_POST['username'] ?? ''));
        $email = strtolower(trim((string)($_POST['email'] ?? '')));
        $password = (string)($_POST['password'] ?? '');
        $confirm = (string)($_POST['confirm-password'] ?? '');
        $phone = trim((string)($_POST['phone'] ?? ''));
        $birthdate = trim((string)($_POST['birthdate'] ?? ''));
        $usernameLength = preg_match_all('/./us', $username);
        if ($usernameLength === false || $usernameLength < 4 || $usernameLength > 32 || !preg_match('/^[\p{L}\p{N}_ .-]+$/u', $username)) fail('invalid');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) fail('invalid');
        if (strlen($password) < 8 || strlen($password) > 128 || $password !== $confirm) fail('invalid');
        if (($_POST['terms'] ?? '') !== 'accepted') fail('invalid');
        if ($phone !== '' && !preg_match('/^\+?[0-9\s-]{9,18}$/', $phone)) fail('invalid');
        if ($birthdate !== '') {
            $date = DateTime::createFromFormat('!Y-m-d', $birthdate);
            if (!$date || $date->format('Y-m-d') !== $birthdate) fail('invalid');
        }
        $id = bin2hex(random_bytes(16));
        $account = [
            'id' => $id, 'username' => $username, 'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'phone' => $phone, 'birthdate' => $birthdate,
            'created_at' => gmdate('c'),
        ];
        $created = with_accounts(static function (array &$accounts) use ($account, $username, $email): bool {
            foreach ($accounts as $entry) {
                if (strcasecmp((string)($entry['username'] ?? ''), $username) === 0 || strcasecmp((string)($entry['email'] ?? ''), $email) === 0) return false;
            }
            $accounts[] = $account;
            return true;
        });
        if (!$created) fail('exists');
        session_regenerate_id(true);
        $_SESSION['account_id'] = $id;
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
        header('Location: workspace.php'); exit;
    }

    if ($action === 'login') {
        $username = trim((string)($_POST['username'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        $account = with_accounts(static function (array &$accounts) use ($username): ?array {
            foreach ($accounts as $entry) if (strcasecmp((string)($entry['username'] ?? ''), $username) === 0) return $entry;
            return null;
        });
        if ($account === null || !password_verify($password, (string)($account['password_hash'] ?? ''))) fail('login');
        session_regenerate_id(true);
        $_SESSION['account_id'] = $account['id'];
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
        header('Location: workspace.php'); exit;
    }

    if ($action === 'logout') {
        unset($_SESSION['account_id']);
        session_regenerate_id(true);
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
        header('Location: sesion.php'); exit;
    }
} catch (Throwable $error) {
    error_log('Corebite auth error: ' . $error->getMessage());
    corebite_render_error_page(500);
}
fail('request');
