#!/usr/bin/env php
<?php
// ============================================================
// admin/change_password.php — パスワード変更CLIツール
// 使い方: php admin/change_password.php
// ============================================================

define('SITE_NAME', 'Portfolio Blog');
define('SITE_AUTHOR', 'Admin');
define('SITE_DESC', '');
define('DB_PATH', __DIR__ . '/../data/blog.db');

ini_set('display_errors', 1);
date_default_timezone_set('Asia/Tokyo');

session_start();

require_once __DIR__ . '/../includes/db.php';

if (php_sapi_name() !== 'cli') {
    die("このスクリプトはCLIからのみ実行できます。\n");
}

echo "=== パスワード変更ツール ===\n\n";

echo "新しいパスワードを入力してください（8文字以上推奨）: ";
$password = trim(fgets(STDIN));

if (strlen($password) < 6) {
    die("エラー: パスワードは6文字以上にしてください。\n");
}

echo "確認のため、もう一度入力してください: ";
$confirm = trim(fgets(STDIN));

if ($password !== $confirm) {
    die("エラー: パスワードが一致しません。\n");
}

$db   = get_db();
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
$stmt = $db->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
$stmt->execute([$hash]);

if ($stmt->rowCount() > 0) {
    echo "\n✅ パスワードを変更しました。\n";
} else {
    echo "\n❌ パスワードの変更に失敗しました（adminユーザーが見つかりません）。\n";
}
