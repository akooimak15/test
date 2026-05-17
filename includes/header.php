<?php
// ============================================================
// header.php — 共通ヘッダーパーシャル
// 使い方: include __DIR__ . '/includes/header.php';
//         $page_title と $page_desc を事前に設定しておく
// ============================================================
$page_title = $page_title ?? SITE_NAME;
$page_desc  = $page_desc  ?? SITE_DESC;
$current_path = basename(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
$is_home = ($current_path === '' || $current_path === 'index.php');
$is_journey = ($current_path === 'journey.php');
$is_blog = ($current_path === 'blog.php' || $current_path === 'article.php');
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title) ?> | <?= e(SITE_NAME) ?></title>
    <meta name="description" content="<?= e($page_desc) ?>">
    <script>
    (function(){
        try{
            var t = localStorage.getItem('theme');
            if (t === 'light' || t === 'dark') {
                document.documentElement.dataset.theme = t;
            } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.dataset.theme = 'dark';
            } else {
                document.documentElement.dataset.theme = 'light';
            }
        }catch(e){}
    })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+JP:wght@400;500;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/all.css?v=16">
</head>
<body>
<div class="nav-drawer" id="nav-drawer">
    <a href="<?= BASE_URL ?>/" onclick="closeDrawer()">Portfolio</a>
    <a href="<?= BASE_URL ?>/journey.php" onclick="closeDrawer()">My Journey</a>
    <a href="<?= BASE_URL ?>/blog.php" onclick="closeDrawer()">Blog</a>
    <button type="button" class="theme-toggle theme-toggle-mobile" data-theme-toggle aria-label="テーマ切り替え"></button>
</div>
<header class="site-header">
    <nav class="nav-inner">
        <a href="<?= BASE_URL ?>/" class="site-logo"><?= e(SITE_NAME) ?></a>
        <ul class="nav-links">
            <li><a href="<?= BASE_URL ?>/" class="<?= $is_home ? 'active' : '' ?>">Portfolio</a></li>
            <li><a href="<?= BASE_URL ?>/journey.php" class="<?= $is_journey ? 'active' : '' ?>">My Journey</a></li>
            <li><a href="<?= BASE_URL ?>/blog.php" class="<?= $is_blog ? 'active' : '' ?>">Blog</a></li>
        </ul>
        <button type="button" class="theme-toggle theme-toggle-desktop" data-theme-toggle aria-label="テーマ切り替え"></button>
        <button class="hamburger" id="hamburger" onclick="toggleDrawer()" aria-label="メニュー">
            <span></span><span></span><span></span>
        </button>
    </nav>
</header>
<script>
function applyTheme(t){
    document.documentElement.dataset.theme = t;
    try{ localStorage.setItem('theme', t); }catch(e){}
    updateThemeToggle();
}
function currentTheme(){
    return document.documentElement.dataset.theme === 'dark' ? 'dark' : 'light';
}
function updateThemeToggle(){
    var t = currentTheme();
    var next = t === 'dark' ? 'light' : 'dark';
    var label = next === 'dark' ? 'Dark' : 'Light';
    document.querySelectorAll('[data-theme-toggle]').forEach(function(b){
        b.textContent = label;
        b.setAttribute('aria-label', 'テーマ切り替え: ' + label);
    });
}
function toggleDrawer(){
    document.getElementById('hamburger').classList.toggle('open');
    document.getElementById('nav-drawer').classList.toggle('open');
    document.body.style.overflow=document.getElementById('nav-drawer').classList.contains('open')?'hidden':'';
}
function closeDrawer(){
    document.getElementById('hamburger').classList.remove('open');
    document.getElementById('nav-drawer').classList.remove('open');
    document.body.style.overflow='';
}
document.querySelectorAll('[data-theme-toggle]').forEach(function(b){
    b.addEventListener('click', function(){
        applyTheme(currentTheme() === 'dark' ? 'light' : 'dark');
    });
});
updateThemeToggle();
</script>
<main class="main-content">
