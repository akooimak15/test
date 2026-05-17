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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'upload_image') {
    header('Content-Type: application/json; charset=utf-8');
    verify_csrf();

    if (empty($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
        echo json_encode(['ok' => false, 'error' => '画像ファイルを選択してください。'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $allowed_types = [
        'image/png'  => 'png',
        'image/jpeg' => 'jpg',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    ];

    if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['ok' => false, 'error' => '画像のアップロードに失敗しました。'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $_FILES['image']['tmp_name']);
    finfo_close($finfo);

    if (!isset($allowed_types[$mime])) {
        echo json_encode(['ok' => false, 'error' => '対応していない画像形式です。PNG / JPEG / GIF / WebP をご利用ください。'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
        echo json_encode(['ok' => false, 'error' => '画像は5MB以下にしてください。'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $upload_dir = __DIR__ . '/../assets/uploads';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    @chmod($upload_dir, 0755);

    $filename = 'img_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $allowed_types[$mime];
    $dest     = $upload_dir . '/' . $filename;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
        echo json_encode(['ok' => false, 'error' => '画像の保存に失敗しました。'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    @chmod($dest, 0644);

    $image_path = '/assets/uploads/' . $filename;
    echo json_encode(['ok' => true, 'url' => $image_path], JSON_UNESCAPED_UNICODE);
    exit;
}

// フォーム送信処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $title  = trim($_POST['title']  ?? '');
    $body   = trim($_POST['body']   ?? '');
    $tags   = trim($_POST['tags']   ?? '');
    $status = in_array($_POST['status'] ?? '', ['draft', 'published'], true)
              ? $_POST['status']
              : 'draft';

    if (!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $allowed_types = [
            'image/png'  => 'png',
            'image/jpeg' => 'jpg',
            'image/webp' => 'webp',
            'image/gif'  => 'gif',
        ];

        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = '画像のアップロードに失敗しました。';
        } else {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime  = finfo_file($finfo, $_FILES['image']['tmp_name']);
            finfo_close($finfo);

            if (!isset($allowed_types[$mime])) {
                $errors[] = '対応していない画像形式です。PNG / JPEG / GIF / WebP をご利用ください。';
            } elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) {
                $errors[] = '画像は5MB以下にしてください。';
            } else {
                $upload_dir = __DIR__ . '/../assets/uploads';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                $filename = 'img_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $allowed_types[$mime];
                $dest     = $upload_dir . '/' . $filename;

                if (!move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                    $errors[] = '画像の保存に失敗しました。';
                } else {
                    @chmod($upload_dir, 0755);
                    @chmod($dest, 0644);
                    $image_path = '/assets/uploads/' . $filename;
                    $body .= "\n\n![]({$image_path})\n";
                }
            }
        }
    }

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
    <link rel="stylesheet" href="/assets/css/all.css?v=14">
    <style>
        .editor-toolbar {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            padding: 10px 14px;
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-bottom: none;
            border-radius: var(--radius-sm) var(--radius-sm) 0 0;
        }
        .editor-toolbar button {
            padding: 5px 10px;
            font-size: 0.78rem;
            font-weight: 600;
            background: #242933;
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
            min-height: 55vh;
            height: 55vh;
            resize: vertical;
        }
        .preview-wrap {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-sm);
            padding: 24px;
            min-height: 55vh;
        }
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
        .tab-btn.active { background: var(--color-surface); color: var(--color-text); border-bottom-color: var(--color-surface); }
        .tab-panel { display: none; }
        .tab-panel.active { display: block; }
        @media (max-width: 640px) {
            .editor-area { min-height: 48vh; height: 48vh; }
            .preview-wrap { min-height: 48vh; padding: 18px; }
        }
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

    <form method="POST" action="/admin/edit.php<?= $id ? "?id={$id}" : '' ?>" enctype="multipart/form-data">
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

            <!-- 画像アップロード -->
            <div class="form-group">
                <label class="form-label" for="image">画像アップロード</label>
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                    <input
                        class="form-input"
                        type="file"
                        id="image"
                        name="image"
                        accept="image/png,image/jpeg,image/gif,image/webp"
                    >
                    <button type="button" class="btn btn-secondary" id="insert-image-btn">挿入</button>
                    <span id="insert-image-status" style="color:var(--color-text-sub);font-size:0.85rem;"></span>
                </div>
                <p class="form-hint">JPEG / PNG / GIF / WebP 形式を最大 5MB までアップロードできます。アップロード後、本文末尾にMarkdown画像として挿入されます。</p>
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
        .replace(/!\[([^\]]*)\]\(([^)]+)\)/g, '<img src="$2" alt="$1">')
        .replace(/^#{6} (.+)$/gm,'<h6>$1</h6>')
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

<script>
function insertAtCursor(text) {
    const ta = document.getElementById('body');
    if (!ta) return;
    const s = ta.selectionStart ?? ta.value.length;
    const e = ta.selectionEnd ?? ta.value.length;
    ta.setRangeText(text, s, e, 'end');
    ta.focus();
}

async function uploadAndInsertImage() {
    const fileInput = document.getElementById('image');
    const statusEl = document.getElementById('insert-image-status');
    const form = document.querySelector('form');

    if (!fileInput || !form) return;
    if (!fileInput.files || fileInput.files.length === 0) {
        if (statusEl) statusEl.textContent = '画像を選択してください';
        return;
    }

    const csrf = form.querySelector('input[name="csrf_token"]')?.value || '';
    const actionUrl = form.getAttribute('action') || window.location.pathname;

    const fd = new FormData();
    fd.append('csrf_token', csrf);
    fd.append('action', 'upload_image');
    fd.append('image', fileInput.files[0]);

    if (statusEl) statusEl.textContent = 'アップロード中...';

    try {
        const res = await fetch(actionUrl, { method: 'POST', body: fd });
        const data = await res.json();
        if (!data || !data.ok) {
            if (statusEl) statusEl.textContent = data?.error || 'アップロードに失敗しました';
            return;
        }

        insertAtCursor(`\n\n![](${data.url})\n\n`);
        if (statusEl) statusEl.textContent = '挿入しました';
        fileInput.value = '';
    } catch (e) {
        if (statusEl) statusEl.textContent = '通信に失敗しました';
    }
}

document.getElementById('insert-image-btn')?.addEventListener('click', uploadAndInsertImage);
</script>

</body>
</html>
