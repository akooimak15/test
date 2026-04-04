<?php
// ============================================================
// admin/login.php — 管理者ログインページ
// ============================================================

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/articles.php';

// すでにログイン済みならダッシュボードへ
if (!empty($_SESSION['admin_id'])) {
    header('Location: /admin/');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'ユーザー名とパスワードを入力してください。';
    } elseif (!attempt_login($username, $password)) {
        // ブルートフォース対策：失敗時に少し待機
        sleep(1);
        $error = 'ユーザー名またはパスワードが正しくありません。';
    } else {
        header('Location: /admin/');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者ログイン | <?= e(SITE_NAME) ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/blog.css">
</head>
<body class="admin-body">

<div class="login-wrapper">
    <div class="login-card">

        <div class="login-logo">
            <h1><?= e(SITE_NAME) ?></h1>
            <p>管理者ページ</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/admin/login.php" novalidate>
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

            <div class="form-group">
                <label class="form-label" for="username">ユーザー名</label>
                <input
                    class="form-input"
                    type="text"
                    id="username"
                    name="username"
                    autocomplete="username"
                    required
                    autofocus
                    value="<?= e($_POST['username'] ?? '') ?>"
                >
            </div>

            <div class="form-group">
                <label class="form-label" for="password">パスワード</label>
                <input
                    class="form-input"
                    type="password"
                    id="password"
                    name="password"
                    autocomplete="current-password"
                    required
                >
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:13px;">
                ログイン
            </button>
        </form>

    </div>
</div>

</body>
</html>
