@extends('layouts.public')

@section('title', 'Contact Academy Headquarters | Imperial Defence Academy')

@section('content')
<!-- Page Header (Atmospheric Emerald & Obsidian) -->
<section style="position: relative; background: radial-gradient(circle at 50% 0%, rgba(16, 185, 129, 0.15) 0%, var(--brand-deep, #022c22) 50%, var(--accent-navy, #050b14) 100%); border-bottom: 1px solid rgba(16, 185, 129, 0.2); padding: 65px 24px; text-align: center; overflow: hidden;">
  <div style="position: relative; max-width: 800px; margin: 0 auto;">
    @if(filled(cms('contact_badge', 'COMMUNICATION & VISITATION')))
    <span data-aos="fade-down" style="display: inline-flex; align-items: center; gap: 6px; background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(52, 211, 153, 0.3); padding: 4px 14px; border-radius: 20px; font-size: 11px; font-weight: 700; color: #a7f3d0; margin-bottom: 16px; letter-spacing: 0.8px;">
      {{ cms('contact_badge', 'COMMUNICATION & VISITATION') }}
    </span>
    @endif

    @if(filled(cms('contact_title', 'Contact Imperial Defence Academy')))
    <h1 data-aos="zoom-in" data-aos-delay="150" style="font-size: 40px; font-weight: 800; color: #ffffff; font-family: 'Roboto', sans-serif; letter-spacing: -0.02em; margin-bottom: 12px;">
      {{ cms('contact_title', 'Contact Imperial Defence Academy') }}
    </h1>
    @endif

    @if(filled(cms('contact_subtitle', 'Our admission officers and senior assessors are available for campus walk-ins, candidate counseling, and physical obstacle inspections.')))
    <p data-aos="fade-up" data-aos-delay="250" style="color: #cbd5e1; font-size: 15.5px; line-height: 1.7; margin: 0 auto; max-width: 680px;">
      {{ cms('contact_subtitle', 'Our admission officers and senior assessors are available for campus walk-ins, candidate counseling, and physical obstacle inspections.') }}
    </p>
    @endif
  </div>
</section>

