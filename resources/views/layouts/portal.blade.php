<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'IDA Portal') | Imperial Defence Academy</title>

  <!-- Google Fonts: Poppins (Primary Portal Font) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700&family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

    // 1. First run KaTeX modern high-contrast renderer
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

    // 2. MathJax fallback for environments not handled by KaTeX
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

  <!-- IDA Dark Command Theme -->
  <link rel="stylesheet" href="{{ asset('css/ida_theme.css') }}">
  <style>
    /* Viewport & Overflow Shield */
    html, body.portal-body {
      width: 100% !important;
      max-width: 100% !important;
      overflow-x: hidden !important;
      position: relative !important;
      margin: 0;
      padding: 0;
    }

    /* Dark Theme High-Contrast Variable Overrides for Portal */
    body.portal-body {
      --brand-deep: #ffffff !important;
      --surface-subtle: #161a26 !important;
      --border-soft: rgba(255, 255, 255, 0.08) !important;
      --text-main: #ffffff !important;
      --text-body: #cbd5e1 !important;
      --text-muted: #94a3b8 !important;
    }

    /* Dark Theme Button Overrides for Portal */
    .portal-body .btn-tactical-outline {
      background: rgba(255, 255, 255, 0.05) !important;
      color: #cbd5e1 !important;
      border: 1px solid rgba(255, 255, 255, 0.12) !important;
      transition: all 0.2s ease !important;
    }
    .portal-body .btn-tactical-outline:hover {
      background: rgba(255, 255, 255, 0.1) !important;
      color: #ffffff !important;
      border-color: rgba(255, 255, 255, 0.25) !important;
      transform: translateY(-1px);
    }

    /* Global Poppins Font Enforcement for Backend */
    body.portal-body,
    .portal-layout,
    .portal-layout *:not(i):not([class*="fa-"]):not([class^="fa-"]):not([class*=" fa-"]):not(.fa):not(.fas):not(.far):not(.fal):not(.fad):not(.fab):not(.fa-solid):not(.fa-regular):not(.fa-brands) {
      font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
      letter-spacing: -0.01em;
    }

    /* Preserve Font Awesome Icons */
    i[class*="fa-"], [class^="fa-"], [class*=" fa-"], .fa, .fas, .far, .fal, .fad, .fab, .fa-solid, .fa-regular, .fa-brands {
      font-family: "Font Awesome 6 Free", "Font Awesome 6 Brands", fontawesome !important;
    }

    /* Ultra-clean Sleek Scrollbar */
    ::-webkit-scrollbar {
      width: 6px;
      height: 6px;
    }
    ::-webkit-scrollbar-track {
      background: #0d1017;
    }
    ::-webkit-scrollbar-thumb {
      background: rgba(255, 255, 255, 0.14);
      border-radius: 4px;
    }
    ::-webkit-scrollbar-thumb:hover {
      background: rgba(255, 255, 255, 0.28);
    }

    /* Desktop Fixed Independent-Scrolling Sidebar */
    @media (min-width: 992px) {
      .portal-layout {
        display: flex !important;
        min-height: 100vh !important;
        position: relative !important;
      }
      .portal-sidebar {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        bottom: 0 !important;
        width: 260px !important;
        height: 100vh !important;
        z-index: 1000 !important;
        display: flex !important;
        flex-direction: column !important;
        overflow: hidden !important;
        background: #11141d !important;
        border-right: 1px solid rgba(255, 255, 255, 0.06) !important;
        box-shadow: 4px 0 24px rgba(0, 0, 0, 0.25) !important;
      }
      .portal-sidebar .sidebar-menu {
        flex: 1 1 auto !important;
        min-height: 0 !important;
        overflow-y: auto !important;
        overscroll-behavior: contain !important;
        scrollbar-width: thin !important;
        scrollbar-color: rgba(255, 255, 255, 0.14) transparent !important;
      }
      .portal-main {
        margin-left: 260px !important;
        width: calc(100% - 260px) !important;
        min-width: 0 !important;
        min-height: 100vh !important;
        box-sizing: border-box !important;
      }
    }

    /* Sidebar Refinement */
    .portal-sidebar {
      background: #11141d !important;
      border-right: 1px solid rgba(255, 255, 255, 0.06) !important;
    }
    .portal-brand-title {
      font-family: 'Poppins', sans-serif !important;
      font-size: 19px !important;
      font-weight: 800 !important;
      letter-spacing: 0.5px !important;
      color: #ff5757 !important;
    }
    .portal-brand-subtitle {
      font-family: 'Poppins', sans-serif !important;
      font-size: 10px !important;
      font-weight: 600 !important;
      letter-spacing: 1.4px !important;
      color: #8c96a8 !important;
    }
    .sidebar-item {
      font-family: 'Poppins', sans-serif !important;
      font-size: 13px !important;
      font-weight: 500 !important;
      border-radius: 10px !important;
      padding: 9px 14px !important;
      color: #8c96a8 !important;
      transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }
    .sidebar-item:hover {
      background: rgba(255, 255, 255, 0.05) !important;
      color: #ffffff !important;
    }
    .sidebar-item:hover i {
      color: #ff5757 !important;
    }
    .sidebar-item.active {
      background: rgba(255, 87, 87, 0.12) !important;
      color: #ffffff !important;
      font-weight: 600 !important;
      box-shadow: 0 2px 12px rgba(255, 87, 87, 0.12) !important;
      border-left: 3px solid #ff5757 !important;
      padding-left: 11px !important;
    }
    .sidebar-item.active i {
      color: #ff5757 !important;
    }

    .sidebar-subitem {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 7px 12px;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 500;
      color: #8c96a8;
      text-decoration: none;
      transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .sidebar-subitem i {
      font-size: 12px;
      width: 16px;
      text-align: center;
      color: #717d96;
      transition: color 0.2s ease;
    }
    .sidebar-subitem:hover {
      background: rgba(255, 255, 255, 0.05);
      color: #ffffff;
    }
    .sidebar-subitem:hover i {
      color: #ff5757;
    }
    .sidebar-subitem.active {
      background: rgba(255, 87, 87, 0.14);
      color: #ff7575;
      font-weight: 600;
    }
    .sidebar-subitem.active i {
      color: #ff5757;
    }

    .sidebar-user-card {
      background: #161924 !important;
      border: 1px solid rgba(255, 255, 255, 0.07) !important;
      border-radius: 14px !important;
    }

    /* Topbar Modernization */
    .portal-topbar {
      background: #11141d !important;
      border: 1px solid rgba(255, 255, 255, 0.06) !important;
      border-radius: 14px !important;
      padding: 14px 24px !important;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25) !important;
      margin-bottom: 24px !important;
    }
    .tactical-clock {
      background: #161a26 !important;
      border: 1px solid rgba(255, 255, 255, 0.08) !important;
      border-radius: 8px !important;
      padding: 7px 14px !important;
      font-size: 12.5px !important;
      font-weight: 600 !important;
      color: #cbd5e1 !important;
    }

    /* Cards, Panels & Metric Overrides */
    .tactical-card, .content-panel {
      background: #141722 !important;
      border: 1px solid rgba(255, 255, 255, 0.07) !important;
      border-radius: 16px !important;
      box-shadow: 0 4px 24px rgba(0, 0, 0, 0.3) !important;
    }
    .dark-stats-hero {
      background: #141722 !important;
      border: 1px solid rgba(255, 255, 255, 0.07) !important;
      border-radius: 18px !important;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.35) !important;
    }
    .dark-action-card {
      background: #141722 !important;
      border: 1px solid rgba(255, 255, 255, 0.07) !important;
      border-radius: 16px !important;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25) !important;
    }
    .dark-action-card:hover {
      transform: translateY(-4px) !important;
      border-color: rgba(255, 87, 87, 0.4) !important;
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4), 0 0 20px rgba(255, 87, 87, 0.12) !important;
    }

    /* Tables */
    .tactical-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
    }
    .tactical-table th {
      background: #0f121a !important;
      color: #8c96a8 !important;
      font-size: 11px !important;
      font-weight: 600 !important;
      text-transform: uppercase !important;
      letter-spacing: 0.8px !important;
      padding: 13px 18px !important;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
    }
    .tactical-table td {
      padding: 13px 18px !important;
      font-size: 13px !important;
      color: #cbd5e1 !important;
      border-bottom: 1px solid rgba(255, 255, 255, 0.04) !important;
    }
    .tactical-table tr:hover td {
      background: rgba(255, 255, 255, 0.03) !important;
    }

    /* Forms */
    .form-tactical, .form-control {
      background: #0f121a !important;
      border: 1px solid rgba(255, 255, 255, 0.1) !important;
      border-radius: 10px !important;
      padding: 10px 14px !important;
      color: #ffffff !important;
      font-size: 13px !important;
      transition: border-color 0.2s, box-shadow 0.2s !important;
    }
    .form-tactical:focus, .form-control:focus {
      border-color: #ff5757 !important;
      box-shadow: 0 0 0 3px rgba(255, 87, 87, 0.15) !important;
      outline: none !important;
    }

    /* Tactical Buttons */
    .btn-tactical {
      font-family: 'Poppins', sans-serif !important;
      font-weight: 600 !important;
      font-size: 12.5px !important;
      border-radius: 9px !important;
      padding: 9px 18px !important;
      display: inline-flex !important;
      align-items: center !important;
      gap: 8px !important;
      cursor: pointer !important;
      transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
      text-decoration: none !important;
    }
    .btn-tactical:hover {
      transform: translateY(-1px) !important;
    }
    .badge {
      font-family: 'Poppins', sans-serif !important;
      font-weight: 600 !important;
      border-radius: 6px !important;
      padding: 4px 10px !important;
      letter-spacing: 0.3px !important;
    }

    /* Modern Mathematical Typography & Equations */
    .katex {
      font-size: 1.15em !important;
      color: #f8fafc !important;
      text-rendering: geometricPrecision !important;
    }
    .katex-display {
      display: block !important;
      margin: 14px 0 !important;
      padding: 14px 22px !important;
      background: rgba(255, 255, 255, 0.035) !important;
      border: 1px solid rgba(255, 255, 255, 0.08) !important;
      border-radius: 12px !important;
      text-align: center !important;
      overflow-x: auto !important;
      overflow-y: hidden !important;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25) !important;
    }
    .katex-display > .katex {
      font-size: 1.3em !important;
      color: #ffffff !important;
      letter-spacing: 0.02em !important;
    }
    mjx-container[display="true"] {
      display: block !important;
      margin: 14px 0 !important;
      padding: 14px 22px !important;
      background: rgba(255, 255, 255, 0.035) !important;
      border: 1px solid rgba(255, 255, 255, 0.08) !important;
      border-radius: 12px !important;
      text-align: center !important;
      font-size: 122% !important;
      overflow-x: auto !important;
      overflow-y: hidden !important;
      color: #ffffff !important;
    }
    mjx-container:not([display="true"]) {
      font-size: 110% !important;
      color: #ffffff !important;
    }

    /* Flash Alerts */
    .alert-box {
      border-radius: 12px !important;
      padding: 14px 18px !important;
      margin-bottom: 20px !important;
      font-size: 13px !important;
      font-weight: 500 !important;
      display: flex !important;
      align-items: center !important;
      gap: 12px !important;
    }

    /* Mobile Off-Canvas Navigation Drawer & Header */
    .portal-mobile-header {
      display: none;
    }
    .portal-mobile-close-btn {
      display: none;
    }
    .portal-backdrop {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.75);
      backdrop-filter: blur(4px);
      -webkit-backdrop-filter: blur(4px);
      z-index: 9998;
      opacity: 0;
      transition: opacity 0.25s ease;
    }
    .portal-backdrop.active {
      display: block !important;
      opacity: 1 !important;
    }

    @media (max-width: 991px) {
      .portal-layout {
        flex-direction: column !important;
      }
      .portal-mobile-header {
        display: flex !important;
        align-items: center;
        justify-content: space-between;
        background: #11141d;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        padding: 12px 18px;
        position: sticky;
        top: 0;
        z-index: 1000;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
      }
      .portal-mobile-btn {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        background: #181c26;
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #ff5757;
        font-size: 16px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .portal-mobile-brand {
        text-align: center;
      }
      .portal-mobile-brand-title {
        display: block;
        font-size: 15px;
        font-weight: 800;
        color: #ff5757;
        letter-spacing: 0.5px;
      }
      .portal-mobile-role {
        display: block;
        font-size: 9.5px;
        font-weight: 700;
        color: #8c96a8;
        letter-spacing: 1px;
      }
      .portal-mobile-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: rgba(255, 87, 87, 0.15);
        border: 1px solid rgba(255, 87, 87, 0.4);
        color: #ff5757;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 700;
      }
      .portal-mobile-close-btn {
        display: flex !important;
        width: 32px;
        height: 32px;
        border-radius: 6px;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #cbd5e1;
        font-size: 15px;
        cursor: pointer;
        align-items: center;
        justify-content: center;
      }

      /* Transform Portal Sidebar to Off-Canvas Slide Drawer */
      .portal-sidebar {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        bottom: 0 !important;
        width: 280px !important;
        height: 100vh !important;
        z-index: 9999 !important;
        transform: translateX(-100%);
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
        box-shadow: none;
      }
      .portal-sidebar.open {
        transform: translateX(0) !important;
        box-shadow: 10px 0 40px rgba(0, 0, 0, 0.7) !important;
      }

      .portal-main {
        padding: 16px 14px !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        min-width: 0 !important;
      }

      /* Failsafe responsive data tables on mobile */
      .tactical-table, table.table {
        display: block !important;
        width: 100% !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
      }

      .dark-stats-hero, .tactical-card, .content-panel {
        max-width: 100% !important;
        box-sizing: border-box !important;
      }

      /* Mobile topbar simplification */
      .portal-topbar {
        padding: 12px 16px !important;
        margin-bottom: 16px !important;
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 12px !important;
      }
      .portal-topbar > div:last-child {
        width: 100%;
        justify-content: space-between;
        flex-wrap: wrap;
      }
    }

    /* =========================================================
       CADET PORTAL - LIGHTISH INSTITUTIONAL THEME (MATCHES FRONTEND)
       ========================================================= */
    body.portal-body.cadet-light-portal {
      background: #f8fafc !important;
      color: #334155 !important;
    }
    .cadet-light-portal .portal-layout {
      background: #f8fafc !important;
    }
    .cadet-light-portal .portal-main {
      background: #f8fafc !important;
    }

    /* Cadet Light Sidebar */
    .cadet-light-portal .portal-sidebar {
      background: #ffffff !important;
      border-right: 1px solid #e2e8f0 !important;
      box-shadow: 4px 0 24px rgba(15, 23, 42, 0.04) !important;
    }
    .cadet-light-portal .sidebar-header {
      border-bottom: 1px solid #f1f5f9 !important;
    }
    .cadet-light-portal .portal-brand-title {
      color: #064e3b !important;
    }
    .cadet-light-portal .portal-brand-subtitle {
      color: #64748b !important;
    }
    .cadet-light-portal .sidebar-item {
      position: relative;
      color: #64748b !important;
      font-size: 13.5px !important;
      font-weight: 500 !important;
      border-radius: 10px !important;
      margin: 3px 12px !important;
      padding: 10px 14px !important;
      transition: all 0.2s ease !important;
    }
    .cadet-light-portal .sidebar-item i {
      color: #94a3b8 !important;
      font-size: 16px !important;
      transition: color 0.2s ease;
    }
    .cadet-light-portal .sidebar-item:hover {
      background: #f8fafc !important;
      color: #0f172a !important;
    }
    .cadet-light-portal .sidebar-item:hover i {
      color: #ff5757 !important;
    }
    .cadet-light-portal .sidebar-item.active {
      background: #fef2f2 !important;
      color: #ef4444 !important;
      font-weight: 700 !important;
      border-left: none !important;
      box-shadow: none !important;
    }
    .cadet-light-portal .sidebar-item.active::before {
      content: '';
      position: absolute;
      left: -12px;
      top: 50%;
      transform: translateY(-50%);
      width: 4px;
      height: 28px;
      background: #ff5757;
      border-radius: 0 4px 4px 0;
    }
    .cadet-light-portal .sidebar-item.active i {
      color: #ef4444 !important;
    }
    .cadet-light-portal .sidebar-user-card {
      background: #f8fafc !important;
      border: 1px solid #e2e8f0 !important;
    }
    .cadet-light-portal .sidebar-user-card strong {
      color: #0f172a !important;
    }
    .cadet-light-portal .sidebar-user-card small {
      color: #64748b !important;
    }
    .cadet-light-portal .sidebar-user-card div > div:first-child {
      background: #ecfdf5 !important;
      border-color: #a7f3d0 !important;
      color: #059669 !important;
    }
    .cadet-under-construction-badge {
      margin-left: auto;
      font-size: 8.5px;
      font-weight: 800;
      padding: 2px 7px;
      border-radius: 9999px;
      background: #fef3c7;
      color: #b45309;
      border: 1px solid #fde68a;
      text-transform: uppercase;
      letter-spacing: 0.3px;
      white-space: nowrap;
      line-height: 1.2;
    }
    .cadet-light-portal .sidebar-user-card a {
      color: #64748b !important;
    }
    .cadet-light-portal .sidebar-user-card a:hover {
      color: #ef4444 !important;
    }

    /* Cadet Light Topbar */
    .cadet-light-portal .portal-topbar {
      background: #ffffff !important;
      border: 1px solid #e2e8f0 !important;
      box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04) !important;
    }
    .cadet-light-portal .portal-topbar > div:first-child > div:first-child {
      color: #64748b !important;
    }
    .cadet-light-portal .portal-topbar > div:first-child > div:last-child {
      color: #475569 !important;
    }
    .cadet-light-portal .portal-topbar > div:first-child > div:last-child span {
      color: #0f172a !important;
    }
    .cadet-light-portal .tactical-clock {
      background: #f8fafc !important;
      border: 1px solid #e2e8f0 !important;
      color: #334155 !important;
    }
    .cadet-light-portal .tactical-clock i {
      color: #059669 !important;
    }
    .cadet-light-portal .portal-topbar a[href*="home"] {
      background: #f8fafc !important;
      border: 1px solid #e2e8f0 !important;
      color: #334155 !important;
    }
    .cadet-light-portal .portal-topbar a[href*="home"]:hover {
      background: #f1f5f9 !important;
      color: #0f172a !important;
    }
    .cadet-light-portal .portal-topbar a[href*="home"] i {
      color: #059669 !important;
    }
    .cadet-light-portal .portal-topbar a[href*="logout"] {
      background: #fef2f2 !important;
      border: 1px solid #fecaca !important;
      color: #dc2626 !important;
    }
    .cadet-light-portal .portal-topbar a[href*="logout"] i {
      color: #dc2626 !important;
    }

    /* Cadet Mobile Header & Drawer */
    .cadet-light-portal .portal-mobile-header {
      background: #ffffff !important;
      border-bottom: 1px solid #e2e8f0 !important;
      box-shadow: 0 2px 10px rgba(15, 23, 42, 0.05) !important;
    }
    .cadet-light-portal .portal-mobile-btn {
      background: #f8fafc !important;
      border: 1px solid #e2e8f0 !important;
      color: #059669 !important;
    }
    .cadet-light-portal .portal-mobile-brand-title {
      color: #064e3b !important;
    }
    .cadet-light-portal .portal-mobile-role {
      color: #64748b !important;
    }
    .cadet-light-portal .portal-mobile-avatar {
      background: #ecfdf5 !important;
      border: 1px solid #a7f3d0 !important;
      color: #059669 !important;
    }
    .cadet-light-portal .portal-mobile-close-btn {
      background: #f8fafc !important;
      border: 1px solid #e2e8f0 !important;
      color: #475569 !important;
    }

    /* Cadet Light Cards, Panels & Stats */
    .cadet-light-portal .tactical-card,
    .cadet-light-portal .content-panel {
      background: #ffffff !important;
      border: 1px solid #e2e8f0 !important;
      box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05) !important;
      color: #334155 !important;
    }
    .cadet-light-portal .stat-card {
      background: #ffffff !important;
      border: 1px solid #e2e8f0 !important;
      box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03) !important;
    }
    .cadet-light-portal .stat-card .stat-label {
      color: #64748b !important;
    }
    .cadet-light-portal .stat-card .stat-value {
      color: #0f172a;
    }
    .cadet-light-portal h1,
    .cadet-light-portal h2,
    .cadet-light-portal h3,
    .cadet-light-portal h4 {
      color: #0f172a;
    }
    .cadet-light-portal .cadet-hero-banner h2,
    .cadet-light-portal .cadet-hero-banner h3,
    .cadet-light-portal .cadet-emerald-hero h2,
    .cadet-light-portal .cadet-emerald-hero h3 {
      color: #ffffff !important;
    }

    /* Cadet Light Tables */
    .cadet-light-portal .tactical-table th {
      background: #f8fafc !important;
      color: #475569 !important;
      border-bottom: 1px solid #e2e8f0 !important;
    }
    .cadet-light-portal .tactical-table td {
      background: #ffffff !important;
      color: #334155 !important;
      border-bottom: 1px solid #f1f5f9 !important;
    }
    .cadet-light-portal .tactical-table tr:hover td {
      background: #f8fafc !important;
    }

    /* Cadet Light Buttons & Badges */
    .cadet-light-portal .btn-tactical-primary {
      background: #059669 !important;
      color: #ffffff !important;
      border-color: #059669 !important;
      box-shadow: 0 4px 14px rgba(5, 150, 105, 0.25) !important;
    }
    .cadet-light-portal .btn-tactical-primary:hover {
      background: #047857 !important;
      border-color: #047857 !important;
    }
    .cadet-light-portal .btn-tactical-outline {
      background: #ffffff !important;
      color: #334155 !important;
      border: 1.5px solid #cbd5e1 !important;
    }
    .cadet-light-portal .btn-tactical-outline:hover {
      background: #f8fafc !important;
      border-color: #059669 !important;
      color: #059669 !important;
    }
    .cadet-light-portal .badge-navy {
      background: #f1f5f9 !important;
      color: #1e293b !important;
      border: 1px solid #cbd5e1 !important;
    }
    .cadet-light-portal .badge-emerald {
      background: #ecfdf5 !important;
      color: #047857 !important;
      border: 1px solid #a7f3d0 !important;
    }
    .cadet-light-portal .badge-gold {
      background: #fffbeb !important;
      color: #b45309 !important;
      border: 1px solid #fde68a !important;
    }
    .cadet-light-portal .badge-danger {
      background: #fef2f2 !important;
      color: #dc2626 !important;
      border: 1px solid #fecaca !important;
    }

    /* Cadet Light Forms & Scrollbars */
    .cadet-light-portal .form-tactical,
    .cadet-light-portal .form-control {
      background: #ffffff !important;
      border: 1.5px solid #cbd5e1 !important;
      color: #0f172a !important;
    }
    .cadet-light-portal .form-tactical:focus,
    .cadet-light-portal .form-control:focus {
      border-color: #059669 !important;
      box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.15) !important;
    }
    .cadet-light-portal ::-webkit-scrollbar-track {
      background: #f1f5f9 !important;
    }
    .cadet-light-portal ::-webkit-scrollbar-thumb {
      background: rgba(0, 0, 0, 0.15) !important;
      border-radius: 4px;
    }
    .cadet-light-portal ::-webkit-scrollbar-thumb:hover {
      background: rgba(0, 0, 0, 0.25) !important;
    }
  </style>
  @yield('styles')
