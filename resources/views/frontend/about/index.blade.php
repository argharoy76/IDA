@extends('layouts.public')

@section('title', 'About Imperial Defence Academy | Khulna')

@section('content')

<!-- 1. HERO SECTION (Lighter, Airy Luminous Aesthetic with #85c9cc) -->
@php
  $heroImage = cms('about_hero_image');
  $defaultBg = "background: linear-gradient(135deg, #f0f9fa 0%, #e6f4f5 50%, #f8fafc 100%);";
  $bgStyle = $heroImage ? "background-image: url('".asset($heroImage)."'); background-size: cover; background-position: center; background-attachment: fixed;" : $defaultBg;
@endphp
<section style="position: relative; {{ $bgStyle }} min-height: 520px; display: flex; align-items: center; justify-content: center; text-align: center; overflow: hidden; padding: 80px 24px; border-bottom: 1px solid rgba(133, 201, 204, 0.35);">
  <!-- Light Overlay: Clean Seafoam Tone (Tuned for image visibility) -->
  @if($heroImage)
  <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(240, 249, 250, 0.48) 0%, rgba(230, 244, 245, 0.58) 100%); z-index: 1;"></div>
  @else
  <div style="position: absolute; inset: 0; background: radial-gradient(circle at 50% 20%, rgba(133, 201, 204, 0.25) 0%, transparent 70%); pointer-events: none; z-index: 1;"></div>
  @endif
  
  <div style="position: relative; z-index: 2; max-width: 860px; margin: 0 auto; width: 100%;">
    @if(filled(cms('about_badge', '★ IMPERIAL DEFENCE ACADEMY ★')))
    <div data-aos="fade-down" data-aos-duration="1000">
      <span style="display: inline-flex; align-items: center; gap: 8px; background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(6px); border: 1px solid #85c9cc; padding: 5px 18px; border-radius: 9999px; font-size: 11.5px; font-weight: 800; color: #082d2f; margin-bottom: 20px; letter-spacing: 1px; text-transform: uppercase; box-shadow: 0 2px 10px rgba(133, 201, 204, 0.3);">
        <i class="fa-solid fa-shield-halved" style="color: #082d2f;"></i> {{ cms('about_badge', '★ IMPERIAL DEFENCE ACADEMY ★') }}
      </span>
    </div>
    @endif
    
    <h1 data-aos="zoom-in" data-aos-duration="1000" data-aos-delay="200" style="font-size: clamp(32px, 5.5vw, 48px); font-weight: 900; color: #082d2f; font-family: 'Roboto', sans-serif; letter-spacing: -0.02em; margin-bottom: 20px; text-shadow: 0 2px 12px rgba(255, 255, 255, 0.85);">
      {{ cms('about_hero_title', 'Building Tomorrow\'s Leaders Today') }}
    </h1>

    <p data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400" style="color: #1e293b; font-size: 16.5px; line-height: 1.8; margin: 0 auto; max-width: 720px; font-weight: 500; text-shadow: 0 1px 8px rgba(255, 255, 255, 0.85);">
      {{ cms('about_hero_subtitle', 'A premier defence preparatory institution dedicated to excellence in tactical training, moral courage, and authentic leadership.') }}
    </p>
  </div>
</section>

