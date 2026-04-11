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
    <link rel="preload" href="<?= BASE_URL ?>/assets/bg_hero.webp" as="image" fetchpriority="high">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Fontsを非同期で読み込み（レンダリングブロック解消） -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Zen+Kurenaido&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Zen+Kurenaido&display=swap" rel="stylesheet"></noscript>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/main.css">
    <style>
    *{margin:0;padding:0;box-sizing:border-box}
    :root{--zen:'Zen Kurenaido',sans-serif}

    /* ===== NAV ===== */
    .site-header{background:rgba(6,13,31,.55)!important;backdrop-filter:blur(20px) saturate(180%);-webkit-backdrop-filter:blur(20px) saturate(180%);border-bottom:1px solid rgba(255,255,255,.08)!important;transition:background .4s,box-shadow .4s;height:64px}
    .site-header.scrolled{background:rgba(6,13,31,.95)!important;box-shadow:0 4px 32px rgba(0,0,0,.5);border-bottom-color:rgba(255,255,255,.12)!important}
    .nav-inner{height:100%;max-width:1200px;margin:0 auto;padding:0 32px;display:flex;align-items:center;justify-content:space-between}
    .site-logo{font-family:var(--zen);font-size:1.3rem;color:#fff;letter-spacing:.05em;text-shadow:0 0 20px rgba(125,211,252,.5)}
    .site-logo:hover{color:#7dd3fc}
    .nav-links{display:flex;gap:4px;list-style:none}
    .nav-links a{color:rgba(255,255,255,.75);font-size:.85rem;font-weight:500;padding:7px 16px;border-radius:99px;transition:all .2s;border:1px solid transparent}
    .nav-links a:hover{color:#fff;border-color:rgba(255,255,255,.2);background:rgba(255,255,255,.08)}
    .nav-links a.active{color:#7dd3fc;border-color:rgba(125,211,252,.3);background:rgba(125,211,252,.08)}

    /* ===== HERO ===== */
    .hero{position:relative;min-height:100svh;display:flex;flex-direction:column;align-items:center;justify-content:center;overflow:hidden;text-align:center}
    .hero-bg{position:absolute;inset:0;background:url('<?= BASE_URL ?>/assets/bg_hero.webp') center/cover no-repeat;animation:bgDrift 20s ease-in-out infinite alternate;z-index:0}
    @keyframes bgDrift{0%{transform:scale(1.08)}100%{transform:scale(1.14) translate(-2%,-1%)}}
    .hero-overlay{position:absolute;inset:0;background:linear-gradient(160deg,rgba(0,20,60,.6) 0%,rgba(0,60,160,.25) 50%,rgba(0,0,0,.65) 100%);z-index:1}

    /* パーティクル */
    .hero-particles{position:absolute;inset:0;z-index:2;overflow:hidden}
    .particle{position:absolute;border-radius:50%;animation:float linear infinite;opacity:0}
    @keyframes float{0%{transform:translateY(100vh) scale(0);opacity:0}10%{opacity:1}90%{opacity:.6}100%{transform:translateY(-10vh) scale(1);opacity:0}}

    .hero-content{position:relative;z-index:3;padding:0 24px;max-width:900px}

    .hero-eyebrow{display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.1);backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,.2);color:rgba(255,255,255,.9);font-size:.75rem;font-weight:600;letter-spacing:.15em;text-transform:uppercase;padding:8px 20px;border-radius:99px;margin-bottom:36px;animation:fadeUp .8s ease both}
    .hero-eyebrow-dot{width:7px;height:7px;border-radius:50%;background:#7df3a0;box-shadow:0 0 10px #7df3a0,0 0 20px #7df3a0;animation:blink 2s ease infinite}
    @keyframes blink{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.3;transform:scale(.8)}}

    .hero-title{font-family:var(--zen);font-size:clamp(3rem,9vw,7rem);font-weight:400;line-height:1.05;color:#fff;margin-bottom:8px;animation:fadeUp .8s .1s ease both;text-shadow:0 0 60px rgba(125,211,252,.2)}
    .hero-title-sub{font-size:clamp(1.8rem,5vw,4rem);font-weight:900;letter-spacing:-0.04em;color:#fff;margin-bottom:28px;animation:fadeUp .8s .2s ease both}
    .hero-title-sub .accent{background:linear-gradient(90deg,#7dd3fc 0%,#a78bfa 40%,#f0abfc 80%,#7dd3fc 100%);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;background-size:300%;animation:gshift 5s ease infinite}
    @keyframes gshift{0%{background-position:0%}50%{background-position:100%}100%{background-position:0%}}

    .hero-sub{font-size:clamp(.95rem,2.2vw,1.2rem);color:rgba(255,255,255,.7);line-height:1.7;margin-bottom:48px;animation:fadeUp .8s .3s ease both}

    .hero-cta{display:flex;gap:14px;justify-content:center;flex-wrap:wrap;animation:fadeUp .8s .4s ease both}
    .btn-hp{display:inline-flex;align-items:center;gap:8px;padding:14px 32px;background:#fff;color:#0a1628;font-size:.9rem;font-weight:800;border-radius:99px;text-decoration:none;transition:all .25s;box-shadow:0 4px 24px rgba(0,0,0,.3),0 0 0 0 rgba(255,255,255,.4)}
    .btn-hp:hover{transform:translateY(-3px) scale(1.04);box-shadow:0 12px 40px rgba(0,0,0,.35),0 0 0 6px rgba(255,255,255,.1);color:#0a1628}
    .btn-hg{display:inline-flex;align-items:center;gap:8px;padding:14px 32px;background:rgba(255,255,255,.1);backdrop-filter:blur(8px);color:#fff;font-size:.9rem;font-weight:700;border-radius:99px;border:1px solid rgba(255,255,255,.25);text-decoration:none;transition:all .25s}
    .btn-hg:hover{background:rgba(255,255,255,.2);transform:translateY(-3px);color:#fff;border-color:rgba(255,255,255,.4)}

    .scroll-hint{position:absolute;bottom:40px;left:50%;transform:translateX(-50%);z-index:3;display:flex;flex-direction:column;align-items:center;gap:10px;color:rgba(255,255,255,.4);font-size:.68rem;letter-spacing:.15em;text-transform:uppercase;animation:fadeUp .8s .9s ease both}
    .scroll-line{width:1px;height:48px;background:linear-gradient(to bottom,rgba(255,255,255,.4),transparent);animation:scrollPulse 2s ease infinite}
    @keyframes scrollPulse{0%{transform:scaleY(0);transform-origin:top}50%{transform:scaleY(1);transform-origin:top}51%{transform:scaleY(1);transform-origin:bottom}100%{transform:scaleY(0);transform-origin:bottom}}
    @keyframes fadeUp{from{opacity:0;transform:translateY(32px)}to{opacity:1;transform:translateY(0)}}

    /* ===== STATS ===== */
    .stats-bar{background:#060d1f;padding:56px 24px;border-top:1px solid rgba(255,255,255,.04);border-bottom:1px solid rgba(255,255,255,.04)}
    .stats-inner{max-width:900px;margin:0 auto;display:grid;grid-template-columns:repeat(4,1fr)}
    .stat-item{text-align:center;padding:0 24px;border-right:1px solid rgba(255,255,255,.06);opacity:0;transform:translateY(24px);transition:opacity .7s,transform .7s}
    .stat-item:last-child{border-right:none}
    .stat-item.visible{opacity:1;transform:translateY(0)}
    .stat-num{font-family:var(--zen);font-size:2.8rem;color:#7dd3fc;line-height:1;margin-bottom:10px;text-shadow:0 0 20px rgba(125,211,252,.4)}
    .stat-label{font-size:.72rem;color:rgba(255,255,255,.35);letter-spacing:.12em;text-transform:uppercase}

    /* ===== SECTION ===== */
    .section{padding:100px 24px}
    .section-dark{background:#0a1628;color:#fff}
    .section-light{background:#f4f7ff}
    .section-inner{max-width:1000px;margin:0 auto}
    .section-eyebrow{font-family:var(--zen);font-size:.9rem;color:#7dd3fc;margin-bottom:16px;letter-spacing:.1em}
    .section-title{font-size:clamp(1.8rem,4vw,2.8rem);font-weight:900;letter-spacing:-0.04em;margin-bottom:56px;line-height:1.1}
    .section-dark .section-title{color:#fff}
    .section-light .section-title{color:#0a1628}

    /* ===== ABOUT ===== */
    .about-grid{display:grid;grid-template-columns:1fr 1.4fr;gap:72px;align-items:center}
    .about-avatar-wrap{position:relative;display:flex;justify-content:center}
    .about-avatar{width:280px;height:280px;border-radius:28px;overflow:hidden;position:relative;box-shadow:0 0 0 1px rgba(125,211,252,.2),0 32px 80px rgba(0,0,0,.5);animation:avatarFloat 6s ease-in-out infinite}
    @keyframes avatarFloat{0%,100%{transform:translateY(0) rotate(-1deg)}50%{transform:translateY(-12px) rotate(1deg)}}
    .about-avatar img{width:100%;height:100%;object-fit:cover}
    .about-avatar::after{content:'';position:absolute;inset:0;background:linear-gradient(135deg,rgba(125,211,252,.15),transparent 60%);pointer-events:none}
    .about-glow{position:absolute;width:320px;height:320px;border-radius:50%;background:radial-gradient(circle,rgba(125,211,252,.15),transparent 70%);top:50%;left:50%;transform:translate(-50%,-50%);animation:glowPulse 4s ease-in-out infinite}
    @keyframes glowPulse{0%,100%{opacity:.5;transform:translate(-50%,-50%) scale(1)}50%{opacity:1;transform:translate(-50%,-50%) scale(1.1)}}
    .about-badge{position:absolute;bottom:-12px;right:-12px;background:rgba(6,13,31,.9);border:1px solid rgba(125,211,252,.3);border-radius:12px;padding:10px 16px;font-size:.75rem;color:#7dd3fc;font-weight:700;backdrop-filter:blur(8px)}
    .about-text p{font-size:1.05rem;color:rgba(255,255,255,.72);line-height:1.85;margin-bottom:20px}
    .skill-tags{display:flex;flex-wrap:wrap;gap:8px;margin-top:32px}
    .skill-tag{background:rgba(125,211,252,.08);border:1px solid rgba(125,211,252,.18);color:#7dd3fc;font-size:.78rem;font-weight:600;padding:7px 16px;border-radius:99px;transition:all .2s}
    .skill-tag:hover{background:rgba(125,211,252,.18);border-color:rgba(125,211,252,.4)}

    /* ===== WORKS ===== */
    .works-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:24px}
    .work-placeholder{background:#eef2ff;border:2px dashed #c4d0ff;border-radius:18px;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:48px 24px;text-align:center;color:#94a3b8;min-height:240px;gap:10px;font-size:.85rem;transition:all .2s}
    .work-placeholder:hover{border-color:#a5b4fc;background:#e8edff}
    .work-placeholder span{font-size:2.2rem}

    /* ===== CONTACT ===== */
    .contact-box{border-radius:28px;padding:72px 48px;text-align:center;position:relative;overflow:hidden;background:linear-gradient(135deg,#1a3a8f,#5b21b6)}
    .contact-box::before{content:'';position:absolute;inset:0;background:url('<?= BASE_URL ?>/assets/bg_hero.webp') center/cover;opacity:.07}
    .contact-box::after{content:'';position:absolute;top:-50%;left:-50%;width:200%;height:200%;background:radial-gradient(circle at 60% 40%,rgba(167,139,250,.2),transparent 60%);pointer-events:none}
    .contact-box h2{font-family:var(--zen);font-size:clamp(1.8rem,4vw,2.6rem);color:#fff;margin-bottom:16px;position:relative;z-index:1}
    .contact-box p{color:rgba(255,255,255,.65);margin-bottom:40px;font-size:1rem;position:relative;z-index:1}
    .contact-links{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;position:relative;z-index:1}
    .clink{display:inline-flex;align-items:center;gap:8px;padding:13px 28px;background:rgba(255,255,255,.12);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.2);border-radius:99px;color:#fff;font-weight:700;font-size:.88rem;text-decoration:none;transition:all .2s}
    .clink:hover{background:rgba(255,255,255,.25);transform:translateY(-3px);color:#fff;box-shadow:0 8px 24px rgba(0,0,0,.2)}
    .clink.primary{background:#fff;color:#1a3a8f;border-color:transparent}
    .clink.primary:hover{transform:translateY(-3px);color:#1a3a8f;box-shadow:0 8px 24px rgba(0,0,0,.25)}

    .site-footer{background:#030810;border-top:1px solid rgba(255,255,255,.04)}
    .site-footer .footer-inner{color:rgba(255,255,255,.25)}

    /* ===== アニメーション共通 ===== */
    .reveal{opacity:0;transform:translateY(32px);transition:opacity .8s cubic-bezier(.16,1,.3,1),transform .8s cubic-bezier(.16,1,.3,1)}
    .reveal.visible{opacity:1;transform:translateY(0)}
    .reveal-left{opacity:0;transform:translateX(-40px);transition:opacity .8s cubic-bezier(.16,1,.3,1),transform .8s cubic-bezier(.16,1,.3,1)}
    .reveal-left.visible{opacity:1;transform:translateX(0)}
    .reveal-right{opacity:0;transform:translateX(40px);transition:opacity .8s cubic-bezier(.16,1,.3,1),transform .8s cubic-bezier(.16,1,.3,1)}
    .reveal-right.visible{opacity:1;transform:translateX(0)}


    /* ===== ハンバーガーメニュー（スマホのみ） ===== */
    .hamburger{display:none;flex-direction:column;gap:5px;cursor:pointer;padding:8px;border:none;background:none;z-index:200}
    .hamburger span{display:block;width:24px;height:2px;background:#fff;border-radius:2px;transition:all .3s}
    .hamburger.open span:nth-child(1){transform:translateY(7px) rotate(45deg)}
    .hamburger.open span:nth-child(2){opacity:0;transform:scaleX(0)}
    .hamburger.open span:nth-child(3){transform:translateY(-7px) rotate(-45deg)}

    .nav-drawer{position:fixed;inset:0;background:rgba(6,13,31,.97);backdrop-filter:blur(20px);z-index:150;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;opacity:0;pointer-events:none;transition:opacity .3s}
    .nav-drawer.open{opacity:1;pointer-events:all}
    .nav-drawer a{font-family:var(--zen);font-size:1.8rem;color:rgba(255,255,255,.7);text-decoration:none;padding:12px 32px;transition:color .2s}
    .nav-drawer a:hover{color:#7dd3fc}

    @media(max-width:768px){
        .hamburger{display:flex}
        .nav-links{display:none}

        .about-grid{grid-template-columns:1fr;gap:48px;text-align:center}
        .about-avatar{width:200px;height:200px;margin:0 auto}
        .skill-tags{justify-content:center}
        .contact-box{padding:48px 24px}
        .stats-inner{grid-template-columns:repeat(2,1fr)}
        .stat-item{border-right:none;border-bottom:1px solid rgba(255,255,255,.06);padding:24px 0}
        .stat-item:nth-child(2n){border-right:none}
    }
    </style>
</head>
<body>

<!-- ===== NAV ===== -->
<div class="nav-drawer" id="nav-drawer">
    <a href="<?= BASE_URL ?>/" onclick="closeDrawer()">Portfolio</a>
    <a href="<?= BASE_URL ?>/journey.php" onclick="closeDrawer()">My Journey</a>
    <a href="<?= BASE_URL ?>/blog.php" onclick="closeDrawer()">Blog</a>
</div>
<header class="site-header" id="site-header">
    <nav class="nav-inner">
        <a href="<?= BASE_URL ?>/" class="site-logo"><?= e(SITE_NAME) ?></a>
        <ul class="nav-links">
            <li><a href="<?= BASE_URL ?>/" class="active">Portfolio</a></li>
            <li><a href="<?= BASE_URL ?>/journey.php">My Journey</a></li>
            <li><a href="<?= BASE_URL ?>/blog.php">Blog</a></li>
        </ul>
        <button class="hamburger" id="hamburger" onclick="toggleDrawer()" aria-label="メニュー">
            <span></span><span></span><span></span>
        </button>
    </nav>
</header>

<!-- ===== HERO ===== -->
<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-overlay"></div>
    <div class="hero-particles" id="particles"></div>
    <div class="hero-content">
        <div class="hero-eyebrow">
            <span class="hero-eyebrow-dot"></span>
            🚧 絶賛制作中
        </div>
        <div class="hero-title">Hi, I'm</div>
        <div class="hero-title-sub"><span class="accent"><?= e(SITE_AUTHOR) ?></span>.</div>
        <p class="hero-sub">
            論理的思考と柔軟な発想の共存<br>
            高校生エンジニア
        </p>
        <div class="hero-cta">
            <a href="<?= BASE_URL ?>/journey.php" class="btn-hp">🗺 My Journey</a>
            <a href="<?= BASE_URL ?>/blog.php" class="btn-hg">📝 Blog</a>
        </div>
    </div>
    <div class="scroll-hint">
        <div class="scroll-line"></div>
        <span>scroll</span>
    </div>
</section>

<!-- ===== STATS ===== -->
<div class="stats-bar">
    <div class="stats-inner">
        <div class="stat-item"><div class="stat-num" data-count="0">0</div><div class="stat-label">Projects</div></div>
        <div class="stat-item"><div class="stat-num" data-count="0">0</div><div class="stat-label">Blog Posts</div></div>
        <div class="stat-item"><div class="stat-num" data-count="0">0</div><div class="stat-label">Technologies</div></div>
        <div class="stat-item"><div class="stat-num">∞</div><div class="stat-label">Curiosity</div></div>
    </div>
</div>

<!-- ===== ABOUT ===== -->
<section id="about" class="section section-dark" style="padding-top:100px;">
    <div class="section-inner">
        <div class="section-eyebrow">About Me</div>
        <div class="about-grid">
            <div class="about-avatar-wrap">
                <div class="about-glow"></div>
                <div class="about-avatar">
                    <img src="<?= BASE_URL ?>/assets/avatar.png" alt="akooimak15">
                    <div class="about-badge">🚧 絶賛制作中</div>
                </div>
            </div>
            <div class="about-text">
                <h2 class="section-title" style="margin-bottom:24px;"><?= e(SITE_AUTHOR) ?></h2>
                <p>中学時代よりプログラミングに親しみ、現在は「論理的思考と柔軟な発想の共存」をテーマに開発を行っています。</p>
                <p style="margin-top:12px;">
                    <a href="<?= BASE_URL ?>/journey.php" style="color:#7dd3fc;border-bottom:1px solid rgba(125,211,252,.3);padding-bottom:2px;">
                        My Journey を見る →
                    </a>
                </p>
                <div class="skill-tags">
                    <span class="skill-tag">PHP</span>
                    <span class="skill-tag">JavaScript</span>
                    <span class="skill-tag">HTML / CSS</span>
                    <span class="skill-tag">SQLite</span>
                    <span class="skill-tag">Linux / Termux</span>
                    <span class="skill-tag">Docker</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== WORKS ===== -->
<section id="works" class="section section-light">
    <div class="section-inner">
        <div class="section-eyebrow" style="color:#3ea8ff;">Works</div>
        <h2 class="section-title">制作物</h2>
        <div class="works-grid">
            <div class="work-placeholder"><span>＋</span><strong>作品を追加</strong><p>プロジェクト・制作物・OSSなどをここに掲載する</p></div>
            <div class="work-placeholder"><span>＋</span><strong>作品を追加</strong><p>GitHub リポジトリのリンクなど</p></div>
            <div class="work-placeholder"><span>＋</span><strong>作品を追加</strong><p>デモサイト・スクリーンショットなど</p></div>
        </div>
    </div>
</section>

<!-- ===== CONTACT ===== -->
<section id="contact" class="section section-dark" style="padding-top:0;">
    <div class="section-inner">
        <div class="contact-box">
            <h2>一緒に何か作りませんか？</h2>
            <p>高校生エンジニア akooimak15 へのお問い合わせはこちら</p>
            <div class="contact-links">
                <a href="mailto:your@email.com" class="clink primary">✉ メールを送る</a>
                <a href="https://github.com/akooimak15" class="clink" target="_blank" rel="noopener">GitHub</a>
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
// ハンバーガーメニュー
function toggleDrawer(){
    document.getElementById('hamburger').classList.toggle('open');
    document.getElementById('nav-drawer').classList.toggle('open');
    document.body.style.overflow = document.getElementById('nav-drawer').classList.contains('open') ? 'hidden' : '';
}
function closeDrawer(){
    document.getElementById('hamburger').classList.remove('open');
    document.getElementById('nav-drawer').classList.remove('open');
    document.body.style.overflow = '';
}

// ナビスクロール
const header = document.getElementById('site-header');
window.addEventListener('scroll', () => {
    header.classList.toggle('scrolled', window.scrollY > 60);
}, {passive:true});

// パーティクル生成
const pc = document.getElementById('particles');
for(let i = 0; i < 28; i++){
    const p = document.createElement('div');
    p.className = 'particle';
    const size = Math.random() * 4 + 1;
    const colors = ['rgba(125,211,252,.6)','rgba(167,139,250,.6)','rgba(240,171,252,.5)','rgba(255,255,255,.4)'];
    p.style.cssText = `
        width:${size}px;height:${size}px;
        left:${Math.random()*100}%;
        background:${colors[Math.floor(Math.random()*colors.length)]};
        animation-duration:${Math.random()*12+8}s;
        animation-delay:${Math.random()*8}s;
        box-shadow:0 0 ${size*3}px currentColor;
    `;
    pc.appendChild(p);
}

// スクロールアニメーション
const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry, i) => {
        if(entry.isIntersecting){
            setTimeout(() => entry.target.classList.add('visible'), i * 100);
        }
    });
}, {threshold:0.05, rootMargin:'0px 0px -50px 0px'});

document.querySelectorAll('.stat-item, .reveal, .reveal-left, .reveal-right').forEach(el => observer.observe(el));

// スムーススクロール
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
        const t = document.querySelector(a.getAttribute('href'));
        if(t){e.preventDefault();t.scrollIntoView({behavior:'smooth'});}
    });
});
</script>
</body>
</html>
