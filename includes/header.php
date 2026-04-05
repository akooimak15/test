<?php
// ============================================================
// header.php — 共通ヘッダーパーシャル
// 使い方: include __DIR__ . '/includes/header.php';
//         $page_title と $page_desc を事前に設定しておく
// ============================================================
$page_title = $page_title ?? SITE_NAME;
$page_desc  = $page_desc  ?? SITE_DESC;
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title) ?> | <?= e(SITE_NAME) ?></title>
    <meta name="description" content="<?= e($page_desc) ?>">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/blog.css">
<style>
.hamburger{display:none;flex-direction:column;gap:5px;cursor:pointer;padding:8px;border:none;background:none}
.hamburger span{display:block;width:24px;height:2px;background:#fff;border-radius:2px;transition:all .3s}
.hamburger.open span:nth-child(1){transform:translateY(7px) rotate(45deg)}
.hamburger.open span:nth-child(2){opacity:0}
.hamburger.open span:nth-child(3){transform:translateY(-7px) rotate(-45deg)}
.nav-drawer{position:fixed;inset:0;background:rgba(6,13,31,.97);backdrop-filter:blur(20px);z-index:150;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;opacity:0;pointer-events:none;transition:opacity .3s}
.nav-drawer.open{opacity:1;pointer-events:all}
.nav-drawer a{font-size:1.8rem;color:rgba(255,255,255,.7);text-decoration:none;padding:12px 32px;transition:color .2s}
.nav-drawer a:hover{color:#3ea8ff}
@media(max-width:768px){.hamburger{display:flex}.nav-links{display:none}}
</style>
</head>
<body>
<div class="nav-drawer" id="nav-drawer">
    <a href="<?= BASE_URL ?>/" onclick="closeDrawer()">Portfolio</a>
    <a href="<?= BASE_URL ?>/journey.php" onclick="closeDrawer()">My Journey</a>
    <a href="<?= BASE_URL ?>/blog.php" onclick="closeDrawer()">Blog</a>
</div>
<header class="site-header">
    <nav class="nav-inner">
        <a href="<?= BASE_URL ?>/" class="site-logo"><?= e(SITE_NAME) ?></a>
        <ul class="nav-links">
            <li><a href="<?= BASE_URL ?>/">Portfolio</a></li>
            <li><a href="<?= BASE_URL ?>/journey.php">My Journey</a></li>
            <li><a href="<?= BASE_URL ?>/blog.php">Blog</a></li>
        </ul>
        <button class="hamburger" id="hamburger" onclick="toggleDrawer()" aria-label="メニュー">
            <span></span><span></span><span></span>
        </button>
    </nav>
</header>
<script>
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
</script>
<main class="main-content">