<!-- 2. ABOUT TEXT SECTION (Who We Are) -->
<section style="background: #ffffff; padding: 85px 24px; position: relative;">
  <div style="max-width: 940px; margin: 0 auto; text-align: center;" data-aos="fade-up" data-aos-duration="1000">
    <span style="font-size: 11.5px; font-weight: 800; color: #082d2f; text-transform: uppercase; letter-spacing: 1px; display: inline-flex; align-items: center; gap: 6px; background: rgba(133, 201, 204, 0.22); border: 1px solid #85c9cc; padding: 5px 16px; border-radius: 9999px; margin-bottom: 16px; box-shadow: 0 2px 8px rgba(133, 201, 204, 0.25);">
      Who We Are
    </span>
    <h2 style="font-size: clamp(28px, 4vw, 36px); font-weight: 800; color: #082d2f; margin-bottom: 20px; font-family: 'Roboto', sans-serif; letter-spacing: -0.01em;">
      {{ cms('about_title', 'About Our Academy') }}
    </h2>
    <div style="width: 64px; height: 3px; background: #85c9cc; margin: 0 auto 28px; border-radius: 2px;"></div>
    
    <p style="font-size: 19px; color: #082d2f; line-height: 1.85; margin-bottom: 22px; font-weight: 500;">
      {{ cms('about_hero_overlay_text', 'Imperial Defence Academy stands as a beacon of excellence, bridging the gap between potential and achievement. We specialize in comprehensive preparatory training for the Armed Forces.') }}
    </p>
    <p style="font-size: 15.5px; color: #475569; line-height: 1.8; margin: 0 auto; max-width: 820px;">
      {{ cms('about_subtitle', 'Our specialized programs combine rigorous physical conditioning with advanced psychological screening techniques, ensuring our candidates are fully prepared for the challenges of military selection boards and beyond.') }}
    </p>
  </div>
</section>

<!-- 3. MISSION & VISION -->
<section style="background-color: #f4fafb; padding: 85px 24px; border-top: 1px solid rgba(133, 201, 204, 0.35); border-bottom: 1px solid rgba(133, 201, 204, 0.35);">
  <div style="max-width: 1240px; margin: 0 auto;">
    @php
      $missionTitle = cms('mission_title', 'Our Mission');
      $missionDesc = cms('about_mission', 'To provide rigorous, honest, and scientifically structured preparatory training that instills moral courage, rapid tactical decision-making, physical agility, and authentic Officer-Like Qualities (OLQ) in every candidate, preparing them to successfully conquer the ISSB board.');
      $visionTitle = cms('vision_title', 'Our Vision');
      $visionDesc = cms('about_vision', 'To establish Imperial Defence Academy as the most technologically advanced and trusted military preparatory institution in Bangladesh, continuously bridging digital simulation with real-world physical conditioning.');
      $hasMission = filled($missionTitle);
      $hasVision = filled($visionTitle);
    @endphp
    
    @if($hasMission || $hasVision)
    <div style="display: grid; gap: 40px; {{ (!$hasMission || !$hasVision) ? 'grid-template-columns: 1fr; max-width: 800px; margin: 0 auto;' : 'grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));' }}">
      @if($hasMission)
      <div class="content-panel classical-card hover-up" data-aos="fade-right" data-aos-duration="1000" style="padding: 40px; background: #ffffff; border-radius: 18px; box-shadow: 0 10px 30px rgba(133, 201, 204, 0.15); border: 1.5px solid #85c9cc; transition: transform 0.3s ease;">
        <div class="metric-icon" style="margin-bottom: 24px; width: 64px; height: 64px; font-size: 28px; border-radius: 16px; background: #85c9cc; color: #082d2f; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(133, 201, 204, 0.35);">
          <i class="fa-solid fa-flag"></i>
        </div>
        <h2 style="font-size: 24px; font-weight: 800; color: #082d2f; margin-bottom: 16px; font-family: 'Roboto', sans-serif;">{{ $missionTitle }}</h2>
        @if(filled($missionDesc))
        <p style="color: #475569; font-size: 15px; line-height: 1.8; margin: 0;">
          {{ $missionDesc }}
        </p>
        @endif
      </div>
      @endif

      @if($hasVision)
      <div class="content-panel classical-card hover-up" data-aos="fade-left" data-aos-duration="1000" style="padding: 40px; background: #ffffff; border-radius: 18px; box-shadow: 0 10px 30px rgba(133, 201, 204, 0.15); border: 1.5px solid #85c9cc; transition: transform 0.3s ease;">
        <div class="metric-icon" style="margin-bottom: 24px; width: 64px; height: 64px; font-size: 28px; border-radius: 16px; background: #85c9cc; color: #082d2f; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(133, 201, 204, 0.35);">
          <i class="fa-solid fa-eye"></i>
        </div>
        <h2 style="font-size: 24px; font-weight: 800; color: #082d2f; margin-bottom: 16px; font-family: 'Roboto', sans-serif;">{{ $visionTitle }}</h2>
        @if(filled($visionDesc))
        <p style="color: #475569; font-size: 15px; line-height: 1.8; margin: 0;">
          {{ $visionDesc }}
        </p>
        @endif
      </div>
      @endif
    </div>
    @endif
  </div>
