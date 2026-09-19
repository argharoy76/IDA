@extends('layouts.public')

@section('title', 'Imperial Defence Academy | Khulna')

@section('content')
<style>
  .hero-animate-in {
    animation: heroFadeIn 0.7s cubic-bezier(0.16, 1, 0.3, 1) both;
  }
  @keyframes heroFadeIn {
    from {
      opacity: 0;
      transform: translateY(12px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
</style>
<!-- Hero Section (Atmospheric Dark Command with Ken Burns background slider) -->
<section class="hero-section relative min-h-[640px] md:min-h-[740px] flex items-center justify-center overflow-hidden border-b border-navy-border" style="position: relative; overflow: hidden; padding: 80px 24px 90px; background-color: #050b14;">
  
  <!-- Dynamic Multi-Image Sliding & Zooming Background Layer from Reference Website -->
  @php
    $heroSlides = cms_hero_slides();
  @endphp
  <div id="hero-slider" class="ken-burns" style="position: absolute; inset: 0; z-index: 0; pointer-events: none;">
    @foreach($heroSlides as $idx => $slide)
      @php
        $slideUrl = str_starts_with($slide, 'http') ? $slide : asset($slide);
      @endphp
      <div class="hero-slide" style="position: absolute; inset: 0; background-image: url('{{ $slideUrl }}'); background-size: cover; background-position: center; opacity: {{ $idx === 0 ? '1' : '0' }}; transition: opacity 1s ease-in-out;"></div>
    @endforeach
  </div>

  <!-- High-contrast Atmospheric Emerald Military Overlays (Vibrant & Photo-preserving) -->
  <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(5, 11, 20, 0.40) 0%, rgba(2, 44, 34, 0.28) 50%, rgba(5, 11, 20, 0.78) 100%); z-index: 1; pointer-events: none;"></div>
  <div style="position: absolute; inset: 0; background: radial-gradient(ellipse at center, rgba(5, 11, 20, 0.05) 0%, rgba(5, 11, 20, 0.65) 90%); z-index: 1; pointer-events: none;"></div>

  <!-- Subtle Ambient Glow Orbs -->
  <div style="position: absolute; top: -100px; left: 10%; width: 450px; height: 450px; background: radial-gradient(circle, rgba(16, 185, 129, 0.22) 0%, transparent 70%); filter: blur(50px); pointer-events: none; z-index: 2;"></div>
  <div style="position: absolute; bottom: -80px; right: 15%; width: 400px; height: 400px; background: radial-gradient(circle, rgba(217, 119, 6, 0.15) 0%, transparent 70%); filter: blur(60px); pointer-events: none; z-index: 2;"></div>

  <div style="position: relative; z-index: 20; max-width: 1240px; margin: 0 auto;" class="hero-grid-responsive">
    <div class="hero-animate-in">
      
      <!-- 1. Drop-down Animated Badge -->
      @php
        $heroBadge = cms('hero_badge', '★ PREMIER OFFICER CADET ACADEMY • KHULNA ★');
      @endphp
      @if(filled($heroBadge))
      <div style="display: inline-flex; align-items: center; gap: 8px; margin-bottom: 22px;">
        <span class="animate-gold-glow" style="padding: 6px 16px; border: 1px solid rgba(212, 175, 55, 0.6); color: #f3e5ab; background: rgba(212, 175, 55, 0.12); font-size: 11.5px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; border-radius: 4px;">
          {{ $heroBadge }}
        </span>
      </div>
      @endif

      <!-- 2. Dynamic Animated Headline with Word Rotator -->
      @php
        $heroPrefix = cms('hero_title_prefix', 'Prepare for Commission in');
        $heroRotator = cms('hero_rotating_words', 'Bangladesh Army, Bangladesh Navy, Bangladesh Air Force, ISSB Screening Board');
        $heroSuffix = cms('hero_title_suffix', 'Officer Cadet Preparatory Wing');
        $rotatingWords = array_values(array_filter(array_map('trim', explode(',', $heroRotator))));
      @endphp
      @if(filled($heroPrefix) || filled($heroSuffix) || !empty($rotatingWords))
      <h1 class="hero-headline" style="font-size: 44px; font-weight: 900; line-height: 1.2; color: #ffffff; margin-bottom: 20px; font-family: 'Roboto', sans-serif; letter-spacing: -0.02em;">
        @if(filled($heroPrefix))
          {{ $heroPrefix }}
          @if(!empty($rotatingWords)) <br> @endif
        @endif
        @if(!empty($rotatingWords))
          <span id="typed-text" class="text-gold" style="color: var(--accent-gold); display: inline-block; transition: all 0.3s ease; text-shadow: 0 0 20px rgba(212, 175, 55, 0.4);">
            {{ $rotatingWords[0] }}
          </span>
        @endif
        @if(filled($heroSuffix))
          <span class="hero-subheadline" style="display: block; font-size: 28px; color: #e2e8f0; font-weight: 700; margin-top: 6px;">
            {{ $heroSuffix }}
          </span>
        @endif
      </h1>
      @endif

      <!-- 3. Subtitle -->
      @php
        $heroSubheading = cms('hero_subheading', "Imperial Defence Academy (IDA), Khulna provides modern psychological profiling, computerized IQ screenings, and tactical obstacle drills for BMA Long Course, Navy Officer Cadet, Air Force (BAFA), and 4-Day ISSB screening boards.");
      @endphp
      @if(filled($heroSubheading))
      <p style="font-size: 16px; color: #cbd5e1; line-height: 1.75; margin-bottom: 32px; max-width: 600px; font-weight: 400; text-shadow: 0 2px 4px rgba(0,0,0,0.8);">
        {{ $heroSubheading }}
      </p>
      @endif

      <!-- 4. Action Buttons -->
      @php
        $btn1Text = cms('hero_btn1_text', 'Online Admission →');
        $btn2Text = cms('hero_btn2_text', 'Explore Courses');
        $btn3Text = cms('hero_btn3_text', 'Online Assessment');
      @endphp
      @if(filled($btn1Text) || filled($btn2Text) || filled($btn3Text))
      <div class="hero-actions-wrap" style="display: flex; flex-wrap: wrap; gap: 14px; margin-bottom: 36px;">
        @if(filled($btn1Text))
        <a href="{{ cms('hero_btn1_url', route('register')) }}" class="btn-academy-primary" style="padding: 13px 26px; font-size: 14px; font-weight: 700; border-radius: 8px;">
          <i class="fa-solid fa-user-shield"></i> {{ $btn1Text }}
        </a>
        @endif
        @if(filled($btn2Text))
        <a href="{{ cms('hero_btn2_url', route('courses')) }}" class="btn-academy-outline" style="padding: 13px 22px; font-size: 14px; border-radius: 8px;">
          <i class="fa-solid fa-book-open"></i> {{ $btn2Text }}
        </a>
        @endif
        @if(filled($btn3Text))
        <a href="{{ cms('hero_btn3_url', route('online_tests')) }}" class="btn-gold" style="padding: 13px 22px; font-size: 14px; font-weight: 700; border-radius: 8px;">
          <i class="fa-solid fa-stopwatch"></i> {{ $btn3Text }}
        </a>
        @endif
      </div>
      @endif

      <!-- 5. Trust Pillars Below CTA -->
      @php
        $pill1 = cms('stat_pill_1', '98.4% Preliminary Screening Pass');
        $pill2 = cms('stat_pill_2', '500+ Commissioned Officers');
        $pill3 = cms('stat_pill_3', '15 OLQ Standard Drill');
      @endphp
      @if(filled($pill1) || filled($pill2) || filled($pill3))
      <div class="hero-trust-pills" style="display: flex; align-items: center; gap: 20px; font-size: 12px; color: #94a3b8; flex-wrap: wrap;">
        @if(filled($pill1))
        <div style="display: flex; align-items: center; gap: 6px;">
          <i class="fa-solid fa-certificate" style="color: var(--accent-gold);"></i>
          <span style="color: #e2e8f0; font-weight: 600; text-shadow: 0 1px 3px rgba(0,0,0,0.8);">{{ $pill1 }}</span>
        </div>
        @endif
        @if(filled($pill2))
        <div style="display: flex; align-items: center; gap: 6px;">
          <i class="fa-solid fa-award" style="color: var(--brand-mint);"></i>
          <span style="color: #e2e8f0; font-weight: 600; text-shadow: 0 1px 3px rgba(0,0,0,0.8);">{{ $pill2 }}</span>
        </div>
        @endif
        @if(filled($pill3))
        <div style="display: flex; align-items: center; gap: 6px;">
          <i class="fa-solid fa-shield-check" style="color: #60a5fa;"></i>
          <span style="text-shadow: 0 1px 3px rgba(0,0,0,0.8);">{{ $pill3 }}</span>
        </div>
        @endif
      </div>
      @endif
    </div>

    <!-- Right 3D-styled Frosted Showcase Card -->
    @php
      $showcaseTitle = cms('showcase_title', cms('site_name', 'IMPERIAL DEFENCE ACADEMY'));
      $showcaseTagline = cms('showcase_tagline', cms('site_tagline', 'Courage • Character • Commission'));
      $box1Label = cms('stat_box1_label', 'Active Cadets');
      $box2Label = cms('stat_box2_label', 'Recommended');
      $box3Label = cms('stat_box3_label', 'Active Squads');
      $hasStats = filled($box1Label) || filled($box2Label) || filled($box3Label);
      $statsCount = (filled($box1Label) ? 1 : 0) + (filled($box2Label) ? 1 : 0) + (filled($box3Label) ? 1 : 0);
      $beaconText = cms('hero_batch_status', 'Current Batch: 95th BMA Long Course Active');
    @endphp
    <div class="hero-animate-in" style="position: relative;">
      <div class="hero-showcase-card classical-card" style="background: rgba(3, 10, 24, 0.22); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); border: 1px solid rgba(255, 255, 255, 0.18); border-top: 2px solid var(--accent-gold); border-radius: 20px; padding: 36px 30px; box-shadow: 0 20px 45px -10px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.2); text-align: center;">
        
        <!-- Animated Floating 3D Crest -->
        <div style="display: flex; justify-content: center; margin-bottom: 20px;">
          @include('frontend.partials.logo_crest', ['size' => 74])
        </div>

        @if(filled($showcaseTitle))
        <h2 style="font-size: 21px; font-weight: 800; color: #ffffff; margin-bottom: 4px; font-family: 'Roboto', sans-serif; text-shadow: 0 2px 8px rgba(0, 0, 0, 0.9);">
          {{ $showcaseTitle }}
        </h2>
        @endif

        @if(filled($showcaseTagline))
        <p style="font-size: 11px; font-weight: 700; color: var(--accent-gold); text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 24px; text-shadow: 0 2px 6px rgba(0, 0, 0, 0.9);">
          {{ $showcaseTagline }}
        </p>
        @endif

        <!-- Live Counting Statistics Grid (From Reference: count-up on scroll) -->
        @if($hasStats)
        <div id="stats-section" class="hero-stats-grid" style="display: grid; grid-template-columns: repeat({{ $statsCount }}, 1fr); gap: 12px; border-top: 1px solid rgba(255, 255, 255, 0.16); padding-top: 22px;">
          @if(filled($box1Label))
          <div style="background: rgba(2, 6, 23, 0.32); backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px); border: 1px solid rgba(255, 255, 255, 0.14); padding: 14px 8px; border-radius: 12px; box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.12), 0 4px 14px rgba(0, 0, 0, 0.25);">
            <strong style="display: block; font-size: 24px; color: #ffffff; font-family: 'Roboto', sans-serif; font-weight: 800; text-shadow: 0 2px 6px rgba(0, 0, 0, 0.9);">
              <span class="counter-number" data-target="{{ $stats['total_cadets'] }}" data-decimals="0">0</span>
            </strong>
            <small style="font-size: 11px; color: #cbd5e1; font-weight: 600; text-shadow: 0 1px 4px rgba(0, 0, 0, 0.85);">{{ $box1Label }}</small>
          </div>
          @endif

          @if(filled($box2Label))
          <div style="background: rgba(2, 6, 23, 0.32); backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px); border: 1px solid rgba(255, 255, 255, 0.14); padding: 14px 8px; border-radius: 12px; box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.12), 0 4px 14px rgba(0, 0, 0, 0.25);">
            <strong style="display: block; font-size: 24px; color: var(--accent-gold); font-family: 'Roboto', sans-serif; font-weight: 800; text-shadow: 0 2px 6px rgba(0, 0, 0, 0.9);">
              <span class="counter-number" data-target="{{ $stats['recommended_cadets'] }}" data-decimals="0">0</span>+
            </strong>
            <small style="font-size: 11px; color: #cbd5e1; font-weight: 600; text-shadow: 0 1px 4px rgba(0, 0, 0, 0.85);">{{ $box2Label }}</small>
          </div>
          @endif

          @if(filled($box3Label))
          <div style="background: rgba(2, 6, 23, 0.32); backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px); border: 1px solid rgba(255, 255, 255, 0.14); padding: 14px 8px; border-radius: 12px; box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.12), 0 4px 14px rgba(0, 0, 0, 0.25);">
            <strong style="display: block; font-size: 24px; color: #34d399; font-family: 'Roboto', sans-serif; font-weight: 800; text-shadow: 0 2px 6px rgba(0, 0, 0, 0.9);">
              <span class="counter-number" data-target="{{ $stats['active_batches'] }}" data-decimals="0">0</span>
            </strong>
            <small style="font-size: 11px; color: #cbd5e1; font-weight: 600; text-shadow: 0 1px 4px rgba(0, 0, 0, 0.85);">{{ $box3Label }}</small>
          </div>
          @endif
        </div>
        @endif

        <!-- Live Beacon Status -->
        @if(filled($beaconText))
        <div class="hero-beacon" style="display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 12px; color: #fef08a; background: rgba(0, 0, 0, 0.32); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); border: 1px solid rgba(212, 175, 55, 0.45); border-radius: 20px; padding: 7px 16px; margin-top: 20px; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3);">
          <span style="width: 7px; height: 7px; border-radius: 50%; background: var(--accent-gold); display: inline-block; box-shadow: 0 0 10px var(--accent-gold); animation: pulseGlow 2s infinite;"></span>
          <span style="font-weight: 600; text-shadow: 0 1px 3px rgba(0, 0, 0, 0.9);">{{ $beaconText }}</span>
        </div>
        @endif
      </div>
    </div>
  </div>

  <!-- Animated Slide Indicator Dots for Any Number of Slides -->
  @if(count($heroSlides) > 1)
    <div style="position: absolute; bottom: 18px; left: 50%; transform: translateX(-50%); z-index: 25; display: flex; align-items: center; gap: 8px;">
      @foreach($heroSlides as $idx => $slide)
        <button type="button" onclick="changeHeroSlide({{ $idx }})" class="hero-slider-dot" style="width: {{ $idx === 0 ? '32px' : '8px' }}; height: 5px; border-radius: 9999px; background: {{ $idx === 0 ? 'var(--accent-gold, #d4af37)' : 'rgba(255,255,255,0.3)' }}; border: none; cursor: pointer; transition: all 0.3s ease;" aria-label="Slide {{ $idx + 1 }}"></button>
      @endforeach
    </div>
  @endif
</section>

<!-- Animated Marquee Dispatch (From Reference) -->
@php
  $marqueeIsLive = cms('marquee_is_live', '1') == '1';
  $marqueeLabel = cms('marquee_label', 'BULLETIN:');
  $marqueeText = cms('marquee_text', 'BMA 95th Long Course & Navy 2026-B Batches Enrolling Now ★ Free Dossier Screening Every Friday at Khulna HQ ★');
  $showMarquee = $marqueeIsLive && (filled($marqueeText) || ($notices->isNotEmpty() && filled($marqueeLabel)));
@endphp
@if($showMarquee)
<div style="background: #02060d; border-bottom: 1px solid rgba(255, 255, 255, 0.08); max-width: 100vw; width: 100%; overflow: hidden; position: relative; display: flex; align-items: center; padding: 10px 0;">
  @if(filled($marqueeLabel))
  <div style="background: #02060d; padding: 0 14px; font-weight: 800; color: var(--accent-gold); border-right: 1px solid rgba(255, 255, 255, 0.1); display: flex; align-items: center; gap: 8px; z-index: 10; flex-shrink: 0;">
    <span style="width: 7px; height: 7px; border-radius: 50%; background: var(--accent-gold); animation: pulseGlow 1.5s infinite;"></span>
    <span style="font-size: 11.5px; letter-spacing: 0.5px;">{{ $marqueeLabel }}</span>
  </div>
  @endif
  <div style="flex: 1 1 0%; min-width: 0; overflow: hidden; padding-left: 10px;">
    <div class="animate-marquee" style="color: #e2e8f0; font-size: 13px; font-weight: 500;">
      @if($notices->isNotEmpty())
        ★ {{ $notices->first()->title }} ({{ $notices->first()->publish_date->format('d M, Y') }})
      @endif
      @if(filled($marqueeText))
        ★ {{ $marqueeText }}
      @endif
    </div>
  </div>
</div>
@endif

@php
  $pillarsHeading = cms('pillars_heading', 'The Four Pillars of IDA Preparation');
  $pillarsTag = cms('pillars_tag', 'Comprehensive Cadet Curriculum');
  $p1Title = cms('pillar1_title', 'Verbal & Non-Verbal IQ');
  $p2Title = cms('pillar2_title', 'Psychological Battery');
  $p3Title = cms('pillar3_title', 'Ground Tasks (GTO)');
  $p4Title = cms('pillar4_title', 'Interview & Viva Voce');
  $pillarCount = (filled($p1Title) ? 1 : 0) + (filled($p2Title) ? 1 : 0) + (filled($p3Title) ? 1 : 0) + (filled($p4Title) ? 1 : 0);
@endphp
@if(filled($pillarsHeading) && $pillarCount > 0)
<!-- 4 Pillars Training System (From Reference: Staggered Fade-Up) -->
<section id="courses" class="section-responsive-pad" style="max-width: 1240px; margin: 70px auto; padding: 0 24px;">
  <div data-aos="fade-up" style="text-align: center; max-width: 680px; margin: 0 auto 46px;">
    @if(filled($pillarsTag))
    <span style="font-size: 11.5px; font-weight: 700; color: var(--accent-gold); text-transform: uppercase; letter-spacing: 1.5px; display: inline-block; background: rgba(212, 175, 55, 0.1); border: 1px solid rgba(212, 175, 55, 0.3); padding: 4px 14px; border-radius: 20px; margin-bottom: 10px;">
      {{ $pillarsTag }}
    </span>
    @endif
    <h2 style="font-size: 32px; font-weight: 800; color: var(--text-main); font-family: 'Roboto', sans-serif;">
      {{ $pillarsHeading }}
    </h2>
    <div style="width: 60px; height: 2px; background: var(--accent-gold); margin: 12px auto 0;"></div>
  </div>

  <div class="grid-cols-4-responsive" style="grid-template-columns: repeat({{ min($pillarCount, 4) }}, 1fr);">
    
    <!-- Pillar 1 -->
    @if(filled($p1Title))
    <div class="classical-card content-panel" data-aos="fade-up" data-aos-delay="100" style="margin-bottom: 0;">
      <div class="metric-icon icon-emerald" style="margin-bottom: 16px; width: 50px; height: 50px; font-size: 22px; border-radius: 14px;">
        <i class="fa-solid fa-brain"></i>
      </div>
      <div style="font-size: 11px; color: var(--accent-gold); font-weight: 800; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 4px;">PILLAR 1</div>
      <h3 style="font-size: 17px; font-weight: 700; margin-bottom: 8px; color: var(--text-main); font-family: 'Roboto', sans-serif;">
        {{ $p1Title }}
      </h3>
      @if(filled(cms('pillar1_desc')))
      <p style="font-size: 13px; color: var(--text-muted); line-height: 1.65; margin: 0;">
        {{ cms('pillar1_desc', 'Fast-paced computerized intelligence screening, matrix pattern puzzles, spatial reasoning, and negative marking calibration.') }}
      </p>
      @endif
    </div>
    @endif

    <!-- Pillar 2 -->
    @if(filled($p2Title))
    <div class="classical-card content-panel" data-aos="fade-up" data-aos-delay="200" style="margin-bottom: 0;">
      <div class="metric-icon icon-gold" style="margin-bottom: 16px; width: 50px; height: 50px; font-size: 22px; border-radius: 14px;">
        <i class="fa-solid fa-shield-halved"></i>
      </div>
      <div style="font-size: 11px; color: var(--accent-gold); font-weight: 800; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 4px;">PILLAR 2</div>
      <h3 style="font-size: 17px; font-weight: 700; margin-bottom: 8px; color: var(--text-main); font-family: 'Roboto', sans-serif;">
        {{ $p2Title }}
      </h3>
      @if(filled(cms('pillar2_desc')))
      <p style="font-size: 13px; color: var(--text-muted); line-height: 1.65; margin: 0;">
        {{ cms('pillar2_desc', 'Timed Word Association Test (WAT), Picture Perception (PPDT), Thematic Apperception (TAT), and Situation Reaction Tests.') }}
      </p>
      @endif
    </div>
    @endif

    <!-- Pillar 3 -->
    @if(filled($p3Title))
    <div class="classical-card content-panel" data-aos="fade-up" data-aos-delay="300" style="margin-bottom: 0;">
      <div class="metric-icon icon-navy" style="margin-bottom: 16px; width: 50px; height: 50px; font-size: 22px; border-radius: 14px;">
        <i class="fa-solid fa-person-hiking"></i>
      </div>
      <div style="font-size: 11px; color: var(--accent-gold); font-weight: 800; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 4px;">PILLAR 3</div>
      <h3 style="font-size: 17px; font-weight: 700; margin-bottom: 8px; color: var(--text-main); font-family: 'Roboto', sans-serif;">
        {{ $p3Title }}
      </h3>
      @if(filled(cms('pillar3_desc')))
      <p style="font-size: 13px; color: var(--text-muted); line-height: 1.65; margin: 0;">
        {{ cms('pillar3_desc', 'Full-scale Progressive Group Tasks (PGT), Half Group Tasks (HGT), Command Tasks, and individual obstacle courses in Boyra.') }}
      </p>
      @endif
    </div>
    @endif

    <!-- Pillar 4 -->
    @if(filled($p4Title))
    <div class="classical-card content-panel" data-aos="fade-up" data-aos-delay="400" style="margin-bottom: 0;">
      <div class="metric-icon icon-red" style="margin-bottom: 16px; width: 50px; height: 50px; font-size: 22px; border-radius: 14px;">
        <i class="fa-solid fa-comments"></i>
      </div>
      <div style="font-size: 11px; color: var(--accent-gold); font-weight: 800; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 4px;">PILLAR 4</div>
      <h3 style="font-size: 17px; font-weight: 700; margin-bottom: 8px; color: var(--text-main); font-family: 'Roboto', sans-serif;">
        {{ $p4Title }}
      </h3>
      @if(filled(cms('pillar4_desc')))
      <p style="font-size: 13px; color: var(--text-muted); line-height: 1.65; margin: 0;">
        {{ cms('pillar4_desc', '1-on-1 mock interviews conducted by retired senior defense officers focusing on Officer-Like Qualities (OLQ) and composure.') }}
      </p>
      @endif
    </div>
    @endif

  </div>
</section>
@endif

@php
  $olqHeading = cms('olq_heading', '15 Officer-Like Qualities (OLQ) Matrix');
  $olqTag = cms('olq_tag', 'Core Military Evaluation Standards');
  $f1Title = cms('olq_factor1_title', 'FACTOR I: PLANNING & INTELLECT');
  $f2Title = cms('olq_factor2_title', 'FACTOR II: SOCIAL ADAPTABILITY');
  $f3Title = cms('olq_factor3_title', 'FACTOR III: SOCIAL EFFECTIVENESS');
  $f4Title = cms('olq_factor4_title', 'FACTOR IV: DYNAMIC COURAGE');
  $olqCount = (filled($f1Title) ? 1 : 0) + (filled($f2Title) ? 1 : 0) + (filled($f3Title) ? 1 : 0) + (filled($f4Title) ? 1 : 0);
@endphp
@if(filled($olqHeading) && $olqCount > 0)
<!-- 15 Officer Like Qualities (OLQ) Matrix (From Reference: fade-right / fade-left) -->
<section class="section-responsive-pad" style="background: #0b1728; border-top: 1px solid #1e314b; border-bottom: 1px solid #1e314b; padding: 75px 24px; color: #e2e8f0;">
  <div style="max-width: 1240px; margin: 0 auto;">
    
    <div data-aos="fade-up" style="text-align: center; max-width: 680px; margin: 0 auto 46px;">
      @if(filled($olqTag))
      <span style="font-size: 11.5px; font-weight: 700; color: var(--accent-gold); text-transform: uppercase; letter-spacing: 1.5px; display: inline-block; background: rgba(212, 175, 55, 0.1); border: 1px solid rgba(212, 175, 55, 0.3); padding: 4px 14px; border-radius: 20px; margin-bottom: 8px;">
        {{ $olqTag }}
      </span>
      @endif
      <h2 style="font-size: 30px; font-weight: 800; color: #ffffff; font-family: 'Roboto', sans-serif;">
        {{ $olqHeading }}
      </h2>
      <div style="width: 60px; height: 2px; background: var(--accent-gold); margin: 12px auto 0;"></div>
    </div>

    <div class="grid-cols-4-responsive" style="grid-template-columns: repeat({{ min($olqCount, 4) }}, 1fr);">
      
      <!-- Factor 1 (fade-right delay 100) -->
      @if(filled($f1Title))
      <div class="classical-card" data-aos="fade-right" data-aos-delay="100" style="background: #050b14; border: 1px solid #1e314b; padding: 24px; border-radius: 12px;">
        <h4 style="font-size: 13.5px; font-weight: 800; color: #d4af37; border-bottom: 1px solid #1e314b; padding-bottom: 10px; margin-bottom: 14px; font-family: 'Roboto', sans-serif;">
          {{ $f1Title }}
        </h4>
        <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 9px; font-size: 12.5px; color: #cbd5e1;">
          <li><span style="color: #d4af37;">✦</span> 1. Effective Intelligence</li>
          <li><span style="color: #d4af37;">✦</span> 2. Reasoning Ability</li>
          <li><span style="color: #d4af37;">✦</span> 3. Organizing Ability</li>
          <li><span style="color: #d4af37;">✦</span> 4. Power of Expression</li>
        </ul>
      </div>
      @endif

      <!-- Factor 2 (fade-right delay 200) -->
      @if(filled($f2Title))
      <div class="classical-card" data-aos="fade-right" data-aos-delay="200" style="background: #050b14; border: 1px solid #1e314b; padding: 24px; border-radius: 12px;">
        <h4 style="font-size: 13.5px; font-weight: 800; color: #d4af37; border-bottom: 1px solid #1e314b; padding-bottom: 10px; margin-bottom: 14px; font-family: 'Roboto', sans-serif;">
          {{ $f2Title }}
        </h4>
        <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 9px; font-size: 12.5px; color: #cbd5e1;">
          <li><span style="color: #d4af37;">✦</span> 5. Social Adaptability</li>
          <li><span style="color: #d4af37;">✦</span> 6. Cooperation & Camraderie</li>
          <li><span style="color: #d4af37;">✦</span> 7. Sense of Responsibility</li>
        </ul>
      </div>
      @endif

      <!-- Factor 3 (fade-left delay 300) -->
      @if(filled($f3Title))
      <div class="classical-card" data-aos="fade-left" data-aos-delay="300" style="background: #050b14; border: 1px solid #1e314b; padding: 24px; border-radius: 12px;">
        <h4 style="font-size: 13.5px; font-weight: 800; color: #d4af37; border-bottom: 1px solid #1e314b; padding-bottom: 10px; margin-bottom: 14px; font-family: 'Roboto', sans-serif;">
          {{ $f3Title }}
        </h4>
        <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 9px; font-size: 12.5px; color: #cbd5e1;">
          <li><span style="color: #d4af37;">✦</span> 8. Initiative & Boldness</li>
          <li><span style="color: #d4af37;">✦</span> 9. Self-Confidence</li>
          <li><span style="color: #d4af37;">✦</span> 10. Speed of Decision</li>
          <li><span style="color: #d4af37;">✦</span> 11. Ability to Influence</li>
          <li><span style="color: #d4af37;">✦</span> 12. Liveliness & Morale</li>
        </ul>
      </div>
      @endif

      <!-- Factor 4 (fade-left delay 400) -->
      @if(filled($f4Title))
      <div class="classical-card" data-aos="fade-left" data-aos-delay="400" style="background: #050b14; border: 1px solid #1e314b; padding: 24px; border-radius: 12px;">
        <h4 style="font-size: 13.5px; font-weight: 800; color: #d4af37; border-bottom: 1px solid #1e314b; padding-bottom: 10px; margin-bottom: 14px; font-family: 'Roboto', sans-serif;">
          {{ $f4Title }}
        </h4>
        <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 9px; font-size: 12.5px; color: #cbd5e1;">
          <li><span style="color: #d4af37;">✦</span> 13. Determination</li>
          <li><span style="color: #d4af37;">✦</span> 14. Courage & Moral Stature</li>
          <li><span style="color: #d4af37;">✦</span> 15. Physical Stamina</li>
        </ul>
      </div>
      @endif

    </div>
  </div>
</section>
@endif

@php
  $courseTag = cms('courses_section_tag', 'Admissions Open');
  $courseHeading = cms('courses_section_heading', 'Featured Preparatory Programs');
@endphp
@if($courses->isNotEmpty() && filled($courseHeading))
<!-- Featured Preparatory Programs -->
<section class="section-responsive-pad" style="background: #ffffff; border-bottom: 1px solid var(--border-soft); padding: 75px 24px;">
  <div style="max-width: 1240px; margin: 0 auto;">
    
    <div data-aos="fade-up" style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 40px; flex-wrap: gap: 16px;">
      <div>
        @if(filled($courseTag))
        <span style="font-size: 11.5px; font-weight: 700; color: var(--brand-emerald); text-transform: uppercase; letter-spacing: 0.8px; display: inline-block; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 3px 10px; border-radius: 20px; margin-bottom: 8px;">
          {{ $courseTag }}
        </span>
        @endif
        <h2 style="font-size: 30px; font-weight: 800; color: var(--text-main); font-family: 'Roboto', sans-serif;">
          {{ $courseHeading }}
        </h2>
      </div>
      <a href="{{ route('courses') }}" class="btn-secondary" style="font-size: 13px;">
        View All Courses &rarr;
      </a>
    </div>

    <div class="grid-cols-3-responsive">
      @foreach($courses as $idx => $course)
        <div class="content-panel classical-card" data-aos="fade-up" data-aos-delay="{{ ($idx + 1) * 150 }}" style="display: flex; flex-direction: column; justify-content: space-between; height: 100%; margin-bottom: 0; padding: 26px;">
          <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
              <span class="badge badge-emerald" style="font-size: 11px;">{{ $course->category }}</span>
              <span style="font-size: 12px; font-weight: 600; color: var(--text-muted); display: inline-flex; align-items: center; gap: 4px;">
                <i class="fa-regular fa-clock" style="color: var(--brand-emerald);"></i> {{ $course->duration }}
              </span>
            </div>

            <h3 style="font-size: 18px; font-weight: 800; margin-bottom: 10px; color: var(--brand-deep); font-family: 'Roboto', sans-serif;">
              {{ $course->title }}
            </h3>

            <p style="font-size: 13.5px; color: var(--text-muted); line-height: 1.65; margin-bottom: 18px;">
              {{ Str::limit($course->description, 130) }}
            </p>

            @if(!empty($course->features))
              <ul style="list-style: none; margin-bottom: 22px; display: flex; flex-direction: column; gap: 8px; padding: 0;">
                @foreach(array_slice($course->features, 0, 3) as $feat)
                  <li style="font-size: 12.5px; color: var(--text-body); display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-circle-check" style="color: var(--brand-mint); font-size: 14px; flex-shrink: 0;"></i>
                    <span>{{ $feat }}</span>
                  </li>
                @endforeach
              </ul>
            @endif
          </div>

          <div style="border-top: 1px solid var(--border-soft); padding-top: 18px; display: flex; justify-content: space-between; align-items: center;">
            <div>
              <small style="display: block; font-size: 11px; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">Tuition Fee</small>
              <strong style="font-size: 20px; color: var(--brand-deep); font-family: 'Roboto', sans-serif; font-weight: 800;">৳{{ number_format($course->fee, 0) }}</strong>
            </div>
            <a href="{{ route('courses.detail', $course->slug) }}" class="btn-primary" style="padding: 8px 16px; font-size: 12.5px;">
              Details & Apply &rarr;
            </a>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>
@endif

@php
  $galleryTag = cms('home_gallery_tag', 'Life at IDA');
  $galleryTitle = cms('home_gallery_title', 'Rigorous Ground & Academic Training');
@endphp
@if($gallery->isNotEmpty() && filled($galleryTitle))
<!-- Cadet Training Gallery Preview -->
<section class="section-responsive-pad" style="max-width: 1240px; margin: 70px auto; padding: 0 24px;">
  <div data-aos="fade-up" style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 32px; flex-wrap: wrap; gap: 16px;">
    <div>
      @if(filled($galleryTag))
      <span style="font-size: 11.5px; font-weight: 700; color: var(--brand-emerald); text-transform: uppercase; letter-spacing: 0.8px; display: inline-block; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 3px 10px; border-radius: 20px; margin-bottom: 8px;">
        {{ $galleryTag }}
      </span>
      @endif
      <h2 style="font-size: 30px; font-weight: 800; color: var(--text-main); font-family: 'Roboto', sans-serif;">
        {{ $galleryTitle }}
      </h2>
    </div>
    <a href="{{ route('gallery') }}" class="btn-secondary" style="font-size: 13px;">View Full Archive &rarr;</a>
  </div>

  <div class="grid-cols-4-responsive">
    @foreach($gallery as $idx => $item)
      <div class="classical-card" data-aos="fade-up" data-aos-delay="{{ ($idx + 1) * 100 }}" style="background: var(--surface); border: 1px solid var(--border-soft); border-radius: 16px; overflow: hidden; box-shadow: var(--shadow-card);">
        <div style="height: 155px; background: linear-gradient(135deg, #064e3b 0%, #0f172a 100%); display: grid; place-items: center; color: rgba(255,255,255,0.7); font-size: 32px; overflow: hidden; position: relative;">
          @if($item->image_path)
            <img src="{{ str_starts_with($item->image_path, 'http') ? $item->image_path : asset($item->image_path) }}" alt="{{ $item->title }}" style="width: 100%; height: 100%; object-fit: cover;">
          @else
            <i class="fa-solid fa-camera"></i>
          @endif
        </div>
        <div style="padding: 14px 16px;">
          <span class="badge badge-blue" style="margin-bottom: 6px; font-size: 10.5px;">{{ $item->category }}</span>
          <h4 style="font-size: 13.5px; font-weight: 700; color: var(--text-main); line-height: 1.4; margin-bottom: 3px; font-family: 'Roboto', sans-serif;">{{ $item->title }}</h4>
          @if(filled($item->caption))
          <small style="color: var(--text-muted); font-size: 11.5px; display: block;">{{ $item->caption }}</small>
          @endif
        </div>
      </div>
    @endforeach
  </div>
</section>
@endif

@php
  $ctaHeading = cms('cta_heading', 'Ready to Wear the Prestigious Officer Uniform?');
  $ctaSubheading = cms('cta_subheading', "Join Khulna's premier defence preparatory academy. Meet our retired military faculty, experience the obstacle fields in Boyra, and commence your journey toward commissioning.");
  $ctaBtn1 = cms('cta_btn1_text', 'Apply for Direct Admission');
  $ctaBtn2 = cms('cta_btn2_text', 'Visit Khulna Campus');
@endphp
@if(filled($ctaHeading))
<!-- Call to Action Banner (From Reference: data-aos="zoom-in" with animate-gold-glow) -->
<section data-aos="zoom-in" data-aos-duration="1000" class="animate-gold-glow cta-responsive-pad" style="position: relative; background: radial-gradient(circle at 50% 0%, rgba(16, 185, 129, 0.3) 0%, var(--brand-primary, #064e3b) 50%, var(--accent-navy, #090e1a) 100%); color: #ffffff; padding: 65px 24px; text-align: center; border-radius: 20px; max-width: 1240px; margin: 30px auto 40px; box-shadow: 0 20px 40px -10px rgba(6, 78, 59, 0.4); border: 1px solid var(--accent-gold); overflow: hidden;">
  <div style="position: relative; max-width: 720px; margin: 0 auto;">
    <h2 style="font-size: 34px; font-weight: 900; margin-bottom: 14px; color: #ffffff; font-family: 'Roboto', sans-serif; letter-spacing: -0.02em;">
      {{ $ctaHeading }}
    </h2>
    @if(filled($ctaSubheading))
    <p style="font-size: 15px; color: #cbd5e1; line-height: 1.7; margin-bottom: 30px;">
      {{ $ctaSubheading }}
    </p>
    @endif
    @if(filled($ctaBtn1) || filled($ctaBtn2))
    <div class="cta-btn-wrap" style="display: flex; justify-content: center; gap: 14px; flex-wrap: wrap;">
      @if(filled($ctaBtn1))
      <a href="{{ cms('cta_btn1_url', route('register')) }}" class="btn-academy-primary" style="padding: 13px 28px; font-size: 14px; border-radius: 8px;">
        <i class="fa-solid fa-file-signature"></i> {{ $ctaBtn1 }}
      </a>
      @endif
      @if(filled($ctaBtn2))
      <a href="{{ cms('cta_btn2_url', route('contact')) }}" class="btn-academy-outline" style="padding: 13px 24px; font-size: 14px; border-radius: 8px;">
        <i class="fa-solid fa-compass"></i> {{ $ctaBtn2 }}
      </a>
      @endif
    </div>
    @endif
  </div>
</section>
@endif
@endsection