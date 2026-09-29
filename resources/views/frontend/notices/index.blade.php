@extends('layouts.public')

@section('title', 'Academy Notices & Circulars | Imperial Defence Academy')

@section('content')
<!-- Page Header (Luminous Seafoam #85c9cc & Slate Aesthetic) -->
<section style="position: relative; background: linear-gradient(135deg, #f0f9fa 0%, #e6f4f5 50%, #f8fafc 100%); border-bottom: 1px solid rgba(133, 201, 204, 0.35); padding: 75px 24px 65px; text-align: center; overflow: hidden;">
  <div style="position: absolute; inset: 0; background: radial-gradient(circle at 50% 20%, rgba(133, 201, 204, 0.25) 0%, transparent 70%); pointer-events: none;"></div>
  <div style="position: relative; z-index: 2; max-width: 800px; margin: 0 auto;">
    @if(filled(cms('notices_badge', 'OFFICIAL BULLETIN')))
    <span data-aos="fade-down" style="display: inline-flex; align-items: center; gap: 6px; background: rgba(133, 201, 204, 0.22); border: 1px solid #85c9cc; padding: 5px 16px; border-radius: 9999px; font-size: 11.5px; font-weight: 800; color: #082d2f; margin-bottom: 16px; letter-spacing: 1px; text-transform: uppercase; box-shadow: 0 2px 8px rgba(133, 201, 204, 0.25);">
      {{ cms('notices_badge', 'OFFICIAL BULLETIN') }}
    </span>
    @endif

    @if(filled(cms('notices_title', 'Notices, Circulars & Announcements')))
    <h1 data-aos="zoom-in" data-aos-delay="150" style="font-size: clamp(30px, 4.5vw, 42px); font-weight: 900; color: #082d2f; font-family: 'Roboto', sans-serif; letter-spacing: -0.02em; margin-bottom: 14px;">
      {{ cms('notices_title', 'Notices, Circulars & Announcements') }}
    </h1>
    @endif

    @if(filled(cms('notices_subtitle', 'Real-time official academy notifications regarding batch commencement, mock test schedules, and board results.')))
    <p data-aos="fade-up" data-aos-delay="250" style="color: #475569; font-size: 16px; line-height: 1.75; margin: 0 auto; max-width: 680px; font-weight: 400;">
      {{ cms('notices_subtitle', 'Real-time official academy notifications regarding batch commencement, mock test schedules, and board results.') }}
    </p>
    @endif
  </div>
</section>

<section style="max-width: 920px; margin: 60px auto; padding: 0 24px;">
  <div style="display: flex; flex-direction: column; gap: 22px;">
    @forelse($notices as $notice)
      <div class="content-panel classical-card" data-aos="fade-up" data-aos-delay="{{ 100 + ($loop->index % 5) * 80 }}" style="margin-bottom: 0; position: relative; padding: 28px;">
        @if($notice->is_urgent)
          <div style="position: absolute; top: 20px; right: 20px;">
            <span class="badge badge-red" style="font-size: 11px;"><i class="fa-solid fa-triangle-exclamation"></i> URGENT</span>
          </div>
        @endif

        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 14px;">
          <span class="badge badge-emerald" style="font-size: 11px;">{{ $notice->category }}</span>
          <span style="font-size: 12.5px; color: var(--text-muted); font-weight: 600;">
            <i class="fa-regular fa-calendar" style="color: var(--brand-emerald);"></i> Published: {{ $notice->publish_date->format('d F, Y') }}
          </span>
        </div>

        <h2 style="font-size: 20px; font-weight: 800; color: var(--brand-deep); margin-bottom: 12px; font-family: 'Roboto', sans-serif;">
          {{ $notice->title }}
        </h2>

        <p style="font-size: 14px; color: var(--text-muted); line-height: 1.75; white-space: pre-line; margin: 0;">
          {{ $notice->content }}
        </p>
      </div>
    @empty
      <div class="content-panel" style="text-align: center; padding: 50px;">
        <p style="color: var(--text-muted);">No official notices currently posted.</p>
      </div>
    @endforelse

    <div style="margin-top: 20px;">
      {{ $notices->links() }}
    </div>
  </div>
</section>
@endsection