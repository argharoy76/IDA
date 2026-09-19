@extends('layouts.portal')

@section('title', 'Cadet Dashboard | Imperial Defence Academy')
@section('page_title', 'Cadet Dashboard')

@section('content')
<style>
  /* -------------------------------------------------------------
     Cadet Dashboard Layout Styles (Ultra-Clean & Neat)
     ------------------------------------------------------------- */
  .cadet-dash-wrapper {
    display: flex;
    flex-direction: column;
    gap: 20px;
    width: 100%;
  }

  /* Large White Container for the 6 Cards */
  .cadet-cards-container {
    background: #ffffff;
    border-radius: 24px;
    padding: 24px;
    box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05), 0 1px 3px rgba(15, 23, 42, 0.03);
    border: 1px solid #f1f5f9;
  }

  /* 2 Columns Responsive Grid for Active Cards */
  .cadet-tiles-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
  }
  @media (max-width: 768px) {
    .cadet-tiles-grid {
      grid-template-columns: 1fr;
    }
  }

  /* Individual Vibrant 3D Card */
  .cadet-tile-card {
    position: relative;
    border-radius: 24px;
    padding: 24px 22px 22px 24px;
    min-height: 168px;
    height: 172px;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    align-items: flex-start;
    overflow: hidden;
    color: #ffffff !important;
    text-decoration: none !important;
    cursor: pointer;
    box-shadow: 
      0 16px 32px -8px rgba(0, 0, 0, 0.2),
      0 6px 14px -3px rgba(0, 0, 0, 0.1),
      inset 0 2px 2px rgba(255, 255, 255, 0.52),
      inset 0 -3.5px 0 rgba(0, 0, 0, 0.18);
    border: 1.5px solid rgba(255, 255, 255, 0.32);
    transition: transform 0.28s cubic-bezier(0.2, 0.8, 0.2, 1), box-shadow 0.28s cubic-bezier(0.2, 0.8, 0.2, 1), filter 0.28s ease;
  }
  .cadet-tile-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 
      0 26px 48px -10px rgba(0, 0, 0, 0.28),
      0 12px 20px -5px rgba(0, 0, 0, 0.14),
      inset 0 2.5px 2.5px rgba(255, 255, 255, 0.65),
      inset 0 -3.5px 0 rgba(0, 0, 0, 0.22);
    filter: brightness(1.04);
    color: #ffffff !important;
  }
  .cadet-tile-card:active {
    transform: translateY(-2px) scale(0.995);
    box-shadow: 
      0 8px 18px -4px rgba(0, 0, 0, 0.14),
      inset 0 1px 1px rgba(255, 255, 255, 0.35),
      inset 0 -1.5px 0 rgba(0, 0, 0, 0.12);
  }

  /* Luminous Ambient Spotlight behind 3D Graphic */
  .cadet-tile-glow {
    position: absolute;
    top: -30px;
    right: -30px;
    width: 175px;
    height: 175px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.35) 0%, rgba(255, 255, 255, 0.1) 50%, rgba(255, 255, 255, 0) 72%);
    pointer-events: none;
    transition: transform 0.35s ease, opacity 0.35s ease;
  }
  .cadet-tile-card:hover .cadet-tile-glow {
    transform: scale(1.18);
    opacity: 1;
  }

  /* 3D Graphical Illustration Container */
  .cadet-tile-graphic {
    position: absolute;
    top: 12px;
    right: 16px;
    width: 98px;
    height: 98px;
    display: flex;
    align-items: center;
    justify-content: center;
    pointer-events: none;
    filter: drop-shadow(0 10px 14px rgba(0, 0, 0, 0.22));
    transition: transform 0.32s cubic-bezier(0.34, 1.56, 0.64, 1), filter 0.32s ease;
  }
  .cadet-tile-card:hover .cadet-tile-graphic {
    transform: scale(1.12) translateY(-5px) rotate(2.5deg);
    filter: drop-shadow(0 16px 24px rgba(0, 0, 0, 0.3));
  }

  /* Clean Single-Word Title at Bottom-Left */
  .cadet-tile-title,
  h3.cadet-tile-title {
    position: relative;
    z-index: 2;
    font-size: 21px !important;
    font-weight: 800 !important;
    color: #ffffff !important;
    letter-spacing: -0.02em;
    line-height: 1.2;
    text-shadow: 0 2px 6px rgba(0, 0, 0, 0.45) !important;
    margin: 0 !important;
    padding: 0 !important;
    pointer-events: none;
    user-select: none;
    max-width: 65%;
  }

  /* Specific 3D Card Gradients */
  .tile-coral {
    background: linear-gradient(135deg, #ff7348 0%, #ff5252 50%, #e12727 100%);
  }
  .tile-indigo {
    background: linear-gradient(135deg, #6370e8 0%, #4b58c7 50%, #3642a8 100%);
  }
  .tile-sky {
    background: linear-gradient(135deg, #38bdf8 0%, #0ea5e9 50%, #0284c7 100%);
  }
  .tile-purple {
    background: linear-gradient(135deg, #af52de 0%, #9333ea 50%, #7928ca 100%);
  }
  .tile-lavender {
    background: linear-gradient(135deg, #818cf8 0%, #6873e8 50%, #4f5bd5 100%);
  }
  .tile-lime {
    background: linear-gradient(135deg, #84cc16 0%, #70b40e 50%, #548e06 100%);
  }

  /* Tactical Under Construction Badge on Tile */
  .cadet-construction-ribbon {
    position: absolute;
    top: 14px;
    left: 16px;
    z-index: 4;
    display: inline-flex;
    align-items: center;
    gap: 5.5px;
    background: rgba(15, 23, 42, 0.45);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.32);
    color: #ffffff;
    padding: 4px 10px;
    border-radius: 9999px;
    font-size: 9.5px;
    font-weight: 800;
    letter-spacing: 0.4px;
    text-transform: uppercase;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    pointer-events: none;
    transition: transform 0.28s ease, background 0.28s ease;
  }
  .cadet-tile-card:hover .cadet-construction-ribbon {
    transform: translateY(-1px);
    background: rgba(15, 23, 42, 0.6);
  }
  .cadet-construction-ribbon i {
    color: #fde047;
    font-size: 10.5px;
  }

  /* Clean Modal Popup */
  .cadet-modal-backdrop {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(4px);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 16px;
  }
  .cadet-modal-dialog {
    background: #ffffff;
    border-radius: 18px;
    width: 100%;
    max-width: 540px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.25);
    overflow: hidden;
    border: 1px solid #e2e8f0;
    animation: modalPop 0.22s ease-out;
  }
  @keyframes modalPop {
    from { transform: scale(0.96); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
  }
  .cadet-modal-header {
    padding: 16px 20px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f8fafc;
  }
  .cadet-modal-header h4 {
    margin: 0;
    font-size: 15px;
    font-weight: 700;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .cadet-modal-close {
    background: none;
    border: none;
    font-size: 18px;
    color: #94a3b8;
    cursor: pointer;
    line-height: 1;
    padding: 4px;
    border-radius: 6px;
    transition: color 0.15s ease;
  }
  .cadet-modal-close:hover {
    color: #ef4444;
  }
  .cadet-modal-body {
    padding: 20px;
    max-height: 78vh;
    overflow-y: auto;
  }
  .cadet-form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
  }
  .cadet-form-group {
    display: flex;
    flex-direction: column;
    gap: 4px;
  }
  .cadet-form-group.full-width {
    grid-column: 1 / -1;
  }
  .cadet-form-group label {
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  .cadet-form-control {
    border: 1.5px solid #cbd5e1;
    border-radius: 8px;
    padding: 8px 12px;
    font-size: 12.5px;
    color: #0f172a;
    outline: none;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
  }
  .cadet-form-control:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
  }
  .cadet-modal-footer {
    padding: 12px 20px;
    border-top: 1px solid #f1f5f9;
    background: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
  }

  /* Question archive item */
  .archive-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 14px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    margin-bottom: 10px;
    transition: all 0.2s ease;
  }
  .archive-item:hover {
    border-color: #6366f1;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.08);
  }

  /* Responsive Breakpoints */
  @media (max-width: 1024px) {
    .cadet-tiles-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }
  @media (max-width: 640px) {
    .cadet-tiles-grid {
      grid-template-columns: 1fr;
    }
    .cadet-cards-container {
      padding: 16px;
      border-radius: 18px;
    }
    .cadet-form-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

<div class="cadet-dash-wrapper">

  <!-- Large White Container Housing the 6 Vibrant Cards -->
  <div class="cadet-cards-container">
    <div class="cadet-tiles-grid">

      <!-- Box 1 (Red / Coral): Exam -->
      <a href="{{ route('cadet.exams.index') }}" class="cadet-tile-card tile-coral" title="Exam">
        <div class="cadet-tile-glow"></div>
        <div class="cadet-tile-graphic">
          <svg viewBox="0 0 96 96" width="90" height="90" fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
              <filter id="shadow-c1" x="-20%" y="-20%" width="150%" height="150%">
                <feDropShadow dx="0" dy="5" stdDeviation="4.5" flood-color="#000000" flood-opacity="0.25"/>
              </filter>
              <linearGradient id="boardGrad1" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#475569"/>
                <stop offset="100%" stop-color="#1e293b"/>
              </linearGradient>
              <linearGradient id="paperGrad1" x1="0%" y1="0%" x2="0%" y2="100%">
                <stop offset="0%" stop-color="#ffffff"/>
                <stop offset="100%" stop-color="#f1f5f9"/>
              </linearGradient>
              <linearGradient id="clipGrad1" x1="0%" y1="0%" x2="100%" y2="0%">
                <stop offset="0%" stop-color="#cbd5e1"/>
                <stop offset="50%" stop-color="#ffffff"/>
                <stop offset="100%" stop-color="#94a3b8"/>
              </linearGradient>
              <linearGradient id="pencilBody" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#fbbf24"/>
                <stop offset="50%" stop-color="#f59e0b"/>
                <stop offset="100%" stop-color="#d97706"/>
              </linearGradient>
              <linearGradient id="checkCircle" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#34d399"/>
                <stop offset="100%" stop-color="#059669"/>
              </linearGradient>
            </defs>

            <!-- Board Shadow & Base -->
            <rect x="19" y="16" width="54" height="66" rx="9" fill="#0f172a" opacity="0.35"/>
            <rect x="18" y="14" width="54" height="66" rx="8" fill="#1e293b"/>
            <rect x="18" y="11" width="54" height="66" rx="8" fill="url(#boardGrad1)"/>
            <rect x="18" y="11" width="54" height="66" rx="8" stroke="rgba(255,255,255,0.2)" stroke-width="1" fill="none"/>

            <!-- Paper Sheet -->
            <rect x="24" y="20" width="42" height="52" rx="4" fill="#cbd5e1"/>
            <rect x="23" y="18" width="42" height="52" rx="4" fill="url(#paperGrad1)" filter="url(#shadow-c1)"/>

            <!-- Exam Lines -->
            <rect x="28" y="30" width="22" height="3.5" rx="1.75" fill="#e02424" opacity="0.8"/>
            <rect x="28" y="38" width="32" height="3" rx="1.5" fill="#94a3b8"/>
            <rect x="28" y="45" width="26" height="3" rx="1.5" fill="#cbd5e1"/>
            <rect x="28" y="52" width="29" height="3" rx="1.5" fill="#cbd5e1"/>
            <rect x="28" y="59" width="18" height="3" rx="1.5" fill="#cbd5e1"/>

            <!-- Metallic 3D Clip -->
            <path d="M36 8 h18 a3 3 0 0 1 3 3 v5 h-24 v-5 a3 3 0 0 1 3 -3 z" fill="#64748b"/>
            <path d="M36 6 h18 a3 3 0 0 1 3 3 v5 h-24 v-5 a3 3 0 0 1 3 -3 z" fill="url(#clipGrad1)"/>
            <rect x="42" y="10" width="6" height="3" rx="1.5" fill="#475569"/>

            <!-- 3D Check Badge -->
            <g transform="translate(52, 50)">
              <circle cx="12" cy="14" r="12" fill="#065f46" opacity="0.4"/>
              <circle cx="12" cy="12" r="12" fill="url(#checkCircle)" filter="url(#shadow-c1)"/>
              <circle cx="12" cy="12" r="12" stroke="rgba(255,255,255,0.5)" stroke-width="1.5" fill="none"/>
              <path d="M7 12 l3.5 3.5 l6.5 -7" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
            </g>

            <!-- 3D Floating Pencil -->
            <g transform="rotate(-38 72 38)">
              <rect x="52" y="40" width="34" height="7" rx="3.5" fill="rgba(0,0,0,0.22)" filter="url(#shadow-c1)"/>
              <polygon points="46,36 54,32 54,40" fill="#fde68a"/>
              <polygon points="46,36 49,34.5 49,37.5" fill="#1e293b"/>
              <rect x="54" y="32" width="24" height="8" fill="url(#pencilBody)"/>
              <rect x="54" y="32" width="24" height="2.5" fill="#fef08a" opacity="0.6"/>
              <rect x="54" y="37.5" width="24" height="2.5" fill="#b45309" opacity="0.4"/>
              <rect x="78" y="32" width="4" height="8" fill="#e2e8f0"/>
              <path d="M82 32 h3 a2.5 2.5 0 0 1 2.5 2.5 v3 a2.5 2.5 0 0 1 -2.5 2.5 h-3 z" fill="#f43f5e"/>
            </g>
          </svg>
        </div>
        <h3 class="cadet-tile-title">Exam</h3>
      </a>

      <!-- Box 2 (Blue / Indigo): Exam History -->
      <a href="{{ route('cadet.exams.history') }}" class="cadet-tile-card tile-indigo" title="Exam History">
        <div class="cadet-tile-glow"></div>
        <div class="cadet-tile-graphic">
          <svg viewBox="0 0 96 96" width="90" height="90" fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
              <filter id="shadow-c2" x="-20%" y="-20%" width="150%" height="150%">
                <feDropShadow dx="0" dy="5" stdDeviation="4.5" flood-color="#000000" flood-opacity="0.25"/>
              </filter>
              <linearGradient id="bar1Top" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#93c5fd"/><stop offset="100%" stop-color="#60a5fa"/></linearGradient>
              <linearGradient id="bar1Front" x1="0%" y1="0%" x2="0%" y2="100%"><stop offset="0%" stop-color="#3b82f6"/><stop offset="100%" stop-color="#1d4ed8"/></linearGradient>
              <linearGradient id="bar1Side" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" stop-color="#1e40af"/><stop offset="100%" stop-color="#172554"/></linearGradient>

              <linearGradient id="bar2Top" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#c084fc"/><stop offset="100%" stop-color="#a855f7"/></linearGradient>
              <linearGradient id="bar2Front" x1="0%" y1="0%" x2="0%" y2="100%"><stop offset="0%" stop-color="#9333ea"/><stop offset="100%" stop-color="#7e22ce"/></linearGradient>
              <linearGradient id="bar2Side" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" stop-color="#6b21a8"/><stop offset="100%" stop-color="#3b0764"/></linearGradient>

              <linearGradient id="bar3Top" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#fde047"/><stop offset="100%" stop-color="#eab308"/></linearGradient>
              <linearGradient id="bar3Front" x1="0%" y1="0%" x2="0%" y2="100%"><stop offset="0%" stop-color="#f59e0b"/><stop offset="100%" stop-color="#d97706"/></linearGradient>
              <linearGradient id="bar3Side" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" stop-color="#b45309"/><stop offset="100%" stop-color="#78350f"/></linearGradient>

              <linearGradient id="arrowGrad2" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#ff7a59"/><stop offset="100%" stop-color="#f43f5e"/></linearGradient>
              <linearGradient id="goldTrophy" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#fef08a"/><stop offset="50%" stop-color="#f59e0b"/><stop offset="100%" stop-color="#b45309"/></linearGradient>
            </defs>

            <!-- Isometric Base Plate -->
            <polygon points="12,70 48,88 84,70 48,52" fill="#1e1b4b" opacity="0.45"/>
            <polygon points="12,68 48,86 84,68 48,50" fill="#312e81"/>
            <polygon points="12,66 48,84 84,66 48,48" fill="#4338ca"/>

            <!-- Bar 1 (Left - Small) -->
            <polygon points="20,53 28,49 36,53 28,57" fill="url(#bar1Top)"/>
            <polygon points="20,53 28,57 28,73 20,69" fill="url(#bar1Front)"/>
            <polygon points="28,57 36,53 36,69 28,73" fill="url(#bar1Side)"/>

            <!-- Bar 2 (Center - Medium) -->
            <polygon points="38,41 46,37 54,41 46,45" fill="url(#bar2Top)"/>
            <polygon points="38,41 46,45 46,67 38,63" fill="url(#bar2Front)"/>
            <polygon points="46,45 54,41 54,63 46,67" fill="url(#bar2Side)"/>

            <!-- Bar 3 (Right - High) -->
            <polygon points="56,27 64,23 72,27 64,31" fill="url(#bar3Top)"/>
            <polygon points="56,27 64,31 64,61 56,57" fill="url(#bar3Front)"/>
            <polygon points="64,31 72,27 72,57 64,61" fill="url(#bar3Side)"/>

            <!-- 3D Surge Trend Arrow -->
            <path d="M22 47 Q44 38 68 18" stroke="url(#arrowGrad2)" stroke-width="5" stroke-linecap="round" fill="none" filter="url(#shadow-c2)"/>
            <polygon points="62,13 74,15 70,26" fill="url(#arrowGrad2)"/>

            <!-- 3D Floating Star Badge -->
            <g transform="translate(62, 10)">
              <circle cx="12" cy="12" r="11" fill="#78350f" opacity="0.35"/>
              <circle cx="12" cy="10" r="11" fill="url(#goldTrophy)" filter="url(#shadow-c2)"/>
              <circle cx="12" cy="10" r="11" stroke="rgba(255,255,255,0.6)" stroke-width="1.2" fill="none"/>
              <path d="M12 4 l1.8 3.8 l4.2 0.6 l-3 3 l0.7 4.2 l-3.7 -2 l-3.7 2 l0.7 -4.2 l-3 -3 l4.2 -0.6 z" fill="#ffffff"/>
            </g>
          </svg>
        </div>
        <h3 class="cadet-tile-title">Exam History</h3>
      </a>

      {{-- Hidden from Cadet Dashboard - Code preserved intact for future activation --}}
      @if(false)
      <!-- Box 3 (Cyan / Sky Blue): Routine -->
      <a href="{{ route('cadet.routine') }}" class="cadet-tile-card tile-sky" title="Routine">
        <div class="cadet-construction-ribbon">
          <i class="fa-solid fa-person-digging"></i> Under Construction
        </div>
        <div class="cadet-tile-glow"></div>
        <div class="cadet-tile-graphic">
          <svg viewBox="0 0 96 96" width="90" height="90" fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
              <filter id="shadow-c3" x="-20%" y="-20%" width="150%" height="150%">
                <feDropShadow dx="0" dy="5" stdDeviation="4.5" flood-color="#000000" flood-opacity="0.22"/>
              </filter>
              <linearGradient id="calBackGrad" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#0284c7"/><stop offset="100%" stop-color="#0369a1"/></linearGradient>
              <linearGradient id="calRedHeader" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" stop-color="#ef4444"/><stop offset="100%" stop-color="#dc2626"/></linearGradient>
              <linearGradient id="clockBevel" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#ffffff"/><stop offset="50%" stop-color="#e2e8f0"/><stop offset="100%" stop-color="#94a3b8"/></linearGradient>
            </defs>

            <!-- Calendar Base Depth -->
            <rect x="16" y="20" width="56" height="60" rx="10" fill="#075985" opacity="0.4"/>
            <rect x="15" y="18" width="56" height="60" rx="10" fill="url(#calBackGrad)"/>

            <!-- Calendar White Sheet -->
            <rect x="18" y="24" width="50" height="51" rx="6" fill="#ffffff" filter="url(#shadow-c3)"/>

            <!-- Header Red Banner -->
            <path d="M18 24 h50 v14 h-50 z" fill="url(#calRedHeader)"/>
            <rect x="18" y="24" width="50" height="2" fill="rgba(255,255,255,0.4)"/>

            <!-- Spiral Ring Binders -->
            <rect x="25" y="16" width="4" height="13" rx="2" fill="#e2e8f0"/>
            <rect x="25" y="16" width="2" height="13" rx="1" fill="#ffffff"/>
            <rect x="37" y="16" width="4" height="13" rx="2" fill="#e2e8f0"/>
            <rect x="37" y="16" width="2" height="13" rx="1" fill="#ffffff"/>
            <rect x="49" y="16" width="4" height="13" rx="2" fill="#e2e8f0"/>
            <rect x="49" y="16" width="2" height="13" rx="1" fill="#ffffff"/>
            <rect x="61" y="16" width="4" height="13" rx="2" fill="#e2e8f0"/>
            <rect x="61" y="16" width="2" height="13" rx="1" fill="#ffffff"/>

            <!-- Calendar Date Display -->
            <text x="43" y="60" font-family="system-ui, -apple-system, sans-serif" font-size="20" font-weight="900" fill="#0284c7" text-anchor="middle">24</text>
            <rect x="24" y="66" width="38" height="2.5" rx="1.25" fill="#e2e8f0"/>

            <!-- Floating 3D Analog Clock -->
            <g transform="translate(54, 46)">
              <circle cx="17" cy="19" r="17" fill="#0f172a" opacity="0.3" filter="url(#shadow-c3)"/>
              <circle cx="17" cy="17" r="17" fill="url(#clockBevel)"/>
              <circle cx="17" cy="17" r="13.5" fill="#0284c7"/>
              <circle cx="17" cy="17" r="2" fill="#ffffff"/>
              <line x1="17" y1="17" x2="17" y2="10" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round"/>
              <line x1="17" y1="17" x2="23" y2="17" stroke="#fde047" stroke-width="2" stroke-linecap="round"/>
              <path d="M7 14 a13.5 13.5 0 0 1 20 -7" stroke="rgba(255,255,255,0.6)" stroke-width="1.5" stroke-linecap="round" fill="none"/>
            </g>
          </svg>
        </div>
        <h3 class="cadet-tile-title">Routine</h3>
      </a>

      <!-- Box 4 (Purple / Violet): Payment -->
      <a href="{{ route('cadet.fees') }}" class="cadet-tile-card tile-purple" title="Payment">
        <div class="cadet-construction-ribbon">
          <i class="fa-solid fa-person-digging"></i> Under Construction
        </div>
        <div class="cadet-tile-glow"></div>
        <div class="cadet-tile-graphic">
          <svg viewBox="0 0 96 96" width="90" height="90" fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
              <filter id="shadow-c4" x="-20%" y="-20%" width="150%" height="150%">
                <feDropShadow dx="0" dy="6" stdDeviation="5" flood-color="#000000" flood-opacity="0.25"/>
              </filter>
              <linearGradient id="cardGrad4" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#7c3aed"/>
                <stop offset="50%" stop-color="#6366f1"/>
                <stop offset="100%" stop-color="#4338ca"/>
              </linearGradient>
              <linearGradient id="goldCoinGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#fef08a"/>
                <stop offset="45%" stop-color="#f59e0b"/>
                <stop offset="100%" stop-color="#b45309"/>
              </linearGradient>
              <linearGradient id="goldCoinSide" x1="0%" y1="0%" x2="100%" y2="0%">
                <stop offset="0%" stop-color="#d97706"/>
                <stop offset="100%" stop-color="#78350f"/>
              </linearGradient>
              <linearGradient id="chipGrad4" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#fef9c3"/>
                <stop offset="100%" stop-color="#eab308"/>
              </linearGradient>
            </defs>

            <!-- 3D Angled Credit Card -->
            <g transform="rotate(-10 42 42)">
              <rect x="6" y="16" width="62" height="40" rx="7" fill="#1e1b4b" opacity="0.32" filter="url(#shadow-c4)"/>
              <rect x="6" y="13" width="62" height="40" rx="7" fill="#312e81"/>
              <rect x="6" y="10" width="62" height="40" rx="7" fill="url(#cardGrad4)"/>
              <rect x="6" y="10" width="62" height="40" rx="7" stroke="rgba(255,255,255,0.35)" stroke-width="1.2" fill="none"/>
              <rect x="13" y="19" width="11" height="8.5" rx="2" fill="url(#chipGrad4)"/>
              <rect x="13" y="19" width="11" height="8.5" rx="2" stroke="#b45309" stroke-width="0.6" fill="none"/>
              <line x1="18.5" y1="19" x2="18.5" y2="27.5" stroke="#b45309" stroke-width="0.6"/>
              <line x1="13" y1="23.25" x2="24" y2="23.25" stroke="#b45309" stroke-width="0.6"/>
              <path d="M54 18 a7 7 0 0 1 0 10" stroke="rgba(255,255,255,0.75)" stroke-width="1.5" stroke-linecap="round" fill="none"/>
              <path d="M58 15 a12 12 0 0 1 0 16" stroke="rgba(255,255,255,0.5)" stroke-width="1.5" stroke-linecap="round" fill="none"/>
              <rect x="13" y="34" width="34" height="3" rx="1.5" fill="rgba(255,255,255,0.45)"/>
              <rect x="13" y="41" width="18" height="2.5" rx="1.25" fill="rgba(255,255,255,0.3)"/>
            </g>

            <!-- Floating 3D Gold Coin 1 (Base) -->
            <g transform="translate(44, 46)">
              <ellipse cx="22" cy="26" rx="20" ry="7.5" fill="#0f172a" opacity="0.38"/>
              <path d="M2 18 v6 c0 6 9 10.5 20 10.5 s20 -4.5 20 -10.5 v-6 z" fill="url(#goldCoinSide)"/>
              <ellipse cx="22" cy="18" rx="20" ry="10" fill="url(#goldCoinGrad)" stroke="#fef08a" stroke-width="1.2"/>
              <ellipse cx="22" cy="18" rx="16" ry="7.5" stroke="#b45309" stroke-width="1.2" stroke-dasharray="2.5 2" fill="none"/>
              <text x="22" y="23" font-family="system-ui, sans-serif" font-size="13" font-weight="900" fill="#78350f" text-anchor="middle">৳</text>
            </g>

            <!-- Floating 3D Gold Coin 2 (Hovering) -->
            <g transform="translate(56, 20)">
              <path d="M1 16 v5 c0 5 8 9 17 9 s17 -4 17 -9 v-5 z" fill="url(#goldCoinSide)"/>
              <ellipse cx="18" cy="16" rx="17" ry="8.5" fill="url(#goldCoinGrad)" stroke="#fef08a" stroke-width="1.2"/>
              <ellipse cx="18" cy="16" rx="13.5" ry="6.5" stroke="#b45309" stroke-width="1" fill="none"/>
              <text x="18" y="21" font-family="system-ui, sans-serif" font-size="12" font-weight="900" fill="#78350f" text-anchor="middle">৳</text>
            </g>
          </svg>
        </div>
        <h3 class="cadet-tile-title">Payment</h3>
      </a>

      <!-- Box 5 (Lavender / Periwinkle): Previous Year Question -->
      <a href="javascript:void(0)" onclick="openPreviousQuestionsModal()" class="cadet-tile-card tile-lavender" title="Previous Year Question">
        <div class="cadet-construction-ribbon">
          <i class="fa-solid fa-person-digging"></i> Under Construction
        </div>
        <div class="cadet-tile-glow"></div>
        <div class="cadet-tile-graphic">
          <svg viewBox="0 0 96 96" width="90" height="90" fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
              <filter id="shadow-c5" x="-20%" y="-20%" width="150%" height="150%">
                <feDropShadow dx="0" dy="6" stdDeviation="5" flood-color="#000000" flood-opacity="0.25"/>
              </filter>
              <linearGradient id="pageGradLeft" x1="0%" y1="0%" x2="100%" y2="0%">
                <stop offset="0%" stop-color="#e2e8f0"/>
                <stop offset="100%" stop-color="#ffffff"/>
              </linearGradient>
              <linearGradient id="pageGradRight" x1="0%" y1="0%" x2="100%" y2="0%">
                <stop offset="0%" stop-color="#ffffff"/>
                <stop offset="100%" stop-color="#e2e8f0"/>
              </linearGradient>
              <linearGradient id="qBadgeGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#f59e0b"/>
                <stop offset="50%" stop-color="#ea580c"/>
                <stop offset="100%" stop-color="#c2410c"/>
              </linearGradient>
            </defs>

            <!-- Hardcover Base & Shadow -->
            <path d="M12 56 Q48 68 84 56 L84 66 Q48 78 12 66 Z" fill="#1e1b4b" opacity="0.4"/>
            <path d="M10 52 Q48 64 86 52 L86 60 Q48 72 10 60 Z" fill="#1e3a8a"/>

            <!-- Open Pages Stack -->
            <path d="M12 48 Q48 58 48 58 L48 30 Q48 30 12 22 Z" fill="url(#pageGradLeft)" filter="url(#shadow-c5)"/>
            <path d="M84 48 Q48 58 48 58 L48 30 Q48 30 84 22 Z" fill="url(#pageGradRight)" filter="url(#shadow-c5)"/>

            <!-- Center Spine Seam -->
            <path d="M47 30 L47 58 L49 58 L49 30 Z" fill="#94a3b8" opacity="0.6"/>

            <!-- Printed Text Lines on Left Page -->
            <line x1="18" y1="28" x2="42" y2="34" stroke="#94a3b8" stroke-width="2" stroke-linecap="round"/>
            <line x1="18" y1="35" x2="38" y2="40" stroke="#cbd5e1" stroke-width="2" stroke-linecap="round"/>
            <line x1="18" y1="42" x2="42" y2="47" stroke="#cbd5e1" stroke-width="2" stroke-linecap="round"/>

            <!-- Printed Text Lines on Right Page -->
            <line x1="54" y1="34" x2="78" y2="28" stroke="#94a3b8" stroke-width="2" stroke-linecap="round"/>
            <line x1="54" y1="40" x2="74" y2="35" stroke="#cbd5e1" stroke-width="2" stroke-linecap="round"/>
            <line x1="54" y1="47" x2="78" y2="42" stroke="#cbd5e1" stroke-width="2" stroke-linecap="round"/>

            <!-- Red Silk Ribbon Bookmark -->
            <path d="M48 58 Q48 72 44 78 L49 75 L54 78 Q50 72 48 58 Z" fill="#ef4444"/>

            <!-- Floating 3D Question Mark Badge -->
            <g transform="translate(34, 4)">
              <circle cx="14" cy="18" r="14" fill="#0f172a" opacity="0.35" filter="url(#shadow-c5)"/>
              <circle cx="14" cy="14" r="14" fill="url(#qBadgeGrad)"/>
              <circle cx="14" cy="14" r="14" stroke="rgba(255,255,255,0.6)" stroke-width="1.5" fill="none"/>
              <text x="14" y="21" font-family="system-ui, -apple-system, sans-serif" font-size="20" font-weight="900" fill="#ffffff" text-anchor="middle" filter="url(#shadow-c5)">?</text>
              <path d="M5 11 a10 10 0 0 1 12 -7" stroke="rgba(255,255,255,0.7)" stroke-width="1.5" stroke-linecap="round" fill="none"/>
            </g>
          </svg>
        </div>
        <h3 class="cadet-tile-title">Previous Year Question</h3>
      </a>

      <!-- Box 6 (Lime Green): Other Courses -->
      <a href="{{ route('courses') }}" target="_blank" class="cadet-tile-card tile-lime" title="Other Courses">
        <div class="cadet-construction-ribbon">
          <i class="fa-solid fa-person-digging"></i> Under Construction
        </div>
        <div class="cadet-tile-glow"></div>
        <div class="cadet-tile-graphic">
          <svg viewBox="0 0 96 96" width="90" height="90" fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
              <filter id="shadow-c6" x="-20%" y="-20%" width="150%" height="150%">
                <feDropShadow dx="0" dy="6" stdDeviation="5" flood-color="#000000" flood-opacity="0.25"/>
              </filter>
              <linearGradient id="capTopGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#334155"/>
                <stop offset="50%" stop-color="#1e293b"/>
                <stop offset="100%" stop-color="#0f172a"/>
              </linearGradient>
              <linearGradient id="capBaseGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                <stop offset="0%" stop-color="#1e293b"/>
                <stop offset="100%" stop-color="#020617"/>
              </linearGradient>
              <linearGradient id="tasselGold" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#fde047"/>
                <stop offset="50%" stop-color="#eab308"/>
                <stop offset="100%" stop-color="#ca8a04"/>
              </linearGradient>
              <linearGradient id="scrollGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#ffffff"/>
                <stop offset="50%" stop-color="#f8fafc"/>
                <stop offset="100%" stop-color="#e2e8f0"/>
              </linearGradient>
            </defs>

            <!-- Skull Cap Base Underneath -->
            <path d="M28 42 Q48 58 68 42 L68 52 Q48 68 28 52 Z" fill="url(#capBaseGrad)"/>

            <!-- Mortarboard Diamond Top Face & Shadow -->
            <polygon points="10,33 48,51 86,33 48,15" fill="#020617" opacity="0.4" filter="url(#shadow-c6)"/>
            <polygon points="10,31 48,49 86,31 48,13" fill="#0f172a"/>
            <polygon points="10,29 48,47 86,29 48,11" fill="url(#capTopGrad)"/>
            <polygon points="10,29 48,47 86,29 48,11" stroke="rgba(255,255,255,0.2)" stroke-width="1" fill="none"/>

            <!-- Center Button & Golden Tassel -->
            <ellipse cx="48" cy="29" rx="3.5" ry="2" fill="url(#tasselGold)"/>
            <path d="M48 29 Q62 33 66 45" stroke="url(#tasselGold)" stroke-width="2.5" stroke-linecap="round" fill="none"/>
            <polygon points="64,45 68,45 69,57 63,57" fill="url(#tasselGold)"/>
            <circle cx="66" cy="45" r="2.5" fill="#ca8a04"/>

            <!-- 3D Rolled Diploma Scroll Tied With Red Ribbon -->
            <g transform="translate(18, 54)">
              <rect x="2" y="8" width="52" height="18" rx="6" fill="#0f172a" opacity="0.3" filter="url(#shadow-c6)"/>
              <rect x="0" y="4" width="52" height="18" rx="5" fill="url(#scrollGrad)"/>
              <rect x="0" y="4" width="52" height="18" rx="5" stroke="#cbd5e1" stroke-width="1" fill="none"/>
              <ellipse cx="4" cy="13" rx="3" ry="8" fill="#e2e8f0"/>
              <ellipse cx="4" cy="13" rx="1.5" ry="4" fill="#94a3b8"/>
              <ellipse cx="48" cy="13" rx="3" ry="8" fill="#cbd5e1"/>
              <rect x="24" y="4" width="6" height="18" fill="#ef4444"/>
              <circle cx="27" cy="13" r="3.5" fill="#dc2626"/>
              <path d="M26 15 L22 23 L25 21 L28 23 Z" fill="#dc2626"/>
            </g>
          </svg>
        </div>
        <h3 class="cadet-tile-title">Other Courses</h3>
      </a>
      @endif

    </div>
  </div>

</div>
@endsection
