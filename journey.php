<?php
require_once __DIR__ . '/includes/config.php';

$page_title = 'My Journey';
$page_desc = 'akooimak15のプログラミング遍歴';

include __DIR__ . '/includes/header.php';
?>

<section class="journey-header">
    <h1>My Journey</h1>
    <p>プログラミングとの出会いから、今までの歩み。</p>
</section>

<section class="timeline">
    <article class="timeline-item">
        <div class="timeline-year">中学1年</div>
        <div class="timeline-content">
            <h2>プログラミングとの出会い</h2>
            <p>
                初めてコードを書いて動かす体験をし、独学をスタート。
                仕組みを理解して作る面白さを知りました。
            </p>
            <div class="timeline-tags">
                <span class="tag">Scratch</span>
                <span class="tag">独学</span>
            </div>
        </div>
    </article>

    <article class="timeline-item">
        <div class="timeline-year">中学2-3年</div>
        <div class="timeline-content">
            <h2>Web開発へ</h2>
            <p>
                HTML/CSS/JavaScriptを学び、初めて自分のWebページを公開。
                作ったものが誰かに届く楽しさを実感しました。
            </p>
            <div class="timeline-tags">
                <span class="tag">HTML</span>
                <span class="tag">CSS</span>
                <span class="tag">JavaScript</span>
            </div>
        </div>
    </article>

    <article class="timeline-item">
        <div class="timeline-year">高校入学</div>
        <div class="timeline-content">
            <h2>バックエンドに挑戦</h2>
            <p>
                PHPとSQLiteを学習。Termux環境を使った実験的な運用も試しながら、
                小さなアプリを作って改善する流れを身につけました。
            </p>
            <div class="timeline-tags">
                <span class="tag">PHP</span>
                <span class="tag">SQLite</span>
                <span class="tag">Termux</span>
            </div>
        </div>
    </article>

    <article class="timeline-item">
        <div class="timeline-year">現在</div>
        <div class="timeline-content">
            <h2>ポートフォリオを構築</h2>
            <p>
                DockerやRailwayなどを活用し、公開可能な構成へ発展。
                ブログで学習内容を継続的に発信しています。
            </p>
            <div class="timeline-tags">
                <span class="tag">Docker</span>
                <span class="tag">Railway</span>
                <span class="tag">Cloudflare</span>
            </div>
        </div>
    </article>
</section>

<section class="section-block">
    <h2 class="section-title">Continue</h2>
    <p class="bio">詳しい学習記録はブログにまとめています。</p>
    <p style="margin-top:12px;"><a class="hero-link" href="<?= BASE_URL ?>/blog.php">Blogを読む</a></p>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
