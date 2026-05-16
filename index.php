<?php
require_once __DIR__ . '/includes/config.php';

$page_title = 'Portfolio';
$page_desc = SITE_DESC;

include __DIR__ . '/includes/header.php';
?>

<section class="home-hero">
    <div class="hero-kicker">portfolio / minimal theme</div>
    <h1><?= e(SITE_AUTHOR) ?>のポートフォリオ</h1>
    <p>
        論理的思考と柔軟な発想の共存をテーマに、Web開発と学習記録を積み重ねています。
        このサイトは、制作物・遍歴・技術ブログをシンプルにまとめた場所です。
    </p>
    <div class="hero-links">
        <a class="hero-link" href="<?= BASE_URL ?>/journey.php">My Journey</a>
        <a class="hero-link" href="<?= BASE_URL ?>/blog.php">Blog</a>
        <a class="hero-link" href="https://github.com/akooimak15" target="_blank" rel="noopener">GitHub</a>
    </div>
</section>

<section class="section-block">
    <h2 class="section-title">About</h2>
    <p class="bio">
        中学時代から独学でプログラミングを継続中。現在はPHP / SQLiteを中心に、
        小さく作って改善するサイクルを重視しています。
    </p>
</section>

<section class="section-block">
    <h2 class="section-title">Skills</h2>
    <ul class="skill-list">
        <li>PHP</li>
        <li>JavaScript</li>
        <li>HTML / CSS</li>
        <li>SQLite</li>
        <li>Linux</li>
        <li>Docker</li>
    </ul>
</section>

<section class="section-block">
    <h2 class="section-title">Works</h2>
    <ul class="project-list">
        <li>
            <div class="project-title">Portfolio Blog</div>
            <div class="project-note">このサイト。PHPで記事管理と公開機能を実装。</div>
        </li>
        <li>
            <div class="project-title">Next Project</div>
            <div class="project-note">次の制作物をここに追加予定。</div>
        </li>
    </ul>
</section>

<section class="section-block">
    <h2 class="section-title">Contact</h2>
    <ul class="contact-list">
        <li><a href="mailto:your@email.com">Email</a></li>
        <li><a href="https://github.com/akooimak15" target="_blank" rel="noopener">GitHub</a></li>
        <li><a href="<?= BASE_URL ?>/blog.php">Blog</a></li>
    </ul>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