</section>

<!-- 4. TEAM / LEADERSHIP SECTION (OneMedical Inspired, Light Palette) -->
@if(isset($teamMembers) && $teamMembers->count() > 0)
<section style="padding: 100px 24px; background: #ffffff;">
  <div style="max-width: 1240px; margin: 0 auto;">
    
    <div data-aos="fade-up" style="text-align: center; margin-bottom: 60px;">
      <span style="font-size: 11.5px; font-weight: 800; color: #082d2f; text-transform: uppercase; letter-spacing: 1px; display: inline-flex; align-items: center; gap: 6px; background: rgba(133, 201, 204, 0.22); border: 1px solid #85c9cc; padding: 5px 16px; border-radius: 9999px; margin-bottom: 14px; box-shadow: 0 2px 8px rgba(133, 201, 204, 0.25);">
        Our Leadership
      </span>
      <h2 style="font-size: clamp(30px, 4.5vw, 40px); font-weight: 800; color: #082d2f; font-family: 'Roboto', sans-serif; letter-spacing: -0.01em;">
        Meet the Team
      </h2>
      <div style="width: 64px; height: 3px; background: #85c9cc; margin: 16px auto 0; border-radius: 2px;"></div>
    </div>

    @php
      $cat1 = $teamMembers->where('display_category', 1);
      $cat2 = $teamMembers->where('display_category', 2);
      $cat3 = $teamMembers->where('display_category', 3);
      $cat4 = $teamMembers->where('display_category', 4);
    @endphp

    <!-- Category 1: Featured / Large (CEO Spotlight - Light & Luminous) -->
    @if($cat1->count() > 0)
    <div style="margin-bottom: 80px; display: flex; flex-direction: column; gap: 60px;">
      @foreach($cat1 as $idx => $member)
      <div class="team-card-large" data-aos="fade-up" style="display: flex; flex-wrap: wrap; background: linear-gradient(135deg, #f0f9fa 0%, #e6f4f5 50%, #eef8f8 100%); border-radius: 24px; overflow: hidden; box-shadow: 0 20px 45px rgba(8, 45, 47, 0.08); border: 1.5px solid #85c9cc; position: relative;">
        <!-- Left: Image (400x500 area approx) -->
        <div style="flex: 1 1 420px; min-height: 440px; position: relative; overflow: hidden; background: #e3f5f6;">
          <div style="position: absolute; inset: 0; background-image: url('{{ $member->getPhotoUrl() }}'); background-size: cover; background-position: center; transition: transform 0.5s ease;" class="hover-scale"></div>
        </div>
        <!-- Right: Content -->
        <div style="flex: 1 1 420px; padding: 50px 45px; display: flex; flex-direction: column; justify-content: center; position: relative; z-index: 2;">
          <div style="display: inline-flex; align-items: center; gap: 8px; width: fit-content; background: #ffffff; color: #082d2f; padding: 6px 16px; border-radius: 9999px; border: 1px solid #85c9cc; font-size: 11px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 18px; box-shadow: 0 2px 8px rgba(133, 201, 204, 0.25);">
            <i class="fa-solid fa-crown" style="font-size: 10px; color: #082d2f;"></i> Executive Leadership
          </div>
          <h3 style="font-size: clamp(26px, 3.5vw, 36px); font-weight: 900; color: #082d2f; margin-bottom: 8px; font-family: 'Roboto', sans-serif; letter-spacing: -0.01em;">
            {{ $member->name }}
          </h3>
          <h4 style="font-size: 15px; font-weight: 800; color: #0a5c60; margin-bottom: 22px; text-transform: uppercase; letter-spacing: 0.8px;">
            {{ $member->designation }}
          </h4>
          @if($member->bio)
          <div style="font-size: 16px; color: #334155; line-height: 1.85; font-weight: 400;">
            <p style="margin: 0;">{!! nl2br(e($member->bio)) !!}</p>
          </div>
          @endif
        </div>
      </div>
      @endforeach
    </div>
    @endif

    <!-- Category 2: Standard (Directors) -->
    @if($cat2->count() > 0)
    <div style="margin-bottom: 80px;">
      <div data-aos="fade-up" style="text-align: center; margin-bottom: 35px;">
        <span style="font-size: 11px; font-weight: 800; color: #082d2f; text-transform: uppercase; letter-spacing: 1px; display: inline-flex; align-items: center; background: rgba(133, 201, 204, 0.22); border: 1px solid #85c9cc; padding: 4px 14px; border-radius: 9999px; margin-bottom: 8px;">Directing Directorate</span>
        <h3 style="font-size: 24px; font-weight: 800; color: #082d2f; font-family: 'Roboto', sans-serif;">Directors & Core Leadership</h3>
      </div>
      <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 35px;">
        @foreach($cat2 as $idx => $member)
        <div class="team-card-medium" data-aos="fade-up" data-aos-delay="{{ $idx * 100 }}" style="background: #ffffff; border-radius: 18px; overflow: hidden; box-shadow: 0 10px 30px rgba(133, 201, 204, 0.18); border: 1.5px solid #85c9cc; transition: transform 0.3s, box-shadow 0.3s; display: flex; flex-direction: column; height: 100%;">
          <div style="height: 310px; overflow: hidden; position: relative; background: #e8f5f6;">
            <div style="position: absolute; inset: 0; background-image: url('{{ $member->getPhotoUrl() }}'); background-size: cover; background-position: center; transition: transform 0.5s ease;" class="hover-scale"></div>
            <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 60px; background: linear-gradient(to top, rgba(133, 201, 204, 0.4), transparent);"></div>
          </div>
          <div style="padding: 26px 24px; display: flex; flex-direction: column; flex-grow: 1;">
            <h4 style="font-size: 20px; font-weight: 800; color: #082d2f; margin-bottom: 4px; font-family: 'Roboto', sans-serif;">{{ $member->name }}</h4>
            <span style="font-size: 13px; font-weight: 700; color: #0a5c60; margin-bottom: 14px; display: block;">{{ $member->designation }}</span>
            @if($member->bio)
            <p style="font-size: 14px; color: #475569; line-height: 1.65; margin-bottom: 0; flex-grow: 1;">{{ $member->bio }}</p>
            @endif
          </div>
        </div>
        @endforeach
      </div>
    </div>
    @endif

    <!-- Category 3: Compact (Staff) -->
    @if($cat3->count() > 0)
    <div style="margin-bottom: 80px;">
      <div data-aos="fade-up" style="text-align: center; margin-bottom: 35px;">
        <span style="font-size: 11px; font-weight: 800; color: #082d2f; text-transform: uppercase; letter-spacing: 1px; display: inline-flex; align-items: center; background: rgba(133, 201, 204, 0.22); border: 1px solid #85c9cc; padding: 4px 14px; border-radius: 9999px; margin-bottom: 8px;">Faculty Squad</span>
        <h3 style="font-size: 24px; font-weight: 800; color: #082d2f; font-family: 'Roboto', sans-serif;">Academy Mentors & Specialists</h3>
      </div>
      <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 30px;">
        @foreach($cat3 as $idx => $member)
        <div data-aos="fade-up" data-aos-delay="{{ $idx * 50 }}" style="text-align: center; background: #f4fafb; padding: 32px 22px; border-radius: 16px; border: 1.5px solid #85c9cc; box-shadow: 0 8px 20px rgba(133, 201, 204, 0.12); transition: transform 0.3s ease, box-shadow 0.3s ease;" class="hover-up">
          <div style="width: 140px; height: 140px; border-radius: 50%; overflow: hidden; margin: 0 auto 18px; box-shadow: 0 8px 20px rgba(133, 201, 204, 0.25); border: 4px solid #85c9cc;">
            <div style="width: 100%; height: 100%; background-image: url('{{ $member->getPhotoUrl() }}'); background-size: cover; background-position: center;"></div>
          </div>
          <h4 style="font-size: 18px; font-weight: 800; color: #082d2f; margin-bottom: 4px;">{{ $member->name }}</h4>
          <span style="font-size: 12px; font-weight: 700; color: #0a5c60; margin-bottom: 12px; display: block; text-transform: uppercase; letter-spacing: 0.5px;">{{ $member->designation }}</span>
          @if($member->bio)
          <p style="font-size: 13px; color: #64748b; line-height: 1.55; margin-bottom: 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $member->bio }}</p>
          @endif
        </div>
        @endforeach
      </div>
    </div>
    @endif

    <!-- Category 4: Mini (Support) -->
    @if($cat4->count() > 0)
    <div>
      <div data-aos="fade-up" style="text-align: center; margin-bottom: 30px;">
        <span style="font-size: 11px; font-weight: 800; color: #082d2f; text-transform: uppercase; letter-spacing: 1px; display: inline-flex; align-items: center; background: rgba(133, 201, 204, 0.22); border: 1px solid #85c9cc; padding: 4px 14px; border-radius: 9999px; margin-bottom: 8px;">Cadre</span>
        <h3 style="font-size: 22px; font-weight: 800; color: #082d2f; font-family: 'Roboto', sans-serif;">Support Cadre</h3>
      </div>
      <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
        @foreach($cat4 as $idx => $member)
        <div data-aos="fade-up" data-aos-delay="{{ $idx * 30 }}" style="display: flex; align-items: center; gap: 14px; background: #ffffff; padding: 14px; border-radius: 12px; border: 1.5px solid #85c9cc; box-shadow: 0 2px 10px rgba(133, 201, 204, 0.15); transition: box-shadow 0.3s, transform 0.3s;" class="hover-shadow">
          <div style="width: 54px; height: 54px; border-radius: 50%; overflow: hidden; flex-shrink: 0; border: 2px solid #85c9cc;">
            <div style="width: 100%; height: 100%; background-image: url('{{ $member->getPhotoUrl() }}'); background-size: cover; background-position: center;"></div>
          </div>
          <div>
            <h4 style="font-size: 14px; font-weight: 800; color: #082d2f; margin-bottom: 2px;">{{ $member->name }}</h4>
            <span style="font-size: 11.5px; font-weight: 600; color: #64748b;">{{ $member->designation }}</span>
          </div>
        </div>
        @endforeach
      </div>
    </div>
    @endif
  </div>
