@extends('layouts.public')

@section('title', 'Academy Notices & Circulars | Imperial Defence Academy')

@section('content')
<!-- Page Header (Atmospheric Emerald & Obsidian) -->
<section style="position: relative; background: radial-gradient(circle at 50% 0%, rgba(16, 185, 129, 0.15) 0%, var(--brand-deep, #022c22) 50%, var(--accent-navy, #050b14) 100%); border-bottom: 1px solid rgba(16, 185, 129, 0.2); padding: 65px 24px; text-align: center; overflow: hidden;">
  <div style="position: relative; max-width: 800px; margin: 0 auto;">
    <span data-aos="fade-down" style="display: inline-flex; align-items: center; gap: 6px; background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(52, 211, 153, 0.3); padding: 4px 14px; border-radius: 20px; font-size: 11px; font-weight: 700; color: #a7f3d0; margin-bottom: 16px; letter-spacing: 0.8px;">
      {{ cms('notices_badge', 'OFFICIAL BULLETIN') }}
    </span>
    <h1 data-aos="zoom-in" data-aos-delay="150" style="font-size: 40px; font-weight: 800; color: #ffffff; font-family: 'Roboto', sans-serif; letter-spacing: -0.02em; margin-bottom: 12px;">
      {{ cms('notices_title', 'Notices, Circulars & Announcements') }}
    </h1>
    <p data-aos="fade-up" data-aos-delay="250" style="color: #cbd5e1; font-size: 15.5px; line-height: 1.7; margin: 0 auto; max-width: 680px;">
      {{ cms('notices_subtitle', 'Real-time official academy notifications regarding batch commencement, mock test schedules, and board results.') }}
    </p>
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