<section style="max-width: 1240px; margin: 60px auto; padding: 0 24px;">
  <style>
    .contact-layout {
      display: grid;
      grid-template-columns: 1fr 1.3fr;
      gap: 36px;
    }
    .contact-form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
      margin-bottom: 16px;
    }
    @media (max-width: 640px) {
      .contact-layout {
        grid-template-columns: 1fr !important;
        gap: 24px !important;
      }
      .contact-form-grid {
        grid-template-columns: 1fr !important;
        gap: 12px !important;
      }
    }
  </style>
  <div class="contact-layout">
    <!-- Campus Contact Details -->
    <div data-aos="fade-right" data-aos-delay="200">
      <div class="content-panel classical-card" style="margin-bottom: 24px; padding: 28px;">
        <h3 style="font-size: 18px; font-weight: 800; color: var(--brand-deep); margin-bottom: 22px; font-family: 'Roboto', sans-serif;">
          <i class="fa-solid fa-building-shield" style="color: var(--brand-emerald); margin-right: 6px;"></i> Academy Headquarters
        </h3>

        <div style="display: flex; flex-direction: column; gap: 20px;">
          <div style="display: flex; gap: 14px; align-items: flex-start;">
            <div class="metric-icon icon-emerald" style="width: 44px; height: 44px; border-radius: 12px; font-size: 18px;"><i class="fa-solid fa-location-dot"></i></div>
            <div>
              <strong style="display: block; font-size: 13.5px; color: var(--text-main);">Campus Address</strong>
              <span style="font-size: 13px; color: var(--text-muted); line-height: 1.5; display: block;">{{ cms('academy_location', 'Boyra Main Road (Near Medical College), Khulna - 9000') }}</span>
            </div>
          </div>

          <div style="display: flex; gap: 14px; align-items: flex-start;">
            <div class="metric-icon icon-gold" style="width: 44px; height: 44px; border-radius: 12px; font-size: 18px;"><i class="fa-solid fa-phone"></i></div>
            <div>
              <strong style="display: block; font-size: 13.5px; color: var(--text-main);">Hotlines & WhatsApp</strong>
              <span style="font-size: 13px; color: var(--text-muted); line-height: 1.5; display: block;">{{ cms('academy_phone', '+880 1712-345678, +880 1911-987654') }}</span>
            </div>
          </div>

          <div style="display: flex; gap: 14px; align-items: flex-start;">
            <div class="metric-icon icon-navy" style="width: 44px; height: 44px; border-radius: 12px; font-size: 18px;"><i class="fa-solid fa-envelope"></i></div>
            <div>
              <strong style="display: block; font-size: 13.5px; color: var(--text-main);">Official Email</strong>
              <span style="font-size: 13px; color: var(--text-muted); line-height: 1.5; display: block;">{{ cms('academy_email', 'admissions@ida-khulna.edu.bd') }}</span>
            </div>
          </div>

          <div style="display: flex; gap: 14px; align-items: flex-start;">
            <div class="metric-icon icon-red" style="width: 44px; height: 44px; border-radius: 12px; font-size: 18px;"><i class="fa-solid fa-clock"></i></div>
            <div>
              <strong style="display: block; font-size: 13.5px; color: var(--text-main);">Office & Ground Hours</strong>
              <span style="font-size: 13px; color: var(--text-muted); line-height: 1.5; display: block;">{!! nl2br(e(cms('office_hours', "Sat – Thu: 08:00 AM – 08:00 PM\nFriday: 03:00 PM – 08:00 PM"))) !!}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Candidate Direct Inquiry Form -->
    <div data-aos="fade-left" data-aos-delay="300">
      <div class="content-panel classical-card" style="padding: 28px;">
        <h3 style="font-size: 18px; font-weight: 800; color: var(--brand-deep); margin-bottom: 6px; font-family: 'Roboto', sans-serif;">Submit an Admission Inquiry</h3>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 22px;">Send us your query regarding batches, course eligibility, fees, or medical screening.</p>

        <form action="{{ route('contact.submit') }}" method="POST">
          @csrf
          <div class="contact-form-grid">
            <div>
              <label style="display: block; font-size: 11.5px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px; color: var(--text-main);">Full Name *</label>
              <input type="text" name="name" required class="form-control" placeholder="Candidate name">
            </div>
            <div>
              <label style="display: block; font-size: 11.5px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px; color: var(--text-main);">Email Address *</label>
              <input type="email" name="email" required class="form-control" placeholder="candidate@example.com">
            </div>
          </div>

          <div class="contact-form-grid">
            <div>
              <label style="display: block; font-size: 11.5px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px; color: var(--text-main);">Phone Number *</label>
              <input type="text" name="phone" required class="form-control" placeholder="017XXXXXXXX">
            </div>
            <div>
              <label style="display: block; font-size: 11.5px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px; color: var(--text-main);">Target Defense Wing</label>
              <select name="subject" class="form-control">
                <option value="Army BMA Long Course">Bangladesh Army (BMA)</option>
                <option value="Navy Officer Cadet">Bangladesh Navy (BNA)</option>
                <option value="Air Force Flight Wing">Bangladesh Air Force (BAFA)</option>
                <option value="ISSB 4-Day Special">ISSB Comprehensive Prep</option>
                <option value="General Admission Inquiry">General Inquiry</option>
              </select>
            </div>
          </div>

          <div style="margin-bottom: 20px;">
            <label style="display: block; font-size: 11.5px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px; color: var(--text-main);">Inquiry Details *</label>
            <textarea name="message" required rows="4" class="form-control" placeholder="Write your question or request a campus counseling appointment..."></textarea>
          </div>

          <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; padding: 12px; font-size: 13.5px;">
            <i class="fa-solid fa-paper-plane"></i> Send Admission Inquiry
          </button>
        </form>
      </div>
    </div>
  </div>
</section>
@endsection