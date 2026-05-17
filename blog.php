<?php
// ============================================================
// blog.php — 記事一覧ページ（Zenn風カードレイアウト）
// ============================================================
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/articles.php';

$per_page = 12;
$page     = max(1, (int)($_GET['page'] ?? 1));
$q        = trim((string)($_GET['q'] ?? ''));
$tag      = trim((string)($_GET['tag'] ?? ''));

$articles = get_published_articles($page, $per_page, $q ?: null, $tag ?: null);
$total    = count_published_articles($q ?: null, $tag ?: null);
$pages    = (int)ceil($total / $per_page);
$all_tags = get_published_tags();

function blog_url(array $params = []): string {
    $query = array_merge($_GET, $params);
    foreach ($query as $k => $v) {
        if ($v === null || $v === '') unset($query[$k]);
    }
    $qs = http_build_query($query, '', '&', PHP_QUERY_RFC3986);
    return BASE_URL . '/blog.php' . ($qs ? ('?' . $qs) : '');
}

$page_title = 'Blog';
$page_desc  = '技術記事・日々の学びを発信しています。';

include __DIR__ . '/includes/header.php';
?>

<!-- ヒーロー -->
<div class="blog-hero">
    <h1>Blog</h1>
    <p>技術記事・日々の学びを発信しています。</p>
</div>

<!-- 絞り込み -->
<div class="blog-filters">
    <form class="filter-form" method="get" action="<?= BASE_URL ?>/blog.php">
        <input type="hidden" name="page" value="1">
        <div class="filter-field">
            <input
                class="filter-input"
                type="text"
                name="q"
                value="<?= e($q) ?>"
                placeholder="検索（タイトル/本文/タグ）"
                autocomplete="off"
            >
        </div>
        <?php if ($tag !== ''): ?>
            <input type="hidden" name="tag" value="<?= e($tag) ?>">
        <?php endif; ?>
        <button type="submit" class="btn">検索</button>
        <?php if ($q !== '' || $tag !== ''): ?>
            <a class="btn btn-secondary" href="<?= BASE_URL ?>/blog.php">クリア</a>
        <?php endif; ?>
    </form>

    <?php if (!empty($all_tags)): ?>
        <div class="tag-filter" aria-label="タグで絞り込み">
            <a class="tag-chip <?= $tag === '' ? 'active' : '' ?>" href="<?= blog_url(['tag' => '', 'page' => 1]) ?>">All</a>
            <?php foreach ($all_tags as $t): ?>
                <a
                    class="tag-chip <?= $tag === $t ? 'active' : '' ?>"
                    href="<?= blog_url(['tag' => $t, 'page' => 1]) ?>"
                >
                    <?= e($t) ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($q !== '' || $tag !== ''): ?>
        <div class="filter-status">
            <?php if ($tag !== ''): ?>
                <span>タグ: <strong><?= e($tag) ?></strong></span>
            <?php endif; ?>
            <?php if ($q !== ''): ?>
                <span>検索: <strong><?= e($q) ?></strong></span>
            <?php endif; ?>
            <span><?= (int)$total ?>件</span>
        </div>
    <?php endif; ?>
</div>

<!-- 記事一覧 -->
<div class="blog-layout">
    <div class="articles-grid">
        <?php if (empty($articles)): ?>
            <div class="no-articles">
                <div style="font-size:3rem;">📝</div>
                <p>まだ記事がありません。</p>
            </div>
        <?php else: ?>
            <?php foreach ($articles as $article): ?>
                <?php $tags = parse_tags($article['tags'] ?? ''); ?>
                <a class="article-card" href="<?= BASE_URL ?>/article.php?slug=<?= urlencode($article['slug']) ?>">

                    <!-- 著者メタ -->
                    <div class="card-meta">
                        <div class="avatar"><?= mb_substr(SITE_AUTHOR, 0, 1, 'UTF-8') ?></div>
                        <div class="meta-text">
                            <span class="meta-author"><?= e(SITE_AUTHOR) ?></span>
                            <span class="meta-date">
                                <?= date('Y年n月j日', strtotime($article['created_at'])) ?>
                            </span>
                        </div>
                    </div>

                    <!-- タイトル -->
                    <div class="card-title"><?= e($article['title']) ?></div>

                    <!-- 抜粋 -->
                    <?php if (!empty($article['excerpt'])): ?>
                        <div class="card-excerpt">
                            <?= e(mb_strimwidth(strip_tags($article['excerpt']), 0, 120, '…', 'UTF-8')) ?>
                        </div>
                    <?php endif; ?>

                    <!-- タグ -->
                    <?php if (!empty($tags)): ?>
                        <div class="card-tags">
                            <?php foreach ($tags as $tag): ?>
                                <span class="tag"><?= e($tag) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- ページネーション -->
    <?php if ($pages > 1): ?>
        <nav class="pagination" aria-label="ページナビゲーション">
            <?php if ($page > 1): ?>
                <a href="<?= blog_url(['page' => $page - 1]) ?>" aria-label="前のページ">‹</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <?php if ($i === $page): ?>
                    <span class="current"><?= $i ?></span>
                <?php else: ?>
                    <a href="<?= blog_url(['page' => $i]) ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page < $pages): ?>
                <a href="<?= blog_url(['page' => $page + 1]) ?>" aria-label="次のページ">›</a>
            <?php endif; ?>
        </nav>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
