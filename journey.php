<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/articles.php';
$page_title = 'My Journey';
$page_desc  = 'akooimak15のプログラミング遍歴';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title) ?> | <?= e(SITE_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Zen+Kurenaido&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/main.css">
    <style>
    *{margin:0;padding:0;box-sizing:border-box}
    :root{--zen:'Zen Kurenaido',sans-serif;--blue:#7dd3fc;--purple:#a78bfa;--pink:#f0abfc}
    body{background:#060d1f;color:#fff;font-family:var(--font-sans)}

    /* NAV */
    .site-header{background:rgba(6,13,31,.9);backdrop-filter:blur(16px);border-bottom:1px solid rgba(255,255,255,.06);height:64px}
    .nav-inner{max-width:1200px;margin:0 auto;padding:0 32px;height:100%;display:flex;align-items:center;justify-content:space-between}
    .site-logo{font-family:var(--zen);font-size:1.3rem;color:#fff}
    .site-logo:hover{color:var(--blue)}
    .nav-links{display:flex;gap:4px;list-style:none}
    .nav-links a{color:rgba(255,255,255,.65);font-size:.85rem;padding:7px 16px;border-radius:99px;transition:all .2s;border:1px solid transparent}
    .nav-links a:hover{color:#fff;border-color:rgba(255,255,255,.2);background:rgba(255,255,255,.08)}
    .nav-links a.active{color:var(--blue);border-color:rgba(125,211,252,.3);background:rgba(125,211,252,.08)}

    /* HERO */
    .journey-hero{padding:100px 24px 64px;text-align:center;position:relative;overflow:hidden}
    .journey-hero::before{content:'';position:absolute;top:-200px;left:50%;transform:translateX(-50%);width:600px;height:600px;background:radial-gradient(circle,rgba(125,211,252,.08),transparent 70%);pointer-events:none}
    .journey-eyebrow{font-family:var(--zen);font-size:.9rem;color:var(--blue);letter-spacing:.1em;margin-bottom:20px;animation:fadeUp .6s ease both}
    .journey-title{font-family:var(--zen);font-size:clamp(2.5rem,7vw,5rem);color:#fff;margin-bottom:20px;animation:fadeUp .6s .1s ease both;text-shadow:0 0 40px rgba(125,211,252,.2)}
    .journey-desc{font-size:1rem;color:rgba(255,255,255,.55);animation:fadeUp .6s .2s ease both}
    @keyframes fadeUp{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:translateY(0)}}

    /* TIMELINE */
    .timeline-wrap{max-width:800px;margin:0 auto;padding:32px 24px 100px;position:relative}

    /* 縦線 */
    .timeline-wrap::before{content:'';position:absolute;left:50%;top:0;bottom:0;width:1px;background:linear-gradient(to bottom,transparent,rgba(125,211,252,.3) 10%,rgba(125,211,252,.3) 90%,transparent);transform:translateX(-50%)}

    .timeline-item{display:grid;grid-template-columns:1fr 60px 1fr;gap:0;margin-bottom:64px;opacity:0;transition:opacity .8s cubic-bezier(.16,1,.3,1),transform .8s cubic-bezier(.16,1,.3,1)}
    .timeline-item:nth-child(odd){transform:translateX(-30px)}
    .timeline-item:nth-child(even){transform:translateX(30px)}
    .timeline-item.visible{opacity:1;transform:translateX(0)}

    /* ドット */
    .timeline-dot{display:flex;align-items:center;justify-content:center;position:relative;z-index:2}
    .timeline-dot-inner{width:14px;height:14px;border-radius:50%;border:2px solid var(--blue);background:#060d1f;box-shadow:0 0 0 4px rgba(125,211,252,.1),0 0 16px rgba(125,211,252,.4);transition:all .3s}
    .timeline-item:hover .timeline-dot-inner{background:var(--blue);box-shadow:0 0 0 8px rgba(125,211,252,.15),0 0 24px rgba(125,211,252,.6)}

    /* カード */
    .timeline-card{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:24px 28px;transition:all .3s;backdrop-filter:blur(8px)}
    .timeline-card:hover{background:rgba(125,211,252,.06);border-color:rgba(125,211,252,.2);transform:translateY(-3px);box-shadow:0 12px 40px rgba(0,0,0,.3)}

    /* 左右配置 */
    .timeline-item:nth-child(odd) .timeline-left{grid-column:1}
    .timeline-item:nth-child(odd) .timeline-center{grid-column:2}
    .timeline-item:nth-child(odd) .timeline-right{grid-column:3;visibility:hidden}
    .timeline-item:nth-child(even) .timeline-left{grid-column:1;visibility:hidden}
    .timeline-item:nth-child(even) .timeline-center{grid-column:2}
    .timeline-item:nth-child(even) .timeline-right{grid-column:3}

    .timeline-year{font-family:var(--zen);font-size:1.1rem;color:var(--blue);margin-bottom:8px;display:block}
    .timeline-card-title{font-size:1.05rem;font-weight:700;color:#fff;margin-bottom:8px;line-height:1.4}
    .timeline-card-body{font-size:.875rem;color:rgba(255,255,255,.6);line-height:1.75}
    .timeline-tag{display:inline-block;background:rgba(125,211,252,.1);border:1px solid rgba(125,211,252,.2);color:var(--blue);font-size:.7rem;font-weight:600;padding:3px 10px;border-radius:99px;margin-top:12px;margin-right:4px}
    .timeline-tag.purple{background:rgba(167,139,250,.1);border-color:rgba(167,139,250,.2);color:var(--purple)}
    .timeline-tag.pink{background:rgba(240,171,252,.1);border-color:rgba(240,171,252,.2);color:var(--pink)}

    /* CTA */
    .journey-cta{text-align:center;padding:0 24px 100px}
    .journey-cta p{color:rgba(255,255,255,.45);font-size:.9rem;margin-bottom:24px}

    .btn-outline{display:inline-flex;align-items:center;gap:8px;padding:13px 28px;background:transparent;border:1px solid rgba(125,211,252,.3);border-radius:99px;color:var(--blue);font-size:.88rem;font-weight:700;text-decoration:none;transition:all .2s}
    .btn-outline:hover{background:rgba(125,211,252,.1);border-color:var(--blue);transform:translateY(-2px);color:var(--blue)}

    .site-footer{background:#030810;border-top:1px solid rgba(255,255,255,.04)}
    .site-footer .footer-inner{color:rgba(255,255,255,.25);text-align:center;padding:32px;font-size:.85rem}

    @media(max-width:640px){
        .timeline-wrap::before{left:24px}
        .timeline-item{grid-template-columns:48px 1fr;margin-bottom:40px}
        .timeline-item:nth-child(odd){transform:translateX(-20px)}
        .timeline-item:nth-child(even){transform:translateX(20px)}
        .timeline-item .timeline-right,
        .timeline-item .timeline-left{display:none!important}
        .timeline-item:nth-child(odd) .timeline-left,
        .timeline-item:nth-child(even) .timeline-left{display:none}
        .timeline-item:nth-child(odd) .timeline-right,
        .timeline-item:nth-child(even) .timeline-right{display:none}
        .timeline-dot{grid-column:1;align-items:flex-start;padding-top:6px}
        .timeline-card-wrap{grid-column:2}
        .timeline-item:nth-child(odd) .timeline-left{visibility:visible;display:none}
        .timeline-item:nth-child(even) .timeline-right{visibility:visible;display:none}
    }
    </style>
</head>
<body>

<header class="site-header">
    <nav class="nav-inner">
        <a href="<?= BASE_URL ?>/" class="site-logo"><?= e(SITE_NAME) ?></a>
        <ul class="nav-links">
            <li><a href="<?= BASE_URL ?>/">Portfolio</a></li>
            <li><a href="<?= BASE_URL ?>/journey.php" class="active">My Journey</a></li>
            <li><a href="<?= BASE_URL ?>/blog.php">Blog</a></li>
        </ul>
    </nav>
</header>

<div class="journey-hero">
    <div class="journey-eyebrow">My Journey</div>
    <h1 class="journey-title">遍歴</h1>
    <p class="journey-desc">プログラミングとの出会いから、今日まで。</p>
</div>

<div class="timeline-wrap">

    <!-- アイテム1（左） -->
    <div class="timeline-item">
        <div class="timeline-left">
            <div class="timeline-card">
                <span class="timeline-year">中学1年</span>
                <div class="timeline-card-title">プログラミングとの出会い</div>
                <div class="timeline-card-body">
                    初めてプログラミングに触れる。「コードを書いたら動く」という感覚に衝撃を受け、独学を開始。
                </div>
                <span class="timeline-tag">Scratch</span>
                <span class="timeline-tag">独学スタート</span>
            </div>
        </div>
        <div class="timeline-center"><div class="timeline-dot"><div class="timeline-dot-inner"></div></div></div>
        <div class="timeline-right"></div>
    </div>

    <!-- アイテム2（右） -->
    <div class="timeline-item">
        <div class="timeline-left"></div>
        <div class="timeline-center"><div class="timeline-dot"><div class="timeline-dot-inner"></div></div></div>
        <div class="timeline-right">
            <div class="timeline-card">
                <span class="timeline-year">中学2〜3年</span>
                <div class="timeline-card-title">Web開発へ踏み込む</div>
                <div class="timeline-card-body">
                    HTMLとCSSを独学。初めて自分のWebページを作成し、「作ったものが世界に公開できる」ことの面白さを知る。
                </div>
                <span class="timeline-tag">HTML</span>
                <span class="timeline-tag">CSS</span>
                <span class="timeline-tag purple">JavaScript</span>
            </div>
        </div>
    </div>

    <!-- アイテム3（左） -->
    <div class="timeline-item">
        <div class="timeline-left">
            <div class="timeline-card">
                <span class="timeline-year">高校入学</span>
                <div class="timeline-card-title">バックエンドに挑戦</div>
                <div class="timeline-card-body">
                    PHPとSQLiteを学び始める。古いAndroidスマホをTermux環境のサーバーとして活用するという独自のアプローチで開発を続ける。
                </div>
                <span class="timeline-tag purple">PHP</span>
                <span class="timeline-tag">SQLite</span>
                <span class="timeline-tag">Termux</span>
            </div>
        </div>
        <div class="timeline-center"><div class="timeline-dot"><div class="timeline-dot-inner"></div></div></div>
        <div class="timeline-right"></div>
    </div>

    <!-- アイテム4（右） -->
    <div class="timeline-item">
        <div class="timeline-left"></div>
        <div class="timeline-center"><div class="timeline-dot"><div class="timeline-dot-inner"></div></div></div>
        <div class="timeline-right">
            <div class="timeline-card">
                <span class="timeline-year">現在</span>
                <div class="timeline-card-title">このポートフォリオを構築</div>
                <div class="timeline-card-body">
                    Docker・Railway・Cloudflareなどを活用し、ポートフォリオ兼ブログサイトを公開。「論理的思考と柔軟な発想の共存」をテーマに開発を続ける。
                </div>
                <span class="timeline-tag">Docker</span>
                <span class="timeline-tag purple">Railway</span>
                <span class="timeline-tag pink">公開中 🚀</span>
            </div>
        </div>
    </div>

    <!-- アイテム5（左・未来） -->
    <div class="timeline-item">
        <div class="timeline-left">
            <div class="timeline-card" style="border-style:dashed;opacity:.6;">
                <span class="timeline-year" style="color:var(--purple);">Next...</span>
                <div class="timeline-card-title">[ 続きは自分で書く ]</div>
                <div class="timeline-card-body">
                    これからの遍歴はここに追加していく予定。
                </div>
                <span class="timeline-tag purple">TBD</span>
            </div>
        </div>
        <div class="timeline-center"><div class="timeline-dot"><div class="timeline-dot-inner" style="border-color:var(--purple);box-shadow:0 0 0 4px rgba(167,139,250,.1),0 0 16px rgba(167,139,250,.3)"></div></div></div>
        <div class="timeline-right"></div>
    </div>

</div>

<div class="journey-cta">
    <p>気になったことはブログでも発信しています</p>
    <a href="<?= BASE_URL ?>/blog.php" class="btn-outline">📝 Blog を読む</a>
</div>

<footer class="site-footer">
    <div class="footer-inner">
        <p>&copy; <?= date('Y') ?> <?= e(SITE_AUTHOR) ?>. All rights reserved.</p>
    </div>
</footer>

<script>
const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry, i) => {
        if(entry.isIntersecting){
            setTimeout(() => entry.target.classList.add('visible'), i * 150);
        }
    });
}, {threshold: 0.15});
document.querySelectorAll('.timeline-item').forEach(el => observer.observe(el));
</script>
</body>
</html>
