<?php
// ============================================================
// auth.php — 認証ヘルパー
// ============================================================

require_once __DIR__ . '/db.php';

/**
 * ログイン済みかチェック。未ログインならリダイレクト。
 */
function require_login(): void {
    if (empty($_SESSION['admin_id'])) {
        header('Location: /admin/login.php');
        exit;
    }
}

/**
 * ユーザー名とパスワードで認証。成功時trueを返す。
 */
function attempt_login(string $username, string $password): bool {
    $db   = get_db();
    $stmt = $db->prepare("SELECT id, password FROM users WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        return false;
    }

    // セッション固定化攻撃対策：ログイン成功時にセッションIDを再生成
    session_regenerate_id(true);
    $_SESSION['admin_id'] = $user['id'];
    return true;
}

/**
 * ログアウト処理
 */
function logout(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

/**
 * CSRFトークン生成・検証
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die('不正なリクエストです。');
    }
}
