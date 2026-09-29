@extends('layouts.public')

@section('title', 'Cadet Gallery | Imperial Defence Academy')

@section('content')
<!-- Page Header (Luminous Seafoam #85c9cc & Slate Aesthetic) -->
<section style="position: relative; background: linear-gradient(135deg, #f0f9fa 0%, #e6f4f5 50%, #f8fafc 100%); border-bottom: 1px solid rgba(133, 201, 204, 0.35); padding: 75px 24px 65px; text-align: center; overflow: hidden;">
  <div style="position: absolute; inset: 0; background: radial-gradient(circle at 50% 20%, rgba(133, 201, 204, 0.25) 0%, transparent 70%); pointer-events: none;"></div>
  <div style="position: relative; z-index: 2; max-width: 800px; margin: 0 auto;">
    @if(filled(cms('gallery_badge', 'PHOTO ARCHIVE')))
    <span data-aos="fade-down" style="display: inline-flex; align-items: center; gap: 6px; background: rgba(133, 201, 204, 0.22); border: 1px solid #85c9cc; padding: 5px 16px; border-radius: 9999px; font-size: 11.5px; font-weight: 800; color: #082d2f; margin-bottom: 16px; letter-spacing: 1px; text-transform: uppercase; box-shadow: 0 2px 8px rgba(133, 201, 204, 0.25);">
      {{ cms('gallery_badge', 'PHOTO ARCHIVE') }}
    </span>
    @endif

    @if(filled(cms('gallery_title', 'Life, Training & Discipline at IDA')))
    <h1 data-aos="zoom-in" data-aos-delay="150" style="font-size: clamp(30px, 4.5vw, 42px); font-weight: 900; color: #082d2f; font-family: 'Roboto', sans-serif; letter-spacing: -0.02em; margin-bottom: 14px;">
      {{ cms('gallery_title', 'Life, Training & Discipline at IDA') }}
    </h1>
    @endif

    @if(filled(cms('gallery_subtitle', 'Visual moments capturing obstacle mastery, mental endurance, classroom presentations, and triumph.')))
    <p data-aos="fade-up" data-aos-delay="250" style="color: #475569; font-size: 16px; line-height: 1.75; margin: 0 auto 24px; max-width: 680px; font-weight: 400;">
      {{ cms('gallery_subtitle', 'Visual moments capturing obstacle mastery, mental endurance, classroom presentations, and triumph.') }}
    </p>
    @endif

    <!-- Category Filters (Lighter Pill Badges) -->
    <div data-aos="fade-up" data-aos-delay="350" style="display: flex; justify-content: center; gap: 10px; margin-top: 10px; flex-wrap: wrap;">
      <a href="{{ route('gallery') }}" class="btn-secondary {{ !request('category') ? 'active' : '' }}" style="border-radius: 9999px; padding: 8px 20px; font-size: 13px; font-weight: 700; {{ !request('category') ? 'background: #85c9cc; color: #082d2f !important; border-color: #72bcc0; box-shadow: 0 4px 12px rgba(133, 201, 204, 0.35);' : 'background: #ffffff; color: #334155; border-color: #cbd5e1; box-shadow: 0 2px 6px rgba(0,0,0,0.04);' }}">All Photos</a>
      @foreach($categories as $cat)
        <a href="{{ route('gallery', ['category' => $cat]) }}" class="btn-secondary {{ request('category') == $cat ? 'active' : '' }}" style="border-radius: 9999px; padding: 8px 20px; font-size: 13px; font-weight: 700; {{ request('category') == $cat ? 'background: #85c9cc; color: #082d2f !important; border-color: #72bcc0; box-shadow: 0 4px 12px rgba(133, 201, 204, 0.35);' : 'background: #ffffff; color: #334155; border-color: #cbd5e1; box-shadow: 0 2px 6px rgba(0,0,0,0.04);' }}">
          {{ $cat }}
        </a>
      @endforeach
    </div>
  </div>
</section>

<section style="max-width: 1240px; margin: 60px auto; padding: 0 24px;">
  <div class="grid-cols-3-responsive">
    @forelse($items as $item)
      <div class="classical-card" data-aos="fade-up" data-aos-delay="{{ 100 + ($loop->index % 6) * 100 }}" style="background: var(--surface); border: 1px solid var(--border-soft); border-radius: 16px; overflow: hidden; box-shadow: var(--shadow-card); transition: var(--transition-smooth);" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='var(--shadow-hover)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='var(--shadow-card)';">
        @if($item->image_path && file_exists(public_path($item->image_path)))
          <img src="{{ asset($item->image_path) }}" alt="{{ $item->title }}" style="width: 100%; height: 210px; object-fit: cover;">
        @else
          <div style="height: 210px; background: linear-gradient(135deg, #85c9cc 0%, #082d2f 100%); display: grid; place-items: center; color: rgba(255,255,255,0.7); font-size: 42px;">
            <i class="fa-solid fa-camera-retro"></i>
          </div>
        @endif
        <div style="padding: 18px 20px;">
          <span class="badge badge-blue" style="margin-bottom: 8px; font-size: 11px;">{{ $item->category }}</span>
          <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main); margin-bottom: 6px; font-family: 'Roboto', sans-serif;">{{ $item->title }}</h3>
          <p style="font-size: 13px; color: var(--text-muted); line-height: 1.55; margin: 0;">{{ $item->caption }}</p>
        </div>
      </div>
    @empty
      <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
        <p style="color: var(--text-muted);">No images found in this gallery category.</p>
      </div>
    @endforelse
  </div>
</section>
@endsection