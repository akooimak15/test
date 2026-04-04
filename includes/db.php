<?php
// ============================================================
// db.php — SQLite接続・初期化
// ============================================================

require_once __DIR__ . '/config.php';

function get_db(): PDO {
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $dir = dirname(DB_PATH);
    if (!is_dir($dir)) mkdir($dir, 0750, true);

    $pdo = new PDO('sqlite:' . DB_PATH, null, null, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // WALモードで同時アクセス耐性を向上
    $pdo->exec('PRAGMA journal_mode=WAL;');
    $pdo->exec('PRAGMA foreign_keys=ON;');

    init_db($pdo);
    return $pdo;
}

function init_db(PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id       INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT    NOT NULL UNIQUE,
            password TEXT    NOT NULL,
            created_at TEXT DEFAULT (datetime('now','localtime'))
        );

        CREATE TABLE IF NOT EXISTS articles (
            id         INTEGER PRIMARY KEY AUTOINCREMENT,
            title      TEXT    NOT NULL,
            slug       TEXT    NOT NULL UNIQUE,
            body       TEXT    NOT NULL,
            tags       TEXT    DEFAULT '',
            status     TEXT    NOT NULL DEFAULT 'draft',  -- 'draft' | 'published'
            created_at TEXT    DEFAULT (datetime('now','localtime')),
            updated_at TEXT    DEFAULT (datetime('now','localtime'))
        );

        CREATE INDEX IF NOT EXISTS idx_articles_status ON articles(status);
        CREATE INDEX IF NOT EXISTS idx_articles_slug   ON articles(slug);
    ");

    // 管理者が未登録なら初期アカウントを作成
    // ★ 初回起動後に必ずパスワードを変更してください ★
    $count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ((int)$count === 0) {
        $hash = password_hash('changeme123', PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute(['admin', $hash]);
    }
}
