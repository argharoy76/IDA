@extends('layouts.public')

@section('title', 'Cadet Gallery | Imperial Defence Academy')

@section('content')
<!-- Page Header (Atmospheric Emerald & Obsidian) -->
<section style="position: relative; background: radial-gradient(circle at 50% 0%, rgba(16, 185, 129, 0.15) 0%, var(--brand-deep, #022c22) 50%, var(--accent-navy, #050b14) 100%); border-bottom: 1px solid rgba(16, 185, 129, 0.2); padding: 65px 24px; text-align: center; overflow: hidden;">
  <div style="position: relative; max-width: 800px; margin: 0 auto;">
    <span data-aos="fade-down" style="display: inline-flex; align-items: center; gap: 6px; background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(52, 211, 153, 0.3); padding: 4px 14px; border-radius: 20px; font-size: 11px; font-weight: 700; color: #a7f3d0; margin-bottom: 16px; letter-spacing: 0.8px;">
      {{ cms('gallery_badge', 'PHOTO ARCHIVE') }}
    </span>
    <h1 data-aos="zoom-in" data-aos-delay="150" style="font-size: 40px; font-weight: 800; color: #ffffff; font-family: 'Roboto', sans-serif; letter-spacing: -0.02em; margin-bottom: 12px;">
      {{ cms('gallery_title', 'Life, Training & Discipline at IDA') }}
    </h1>
    <p data-aos="fade-up" data-aos-delay="250" style="color: #cbd5e1; font-size: 15.5px; line-height: 1.7; margin: 0 auto 24px; max-width: 680px;">
      {{ cms('gallery_subtitle', 'Visual moments capturing obstacle mastery, mental endurance, classroom presentations, and triumph.') }}
    </p>

    <!-- Category Filters -->
    <div data-aos="fade-up" data-aos-delay="350" style="display: flex; justify-content: center; gap: 10px; margin-top: 10px; flex-wrap: wrap;">
      <a href="{{ route('gallery') }}" class="btn-secondary {{ !request('category') ? 'active' : '' }}" style="border-radius: 24px; padding: 7px 18px; font-size: 12.5px; {{ !request('category') ? 'background: #059669; color: #ffffff !important; border-color: #059669;' : '' }}">All Photos</a>
      @foreach($categories as $cat)
        <a href="{{ route('gallery', ['category' => $cat]) }}" class="btn-secondary {{ request('category') == $cat ? 'active' : '' }}" style="border-radius: 24px; padding: 7px 18px; font-size: 12.5px; {{ request('category') == $cat ? 'background: #059669; color: #ffffff !important; border-color: #059669;' : '' }}">
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
          <div style="height: 210px; background: linear-gradient(135deg, var(--brand-primary, #064e3b) 0%, var(--accent-navy, #0f172a) 100%); display: grid; place-items: center; color: rgba(255,255,255,0.7); font-size: 42px;">
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