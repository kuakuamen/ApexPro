<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ApexPro — Escale sua consultoria online</title>
<link rel="icon" href="{{ asset('favicon.ico') }}">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
  :root {
    --black: #000000;
    --purple: #7B2FBE;
    --blue: #1A73E8;
    --neon: #00E5FF;
    --card: #111111;
    --white: #FFFFFF;
    --gray: #AAAAAA;
    --gradient: linear-gradient(135deg, #7B2FBE, #1A73E8);
    --gradient-h: linear-gradient(90deg, #7B2FBE, #1A73E8);
  }

  * { margin: 0; padding: 0; box-sizing: border-box; }
  html { scroll-behavior: smooth; }

  body {
    background: var(--black);
    color: var(--white);
    font-family: 'Montserrat', sans-serif;
    overflow-x: hidden;
  }

  body::before {
    content: '';
    position: fixed;
    inset: 0;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
    pointer-events: none;
    z-index: 1000;
    opacity: 0.4;
  }

  /* ── NAVBAR ── */
  nav {
    position: fixed;
    top: 0; left: 0; right: 0;
    z-index: 999;
    padding: 20px 60px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: rgba(0,0,0,0.7);
    backdrop-filter: blur(20px);
    border-bottom: 1px solid rgba(123,47,190,0.2);
  }

  .logo { display: flex; align-items: center; gap: 10px; }
  .logo img { width: 40px; height: 40px; border-radius: 10px; }
  .logo span {
    font-family: 'Montserrat', sans-serif;
    font-size: 22px;
    font-weight: 900;
    background: var(--gradient-h);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    letter-spacing: 2px;
  }

  .nav-cta {
    background: var(--gradient-h);
    color: white;
    border: none;
    padding: 12px 28px;
    border-radius: 50px;
    font-family: 'Montserrat', sans-serif;
    font-weight: 700;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 0 20px rgba(123,47,190,0.4);
    text-decoration: none;
    display: inline-block;
  }
  .nav-cta:hover {
    transform: translateY(-2px);
    box-shadow: 0 0 40px rgba(123,47,190,0.7);
    color: white;
  }

  /* ── HERO ── */
  #hero {
    position: relative;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    overflow: hidden;
    padding: 120px 40px 80px;
  }

  .hero-bg {
    position: absolute;
    inset: 0;
    background:
      radial-gradient(ellipse at 20% 50%, rgba(123,47,190,0.3) 0%, transparent 60%),
      radial-gradient(ellipse at 80% 50%, rgba(26,115,232,0.3) 0%, transparent 60%),
      radial-gradient(ellipse at 50% 100%, rgba(0,229,255,0.1) 0%, transparent 50%),
      linear-gradient(180deg, #000 0%, #0a0010 50%, #000 100%);
  }

  .hero-grid {
    position: absolute;
    inset: 0;
    background-image:
      linear-gradient(rgba(123,47,190,0.08) 1px, transparent 1px),
      linear-gradient(90deg, rgba(123,47,190,0.08) 1px, transparent 1px);
    background-size: 60px 60px;
    mask-image: radial-gradient(ellipse at center, black 30%, transparent 80%);
  }

  .hero-content { position: relative; z-index: 2; max-width: 900px; }

  .hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(123,47,190,0.15);
    border: 1px solid rgba(123,47,190,0.4);
    border-radius: 50px;
    padding: 8px 20px;
    font-size: 13px;
    font-weight: 600;
    color: var(--neon);
    margin-bottom: 32px;
    letter-spacing: 1px;
    text-transform: uppercase;
  }

  .hero-badge::before {
    content: '';
    width: 8px; height: 8px;
    background: var(--neon);
    border-radius: 50%;
    animation: pulse 2s infinite;
  }

  @keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.3); }
  }

  .hero-title {
    font-size: clamp(42px, 6vw, 72px);
    font-weight: 900;
    line-height: 1.1;
    margin-bottom: 24px;
    letter-spacing: -1px;
  }

  .hero-title span {
    background: var(--gradient-h);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }

  .hero-sub {
    font-size: clamp(16px, 2vw, 22px);
    font-weight: 400;
    color: var(--gray);
    line-height: 1.7;
    margin-bottom: 48px;
    max-width: 650px;
    margin-left: auto;
    margin-right: auto;
  }

  .hero-sub strong { color: var(--neon); font-weight: 700; }

  .btn-primary {
    background: var(--gradient-h);
    color: white;
    border: none;
    padding: 20px 48px;
    border-radius: 50px;
    font-family: 'Montserrat', sans-serif;
    font-weight: 800;
    font-size: 17px;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 0 40px rgba(123,47,190,0.5), 0 0 80px rgba(123,47,190,0.2);
    text-decoration: none;
    display: inline-block;
    letter-spacing: 0.5px;
  }

  .btn-primary:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 0 60px rgba(123,47,190,0.8), 0 0 120px rgba(123,47,190,0.3);
    color: white;
  }

  .hero-stats {
    display: flex;
    justify-content: center;
    gap: 60px;
    margin-top: 72px;
    padding-top: 48px;
    border-top: 1px solid rgba(255,255,255,0.07);
  }

  .stat-item { text-align: center; }

  .stat-number {
    font-size: 36px;
    font-weight: 900;
    background: var(--gradient-h);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    display: block;
  }

  .stat-label {
    font-size: 13px;
    color: var(--gray);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 1px;
  }

  /* ── SECTIONS COMMON ── */
  section { padding: 100px 60px; position: relative; }

  .section-label {
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 3px;
    text-transform: uppercase;
    color: var(--neon);
    margin-bottom: 16px;
    display: block;
  }

  .section-title {
    font-size: clamp(32px, 4vw, 52px);
    font-weight: 800;
    line-height: 1.15;
    margin-bottom: 20px;
  }

  .section-title span {
    background: var(--gradient-h);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }

  .container { max-width: 1100px; margin: 0 auto; }

  /* ── PAIN ── */
  #pain {
    background: var(--black);
    border-top: 1px solid rgba(255,255,255,0.05);
  }

  .pain-inner {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 80px;
    align-items: center;
  }

  .pain-text .section-title { margin-bottom: 32px; }

  .pain-list { list-style: none; display: flex; flex-direction: column; gap: 20px; }

  .pain-list li {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    font-size: 16px;
    color: var(--gray);
    line-height: 1.6;
  }

  .pain-list li::before {
    content: '✗';
    color: var(--purple);
    font-weight: 900;
    font-size: 18px;
    flex-shrink: 0;
    margin-top: 1px;
  }

  .pain-card {
    background: var(--card);
    border: 1px solid rgba(123,47,190,0.3);
    border-radius: 20px;
    padding: 40px;
    position: relative;
    overflow: hidden;
  }

  .pain-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 2px;
    background: var(--gradient-h);
  }

  .pain-highlight {
    display: inline-block;
    font-size: clamp(48px, 6vw, 80px);
    font-weight: 900;
    line-height: 1;
    color: white;
    margin-bottom: 8px;
  }

  .pain-card-text {
    font-size: 16px;
    color: var(--gray);
    line-height: 1.6;
    margin-bottom: 24px;
  }

  .pain-divider { height: 1px; background: rgba(255,255,255,0.07); margin: 24px 0; }

  /* ── FEATURES ── */
  #features { background: #050505; }

  .features-header { text-align: center; margin-bottom: 64px; }

  .features-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
  }

  .feature-card {
    background: var(--card);
    border-radius: 16px;
    padding: 36px 28px;
    position: relative;
    overflow: hidden;
    transition: all 0.3s;
    border: 1px solid rgba(255,255,255,0.05);
  }

  .feature-card::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 16px;
    padding: 1px;
    background: var(--gradient);
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    opacity: 0;
    transition: opacity 0.3s;
  }

  .feature-card:hover::before { opacity: 1; }
  .feature-card:hover { transform: translateY(-4px); }

  .feature-icon { font-size: 36px; margin-bottom: 20px; display: block; }
  .feature-title { font-size: 17px; font-weight: 700; margin-bottom: 10px; color: white; }
  .feature-desc { font-size: 14px; color: var(--gray); line-height: 1.6; }

  /* ── SCALE ── */
  #scale {
    background: var(--gradient);
    text-align: center;
    position: relative;
    overflow: hidden;
  }

  #scale::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Ccircle cx='30' cy='30' r='1'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
  }

  .scale-content { position: relative; z-index: 2; }

  .scale-numbers {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 40px;
    margin: 48px 0;
  }

  .scale-num {
    font-size: clamp(60px, 10vw, 120px);
    line-height: 1;
    color: white;
    font-weight: 900;
    text-shadow: 0 0 40px rgba(0,0,0,0.3);
  }

  .scale-num.before { opacity: 0.5; }
  .scale-arrow { font-size: clamp(40px, 6vw, 80px); color: white; opacity: 0.7; }

  .scale-label {
    font-size: clamp(16px, 2vw, 22px);
    font-weight: 400;
    color: rgba(255,255,255,0.85);
    line-height: 1.7;
    max-width: 600px;
    margin: 0 auto;
  }

  /* ── PRICING ── */
  #pricing { background: var(--black); }

  .pricing-header { text-align: center; margin-bottom: 64px; }

  .pricing-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
    align-items: start;
  }

  .pricing-card {
    background: var(--card);
    border-radius: 20px;
    padding: 40px 32px;
    position: relative;
    border: 1px solid rgba(255,255,255,0.07);
    transition: all 0.3s;
  }

  .pricing-card:hover { transform: translateY(-4px); }

  .pricing-card.featured {
    background: #130820;
    border: 1px solid rgba(123,47,190,0.5);
    transform: scale(1.05);
    box-shadow: 0 0 60px rgba(123,47,190,0.3), 0 0 120px rgba(123,47,190,0.1);
  }

  .pricing-card.featured:hover { transform: scale(1.05) translateY(-4px); }

  .featured-badge {
    position: absolute;
    top: -14px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--gradient-h);
    color: white;
    font-size: 12px;
    font-weight: 800;
    padding: 6px 20px;
    border-radius: 50px;
    white-space: nowrap;
    letter-spacing: 0.5px;
  }

  .plan-icon { font-size: 32px; margin-bottom: 16px; display: block; }
  .plan-name { font-size: 22px; font-weight: 800; margin-bottom: 8px; }

  .plan-desc {
    font-size: 13px;
    color: var(--gray);
    margin-bottom: 28px;
    line-height: 1.5;
  }

  .plan-price { margin-bottom: 32px; }

  .price-from { font-size: 13px; color: var(--gray); display: block; margin-bottom: 4px; }

  .price-value {
    font-size: 48px;
    font-weight: 900;
    line-height: 1;
    background: var(--gradient-h);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }

  .price-period { font-size: 14px; color: var(--gray); }

  .plan-features {
    list-style: none;
    margin-bottom: 36px;
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  .plan-features li {
    font-size: 14px;
    color: var(--gray);
    display: flex;
    align-items: center;
    gap: 10px;
    line-height: 1.4;
  }

  .plan-features li::before {
    content: '✓';
    color: var(--neon);
    font-weight: 900;
    flex-shrink: 0;
  }

  .btn-plan {
    width: 100%;
    padding: 16px;
    border-radius: 12px;
    font-family: 'Montserrat', sans-serif;
    font-weight: 700;
    font-size: 15px;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
    display: block;
    text-align: center;
    letter-spacing: 0.3px;
  }

  .btn-plan-outline {
    background: transparent;
    border: 1px solid rgba(123,47,190,0.5);
    color: white;
  }

  .btn-plan-outline:hover {
    background: rgba(123,47,190,0.15);
    border-color: var(--purple);
    transform: translateY(-2px);
    color: white;
  }

  .btn-plan-gradient {
    background: var(--gradient-h);
    border: none;
    color: white;
    box-shadow: 0 0 30px rgba(123,47,190,0.4);
  }

  .btn-plan-gradient:hover {
    transform: translateY(-2px);
    box-shadow: 0 0 50px rgba(123,47,190,0.7);
    color: white;
  }

  /* ── GUARANTEE ── */
  #guarantee {
    background: #050505;
    border-top: 1px solid rgba(255,255,255,0.05);
    border-bottom: 1px solid rgba(255,255,255,0.05);
    text-align: center;
  }

  .guarantee-inner { max-width: 700px; margin: 0 auto; }

  .guarantee-icon { font-size: 64px; margin-bottom: 24px; display: block; }

  .guarantee-title {
    font-size: clamp(24px, 3vw, 36px);
    font-weight: 800;
    line-height: 1.3;
    margin-bottom: 16px;
  }

  .guarantee-title span {
    background: var(--gradient-h);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }

  .guarantee-text { font-size: 17px; color: var(--gray); line-height: 1.7; }

  /* ── FAQ ── */
  #faq { background: var(--black); }

  .faq-header { text-align: center; margin-bottom: 64px; }

  .faq-list { max-width: 780px; margin: 0 auto; display: flex; flex-direction: column; gap: 12px; }

  .faq-item {
    background: var(--card);
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.05);
    border-left: 3px solid var(--purple);
    transition: border-color 0.3s;
  }

  .faq-item:hover { border-left-color: var(--neon); }

  .faq-question {
    width: 100%;
    background: none;
    border: none;
    padding: 24px 28px;
    text-align: left;
    font-family: 'Montserrat', sans-serif;
    font-size: 16px;
    font-weight: 700;
    color: white;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
  }

  .faq-question::after {
    content: '+';
    font-size: 24px;
    color: var(--purple);
    flex-shrink: 0;
    transition: transform 0.3s;
  }

  .faq-item.open .faq-question::after { transform: rotate(45deg); color: var(--neon); }

  .faq-answer { max-height: 0; overflow: hidden; transition: max-height 0.3s ease; }
  .faq-item.open .faq-answer { max-height: 200px; }

  .faq-answer-inner { padding: 0 28px 24px; font-size: 15px; color: var(--gray); line-height: 1.7; }

  /* ── FINAL CTA ── */
  #cta-final {
    background: var(--gradient);
    text-align: center;
    position: relative;
    overflow: hidden;
    padding: 120px 60px;
  }

  #cta-final::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse at center bottom, rgba(0,0,0,0.4) 0%, transparent 70%);
  }

  .cta-content { position: relative; z-index: 2; max-width: 800px; margin: 0 auto; }
  .cta-pre { font-size: 18px; font-weight: 400; color: rgba(255,255,255,0.8); margin-bottom: 24px; line-height: 1.7; }

  .cta-title {
    font-size: clamp(32px, 5vw, 60px);
    font-weight: 900;
    line-height: 1.1;
    margin-bottom: 20px;
    letter-spacing: -1px;
  }

  .cta-sub { font-size: 18px; color: rgba(255,255,255,0.85); margin-bottom: 48px; line-height: 1.6; }

  .btn-white {
    background: white;
    color: var(--purple);
    border: none;
    padding: 20px 56px;
    border-radius: 50px;
    font-family: 'Montserrat', sans-serif;
    font-weight: 800;
    font-size: 17px;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 0 40px rgba(0,0,0,0.2);
    text-decoration: none;
    display: inline-block;
  }

  .btn-white:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 0 60px rgba(0,0,0,0.3);
    color: var(--purple);
  }

  /* ── FOOTER ── */
  footer {
    background: #050505;
    padding: 40px 60px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-top: 1px solid rgba(255,255,255,0.05);
  }

  .footer-logo {
    font-size: 20px;
    font-weight: 900;
    background: var(--gradient-h);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    letter-spacing: 2px;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .footer-logo img { width: 32px; height: 32px; border-radius: 8px; }
  .footer-text { font-size: 13px; color: var(--gray); }

  /* ── ANIMATIONS ── */
  .fade-up {
    opacity: 0;
    transform: translateY(30px);
    transition: opacity 0.7s ease, transform 0.7s ease;
  }

  .fade-up.visible { opacity: 1; transform: translateY(0); }

  /* ── RESPONSIVE ── */
  @media (max-width: 768px) {
    nav { padding: 16px 24px; }
    section { padding: 80px 24px; }
    .pain-inner { grid-template-columns: 1fr; gap: 40px; }
    .features-grid { grid-template-columns: 1fr; }
    .pricing-grid { grid-template-columns: 1fr; }
    .pricing-card.featured { transform: scale(1); }
    .scale-numbers { gap: 20px; }
    footer { flex-direction: column; gap: 16px; text-align: center; }
    .hero-stats { gap: 30px; flex-wrap: wrap; }
  }
</style>
</head>
<body>

<!-- NAVBAR -->
<nav>
  <div class="logo">
    <img src="{{ asset('img/favicons/apple-touch-icon.png') }}" alt="ApexPro">
  </div>
  <a href="#pricing" class="nav-cta">Quero começar agora</a>
</nav>

<!-- HERO -->
<section id="hero">
  <div class="hero-bg"></div>
  <div class="hero-grid"></div>
  <div class="hero-content">
    <div class="hero-badge">Plataforma para Personal Trainers</div>
    <h1 class="hero-title">
      Escale sua consultoria online<br>
      <span>sem abrir mão da sua vida.</span>
    </h1>
    <p class="hero-sub">
      A plataforma com IA que monta o protocolo completo do seu aluno em <strong>40 segundos.</strong>
    </p>
    <a href="#pricing" class="btn-primary">Quero começar agora</a>
    <div class="hero-stats">
      <div class="stat-item">
        <span class="stat-number">40s</span>
        <span class="stat-label">Por protocolo</span>
      </div>
      <div class="stat-item">
        <span class="stat-number">1.000+</span>
        <span class="stat-label">Alunos escaláveis</span>
      </div>
      <div class="stat-item">
        <span class="stat-number">10x</span>
        <span class="stat-label">Mais produtivo</span>
      </div>
    </div>
  </div>
</section>

<!-- PAIN -->
<section id="pain">
  <div class="container">
    <div class="pain-inner">
      <div class="pain-text fade-up">
        <span class="section-label">A realidade do personal</span>
        <h2 class="section-title">Você reconhece <span>essa rotina?</span></h2>
        <ul class="pain-list">
          <li>Das 6h às 23h na academia, sem tempo para almoçar</li>
          <li>Final de semana perdido atualizando alunos de consultoria</li>
          <li>1 a 2 horas para montar 1 protocolo por aluno</li>
          <li>Família e amigos esperando enquanto você trabalha</li>
          <li>Renda travada porque seu tempo é limitado</li>
        </ul>
      </div>
      <div class="pain-card fade-up">
        <div class="pain-highlight">2h</div>
        <p class="pain-card-text">É quanto tempo você gasta hoje para montar 1 protocolo completo de consultoria.</p>
        <div class="pain-divider"></div>
        <div class="pain-highlight" style="font-size: clamp(32px,4vw,56px); color: #00E5FF;">10 alunos =</div>
        <p class="pain-card-text" style="margin-top: 8px;">fim de semana inteiro perdido. Toda semana. Todo mês.</p>
      </div>
    </div>
  </div>
</section>

<!-- FEATURES -->
<section id="features">
  <div class="container">
    <div class="features-header fade-up">
      <span class="section-label">Funcionalidades</span>
      <h2 class="section-title">A plataforma que <span>trabalha por você</span></h2>
      <p style="color: var(--gray); font-size: 18px; margin-top: 12px;">Tudo que você precisa para sua consultoria online em 1 lugar.</p>
    </div>
    <div class="features-grid">
      <div class="feature-card fade-up">
        <span class="feature-icon">🤖</span>
        <div class="feature-title">IA que monta treino em 40 segundos</div>
        <div class="feature-desc">Insira a avaliação, suba as fotos — a inteligência artificial monta o protocolo completo automaticamente.</div>
      </div>
      <div class="feature-card fade-up">
        <span class="feature-icon">🧠</span>
        <div class="feature-title">Avaliação física e correção postural</div>
        <div class="feature-desc">Identifica músculos encurtados, alongados e correções posturais com precisão para cada aluno.</div>
      </div>
      <div class="feature-card fade-up">
        <span class="feature-icon">📸</span>
        <div class="feature-title">Comparativo antes e depois</div>
        <div class="feature-desc">Visualize a evolução do aluno com comparativos visuais, percentual de gordura e ganho muscular.</div>
      </div>
      <div class="feature-card fade-up">
        <span class="feature-icon">💰</span>
        <div class="feature-title">Gestão financeira completa</div>
        <div class="feature-desc">Controle quantos alunos estão pagando, previsão de faturamento e métricas do seu negócio.</div>
      </div>
      <div class="feature-card fade-up">
        <span class="feature-icon">📈</span>
        <div class="feature-title">Evolução detalhada do aluno</div>
        <div class="feature-desc">Acompanhe em dados reais quanto cada aluno evoluiu em massa muscular, gordura e medidas.</div>
      </div>
      <div class="feature-card fade-up">
        <span class="feature-icon">👤</span>
        <div class="feature-title">Perfil completo de cada aluno</div>
        <div class="feature-desc">Histórico, anamnese, treinos, evolução e financeiro de cada aluno centralizado em um só lugar.</div>
      </div>
    </div>
  </div>
</section>

<!-- SCALE -->
<section id="scale">
  <div class="scale-content fade-up">
    <span class="section-label" style="color: rgba(255,255,255,0.7);">O poder da escala</span>
    <h2 class="section-title" style="color: white;">Você que atendia <br>poucos alunos online…</h2>
    <div class="scale-numbers">
      <span class="scale-num before">30</span>
      <span class="scale-arrow">→</span>
      <span class="scale-num">1.000</span>
    </div>
    <p class="scale-label">Mesmo tempo. Mais alunos. Muito mais dinheiro.<br>A Apex elimina o limite da sua consultoria.</p>
  </div>
</section>

<!-- PRICING -->
<section id="pricing">
  <div class="container">
    <div class="pricing-header fade-up">
      <span class="section-label">Planos</span>
      <h2 class="section-title">Escolha seu <span>plano</span></h2>
      <p style="color: var(--gray); font-size: 18px; margin-top: 12px;">Comece agora e escale no seu ritmo.</p>
    </div>
    <div class="pricing-grid">
      @php
        $planIcons = ['🌱', '⚡', '🚀'];
        $planIndex = 0;
        $planKeys = array_keys($plans);
        $featuredKey = 'plan_pro';
      @endphp

      @foreach($plans as $planId => $plan)
        @php
          $isFeatured = $planId === $featuredKey;
          $icon = $planIcons[$planIndex] ?? '⭐';
          $planIndex++;
        @endphp

        <div class="pricing-card {{ $isFeatured ? 'featured' : '' }} fade-up">
          @if($isFeatured)
            <div class="featured-badge">⭐ Mais escolhido</div>
          @endif

          <span class="plan-icon">{{ $icon }}</span>
          <div class="plan-name">{{ $plan['name'] }}</div>
          <div class="plan-desc">
            @if($planId === 'plan_elite')
              Para gerenciar <strong style="color: white;">100+ alunos</strong> de consultoria.
            @else
              Para gerenciar até <strong style="color: white;">{{ $plan['max_students'] }} alunos</strong> de consultoria.
            @endif
          </div>

          <div class="plan-price">
            <span class="price-from">A partir de</span>
            <span class="price-value">R$ {{ number_format($plan['price'], 2, ',', '.') }}</span>
            <span class="price-period">/mês</span>
          </div>

          <ul class="plan-features">
            @foreach($plan['features'] as $feature)
              <li>{{ $feature }}</li>
            @endforeach
          </ul>

          <a href="{{ route('plans.checkout', $planId) }}" class="btn-plan {{ $isFeatured ? 'btn-plan-gradient' : 'btn-plan-outline' }}">
            {{ $isFeatured ? 'Quero o ' . $plan['name'] : 'Começar agora' }}
          </a>
        </div>
      @endforeach
    </div>
  </div>
</section>

<!-- GUARANTEE -->
<section id="guarantee">
  <div class="guarantee-inner fade-up">
    <span class="guarantee-icon">🛡️</span>
    <h2 class="guarantee-title">Teste a <span>ApexPro</span> por <span>7 dias</span> sem risco.</h2>
    <p class="guarantee-text">Se não gostar por qualquer motivo, devolvemos seu dinheiro. Sem burocracia. Sem perguntas.</p>
  </div>
</section>

<!-- FAQ -->
<section id="faq">
  <div class="container">
    <div class="faq-header fade-up">
      <span class="section-label">Dúvidas</span>
      <h2 class="section-title">Perguntas <span>frequentes</span></h2>
    </div>
    <div class="faq-list">
      <div class="faq-item fade-up">
        <button class="faq-question" onclick="toggleFaq(this)">Preciso ser expert em tecnologia?</button>
        <div class="faq-answer"><div class="faq-answer-inner">Não. A Apex foi desenvolvida para ser simples e intuitiva. Em poucos minutos você já consegue usar todas as funcionalidades sem nenhum conhecimento técnico.</div></div>
      </div>
      <div class="faq-item fade-up">
        <button class="faq-question" onclick="toggleFaq(this)">Funciona para consultoria online?</button>
        <div class="faq-answer"><div class="faq-answer-inner">Sim! A Apex foi criada especificamente para personal trainers que trabalham com consultoria online. Todas as funcionalidades são pensadas para esse modelo de negócio.</div></div>
      </div>
      <div class="faq-item fade-up">
        <button class="faq-question" onclick="toggleFaq(this)">Quantos alunos posso cadastrar?</button>
        <div class="faq-answer"><div class="faq-answer-inner">Depende do plano escolhido. Cada plano tem um limite de alunos definido — verifique os detalhes de cada opção acima.</div></div>
      </div>
      <div class="faq-item fade-up">
        <button class="faq-question" onclick="toggleFaq(this)">Como funciona a IA da Apex?</button>
        <div class="faq-answer"><div class="faq-answer-inner">Você insere a avaliação física do aluno e sobe as fotos. Nossa IA analisa os dados e monta o protocolo de treino completo em menos de 40 segundos, levando em conta as correções posturais e objetivos do aluno.</div></div>
      </div>
      <div class="faq-item fade-up">
        <button class="faq-question" onclick="toggleFaq(this)">Posso cancelar quando quiser?</button>
        <div class="faq-answer"><div class="faq-answer-inner">Sim. Não há fidelidade ou multa. Você pode cancelar a qualquer momento diretamente pela plataforma.</div></div>
      </div>
    </div>
  </div>
</section>

<!-- FINAL CTA -->
<section id="cta-final">
  <div class="cta-content fade-up">
    <p class="cta-pre">Você não quer só ter o fim de semana livre.</p>
    <h2 class="cta-title">Você quer faturar mais.<br>Atender mais alunos.<br>Trabalhar menos.</h2>
    <p class="cta-sub">A Apex é a única plataforma que torna isso possível para o personal trainer.</p>
    <a href="#pricing" class="btn-white">Quero escalar minha consultoria agora</a>
  </div>
</section>

<!-- WHATSAPP COMMUNITY -->
<section id="whatsapp-community" style="padding: 80px 24px; background: #0d1117; position: relative; overflow: hidden;">
  <div style="position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); width: 600px; height: 600px; background: radial-gradient(circle, rgba(37,211,102,0.12) 0%, transparent 70%); pointer-events: none;"></div>
  <div style="max-width: 760px; margin: 0 auto; position: relative; z-index: 1;">
    <div style="background: linear-gradient(135deg, rgba(255,255,255,0.04) 0%, rgba(255,255,255,0.01) 100%); border: 1px solid rgba(37,211,102,0.25); border-radius: 28px; padding: 56px 48px; text-align: center; box-shadow: 0 0 60px rgba(37,211,102,0.07);">
      <!-- Badge -->
      <div style="display: inline-flex; align-items: center; gap: 8px; background: rgba(37,211,102,0.1); border: 1px solid rgba(37,211,102,0.3); color: #25d366; font-size: 11px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; padding: 8px 18px; border-radius: 100px; margin-bottom: 28px;">
        <span style="width: 7px; height: 7px; background: #25d366; border-radius: 50%; animation: pulse 1.5s infinite;"></span>
        Oferta exclusiva para membros
      </div>

      <!-- Icon -->
      <div style="display: flex; justify-content: center; margin-bottom: 24px;">
        <div style="width: 76px; height: 76px; background: linear-gradient(135deg, #25d366, #128c7e); border-radius: 20px; display: flex; align-items: center; justify-content: center; box-shadow: 0 12px 32px rgba(37,211,102,0.35);">
          <svg width="42" height="42" fill="white" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        </div>
      </div>

      <!-- Title -->
      <h2 style="font-size: clamp(1.6rem, 4vw, 2.4rem); font-weight: 800; color: #fff; margin-bottom: 16px; line-height: 1.25;">
        Entre na comunidade e garanta<br>
        <span style="color: #25d366;">50% de desconto</span> nos 3 primeiros meses
      </h2>

      <!-- Description -->
      <p style="color: rgba(255,255,255,0.5); font-size: 1.05rem; margin-bottom: 32px; max-width: 520px; margin-left: auto; margin-right: auto; line-height: 1.7;">
        Quem entrar agora na nossa comunidade garante acesso antecipado com <strong style="color: rgba(255,255,255,0.85);">metade do preço nos 3 primeiros meses.</strong>
      </p>

      <!-- Perks -->
      <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 10px; margin-bottom: 36px;">
        <span style="display: flex; align-items: center; gap: 6px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08); color: rgba(255,255,255,0.7); font-size: 13px; padding: 8px 16px; border-radius: 100px;">✅ Acesso antecipado</span>
        <span style="display: flex; align-items: center; gap: 6px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08); color: rgba(255,255,255,0.7); font-size: 13px; padding: 8px 16px; border-radius: 100px;">✅ 50% de desconto nos 3 primeiros meses</span>
        <span style="display: flex; align-items: center; gap: 6px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08); color: rgba(255,255,255,0.7); font-size: 13px; padding: 8px 16px; border-radius: 100px;">✅ Conteúdos exclusivos</span>
      </div>

      <!-- CTA Button -->
      <a href="https://chat.whatsapp.com/IOs16RItrZNL2HcPsez7sL" target="_blank" rel="noopener noreferrer"
         style="display: inline-flex; align-items: center; gap: 12px; background: #25d366; color: white; font-weight: 700; font-size: 1.05rem; padding: 18px 40px; border-radius: 16px; text-decoration: none; box-shadow: 0 8px 30px rgba(37,211,102,0.35); transition: all 0.3s ease;"
         onmouseover="this.style.background='#20bd5a'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 12px 40px rgba(37,211,102,0.45)'"
         onmouseout="this.style.background='#25d366'; this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 30px rgba(37,211,102,0.35)'">
        <svg width="24" height="24" fill="white" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        Entrar na comunidade grátis
      </a>

      <p style="color: rgba(255,255,255,0.25); font-size: 13px; margin-top: 18px;">Grátis para entrar. Desconto garantido para quem entrar agora.</p>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <div class="footer-logo">
    <img src="{{ asset('img/favicons/apple-touch-icon.png') }}" alt="ApexPro">
    ApexPro
  </div>
  <div class="footer-text">&copy; {{ date('Y') }} ApexPro. Todos os direitos reservados.</div>
</footer>

<script>
  function toggleFaq(btn) {
    const item = btn.closest('.faq-item');
    const isOpen = item.classList.contains('open');
    document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('open'));
    if (!isOpen) item.classList.add('open');
  }

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry, i) => {
      if (entry.isIntersecting) {
        setTimeout(() => entry.target.classList.add('visible'), i * 80);
      }
    });
  }, { threshold: 0.1 });

  document.querySelectorAll('.fade-up').forEach(el => observer.observe(el));
</script>
</body>
</html>
