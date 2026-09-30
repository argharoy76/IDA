@extends('layouts.portal')

@section('title', 'Home Page - Web Management')
@section('page_title', 'Home Page Editor')
@section('page_subtitle', 'Manage home page content sections in order from top to bottom')

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px;">

  @include('backend.cms.partials.nav')

  @if(session('success'))
    <div class="alert alert-success" style="background: rgba(16, 185, 129, 0.15); border: 1px solid var(--brand-mint); color: #065f46; padding: 12px 16px; border-radius: var(--radius-sm); font-size: 13px; font-weight: 600;">
      <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger" style="background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; color: #b91c1c; padding: 12px 16px; border-radius: var(--radius-sm); font-size: 13px; font-weight: 600;">
      <i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}
    </div>
  @endif

  <!-- Notice -->
  <div style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(52, 211, 153, 0.25); border-left: 4px solid var(--brand-mint); border-radius: 12px; padding: 13px 18px; font-size: 13px; color: #cbd5e1; display: flex; align-items: center; gap: 12px;">
    <i class="fa-solid fa-circle-info" style="color: var(--brand-mint); font-size: 16px; flex-shrink: 0;"></i>
    <div>
      <strong style="color: #ffffff;">Text Editability & Removability:</strong>
      All fields below are arranged to match the website from top to bottom. Any field left empty is completely removed from the live website.
    </div>
  </div>

  @php
    $activeSlides = cms_hero_slides();
    $activeLogo = cms('site_logo') ?: cms('site_crest');
    $hasCustomLogo = !empty($activeLogo) && file_exists(public_path(ltrim($activeLogo, '/')));
    
    // Parse selected featured courses
    $rawFeatured = cms('featured_courses');
    $selectedCourseIds = [];
    if ($rawFeatured !== null && $rawFeatured !== '') {
        $decoded = is_array($rawFeatured) ? $rawFeatured : json_decode($rawFeatured, true);
        $selectedCourseIds = is_array($decoded) ? $decoded : [];
    } else {
        $selectedCourseIds = isset($courses) ? $courses->where('is_featured', true)->pluck('id')->toArray() : [];
        if (empty($selectedCourseIds) && isset($courses)) {
            $selectedCourseIds = $courses->take(4)->pluck('id')->toArray();
        }
    }
  @endphp

  <!-- Academy Logo Status Card -->
  <div style="background: #11141d; border: 1px solid rgba(255, 255, 255, 0.08); border-left: 4px solid var(--accent-gold); border-radius: 12px; padding: 14px 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
    <div style="display: flex; align-items: center; gap: 14px;">
      <div style="width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; background: #070a12; border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; padding: 3px;">
        @include('frontend.partials.logo_crest', ['size' => 38])
      </div>
      <div>
        <div style="display: flex; align-items: center; gap: 8px;">
          <strong style="color: #ffffff; font-size: 13.5px;">Academy Logo</strong>
          <span class="badge {{ $hasCustomLogo ? 'badge-emerald' : 'badge-gold' }}" style="font-size: 10px; padding: 3px 8px;">
            {{ $hasCustomLogo ? 'Custom Logo Active' : 'Official Logo Active' }}
          </span>
        </div>
        <span style="font-size: 12px; color: #94a3b8;">
          Active on Navbar, Hero Card, and Footer.
        </span>
      </div>
    </div>
    <a href="{{ route('admin.cms.branding') }}" class="btn-tactical btn-tactical-primary" style="font-size: 12px; padding: 8px 18px; gap: 6px; font-weight: 700;">
      <i class="fa-solid fa-cloud-arrow-up"></i> Manage Logo &rarr;
    </a>
  </div>

  <!-- ========================================================================= -->
  <!-- 1. BACKGROUND IMAGE (HERO SECTION BACKGROUND) -->
  <!-- ========================================================================= -->
  <div class="tactical-card" style="margin-bottom: 24px; border-left: 4px solid var(--accent-gold);">
    <div style="border-bottom: 1px solid rgba(255, 255, 255, 0.08); padding-bottom: 14px; margin-bottom: 20px;">
      <h3 style="font-size: 16px; font-weight: 800; color: #ffffff; margin: 0; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-image" style="color: var(--accent-gold);"></i> Background Image
      </h3>
    </div>

    <!-- Active Slides Grid with Delete and Set as Primary Action -->
    <div style="margin-bottom: 20px;">
      <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 12px;">
        Current Background Image(s):
      </label>

      <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px;">
        @foreach($activeSlides as $idx => $slide)
          @php
            $slideUrl = str_starts_with($slide, 'http') ? $slide : asset($slide);
            $isPrimary = ($idx === 0);
          @endphp
          <div style="background: #141722; border: 1.5px solid {{ $isPrimary ? 'var(--accent-gold)' : 'rgba(255, 255, 255, 0.08)' }}; border-radius: 12px; overflow: hidden; position: relative; box-shadow: 0 4px 14px rgba(0,0,0,0.3); display: flex; flex-direction: column;">
            <div style="height: 190px; overflow: hidden; position: relative; background: #0b1329;">
              <img src="{{ $slideUrl }}" alt="Slide {{ $idx + 1 }}" style="width: 100%; height: 100%; object-fit: cover;">
              @if($isPrimary)
                <span class="badge" style="position: absolute; top: 6px; left: 6px; font-size: 10px; background: rgba(5, 11, 20, 0.9); color: var(--accent-gold); border: 1px solid var(--accent-gold); font-weight: 800;">
                  ★ Primary Background
                </span>
              @else
                <span class="badge badge-primary" style="position: absolute; top: 6px; left: 6px; font-size: 10px; background: rgba(5, 11, 20, 0.85); color: #94a3b8; border: 1px solid rgba(255, 255, 255, 0.2);">
                  Slide #{{ $idx + 1 }}
                </span>
              @endif
            </div>
            <div style="padding: 10px 12px; background: #11141d; border-top: 1px solid rgba(255,255,255,0.06); display: flex; justify-content: space-between; align-items: center; gap: 8px;">
              <span style="font-size: 11px; color: #94a3b8; text-overflow: ellipsis; overflow: hidden; white-space: nowrap; max-width: 110px;" title="{{ basename($slide) }}">
                {{ basename($slide) }}
              </span>
              <div style="display: flex; gap: 6px; align-items: center;">
                @if(!$isPrimary)
                  <form action="{{ route('admin.cms.hero_slides.primary', $idx) }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn-tactical btn-tactical-outline" style="color: var(--accent-gold); border-color: rgba(212,175,55,0.4); font-size: 10.5px; padding: 4px 8px;" title="Set as primary background image">
                      <i class="fa-solid fa-star"></i> Set Primary
                    </button>
                  </form>
                @endif
                <form action="{{ route('admin.cms.hero_slides.delete', $idx) }}" method="POST" onsubmit="return confirm('Remove this image from the background?');" style="margin: 0;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn-tactical btn-tactical-outline" style="color: #ef4444; border-color: rgba(239,68,68,0.3); font-size: 11px; padding: 4px 8px;" title="Delete image">
                    <i class="fa-solid fa-trash-can"></i>
                  </button>
                </form>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>

    <!-- Upload Photo Section -->
    <div style="background: #10141f; border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 12px; padding: 16px 20px;">
      <form action="{{ route('admin.cms.hero_slides.add') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label style="display: block; font-size: 12px; font-weight: 700; color: #cbd5e1; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px;">
          Upload Background Image(s)
        </label>
        <div style="display: flex; flex-direction: column; gap: 12px;">
          <div style="display: flex; flex-wrap: wrap; gap: 14px; align-items: center;">
            <input type="file" id="hero-photo-input" name="slide_images[]" multiple class="form-tactical" accept="image/*,.heic,.heif" style="max-width: 360px; background: #161a26; border: 1px solid rgba(255,255,255,0.14); color: #ffffff; padding: 9px 12px; border-radius: 8px;" onchange="handleHeroPhotoSelect(this)">
            <span style="color: #64748b; font-size: 12px; font-weight: 600;">OR</span>
            <input type="url" name="slide_url" class="form-tactical" placeholder="e.g. https://images.unsplash.com/photo-..." style="max-width: 380px;" oninput="handleHeroUrlInput(this)">
          </div>
          <div id="add-slides-wrapper" style="display: none;">
            <button type="submit" id="add-slides-btn" class="btn-tactical btn-tactical-primary" style="padding: 10px 24px; font-size: 13px; font-weight: 700; background: var(--brand-mint); border-color: var(--brand-mint); color: #022c22; box-shadow: 0 0 16px rgba(16, 185, 129, 0.3);">
              <i class="fa-solid fa-plus"></i> Add Background Image
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <script>
    function handleHeroPhotoSelect(input) {
      var wrapper = document.getElementById('add-slides-wrapper');
      if (wrapper) {
        if (input.files && input.files.length > 0) {
          wrapper.style.display = 'inline-flex';
        } else {
          wrapper.style.display = 'none';
        }
      }
    }
    function handleHeroUrlInput(input) {
      var wrapper = document.getElementById('add-slides-wrapper');
      if (wrapper) {
        if (input.value && input.value.trim().length > 5) {
          wrapper.style.display = 'inline-flex';
        } else {
          var fileInput = document.getElementById('hero-photo-input');
          if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
            wrapper.style.display = 'none';
          }
        }
      }
    }
    document.addEventListener('DOMContentLoaded', function() {
      var input = document.getElementById('hero-photo-input');
      if (input && input.files && input.files.length > 0) {
        handleHeroPhotoSelect(input);
      }
    });
  </script>

  <!-- MAIN HOMEPAGE SETTINGS FORM -->
  <form action="{{ route('admin.cms.settings') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="form_section" value="home">

    <!-- ========================================================================= -->
    <!-- 2. HERO CONTENT (LEFT HERO SIDE) -->
    <!-- ========================================================================= -->
    <div class="tactical-card" style="margin-bottom: 24px; border-left: 4px solid #38bdf8;">
      <div style="border-bottom: 1px solid rgba(255, 255, 255, 0.08); padding-bottom: 14px; margin-bottom: 20px;">
        <h3 style="font-size: 16px; font-weight: 800; color: #ffffff; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-heading" style="color: #38bdf8;"></i> Hero Content
        </h3>
      </div>

      <div style="display: flex; flex-direction: column; gap: 16px;">
        
        <!-- Top Badge -->
        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px;">
            Top Badge
          </label>
          <input type="text" name="hero_badge" value="{{ cms('hero_badge') }}" class="form-tactical" placeholder="e.g. ★ BANGLADESH ARMED FORCES PREPARATORY WING ★">
        </div>

        <!-- 3-Part Headline -->
        <div style="display: grid; grid-template-columns: 1.1fr 1.8fr 1.1fr; gap: 14px;">
          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px;">
              Heading Prefix
            </label>
            <input type="text" name="hero_title_prefix" value="{{ cms('hero_title_prefix', 'Forge Your Legacy in the') }}" class="form-tactical" placeholder="e.g. Forge Your Legacy in the">
          </div>
          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px;">
              Rotating Words <small style="text-transform: none; color: #f59e0b; font-weight: 400;">(comma-separated)</small>
            </label>
            <input type="text" name="hero_rotating_words" value="{{ cms('hero_rotating_words', 'Bangladesh Army, Bangladesh Navy, Bangladesh Air Force, ISSB Screening Board') }}" class="form-tactical" placeholder="e.g. Bangladesh Army, Bangladesh Navy, Bangladesh Air Force, ISSB Board">
          </div>
          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px;">
              Heading Suffix
            </label>
            <input type="text" name="hero_title_suffix" value="{{ cms('hero_title_suffix', 'Officer Cadet Preparatory Wing') }}" class="form-tactical" placeholder="e.g. Officer Cadet Preparatory Wing">
          </div>
        </div>

        <!-- Subheading Paragraph -->
        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px;">
            Subheading
          </label>
          <textarea name="hero_subheading" rows="3" class="form-tactical" placeholder="e.g. Khulna's premier academy for BMA Long Course, Navy Officer Cadet, BAFA Flight Wing, and comprehensive ISSB preparation guided by retired armed forces officers.">{{ cms('hero_subheading', "Khulna's premier academy for BMA Long Course, Navy Officer Cadet, BAFA Flight Wing, and comprehensive ISSB preparation guided by retired armed forces officers.") }}</textarea>
        </div>

        <!-- Action Buttons -->
        <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 10px; padding: 16px;">
          <h4 style="font-size: 12px; font-weight: 800; text-transform: uppercase; margin: 0 0 12px 0; color: #cbd5e1; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-link" style="color: var(--brand-mint);"></i> Action Buttons
          </h4>
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 14px;">
            <div>
              <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px; color: #94a3b8;">Button 1 (Primary)</label>
              <div style="display: flex; gap: 8px;">
                <input type="text" name="hero_btn1_text" value="{{ cms('hero_btn1_text', 'Online Admission →') }}" class="form-tactical" placeholder="e.g. Online Admission →">
                <input type="text" name="hero_btn1_url" value="{{ cms('hero_btn1_url', route('register')) }}" class="form-tactical" placeholder="e.g. {{ route('register') }}">
              </div>
            </div>
            <div>
              <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px; color: #94a3b8;">Button 2 (Outline)</label>
              <div style="display: flex; gap: 8px;">
                <input type="text" name="hero_btn2_text" value="{{ cms('hero_btn2_text', 'Explore Courses') }}" class="form-tactical" placeholder="e.g. Explore Courses">
                <input type="text" name="hero_btn2_url" value="{{ cms('hero_btn2_url', route('courses')) }}" class="form-tactical" placeholder="e.g. {{ route('courses') }}">
              </div>
            </div>
            <div>
              <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px; color: #94a3b8;">Button 3 (Gold)</label>
              <div style="display: flex; gap: 8px;">
                <input type="text" name="hero_btn3_text" value="{{ cms('hero_btn3_text', 'Online Assessment') }}" class="form-tactical" placeholder="e.g. Online Assessment">
                <input type="text" name="hero_btn3_url" value="{{ cms('hero_btn3_url', route('online_tests')) }}" class="form-tactical" placeholder="e.g. {{ route('online_tests') }}">
              </div>
            </div>
          </div>
        </div>

        <!-- Trust Badges -->
        <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 10px; padding: 16px;">
          <h4 style="font-size: 12px; font-weight: 800; text-transform: uppercase; margin: 0 0 12px 0; color: #cbd5e1; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-certificate" style="color: var(--accent-gold);"></i> Trust Badges
          </h4>
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px;">
            <div>
              <label style="display: block; font-size: 11px; font-weight: 700; margin-bottom: 4px; color: #94a3b8;">Badge 1</label>
              <input type="text" name="stat_pill_1" value="{{ cms('stat_pill_1') }}" class="form-tactical" placeholder="e.g. 98.4% Preliminary Screening Pass">
            </div>
            <div>
              <label style="display: block; font-size: 11px; font-weight: 700; margin-bottom: 4px; color: #94a3b8;">Badge 2</label>
              <input type="text" name="stat_pill_2" value="{{ cms('stat_pill_2') }}" class="form-tactical" placeholder="e.g. 500+ Commissioned Officers">
            </div>
            <div>
              <label style="display: block; font-size: 11px; font-weight: 700; margin-bottom: 4px; color: #94a3b8;">Badge 3</label>
              <input type="text" name="stat_pill_3" value="{{ cms('stat_pill_3') }}" class="form-tactical" placeholder="e.g. 15 OLQ Standard Drill">
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 3. SHOWCASE CARD & LIVE STATISTICS (RIGHT HERO SIDE) -->
    <!-- ========================================================================= -->
    <div class="tactical-card" style="margin-bottom: 24px; border-left: 4px solid var(--accent-gold);">
      <div style="border-bottom: 1px solid rgba(255, 255, 255, 0.08); padding-bottom: 14px; margin-bottom: 20px;">
        <h3 style="font-size: 16px; font-weight: 800; color: #ffffff; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-id-card" style="color: var(--accent-gold);"></i> Showcase Card & Live Statistics
        </h3>
      </div>

      <div style="display: flex; flex-direction: column; gap: 16px;">
        
        <!-- Card Title & Tagline -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px;">
              Card Title
            </label>
            <input type="text" name="showcase_title" value="{{ cms('showcase_title', cms('site_name', 'IMPERIAL DEFENCE ACADEMY')) }}" class="form-tactical" placeholder="e.g. Imperial Defence Academy (IDA)">
          </div>
          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px;">
              Card Tagline
            </label>
            <input type="text" name="showcase_tagline" value="{{ cms('showcase_tagline', cms('site_tagline', 'Courage • Character • Commission')) }}" class="form-tactical" placeholder="e.g. Courage • Character • Commission">
          </div>
        </div>

        <!-- 3 Statistics Counters -->
        <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 10px; padding: 16px;">
          <h4 style="font-size: 12px; font-weight: 800; text-transform: uppercase; margin: 0 0 12px 0; color: #cbd5e1;">
            Statistics Counters
          </h4>
          
          <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; margin-bottom: 14px;">
            <!-- Counter 1: Active Cadets -->
            <div style="background: #10141f; padding: 12px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.08);">
              <label style="display: block; font-size: 11px; font-weight: 700; color: #38bdf8; margin-bottom: 4px; text-transform: uppercase;">Counter 1</label>
              <input type="text" name="stat_box1_label" value="{{ cms('stat_box1_label', 'Active Cadets') }}" class="form-tactical" style="margin-bottom: 6px;" placeholder="e.g. Active Cadets">
              <input type="number" name="stat_total_cadets" value="{{ cms('stat_total_cadets') }}" class="form-tactical" placeholder="Live count if empty">
            </div>

            <!-- Counter 2: Recommended Cadets -->
            <div style="background: #10141f; padding: 12px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.08);">
              <label style="display: block; font-size: 11px; font-weight: 700; color: var(--accent-gold); margin-bottom: 4px; text-transform: uppercase;">Counter 2</label>
              <input type="text" name="stat_box2_label" value="{{ cms('stat_box2_label', 'Recommended') }}" class="form-tactical" style="margin-bottom: 6px;" placeholder="e.g. Recommended">
              <input type="number" name="stat_recommended_cadets" value="{{ cms('stat_recommended_cadets', 148) }}" class="form-tactical" placeholder="e.g. 148">
            </div>

            <!-- Counter 3: Active Squads -->
            <div style="background: #10141f; padding: 12px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.08);">
              <label style="display: block; font-size: 11px; font-weight: 700; color: var(--brand-mint); margin-bottom: 4px; text-transform: uppercase;">Counter 3</label>
              <input type="text" name="stat_box3_label" value="{{ cms('stat_box3_label', 'Active Squads') }}" class="form-tactical" style="margin-bottom: 6px;" placeholder="e.g. Active Squads">
              <input type="number" name="stat_active_batches" value="{{ cms('stat_active_batches') }}" class="form-tactical" placeholder="Live count if empty">
            </div>
          </div>

          <!-- Status Beacon -->
          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px;">
              Live Status Beacon
            </label>
            <input type="text" name="hero_batch_status" value="{{ cms('hero_batch_status') }}" class="form-tactical" placeholder="e.g. Current Batch: 95th BMA Long Course Active">
          </div>
        </div>

      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 4. BULLETIN MARQUEE TICKER (UNDER HERO ON LIVE SITE) -->
    <!-- ========================================================================= -->
    <div class="tactical-card" style="margin-bottom: 24px; border-left: 4px solid #f59e0b;">
      <div style="border-bottom: 1px solid rgba(255, 255, 255, 0.08); padding-bottom: 14px; margin-bottom: 20px;">
        <h3 style="font-size: 16px; font-weight: 800; color: #ffffff; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-bullhorn" style="color: #f59e0b;"></i> Bulletin Marquee
        </h3>
      </div>

      <div style="display: flex; flex-direction: column; gap: 14px;">
        
        <!-- Bulletin Status Control (Live vs Offline) -->
        <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 8px; padding: 12px 16px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
          <div>
            <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #cbd5e1; display: block;">
              Bulletin Status
            </span>
            <span style="font-size: 11.5px; color: #94a3b8;">
              When live, the ticker displays on the website; when offline, it is hidden.
            </span>
          </div>
          <div style="display: flex; gap: 12px; align-items: center;">
            <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer; color: #ffffff; font-size: 12.5px; font-weight: 600; padding: 6px 14px; background: {{ cms('marquee_is_live', '1') == '1' ? 'rgba(16, 185, 129, 0.15)' : 'rgba(255,255,255,0.03)' }}; border: 1px solid {{ cms('marquee_is_live', '1') == '1' ? 'rgba(16, 185, 129, 0.5)' : 'rgba(255, 255, 255, 0.1)' }}; border-radius: 6px;">
              <input type="radio" name="marquee_is_live" value="1" {{ cms('marquee_is_live', '1') == '1' ? 'checked' : '' }} style="accent-color: #10b981;">
              <span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981; display: inline-block;"></span>
              Live (Shown on Website)
            </label>
            <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer; color: #94a3b8; font-size: 12.5px; font-weight: 600; padding: 6px 14px; background: {{ cms('marquee_is_live', '1') == '0' ? 'rgba(239, 68, 68, 0.12)' : 'rgba(255,255,255,0.03)' }}; border: 1px solid {{ cms('marquee_is_live', '1') == '0' ? 'rgba(239, 68, 68, 0.4)' : 'rgba(255, 255, 255, 0.1)' }}; border-radius: 6px;">
              <input type="radio" name="marquee_is_live" value="0" {{ cms('marquee_is_live', '1') == '0' ? 'checked' : '' }} style="accent-color: #ef4444;">
              <span style="width: 8px; height: 8px; border-radius: 50%; background: #ef4444; display: inline-block;"></span>
              Offline / Hidden (Not Shown)
            </label>
          </div>
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px;">
            Moving Announcement Text (Scrolling Marquee)
          </label>
          <input type="text" name="marquee_text" value="{{ cms('marquee_text', 'BMA 95th Long Course & Navy 2026-B Batches Enrolling Now ★ Free Dossier Screening Every Friday at Khulna HQ ★') }}" class="form-tactical" placeholder="e.g. BMA 95th Long Course & Navy 2026-B Batches Enrolling Now ★ Free Dossier Screening Every Friday at Khulna HQ ★">
        </div>
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 5. THE FOUR PILLARS (NEXT ON LIVE SITE) -->
    <!-- ========================================================================= -->
    <div class="tactical-card" style="margin-bottom: 24px; border-left: 4px solid #a855f7;">
      <div style="border-bottom: 1px solid rgba(255, 255, 255, 0.08); padding-bottom: 14px; margin-bottom: 20px;">
        <h3 style="font-size: 16px; font-weight: 800; color: #ffffff; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-cubes-stacked" style="color: #a855f7;"></i> The Four Pillars
        </h3>
      </div>

      <div style="display: flex; flex-direction: column; gap: 16px;">
        <!-- Section Headers -->
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 14px;">
          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px;">Section Tag</label>
            <input type="text" name="pillars_tag" value="{{ cms('pillars_tag', 'Comprehensive Cadet Curriculum') }}" class="form-tactical" placeholder="e.g. Comprehensive Cadet Curriculum">
          </div>
          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px;">Section Heading</label>
            <input type="text" name="pillars_heading" value="{{ cms('pillars_heading', 'The Four Pillars of IDA Preparation') }}" class="form-tactical" placeholder="e.g. The Four Pillars of IDA Preparation">
          </div>
        </div>

        <!-- 4 Pillars Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 14px;">
          <!-- Pillar 1 -->
          <div style="background: rgba(255, 255, 255, 0.03); padding: 14px; border-radius: 10px; border: 1px solid rgba(255, 255, 255, 0.08);">
            <label style="display: block; font-size: 11px; font-weight: 700; color: #38bdf8; margin-bottom: 6px; text-transform: uppercase;">Pillar 1</label>
            <input type="text" name="pillar1_title" value="{{ cms('pillar1_title', 'Verbal & Non-Verbal IQ') }}" class="form-tactical" style="margin-bottom: 8px;" placeholder="e.g. Verbal & Non-Verbal IQ">
            <textarea name="pillar1_desc" rows="3" class="form-tactical" placeholder="e.g. Fast-paced computerized intelligence screening, matrix pattern puzzles, spatial reasoning...">{{ cms('pillar1_desc', 'Fast-paced computerized intelligence screening, matrix pattern puzzles, spatial reasoning, and negative marking calibration.') }}</textarea>
          </div>

          <!-- Pillar 2 -->
          <div style="background: rgba(255, 255, 255, 0.03); padding: 14px; border-radius: 10px; border: 1px solid rgba(255, 255, 255, 0.08);">
            <label style="display: block; font-size: 11px; font-weight: 700; color: var(--accent-gold); margin-bottom: 6px; text-transform: uppercase;">Pillar 2</label>
            <input type="text" name="pillar2_title" value="{{ cms('pillar2_title', 'Psychological Battery') }}" class="form-tactical" style="margin-bottom: 8px;" placeholder="e.g. Psychological Battery">
            <textarea name="pillar2_desc" rows="3" class="form-tactical" placeholder="e.g. Timed Word Association Test (WAT), Picture Perception (PPDT), Thematic Apperception (TAT)...">{{ cms('pillar2_desc', 'Timed Word Association Test (WAT), Picture Perception (PPDT), Thematic Apperception (TAT), and Situation Reaction Tests.') }}</textarea>
          </div>

          <!-- Pillar 3 -->
          <div style="background: rgba(255, 255, 255, 0.03); padding: 14px; border-radius: 10px; border: 1px solid rgba(255, 255, 255, 0.08);">
            <label style="display: block; font-size: 11px; font-weight: 700; color: var(--brand-mint); margin-bottom: 6px; text-transform: uppercase;">Pillar 3</label>
            <input type="text" name="pillar3_title" value="{{ cms('pillar3_title', 'Ground Tasks (GTO)') }}" class="form-tactical" style="margin-bottom: 8px;" placeholder="e.g. Ground Tasks (GTO)">
            <textarea name="pillar3_desc" rows="3" class="form-tactical" placeholder="e.g. Full-scale Progressive Group Tasks (PGT), Half Group Tasks (HGT), Command Tasks...">{{ cms('pillar3_desc', 'Full-scale Progressive Group Tasks (PGT), Half Group Tasks (HGT), Command Tasks, and individual obstacle courses in Boyra.') }}</textarea>
          </div>

          <!-- Pillar 4 -->
          <div style="background: rgba(255, 255, 255, 0.03); padding: 14px; border-radius: 10px; border: 1px solid rgba(255, 255, 255, 0.08);">
            <label style="display: block; font-size: 11px; font-weight: 700; color: #f43f5e; margin-bottom: 6px; text-transform: uppercase;">Pillar 4</label>
            <input type="text" name="pillar4_title" value="{{ cms('pillar4_title', 'Interview & Viva Voce') }}" class="form-tactical" style="margin-bottom: 8px;" placeholder="e.g. Interview & Viva Voce">
            <textarea name="pillar4_desc" rows="3" class="form-tactical" placeholder="e.g. 1-on-1 mock interviews conducted by retired senior defense officers focusing on OLQ...">{{ cms('pillar4_desc', '1-on-1 mock interviews conducted by retired senior defense officers focusing on Officer-Like Qualities (OLQ) and composure.') }}</textarea>
          </div>
        </div>

      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 6. 15 OFFICER-LIKE QUALITIES (OLQ) MATRIX (NEXT ON LIVE SITE) -->
    <!-- ========================================================================= -->
    <div class="tactical-card" style="margin-bottom: 24px; border-left: 4px solid #eab308;">
      <div style="border-bottom: 1px solid rgba(255, 255, 255, 0.08); padding-bottom: 14px; margin-bottom: 20px;">
        <h3 style="font-size: 16px; font-weight: 800; color: #ffffff; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-shield-halved" style="color: #eab308;"></i> 15 Officer-Like Qualities (OLQ) Matrix
        </h3>
      </div>

      <div style="display: flex; flex-direction: column; gap: 16px;">
        <!-- Section Headers -->
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 14px;">
          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px;">Section Tag</label>
            <input type="text" name="olq_tag" value="{{ cms('olq_tag', 'Core Military Evaluation Standards') }}" class="form-tactical" placeholder="e.g. Core Military Evaluation Standards">
          </div>
          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px;">Section Heading</label>
            <input type="text" name="olq_heading" value="{{ cms('olq_heading', '15 Officer-Like Qualities (OLQ) Matrix') }}" class="form-tactical" placeholder="e.g. 15 Officer-Like Qualities (OLQ) Matrix">
          </div>
        </div>

        <!-- 4 Factors Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 14px;">
          <!-- Factor I -->
          <div style="background: rgba(255, 255, 255, 0.03); padding: 14px; border-radius: 10px; border: 1px solid rgba(255, 255, 255, 0.08);">
            <label style="display: block; font-size: 11px; font-weight: 700; color: var(--accent-gold); margin-bottom: 4px;">Factor I</label>
            <input type="text" name="olq_factor1_title" value="{{ cms('olq_factor1_title', 'FACTOR I: PLANNING & INTELLECT') }}" class="form-tactical" style="margin-bottom: 6px;" placeholder="e.g. FACTOR I: PLANNING & INTELLECT">
            <textarea name="olq_factor1_items" rows="4" class="form-tactical" placeholder="e.g. 1. Effective Intelligence&#10;2. Reasoning Ability&#10;3. Organizing Ability&#10;4. Power of Expression">{{ cms('olq_factor1_items', "1. Effective Intelligence\n2. Reasoning Ability\n3. Organizing Ability\n4. Power of Expression") }}</textarea>
          </div>

          <!-- Factor II -->
          <div style="background: rgba(255, 255, 255, 0.03); padding: 14px; border-radius: 10px; border: 1px solid rgba(255, 255, 255, 0.08);">
            <label style="display: block; font-size: 11px; font-weight: 700; color: var(--accent-gold); margin-bottom: 4px;">Factor II</label>
            <input type="text" name="olq_factor2_title" value="{{ cms('olq_factor2_title', 'FACTOR II: SOCIAL ADAPTABILITY') }}" class="form-tactical" style="margin-bottom: 6px;" placeholder="e.g. FACTOR II: SOCIAL ADAPTABILITY">
            <textarea name="olq_factor2_items" rows="4" class="form-tactical" placeholder="e.g. 5. Social Adaptability&#10;6. Cooperation & Camaraderie&#10;7. Sense of Responsibility">{{ cms('olq_factor2_items', "5. Social Adaptability\n6. Cooperation & Camraderie\n7. Sense of Responsibility") }}</textarea>
          </div>

          <!-- Factor III -->
          <div style="background: rgba(255, 255, 255, 0.03); padding: 14px; border-radius: 10px; border: 1px solid rgba(255, 255, 255, 0.08);">
            <label style="display: block; font-size: 11px; font-weight: 700; color: var(--accent-gold); margin-bottom: 4px;">Factor III</label>
            <input type="text" name="olq_factor3_title" value="{{ cms('olq_factor3_title', 'FACTOR III: SOCIAL EFFECTIVENESS') }}" class="form-tactical" style="margin-bottom: 6px;" placeholder="e.g. FACTOR III: SOCIAL EFFECTIVENESS">
            <textarea name="olq_factor3_items" rows="4" class="form-tactical" placeholder="e.g. 8. Initiative & Boldness&#10;9. Self-Confidence&#10;10. Speed of Decision&#10;11. Ability to Influence&#10;12. Liveliness & Morale">{{ cms('olq_factor3_items', "8. Initiative & Boldness\n9. Self-Confidence\n10. Speed of Decision\n11. Ability to Influence\n12. Liveliness & Morale") }}</textarea>
          </div>

          <!-- Factor IV -->
          <div style="background: rgba(255, 255, 255, 0.03); padding: 14px; border-radius: 10px; border: 1px solid rgba(255, 255, 255, 0.08);">
            <label style="display: block; font-size: 11px; font-weight: 700; color: var(--accent-gold); margin-bottom: 4px;">Factor IV</label>
            <input type="text" name="olq_factor4_title" value="{{ cms('olq_factor4_title', 'FACTOR IV: DYNAMIC COURAGE') }}" class="form-tactical" style="margin-bottom: 6px;" placeholder="e.g. FACTOR IV: DYNAMIC COURAGE">
            <textarea name="olq_factor4_items" rows="4" class="form-tactical" placeholder="e.g. 13. Determination&#10;14. Courage & Moral Stature&#10;15. Physical Stamina">{{ cms('olq_factor4_items', "13. Determination\n14. Courage & Moral Stature\n15. Physical Stamina") }}</textarea>
          </div>
        </div>

      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 7. FEATURED PREPARATORY PROGRAMS (NEXT ON LIVE SITE) -->
    <!-- ========================================================================= -->
    <div class="tactical-card" style="margin-bottom: 24px; border-left: 4px solid var(--brand-mint);">
      <div style="border-bottom: 1px solid rgba(255, 255, 255, 0.08); padding-bottom: 14px; margin-bottom: 20px;">
        <h3 style="font-size: 16px; font-weight: 800; color: #ffffff; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-graduation-cap" style="color: var(--brand-mint);"></i> Featured Preparatory Programs
        </h3>
      </div>

      <div style="display: flex; flex-direction: column; gap: 16px;">
        
        <!-- Section Headers -->
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 14px;">
          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px;">Section Tag</label>
            <input type="text" name="courses_section_tag" value="{{ cms('courses_section_tag', 'Admissions Open') }}" class="form-tactical" placeholder="e.g. Admissions Open">
          </div>
          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px;">Section Heading</label>
            <input type="text" name="courses_section_heading" value="{{ cms('courses_section_heading', 'Featured Preparatory Programs') }}" class="form-tactical" placeholder="e.g. Featured Preparatory Programs">
          </div>
        </div>

        <!-- Course Selector Box -->
        <div style="background: rgba(16, 185, 129, 0.04); border: 1.5px solid rgba(16, 185, 129, 0.25); border-radius: 12px; padding: 18px;">
          <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 14px;">
            <div>
              <strong style="color: #ffffff; font-size: 13.5px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-graduation-cap" style="color: var(--brand-mint);"></i> Active Courses Available in Database
              </strong>
            </div>

            <div style="display: flex; gap: 8px;">
              <button type="button" class="btn-tactical btn-tactical-outline" style="font-size: 11px; padding: 5px 12px;" onclick="selectAllCourses(true)">
                <i class="fa-solid fa-check-double"></i> Select All
              </button>
              <button type="button" class="btn-tactical btn-tactical-outline" style="font-size: 11px; padding: 5px 12px;" onclick="selectAllCourses(false)">
                <i class="fa-solid fa-xmark"></i> Deselect All
              </button>
              <a href="{{ route('admin.courses.index') }}" target="_blank" class="btn-tactical btn-tactical-outline" style="font-size: 11px; padding: 5px 12px; color: var(--brand-mint); border-color: rgba(16,185,129,0.3);">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Courses Manager
              </a>
            </div>
          </div>

          @if(isset($courses) && $courses->count() > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 12px;">
              @foreach($courses as $course)
                @php
                  $isChecked = in_array($course->id, $selectedCourseIds);
                @endphp
                <label id="course-label-{{ $course->id }}" style="display: flex; align-items: flex-start; gap: 12px; background: {{ $isChecked ? 'rgba(16, 185, 129, 0.12)' : '#121622' }}; border: 1.5px solid {{ $isChecked ? 'var(--brand-mint)' : 'rgba(255, 255, 255, 0.08)' }}; border-radius: 10px; padding: 12px 14px; cursor: pointer; transition: all 0.2s ease;">
                  <input type="checkbox" name="featured_courses[]" value="{{ $course->id }}" {{ $isChecked ? 'checked' : '' }} onchange="toggleCourseCardStyle(this, 'course-label-{{ $course->id }}')" style="width: 18px; height: 18px; accent-color: #10b981; margin-top: 3px; cursor: pointer;">
                  <div style="flex: 1; min-width: 0;">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 6px; margin-bottom: 4px;">
                      <strong style="color: #ffffff; font-size: 13px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $course->title }}">
                        {{ $course->title }}
                      </strong>
                      <span class="badge" style="background: rgba(255,255,255,0.08); color: var(--accent-gold); font-size: 9.5px; padding: 2px 6px;">
                        {{ $course->category }}
                      </span>
                    </div>
                    <div style="font-size: 11px; color: #94a3b8; display: flex; gap: 12px;">
                      <span><i class="fa-regular fa-clock"></i> {{ $course->duration }}</span>
                      <span><i class="fa-solid fa-bangladeshi-taka-sign"></i> ৳{{ number_format($course->fee, 0) }}</span>
                    </div>
                  </div>
                </label>
              @endforeach
            </div>
          @else
            <div style="text-align: center; padding: 20px; color: #94a3b8; font-size: 13px;">
              No courses found. Create courses in the <a href="{{ route('admin.courses.index') }}" style="color: var(--brand-mint);">Academic Courses section</a>.
            </div>
          @endif
        </div>

      </div>
    </div>

    <script>
      function toggleCourseCardStyle(checkbox, labelId) {
        var label = document.getElementById(labelId);
        if (!label) return;
        if (checkbox.checked) {
          label.style.background = 'rgba(16, 185, 129, 0.12)';
          label.style.borderColor = 'var(--brand-mint)';
        } else {
          label.style.background = '#121622';
          label.style.borderColor = 'rgba(255, 255, 255, 0.08)';
        }
      }
      function selectAllCourses(select) {
        document.querySelectorAll('input[name="featured_courses[]"]').forEach(function(cb) {
          cb.checked = select;
          var label = cb.closest('label');
          if (label) {
            label.style.background = select ? 'rgba(16, 185, 129, 0.12)' : '#121622';
            label.style.borderColor = select ? 'var(--brand-mint)' : 'rgba(255, 255, 255, 0.08)';
          }
        });
      }
    </script>

    <!-- ========================================================================= -->
    <!-- 8. TRAINING GALLERY PREVIEW (NEXT ON LIVE SITE) -->
    <!-- ========================================================================= -->
    <div class="tactical-card" style="margin-bottom: 24px; border-left: 4px solid #38bdf8;">
      <div style="border-bottom: 1px solid rgba(255, 255, 255, 0.08); padding-bottom: 14px; margin-bottom: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
          <h3 style="font-size: 16px; font-weight: 800; color: #ffffff; margin: 0; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-camera-retro" style="color: #38bdf8;"></i> Training Gallery Preview
          </h3>
          <a href="{{ route('admin.cms.gallery') }}" class="btn-tactical btn-tactical-outline" style="font-size: 11px; padding: 4px 10px;">
            <i class="fa-solid fa-arrow-up-right-from-square"></i> Gallery Manager
          </a>
        </div>
      </div>

      <div style="display: flex; flex-direction: column; gap: 14px;">
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 14px;">
          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px;">Section Tag</label>
            <input type="text" name="home_gallery_tag" value="{{ cms('home_gallery_tag', 'Life at IDA') }}" class="form-tactical" placeholder="e.g. Life at IDA">
          </div>
          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px;">Section Heading</label>
            <input type="text" name="home_gallery_title" value="{{ cms('home_gallery_title', 'Rigorous Ground & Academic Training') }}" class="form-tactical" placeholder="e.g. Rigorous Ground & Academic Training">
          </div>
        </div>
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 9. CALL TO ACTION BANNER (BOTTOM OF LIVE SITE) -->
    <!-- ========================================================================= -->
    <div class="tactical-card" style="margin-bottom: 24px; border-left: 4px solid #ef4444;">
      <div style="border-bottom: 1px solid rgba(255, 255, 255, 0.08); padding-bottom: 14px; margin-bottom: 20px;">
        <h3 style="font-size: 16px; font-weight: 800; color: #ffffff; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-bullseye" style="color: #ef4444;"></i> Call To Action Banner
        </h3>
      </div>

      <div style="display: flex; flex-direction: column; gap: 14px;">
        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px;">Heading</label>
          <input type="text" name="cta_heading" value="{{ cms('cta_heading', 'Ready to Wear the Prestigious Officer Uniform?') }}" class="form-tactical" placeholder="e.g. Ready to Wear the Prestigious Officer Uniform?">
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px;">Subheading</label>
          <textarea name="cta_subheading" rows="2" class="form-tactical" placeholder="e.g. Join Khulna's premier defence preparatory academy. Meet our retired military faculty, experience the obstacle fields in Boyra, and commence your journey toward commissioning.">{{ cms('cta_subheading', "Join Khulna's premier defence preparatory academy. Meet our retired military faculty, experience the obstacle fields in Boyra, and commence your journey toward commissioning.") }}</textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 4px;">Primary Button</label>
            <div style="display: flex; gap: 8px;">
              <input type="text" name="cta_btn1_text" value="{{ cms('cta_btn1_text', 'Apply for Direct Admission') }}" class="form-tactical" placeholder="e.g. Apply for Direct Admission">
              <input type="text" name="cta_btn1_url" value="{{ cms('cta_btn1_url', route('register')) }}" class="form-tactical" placeholder="e.g. {{ route('register') }}">
            </div>
          </div>
          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 4px;">Secondary Button</label>
            <div style="display: flex; gap: 8px;">
              <input type="text" name="cta_btn2_text" value="{{ cms('cta_btn2_text', 'Visit Khulna Campus') }}" class="form-tactical" placeholder="e.g. Visit Khulna Campus">
              <input type="text" name="cta_btn2_url" value="{{ cms('cta_btn2_url', route('contact')) }}" class="form-tactical" placeholder="e.g. {{ route('contact') }}">
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 10. KHULNA CAMPUS & SITEWIDE FOOTER (BOTTOM OF LIVE WEBSITE) -->
    <!-- ========================================================================= -->
    <div class="tactical-card" style="margin-bottom: 24px; border-left: 4px solid var(--brand-mint);">
      <div style="border-bottom: 1px solid rgba(255, 255, 255, 0.08); padding-bottom: 14px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <div>
          <h3 style="font-size: 16px; font-weight: 800; color: #ffffff; margin: 0; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-building-shield" style="color: var(--brand-mint);"></i> Khulna Campus & Sitewide Footer
          </h3>
          <span style="font-size: 12px; color: #94a3b8; display: block; margin-top: 4px;">
            Displayed at the bottom of the homepage and across all public pages. Emptying any field completely removes it from the website.
          </span>
        </div>
        <span class="badge badge-emerald" style="font-size: 11px; padding: 4px 10px;">
          <i class="fa-solid fa-location-dot"></i> Live Footer Section
        </span>
      </div>

      <div style="display: flex; flex-direction: column; gap: 20px;">
        
        <!-- Khulna Campus Details (Right Column of Footer) -->
        <div style="background: rgba(15, 23, 42, 0.5); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 10px; padding: 18px;">
          <h4 style="font-size: 13.5px; font-weight: 800; color: var(--accent-gold); margin: 0 0 14px 0; text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-map-location-dot"></i> Khulna Campus Headquarters
          </h4>

          <div style="display: flex; flex-direction: column; gap: 14px;">
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 14px;">
              <div>
                <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px;">Campus Column Title</label>
                <input type="text" name="footer_campus_heading" value="{{ cms('footer_campus_heading', 'KHULNA CAMPUS') }}" class="form-tactical" placeholder="e.g. KHULNA CAMPUS">
              </div>
              <div>
                <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px;">Campus Address</label>
                <input type="text" name="academy_location" value="{{ cms('academy_location', 'Boyra Main Road (Near Medical College), Khulna - 9000') }}" class="form-tactical" placeholder="e.g. Boyra Main Road (Near Medical College), Khulna - 9000">
              </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
              <div>
                <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px;">
                  <i class="fa-solid fa-phone" style="color: var(--brand-mint); margin-right: 4px;"></i> Hotlines & WhatsApp
                </label>
                <input type="text" name="academy_phone" value="{{ cms('academy_phone', '+880 1712-345678, +880 1911-987654') }}" class="form-tactical" placeholder="e.g. +880 1712-345678, +880 1911-987654">
              </div>
              <div>
                <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px;">
                  <i class="fa-solid fa-envelope" style="color: var(--brand-mint); margin-right: 4px;"></i> Email Addresses
                </label>
                <input type="text" name="academy_email" value="{{ cms('academy_email', 'info@ida.com.bd, admissions@ida.com.bd') }}" class="form-tactical" placeholder="e.g. info@ida.com.bd, admissions@ida.com.bd">
              </div>
            </div>

            <div>
              <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px;">
                <i class="fa-regular fa-clock" style="color: var(--brand-mint); margin-right: 4px;"></i> Campus Office & Visiting Hours
              </label>
              <input type="text" name="office_hours" value="{{ cms('office_hours', 'Saturday - Thursday: 08:00 AM - 08:00 PM (Friday: 03:00 PM - 08:00 PM)') }}" class="form-tactical" placeholder="e.g. Saturday - Thursday: 08:00 AM - 08:00 PM (Friday: 03:00 PM - 08:00 PM)">
            </div>
          </div>
        </div>

        <!-- Footer Bio, Values & Copyright (Left Column & Bottom Bar) -->
        <div style="background: rgba(15, 23, 42, 0.5); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 10px; padding: 18px;">
          <h4 style="font-size: 13.5px; font-weight: 800; color: var(--accent-gold); margin: 0 0 14px 0; text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-circle-info"></i> Academy Bio & Footer Details
          </h4>

          <div style="display: flex; flex-direction: column; gap: 14px;">
            <div>
              <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px;">Academy Bio / Description</label>
              <textarea name="footer_bio" rows="2" class="form-tactical" placeholder="e.g. Khulna's premier defense preparatory academy, providing structured grooming for Bangladesh Army (BMA Long Course), Navy, Air Force (BAFA), and complete 4-day simulated ISSB screening mentored by experienced defense officers.">{{ cms('footer_bio', "Khulna's premier defense preparatory academy, providing structured grooming for Bangladesh Army (BMA Long Course), Navy, Air Force (BAFA), and complete 4-day simulated ISSB screening mentored by experienced defense officers.") }}</textarea>
            </div>

            <div>
              <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px;">Core Values Badges</label>
              <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px;">
                <input type="text" name="footer_tag1" value="{{ cms('footer_tag1', 'Discipline') }}" class="form-tactical" placeholder="e.g. Discipline">
                <input type="text" name="footer_tag2" value="{{ cms('footer_tag2', 'Leadership') }}" class="form-tactical" placeholder="e.g. Leadership">
                <input type="text" name="footer_tag3" value="{{ cms('footer_tag3', 'Character') }}" class="form-tactical" placeholder="e.g. Character">
              </div>
            </div>

            <div>
              <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px;">Copyright & Motto</label>
              <input type="text" name="footer_copyright" value="{{ cms('footer_copyright', 'Imperial Defence Academy (IDA), Khulna. All rights reserved. Precision • Character • Commission.') }}" class="form-tactical" placeholder="e.g. Imperial Defence Academy (IDA), Khulna. All rights reserved. Precision • Character • Commission.">
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- Sticky Save Bar -->
    <div style="display: flex; justify-content: flex-end; gap: 12px; position: sticky; bottom: 20px; z-index: 10; background: rgba(17, 20, 29, 0.96); padding: 14px 20px; border-radius: var(--radius-sm); border: 1px solid rgba(255, 255, 255, 0.12); box-shadow: 0 8px 32px rgba(0,0,0,0.6); backdrop-filter: blur(8px);">
      <a href="{{ route('home') }}" target="_blank" class="btn-tactical btn-tactical-outline">
        <i class="fa-solid fa-eye"></i> View Live Site
      </a>
      <button type="submit" class="btn-tactical btn-tactical-primary" style="padding: 10px 24px; font-size: 13.5px;">
        <i class="fa-solid fa-floppy-disk"></i> Save Changes
      </button>
    </div>
  </form>

</div>
@endsection
