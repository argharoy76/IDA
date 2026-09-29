<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, viewport-fit=cover">
  <meta name="theme-color" content="#85c9cc">
  <title>@yield('title', 'Imperial Defence Academy | Khulna')</title>

  <!-- Google Fonts: Roboto -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

  <!-- AOS (Animate On Scroll) Library -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

  <!-- KaTeX for Ultra-Modern, Crisp Equation Typography -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.10/dist/katex.min.css">
  <script src="https://cdn.jsdelivr.net/npm/katex@0.16.10/dist/katex.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/katex@0.16.10/dist/contrib/auto-render.min.js"></script>

  <!-- MathJax for Extended STEM Environments & Fallback -->
  <script>
  MathJax = {
    tex: {
      inlineMath: [['$', '$'], ['\\(', '\\)']],
      displayMath: [['$$', '$$'], ['\\[', '\\]']],
      processEscapes: true
    },
    options: {
      skipHtmlTags: ['script', 'noscript', 'style', 'textarea', 'pre', 'code']
    }
  };
  </script>
  <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js" async></script>
  <script>
  window.typesetMathJax = function(element) {
    const target = element ? (Array.isArray(element) ? element[0] : element) : document.body;
    if (!target) return;

    if (window.renderMathInElement) {
      try {
        renderMathInElement(target, {
          delimiters: [
            { left: '$$', right: '$$', display: true },
            { left: '\\[', right: '\\]', display: true },
            { left: '$', right: '$', display: false },
            { left: '\\(', right: '\\)', display: false }
          ],
          ignoredTags: ['script', 'noscript', 'style', 'textarea', 'pre', 'code'],
          throwOnError: false,
          macros: {
            "\\tr": "\\operatorname{tr}",
            "\\Tr": "\\operatorname{Tr}",
            "\\rank": "\\operatorname{rank}",
            "\\diag": "\\operatorname{diag}",
            "\\adj": "\\operatorname{adj}",
            "\\sgn": "\\operatorname{sgn}"
          }
        });
      } catch(e) {
        console.warn('KaTeX render error:', e);
      }
    }

    if (window.MathJax) {
      const runTypeset = function() {
        if (window.MathJax.typesetClear) {
          try { window.MathJax.typesetClear([target]); } catch(e) {}
        }
        if (window.MathJax.typesetPromise) {
          return window.MathJax.typesetPromise([target]);
        }
        return Promise.resolve();
      };

      if (window.MathJax.startup && window.MathJax.startup.promise) {
        window.MathJax.startup.promise.then(runTypeset).catch(function() {});
      } else if (window.MathJax.typesetPromise) {
        runTypeset().catch(function() {});
      }
    }
  };
  </script>

  <!-- IDA Modern CSS Theme -->
  <link rel="stylesheet" href="{{ asset('css/ida_theme.css') }}?v={{ time() }}">

  <style>
    :root {
      --brand-emerald: {{ cms('color_primary', '#85c9cc') }};
      --brand-primary: {{ cms('color_primary', '#85c9cc') }};
      --brand-deep: {{ cms('color_deep', '#082d2f') }};
      --brand-mint: {{ cms('color_mint', '#7ec2c5') }};
      --accent-gold: {{ cms('color_gold', '#d4af37') }};
      --accent-navy: {{ cms('color_navy', '#082d2f') }};
    }

    /* Enforce Roboto sitewide across frontend while preserving Font Awesome icons */
    body, h1, h2, h3, h4, h5, h6, p, span:not(.fa):not([class*="fa-"]):not([class^="fa-"]), a, label, button, input, select, textarea, div, strong, small, em, li, dt, dd, th, td {
      font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif !important;
    }
    i[class*="fa-"], [class^="fa-"], [class*=" fa-"], .fa, .fas, .far, .fal, .fad, .fab, .fa-solid, .fa-regular, .fa-brands {
      font-family: "Font Awesome 6 Free", "Font Awesome 6 Brands", fontawesome !important;
    }

    /* Ken Burns Cinematic Slow Zoom (from reference design) */
    @keyframes kenBurnsZoom {
      0% { transform: scale(1); }
      50% { transform: scale(1.08); }
      100% { transform: scale(1); }
    }
    .ken-burns {
      animation: kenBurnsZoom 14s infinite alternate ease-in-out;
      transform-origin: center center;
    }

    /* Gold Glow Animation (from reference design) */
    @keyframes goldGlow {
      0%, 100% {
        box-shadow: 0 0 15px rgba(212, 175, 55, 0.25);
        border-color: rgba(212, 175, 55, 0.4);
      }
      50% {
        box-shadow: 0 0 35px rgba(212, 175, 55, 0.6);
        border-color: rgba(212, 175, 55, 0.95);
      }
    }
    .animate-gold-glow {
      animation: goldGlow 3s infinite ease-in-out;
    }

    /* 3D Elevated Public Navbar (Frosted Glass & Depth Elevation) */
    .public-navbar {
      background: rgba(255, 255, 255, 0.94) !important;
      backdrop-filter: blur(16px) saturate(180%) !important;
      -webkit-backdrop-filter: blur(16px) saturate(180%) !important;
      border-bottom: 1px solid rgba(226, 232, 240, 0.85) !important;
      box-shadow: 0 4px 20px -2px rgba(8, 45, 47, 0.05), 0 1px 3px rgba(8, 45, 47, 0.03), inset 0 -1px 0 rgba(133, 201, 204, 0.25) !important;
      position: sticky;
      top: 0;
      z-index: 1000;
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .public-nav-container {
      max-width: 1440px;
      width: 100%;
      margin: 0 auto;
      padding: 0 32px;
      height: 74px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: relative;
      flex-wrap: nowrap;
    }
    .brand-box {
      display: flex;
      align-items: center;
      gap: 13px;
      text-decoration: none;
      flex-shrink: 0;
      margin-right: clamp(24px, 3.5vw, 60px);
      transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .brand-box:hover {
      transform: scale(1.015);
    }
    .brand-crest-3d-wrap {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), filter 0.3s ease;
      filter: drop-shadow(0 6px 14px rgba(133, 201, 204, 0.35)) drop-shadow(0 2px 4px rgba(0, 0, 0, 0.15));
    }
    .brand-box:hover .brand-crest-3d-wrap {
      transform: translateY(-2px) scale(1.06);
      filter: drop-shadow(0 10px 22px rgba(217, 119, 6, 0.35)) drop-shadow(0 4px 10px rgba(133, 201, 204, 0.5));
    }
    .brand-titles {
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
    .brand-titles h1 {
      font-size: 15.5px;
      font-weight: 900;
      color: #082d2f !important;
      margin: 0;
      line-height: 1.2;
      white-space: nowrap;
      letter-spacing: 0.4px;
      text-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
      font-family: 'Roboto', sans-serif;
    }
    .brand-titles span {
      font-size: 9.5px;
      font-weight: 800;
      letter-spacing: 1.3px;
      text-transform: uppercase;
      display: block;
      margin-top: 2.5px;
      white-space: nowrap;
      background: linear-gradient(90deg, #082d2f 0%, #134e4a 40%, #85c9cc 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      text-shadow: none;
    }
    .nav-links {
      display: flex;
      align-items: center;
      gap: 3px;
      flex-wrap: nowrap;
      margin: 0;
      padding: 0;
      margin-left: auto;
    }
    .nav-link-item {
      text-decoration: none !important;
      font-size: 13px;
      font-weight: 600;
      color: #334155 !important;
      padding: 7px 11px;
      border-radius: 20px;
      transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
      white-space: nowrap !important;
      flex-shrink: 0;
      border: 1px solid transparent;
    }
    .nav-link-item:hover {
      color: #082d2f !important;
      background: #eef8f8 !important;
      border-color: rgba(133, 201, 204, 0.45) !important;
      transform: translateY(-1px);
      box-shadow: 0 2px 6px rgba(133, 201, 204, 0.15);
    }
    .nav-link-item.active {
      color: #082d2f !important;
      background: #85c9cc !important;
      font-weight: 700;
      border-color: #72bcc0 !important;
      box-shadow: inset 0 1px 2px rgba(8, 45, 47, 0.1), 0 2px 6px rgba(133, 201, 204, 0.25);
    }
    .nav-auth-group {
      display: flex;
      align-items: center;
      gap: 8px;
      flex-shrink: 0;
      white-space: nowrap;
      margin-left: 8px;
      padding-left: 12px;
      border-left: 1.5px solid rgba(226, 232, 240, 0.9);
    }
    @media (min-width: 992px) and (max-width: 1240px) {
      .public-nav-container {
        padding: 0 16px !important;
      }
      .brand-box {
        margin-right: 12px !important;
        gap: 9px !important;
      }
      .brand-titles h1 {
        font-size: 13.5px !important;
      }
      .brand-titles span {
        font-size: 8px !important;
      }
      .nav-links {
        gap: 2px !important;
      }
      .nav-link-item {
        padding: 6px 7px !important;
        font-size: 11.5px !important;
      }
      .nav-auth-group {
        margin-left: 4px !important;
        padding-left: 8px !important;
      }
    }
    .nav-candidate-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 4px 12px 4px 5px;
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 30px;
      box-shadow: inset 0 1px 0 rgba(255,255,255,0.8), 0 2px 6px rgba(15, 23, 42, 0.05);
    }
    .nav-candidate-avatar {
      width: 28px;
      height: 28px;
      border-radius: 50%;
      background: #85c9cc;
      color: #082d2f;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      font-size: 12px;
      flex-shrink: 0;
      box-shadow: 0 2px 6px rgba(133, 201, 204, 0.4);
    }
    .nav-candidate-name {
      font-size: 13px;
      font-weight: 600;
      color: #1e293b !important;
      max-width: 140px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }
    /* 3D Tactile Action Button */
    .nav-btn-signup {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      padding: 9px 20px !important;
      border-radius: 9px;
      font-size: 13px;
      font-weight: 800;
      color: #082d2f !important;
      text-decoration: none !important;
      background: #85c9cc !important;
      border: 1.5px solid #72bcc0 !important;
      white-space: nowrap;
      box-shadow: 0 4px 14px rgba(133, 201, 204, 0.4) !important;
      transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
      text-shadow: none;
    }
    .nav-btn-signup:hover {
      background: #72bcc0 !important;
      color: #062325 !important;
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(133, 201, 204, 0.5) !important;
    }
    .nav-btn-signup:active {
      transform: translateY(1px);
      box-shadow: 0 2px 4px rgba(133, 201, 204, 0.25) !important;
    }
    .nav-btn-logout {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 6px 12px;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 600;
      color: #ef4444 !important;
      text-decoration: none !important;
      background: #fef2f2;
      border: 1px solid #fecaca;
      white-space: nowrap;
      box-shadow: 0 1px 3px rgba(239, 68, 68, 0.15);
      transition: all 0.2s ease;
    }
    .nav-btn-logout:hover {
      background: #fee2e2;
      color: #dc2626 !important;
      transform: translateY(-1px);
      box-shadow: 0 2px 6px rgba(239, 68, 68, 0.25);
    }
    .mobile-nav-toggle {
      display: none;
      width: 40px;
      height: 40px;
      border-radius: 10px;
      background: #f8fafc;
      border: 1.5px solid #e2e8f0;
      color: #334155;
      font-size: 17px;
      cursor: pointer;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      transition: all 0.2s ease;
    }
    .mobile-nav-toggle:active, .mobile-nav-toggle:focus {
      background: #eef8f8;
      border-color: #85c9cc;
      color: #082d2f;
    }
    .mobile-nav-drawer {
      display: none;
      position: absolute;
      top: 100%;
      left: 0;
      right: 0;
      background: #ffffff;
      border-bottom: 2px solid #85c9cc;
      box-shadow: 0 16px 36px rgba(0, 0, 0, 0.12);
      padding: 16px 20px 24px;
      z-index: 1000;
      transform: translateY(-8px);
      opacity: 0;
      transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
      max-height: 85vh;
      overflow-y: auto;
      -webkit-overflow-scrolling: touch;
    }
    .mobile-nav-drawer.open {
      display: block !important;
      transform: translateY(0);
      opacity: 1;
    }
    .mobile-nav-links {
      display: flex;
      flex-direction: column;
      gap: 5px;
      margin-bottom: 18px;
      padding: 0;
    }
    .mobile-nav-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 16px;
      border-radius: 10px;
      color: #1e293b !important;
      font-size: 14.5px;
      font-weight: 500;
      text-decoration: none !important;
      transition: all 0.2s ease;
      border: 1px solid transparent;
    }
    .mobile-nav-item i {
      width: 22px;
      text-align: center;
      color: #082d2f;
      font-size: 16px;
    }
    .mobile-nav-item:hover {
      background: #eef8f8;
      color: #082d2f !important;
      border-color: rgba(133, 201, 204, 0.35);
    }
    .mobile-nav-item.active {
      background: #85c9cc !important;
      color: #082d2f !important;
      font-weight: 700;
      border-color: #72bcc0;
    }
    .mobile-auth-section {
      border-top: 1px solid #e2e8f0;
      padding-top: 16px;
    }

    /* Public Footer Grid */
    .public-footer-grid {
      display: grid;
      grid-template-columns: 2fr 1fr 1fr 1.4fr;
      gap: 36px;
      margin-bottom: 36px;
    }

    /* Universal Responsive Utilities */
    .grid-cols-4-responsive {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 22px;
    }
    .grid-cols-3-responsive {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 24px;
    }
    .grid-cols-2-responsive {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 30px;
    }
    .hero-grid-responsive {
      display: grid;
      grid-template-columns: 1.25fr 0.85fr;
      gap: 48px;
      align-items: center;
    }

    html, body {
      width: 100% !important;
      max-width: 100% !important;
      overflow-x: hidden !important;
      position: relative !important;
      margin: 0 !important;
      padding: 0 !important;
    }
    *, *::before, *::after {
      box-sizing: border-box !important;
    }

    @media (max-width: 991px) {
      .public-nav-container {
        padding: 0 16px !important;
        justify-content: space-between !important;
        width: 100% !important;
      }
      .brand-box {
        margin-right: 0 !important;
        max-width: calc(100% - 50px) !important;
        min-width: 0 !important;
        flex: 1 1 auto !important;
      }
      .brand-titles {
        min-width: 0 !important;
        flex: 1 1 auto !important;
        overflow: hidden !important;
      }
      .brand-titles h1 {
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
      }
      .brand-titles span {
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
      }
      .nav-links { display: none !important; }
      .mobile-nav-toggle {
        display: inline-flex !important;
        flex-shrink: 0 !important;
        margin-left: auto !important;
      }
      .public-footer-grid {
        grid-template-columns: 1fr 1fr !important;
        gap: 28px !important;
      }
      .grid-cols-4-responsive {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 16px !important;
      }
      .grid-cols-3-responsive {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 18px !important;
      }
      .hero-grid-responsive {
        grid-template-columns: 1fr !important;
        gap: 32px !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
      }
    }
    @media (min-width: 992px) {
      .nav-links { display: flex !important; }
      .mobile-nav-toggle { display: none !important; }
      .mobile-nav-drawer { display: none !important; }
    }
    @media (max-width: 768px) {
      .announcement-hide-mobile {
        display: none !important;
      }
      .top-announcement-bar {
        padding: 6px 12px !important;
        font-size: 11px !important;
      }
      .top-announcement-inner {
        gap: 6px !important;
      }
      .public-nav-container {
        padding: 0 12px !important;
        height: 58px !important;
        gap: 8px !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        overflow: hidden !important;
      }
      .brand-box {
        margin-right: 0 !important;
        gap: 8px !important;
        flex: 1 1 auto !important;
        max-width: calc(100% - 48px) !important;
        min-width: 0 !important;
        overflow: hidden !important;
      }
      .brand-titles {
        flex: 1 1 auto !important;
        min-width: 0 !important;
        overflow: hidden !important;
      }
      .brand-titles h1 {
        font-size: clamp(10.5px, 3.2vw, 13px) !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        max-width: 100% !important;
      }
      .brand-titles span {
        font-size: clamp(6.5px, 2vw, 8px) !important;
        letter-spacing: 0.2px !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        max-width: 100% !important;
      }
      .mobile-nav-toggle {
        display: inline-flex !important;
        width: 38px !important;
        height: 38px !important;
        font-size: 16px !important;
        flex-shrink: 0 !important;
        margin-left: auto !important;
      }
      .grid-cols-4-responsive {
        grid-template-columns: 1fr !important;
        gap: 16px !important;
      }
      .grid-cols-3-responsive {
        grid-template-columns: 1fr !important;
        gap: 16px !important;
      }
      .grid-cols-2-responsive {
        grid-template-columns: 1fr !important;
        gap: 16px !important;
      }
      .hero-section {
        padding: 36px 14px 44px !important;
      }
      .hero-headline {
        font-size: clamp(21px, 6.2vw, 34px) !important;
        line-height: 1.25 !important;
        word-break: break-word !important;
        overflow-wrap: break-word !important;
      }
      .hero-headline br {
        display: none !important;
      }
      .hero-headline #typed-text {
        display: block !important;
        margin-top: 4px !important;
      }
      .hero-subheadline {
        font-size: clamp(15px, 4.2vw, 22px) !important;
        margin-top: 6px !important;
      }
      .hero-actions-wrap {
        flex-direction: column !important;
        width: 100% !important;
        gap: 10px !important;
      }
      .hero-actions-wrap > a {
        width: 100% !important;
        text-align: center !important;
        justify-content: center !important;
        padding: 12px 16px !important;
        font-size: 13.5px !important;
      }
      .hero-trust-pills {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 8px !important;
      }
      .hero-showcase-card {
        padding: 20px 14px !important;
      }
      .hero-stats-grid {
        gap: 8px !important;
        padding-top: 14px !important;
      }
      .hero-stats-grid strong {
        font-size: 20px !important;
      }
      .hero-beacon {
        font-size: 11px !important;
        padding: 6px 10px !important;
        text-align: center !important;
      }
      .section-responsive-pad {
        padding: 40px 14px !important;
        margin-top: 30px !important;
        margin-bottom: 30px !important;
      }
      .cta-responsive-pad {
        padding: 36px 16px !important;
        margin: 20px 12px !important;
      }
      .cta-btn-wrap {
        flex-direction: column !important;
        width: 100% !important;
        gap: 10px !important;
      }
      .cta-btn-wrap > a {
        width: 100% !important;
        text-align: center !important;
        justify-content: center !important;
      }
    }
    @media (max-width: 640px) {
      .public-footer-grid {
        grid-template-columns: 1fr !important;
        gap: 24px !important;
      }
      .grid-cols-4-responsive {
        grid-template-columns: 1fr !important;
        gap: 14px !important;
      }
      .grid-cols-3-responsive {
        grid-template-columns: 1fr !important;
        gap: 16px !important;
      }
    }
  </style>
  @yield('styles')
