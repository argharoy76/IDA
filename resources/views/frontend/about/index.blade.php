@extends('layouts.public')

@section('title', 'About Imperial Defence Academy | Khulna')

@section('content')
<!-- Page Header (Drop-down & Zoom-in Animations) -->
<section style="position: relative; background: radial-gradient(circle at 50% 0%, rgba(16, 185, 129, 0.15) 0%, var(--brand-deep, #022c22) 50%, var(--accent-navy, #050b14) 100%); border-bottom: 1px solid rgba(16, 185, 129, 0.2); padding: 65px 24px; text-align: center; overflow: hidden;">
  <div style="position: relative; max-width: 800px; margin: 0 auto;">
    <!-- Drop-down Badge -->
    @if(filled(cms('about_badge', '★ ESTABLISHED IN KHULNA ★')))
    <div data-aos="fade-down" data-aos-duration="1000">
      <span class="animate-gold-glow" style="display: inline-flex; align-items: center; gap: 6px; background: rgba(212, 175, 55, 0.1); border: 1px solid rgba(212, 175, 55, 0.4); padding: 4px 14px; border-radius: 20px; font-size: 11px; font-weight: 700; color: #f3e5ab; margin-bottom: 16px; letter-spacing: 1px; text-transform: uppercase;">
        {{ cms('about_badge', '★ ESTABLISHED IN KHULNA ★') }}
      </span>
    </div>
    @endif
    
    @if(filled(cms('about_title', 'About Imperial Defence Academy')))
    <h1 data-aos="zoom-in" data-aos-duration="1000" data-aos-delay="200" style="font-size: 40px; font-weight: 800; color: #ffffff; font-family: 'Roboto', sans-serif; letter-spacing: -0.02em; margin-bottom: 12px;">
      {{ cms('about_title', 'About Imperial Defence Academy') }}
    </h1>
    @endif

    @if(filled(cms('about_subtitle', 'A specialized digital and ground training academy dedicated to training, mentoring, and commissioning the future officer corps of the Bangladesh Armed Forces.')))
    <p data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400" style="color: #cbd5e1; font-size: 15.5px; line-height: 1.7; margin: 0 auto; max-width: 680px;">
      {{ cms('about_subtitle', 'A specialized digital and ground training academy dedicated to training, mentoring, and commissioning the future officer corps of the Bangladesh Armed Forces.') }}
    </p>
    @endif
  </div>
</section>

