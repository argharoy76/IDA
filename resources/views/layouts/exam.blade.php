<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Examination Session | Imperial Defence Academy')</title>

  <!-- Google Fonts: Poppins & Roboto -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,600&family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900;1,400;1,700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

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

  <!-- IDA Theme Styles -->
  <link rel="stylesheet" href="{{ asset('css/ida_theme.css') }}">

  <style>
    :root {
      --brand-emerald: {{ cms('color_primary', '#059669') }};
      --brand-primary: {{ cms('color_primary', '#064e3b') }};
      --brand-deep: {{ cms('color_deep', '#022c22') }};
      --brand-mint: {{ cms('color_mint', '#10b981') }};
      --accent-gold: {{ cms('color_gold', '#d4af37') }};
      --surface-bg: #f8fafc;
      --card-bg: #ffffff;
      --border-color: #e2e8f0;
      --text-main: #0f172a;
      --text-muted: #64748b;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Poppins', 'Roboto', sans-serif !important;
      background: var(--surface-bg);
      color: var(--text-main);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* Modern Mathematical Typography */
    .katex {
      font-size: 1.14em !important;
      text-rendering: geometricPrecision !important;
    }
    .katex-display {
      display: block !important;
      margin: 14px 0 !important;
      padding: 14px 20px !important;
      background: #f1f5f9 !important;
      border: 1px solid #e2e8f0 !important;
      border-radius: 10px !important;
      text-align: center !important;
      overflow-x: auto !important;
      overflow-y: hidden !important;
    }
    .katex-display > .katex {
      font-size: 1.28em !important;
    }
    .katex .matrix, .katex .bmatrix, .katex .pmatrix, .katex .vmatrix, .katex .array {
      margin: 4px 0 !important;
    }
    .katex .matrix-cell {
      padding: 3px 6px !important;
    }

    /* Fixed Test Topbar */
    .exam-topbar {
      position: sticky;
      top: 0;
      z-index: 1000;
      background: #ffffff;
      border-bottom: 2px solid var(--border-color);
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
      padding: 12px 24px;
    }

    .exam-topbar-inner {
      max-width: 1400px;
      margin: 0 auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 16px;
      flex-wrap: wrap;
    }

    .exam-brand-area {
      display: flex;
      align-items: center;
      gap: 14px;
    }

    .exam-brand-title {
      font-size: 15px;
      font-weight: 800;
      color: var(--brand-deep);
      letter-spacing: 0.5px;
      line-height: 1.2;
    }

    .exam-brand-sub {
      font-size: 11px;
      color: var(--text-muted);
      font-weight: 600;
    }

    /* Live Countdown Clock Widget */
    .exam-timer-card {
      background: #ffffff;
      border: 2px solid #059669;
      border-radius: 12px;
      padding: 6px 20px;
      display: flex;
      align-items: center;
      gap: 12px;
      box-shadow: 0 2px 10px rgba(5, 150, 105, 0.12);
      transition: all 0.3s ease;
    }

    .exam-timer-card.warning {
      border-color: #d97706 !important;
      background: #fffbeb !important;
      box-shadow: 0 2px 12px rgba(217, 119, 6, 0.2) !important;
    }

    .exam-timer-card.danger {
      border-color: #dc2626 !important;
      background: #fef2f2 !important;
      animation: timerPulse 1.2s infinite;
      box-shadow: 0 2px 15px rgba(220, 38, 38, 0.25) !important;
    }

    @keyframes timerPulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.03); }
    }

    .timer-label {
      font-size: 9.5px;
      text-transform: uppercase;
      color: var(--text-muted);
      font-weight: 800;
      letter-spacing: 0.5px;
    }

    .timer-digits {
      font-family: 'Poppins', monospace;
      font-size: 22px;
      font-weight: 800;
      color: #0f172a;
      letter-spacing: 1px;
      line-height: 1.1;
    }

    .exam-timer-card.warning .timer-digits {
      color: #b45309 !important;
    }

    .exam-timer-card.danger .timer-digits {
      color: #dc2626 !important;
    }

    /* Action Buttons */
    .btn-exam-submit {
      background: linear-gradient(135deg, #059669, #047857);
      color: #ffffff !important;
      border: none;
      padding: 10px 22px;
      border-radius: 9px;
      font-weight: 700;
      font-size: 13px;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      cursor: pointer;
      box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25);
      transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
      text-decoration: none;
    }

    .btn-exam-submit:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(5, 150, 105, 0.35);
      background: linear-gradient(135deg, #047857, #065f46);
    }

    /* Question Cards & Body */
    .exam-content-area {
      max-width: 1400px;
      width: 100%;
      margin: 0 auto;
      padding: 24px 16px 60px 16px;
      flex: 1;
    }

    .exam-card {
      background: #ffffff;
      border: 1px solid var(--border-color);
      border-radius: 14px;
      box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
    }

    /* Modal Styling */
    .exam-modal-overlay {
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, 0.7);
      backdrop-filter: blur(4px);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 9999;
      padding: 16px;
    }

    .exam-modal-box {
      background: #ffffff;
      border-radius: 18px;
      max-width: 480px;
      width: 100%;
      padding: 32px 28px;
      text-align: center;
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
      animation: modalPop 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes modalPop {
      0% { transform: scale(0.9); opacity: 0; }
      100% { transform: scale(1); opacity: 1; }
    }

    @media (max-width: 768px) {
      .exam-topbar {
        padding: 8px 12px !important;
      }
      .exam-topbar-inner {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        gap: 6px !important;
        width: 100% !important;
      }
      .hide-mobile {
        display: none !important;
      }
      .exam-brand-area {
        gap: 6px !important;
        flex: 0 1 auto !important;
        min-width: 0 !important;
      }
      .exam-brand-area img {
        height: 28px !important;
      }
      .exam-brand-title {
        font-size: 11.5px !important;
        max-width: 110px !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
      }
      .exam-timer-card {
        padding: 3px 8px !important;
        gap: 6px !important;
        border-radius: 8px !important;
        flex-shrink: 0 !important;
      }
      .exam-timer-card i {
        font-size: 15px !important;
      }
      .timer-label {
        display: none !important;
      }
      .timer-digits {
        font-size: 15px !important;
        letter-spacing: 0.5px !important;
      }
      .btn-exam-submit {
        padding: 7px 11px !important;
        font-size: 11px !important;
        white-space: nowrap !important;
        border-radius: 7px !important;
        flex-shrink: 0 !important;
        gap: 4px !important;
      }
      .exam-content-area {
        padding: 12px 10px 40px 10px !important;
      }
    }
  </style>

  @yield('styles')
</head>
<body>

  <!-- Clean Distraction-Free Header (NO SIDEBAR!) -->
  <header class="exam-topbar">
    <div class="exam-topbar-inner">
      
      <!-- Brand & Exam Title -->
      <div class="exam-brand-area">
        <a href="{{ route('online_tests') }}" style="display: flex; align-items: center; gap: 10px; text-decoration: none;" title="Back to Online Tests">
          @if(cms('site_logo'))
            <img src="{{ asset(cms('site_logo')) }}" alt="IDA" style="height: 38px; width: auto; object-fit: contain; border-radius: 6px;">
          @else
            <div style="width: 38px; height: 38px; background: #059669; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 18px;">
              <i class="fa-solid fa-shield-halved"></i>
            </div>
          @endif
          <div>
            <div class="exam-brand-title">{{ cms('site_name', 'IMPERIAL DEFENCE ACADEMY') }}</div>
          </div>
        </a>

        <div style="height: 28px; width: 1px; background: var(--border-color); margin: 0 4px;"></div>

        <!-- Exam Subject Tag -->
        <div style="display: flex; align-items: center; gap: 8px;">
          <span class="badge" style="background: {{ $exam->branchColor() ?? '#059669' }}; color: #ffffff; font-size: 11px; padding: 4px 10px; border-radius: 6px; font-weight: 700;">
            <i class="fa-solid {{ $exam->branchIcon() ?? 'fa-award' }}"></i> {{ strtoupper($exam->branch ?? 'TEST') }}
          </span>
          <span style="font-size: 14px; font-weight: 700; color: #0f172a; max-width: 320px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
            {{ $exam->title }}
          </span>
        </div>
      </div>

      <!-- Live Center Countdown Timer -->
      <div class="exam-timer-card" id="examTimerCard">
        <i class="fa-solid fa-stopwatch" id="timerIcon" style="color: #059669; font-size: 22px;"></i>
        <div>
          <div class="timer-label">Time Remaining</div>
          <div id="countdownTimer" class="timer-digits">
            @php
              $initialSec = isset($remainingSeconds) ? (int) floor($remainingSeconds) : ((int) ($exam->duration_minutes ?? 30) * 60);
              $initHours = (int) floor($initialSec / 3600);
              $initMins = (int) floor(($initialSec % 3600) / 60);
              $initSecs = (int) ($initialSec % 60);
            @endphp
            @if($initHours > 0)
              {{ sprintf('%02d:%02d:%02d', $initHours, $initMins, $initSecs) }}
            @else
              {{ sprintf('%02d:%02d', $initMins, $initSecs) }}
            @endif
          </div>
        </div>
      </div>

      <!-- Candidate Avatar & Finish Button -->
      <div style="display: flex; align-items: center; gap: 14px;">
        <div style="text-align: right;" class="hide-mobile">
          <div style="font-size: 13px; font-weight: 700; color: #0f172a;">{{ auth()->user()->name ?? 'Candidate' }}</div>
          <div style="font-size: 11px; color: var(--text-muted);">Attempt #{{ $attempt->attempt_number ?? 1 }}</div>
        </div>

        <button type="button" class="btn-exam-submit" onclick="confirmFinishExam()">
          <i class="fa-solid fa-flag-checkered"></i> Finish & Submit
        </button>
      </div>

    </div>
  </header>

  <!-- Main Content Exam Room -->
  <main class="exam-content-area">
    @if(session('error'))
      <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 14px 20px; border-radius: 10px; margin-bottom: 20px; font-size: 13.5px; font-weight: 500; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-circle-exclamation" style="font-size: 16px;"></i>
        {{ session('error') }}
      </div>
    @endif

    @yield('content')
  </main>

  <!-- Submit Confirmation Modal -->
  <div id="submitModal" class="exam-modal-overlay">
    <div class="exam-modal-box">
      <div style="width: 60px; height: 60px; background: #ecfdf5; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; color: #059669; font-size: 26px; margin-bottom: 16px;">
        <i class="fa-solid fa-clipboard-check"></i>
      </div>

      <h3 style="font-size: 20px; font-weight: 800; color: #0f172a; margin-bottom: 8px;">Finish Examination?</h3>
      <p style="font-size: 13.5px; color: #64748b; line-height: 1.5; margin-bottom: 18px;">
        Are you sure you want to finish and submit your exam?
      </p>

      <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px; margin-bottom: 24px; display: flex; justify-content: space-around;">
        <div>
          <div style="font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: 700;">Total Questions</div>
          <strong style="font-size: 18px; color: #0f172a;" id="modalTotalCount">{{ $exam->questions ? $exam->questions->count() : ($exam->watWords ? $exam->watWords->count() : 0) }}</strong>
        </div>
        <div style="border-left: 1px solid #e2e8f0;"></div>
        <div>
          <div style="font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: 700;">Answered</div>
          <strong style="font-size: 18px; color: #059669;" id="modalAnsweredCount">0</strong>
        </div>
      </div>

      <div style="display: flex; gap: 12px; justify-content: center;">
        <button type="button" onclick="closeSubmitModal()" style="background: #f1f5f9; border: 1px solid #cbd5e1; color: #475569; padding: 10px 20px; border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer;">
          Continue Test
        </button>
        <button type="button" onclick="executeSubmit()" style="background: #059669; border: none; color: #ffffff; padding: 10px 24px; border-radius: 8px; font-weight: 700; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
          <i class="fa-solid fa-check"></i> Yes, Submit Now
        </button>
      </div>
    </div>
  </div>

  @yield('scripts')
</body>
</html>
