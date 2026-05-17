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
    <div class="contact-icons" aria-label="連絡先リンク">
        <a class="contact-icon" href="mailto:your@email.com" aria-label="Email">
            <span class="sr-only">Email</span>
            <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                <path fill="currentColor" d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm0 4-8 5-8-5V6l8 5 8-5v2Z"/>
            </svg>
        </a>
        <a class="contact-icon" href="https://github.com/akooimak15" target="_blank" rel="noopener" aria-label="GitHub">
            <span class="sr-only">GitHub</span>
            <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                <path fill="currentColor" d="M12 .6C5.7.6.6 5.8.6 12.2c0 5.1 3.3 9.4 7.9 10.9.6.1.8-.3.8-.6v-2.2c-3.2.7-3.9-1.4-3.9-1.4-.5-1.3-1.3-1.7-1.3-1.7-1.1-.8.1-.8.1-.8 1.2.1 1.8 1.2 1.8 1.2 1.1 1.9 2.9 1.4 3.6 1.1.1-.8.4-1.4.7-1.7-2.6-.3-5.3-1.3-5.3-5.9 0-1.3.5-2.4 1.2-3.2-.1-.3-.5-1.5.1-3.1 0 0 1-.3 3.3 1.2.9-.3 1.9-.4 2.9-.4s2 .1 2.9.4c2.3-1.5 3.3-1.2 3.3-1.2.6 1.6.2 2.8.1 3.1.8.8 1.2 1.9 1.2 3.2 0 4.6-2.7 5.6-5.3 5.9.4.4.8 1.1.8 2.2v3.3c0 .3.2.7.8.6 4.6-1.5 7.9-5.8 7.9-10.9C23.4 5.8 18.3.6 12 .6Z"/>
            </svg>
        </a>
        <a class="contact-icon" href="<?= BASE_URL ?>/blog.php" aria-label="Blog">
            <span class="sr-only">Blog</span>
            <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                <path fill="currentColor" d="M4 4h16v2H4V4Zm0 5h10v2H4V9Zm0 5h16v2H4v-2Zm0 5h10v2H4v-2Z"/>
            </svg>
        </a>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