</head>
<body>

  @if(cms('show_top_announcement_bar', false))
  <!-- Optional Top Announcement Bar (Default Disabled for Clean Header) -->
  <div class="rolex-animate-down delay-0 top-announcement-bar" style="background: linear-gradient(90deg, #050b14 0%, #0a172c 50%, #050b14 100%); color: #cbd5e1; font-size: 12px; font-weight: 500; padding: 8px 24px; border-bottom: 1px solid rgba(255, 255, 255, 0.09); box-shadow: 0 2px 6px rgba(0,0,0,0.35);">
    <div class="top-announcement-inner" style="max-width: 1440px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
      <div style="display: flex; align-items: center; gap: 10px;">
        <span style="display: inline-flex; align-items: center; gap: 6px; background: linear-gradient(180deg, #fef08a 0%, #f59e0b 60%, #b45309 100%); color: #020617; border: 1px solid #78350f; padding: 3px 10px; border-radius: 6px; font-size: 10px; font-weight: 800; letter-spacing: 0.8px; box-shadow: inset 0 1px 0 rgba(255,255,255,0.6), 0 2px 5px rgba(217,119,6,0.35); text-shadow: none;">
          <span style="width: 6px; height: 6px; border-radius: 50%; background: #020617; animation: pulseGlow 1.8s infinite;"></span>
          {{ cms('announcement_badge', 'ADMISSIONS OPEN') }}
        </span>
        <span style="color: #f1f5f9; font-weight: 600; letter-spacing: 0.2px;">{{ cms('announcement_text', cms('announcement_bar_text', 'BMA 95th Long Course & Navy 2026-B Batches Enrolling Now')) }}</span>
      </div>
      <div style="display: flex; gap: 16px; align-items: center; font-size: 12px;">
        <span style="color: #94a3b8; display: inline-flex; align-items: center;" class="announcement-hide-mobile"><i class="fa-solid fa-location-dot" style="color: #f59e0b; margin-right: 6px;"></i> {{ cms('academy_location', 'Boyra Main Road, Khulna') }}</span>
        <span style="color: #94a3b8; display: inline-flex; align-items: center;" class="announcement-hide-mobile"><i class="fa-solid fa-phone" style="color: #85c9cc; margin-right: 6px;"></i> {{ explode(',', cms('academy_phone', '+880 1712-345678'))[0] }}</span>
      </div>
    </div>
  </div>
  @endif

  <!-- Main Navbar (Clean 3D Institutional Header) -->
  <header class="public-navbar">
    <div class="public-nav-container">
      <a href="{{ route('home') }}" class="brand-box" title="{{ cms('site_name', 'IMPERIAL DEFENCE ACADEMY') }}">
        @include('frontend.partials.logo_crest', ['size' => 48])
        <div class="brand-titles">
          @if(filled(cms('site_name', 'IMPERIAL DEFENCE ACADEMY')))
          <h1>{{ strtoupper(cms('site_name', 'IMPERIAL DEFENCE ACADEMY')) }}</h1>
          @endif
          @if(filled(cms('site_tagline', 'COURAGE • CHARACTER • COMMISSION')))
          <span>{{ cms('site_tagline', 'COURAGE • CHARACTER • COMMISSION') }}</span>
          @endif
        </div>
      </a>

      <!-- Desktop Navigation Links -->
      <nav class="nav-links">
        <a href="{{ route('home') }}" class="nav-link-item {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
        <a href="{{ route('about') }}" class="nav-link-item {{ request()->routeIs('about') ? 'active' : '' }}">About IDA</a>
        <a href="{{ route('courses') }}" class="nav-link-item {{ request()->routeIs('courses*') ? 'active' : '' }}">Courses</a>
        <a href="{{ route('classes') }}" class="nav-link-item {{ request()->routeIs('classes') ? 'active' : '' }}">Classes</a>
        <a href="{{ route('online_tests') }}" class="nav-link-item {{ request()->routeIs('online_tests') ? 'active' : '' }}">Online Tests</a>
        <a href="{{ auth()->check() ? (auth()->user()->role === 'external_student' ? route('external.dashboard') : route('cadet.dashboard')) : route('login') }}" class="nav-link-item {{ request()->routeIs('cadet.*') || request()->routeIs('external.*') || request()->routeIs('login') ? 'active' : '' }}">Cadet Portal</a>
        <a href="{{ route('gallery') }}" class="nav-link-item {{ request()->routeIs('gallery') ? 'active' : '' }}">Gallery</a>
        <a href="{{ route('notices') }}" class="nav-link-item {{ request()->routeIs('notices') ? 'active' : '' }}">Notices</a>
        <a href="{{ route('contact') }}" class="nav-link-item {{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a>

        <div class="nav-auth-group">
          <a href="{{ route('register') }}" class="nav-btn-signup" style="padding: 8px 20px; font-weight: 700;">
            <i class="fa-solid fa-user-plus"></i> Join Us
          </a>
        </div>
      </nav>

      <!-- Mobile Hamburger Toggle Button -->
      <button id="mobile-nav-toggle" class="mobile-nav-toggle" aria-label="Toggle Navigation Menu">
        <i class="fa-solid fa-bars" id="mobile-nav-icon"></i>
      </button>
    </div>

    <!-- Mobile Navigation Drawer -->
    <div id="mobile-nav-drawer" class="mobile-nav-drawer">
      <nav class="mobile-nav-links">
        <a href="{{ route('home') }}" class="mobile-nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
          <i class="fa-solid fa-house"></i> Home
        </a>
        <a href="{{ route('about') }}" class="mobile-nav-item {{ request()->routeIs('about') ? 'active' : '' }}">
          <i class="fa-solid fa-shield-halved"></i> About IDA
        </a>
        <a href="{{ route('courses') }}" class="mobile-nav-item {{ request()->routeIs('courses*') ? 'active' : '' }}">
          <i class="fa-solid fa-graduation-cap"></i> Courses
        </a>
        <a href="{{ route('classes') }}" class="mobile-nav-item {{ request()->routeIs('classes') ? 'active' : '' }}">
          <i class="fa-solid fa-calendar-days"></i> Classes
        </a>
        <a href="{{ route('online_tests') }}" class="mobile-nav-item {{ request()->routeIs('online_tests') ? 'active' : '' }}">
          <i class="fa-solid fa-laptop-code"></i> Online Tests
        </a>
        <a href="{{ auth()->check() ? (auth()->user()->role === 'external_student' ? route('external.dashboard') : route('cadet.dashboard')) : route('login') }}" class="mobile-nav-item {{ request()->routeIs('cadet.*') || request()->routeIs('external.*') || request()->routeIs('login') ? 'active' : '' }}">
          <i class="fa-solid fa-user-graduate"></i> Cadet Portal
        </a>
        <a href="{{ route('gallery') }}" class="mobile-nav-item {{ request()->routeIs('gallery') ? 'active' : '' }}">
          <i class="fa-solid fa-images"></i> Gallery
        </a>
        <a href="{{ route('notices') }}" class="mobile-nav-item {{ request()->routeIs('notices') ? 'active' : '' }}">
          <i class="fa-solid fa-bullhorn"></i> Notices
        </a>
        <a href="{{ route('contact') }}" class="mobile-nav-item {{ request()->routeIs('contact') ? 'active' : '' }}">
          <i class="fa-solid fa-envelope"></i> Contact
        </a>
      </nav>

      <div class="mobile-auth-section">
        <a href="{{ route('register') }}" class="nav-btn-signup" style="display: flex; justify-content: center; padding: 11px 16px; width: 100%; font-weight: 700;">
          <i class="fa-solid fa-user-plus"></i> Join Us
        </a>
      </div>
    </div>
  </header>

  <!-- Flash Messages -->
  <div style="max-width: 1240px; margin: 16px auto 0; padding: 0 24px;">
    @if(session('success'))
      <div class="alert-box alert-success">
        <i class="fa-solid fa-circle-check" style="font-size: 16px;"></i>
        <span>{{ session('success') }}</span>
      </div>
    @endif
    @if(session('error'))
      <div class="alert-box alert-error">
        <i class="fa-solid fa-triangle-exclamation" style="font-size: 16px;"></i>
        <span>{{ session('error') }}</span>
      </div>
    @endif
  </div>

  <main>
    @yield('content')
  </main>

  <!-- Clean Institutional Footer -->
  <footer style="background: #090e1a; color: #94a3b8; padding: 65px 24px 30px; margin-top: 70px; border-top: 1px solid rgba(133, 201, 204, 0.25);">
    <div style="max-width: 1240px; margin: 0 auto;" class="public-footer-grid">
      <div>
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
          @include('frontend.partials.logo_crest', ['size' => 40])
          @if(filled(cms('site_name', 'IMPERIAL DEFENCE ACADEMY')))
          <h3 style="color: #ffffff; font-size: 17px; margin: 0; font-family: 'Roboto', sans-serif; font-weight: 800; letter-spacing: 0.3px;">{{ cms('site_name', 'IMPERIAL DEFENCE ACADEMY') }}</h3>
          @endif
        </div>
        @if(filled(cms('footer_bio', "Khulna's premier defense preparatory academy, providing structured grooming for Bangladesh Army (BMA Long Course), Navy, Air Force (BAFA), and complete 4-day simulated ISSB screening mentored by experienced defense officers.")))
        <p style="font-size: 13px; line-height: 1.7; margin-bottom: 20px; color: #94a3b8;">
          {{ cms('footer_bio', "Khulna's premier defense preparatory academy, providing structured grooming for Bangladesh Army (BMA Long Course), Navy, Air Force (BAFA), and complete 4-day simulated ISSB screening mentored by experienced defense officers.") }}
        </p>
        @endif
        @php
          $tag1 = cms('footer_tag1', 'Discipline');
          $tag2 = cms('footer_tag2', 'Leadership');
          $tag3 = cms('footer_tag3', 'Character');
        @endphp
        @if(filled($tag1) || filled($tag2) || filled($tag3))
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
          @if(filled($tag1))
            <span class="badge badge-emerald" style="background: rgba(133, 201, 204, 0.18); color: #85c9cc; border-color: rgba(133, 201, 204, 0.4);">{{ $tag1 }}</span>
          @endif
          @if(filled($tag2))
            <span class="badge badge-blue" style="background: rgba(59, 130, 246, 0.15); color: #93c5fd; border-color: rgba(59, 130, 246, 0.3);">{{ $tag2 }}</span>
          @endif
          @if(filled($tag3))
            <span class="badge badge-amber" style="background: rgba(245, 158, 11, 0.15); color: #fcd34d; border-color: rgba(245, 158, 11, 0.3);">{{ $tag3 }}</span>
          @endif
        </div>
        @endif
        @if(cms('social_facebook') || cms('social_youtube') || cms('social_whatsapp') || cms('social_linkedin'))
          <div style="display: flex; gap: 10px; margin-top: 18px; align-items: center;">
            @if(cms('social_facebook'))
              <a href="{{ cms('social_facebook') }}" target="_blank" rel="noopener" style="width: 34px; height: 34px; border-radius: 8px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); display: inline-flex; align-items: center; justify-content: center; color: #93c5fd; font-size: 14px; transition: all 0.2s;" title="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
            @endif
            @if(cms('social_youtube'))
              <a href="{{ cms('social_youtube') }}" target="_blank" rel="noopener" style="width: 34px; height: 34px; border-radius: 8px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); display: inline-flex; align-items: center; justify-content: center; color: #f87171; font-size: 14px; transition: all 0.2s;" title="YouTube"><i class="fa-brands fa-youtube"></i></a>
            @endif
            @if(cms('social_whatsapp'))
              <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', cms('social_whatsapp')) }}" target="_blank" rel="noopener" style="width: 34px; height: 34px; border-radius: 8px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); display: inline-flex; align-items: center; justify-content: center; color: #85c9cc; font-size: 14px; transition: all 0.2s;" title="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
            @endif
            @if(cms('social_linkedin'))
              <a href="{{ cms('social_linkedin') }}" target="_blank" rel="noopener" style="width: 34px; height: 34px; border-radius: 8px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); display: inline-flex; align-items: center; justify-content: center; color: #38bdf8; font-size: 14px; transition: all 0.2s;" title="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
            @endif
          </div>
        @endif
      </div>

      <div>
        <h4 style="color: #ffffff; font-size: 13px; font-weight: 700; margin-bottom: 14px; text-transform: uppercase; letter-spacing: 0.8px;">Quick Links</h4>
        <ul style="list-style: none; display: flex; flex-direction: column; gap: 9px; font-size: 13px;">
          <li><a href="{{ route('courses') }}" style="color: #cbd5e1;">Course Catalog</a></li>
          <li><a href="{{ route('classes') }}" style="color: #cbd5e1;">Class Routines</a></li>
          <li><a href="{{ route('online_tests') }}" style="color: #cbd5e1;">Online Assessments</a></li>
          <li><a href="{{ route('notices') }}" style="color: #cbd5e1;">Notice Circulars</a></li>
          <li><a href="{{ route('gallery') }}" style="color: #cbd5e1;">Training Gallery</a></li>
          <li><a href="{{ route('about') }}" style="color: #cbd5e1;">About IDA</a></li>
          <li><a href="{{ auth()->check() ? (auth()->user()->role === 'external_student' ? route('external.dashboard') : route('cadet.dashboard')) : route('login') }}" style="color: #85c9cc; font-weight: 600;"><i class="fa-solid fa-user-graduate"></i> Cadet Portal</a></li>
        </ul>
      </div>

      <div>
        <h4 style="color: #ffffff; font-size: 13px; font-weight: 700; margin-bottom: 14px; text-transform: uppercase; letter-spacing: 0.8px;">Courses</h4>
        <ul style="list-style: none; display: flex; flex-direction: column; gap: 9px; font-size: 13px;">
          <li><a href="{{ route('courses') }}" style="color: #cbd5e1;">BMA Long Course</a></li>
          <li><a href="{{ route('courses') }}" style="color: #cbd5e1;">Navy Officer Cadet</a></li>
          <li><a href="{{ route('courses') }}" style="color: #cbd5e1;">BAFA Flight Wing</a></li>
          <li><a href="{{ route('courses') }}" style="color: #cbd5e1;">ISSB Simulation</a></li>
          <li><a href="{{ route('courses') }}" style="color: #cbd5e1;">Verbal & Non-Verbal IQ</a></li>
        </ul>
      </div>

      @php
        $campusHeading = cms('footer_campus_heading', 'KHULNA CAMPUS');
        $campusLocation = cms('academy_location', 'Boyra Main Road (Near Medical College), Khulna - 9000');
        $campusPhone = cms('academy_phone', '+880 1712-345678, +880 1911-987654');
        $campusEmail = cms('academy_email', 'info@ida.com.bd, admissions@ida.com.bd');
        $campusHours = cms('office_hours', 'Saturday - Thursday: 08:00 AM - 08:00 PM (Friday: 03:00 PM - 08:00 PM)');
        $hasCampus = filled($campusHeading) || filled($campusLocation) || filled($campusPhone) || filled($campusEmail) || filled($campusHours);
      @endphp
      @if($hasCampus)
      <div>
        @if(filled($campusHeading))
        <h4 style="color: #ffffff; font-size: 13px; font-weight: 700; margin-bottom: 14px; text-transform: uppercase; letter-spacing: 0.8px;">{{ $campusHeading }}</h4>
        @endif
        @if(filled($campusLocation))
        <p style="font-size: 13px; margin-bottom: 8px; color: #cbd5e1; display: flex; align-items: flex-start; gap: 10px;">
          <i class="fa-solid fa-location-dot" style="color: #85c9cc; width: 16px; margin-top: 3px; flex-shrink: 0;"></i>
          <span>{{ $campusLocation }}</span>
        </p>
        @endif
        @if(filled($campusPhone))
        <p style="font-size: 13px; margin-bottom: 8px; color: #cbd5e1; display: flex; align-items: flex-start; gap: 10px;">
          <i class="fa-solid fa-phone" style="color: #85c9cc; width: 16px; margin-top: 3px; flex-shrink: 0;"></i>
          <span>{{ $campusPhone }}</span>
        </p>
        @endif
        @if(filled($campusEmail))
        <p style="font-size: 13px; margin-bottom: 8px; color: #cbd5e1; display: flex; align-items: flex-start; gap: 10px;">
          <i class="fa-solid fa-envelope" style="color: #85c9cc; width: 16px; margin-top: 3px; flex-shrink: 0;"></i>
          <span>{{ $campusEmail }}</span>
        </p>
        @endif
        @if(filled($campusHours))
        <p style="font-size: 13px; color: #cbd5e1; display: flex; align-items: flex-start; gap: 10px;">
          <i class="fa-regular fa-clock" style="color: #85c9cc; width: 16px; margin-top: 3px; flex-shrink: 0;"></i>
          <span>{{ $campusHours }}</span>
        </p>
        @endif
      </div>
      @endif
    </div>

    @if(filled(cms('footer_copyright', 'Imperial Defence Academy (IDA), Khulna. All rights reserved. Precision • Character • Commission.')))
    <div style="border-top: 1px solid rgba(255,255,255,0.06); padding-top: 20px; text-align: center; font-size: 12px; color: #64748b;">
      &copy; {{ date('Y') }} {{ cms('footer_copyright', 'Imperial Defence Academy (IDA), Khulna. All rights reserved. Precision • Character • Commission.') }}
    </div>
    @endif
  </footer>

  <script src="{{ asset('js/app.js') }}"></script>
  
  <!-- AOS (Animate On Scroll) Library -->
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

  <!-- Dynamic Animations Script from Reference -->
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      // 1. Initialize AOS (Disabled on screens <= 991px to eliminate horizontal transform overflow on mobile)
      if (typeof AOS !== 'undefined') {
        AOS.init({
          duration: 900,
          once: true,
          mirror: false,
          offset: 40,
          disable: function() {
            return window.innerWidth < 992;
          }
        });
      }

      // 2. Animated Word Rotator in Hero Headline
      const textElement = document.getElementById('typed-text');
      if (textElement) {
        @php
          $rotatingWords = array_values(array_filter(array_map('trim', explode(',', cms('hero_rotating_words', 'Bangladesh Army, Bangladesh Navy, Bangladesh Air Force, ISSB Screening Board')))));
        @endphp
        const words = {!! json_encode($rotatingWords ?: ['Bangladesh Army', 'Bangladesh Navy', 'Bangladesh Air Force', 'ISSB Screening Board']) !!};
        let wordIndex = 0;
        if (words.length > 1) {
          setInterval(() => {
            wordIndex = (wordIndex + 1) % words.length;
            textElement.style.opacity = '0';
            textElement.style.transform = 'translateY(10px)';
            setTimeout(() => {
              textElement.textContent = words[wordIndex];
              textElement.style.opacity = '1';
              textElement.style.transform = 'translateY(0px)';
            }, 300);
          }, 3200);
        }
      }

      // 3. Live Statistics Count-Up Animation
      let counted = false;
      const statsSection = document.getElementById('stats-section');
      const counters = document.querySelectorAll('.counter-number');

      function animateCounters() {
        counters.forEach(counter => {
          const target = parseFloat(counter.getAttribute('data-target'));
          const decimals = parseInt(counter.getAttribute('data-decimals')) || 0;
          const duration = 2000;
          const startTime = performance.now();

          function updateCount(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const easeProgress = 1 - Math.pow(1 - progress, 3);
            const currentVal = easeProgress * target;

            counter.textContent = currentVal.toFixed(decimals);

            if (progress < 1) {
              requestAnimationFrame(updateCount);
            } else {
              counter.textContent = target.toFixed(decimals);
            }
          }

          requestAnimationFrame(updateCount);
        });
      }

      if (statsSection) {
        const observer = new IntersectionObserver((entries) => {
          entries.forEach(entry => {
            if (entry.isIntersecting && !counted) {
              animateCounters();
              counted = true;
            }
          });
        }, { threshold: 0.3 });
        observer.observe(statsSection);
      }

      // 4. Hero Background Crossfade Slider (from Reference Website)
      const heroSlides = document.querySelectorAll('.hero-slide');
      const heroDots = document.querySelectorAll('.hero-slider-dot');
      let currentHeroSlide = 0;
      const heroInterval = 4500;

      function updateHeroSlide(index) {
        if (!heroSlides.length) return;
        heroSlides.forEach((slide, i) => {
          slide.style.opacity = (i === index) ? '1' : '0';
        });
        heroDots.forEach((dot, i) => {
          if (i === index) {
            dot.style.width = '32px';
            dot.style.background = 'var(--accent-gold, #d4af37)';
          } else {
            dot.style.width = '8px';
            dot.style.background = 'rgba(255, 255, 255, 0.3)';
          }
        });
        currentHeroSlide = index;
      }

      window.changeHeroSlide = function(idx) {
        updateHeroSlide(idx);
      };

      if (heroSlides.length > 1) {
        setInterval(() => {
          let next = (currentHeroSlide + 1) % heroSlides.length;
          updateHeroSlide(next);
        }, heroInterval);
      }

      // 5. Mobile Navigation Drawer Toggle
      const mobileNavToggle = document.getElementById('mobile-nav-toggle');
      const mobileNavDrawer = document.getElementById('mobile-nav-drawer');
      const mobileNavIcon = document.getElementById('mobile-nav-icon');

      if (mobileNavToggle && mobileNavDrawer) {
        mobileNavToggle.addEventListener('click', function(e) {
          e.stopPropagation();
          const isOpen = mobileNavDrawer.classList.toggle('open');
          if (mobileNavIcon) {
            mobileNavIcon.className = isOpen ? 'fa-solid fa-xmark' : 'fa-solid fa-bars';
          }
        });

        // Close when clicking outside drawer
        document.addEventListener('click', function(e) {
          if (!mobileNavDrawer.contains(e.target) && !mobileNavToggle.contains(e.target)) {
            mobileNavDrawer.classList.remove('open');
            if (mobileNavIcon) {
              mobileNavIcon.className = 'fa-solid fa-bars';
            }
          }
        });

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
          if (e.key === 'Escape' && mobileNavDrawer.classList.contains('open')) {
            mobileNavDrawer.classList.remove('open');
            if (mobileNavIcon) {
              mobileNavIcon.className = 'fa-solid fa-bars';
            }
          }
        });
      }
    });
  </script>

  @yield('scripts')
</body>
</html>