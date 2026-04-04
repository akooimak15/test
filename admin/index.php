<?php
// ============================================================
// admin/index.php — 管理ダッシュボード（記事一覧）
// ============================================================

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/articles.php';

require_login();

// 削除処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    verify_csrf();
    $id = (int)$_POST['delete_id'];
    if ($id > 0) {
        delete_article($id);
        header('Location: /admin/?deleted=1');
        exit;
    }
}

$articles = get_all_articles();
$deleted  = isset($_GET['deleted']);
$created  = isset($_GET['created']);
$updated  = isset($_GET['updated']);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ダッシュボード | <?= e(SITE_NAME) ?></title>
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/blog.css">
</head>
<body class="admin-body">

<!-- 管理ヘッダー -->
<header class="admin-header">
    <span class="logo">⚙ 管理画面</span>
    <a href="/admin/logout.php" class="logout">ログアウト</a>
</header>

<div class="admin-container">

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;">
        <h1 class="admin-page-title" style="margin-bottom:0;">記事一覧</h1>
        <a href="/admin/edit.php" class="btn btn-primary">＋ 新規記事を作成</a>
    </div>

    <?php if ($deleted): ?>
        <div class="alert alert-success">記事を削除しました。</div>
    <?php endif; ?>
    <?php if ($created): ?>
        <div class="alert alert-success">記事を作成しました。</div>
    <?php endif; ?>
    <?php if ($updated): ?>
        <div class="alert alert-success">記事を更新しました。</div>
    <?php endif; ?>

    <?php if (empty($articles)): ?>
        <div class="form-card" style="text-align:center;padding:60px;">
            <p style="color:var(--color-text-sub);font-size:1rem;">まだ記事がありません。</p>
            <a href="/admin/edit.php" class="btn btn-primary" style="margin-top:20px;">最初の記事を書く</a>
        </div>
    <?php else: ?>
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>タイトル</th>
                        <th>ステータス</th>
                        <th>タグ</th>
                        <th>作成日</th>
                        <th>更新日</th>
                        <th style="width:160px;">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($articles as $a): ?>
                        <tr>
                            <td>
                                <a href="/admin/edit.php?id=<?= $a['id'] ?>"
                                   style="color:var(--color-text);font-weight:600;">
                                    <?= e($a['title']) ?>
                                </a>
                            </td>
                            <td>
                                <span class="status-badge status-<?= e($a['status']) ?>">
                                    <?= $a['status'] === 'published' ? '公開' : '下書き' ?>
                                </span>
                            </td>
                            <td style="color:var(--color-text-sub);font-size:0.83rem;">
                                <?= e($a['tags'] ?: '—') ?>
                            </td>
                            <td style="color:var(--color-text-sub);font-size:0.83rem;white-space:nowrap;">
                                <?= date('Y/m/d', strtotime($a['created_at'])) ?>
                            </td>
                            <td style="color:var(--color-text-sub);font-size:0.83rem;white-space:nowrap;">
                                <?= date('Y/m/d', strtotime($a['updated_at'])) ?>
                            </td>
                            <td>
                                <div style="display:flex;gap:8px;align-items:center;">
                                    <a href="/admin/edit.php?id=<?= $a['id'] ?>"
                                       class="btn btn-secondary" style="padding:6px 12px;font-size:0.8rem;">
                                        編集
                                    </a>
                                    <?php if ($a['status'] === 'published'): ?>
                                        <a href="/article.php?slug=<?= urlencode($a['slug'] ?? '') ?>"
                                           target="_blank" rel="noopener"
                                           class="btn btn-secondary" style="padding:6px 12px;font-size:0.8rem;">
                                            表示
                                        </a>
                                    <?php endif; ?>
                                    <form method="POST" action="/admin/"
                                          onsubmit="return confirm('「<?= e(addslashes($a['title'])) ?>」を削除しますか？')">
                                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                        <input type="hidden" name="delete_id" value="<?= $a['id'] ?>">
                                        <button type="submit" class="btn btn-danger" style="padding:6px 12px;font-size:0.8rem;">
                                            削除
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</div>

</body>
</html>
