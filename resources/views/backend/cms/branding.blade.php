@extends('layouts.portal')

@section('title', 'Branding & Official Logo - Web Management')
@section('page_title', 'Identity & Theme Colors Customizer (Official Logo Manager)')
@section('page_subtitle', 'Upload official academy logo, manage sitewide theme color palette, site motto, and appearance')

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

  <form action="{{ route('admin.cms.settings') }}" method="POST" enctype="multipart/form-data">
    @csrf

    @php
      $activeLogo = cms('site_logo') ?: cms('site_crest');
      $hasCustomLogo = !empty($activeLogo) && file_exists(public_path(ltrim($activeLogo, '/')));
    @endphp

    <!-- SECTION 1: OFFICIAL ACADEMY LOGO UPLOAD -->
    <div class="tactical-card" style="margin-bottom: 24px; border-top: 3px solid var(--accent-gold, #d97706);">
      <div style="border-bottom: 1px solid var(--border-soft); padding-bottom: 14px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 12px;">
        <div>
          <h3 style="font-size: 18px; font-weight: 800; color: var(--brand-deep); margin: 0 0 6px 0; display: flex; align-items: center; gap: 9px;">
            <i class="fa-solid fa-shield-halved" style="color: var(--accent-gold);"></i> Official Academy Logo (IDA Insignia)
          </h3>
          <p style="font-size: 13px; color: var(--text-muted); margin: 0; max-width: 780px; line-height: 1.6;">
            Upload your official academy logo. When uploaded, it will <strong>automatically replace the IDA emblem everywhere across the entire website</strong> (Header Navbar, Hero Showcase Card, Institutional Footer, and Portal). Only the official IDA logo is replaced.
          </p>
        </div>
        <span class="badge {{ $hasCustomLogo ? 'badge-emerald' : 'badge-gold' }}" style="font-size: 11px; padding: 6px 12px;">
          <i class="fa-solid {{ $hasCustomLogo ? 'fa-circle-check' : 'fa-certificate' }}"></i>
          {{ $hasCustomLogo ? 'Custom Logo Active Sitewide' : 'Default 3D Insignia Active' }}
        </span>
      </div>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; align-items: start;">
        
        <!-- Left: Active Logo Preview -->
        <div style="background: #090e1a; border: 1px solid #1e314b; border-radius: 14px; padding: 22px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 220px; position: relative;">
          <small style="color: #94a3b8; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 14px; display: block;">
            Live Active Logo Preview
          </small>

          <div style="display: flex; align-items: center; justify-content: center; height: 90px; margin-bottom: 16px;">
            @if($hasCustomLogo)
              <img src="{{ asset($activeLogo) }}?v={{ time() }}" alt="Active Academy Logo" style="max-height: 80px; max-width: 220px; object-fit: contain; filter: drop-shadow(0 6px 16px rgba(0,0,0,0.5)); border-radius: 8px;">
            @else
              @include('frontend.partials.logo_crest', ['size' => 74])
            @endif
          </div>

          <div style="font-size: 12px; color: #e2e8f0; font-weight: 600; margin-bottom: 12px;">
            {{ $hasCustomLogo ? basename($activeLogo) : 'Prestige 3D Sovereign Cadet Emblem (Default)' }}
          </div>

          @if($hasCustomLogo)
            <label style="display: inline-flex; align-items: center; gap: 8px; font-size: 12px; color: #ef4444; cursor: pointer; background: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.3); padding: 6px 14px; border-radius: 6px; transition: all 0.2s ease;">
              <input type="checkbox" name="remove_site_logo" value="1" onchange="if(this.checked){ alert('Logo will be removed and default 3D crest restored upon clicking Save.'); }">
              <span><i class="fa-solid fa-trash-can"></i> Remove Logo & Restore Default 3D Insignia</span>
            </label>
          @else
            <span style="font-size: 11.5px; color: #94a3b8;">
              <i class="fa-solid fa-info-circle" style="color: var(--accent-gold);"></i> Upload a file below to replace this default emblem with your custom logo.
            </span>
          @endif
        </div>

        <!-- Right: Upload New Logo File Input & Live Preview -->
        <div style="background: var(--surface-subtle, #f8fafc); border: 1.5px dashed #cbd5e1; border-radius: 14px; padding: 22px; display: flex; flex-direction: column; justify-content: space-between;">
          <div>
            <label for="site_logo_input" style="display: block; font-size: 13px; font-weight: 800; color: var(--brand-deep); text-transform: uppercase; margin-bottom: 8px;">
              <i class="fa-solid fa-cloud-arrow-up" style="color: var(--brand-emerald);"></i> Choose Logo Image
            </label>
            <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 14px; line-height: 1.5;">
              Select a transparent PNG, SVG, JPG, or WEBP image file of your official logo.
            </p>

            <input type="file" id="site_logo_input" name="site_logo" class="form-tactical" accept="image/png,image/jpeg,image/webp,image/svg+xml" style="background: #ffffff; padding: 10px 12px;" onchange="previewSelectedLogo(this)">

            <small style="color: var(--text-muted); font-size: 11px; display: block; margin-top: 6px;">
              <i class="fa-solid fa-circle-info" style="color: var(--brand-emerald);"></i> Recommended: Transparent PNG or SVG. Heights between 48px and 120px look optimal. Max size 5MB.
            </small>

            <!-- Client-side Selected Preview Area -->
            <div id="new-logo-preview-box" style="display: none; margin-top: 14px; background: #090e1a; border: 1px solid #1e314b; border-radius: 8px; padding: 12px; text-align: center;">
              <span style="display: block; font-size: 10.5px; color: #94a3b8; text-transform: uppercase; font-weight: 700; margin-bottom: 6px;">Selected Image Preview (Pending Save):</span>
              <img id="new-logo-preview-img" src="" alt="Preview" style="max-height: 60px; max-width: 180px; object-fit: contain;">
            </div>
          </div>

          <div style="margin-top: 20px; display: flex; gap: 10px; align-items: center;">
            <button type="submit" class="btn-tactical btn-tactical-primary" style="padding: 10px 22px; font-size: 13px; font-weight: 700;">
              <i class="fa-solid fa-check"></i> Upload & Apply Logo
            </button>
            <span style="font-size: 11.5px; color: var(--text-muted);">Applies sitewide immediately.</span>
          </div>
        </div>

      </div>
    </div>

    <!-- SECTION 2: IDENTITY & TITLES -->
    <div class="tactical-card" style="margin-bottom: 24px;">
      <div style="border-bottom: 1px solid var(--border-soft); padding-bottom: 14px; margin-bottom: 20px;">
        <h3 style="font-size: 17px; font-weight: 800; color: var(--brand-deep); margin: 0 0 6px 0;">
          <i class="fa-solid fa-signature" style="color: var(--brand-emerald);"></i> Academy Title & Motto
        </h3>
        <p style="font-size: 12.5px; color: var(--text-muted); margin: 0;">
          The primary institution name and motto displayed beside the logo in the header and footer.
        </p>
      </div>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Academy Title / Site Name</label>
          <input type="text" name="site_name" value="{{ $settings['site_name']->value ?? 'Imperial Defence Academy (IDA)' }}" class="form-tactical">
          <small style="color: var(--text-muted); font-size: 11px; display: block; margin-top: 4px;">Displayed prominently in the navigation header.</small>
        </div>
        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Motto / Tagline</label>
          <input type="text" name="site_tagline" value="{{ $settings['site_tagline']->value ?? 'Courage • Character • Commission' }}" class="form-tactical">
          <small style="color: var(--text-muted); font-size: 11px; display: block; margin-top: 4px;">Sub-line with gold & emerald gradient fill.</small>
        </div>
      </div>
    </div>

    <!-- SECTION 3: THEME COLORS -->
    <div class="tactical-card" style="margin-bottom: 24px;">
      <div style="border-bottom: 1px solid var(--border-soft); padding-bottom: 14px; margin-bottom: 20px;">
        <h3 style="font-size: 17px; font-weight: 800; color: var(--brand-deep); margin: 0 0 6px 0;">
          <i class="fa-solid fa-palette" style="color: var(--brand-emerald);"></i> Live Color Palette Customizer
        </h3>
        <p style="font-size: 12.5px; color: var(--text-muted); margin: 0;">
          Fine-tune the dynamic color variables overriding CSS tokens sitewide. Buttons, headers, cards, and highlights update automatically.
        </p>
      </div>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px;">
        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Primary Emerald</label>
          <div style="display: flex; gap: 8px; align-items: center;">
            <input type="color" name="color_primary" value="{{ $settings['color_primary']->value ?? '#059669' }}" style="width: 44px; height: 38px; border: 1px solid var(--border-soft); border-radius: 6px; cursor: pointer; padding: 2px;">
            <input type="text" value="{{ $settings['color_primary']->value ?? '#059669' }}" class="form-tactical" style="font-family: monospace; font-size: 12px;" oninput="this.previousElementSibling.value=this.value" onchange="this.previousElementSibling.value=this.value">
          </div>
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Deep Base Tone</label>
          <div style="display: flex; gap: 8px; align-items: center;">
            <input type="color" name="color_deep" value="{{ $settings['color_deep']->value ?? '#022c22' }}" style="width: 44px; height: 38px; border: 1px solid var(--border-soft); border-radius: 6px; cursor: pointer; padding: 2px;">
            <input type="text" value="{{ $settings['color_deep']->value ?? '#022c22' }}" class="form-tactical" style="font-family: monospace; font-size: 12px;" oninput="this.previousElementSibling.value=this.value" onchange="this.previousElementSibling.value=this.value">
          </div>
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Military Gold Accent</label>
          <div style="display: flex; gap: 8px; align-items: center;">
            <input type="color" name="color_gold" value="{{ $settings['color_gold']->value ?? '#d4af37' }}" style="width: 44px; height: 38px; border: 1px solid var(--border-soft); border-radius: 6px; cursor: pointer; padding: 2px;">
            <input type="text" value="{{ $settings['color_gold']->value ?? '#d4af37' }}" class="form-tactical" style="font-family: monospace; font-size: 12px;" oninput="this.previousElementSibling.value=this.value" onchange="this.previousElementSibling.value=this.value">
          </div>
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Luminous Mint Glow</label>
          <div style="display: flex; gap: 8px; align-items: center;">
            <input type="color" name="color_mint" value="{{ $settings['color_mint']->value ?? '#10b981' }}" style="width: 44px; height: 38px; border: 1px solid var(--border-soft); border-radius: 6px; cursor: pointer; padding: 2px;">
            <input type="text" value="{{ $settings['color_mint']->value ?? '#10b981' }}" class="form-tactical" style="font-family: monospace; font-size: 12px;" oninput="this.previousElementSibling.value=this.value" onchange="this.previousElementSibling.value=this.value">
          </div>
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Slate Obsidian Navy</label>
          <div style="display: flex; gap: 8px; align-items: center;">
            <input type="color" name="color_navy" value="{{ $settings['color_navy']->value ?? '#050b14' }}" style="width: 44px; height: 38px; border: 1px solid var(--border-soft); border-radius: 6px; cursor: pointer; padding: 2px;">
            <input type="text" value="{{ $settings['color_navy']->value ?? '#050b14' }}" class="form-tactical" style="font-family: monospace; font-size: 12px;" oninput="this.previousElementSibling.value=this.value" onchange="this.previousElementSibling.value=this.value">
          </div>
        </div>
      </div>
    </div>

    <!-- SECTION 4: OPTIONAL TOP ANNOUNCEMENT BAR -->
    <div class="tactical-card" style="margin-bottom: 24px;">
      <div style="border-bottom: 1px solid var(--border-soft); padding-bottom: 14px; margin-bottom: 20px;">
        <h3 style="font-size: 17px; font-weight: 800; color: var(--brand-deep); margin: 0 0 6px 0;">
          <i class="fa-solid fa-bullhorn" style="color: var(--brand-emerald);"></i> Top Announcement Bar (Optional Header Ticker)
        </h3>
        <p style="font-size: 12.5px; color: var(--text-muted); margin: 0;">
          Controls the optional top strip above the navbar. Disabled by default to provide a clean, pristine institutional header.
        </p>
      </div>

      <div style="display: flex; flex-direction: column; gap: 14px;">
        <div style="background: var(--surface-subtle, #f8fafc); border: 1px solid var(--border-soft); border-radius: 8px; padding: 12px 16px;">
          <label style="display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 700; cursor: pointer; color: var(--text-main);">
            <input type="checkbox" name="show_top_announcement_bar" value="1" {{ cms('show_top_announcement_bar', false) ? 'checked' : '' }}>
            <span>Enable Top Announcement Strip on Public Website</span>
          </label>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 16px;">
          <div>
            <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Announcement Badge</label>
            <input type="text" name="announcement_badge" value="{{ cms('announcement_badge', 'ADMISSIONS OPEN') }}" class="form-tactical">
          </div>
          <div>
            <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Notification Text</label>
            <input type="text" name="announcement_text" value="{{ cms('announcement_text', cms('announcement_bar_text', 'BMA 95th Long Course & Navy 2026-B Batches Enrolling Now')) }}" class="form-tactical">
          </div>
        </div>
      </div>
    </div>

    <!-- Sticky Save Bar -->
    <div style="display: flex; justify-content: flex-end; gap: 12px; position: sticky; bottom: 20px; z-index: 10; background: rgba(255,255,255,0.95); padding: 14px 20px; border-radius: var(--radius-sm); border: 1px solid var(--border-soft); box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
      <a href="{{ route('home') }}" target="_blank" class="btn-tactical btn-tactical-outline">
        <i class="fa-solid fa-eye"></i> View Live Site
      </a>
      <button type="submit" class="btn-tactical btn-tactical-primary" style="padding: 10px 24px; font-size: 13.5px; font-weight: 700;">
        <i class="fa-solid fa-floppy-disk"></i> Save All Branding & Settings
      </button>
    </div>
  </form>

</div>

<script>
function previewSelectedLogo(input) {
  const previewBox = document.getElementById('new-logo-preview-box');
  const previewImg = document.getElementById('new-logo-preview-img');
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function(e) {
      previewImg.src = e.target.result;
      previewBox.style.display = 'block';
    };
    reader.readAsDataURL(input.files[0]);
  } else {
    previewBox.style.display = 'none';
  }
}
</script>
@endsection
