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
 *
 * @param ?string $q   フリーワード（タイトル/本文/タグ）
 * @param ?string $tag タグ（単一）
 */
function get_published_articles(int $page = 1, int $per_page = 10, ?string $q = null, ?string $tag = null): array {
    $db     = get_db();
    $offset = ($page - 1) * $per_page;

    $where  = ["status = 'published'"];
    $params = [];

    $q = $q !== null ? trim($q) : '';
    if ($q !== '') {
        $like = '%' . escape_like($q) . '%';
        $where[] = "(title LIKE ? ESCAPE '\\' OR body LIKE ? ESCAPE '\\' OR tags LIKE ? ESCAPE '\\')";
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
    }

    $tag = $tag !== null ? normalize_tag($tag) : '';
    if ($tag !== '') {
        $where[] = "(',' || replace(tags, ' ', '') || ',') LIKE ?";
        $params[] = '%,' . $tag . ',%';
    }

    $sql = "
        SELECT id, title, slug, tags, created_at,
               substr(body, 1, 200) AS excerpt
        FROM   articles
        WHERE  " . implode(' AND ', $where) . "
        ORDER  BY created_at DESC
        LIMIT  ? OFFSET ?
    ";

    $stmt = $db->prepare($sql);
    $params[] = $per_page;
    $params[] = $offset;
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * 公開記事の総数
 *
 * @param ?string $q   フリーワード（タイトル/本文/タグ）
 * @param ?string $tag タグ（単一）
 */
function count_published_articles(?string $q = null, ?string $tag = null): int {
    $db = get_db();

    $where  = ["status = 'published'"];
    $params = [];

    $q = $q !== null ? trim($q) : '';
    if ($q !== '') {
        $like = '%' . escape_like($q) . '%';
        $where[] = "(title LIKE ? ESCAPE '\\' OR body LIKE ? ESCAPE '\\' OR tags LIKE ? ESCAPE '\\')";
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
    }

    $tag = $tag !== null ? normalize_tag($tag) : '';
    if ($tag !== '') {
        $where[] = "(',' || replace(tags, ' ', '') || ',') LIKE ?";
        $params[] = '%,' . $tag . ',%';
    }

    $stmt = $db->prepare("SELECT COUNT(*) FROM articles WHERE " . implode(' AND ', $where));
    $stmt->execute($params);
    return (int)$stmt->fetchColumn();
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
    $has_toc = preg_match('/^\s*\[{1,2}toc\]{1,2}\s*$/im', $text) === 1;
    if ($has_toc) {
        $text = preg_replace('/^\s*\[{1,2}toc\]{1,2}\s*$/im', "\n\n[[TOC]]\n\n", $text);
    }

    $code_blocks = [];
    $text = preg_replace_callback('/```(\w*)\n?([\s\S]*?)```/m', function($m) use (&$code_blocks) {
        $idx = count($code_blocks);
        $code_blocks[] = ['lang' => (string)$m[1], 'code' => (string)$m[2]];
        return "\n\n[[CB{$idx}]]\n\n";
    }, $text);

    $text = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

    $headings = [];
    $used_ids = [];
    $text = preg_replace_callback('/^(#{1,6})\s(.+)$/m', function($m) use (&$headings, &$used_ids) {
        $level = strlen($m[1]);
        $title = trim($m[2]);

        $plain = trim(strip_tags($title));
        $plain_for_id = html_entity_decode($plain, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $id = make_heading_id($plain_for_id, $used_ids);

        $headings[] = ['level' => $level, 'title' => $plain, 'id' => $id];
        return "<h{$level} id=\"{$id}\">{$title}</h{$level}>";
    }, $text);

    // インラインコード
    $text = preg_replace('/`([^`]+)`/', '<code>$1</code>', $text);

    // 太字・斜体
    $text = preg_replace('/\*\*\*(.+?)\*\*\*/', '<strong><em>$1</em></strong>', $text);
    $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);
    $text = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $text);

    // 画像
    $text = preg_replace('/!\[([^\]]*)\]\(([^)]+)\)/', '<img src="$2" alt="$1">', $text);

    // リンク
    $text = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2" target="_blank" rel="noopener">$1</a>', $text);

    // 水平線
    $text = preg_replace('/^---$/m', '<hr>', $text);

    // 箇条書き
    $text = preg_replace('/^[-*]\s(.+)$/m', '<li>$1</li>', $text);
    $text = preg_replace_callback('/(?:^<li>.*<\/li>\s*)+/m', function($m) {
        return "<ul>{$m[0]}</ul>";
    }, $text);

    // 番号付きリスト
    $text = preg_replace('/^\d+\.\s(.+)$/m', '<li>$1</li>', $text);

    // 段落（空行で区切られたテキスト）
    $paragraphs = preg_split('/\n{2,}/', $text);
    $result = [];
    $toc_html = $has_toc ? build_toc_html($headings) : '';
    foreach ($paragraphs as $p) {
        $p = trim($p);
        if (empty($p)) continue;
        if (preg_match('/^\[\[CB(\d+)\]\]$/', $p, $m)) {
            $idx = (int)$m[1];
            $lang = $code_blocks[$idx]['lang'] ?? '';
            $code = $code_blocks[$idx]['code'] ?? '';
            $lang_attr = $lang !== '' ? htmlspecialchars($lang, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : '';
            $code_escaped = htmlspecialchars(rtrim($code, "\n"), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $attr = $lang_attr !== '' ? " class=\"language-{$lang_attr}\" data-lang=\"{$lang_attr}\"" : '';
            $result[] = "<pre><code{$attr}>{$code_escaped}</code></pre>";
            continue;
        }
        if ($p === '[[TOC]]') {
            if ($toc_html !== '') $result[] = $toc_html;
            continue;
        }
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

function make_heading_id(string $text, array &$used_ids): string {
    $t = mb_strtolower($text, 'UTF-8');
    $t = preg_replace('/[^\p{L}\p{N}]+/u', '-', $t);
    $t = trim($t, '-');
    if ($t === '') $t = 'section';

    $id = $t;
    $i = 2;
    while (isset($used_ids[$id])) {
        $id = $t . '-' . $i;
        $i++;
    }
    $used_ids[$id] = true;
    return $id;
}

function build_toc_html(array $headings): string {
    $toc = [];
    $current = -1;

    foreach ($headings as $h) {
        $level = (int)($h['level'] ?? 0);
        if ($level === 2) {
            $toc[] = ['id' => (string)$h['id'], 'title' => (string)$h['title'], 'children' => []];
            $current = count($toc) - 1;
        } elseif ($level === 3 && $current >= 0) {
            $toc[$current]['children'][] = ['id' => (string)$h['id'], 'title' => (string)$h['title']];
        }
    }

    if (empty($toc)) return '';

    $html = '<nav class="toc" aria-label="目次">';
    $html .= '<div class="toc-title">目次</div>';
    $html .= '<ul class="toc-list">';

    foreach ($toc as $h2) {
        $html .= '<li><a href="#' . $h2['id'] . '">' . $h2['title'] . '</a>';
        if (!empty($h2['children'])) {
            $html .= '<ul class="toc-sub">';
            foreach ($h2['children'] as $h3) {
                $html .= '<li><a href="#' . $h3['id'] . '">' . $h3['title'] . '</a></li>';
            }
            $html .= '</ul>';
        }
        $html .= '</li>';
    }

    $html .= '</ul></nav>';
    return $html;
}

/**
 * タグ文字列を配列に変換
 */
function parse_tags(?string $tags): array {
    if (!$tags) return [];
    return array_filter(array_map('trim', explode(',', $tags)));
}

/**
 * LIKE検索用にワイルドカード文字をエスケープ
 */
function escape_like(string $value): string {
    return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
}

/**
 * タグの比較用正規化
 */
function normalize_tag(string $tag): string {
    return trim(str_replace(' ', '', $tag));
}

/**
 * 公開記事に含まれるタグ一覧（ユニーク・昇順）
 */
function get_published_tags(): array {
    $db = get_db();
    $rows = $db->query("SELECT tags FROM articles WHERE status = 'published'")->fetchAll();

    $set = [];
    foreach ($rows as $row) {
        foreach (parse_tags($row['tags'] ?? '') as $tag) {
            $t = normalize_tag($tag);
            if ($t !== '') $set[$t] = true;
        }
    }

    $tags = array_keys($set);
    natcasesort($tags);
    return array_values($tags);
}
