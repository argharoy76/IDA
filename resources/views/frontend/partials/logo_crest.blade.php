@php
  $size = $size ?? 46;
  $uid = 'crest_' . ($size) . '_' . substr(md5(microtime() . rand()), 0, 6);
  $customLogo = cms('site_logo') ?: cms('site_crest');
@endphp

@if($customLogo && (str_starts_with($customLogo, 'http') || file_exists(public_path(ltrim($customLogo, '/')))))
  <div class="brand-crest-3d-wrap" style="height: {{ $size }}px; display: inline-flex; align-items: center; justify-content: center;">
    <img src="{{ asset($customLogo) }}" alt="{{ cms('site_name', 'Imperial Defence Academy') }}" style="max-height: {{ $size }}px; width: auto; max-width: 100%; object-fit: contain; filter: drop-shadow(0 4px 12px rgba(0,0,0,0.5));">
  </div>
@else
  <!-- 3D Sovereign Military Cadet Shield Crest (Gold, Emerald & Tri-Service Insignia) -->
  <div class="brand-crest-3d-wrap" style="width: {{ $size }}px; height: {{ $size }}px;">
    <svg class="brand-crest-3d-svg" viewBox="0 0 100 100" width="{{ $size }}" height="{{ $size }}" fill="none" xmlns="http://www.w3.org/2000/svg">
      <defs>
        <!-- 3D Shadow Layer -->
        <filter id="crest3dShadow_{{ $uid }}" x="-30%" y="-30%" width="160%" height="170%" filterUnits="userSpaceOnUse">
          <feDropShadow dx="0" dy="5" stdDeviation="4.5" flood-color="#000000" flood-opacity="0.4"/>
          <feDropShadow dx="0" dy="2" stdDeviation="2" flood-color="#85c9cc" flood-opacity="0.35"/>
        </filter>
        <!-- Metallic 3D Gold Gradient -->
        <linearGradient id="goldMetallic_{{ $uid }}" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="#fffbeb"/>
          <stop offset="20%" stop-color="#fef08a"/>
          <stop offset="45%" stop-color="#f59e0b"/>
          <stop offset="70%" stop-color="#d97706"/>
          <stop offset="90%" stop-color="#b45309"/>
          <stop offset="100%" stop-color="#78350f"/>
        </linearGradient>
        <!-- Deep Gold Highlight for Inner Elements -->
        <linearGradient id="goldEmboss_{{ $uid }}" x1="0%" y1="0%" x2="0%" y2="100%">
          <stop offset="0%" stop-color="#ffffff"/>
          <stop offset="25%" stop-color="#fef08a"/>
          <stop offset="60%" stop-color="#f59e0b"/>
          <stop offset="100%" stop-color="#92400e"/>
        </linearGradient>
        <!-- Imperial 3D Teal Enamel Core (#85c9cc) -->
        <radialGradient id="emeraldRadial_{{ $uid }}" cx="50%" cy="35%" r="65%">
          <stop offset="0%" stop-color="#b6e7e9"/>
          <stop offset="35%" stop-color="#85c9cc"/>
          <stop offset="70%" stop-color="#5faab0"/>
          <stop offset="100%" stop-color="#082d2f"/>
        </radialGradient>
        <!-- Specular Glass Curve Highlight -->
        <linearGradient id="glassShine_{{ $uid }}" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="#ffffff" stop-opacity="0.65"/>
          <stop offset="45%" stop-color="#ffffff" stop-opacity="0.12"/>
          <stop offset="50%" stop-color="#ffffff" stop-opacity="0"/>
        </linearGradient>
      </defs>

      <!-- Layer 1: Outer 3D Shield Base with Drop Shadow -->
      <path d="M50 4 L90 18 C90 58 70 85 50 96 C30 85 10 58 10 18 Z" fill="url(#goldMetallic_{{ $uid }})" filter="url(#crest3dShadow_{{ $uid }})"/>

      <!-- Layer 2: Beveled Dark Gold Rim Edge for 3D Inset -->
      <path d="M50 8 L85 20 C85 55 67 80 50 90 C33 80 15 55 15 20 Z" fill="#78350f" opacity="0.65"/>

      <!-- Layer 3: Imperial Deep Emerald Enamel Center -->
      <path d="M50 10 L82 22 C82 53 65 77 50 87 C35 77 18 53 18 22 Z" fill="url(#emeraldRadial_{{ $uid }})"/>

      <!-- Layer 4: Tri-Service Golden Crossed Swords (Army, Navy, Air Force) -->
      <g stroke="url(#goldEmboss_{{ $uid }})" stroke-linecap="round">
        <path d="M30 35 L70 67" stroke-width="3"/>
        <path d="M70 35 L30 67" stroke-width="3"/>
        <!-- Sword Hilts -->
        <path d="M28 33 L32 37" stroke-width="4"/>
        <path d="M68 33 L72 37" stroke-width="4"/>
        <!-- Sword Pommels -->
        <circle cx="26" cy="31" r="2" fill="url(#goldEmboss_{{ $uid }})" stroke="none"/>
        <circle cx="74" cy="31" r="2" fill="url(#goldEmboss_{{ $uid }})" stroke="none"/>
      </g>

      <!-- Layer 5: Eagle Wings / Anchor Flourish Arc -->
      <path d="M26 48 Q50 62 74 48" stroke="url(#goldEmboss_{{ $uid }})" stroke-width="2.5" fill="none" stroke-linecap="round"/>
      <path d="M29 53 Q50 66 71 53" stroke="url(#goldEmboss_{{ $uid }})" stroke-width="1.8" fill="none" opacity="0.8"/>

      <!-- Layer 6: Central Golden Shield Heart -->
      <path d="M50 36 L64 43 C64 59 56 69 50 74 C44 69 36 59 36 43 Z" fill="url(#goldMetallic_{{ $uid }})"/>
      <path d="M50 39 L61 45 C61 57 54 65 50 70 C46 65 39 57 39 45 Z" fill="url(#emeraldRadial_{{ $uid }})"/>

      <!-- Layer 7: Central 3D Embossed Star -->
      <polygon points="50,42 52.5,47.5 58,48 54,52 55.5,57.5 50,54.5 44.5,57.5 46,52 42,48 47.5,47.5" fill="url(#goldEmboss_{{ $uid }})"/>

      <!-- Layer 8: Top Command 5-Point Star -->
      <polygon points="50,15 52.5,21.5 59,22 54,26.5 55.5,33 50,29.5 44.5,33 46,26.5 41,22 47.5,21.5" fill="url(#goldEmboss_{{ $uid }})"/>

      <!-- Layer 9: Two Flanking Micro Stars -->
      <polygon points="34,26 35.5,30 39.5,30.5 36.5,33 37.5,37 34,35 30.5,37 31.5,33 28.5,30.5 32.5,30" fill="url(#goldEmboss_{{ $uid }})" opacity="0.9"/>
      <polygon points="66,26 67.5,30 71.5,30.5 68.5,33 69.5,37 66,35 62.5,37 63.5,33 60.5,30.5 64.5,30" fill="url(#goldEmboss_{{ $uid }})" opacity="0.9"/>

      <!-- Layer 10: 3D Curved Specular Glass Reflection Glint -->
      <path d="M50 10 L82 22 C82 38 68 56 50 56 C32 56 24 38 18 22 Z" fill="url(#glassShine_{{ $uid }})"/>
    </svg>
  </div>
@endif
