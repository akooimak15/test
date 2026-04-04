<?php
// ============================================================
// article.php — 記事詳細ページ
// ============================================================

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/articles.php';

$slug    = trim($_GET['slug'] ?? '');
$article = $slug ? get_article_by_slug($slug) : null;

if (!$article) {
    http_response_code(404);
    $page_title = '記事が見つかりません';
    include __DIR__ . '/includes/header.php';
    echo '<div class="container" style="padding:80px 24px;text-align:center;">';
    echo '<h1 style="font-size:4rem;color:var(--color-border);">404</h1>';
    echo '<p style="color:var(--color-text-sub);margin-top:16px;">お探しの記事は見つかりませんでした。</p>';
    echo '<a href="/blog.php" class="btn btn-primary" style="margin-top:24px;">記事一覧に戻る</a>';
    echo '</div>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

$tags       = parse_tags($article['tags']);
$body_html  = parse_markdown($article['body']);
$page_title = $article['title'];
$page_desc  = mb_strimwidth(strip_tags($body_html), 0, 120, '…', 'UTF-8');

include __DIR__ . '/includes/header.php';
?>

<div class="article-page">

    <!-- 戻るリンク -->
    <a href="/blog.php" class="back-link">記事一覧</a>

    <!-- 記事ヘッダー -->
    <header class="article-header">
        <h1 class="article-title"><?= e($article['title']) ?></h1>

        <div class="article-meta">
            <div class="avatar"><?= mb_substr(SITE_AUTHOR, 0, 1, 'UTF-8') ?></div>
            <div class="meta-text">
                <span class="meta-author"><?= e(SITE_AUTHOR) ?></span>
                <span class="meta-date">
                    公開: <?= date('Y年n月j日', strtotime($article['created_at'])) ?>
                    <?php if ($article['updated_at'] !== $article['created_at']): ?>
                        &nbsp;·&nbsp;更新: <?= date('Y年n月j日', strtotime($article['updated_at'])) ?>
                    <?php endif; ?>
                </span>
            </div>
        </div>

        <?php if (!empty($tags)): ?>
            <div class="article-tags" style="margin-top:16px;">
                <?php foreach ($tags as $tag): ?>
                    <span class="tag"><?= e($tag) ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </header>

    <!-- 記事本文 -->
    <article class="article-body">
        <?= $body_html ?>
    </article>

    <!-- フッターナビ -->
    <div style="margin-top:64px;padding-top:32px;border-top:1px solid var(--color-border);">
        <a href="/blog.php" class="back-link">記事一覧に戻る</a>
    </div>

</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
