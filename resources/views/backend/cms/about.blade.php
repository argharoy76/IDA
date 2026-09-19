@extends('layouts.portal')

@section('title', 'About Us - Web Management')
@section('page_title', 'About Page Editor')
@section('page_subtitle', 'Manage mission statement, strategic vision, campus history, and the 3 specialized facility cards')

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px;">

  @include('backend.cms.partials.nav')

  @if(session('success'))
    <div class="alert alert-success" style="background: rgba(16, 185, 129, 0.15); border: 1px solid var(--brand-mint); color: #065f46; padding: 12px 16px; border-radius: var(--radius-sm); font-size: 13px; font-weight: 600;">
      <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
  @endif

  <form action="{{ route('admin.cms.settings') }}" method="POST">
    @csrf

    <div class="tactical-card" style="margin-bottom: 24px;">
      <div style="border-bottom: 1px solid var(--border-soft); padding-bottom: 14px; margin-bottom: 20px;">
        <h3 style="font-size: 17px; font-weight: 800; color: var(--brand-deep); margin: 0 0 6px 0;">
          <i class="fa-solid fa-landmark" style="color: var(--brand-emerald);"></i> About Us Header & Institutional Creed
        </h3>
        <p style="font-size: 12.5px; color: var(--text-muted); margin: 0;">
          Update the institutional statements, history, and core objectives displayed on <code>/about</code>.
        </p>
      </div>

      <div style="display: flex; flex-direction: column; gap: 16px;">
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 16px;">
          <div>
            <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Eyebrow Badge</label>
            <input type="text" name="about_badge" value="{{ cms('about_badge', cms('about_page_badge', '★ ESTABLISHED IN KHULNA ★')) }}" class="form-tactical">
          </div>
          <div>
            <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Page Heading</label>
            <input type="text" name="about_title" value="{{ cms('about_title', cms('about_page_title', 'About Imperial Defence Academy')) }}" class="form-tactical">
          </div>
        </div>

        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Page Subtitle</label>
          <textarea name="about_subtitle" rows="2" class="form-tactical">{{ cms('about_subtitle', cms('about_page_subtitle', 'A specialized digital and ground training academy dedicated to training, mentoring, and commissioning the future officer corps of the Bangladesh Armed Forces.')) }}</textarea>
        </div>

        <!-- Mission & Vision -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
          <div style="background: var(--surface-subtle); border: 1px solid var(--border-soft); border-radius: var(--radius-sm); padding: 16px;">
            <label style="display: block; font-size: 12px; font-weight: 800; text-transform: uppercase; color: var(--brand-emerald); margin-bottom: 6px;">Mission Statement Title</label>
            <input type="text" name="mission_title" value="{{ cms('mission_title', cms('about_mission_title', 'Our Mission')) }}" class="form-tactical" style="margin-bottom: 10px;">
            <label style="display: block; font-size: 11px; font-weight: 700; margin-bottom: 4px;">Mission Description</label>
            <textarea name="about_mission" rows="4" class="form-tactical">{{ cms('about_mission', 'To provide rigorous, honest, and scientifically structured preparatory training that instills moral courage, rapid tactical decision-making, physical agility, and authentic Officer-Like Qualities (OLQ) in every candidate, preparing them to successfully conquer the ISSB board.') }}</textarea>
          </div>

          <div style="background: var(--surface-subtle); border: 1px solid var(--border-soft); border-radius: var(--radius-sm); padding: 16px;">
            <label style="display: block; font-size: 12px; font-weight: 800; text-transform: uppercase; color: var(--accent-gold); margin-bottom: 6px;">Strategic Vision Title</label>
            <input type="text" name="vision_title" value="{{ cms('vision_title', cms('about_vision_title', 'Our Vision')) }}" class="form-tactical" style="margin-bottom: 10px;">
            <label style="display: block; font-size: 11px; font-weight: 700; margin-bottom: 4px;">Vision Description</label>
            <textarea name="about_vision" rows="4" class="form-tactical">{{ cms('about_vision', 'To establish Imperial Defence Academy as the most technologically advanced and trusted military preparatory institution in Bangladesh, continuously bridging digital simulation with real-world physical conditioning.') }}</textarea>
          </div>
        </div>
      </div>
    </div>

    <!-- 3 Campus Facilities -->
    <div class="tactical-card" style="margin-bottom: 24px;">
      <div style="border-bottom: 1px solid var(--border-soft); padding-bottom: 14px; margin-bottom: 20px;">
        <h3 style="font-size: 17px; font-weight: 800; color: var(--brand-deep); margin: 0 0 6px 0;">
          <i class="fa-solid fa-building-columns" style="color: var(--accent-gold);"></i> 3 Campus Facilities & Training Grounds
        </h3>
        <p style="font-size: 12.5px; color: var(--text-muted); margin: 0;">
          Update the titles and descriptions of the 3 facility feature cards displayed on the About page.
        </p>
      </div>

      <div style="display: flex; flex-direction: column; gap: 16px;">
        <!-- Facility 1 -->
        <div style="background: var(--surface-subtle); border: 1px solid var(--border-soft); border-radius: var(--radius-sm); padding: 16px;">
          <label style="display: block; font-size: 12px; font-weight: 800; color: var(--brand-emerald); text-transform: uppercase; margin-bottom: 4px;">Facility 1: Ground Tasks</label>
          <input type="text" name="facility1_title" value="{{ cms('facility1_title', 'Regulation Obstacle Field') }}" class="form-tactical" style="margin-bottom: 8px;">
          <textarea name="facility1_desc" rows="2" class="form-tactical">{{ cms('facility1_desc', 'Full-scale Progressive Group Task (PGT), Half Group Task (HGT), rope climbing, beam balance, and command task bridging structures.') }}</textarea>
        </div>

        <!-- Facility 2 -->
        <div style="background: var(--surface-subtle); border: 1px solid var(--border-soft); border-radius: var(--radius-sm); padding: 16px;">
          <label style="display: block; font-size: 12px; font-weight: 800; color: var(--brand-emerald); text-transform: uppercase; margin-bottom: 4px;">Facility 2: Psychological Lab</label>
          <input type="text" name="facility2_title" value="{{ cms('facility2_title', 'Computerized IQ Laboratory') }}" class="form-tactical" style="margin-bottom: 8px;">
          <textarea name="facility2_desc" rows="2" class="form-tactical">{{ cms('facility2_desc', 'High-speed networked terminals providing simulated computerized preliminary intelligence screenings under timed constraints.') }}</textarea>
        </div>

        <!-- Facility 3 -->
        <div style="background: var(--surface-subtle); border: 1px solid var(--border-soft); border-radius: var(--radius-sm); padding: 16px;">
          <label style="display: block; font-size: 12px; font-weight: 800; color: var(--brand-emerald); text-transform: uppercase; margin-bottom: 4px;">Facility 3: Interview Suite</label>
          <input type="text" name="facility3_title" value="{{ cms('facility3_title', 'Lecturette & Viva Chambers') }}" class="form-tactical" style="margin-bottom: 8px;">
          <textarea name="facility3_desc" rows="2" class="form-tactical">{{ cms('facility3_desc', 'Acoustically isolated presentation suites for group discussions, impromptu speeches, and video-recorded mock interview panels.') }}</textarea>
        </div>
      </div>
    </div>

    <!-- Sticky Save Bar -->
    <div style="display: flex; justify-content: flex-end; gap: 12px; position: sticky; bottom: 20px; z-index: 10; background: rgba(255,255,255,0.95); padding: 14px 20px; border-radius: var(--radius-sm); border: 1px solid var(--border-soft); box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
      <a href="{{ url('/about') }}" target="_blank" class="btn-tactical btn-tactical-outline">
        <i class="fa-solid fa-eye"></i> View Live About Page
      </a>
      <button type="submit" class="btn-tactical btn-tactical-primary" style="padding: 10px 24px; font-size: 13.5px;">
        <i class="fa-solid fa-floppy-disk"></i> Save About Page Settings
      </button>
    </div>
  </form>

</div>
@endsection
