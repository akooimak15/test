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
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/blog.css">
</head>
<body>
<header class="site-header">
    <nav class="nav-inner">
        <a href="/" class="site-logo"><?= e(SITE_NAME) ?></a>
        <ul class="nav-links">
            <li><a href="/">Portfolio</a></li>
            <li><a href="/blog.php">Blog</a></li>
        </ul>
    </nav>
</header>
<main class="main-content">