<section style="max-width: 1240px; margin: 60px auto; padding: 0 24px;">
  <!-- Mission & Vision Cards (Directional Glide from Left and Right) -->
  @php
    $missionTitle = cms('mission_title', 'Our Mission');
    $missionDesc = cms('about_mission', 'To provide rigorous, honest, and scientifically structured preparatory training that instills moral courage, rapid tactical decision-making, physical agility, and authentic Officer-Like Qualities (OLQ) in every candidate, preparing them to successfully conquer the ISSB board.');
    $visionTitle = cms('vision_title', 'Our Vision');
    $visionDesc = cms('about_vision', 'To establish Imperial Defence Academy as the most technologically advanced and trusted military preparatory institution in Bangladesh, continuously bridging digital simulation with real-world physical conditioning.');
    $hasMission = filled($missionTitle);
    $hasVision = filled($visionTitle);
  @endphp
  @if($hasMission || $hasVision)
  <div class="grid-cols-2-responsive" style="margin-bottom: 60px; {{ (!$hasMission || !$hasVision) ? 'grid-template-columns: 1fr;' : '' }}">
    @if($hasMission)
    <div class="content-panel classical-card" data-aos="fade-right" data-aos-duration="1000" style="margin-bottom: 0; padding: 30px;">
      <div class="metric-icon icon-emerald" style="margin-bottom: 16px; width: 52px; height: 52px; font-size: 22px; border-radius: 14px;">
        <i class="fa-solid fa-flag"></i>
      </div>
      <h2 style="font-size: 22px; font-weight: 800; color: var(--brand-deep); margin-bottom: 10px; font-family: 'Roboto', sans-serif;">{{ $missionTitle }}</h2>
      @if(filled($missionDesc))
      <p style="color: var(--text-muted); font-size: 14px; line-height: 1.8;">
        {{ $missionDesc }}
      </p>
      @endif
    </div>
    @endif

    @if($hasVision)
    <div class="content-panel classical-card" data-aos="fade-left" data-aos-duration="1000" style="margin-bottom: 0; padding: 30px;">
      <div class="metric-icon icon-gold" style="margin-bottom: 16px; width: 52px; height: 52px; font-size: 22px; border-radius: 14px;">
        <i class="fa-solid fa-eye"></i>
      </div>
      <h2 style="font-size: 22px; font-weight: 800; color: var(--brand-deep); margin-bottom: 10px; font-family: 'Roboto', sans-serif;">{{ $visionTitle }}</h2>
      @if(filled($visionDesc))
      <p style="color: var(--text-muted); font-size: 14px; line-height: 1.8;">
        {{ $visionDesc }}
      </p>
      @endif
    </div>
    @endif
  </div>
  @endif

  <!-- Academy Facilities (Staggered Fade-Up) -->
  @php
    $fac1 = cms('facility1_title', 'Regulation Obstacle Field');
    $fac2 = cms('facility2_title', 'Computerized IQ Laboratory');
    $fac3 = cms('facility3_title', 'Lecturette & Viva Chambers');
    $facCount = (filled($fac1) ? 1 : 0) + (filled($fac2) ? 1 : 0) + (filled($fac3) ? 1 : 0);
  @endphp
  @if($facCount > 0)
  <div style="margin-bottom: 60px;">
    <div data-aos="fade-up" style="text-align: center; margin-bottom: 36px;">
      <span style="font-size: 11.5px; font-weight: 700; color: var(--brand-emerald); text-transform: uppercase; letter-spacing: 0.8px; display: inline-block; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 3px 12px; border-radius: 20px; margin-bottom: 8px;">Infrastructure</span>
      <h2 style="font-size: 28px; font-weight: 800; color: var(--text-main); font-family: 'Roboto', sans-serif;">Khulna Campus Facilities</h2>
      <div style="width: 50px; height: 2px; background: var(--accent-gold); margin: 10px auto 0;"></div>
    </div>

    <div class="grid-cols-3-responsive" style="grid-template-columns: repeat({{ min($facCount, 3) }}, 1fr);">
      @if(filled($fac1))
      <div class="content-panel classical-card" data-aos="fade-up" data-aos-delay="100" style="padding: 24px;">
        <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 10px; color: var(--brand-deep); font-family: 'Roboto', sans-serif;">
          <i class="fa-solid fa-person-running" style="color: var(--brand-emerald); margin-right: 6px;"></i> {{ $fac1 }}
        </h3>
        @if(filled(cms('facility1_desc')))
        <p style="font-size: 13.5px; color: var(--text-muted); line-height: 1.65; margin: 0;">
          {{ cms('facility1_desc', 'Full-scale Progressive Group Task (PGT), Half Group Task (HGT), rope climbing, beam balance, and command task bridging structures.') }}
        </p>
        @endif
      </div>
      @endif

      @if(filled($fac2))
      <div class="content-panel classical-card" data-aos="fade-up" data-aos-delay="200" style="padding: 24px;">
        <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 10px; color: var(--brand-deep); font-family: 'Roboto', sans-serif;">
          <i class="fa-solid fa-laptop-code" style="color: var(--brand-emerald); margin-right: 6px;"></i> {{ $fac2 }}
        </h3>
        @if(filled(cms('facility2_desc')))
        <p style="font-size: 13.5px; color: var(--text-muted); line-height: 1.65; margin: 0;">
          {{ cms('facility2_desc', 'High-speed networked terminals providing simulated computerized preliminary intelligence screenings under timed constraints.') }}
        </p>
        @endif
      </div>
      @endif

      @if(filled($fac3))
      <div class="content-panel classical-card" data-aos="fade-up" data-aos-delay="300" style="padding: 24px;">
        <h3 style="font-size: 16px; font-weight: 700; margin-bottom: 10px; color: var(--brand-deep); font-family: 'Roboto', sans-serif;">
          <i class="fa-solid fa-microphone-lines" style="color: var(--brand-emerald); margin-right: 6px;"></i> {{ $fac3 }}
        </h3>
        @if(filled(cms('facility3_desc')))
        <p style="font-size: 13.5px; color: var(--text-muted); line-height: 1.65; margin: 0;">
          {{ cms('facility3_desc', 'Acoustically isolated presentation suites for group discussions, impromptu speeches, and video-recorded mock interview panels.') }}
        </p>
        @endif
      </div>
      @endif
    </div>
  </div>
  @endif

  <!-- Faculty & Leadership (Staggered Fade-Up) -->
  <div>
    <div data-aos="fade-up" style="text-align: center; margin-bottom: 36px;">
      <span style="font-size: 11.5px; font-weight: 700; color: var(--brand-emerald); text-transform: uppercase; letter-spacing: 0.8px; display: inline-block; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 3px 12px; border-radius: 20px; margin-bottom: 8px;">Direct Military Mentorship</span>
      <h2 style="font-size: 28px; font-weight: 800; color: var(--text-main); font-family: 'Roboto', sans-serif;">Distinguished Academy Faculty</h2>
      <div style="width: 50px; height: 2px; background: #d4af37; margin: 10px auto 0;"></div>
    </div>

    <div class="grid-cols-3-responsive">
      @foreach($instructors as $idx => $inst)
        <div class="content-panel classical-card" data-aos="fade-up" data-aos-delay="{{ ($idx + 1) * 150 }}" style="text-align: center; padding: 28px 20px;">
          <div style="width: 72px; height: 72px; border-radius: 20px; background: linear-gradient(135deg, #064e3b, #0f172a); color: #fff; display: grid; place-items: center; font-size: 28px; margin: 0 auto 16px; box-shadow: 0 8px 20px rgba(6, 78, 59, 0.25);">
            <i class="fa-solid fa-user-shield"></i>
          </div>
          <h3 style="font-size: 17px; font-weight: 800; color: var(--brand-deep); font-family: 'Roboto', sans-serif;">{{ $inst->user->name }}</h3>
          <p style="font-size: 12px; font-weight: 700; color: var(--brand-emerald); margin-top: 3px; margin-bottom: 10px;">{{ $inst->designation }}</p>
          <div class="badge badge-blue" style="margin-bottom: 14px; font-size: 11px;">{{ $inst->specialization }}</div>
          <p style="font-size: 13px; color: var(--text-muted); line-height: 1.65; margin: 0;">{{ $inst->bio }}</p>
        </div>
      @endforeach
    </div>
  </div>
</section>
@endsection