</head>
@php
  $isCadetPortal = request()->routeIs('cadet.*') || (auth()->check() && auth()->user()->isAcademicStudent());
@endphp
<body class="portal-body {{ $isCadetPortal ? 'cadet-light-portal' : '' }}">

  <!-- Mobile Dark Overlay Backdrop -->
  <div id="portalBackdrop" class="portal-backdrop"></div>

  <!-- Sticky Mobile Header for Portal (Only on Mobile screens <= 991px) -->
  <div class="portal-mobile-header">
    <button id="portalMobileToggle" class="portal-mobile-btn" aria-label="Open Navigation Menu">
      <i class="fa-solid fa-bars"></i>
    </button>
    <div class="portal-mobile-brand">
      <span class="portal-mobile-brand-title">IDA ACADEMY</span>
      <span class="portal-mobile-role">
        @if(request()->routeIs('cadet.*') || auth()->user()->isAcademicStudent()) CADET
        @elseif(auth()->user()->isAdmin()) ADMIN
        @elseif(auth()->user()->isInstructor()) INSTRUCTOR
        @else CANDIDATE
        @endif
      </span>
    </div>
    <div style="display: flex; align-items: center; gap: 8px;">
      <div class="portal-mobile-avatar">
        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
      </div>
      <a href="{{ route('logout') }}" title="Logout" style="color: #ff5757; font-size: 15px; padding: 6px;">
        <i class="fa-solid fa-arrow-right-from-bracket"></i>
      </a>
    </div>
  </div>

  <div class="portal-layout">
    <!-- Left Navigation Sidebar (Dark Matte Command Style) -->
    <aside class="portal-sidebar">
      <div class="sidebar-header" style="display: flex; align-items: center; justify-content: space-between;">
        <a href="{{ route('home') }}" class="portal-brand">
          <div class="portal-brand-title">IDA ACADEMY</div>
          <span class="portal-brand-subtitle">
            @if(request()->routeIs('cadet.*') || auth()->user()->isAcademicStudent()) CADET PORTAL
            @elseif(auth()->user()->isAdmin()) ADMIN PANEL
            @elseif(auth()->user()->isInstructor()) INSTRUCTOR PANEL
            @else CANDIDATE PORTAL
            @endif
          </span>
        </a>
        <button id="portalMobileClose" class="portal-mobile-close-btn" aria-label="Close Navigation">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <nav class="sidebar-menu">
        {{-- ================= ACADEMIC CADET NAVIGATION ================= --}}
        @if(request()->routeIs('cadet.*') || auth()->user()->isAcademicStudent())
          <a href="{{ route('cadet.dashboard') }}" class="sidebar-item {{ request()->routeIs('cadet.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-table-cells-large"></i> <span>Dashboard</span>
          </a>
          <a href="{{ route('cadet.exams.index') }}" class="sidebar-item {{ (request()->routeIs('cadet.exams.index') && request('view') !== 'history' && !request()->has('history')) ? 'active' : '' }}">
            <i class="fa-solid fa-clipboard-list"></i> <span>Exam</span>
          </a>
          <a href="{{ route('cadet.exams.history') }}" class="sidebar-item {{ (request()->routeIs('cadet.exams.history') || (request()->routeIs('cadet.exams*') && (request('view') === 'history' || request()->has('history')))) ? 'active' : '' }}">
            <i class="fa-solid fa-clock-rotate-left"></i> <span>Exam History</span>
          </a>
          {{-- Hidden from Cadet Sidebar - Code preserved intact for future activation --}}
          @if(false)
          <a href="{{ route('cadet.routine') }}" class="sidebar-item {{ request()->routeIs('cadet.routine') ? 'active' : '' }}">
            <i class="fa-regular fa-calendar-days"></i> <span>Routine</span>
            <span class="cadet-under-construction-badge">Under Construction</span>
          </a>
          <a href="{{ route('cadet.fees') }}" class="sidebar-item {{ request()->routeIs('cadet.fees') ? 'active' : '' }}">
            <i class="fa-solid fa-credit-card"></i> <span>Payment</span>
            <span class="cadet-under-construction-badge">Under Construction</span>
          </a>
          <a href="{{ route('cadet.dashboard') }}#question-bank" onclick="if(typeof openPreviousQuestionsModal === 'function'){ openPreviousQuestionsModal(); return false; }" class="sidebar-item">
            <i class="fa-solid fa-book-open"></i> <span>Question Bank</span>
            <span class="cadet-under-construction-badge">Under Construction</span>
          </a>
          <a href="{{ route('courses') }}" target="_blank" class="sidebar-item">
            <i class="fa-solid fa-graduation-cap"></i> <span>Other Courses</span>
            <span class="cadet-under-construction-badge">Under Construction</span>
          </a>
          @endif

          @if(auth()->user()->isAdmin() || auth()->user()->isFinanceManager() || auth()->user()->isInstructor())
            <div style="margin-top: 14px; padding: 10px 14px; background: rgba(239, 68, 68, 0.08); border-radius: 8px; border: 1px dashed rgba(239, 68, 68, 0.3);">
              <a href="{{ route('admin.dashboard') }}" style="color: #dc2626; font-size: 12px; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-shield-halved"></i> Return to Admin Command
              </a>
            </div>
          @endif

        {{-- ================= ADMIN NAVIGATION ================= --}}
        @elseif(auth()->user()->isAdmin() || auth()->user()->isFinanceManager())
          <a href="{{ route('admin.dashboard') }}" class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-table-cells-large"></i> <span>Dashboard</span>
          </a>

          <!-- Web Management with Page Submenu -->
          <div class="sidebar-cms-group">
            <a href="{{ route('admin.cms.index') }}" class="sidebar-item {{ request()->routeIs('admin.cms.index') ? 'active' : '' }}">
              <i class="fa-solid fa-globe"></i> <span>Web Management</span>
            </a>
            @if(request()->routeIs('admin.cms*'))
              <div style="padding-left: 12px; margin: 4px 0 8px 14px; border-left: 2px solid rgba(255, 87, 87, 0.3); display: flex; flex-direction: column; gap: 2px;">
                <a href="{{ route('admin.cms.home') }}" class="sidebar-subitem {{ request()->routeIs('admin.cms.home') ? 'active' : '' }}">
                  <i class="fa-solid fa-house"></i> <span>Home Page</span>
                </a>
                <a href="{{ route('admin.cms.about') }}" class="sidebar-subitem {{ request()->routeIs('admin.cms.about') ? 'active' : '' }}">
                  <i class="fa-solid fa-landmark"></i> <span>About Us</span>
                </a>
                <a href="{{ route('admin.cms.courses') }}" class="sidebar-subitem {{ request()->routeIs('admin.cms.courses') ? 'active' : '' }}">
                  <i class="fa-solid fa-book-bookmark"></i> <span>Courses</span>
                </a>
                <a href="{{ route('admin.cms.classes') }}" class="sidebar-subitem {{ request()->routeIs('admin.cms.classes') ? 'active' : '' }}">
                  <i class="fa-regular fa-calendar-days"></i> <span>Classes & Routines</span>
                </a>
                <a href="{{ route('admin.cms.online_tests') }}" class="sidebar-subitem {{ request()->routeIs('admin.cms.online_tests') ? 'active' : '' }}">
                  <i class="fa-solid fa-crosshairs"></i> <span>Online Tests</span>
                </a>
                <a href="{{ route('admin.cms.gallery') }}" class="sidebar-subitem {{ request()->routeIs('admin.cms.gallery') ? 'active' : '' }}">
                  <i class="fa-solid fa-images"></i> <span>Gallery</span>
                </a>
                <a href="{{ route('admin.cms.notices') }}" class="sidebar-subitem {{ request()->routeIs('admin.cms.notices') ? 'active' : '' }}">
                  <i class="fa-solid fa-bullhorn"></i> <span>Notices</span>
                </a>
                <a href="{{ route('admin.cms.contact') }}" class="sidebar-subitem {{ request()->routeIs('admin.cms.contact') ? 'active' : '' }}">
                  <i class="fa-solid fa-address-book"></i> <span>Contact & Footer</span>
                </a>
                <a href="{{ route('admin.cms.branding') }}" class="sidebar-subitem {{ request()->routeIs('admin.cms.branding') ? 'active' : '' }}">
                  <i class="fa-solid fa-palette"></i> <span>Theme & Branding</span>
                </a>
                <a href="{{ route('admin.cms.inquiries') }}" class="sidebar-subitem {{ request()->routeIs('admin.cms.inquiries') ? 'active' : '' }}">
                  <i class="fa-solid fa-envelope-open-text"></i> <span>Inquiries</span>
                </a>
              </div>
            @endif
          </div>
          <a href="{{ route('admin.student_accounts.index') }}" class="sidebar-item {{ request()->routeIs('admin.student_accounts*') ? 'active' : '' }}">
            <i class="fa-solid fa-users-gear"></i> <span>Student Management</span>
          </a>
          <a href="{{ route('admin.exam_management.index') }}" class="sidebar-item {{ request()->routeIs('admin.exam_management*') ? 'active' : '' }}">
            <i class="fa-solid fa-file-signature"></i> <span>Exam Management</span>
          </a>
          <a href="{{ route('admin.students.index') }}" class="sidebar-item {{ request()->routeIs('admin.students*') ? 'active' : '' }}">
            <i class="fa-solid fa-user-graduate"></i> <span>Users & Cadets</span>
          </a>
          <a href="{{ route('admin.instructors.index') }}" class="sidebar-item {{ request()->routeIs('admin.instructors*') ? 'active' : '' }}">
            <i class="fa-solid fa-user-tie"></i> <span>Team & Instructors</span>
          </a>
          <a href="{{ route('admin.courses.index') }}" class="sidebar-item {{ request()->routeIs('admin.courses*') ? 'active' : '' }}">
            <i class="fa-solid fa-book-bookmark"></i> <span>Courses</span>
          </a>
          <a href="{{ route('admin.batches.index') }}" class="sidebar-item {{ request()->routeIs('admin.batches*') ? 'active' : '' }}">
            <i class="fa-solid fa-layer-group"></i> <span>Batches</span>
          </a>
          <a href="{{ route('admin.routines.index') }}" class="sidebar-item {{ request()->routeIs('admin.routines*') ? 'active' : '' }}">
            <i class="fa-regular fa-calendar-days"></i> <span>Class Schedule</span>
          </a>
          <a href="{{ route('admin.attendance.index') }}" class="sidebar-item {{ request()->routeIs('admin.attendance*') ? 'active' : '' }}">
            <i class="fa-solid fa-clipboard-user"></i> <span>Attendance</span>
          </a>
          <a href="{{ route('admin.fees.index') }}" class="sidebar-item {{ request()->routeIs('admin.fees*') ? 'active' : '' }}">
            <i class="fa-solid fa-file-invoice-dollar"></i> <span>Invoices & Fees</span>
          </a>
          <a href="{{ route('admin.payments.verification') }}" class="sidebar-item {{ request()->routeIs('admin.payments.verification*') ? 'active' : '' }}">
            <i class="fa-solid fa-money-check-dollar"></i> <span>Payment Reviews</span>
          </a>
          <a href="{{ route('admin.finance.index') }}" class="sidebar-item {{ request()->routeIs('admin.finance.index') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-line"></i> <span>Financial Ledger</span>
          </a>
          <a href="{{ route('admin.finance.reports') }}" class="sidebar-item {{ request()->routeIs('admin.finance.reports') ? 'active' : '' }}">
            <i class="fa-solid fa-file-contract"></i> <span>Financial Reports</span>
          </a>
          <a href="{{ route('admin.exams.attempts') }}" class="sidebar-item {{ request()->routeIs('admin.exams.attempts*') ? 'active' : '' }}">
            <i class="fa-solid fa-square-poll-vertical"></i> <span>Exam Results</span>
          </a>
          <a href="{{ route('admin.audit.index') }}" class="sidebar-item {{ request()->routeIs('admin.audit*') ? 'active' : '' }}">
            <i class="fa-solid fa-shield-halved"></i> <span>Audit Logs</span>
          </a>
          <a href="{{ route('admin.accounts.index') }}" class="sidebar-item {{ request()->routeIs('admin.accounts*') ? 'active' : '' }}">
            <i class="fa-solid fa-id-card"></i> <span>Account & ID Control</span>
          </a>

        {{-- ================= INSTRUCTOR NAVIGATION ================= --}}
        @elseif(auth()->user()->isInstructor())
          <a href="{{ route('instructor.dashboard') }}" class="sidebar-item {{ request()->routeIs('instructor.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-table-cells-large"></i> <span>Dashboard</span>
          </a>
          <a href="{{ route('instructor.students.index') }}" class="sidebar-item {{ request()->routeIs('instructor.students*') ? 'active' : '' }}">
            <i class="fa-solid fa-users"></i> <span>Assigned Cadets</span>
          </a>
          <a href="{{ route('instructor.attendance.index') }}" class="sidebar-item {{ request()->routeIs('instructor.attendance*') ? 'active' : '' }}">
            <i class="fa-solid fa-clipboard-user"></i> <span>Mark Attendance</span>
          </a>



        {{-- ================= EXTERNAL CANDIDATE NAVIGATION ================= --}}
        @elseif(auth()->user()->isExternalStudent())
          <a href="{{ route('external.dashboard') }}" class="sidebar-item {{ request()->routeIs('external.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-table-cells-large"></i> <span>Dashboard</span>
          </a>
          <a href="{{ route('external.tests.index') }}" class="sidebar-item {{ request()->routeIs('external.tests*') ? 'active' : '' }}">
            <i class="fa-solid fa-crosshairs"></i> <span>Online Assessments</span>
          </a>
        @endif

        <div style="margin-top: auto; padding-top: 16px;">
          <a href="{{ route('home') }}" target="_blank" class="sidebar-item" style="color: #64748b;">
            <i class="fa-solid fa-globe"></i> <span>Live Academy Site</span>
          </a>
        </div>
      </nav>

      <!-- User Profile Card in Sidebar Footer (Exact match to screenshot) -->
      <div class="sidebar-user-card">
        <div style="display: flex; align-items: center; gap: 10px; overflow: hidden;">
          <div style="width: 36px; height: 36px; border-radius: 50%; background: #131620; border: 1px solid rgba(255, 87, 87, 0.4); color: #ff5757; display: grid; place-items: center; font-size: 14px; flex-shrink: 0;">
            <i class="fa-regular fa-user"></i>
          </div>
          <div style="overflow: hidden;">
            <strong style="display: block; font-size: 13px; color: #ffffff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-family: 'Poppins', sans-serif;">{{ auth()->user()->name }}</strong>
            <small style="font-size: 9.5px; font-weight: 700; color: #64748b; letter-spacing: 0.8px; text-transform: uppercase;">{{ str_replace('_', ' ', auth()->user()->role) }}</small>
          </div>
        </div>
        <a href="{{ route('logout') }}" title="Logout" style="color: #64748b; font-size: 14px; padding: 6px; transition: color 0.2s ease;" onmouseover="this.style.color='#ff5757'" onmouseout="this.style.color='#64748b'">
          <i class="fa-solid fa-arrow-right-from-bracket"></i>
        </a>
      </div>
    </aside>

    <!-- Main Viewport Area -->
    <main class="portal-main">
      @if($isCadetPortal)
        @php
          $portalCadetStudent = \App\Models\Student::with('currentCourse')->where('user_id', auth()->id())->first();
          if (!$portalCadetStudent && in_array(auth()->user()->role, ['super_admin', 'admin', 'instructor'])) {
              $portalCadetStudent = \App\Models\Student::with('currentCourse')->first();
          }
          $portalCadetCourse = $portalCadetStudent->currentCourse->name ?? 'ISSB Course';
          $portalCadetId = $portalCadetStudent->student_id_code ?? (auth()->user()->account_id ?? '250236');

          $cadetSectionTitle = 'Dashboard';
          $sectionBadgeOverride = trim($__env->yieldContent('cadet_badge'));
          if (!empty($sectionBadgeOverride)) {
              $cadetSectionTitle = $sectionBadgeOverride;
          } elseif (request()->routeIs('cadet.dashboard') || request()->is('*cadet/dashboard*')) {
              $cadetSectionTitle = 'Dashboard';
          } elseif (request()->routeIs('cadet.exams.history') || request('view') === 'history' || request()->has('history') || request()->routeIs('cadet.exams.result') || request()->is('*cadet/exam-history*')) {
              $cadetSectionTitle = 'Exam History';
          } elseif (request()->routeIs('cadet.exams*') || request()->is('*cadet/exams*')) {
              $cadetSectionTitle = 'Exam';
          } elseif (request()->routeIs('cadet.routine') || request()->is('*cadet/routine*')) {
              $cadetSectionTitle = 'Routine';
          } elseif (request()->routeIs('cadet.fees*') || request()->is('*cadet/fees*')) {
              $cadetSectionTitle = 'Payment';
          }
        @endphp
        <!-- Pure Cadet Portal Header (Top Left: Dynamic 3D Section Badge | Top Right: Candidate Name / Account Dropdown) -->
        <style>
          .cadet-3d-page-title {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-family: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
            font-size: 20px !important;
            font-weight: 800 !important;
            letter-spacing: -0.025em !important;
            color: #0f172a !important;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 45%, #edf2f7 100%) !important;
            border: 1.5px solid #cbd5e1 !important;
            border-bottom: 4px solid #94a3b8 !important;
            border-radius: 14px !important;
            padding: 8px 22px !important;
            line-height: 1.2 !important;
            -webkit-font-smoothing: antialiased !important;
            -moz-osx-font-smoothing: grayscale !important;
            text-rendering: optimizeLegibility !important;
            box-shadow: 
              0 6px 18px -2px rgba(15, 23, 42, 0.1),
              0 2px 5px -1px rgba(15, 23, 42, 0.04),
              inset 0 1.5px 1px #ffffff,
              inset 0 -1.5px 2px rgba(15, 23, 42, 0.04) !important;
            user-select: none !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
            cursor: default !important;
          }
          .cadet-3d-page-title:hover {
            transform: translateY(-2px) !important;
            border-bottom: 5.5px solid #64748b !important;
            box-shadow: 
              0 10px 22px -3px rgba(15, 23, 42, 0.14),
              0 4px 8px -2px rgba(15, 23, 42, 0.06),
              inset 0 1.5px 1px #ffffff !important;
          }
          .cadet-account-sign-btn:hover {
            background: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
          }
          .cadet-menu-item:hover {
            background: #f8fafc !important;
            color: #0f172a !important;
          }
          @keyframes cadetMenuDrop {
            from { opacity: 0; transform: translateY(-6px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
          }
        </style>

        <div class="portal-topbar cadet-pure-topbar" style="background: #ffffff; border-bottom: 1px solid #f1f5f9; padding: 12px 24px; display: flex; align-items: center; justify-content: space-between; position: relative; z-index: 100;">
          <!-- Top Left Corner: 3D Section Badge (Inside that slim box) -->
          <div style="display: flex; align-items: center;">
            <div class="cadet-3d-page-title" id="cadetTopSectionBadge" title="{{ $cadetSectionTitle }}">
              {{ $cadetSectionTitle }}
            </div>
          </div>

          <!-- Top Right Corner: Candidate Name (Account Sign with Dropdown Menu) -->
          <div class="cadet-account-dropdown-wrapper" style="position: relative;">
            <button type="button" id="cadetAccountTrigger" onclick="toggleCadetAccountDropdown(event)" class="cadet-account-sign-btn" aria-expanded="false" aria-haspopup="true" style="display: inline-flex; align-items: center; gap: 10px; background: #ffffff; border: 1.5px solid #e2e8f0; padding: 5px 14px 5px 6px; border-radius: 999px; cursor: pointer; transition: all 0.2s ease; outline: none; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
              <div class="cadet-account-avatar" style="width: 34px; height: 34px; border-radius: 50%; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; box-shadow: 0 2px 6px rgba(37, 99, 235, 0.28);">
                <i class="fa-solid fa-user"></i>
              </div>
              <div style="display: flex; flex-direction: column; align-items: flex-start; text-align: left; line-height: 1.2;">
                <span style="font-size: 13.5px; font-weight: 800; color: #0f172a; max-width: 180px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ auth()->user()->name }}</span>
                <span style="font-size: 11px; font-weight: 600; color: #64748b;">ID: #{{ $portalCadetId }}</span>
              </div>
              <i class="fa-solid fa-chevron-down" id="cadetAccountChevron" style="font-size: 10.5px; color: #94a3b8; transition: transform 0.2s ease; margin-left: 2px;"></i>
            </button>

            <!-- Dropdown Menu: Right-Aligned to prevent viewport overflow -->
            <div id="cadetAccountMenu" class="cadet-account-menu" style="display: none; position: absolute; top: calc(100% + 8px); right: 0; left: auto; min-width: 270px; background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 18px 40px -10px rgba(15, 23, 42, 0.18), 0 4px 12px rgba(15, 23, 42, 0.08); padding: 14px; z-index: 1050; animation: cadetMenuDrop 0.18s cubic-bezier(0.16, 1, 0.3, 1);">
              <!-- Student Info: Name and ID # -->
              <div style="padding: 4px 6px 8px 6px;">
                <div style="font-size: 15px; font-weight: 800; color: #0f172a; letter-spacing: -0.2px;">
                  {{ auth()->user()->name }}
                </div>
                <div style="display: flex; align-items: center; gap: 6px; margin-top: 4px; flex-wrap: wrap;">
                  <span style="font-size: 11.5px; font-weight: 700; color: #047857; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 2px 8px; border-radius: 999px;">
                    ID #{{ $portalCadetId }}
                  </span>
                  @if($portalCadetCourse)
                    <span style="font-size: 11px; font-weight: 600; color: #64748b;">
                      • {{ $portalCadetCourse }}
                    </span>
                  @endif
                </div>
              </div>

              <!-- Horizontal Line -->
              <hr style="margin: 8px 0 10px 0; border: 0; border-top: 1px solid #f1f5f9;">

              <!-- Options: Manage Account -->
              <div style="display: flex; flex-direction: column; gap: 4px;">
                <button type="button" onclick="openCadetUpdateModal(); closeCadetAccountDropdown();" class="cadet-menu-item" style="display: flex; align-items: center; gap: 10px; width: 100%; padding: 10px 12px; border-radius: 10px; border: none; background: transparent; font-size: 13px; font-weight: 600; color: #334155; cursor: pointer; text-align: left; transition: all 0.15s ease;">
                  <span style="width: 28px; height: 28px; border-radius: 8px; background: #eff6ff; display: flex; align-items: center; justify-content: center; color: #2563eb; font-size: 13px;">
                    <i class="fa-regular fa-pen-to-square"></i>
                  </span>
                  Manage Account
                </button>

                <!-- Last Option: Logout -->
                <a href="{{ route('logout') }}" class="cadet-menu-item" style="display: flex; align-items: center; gap: 10px; width: 100%; padding: 10px 12px; border-radius: 10px; text-decoration: none; font-size: 13px; font-weight: 600; color: #dc2626; cursor: pointer; transition: all 0.15s ease;">
                  <span style="width: 28px; height: 28px; border-radius: 8px; background: #fef2f2; display: flex; align-items: center; justify-content: center; color: #dc2626; font-size: 13px;">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                  </span>
                  Logout
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Cadet Update Details Modal (Globally available across cadet portal) -->
        <div id="cadetUpdateDetailsModal" class="cadet-modal-backdrop" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 16px;">
          <div class="cadet-modal-dialog" style="background: #ffffff; border-radius: 18px; width: 100%; max-width: 540px; box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.25); overflow: hidden; border: 1px solid #e2e8f0; animation: modalPop 0.22s ease-out;">
            <div class="cadet-modal-header" style="padding: 16px 20px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; background: #f8fafc;">
              <h4 style="margin: 0; font-size: 15px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <i class="fa-regular fa-pen-to-square" style="color: #2563eb;"></i> Manage Account
              </h4>
              <button type="button" class="cadet-modal-close" onclick="closeCadetUpdateModal()" style="background: none; border: none; font-size: 20px; color: #94a3b8; cursor: pointer; line-height: 1; padding: 4px;">&times;</button>
            </div>
            <form action="{{ route('cadet.update_details') }}" method="POST">
              @csrf
              <div class="cadet-modal-body" style="padding: 20px; max-height: 78vh; overflow-y: auto;">
                <div class="cadet-form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                  <div class="cadet-form-group full-width" style="grid-column: 1 / -1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">Candidate Full Name</label>
                    <input type="text" name="name" class="cadet-form-control" style="border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 12.5px; color: #0f172a; outline: none;" value="{{ $portalCadetStudent->user->name ?? auth()->user()->name }}" required>
                  </div>

                  <div class="cadet-form-group" style="display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">Email Address</label>
                    <input type="email" name="email" class="cadet-form-control" style="border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 12.5px; color: #0f172a; outline: none;" value="{{ $portalCadetStudent->user->email ?? auth()->user()->email }}" required>
                  </div>

                  <div class="cadet-form-group" style="display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">Contact Phone</label>
                    <input type="text" name="phone" class="cadet-form-control" style="border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 12.5px; color: #0f172a; outline: none;" value="{{ $portalCadetStudent->user->phone ?? '' }}" placeholder="01XXXXXXXXX">
                  </div>

                  <div class="cadet-form-group" style="display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">Target Wing</label>
                    <select name="target_wing" class="cadet-form-control" style="border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 12.5px; color: #0f172a; outline: none;">
                      <option value="Army" {{ ($portalCadetStudent->target_wing ?? '') === 'Army' ? 'selected' : '' }}>Army</option>
                      <option value="Navy" {{ ($portalCadetStudent->target_wing ?? '') === 'Navy' ? 'selected' : '' }}>Navy</option>
                      <option value="Air Force" {{ ($portalCadetStudent->target_wing ?? '') === 'Air Force' ? 'selected' : '' }}>Air Force</option>
                      <option value="General" {{ ($portalCadetStudent->target_wing ?? '') === 'General' ? 'selected' : '' }}>General / Tri-Service</option>
                    </select>
                  </div>

                  <div class="cadet-form-group" style="display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">HSC Passing Year</label>
                    <input type="text" name="hsc_year" class="cadet-form-control" style="border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 12.5px; color: #0f172a; outline: none;" value="{{ $portalCadetStudent->hsc_year ?? '' }}" placeholder="e.g. 2025">
                  </div>

                  <div class="cadet-form-group full-width" style="grid-column: 1 / -1; display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">College / Institution</label>
                    <input type="text" name="institution" class="cadet-form-control" style="border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 12.5px; color: #0f172a; outline: none;" value="{{ $portalCadetStudent->institution ?? '' }}" placeholder="e.g. Notre Dame College">
                  </div>

                  <div class="cadet-form-group" style="display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">Home District</label>
                    <input type="text" name="district" class="cadet-form-control" style="border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 12.5px; color: #0f172a; outline: none;" value="{{ $portalCadetStudent->district ?? '' }}" placeholder="e.g. Dhaka">
                  </div>

                  <div class="cadet-form-group" style="display: flex; flex-direction: column; gap: 4px;">
                    <label style="font-size: 11px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">New Password (Optional)</label>
                    <input type="password" name="password" class="cadet-form-control" style="border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 12.5px; color: #0f172a; outline: none;" placeholder="Leave blank to keep current">
                  </div>
                </div>
              </div>
              <div class="cadet-modal-footer" style="padding: 12px 20px; border-top: 1px solid #f1f5f9; background: #f8fafc; display: flex; align-items: center; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn-tactical btn-tactical-outline" style="padding: 7px 14px; font-size: 12px; border-radius: 8px; background: #ffffff; border: 1.5px solid #cbd5e1; cursor: pointer;" onclick="closeCadetUpdateModal()">Cancel</button>
                <button type="submit" class="btn-tactical btn-tactical-primary" style="padding: 7px 18px; font-size: 12px; border-radius: 8px; background: #2563eb; color: #ffffff; border: none; cursor: pointer;">Save Changes</button>
              </div>
            </form>
          </div>
        </div>

        <!-- Cadet Previous Questions Modal (Question Bank) -->
        <div id="cadetPreviousQuestionsModal" class="cadet-modal-backdrop" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 16px;">
          <div class="cadet-modal-dialog" style="background: #ffffff; border-radius: 18px; width: 100%; max-width: 600px; box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.25); overflow: hidden; border: 1px solid #e2e8f0; animation: modalPop 0.22s ease-out;">
            <div class="cadet-modal-header" style="padding: 16px 20px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; background: #f8fafc;">
              <h4 style="margin: 0; font-size: 15px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-book-open" style="color: #6366f1;"></i> Question Bank & Previous Papers
              </h4>
              <button type="button" class="cadet-modal-close" onclick="closePreviousQuestionsModal()" style="background: none; border: none; font-size: 20px; color: #94a3b8; cursor: pointer; line-height: 1; padding: 4px;">&times;</button>
            </div>
            <div class="cadet-modal-body" style="padding: 20px; max-height: 78vh; overflow-y: auto;">
              <!-- Under Construction Notice -->
              <div style="background: #fffbeb; border: 1.5px dashed #f59e0b; border-radius: 12px; padding: 14px 16px; margin-bottom: 16px; display: flex; align-items: center; gap: 14px;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #f59e0b; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; box-shadow: 0 2px 8px rgba(245, 158, 11, 0.25);">
                  <i class="fa-solid fa-person-digging"></i>
                </div>
                <div>
                  <div style="font-size: 11.5px; font-weight: 800; color: #b45309; text-transform: uppercase; letter-spacing: 0.4px;">Under Construction</div>
                  <div style="font-size: 12.5px; color: #78350f; font-weight: 500; margin-top: 2px; line-height: 1.4;">
                    The digital Question Bank and past paper archive repository is currently under construction and being curated for your squadron.
                  </div>
                </div>
              </div>

              <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 14px; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                <span style="font-size: 12px; color: #64748b; font-weight: 600;">Enrolled Course:</span>
                <strong style="font-size: 12.5px; color: #1e293b; font-weight: 700;">{{ $portalCadetCourse }}</strong>
              </div>

              <div style="display: flex; flex-direction: column; gap: 8px;">
                <div class="archive-item" style="display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; border-radius: 10px; border: 1px solid #e2e8f0; background: #ffffff;">
                  <div>
                    <div style="font-size: 12.5px; font-weight: 700; color: #0f172a;">2024 ISSB Verbal & Abstract IQ Reasoning</div>
                    <div style="font-size: 11px; color: #64748b; margin-top: 2px;">Comprehensive mock assessment with answer key & solutions</div>
                  </div>
                  <a href="{{ route('cadet.exams.index') }}" class="btn-tactical btn-tactical-primary" style="padding: 5px 12px; font-size: 11px; border-radius: 6px; white-space: nowrap; text-decoration: none;">
                    Practice
                  </a>
                </div>

                <div class="archive-item" style="display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; border-radius: 10px; border: 1px solid #e2e8f0; background: #ffffff;">
                  <div>
                    <div style="font-size: 12.5px; font-weight: 700; color: #0f172a;">2024 Word Association Test (WAT) 100-Words Bank</div>
                    <div style="font-size: 11px; color: #64748b; margin-top: 2px;">Official ISSB psychological WAT drill paper</div>
                  </div>
                  <a href="{{ route('cadet.exams.index') }}" class="btn-tactical btn-tactical-primary" style="padding: 5px 12px; font-size: 11px; border-radius: 6px; white-space: nowrap; text-decoration: none;">
                    Practice
                  </a>
                </div>

                <div class="archive-item" style="display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; border-radius: 10px; border: 1px solid #e2e8f0; background: #ffffff;">
                  <div>
                    <div style="font-size: 12.5px; font-weight: 700; color: #0f172a;">2023 BMA Long Course Preliminary Exam Paper</div>
                    <div style="font-size: 11px; color: #64748b; margin-top: 2px;">General Knowledge, English & Math standard model test</div>
                  </div>
                  <a href="{{ route('cadet.exams.index') }}" class="btn-tactical btn-tactical-primary" style="padding: 5px 12px; font-size: 11px; border-radius: 6px; white-space: nowrap; text-decoration: none;">
                    Practice
                  </a>
                </div>

                <div class="archive-item" style="display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; border-radius: 10px; border: 1px solid #e2e8f0; background: #ffffff;">
                  <div>
                    <div style="font-size: 12.5px; font-weight: 700; color: #0f172a;">2023 Bangladesh Navy Officer Cadet Aptitude</div>
                    <div style="font-size: 11px; color: #64748b; margin-top: 2px;">Physics, Higher Math & General Aptitude question set</div>
                  </div>
                  <a href="{{ route('cadet.exams.index') }}" class="btn-tactical btn-tactical-primary" style="padding: 5px 12px; font-size: 11px; border-radius: 6px; white-space: nowrap; text-decoration: none;">
                    Practice
                  </a>
                </div>

                <div class="archive-item" style="display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; border-radius: 10px; border: 1px solid #e2e8f0; background: #ffffff;">
                  <div>
                    <div style="font-size: 12.5px; font-weight: 700; color: #0f172a;">2022 Bangladesh Air Force GDP Screening Test</div>
                    <div style="font-size: 11px; color: #64748b; margin-top: 2px;">Pilot aptitude, spatial reasoning & mechanics test</div>
                  </div>
                  <a href="{{ route('cadet.exams.index') }}" class="btn-tactical btn-tactical-primary" style="padding: 5px 12px; font-size: 11px; border-radius: 6px; white-space: nowrap; text-decoration: none;">
                    Practice
                  </a>
                </div>
              </div>
            </div>
            <div class="cadet-modal-footer" style="padding: 12px 20px; border-top: 1px solid #f1f5f9; background: #f8fafc; display: flex; align-items: center; justify-content: flex-end; gap: 10px;">
              <button type="button" class="btn-tactical btn-tactical-outline" style="padding: 7px 14px; font-size: 12px; border-radius: 8px; background: #ffffff; border: 1.5px solid #cbd5e1; cursor: pointer;" onclick="closePreviousQuestionsModal()">Close</button>
              <a href="{{ route('cadet.exams.index') }}" class="btn-tactical btn-tactical-primary" style="padding: 7px 14px; font-size: 12px; border-radius: 8px; background: #2563eb; color: #ffffff; text-decoration: none;">View All Exams</a>
            </div>
          </div>
        </div>

        <script>
          function toggleCadetAccountDropdown(e) {
            if (e) e.stopPropagation();
            var menu = document.getElementById('cadetAccountMenu');
            var chevron = document.getElementById('cadetAccountChevron');
            var btn = document.getElementById('cadetAccountTrigger');
            if (!menu) return;
            var isHidden = menu.style.display === 'none' || menu.style.display === '';
            menu.style.display = isHidden ? 'block' : 'none';
            if (chevron) {
              chevron.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
            }
            if (btn) {
              btn.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
            }
          }

          function closeCadetAccountDropdown() {
            var menu = document.getElementById('cadetAccountMenu');
            var chevron = document.getElementById('cadetAccountChevron');
            var btn = document.getElementById('cadetAccountTrigger');
            if (menu) menu.style.display = 'none';
            if (chevron) chevron.style.transform = 'rotate(0deg)';
            if (btn) btn.setAttribute('aria-expanded', 'false');
          }

          function openCadetUpdateModal() {
            var modal = document.getElementById('cadetUpdateDetailsModal');
            if (modal) modal.style.display = 'flex';
          }

          function closeCadetUpdateModal() {
            var modal = document.getElementById('cadetUpdateDetailsModal');
            if (modal) modal.style.display = 'none';
          }

          function openPreviousQuestionsModal() {
            var modal = document.getElementById('cadetPreviousQuestionsModal');
            if (modal) modal.style.display = 'flex';
          }

          function closePreviousQuestionsModal() {
            var modal = document.getElementById('cadetPreviousQuestionsModal');
            if (modal) modal.style.display = 'none';
          }

          document.addEventListener('click', function(e) {
            var wrapper = document.querySelector('.cadet-account-dropdown-wrapper');
            if (wrapper && !wrapper.contains(e.target)) {
              closeCadetAccountDropdown();
            }
            var updateModal = document.getElementById('cadetUpdateDetailsModal');
            if (e.target === updateModal) {
              closeCadetUpdateModal();
            }
            var questionsModal = document.getElementById('cadetPreviousQuestionsModal');
            if (e.target === questionsModal) {
              closePreviousQuestionsModal();
            }
          });

          function syncCadetTopBadge() {
            var badge = document.getElementById('cadetTopSectionBadge');
            if (!badge) return;
            if (window.location.pathname.includes('/cadet/exam-history') || window.location.hash === '#history' || new URLSearchParams(window.location.search).get('view') === 'history') {
              badge.textContent = 'Exam History';
            } else if (window.location.pathname.includes('/cadet/exams')) {
              badge.textContent = 'Exam';
            }
          }
          window.addEventListener('hashchange', syncCadetTopBadge);
          if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', syncCadetTopBadge);
          } else {
            syncCadetTopBadge();
          }
        </script>
      @else
        <!-- Universal Portal Header (For Admin, Instructor, External Candidate) -->
        <div class="portal-topbar">
          <div>
            @hasSection('page_title')
              <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 2px;">
                @yield('page_title')
              </div>
            @endif
            <div style="color: #8c96a8; font-size: 14px; font-weight: 500;">
              Welcome back, <span style="color: #ffffff; font-weight: 700;">{{ auth()->user()->name }}</span>
            </div>
          </div>

          <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
            <!-- Clock -->
            <div class="tactical-clock">
              <i class="fa-regular fa-clock" style="color: #ff5757;"></i>
              <span class="clock-time" id="portalClock">--:--:--</span>
            </div>

            <!-- Public Website Button -->
            <a href="{{ route('home') }}" target="_blank" class="sidebar-item" style="background: #181c26; border: 1px solid rgba(255, 255, 255, 0.06); padding: 8px 14px; font-size: 12.5px; color: #cbd5e1;">
              <i class="fa-solid fa-arrow-up-right-from-square" style="color: #34d399;"></i> Preview Site
            </a>

            <!-- Logout Button -->
            <a href="{{ route('logout') }}" class="sidebar-item" style="background: rgba(255, 87, 87, 0.1); border: 1px solid rgba(255, 87, 87, 0.2); padding: 8px 14px; font-size: 12.5px; color: #ff5757;">
              <i class="fa-solid fa-arrow-right-from-bracket" style="color: #ff5757;"></i> Logout
            </a>
          </div>
        </div>
      @endif

      <!-- Flash Notifications -->
      @if(session('success'))
        <div class="alert-box alert-success" style="background: rgba(16, 185, 129, 0.12); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3);">
          <i class="fa-solid fa-circle-check"></i>
          <span>{{ session('success') }}</span>
        </div>
      @endif
      @if(session('error'))
        <div class="alert-box alert-error" style="background: rgba(239, 68, 68, 0.12); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3);">
          <i class="fa-solid fa-triangle-exclamation"></i>
          <span>{{ session('error') }}</span>
        </div>
      @endif
      @if($errors->any())
        <div class="alert-box alert-error" style="background: rgba(239, 68, 68, 0.12); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3);">
          <i class="fa-solid fa-circle-exclamation"></i>
          <div>
            @foreach($errors->all() as $err)
              <div>{{ $err }}</div>
            @endforeach
          </div>
        </div>
      @endif

      <!-- Stage Content -->
      @yield('content')
    </main>
  </div>

  <script>
    // Live Timekeeper
    function startClock() {
      const clockEl = document.getElementById('portalClock');
      if (!clockEl) return;
      function update() {
        const now = new Date();
        let h = now.getHours();
        const m = String(now.getMinutes()).padStart(2, '0');
        const s = String(now.getSeconds()).padStart(2, '0');
        const ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12 || 12;
        clockEl.textContent = `${String(h).padStart(2, '0')}:${m}:${s} ${ampm}`;
      }
      setInterval(update, 1000);
      update();
    }
    // Global Sleek Toast Notification
    window.showToast = function(message, type = 'success') {
      let toastContainer = document.getElementById('globalToastContainer');
      if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'globalToastContainer';
        toastContainer.style.cssText = 'position: fixed; bottom: 24px; right: 24px; z-index: 99999; display: flex; flex-direction: column; gap: 10px; pointer-events: none;';
        document.body.appendChild(toastContainer);
      }

      const toast = document.createElement('div');
      toast.style.cssText = 'pointer-events: auto; min-width: 280px; max-width: 440px; padding: 12px 18px; border-radius: 10px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.4); transform: translateY(20px); opacity: 0; transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);';
      
      if (type === 'success') {
        toast.style.background = '#064e3b';
        toast.style.border = '1px solid #059669';
        toast.style.color = '#34d399';
        toast.innerHTML = `<i class="fa-solid fa-circle-check" style="font-size: 16px; color: #10b981;"></i> <span>${message}</span>`;
      } else {
        toast.style.background = '#450a0a';
        toast.style.border = '1px solid #dc2626';
        toast.style.color = '#f87171';
        toast.innerHTML = `<i class="fa-solid fa-circle-exclamation" style="font-size: 16px; color: #ef4444;"></i> <span>${message}</span>`;
      }

      toastContainer.appendChild(toast);
      requestAnimationFrame(() => {
        toast.style.transform = 'translateY(0)';
        toast.style.opacity = '1';
      });

      setTimeout(() => {
        toast.style.transform = 'translateY(10px)';
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
      }, 3500);
    };

    // Mobile Portal Navigation Drawer Logic
    document.addEventListener('DOMContentLoaded', function() {
      startClock();

      const portalToggle = document.getElementById('portalMobileToggle');
      const portalClose = document.getElementById('portalMobileClose');
      const portalSidebar = document.querySelector('.portal-sidebar');
      const portalBackdrop = document.getElementById('portalBackdrop');

      function openPortalDrawer() {
        if (portalSidebar) portalSidebar.classList.add('open');
        if (portalBackdrop) portalBackdrop.classList.add('active');
        document.body.style.overflow = 'hidden';
      }

      function closePortalDrawer() {
        if (portalSidebar) portalSidebar.classList.remove('open');
        if (portalBackdrop) portalBackdrop.classList.remove('active');
        document.body.style.overflow = '';
      }

      if (portalToggle) portalToggle.addEventListener('click', openPortalDrawer);
      if (portalClose) portalClose.addEventListener('click', closePortalDrawer);
      if (portalBackdrop) portalBackdrop.addEventListener('click', closePortalDrawer);

      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closePortalDrawer();
      });

      // Close drawer when clicking a navigation link inside sidebar on mobile
      if (portalSidebar) {
        portalSidebar.querySelectorAll('.sidebar-item, .sidebar-subitem').forEach(link => {
          link.addEventListener('click', function() {
            if (window.innerWidth <= 991) {
              closePortalDrawer();
            }
          });
        });
      }

      // Ensure scrolling anywhere inside the sidebar scrolls the sidebar-menu independently
      if (portalSidebar) {
        const sidebarMenu = portalSidebar.querySelector('.sidebar-menu');
        if (sidebarMenu) {
          portalSidebar.addEventListener('wheel', function(e) {
            if (!sidebarMenu.contains(e.target)) {
              sidebarMenu.scrollTop += e.deltaY;
              e.preventDefault();
            }
          }, { passive: false });
        }
      }
    });
  </script>

  @yield('scripts')
</body>
</html>