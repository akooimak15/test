<?php
// ============================================================
// articles.php — 記事CRUD関数群
// ============================================================

require_once __DIR__ . '/db.php';

/**
 * タイトルからURLスラッグを生成
 */
function make_slug(string $title): string {
    // 英数字・ハイフン以外を除去してスラッグ化
    $slug = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $title);
    $slug = preg_replace('/[\s]+/', '-', trim($slug));
    $slug = strtolower($slug);
    if (empty($slug)) {
        $slug = 'article-' . time();
    }
    return $slug . '-' . substr(md5($title . microtime()), 0, 6);
}

/**
 * 公開記事一覧を取得（ページング対応）
 */
function get_published_articles(int $page = 1, int $per_page = 10): array {
    $db     = get_db();
    $offset = ($page - 1) * $per_page;
    $stmt   = $db->prepare("
        SELECT id, title, slug, tags, created_at,
               substr(body, 1, 200) AS excerpt
        FROM   articles
        WHERE  status = 'published'
        ORDER  BY created_at DESC
        LIMIT  ? OFFSET ?
    ");
    $stmt->execute([$per_page, $offset]);
    return $stmt->fetchAll();
}

/**
 * 公開記事の総数
 */
function count_published_articles(): int {
    $db = get_db();
    return (int)$db->query("SELECT COUNT(*) FROM articles WHERE status='published'")->fetchColumn();
}

/**
 * スラッグで記事を1件取得（公開のみ）
 */
function get_article_by_slug(string $slug): ?array {
    $db   = get_db();
    $stmt = $db->prepare("SELECT * FROM articles WHERE slug = ? AND status = 'published' LIMIT 1");
    $stmt->execute([$slug]);
    $row  = $stmt->fetch();
    return $row ?: null;
}

/**
 * IDで記事を取得（管理画面用：下書き含む）
 */
function get_article_by_id(int $id): ?array {
    $db   = get_db();
    $stmt = $db->prepare("SELECT * FROM articles WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $row  = $stmt->fetch();
    return $row ?: null;
}

/**
 * 全記事一覧（管理画面用）
 */
function get_all_articles(): array {
    $db = get_db();
    return $db->query("SELECT id, title, status, tags, created_at, updated_at FROM articles ORDER BY created_at DESC")->fetchAll();
}

/**
 * 記事を新規作成
 */
function create_article(string $title, string $body, string $tags, string $status): int {
    $db   = get_db();
    $slug = make_slug($title);
    $stmt = $db->prepare("
        INSERT INTO articles (title, slug, body, tags, status)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$title, $slug, $body, $tags, $status]);
    return (int)$db->lastInsertId();
}

/**
 * 記事を更新
 */
function update_article(int $id, string $title, string $body, string $tags, string $status): void {
    $db   = get_db();
    $stmt = $db->prepare("
        UPDATE articles
        SET    title = ?, body = ?, tags = ?, status = ?,
               updated_at = datetime('now','localtime')
        WHERE  id = ?
    ");
    $stmt->execute([$title, $body, $tags, $status, $id]);
}

/**
 * 記事を削除
 */
function delete_article(int $id): void {
    $db   = get_db();
    $stmt = $db->prepare("DELETE FROM articles WHERE id = ?");
    $stmt->execute([$id]);
}

/**
 * Markdownをシンプルなパース（外部ライブラリ不要の簡易版）
 * Termux環境での依存を最小限に抑えるため自前実装
 */
function parse_markdown(string $text): string {
    $text = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

    // コードブロック（```...```）を先に処理
    $text = preg_replace_callback('/```(\w*)\n?(.*?)```/s', function($m) {
        $lang = htmlspecialchars($m[1]);
        $code = $m[2];
        $attr = $lang ? " class=\"language-{$lang}\" data-lang=\"{$lang}\"" : '';
        return "<pre><code{$attr}>{$code}</code></pre>";
    }, $text);

    // インラインコード
    $text = preg_replace('/`([^`]+)`/', '<code>$1</code>', $text);

    // 見出し
    $text = preg_replace('/^#{6}\s(.+)$/m', '<h6>$1</h6>', $text);
    $text = preg_replace('/^#{5}\s(.+)$/m', '<h5>$1</h5>', $text);
    $text = preg_replace('/^#{4}\s(.+)$/m', '<h4>$1</h4>', $text);
    $text = preg_replace('/^#{3}\s(.+)$/m', '<h3>$1</h3>', $text);
    $text = preg_replace('/^#{2}\s(.+)$/m', '<h2>$1</h2>', $text);
    $text = preg_replace('/^#{1}\s(.+)$/m', '<h1>$1</h1>', $text);

    // 太字・斜体
    $text = preg_replace('/\*\*\*(.+?)\*\*\*/', '<strong><em>$1</em></strong>', $text);
    $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);
    $text = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $text);

    // リンク
    $text = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2" target="_blank" rel="noopener">$1</a>', $text);

    // 水平線
    $text = preg_replace('/^---$/m', '<hr>', $text);

    // 箇条書き
    $text = preg_replace('/^[-*]\s(.+)$/m', '<li>$1</li>', $text);
    $text = preg_replace('/(<li>.*<\/li>)/s', '<ul>$1</ul>', $text);

    // 番号付きリスト
    $text = preg_replace('/^\d+\.\s(.+)$/m', '<li>$1</li>', $text);

    // 段落（空行で区切られたテキスト）
    $paragraphs = preg_split('/\n{2,}/', $text);
    $result = [];
    foreach ($paragraphs as $p) {
        $p = trim($p);
        if (empty($p)) continue;
        // すでにブロック要素の場合はそのまま
        if (preg_match('/^<(h[1-6]|ul|ol|li|pre|hr|blockquote)/', $p)) {
            $result[] = $p;
        } else {
            $p = nl2br($p);
            $result[] = "<p>{$p}</p>";
        }
    }

    return implode("\n", $result);
}

/**
 * タグ文字列を配列に変換
 */
function parse_tags(string $tags): array {
    return array_filter(array_map('trim', explode(',', $tags)));
}

/**
 * XSS対策のエスケープ
 */
function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