</section>
@endif

<!-- 5. FACILITIES SECTION -->
@php
  $fac1 = cms('facility1_title', 'Regulation Obstacle Field');
  $fac2 = cms('facility2_title', 'Computerized IQ Laboratory');
  $fac3 = cms('facility3_title', 'Lecturette & Viva Chambers');
  $facCount = (filled($fac1) ? 1 : 0) + (filled($fac2) ? 1 : 0) + (filled($fac3) ? 1 : 0);
@endphp
@if($facCount > 0)
<section style="background: #f4fafb; padding: 85px 24px; border-top: 1px solid rgba(133, 201, 204, 0.35); border-bottom: 1px solid rgba(133, 201, 204, 0.35);">
  <div style="max-width: 1240px; margin: 0 auto;">
    <div data-aos="fade-up" style="text-align: center; margin-bottom: 50px;">
      <span style="font-size: 11.5px; font-weight: 800; color: #082d2f; text-transform: uppercase; letter-spacing: 1px; display: inline-flex; align-items: center; gap: 6px; background: rgba(133, 201, 204, 0.22); border: 1px solid #85c9cc; padding: 5px 16px; border-radius: 9999px; margin-bottom: 12px; box-shadow: 0 2px 8px rgba(133, 201, 204, 0.25);">
        Infrastructure
      </span>
      <h2 style="font-size: clamp(28px, 4vw, 36px); font-weight: 800; color: #082d2f; font-family: 'Roboto', sans-serif;">
        Campus Facilities
      </h2>
      <div style="width: 64px; height: 3px; background: #85c9cc; margin: 16px auto 0; border-radius: 2px;"></div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
      @if(filled($fac1))
      <div class="content-panel classical-card hover-up" data-aos="fade-up" data-aos-delay="100" style="padding: 34px 28px; background: #ffffff; border-radius: 18px; box-shadow: 0 10px 25px rgba(133, 201, 204, 0.12); border: 1.5px solid #85c9cc;">
        <div style="width: 54px; height: 54px; background: #85c9cc; color: #082d2f; display: flex; align-items: center; justify-content: center; border-radius: 14px; font-size: 22px; margin-bottom: 20px; box-shadow: 0 4px 10px rgba(133, 201, 204, 0.35);">
          <i class="fa-solid fa-person-running"></i>
        </div>
        <h3 style="font-size: 18px; font-weight: 800; margin-bottom: 12px; color: #082d2f; font-family: 'Roboto', sans-serif;">
          {{ $fac1 }}
        </h3>
        @if(filled(cms('facility1_desc')))
        <p style="font-size: 14px; color: #475569; line-height: 1.7; margin: 0;">
          {{ cms('facility1_desc', 'Full-scale Progressive Group Task (PGT), Half Group Task (HGT), rope climbing, beam balance, and command task bridging structures.') }}
        </p>
        @endif
      </div>
      @endif

      @if(filled($fac2))
      <div class="content-panel classical-card hover-up" data-aos="fade-up" data-aos-delay="200" style="padding: 34px 28px; background: #ffffff; border-radius: 18px; box-shadow: 0 10px 25px rgba(133, 201, 204, 0.12); border: 1.5px solid #85c9cc;">
        <div style="width: 54px; height: 54px; background: #85c9cc; color: #082d2f; display: flex; align-items: center; justify-content: center; border-radius: 14px; font-size: 22px; margin-bottom: 20px; box-shadow: 0 4px 10px rgba(133, 201, 204, 0.35);">
          <i class="fa-solid fa-laptop-code"></i>
        </div>
        <h3 style="font-size: 18px; font-weight: 800; margin-bottom: 12px; color: #082d2f; font-family: 'Roboto', sans-serif;">
          {{ $fac2 }}
        </h3>
        @if(filled(cms('facility2_desc')))
        <p style="font-size: 14px; color: #475569; line-height: 1.7; margin: 0;">
          {{ cms('facility2_desc', 'High-speed networked terminals providing simulated computerized preliminary intelligence screenings under timed constraints.') }}
        </p>
        @endif
      </div>
      @endif

      @if(filled($fac3))
      <div class="content-panel classical-card hover-up" data-aos="fade-up" data-aos-delay="300" style="padding: 34px 28px; background: #ffffff; border-radius: 18px; box-shadow: 0 10px 25px rgba(133, 201, 204, 0.12); border: 1.5px solid #85c9cc;">
        <div style="width: 54px; height: 54px; background: #85c9cc; color: #082d2f; display: flex; align-items: center; justify-content: center; border-radius: 14px; font-size: 22px; margin-bottom: 20px; box-shadow: 0 4px 10px rgba(133, 201, 204, 0.35);">
          <i class="fa-solid fa-microphone-lines"></i>
        </div>
        <h3 style="font-size: 18px; font-weight: 800; margin-bottom: 12px; color: #082d2f; font-family: 'Roboto', sans-serif;">
          {{ $fac3 }}
        </h3>
        @if(filled(cms('facility3_desc')))
        <p style="font-size: 14px; color: #475569; line-height: 1.7; margin: 0;">
          {{ cms('facility3_desc', 'Acoustically isolated presentation suites for group discussions, impromptu speeches, and video-recorded mock interview panels.') }}
        </p>
        @endif
      </div>
      @endif
    </div>
  </div>
