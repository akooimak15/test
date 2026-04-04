<?php
// ============================================================
// admin/edit.php — 記事作成・編集ページ
// ============================================================

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/articles.php';

require_login();

$id      = (int)($_GET['id'] ?? 0);
$article = $id > 0 ? get_article_by_id($id) : null;
$is_new  = $article === null;
$errors  = [];

// フォーム送信処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $title  = trim($_POST['title']  ?? '');
    $body   = trim($_POST['body']   ?? '');
    $tags   = trim($_POST['tags']   ?? '');
    $status = in_array($_POST['status'] ?? '', ['draft', 'published'], true)
              ? $_POST['status']
              : 'draft';

    if (empty($title)) $errors[] = 'タイトルは必須です。';
    if (empty($body))  $errors[] = '本文は必須です。';

    if (empty($errors)) {
        if ($is_new) {
            $new_id = create_article($title, $body, $tags, $status);
            header("Location: /admin/?created=1");
        } else {
            update_article($id, $title, $body, $tags, $status);
            header("Location: /admin/?updated=1");
        }
        exit;
    }

    // バリデーションエラー時は入力値を保持
    $article = ['title' => $title, 'body' => $body, 'tags' => $tags, 'status' => $status];
}

$page_heading = $is_new ? '新規記事作成' : '記事を編集';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_heading) ?> | <?= e(SITE_NAME) ?></title>
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/blog.css">
    <style>
        /* エディタのツールバー */
        .editor-toolbar {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            padding: 10px 14px;
            background: var(--color-bg-sub);
            border: 1px solid var(--color-border);
            border-bottom: none;
            border-radius: var(--radius-sm) var(--radius-sm) 0 0;
        }
        .editor-toolbar button {
            padding: 5px 10px;
            font-size: 0.78rem;
            font-weight: 600;
            background: #fff;
            border: 1px solid var(--color-border);
            border-radius: 4px;
            cursor: pointer;
            color: var(--color-text);
            font-family: var(--font-sans);
            transition: all .15s;
        }
        .editor-toolbar button:hover {
            background: var(--color-primary);
            color: #fff;
            border-color: var(--color-primary);
        }
        .editor-area {
            border-radius: 0 0 var(--radius-sm) var(--radius-sm) !important;
        }
        /* プレビュー */
        .preview-wrap {
            background: var(--color-bg-sub);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-sm);
            padding: 24px;
            min-height: 200px;
        }
        /* タブ切替 */
        .tab-bar { display: flex; gap: 0; margin-bottom: 0; }
        .tab-btn {
            padding: 8px 18px;
            font-size: 0.85rem;
            font-weight: 600;
            background: none;
            border: 1px solid var(--color-border);
            border-bottom: none;
            cursor: pointer;
            color: var(--color-text-sub);
            font-family: var(--font-sans);
            transition: .15s;
        }
        .tab-btn:first-child { border-radius: var(--radius-sm) 0 0 0; }
        .tab-btn:last-child  { border-radius: 0 var(--radius-sm) 0 0; }
        .tab-btn.active { background: #fff; color: var(--color-text); border-bottom-color: #fff; }
        .tab-panel { display: none; }
        .tab-panel.active { display: block; }
    </style>
</head>
<body class="admin-body">

<header class="admin-header">
    <span class="logo">⚙ 管理画面</span>
    <a href="/admin/logout.php" class="logout">ログアウト</a>
</header>

<div class="admin-container">

    <div style="display:flex;align-items:center;gap:16px;margin-bottom:24px;flex-wrap:wrap;">
        <a href="/admin/" style="color:var(--color-text-sub);font-size:0.88rem;">← 一覧に戻る</a>
        <h1 class="admin-page-title" style="margin-bottom:0;"><?= e($page_heading) ?></h1>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $err): ?>
                <div>・<?= e($err) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/admin/edit.php<?= $id ? "?id={$id}" : '' ?>">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

        <div class="form-card">

            <!-- タイトル -->
            <div class="form-group">
                <label class="form-label" for="title">タイトル <span>*必須</span></label>
                <input
                    class="form-input"
                    type="text"
                    id="title"
                    name="title"
                    placeholder="記事タイトルを入力"
                    required
                    value="<?= e($article['title'] ?? '') ?>"
                >
            </div>

            <!-- 本文（Markdownエディタ） -->
            <div class="form-group">
                <label class="form-label">本文 <span>Markdown形式</span></label>

                <!-- タブ -->
                <div class="tab-bar">
                    <button type="button" class="tab-btn active" onclick="switchTab('write')">✏ 編集</button>
                    <button type="button" class="tab-btn"        onclick="switchTab('preview')">👁 プレビュー</button>
                </div>

                <!-- 編集タブ -->
                <div id="tab-write" class="tab-panel active">
                    <div class="editor-toolbar">
                        <button type="button" onclick="insertMd('**', '**')"><b>B</b></button>
                        <button type="button" onclick="insertMd('*', '*')"><i>I</i></button>
                        <button type="button" onclick="insertMd('`', '`')">Code</button>
                        <button type="button" onclick="insertMd('\n```\n', '\n```')">```Block</button>
                        <button type="button" onclick="insertLine('## ')">H2</button>
                        <button type="button" onclick="insertLine('### ')">H3</button>
                        <button type="button" onclick="insertLine('- ')">List</button>
                        <button type="button" onclick="insertLine('1. ')">1.</button>
                        <button type="button" onclick="insertLine('---')">HR</button>
                        <button type="button" onclick="insertMd('[', '](URL)')">Link</button>
                    </div>
                    <textarea
                        class="form-textarea editor-area"
                        id="body"
                        name="body"
                        placeholder="Markdown で本文を書いてください..."
                        required
                    ><?= e($article['body'] ?? '') ?></textarea>
                </div>

                <!-- プレビュータブ -->
                <div id="tab-preview" class="tab-panel">
                    <div class="preview-wrap article-body" id="preview-content">
                        <p style="color:var(--color-text-sub);font-style:italic;">プレビューを表示するには「プレビュー」タブをクリックしてください。</p>
                    </div>
                </div>

                <p class="form-hint">Markdownが使えます。コードブロックは ```lang で言語を指定できます。</p>
            </div>

            <!-- タグ -->
            <div class="form-group">
                <label class="form-label" for="tags">
                    タグ <span>カンマ区切りで複数入力可（例: PHP, SQLite, Termux）</span>
                </label>
                <input
                    class="form-input"
                    type="text"
                    id="tags"
                    name="tags"
                    placeholder="PHP, SQLite, Android"
                    value="<?= e($article['tags'] ?? '') ?>"
                >
            </div>

            <!-- 公開ステータス -->
            <div class="form-group">
                <label class="form-label" for="status">公開状態</label>
                <select class="form-select" id="status" name="status">
                    <option value="draft"
                        <?= ($article['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>
                        下書き（非公開）
                    </option>
                    <option value="published"
                        <?= ($article['status'] ?? '') === 'published' ? 'selected' : '' ?>>
                        公開
                    </option>
                </select>
            </div>

            <!-- 送信ボタン -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <?= $is_new ? '記事を作成' : '変更を保存' ?>
                </button>
                <a href="/admin/" class="btn btn-secondary">キャンセル</a>
                <?php if (!$is_new && ($article['status'] ?? '') === 'published'): ?>
                    <a href="/article.php?slug=<?= urlencode(get_article_by_id($id)['slug'] ?? '') ?>"
                       target="_blank" rel="noopener" class="btn btn-secondary">
                        公開ページを確認 ↗
                    </a>
                <?php endif; ?>
            </div>

        </div>
    </form>

</div>

<script>
// タブ切替
function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach((b, i) => {
        b.classList.toggle('active', (i === 0) === (tab === 'write'));
    });
    document.getElementById('tab-write').classList.toggle('active', tab === 'write');
    document.getElementById('tab-preview').classList.toggle('active', tab === 'preview');

    if (tab === 'preview') {
        const body = document.getElementById('body').value;
        // サーバーサイドパースの代わりに簡易クライアントパース
        document.getElementById('preview-content').innerHTML =
            body.trim() ? simpleMarkdown(body) : '<p style="color:var(--color-text-sub)">本文がありません</p>';
    }
}

// Markdownツールバー
function insertMd(before, after) {
    const ta  = document.getElementById('body');
    const s   = ta.selectionStart;
    const e   = ta.selectionEnd;
    const sel = ta.value.substring(s, e);
    const txt = before + sel + after;
    ta.setRangeText(txt, s, e, 'select');
    ta.focus();
    if (!sel) {
        ta.setSelectionRange(s + before.length, s + before.length);
    }
}

function insertLine(prefix) {
    const ta  = document.getElementById('body');
    const s   = ta.selectionStart;
    const lineStart = ta.value.lastIndexOf('\n', s - 1) + 1;
    ta.setRangeText(prefix, lineStart, lineStart, 'end');
    ta.focus();
}

// 簡易クライアントサイドMarkdownパーサー（プレビュー用）
function simpleMarkdown(text) {
    let h = text
        .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
        .replace(/```(\w*)\n?([\s\S]*?)```/g, (_,l,c) =>
            `<pre><code class="language-${l}">${c.trim()}</code></pre>`)
        .replace(/`([^`]+)`/g, '<code>$1</code>')
        .replace(/^#{6} (.+)$/gm,'<h6>$1</h6>')
        .replace(/^#{5} (.+)$/gm,'<h5>$1</h5>')
        .replace(/^#{4} (.+)$/gm,'<h4>$1</h4>')
        .replace(/^#{3} (.+)$/gm,'<h3>$1</h3>')
        .replace(/^#{2} (.+)$/gm,'<h2>$1</h2>')
        .replace(/^#{1} (.+)$/gm,'<h1>$1</h1>')
        .replace(/\*\*\*(.+?)\*\*\*/g,'<strong><em>$1</em></strong>')
        .replace(/\*\*(.+?)\*\*/g,'<strong>$1</strong>')
        .replace(/\*(.+?)\*/g,'<em>$1</em>')
        .replace(/\[([^\]]+)\]\(([^)]+)\)/g,'<a href="$2">$1</a>')
        .replace(/^---$/gm,'<hr>')
        .replace(/^[-*] (.+)$/gm,'<li>$1</li>');
    h = h.replace(/(<li>.*<\/li>)/s,'<ul>$1</ul>');
    return h.split(/\n{2,}/).map(p => {
        p = p.trim();
        if (!p) return '';
        if (/^<(h[1-6]|ul|ol|pre|hr|blockquote)/.test(p)) return p;
        return `<p>${p.replace(/\n/g,'<br>')}</p>`;
    }).join('\n');
}
</script>

</body>
</html>
