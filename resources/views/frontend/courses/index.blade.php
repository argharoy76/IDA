@extends('layouts.public')

@section('title', 'Preparatory Courses | Imperial Defence Academy')

@section('content')
<!-- Page Header (Atmospheric Emerald & Obsidian) -->
<section style="position: relative; background: radial-gradient(circle at 50% 0%, rgba(16, 185, 129, 0.15) 0%, var(--brand-deep, #022c22) 50%, var(--accent-navy, #050b14) 100%); border-bottom: 1px solid rgba(16, 185, 129, 0.2); padding: 65px 24px; text-align: center; overflow: hidden;">
  <div style="position: relative; max-width: 800px; margin: 0 auto;">
    @if(filled(cms('courses_badge', 'ACADEMIC PROGRAMS')))
    <span data-aos="fade-down" style="display: inline-flex; align-items: center; gap: 6px; background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(52, 211, 153, 0.3); padding: 4px 14px; border-radius: 20px; font-size: 11px; font-weight: 700; color: #a7f3d0; margin-bottom: 16px; letter-spacing: 0.8px;">
      {{ cms('courses_badge', 'ACADEMIC PROGRAMS') }}
    </span>
    @endif

    @if(filled(cms('courses_title', 'Officer Cadet Preparatory Courses')))
    <h1 data-aos="zoom-in" data-aos-delay="150" style="font-size: 40px; font-weight: 800; color: #ffffff; font-family: 'Roboto', sans-serif; letter-spacing: -0.02em; margin-bottom: 12px;">
      {{ cms('courses_title', 'Officer Cadet Preparatory Courses') }}
    </h1>
    @endif

    @if(filled(cms('courses_subtitle', 'Comprehensive training programs tailored specifically for Bangladesh Army, Navy, Airforce, and direct ISSB board preparation.')))
    <p data-aos="fade-up" data-aos-delay="250" style="color: #cbd5e1; font-size: 15.5px; line-height: 1.7; margin: 0 auto 24px; max-width: 680px;">
      {{ cms('courses_subtitle', 'Comprehensive training programs tailored specifically for Bangladesh Army, Navy, Airforce, and direct ISSB board preparation.') }}
    </p>
    @endif

    <!-- Category Filters (Frosted Glass Pills) -->
    <div data-aos="fade-up" data-aos-delay="350" style="display: flex; justify-content: center; gap: 10px; margin-top: 10px; flex-wrap: wrap;">
      <a href="{{ route('courses') }}" class="btn-secondary {{ !request('category') ? 'active' : '' }}" style="border-radius: 24px; padding: 7px 18px; font-size: 12.5px; {{ !request('category') ? 'background: #059669; color: #ffffff !important; border-color: #059669;' : '' }}">All Wings</a>
      @foreach($categories as $cat)
        <a href="{{ route('courses', ['category' => $cat]) }}" class="btn-secondary {{ request('category') == $cat ? 'active' : '' }}" style="border-radius: 24px; padding: 7px 18px; font-size: 12.5px; {{ request('category') == $cat ? 'background: #059669; color: #ffffff !important; border-color: #059669;' : '' }}">
          {{ $cat }}
        </a>
      @endforeach
    </div>
  </div>
</section>

<section style="max-width: 1240px; margin: 60px auto; padding: 0 24px;">
  <div class="grid-cols-3-responsive">
    @forelse($courses as $course)
      <div class="content-panel classical-card" data-aos="fade-up" data-aos-delay="{{ 100 + ($loop->index % 6) * 100 }}" style="display: flex; flex-direction: column; justify-content: space-between; height: 100%; margin-bottom: 0; padding: 26px;">
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
            {{ $course->description }}
          </p>

          <div style="background: var(--surface-subtle); padding: 12px 14px; border-radius: var(--radius-sm); margin-bottom: 18px; border-left: 3px solid var(--brand-emerald);">
            <small style="display: block; font-size: 10.5px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">ELIGIBILITY</small>
            <span style="font-size: 12.5px; font-weight: 600; color: var(--text-main);">{{ $course->eligibility }}</span>
          </div>

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
            <small style="display: block; font-size: 10.5px; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">TUITION FEE</small>
            <strong style="font-size: 20px; color: var(--brand-deep); font-family: 'Roboto', sans-serif; font-weight: 800;">৳{{ number_format($course->fee, 0) }}</strong>
          </div>
          <a href="{{ route('courses.detail', $course->slug) }}" class="btn-primary" style="padding: 8px 16px; font-size: 12.5px;">
            View Syllabus <i class="fa-solid fa-arrow-right"></i>
          </a>
        </div>
      </div>
    @empty
      <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
        <p style="color: var(--text-muted);">No courses currently found under this category filter.</p>
      </div>
    @endforelse
  </div>
</section>
@endsection