</section>
@endif

<!-- 6. INSTRUCTORS -->
@if(isset($instructors) && $instructors->count() > 0)
<section style="background: #ffffff; padding: 85px 24px;">
  <div style="max-width: 1240px; margin: 0 auto;">
    <div data-aos="fade-up" style="text-align: center; margin-bottom: 50px;">
      <span style="font-size: 11.5px; font-weight: 800; color: #082d2f; text-transform: uppercase; letter-spacing: 1px; display: inline-flex; align-items: center; gap: 6px; background: rgba(133, 201, 204, 0.22); border: 1px solid #85c9cc; padding: 5px 16px; border-radius: 9999px; margin-bottom: 12px; box-shadow: 0 2px 8px rgba(133, 201, 204, 0.25);">
        Direct Military Mentorship
      </span>
      <h2 style="font-size: clamp(28px, 4vw, 36px); font-weight: 800; color: #082d2f; font-family: 'Roboto', sans-serif;">
        Distinguished Academy Faculty
      </h2>
      <div style="width: 64px; height: 3px; background: #85c9cc; margin: 16px auto 0; border-radius: 2px;"></div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px;">
      @foreach($instructors as $idx => $inst)
        <div class="content-panel classical-card hover-up" data-aos="fade-up" data-aos-delay="{{ ($idx % 3 + 1) * 100 }}" style="text-align: center; padding: 40px 24px; background: #fff; border-radius: 18px; box-shadow: 0 10px 30px rgba(133, 201, 204, 0.15); border: 1.5px solid #85c9cc; transition: transform 0.3s ease;">
          <div style="width: 80px; height: 80px; border-radius: 22px; background: #85c9cc; color: #082d2f; display: grid; place-items: center; font-size: 32px; margin: 0 auto 20px; box-shadow: 0 8px 20px rgba(133, 201, 204, 0.35);">
            <i class="fa-solid fa-user-shield"></i>
          </div>
          <h3 style="font-size: 19px; font-weight: 800; color: #082d2f; font-family: 'Roboto', sans-serif; margin-bottom: 4px;">{{ $inst->user->name }}</h3>
          <p style="font-size: 13px; font-weight: 700; color: #0a5c60; margin-bottom: 12px;">{{ $inst->designation }}</p>
          @if($inst->specialization)
          <div style="display: inline-block; background: rgba(133, 201, 204, 0.22); color: #082d2f; padding: 4px 14px; border-radius: 20px; font-size: 11px; font-weight: 700; margin-bottom: 16px; border: 1px solid #85c9cc;">{{ $inst->specialization }}</div>
          @endif
          <p style="font-size: 14px; color: #64748b; line-height: 1.65; margin: 0;">{{ $inst->bio }}</p>
        </div>
      @endforeach
    </div>
  </div>
</section>
@endif

<style>
  /* Extra Animation Styles */
  .hover-scale {
    transition: transform 0.5s ease;
  }
  .hover-scale:hover {
    transform: scale(1.05);
  }
  .team-card-large:hover .hover-scale,
  .team-card-medium:hover .hover-scale {
    transform: scale(1.05);
  }
  .hover-up {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  .hover-up:hover {
    transform: translateY(-8px);
  }
  .hover-shadow {
    transition: box-shadow 0.3s ease, transform 0.3s ease;
  }
  .hover-shadow:hover {
    box-shadow: 0 8px 20px rgba(133, 201, 204, 0.25) !important;
    transform: translateY(-2px);
  }
  .team-card-large {
    transition: box-shadow 0.3s ease, transform 0.3s ease;
  }
  .team-card-large:hover {
    box-shadow: 0 25px 60px rgba(133, 201, 204, 0.45) !important;
    transform: translateY(-4px);
  }
  .team-card-medium {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  .team-card-medium:hover {
    box-shadow: 0 15px 35px rgba(133, 201, 204, 0.25) !important;
    transform: translateY(-4px);
  }
  @media (max-width: 768px) {
    .team-card-large {
      flex-direction: column;
    }
  }
</style>

@endsection