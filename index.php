<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/articles.php';
$page_title = 'Portfolio';
$page_desc  = SITE_DESC;
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(SITE_NAME) ?></title>
    <meta name="description" content="<?= e($page_desc) ?>">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/main.css">
    <style>
    *{margin:0;padding:0;box-sizing:border-box}
    .site-header{background:transparent;border-bottom:none;transition:background .4s,border-color .4s,box-shadow .4s}
    .site-header.scrolled{background:rgba(255,255,255,.95);border-bottom:1px solid var(--color-border);box-shadow:0 2px 16px rgba(0,0,0,.07)}
    .site-logo{color:#fff}
    .site-header.scrolled .site-logo{color:var(--color-text)}
    .nav-links a{color:rgba(255,255,255,.85)}
    .site-header.scrolled .nav-links a{color:var(--color-text-sub)}
    .nav-links a:hover{background:rgba(255,255,255,.15)!important;color:#fff!important}
    .site-header.scrolled .nav-links a:hover{background:var(--color-bg-sub)!important;color:var(--color-text)!important}

    .hero{position:relative;min-height:100svh;display:flex;flex-direction:column;align-items:center;justify-content:center;overflow:hidden;text-align:center}
    .hero-bg{position:absolute;inset:0;background:url('<?= BASE_URL ?>/assets/bg_hero.jpg') center center/cover no-repeat;animation:bgDrift 18s ease-in-out infinite alternate;z-index:0}
    @keyframes bgDrift{0%{transform:scale(1.08) translate(0,0)}100%{transform:scale(1.12) translate(-1.5%,-1%)}}
    .hero-overlay{position:absolute;inset:0;background:radial-gradient(ellipse 80% 60% at 50% 40%,rgba(0,100,200,.12),transparent),linear-gradient(160deg,rgba(0,30,80,.45) 0%,rgba(0,80,180,.2) 50%,rgba(0,0,0,.55) 100%);z-index:1}
    .hero-content{position:relative;z-index:3;padding:0 24px;max-width:860px}

    .hero-eyebrow{display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.15);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.25);color:rgba(255,255,255,.9);font-size:.8rem;font-weight:600;letter-spacing:.12em;text-transform:uppercase;padding:7px 18px;border-radius:99px;margin-bottom:32px;animation:fadeUp .8s ease both}
    .hero-eyebrow::before{content:'';width:6px;height:6px;border-radius:50%;background:#7df3a0;box-shadow:0 0 6px #7df3a0;animation:blink 2s ease infinite}
    @keyframes blink{0%,100%{opacity:1}50%{opacity:.3}}

    .hero-title{font-size:clamp(2.8rem,8vw,6.5rem);font-weight:900;letter-spacing:-0.04em;line-height:1.0;color:#fff;margin-bottom:24px;animation:fadeUp .8s .15s ease both}
    .hero-title .accent{display:inline-block;background:linear-gradient(90deg,#7dd3fc,#a78bfa,#f0abfc);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;animation:gshift 4s ease infinite;background-size:200%}
    @keyframes gshift{0%{background-position:0%}50%{background-position:100%}100%{background-position:0%}}

    .hero-sub{font-size:clamp(1rem,2.5vw,1.25rem);color:rgba(255,255,255,.75);line-height:1.65;margin-bottom:44px;animation:fadeUp .8s .3s ease both}
    .hero-cta{display:flex;gap:14px;justify-content:center;flex-wrap:wrap;animation:fadeUp .8s .45s ease both}

    .btn-hp{display:inline-flex;align-items:center;gap:8px;padding:15px 32px;background:#fff;color:#0a1628;font-size:.95rem;font-weight:800;border-radius:99px;text-decoration:none;transition:transform .2s,box-shadow .2s;box-shadow:0 4px 24px rgba(0,0,0,.25)}
    .btn-hp:hover{transform:translateY(-3px) scale(1.03);box-shadow:0 10px 36px rgba(0,0,0,.3);color:#0a1628}
    .btn-hg{display:inline-flex;align-items:center;gap:8px;padding:15px 32px;background:rgba(255,255,255,.12);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);color:#fff;font-size:.95rem;font-weight:700;border-radius:99px;border:1px solid rgba(255,255,255,.3);text-decoration:none;transition:all .2s}
    .btn-hg:hover{background:rgba(255,255,255,.22);transform:translateY(-3px);color:#fff}

    .scroll-hint{position:absolute;bottom:36px;left:50%;transform:translateX(-50%);z-index:3;display:flex;flex-direction:column;align-items:center;gap:8px;color:rgba(255,255,255,.5);font-size:.72rem;letter-spacing:.1em;text-transform:uppercase;animation:fadeUp .8s .8s ease both}
    .scroll-arrow{width:22px;height:22px;border-right:2px solid rgba(255,255,255,.4);border-bottom:2px solid rgba(255,255,255,.4);transform:rotate(45deg);animation:bounce 1.8s ease infinite}
    @keyframes bounce{0%,100%{transform:rotate(45deg) translateY(0)}50%{transform:rotate(45deg) translateY(6px)}}
    @keyframes fadeUp{from{opacity:0;transform:translateY(28px)}to{opacity:1;transform:translateY(0)}}

    /* STATS */
    .stats-bar{background:#0a1628;padding:48px 24px;border-bottom:1px solid rgba(255,255,255,.07)}
    .stats-inner{max-width:900px;margin:0 auto;display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:0}
    .stat-item{text-align:center;padding:0 24px;border-right:1px solid rgba(255,255,255,.08);opacity:0;transform:translateY(20px);transition:opacity .6s,transform .6s}
    .stat-item:last-child{border-right:none}
    .stat-item.visible{opacity:1;transform:translateY(0)}
    .stat-num{font-size:2.5rem;font-weight:900;letter-spacing:-0.04em;color:#7dd3fc;line-height:1;margin-bottom:8px}
    .stat-label{font-size:.78rem;color:rgba(255,255,255,.45);letter-spacing:.08em;text-transform:uppercase}

    /* SECTION */
    .section{padding:96px 24px}
    .section-dark{background:#0d1b2e;color:#fff}
    .section-light{background:#f8faff}
    .section-inner{max-width:1000px;margin:0 auto}
    .section-eyebrow{font-size:.75rem;font-weight:700;letter-spacing:.15em;text-transform:uppercase;color:#3ea8ff;margin-bottom:12px}
    .section-title{font-size:clamp(1.8rem,4vw,2.6rem);font-weight:900;letter-spacing:-0.04em;margin-bottom:48px;line-height:1.15}
    .section-dark .section-title{color:#fff}
    .section-light .section-title{color:#0a1628}

    /* ABOUT */
    .about-grid{display:grid;grid-template-columns:1fr 1fr;gap:64px;align-items:center}
    .about-avatar{width:100%;aspect-ratio:1;max-width:340px;background:linear-gradient(135deg,#1d4ed8,#7c3aed);border-radius:24px;display:flex;align-items:center;justify-content:center;font-size:7rem;margin:0 auto;box-shadow:0 24px 64px rgba(29,78,216,.4);position:relative;overflow:hidden}
    .about-avatar::after{content:'';position:absolute;inset:0;background:linear-gradient(135deg,rgba(255,255,255,.1),transparent)}
    .about-badge{position:absolute;bottom:-16px;right:-16px;background:#0d1b2e;border:2px solid #1d4ed8;border-radius:12px;padding:12px 18px;font-size:.8rem;color:#7dd3fc;font-weight:700}
    .about-avatar-wrap{position:relative}
    .about-text p{font-size:1.05rem;color:rgba(255,255,255,.7);line-height:1.8;margin-bottom:20px}
    .skill-tags{display:flex;flex-wrap:wrap;gap:8px;margin-top:28px}
    .skill-tag{background:rgba(125,211,252,.1);border:1px solid rgba(125,211,252,.2);color:#7dd3fc;font-size:.78rem;font-weight:600;padding:6px 14px;border-radius:99px}

    /* WORKS */
    .works-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:24px}
    .work-placeholder{background:#f1f5ff;border:2px dashed #c7d7ff;border-radius:16px;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:48px 24px;text-align:center;color:#94a3b8;min-height:240px;gap:8px;font-size:.85rem}
    .work-placeholder span{font-size:2rem}

    /* CONTACT */
    .contact-box{border-radius:24px;padding:64px 48px;text-align:center;position:relative;overflow:hidden;background:linear-gradient(135deg,#1d4ed8,#7c3aed)}
    .contact-box::before{content:'';position:absolute;inset:0;background:url('<?= BASE_URL ?>/assets/bg_hero.jpg') center/cover;opacity:.08}
    .contact-box h2{font-size:clamp(1.8rem,4vw,2.4rem);font-weight:900;letter-spacing:-0.04em;color:#fff;margin-bottom:16px;position:relative}
    .contact-box p{color:rgba(255,255,255,.7);margin-bottom:36px;font-size:1.05rem;position:relative}
    .contact-links{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;position:relative}
    .clink{display:inline-flex;align-items:center;gap:8px;padding:13px 28px;background:rgba(255,255,255,.15);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.25);border-radius:99px;color:#fff;font-weight:700;font-size:.9rem;text-decoration:none;transition:all .2s}
    .clink:hover{background:rgba(255,255,255,.28);transform:translateY(-2px);color:#fff}
    .clink.primary{background:#fff;color:#1d4ed8;border-color:transparent}
    .clink.primary:hover{transform:translateY(-2px);color:#1d4ed8}

    .site-footer{background:#070f1e;border-top:1px solid rgba(255,255,255,.06)}
    .site-footer .footer-inner{color:rgba(255,255,255,.3)}

    @media(max-width:768px){
        .about-grid{grid-template-columns:1fr;gap:40px}
        .about-avatar{max-width:220px;font-size:5rem}
        .contact-box{padding:40px 24px}
        .stat-item{border-right:none;border-bottom:1px solid rgba(255,255,255,.08);padding:20px 0}
        .stat-item:last-child{border-bottom:none}
    }
    </style>
</head>
<body>

<header class="site-header" id="site-header">
    <nav class="nav-inner">
        <a href="<?= BASE_URL ?>/" class="site-logo"><?= e(SITE_NAME) ?></a>
        <ul class="nav-links">
            <li><a href="<?= BASE_URL ?>/">Portfolio</a></li>
            <li><a href="<?= BASE_URL ?>/blog.php">Blog</a></li>
        </ul>
    </nav>
</header>

<!-- HERO -->
<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="hero-eyebrow">Available for work</div>
        <h1 class="hero-title">Hi, I'm<br><span class="accent"><?= e(SITE_AUTHOR) ?></span>.</h1>
        <p class="hero-sub">[ ここに自己紹介・キャッチコピーを入れる ]<br>エンジニア / クリエイター / ブロガー</p>
        <div class="hero-cta">
            <a href="<?= BASE_URL ?>/blog.php" class="btn-hp">📝 Blog を読む</a>
            <a href="#about" class="btn-hg">About Me ↓</a>
        </div>
    </div>
    <div class="scroll-hint">
        <span>scroll</span>
        <div class="scroll-arrow"></div>
    </div>
</section>

<!-- STATS -->
<div class="stats-bar">
    <div class="stats-inner">
        <div class="stat-item"><div class="stat-num">0+</div><div class="stat-label">Projects</div></div>
        <div class="stat-item"><div class="stat-num">0+</div><div class="stat-label">Blog Posts</div></div>
        <div class="stat-item"><div class="stat-num">0+</div><div class="stat-label">Technologies</div></div>
        <div class="stat-item"><div class="stat-num">∞</div><div class="stat-label">Curiosity</div></div>
    </div>
</div>

<!-- ABOUT -->
<section id="about" class="section section-dark">
    <div class="section-inner">
        <div class="section-eyebrow">About Me</div>
        <div class="about-grid">
            <div class="about-avatar-wrap">
                <div class="about-avatar">👤
                    <div class="about-badge">🚀 Open to work</div>
                </div>
            </div>
            <div class="about-text">
                <h2 class="section-title" style="margin-bottom:24px;">[ 名前・肩書きを入れる ]</h2>
                <p>[ 自己紹介・経歴などをここに書く。どんな技術が好きか、どんなものを作ってきたかなど。 ]</p>
                <p>[ 趣味・モチベーション・目標なども自由に書いてOK。 ]</p>
                <div class="skill-tags">
                    <span class="skill-tag">PHP</span>
                    <span class="skill-tag">JavaScript</span>
                    <span class="skill-tag">HTML / CSS</span>
                    <span class="skill-tag">SQLite</span>
                    <span class="skill-tag">Linux / Termux</span>
                    <span class="skill-tag">[ + 追加 ]</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- WORKS -->
<section id="works" class="section section-light">
    <div class="section-inner">
        <div class="section-eyebrow">Works</div>
        <h2 class="section-title">制作物</h2>
        <div class="works-grid">
            <div class="work-placeholder"><span>＋</span><strong>作品を追加</strong><p>プロジェクト・制作物・OSSなどをここに掲載する</p></div>
            <div class="work-placeholder"><span>＋</span><strong>作品を追加</strong><p>GitHub リポジトリのリンクなど</p></div>
            <div class="work-placeholder"><span>＋</span><strong>作品を追加</strong><p>デモサイト・スクリーンショットなど</p></div>
        </div>
    </div>
</section>

<!-- CONTACT -->
<section id="contact" class="section section-dark" style="padding-top:0;">
    <div class="section-inner">
        <div class="contact-box">
            <h2>一緒に何か作りませんか？</h2>
            <p>[ 連絡先・SNS・GitHub などのリンクをここに設定する ]</p>
            <div class="contact-links">
                <a href="mailto:your@email.com" class="clink primary">✉ メールを送る</a>
                <a href="https://github.com/" class="clink" target="_blank" rel="noopener">GitHub</a>
                <a href="<?= BASE_URL ?>/blog.php" class="clink">Blog を読む</a>
            </div>
        </div>
    </div>
</section>

<footer class="site-footer">
    <div class="footer-inner">
        <p>&copy; <?= date('Y') ?> <?= e(SITE_AUTHOR) ?>. All rights reserved.</p>
    </div>
</footer>

<script>
const header = document.getElementById('site-header');
window.addEventListener('scroll', () => {
    header.classList.toggle('scrolled', window.scrollY > 60);
}, {passive:true});

const obs = new IntersectionObserver((entries) => {
    entries.forEach((e, i) => {
        if (e.isIntersecting) setTimeout(() => e.target.classList.add('visible'), i * 120);
    });
}, {threshold: 0.15});
document.querySelectorAll('.stat-item').forEach(el => obs.observe(el));

document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
        const t = document.querySelector(a.getAttribute('href'));
        if (t) { e.preventDefault(); t.scrollIntoView({behavior:'smooth'}); }
    });
});
</script>
</body>
</html>
