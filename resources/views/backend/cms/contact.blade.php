@extends('layouts.portal')

@section('title', 'Contact & Footer - Web Management')
@section('page_title', 'Contact & Footer Editor')
@section('page_subtitle', 'Manage headquarters location, phone hotlines, emails, operating hours, payment accounts, social channels, and sitewide footer')

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

    <!-- Page Header -->
    <div class="tactical-card" style="margin-bottom: 24px;">
      <div style="border-bottom: 1px solid var(--border-soft); padding-bottom: 14px; margin-bottom: 20px;">
        <h3 style="font-size: 17px; font-weight: 800; color: var(--brand-deep); margin: 0 0 6px 0;">
          <i class="fa-solid fa-address-book" style="color: var(--brand-emerald);"></i> Contact Page Introductory Header
        </h3>
        <p style="font-size: 12.5px; color: var(--text-muted); margin: 0;">
          Badge and title displayed at the top of <code>/contact</code>.
        </p>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 16px; margin-bottom: 14px;">
        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Eyebrow Badge</label>
          <input type="text" name="contact_badge" value="{{ cms('contact_badge', cms('contact_page_badge', 'COMMUNICATION & VISITATION')) }}" class="form-tactical">
        </div>
        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Page Heading</label>
          <input type="text" name="contact_title" value="{{ cms('contact_title', cms('contact_page_title', 'Contact Imperial Defence Academy')) }}" class="form-tactical">
        </div>
      </div>
      <div>
        <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Page Subtitle</label>
        <textarea name="contact_subtitle" rows="2" class="form-tactical">{{ cms('contact_subtitle', cms('contact_page_subtitle', 'Our admission officers and senior assessors are available for campus walk-ins, candidate counseling, and physical obstacle inspections.')) }}</textarea>
      </div>
    </div>

    <!-- Contact Channels & Address -->
    <div class="tactical-card" style="margin-bottom: 24px;">
      <div style="border-bottom: 1px solid var(--border-soft); padding-bottom: 14px; margin-bottom: 20px;">
        <h3 style="font-size: 17px; font-weight: 800; color: var(--brand-deep); margin: 0 0 6px 0;">
          <i class="fa-solid fa-location-dot" style="color: var(--accent-gold);"></i> Headquarters & Communication Channels
        </h3>
        <p style="font-size: 12.5px; color: var(--text-muted); margin: 0;">
          Official location, telephone hotlines, email, and visitor office hours.
        </p>
      </div>

      <div style="display: flex; flex-direction: column; gap: 16px;">
        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Headquarters Address</label>
          <input type="text" name="academy_location" value="{{ cms('academy_location', 'Boyra Main Road (Near Medical College), Khulna - 9000') }}" class="form-tactical">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
          <div>
            <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Hotline Numbers</label>
            <input type="text" name="academy_phone" value="{{ cms('academy_phone', '+880 1712-345678, +880 1911-987654') }}" class="form-tactical">
          </div>
          <div>
            <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Official Email Addresses</label>
            <input type="text" name="academy_email" value="{{ cms('academy_email', 'admissions@ida-khulna.edu.bd') }}" class="form-tactical">
          </div>
        </div>

        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Campus Office Hours</label>
          <input type="text" name="office_hours" value="{{ cms('office_hours', 'Sat – Thu: 08:00 AM – 08:00 PM (Friday: 03:00 PM – 08:00 PM)') }}" class="form-tactical">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
          <div>
            <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Official bKash Account</label>
            <input type="text" name="bkash_account" value="{{ cms('bkash_account', '01712-345678 (Merchant / Personal)') }}" class="form-tactical">
          </div>
          <div>
            <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Official Nagad Account</label>
            <input type="text" name="nagad_account" value="{{ cms('nagad_account', '01911-987654 (Merchant / Personal)') }}" class="form-tactical">
          </div>
        </div>
      </div>
    </div>

    <!-- Official Social Media Channels -->
    <div class="tactical-card" style="margin-bottom: 24px;">
      <div style="border-bottom: 1px solid var(--border-soft); padding-bottom: 14px; margin-bottom: 20px;">
        <h3 style="font-size: 17px; font-weight: 800; color: var(--brand-deep); margin: 0 0 6px 0;">
          <i class="fa-solid fa-share-nodes" style="color: var(--brand-emerald);"></i> Official Social Media Channels & Links
        </h3>
        <p style="font-size: 12.5px; color: var(--text-muted); margin: 0;">
          Links displayed in the website header and footer for cadets and parents to connect.
        </p>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">
            <i class="fa-brands fa-facebook" style="color: #1877f2;"></i> Facebook Page / Group URL
          </label>
          <input type="url" name="social_facebook" value="{{ cms('social_facebook', 'https://facebook.com/ida.khulna') }}" class="form-tactical" placeholder="https://facebook.com/...">
        </div>
        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">
            <i class="fa-brands fa-youtube" style="color: #ff0000;"></i> YouTube Channel URL
          </label>
          <input type="url" name="social_youtube" value="{{ cms('social_youtube', 'https://youtube.com/@ida-khulna') }}" class="form-tactical" placeholder="https://youtube.com/@...">
        </div>
        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">
            <i class="fa-brands fa-whatsapp" style="color: #25d366;"></i> WhatsApp Direct Chat / Group Link
          </label>
          <input type="text" name="social_whatsapp" value="{{ cms('social_whatsapp', 'https://wa.me/8801712345678') }}" class="form-tactical" placeholder="https://wa.me/...">
        </div>
        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">
            <i class="fa-brands fa-linkedin" style="color: #0a66c2;"></i> LinkedIn Page URL
          </label>
          <input type="url" name="social_linkedin" value="{{ cms('social_linkedin', '') }}" class="form-tactical" placeholder="https://linkedin.com/company/...">
        </div>
      </div>
    </div>

    <!-- Sitewide Footer -->
    <div class="tactical-card" style="margin-bottom: 24px;">
      <div style="border-bottom: 1px solid var(--border-soft); padding-bottom: 14px; margin-bottom: 20px;">
        <h3 style="font-size: 17px; font-weight: 800; color: var(--brand-deep); margin: 0 0 6px 0;">
          <i class="fa-solid fa-shoe-prints" style="color: var(--brand-emerald);"></i> Sitewide Public Footer & Bio
        </h3>
        <p style="font-size: 12.5px; color: var(--text-muted); margin: 0;">
          Footer bio description and copyright notice rendered across all public pages.
        </p>
      </div>

      <div style="display: flex; flex-direction: column; gap: 16px;">
        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Footer Academy Bio</label>
          <textarea name="footer_bio" rows="3" class="form-tactical">{{ cms('footer_bio', "Khulna's premier defense preparatory academy, providing structured grooming for Bangladesh Army (BMA Long Course), Navy, Air Force (BAFA), and complete 4-day simulated ISSB screening mentored by experienced defense officers.") }}</textarea>
        </div>
        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Copyright Notice</label>
          <input type="text" name="footer_copyright" value="{{ cms('footer_copyright', cms('site_copyright', 'Imperial Defence Academy (IDA), Khulna. All rights reserved. Precision • Character • Commission.')) }}" class="form-tactical">
        </div>
      </div>
    </div>

    <!-- Sticky Save Bar -->
    <div style="display: flex; justify-content: flex-end; gap: 12px; position: sticky; bottom: 20px; z-index: 10; background: rgba(255,255,255,0.95); padding: 14px 20px; border-radius: var(--radius-sm); border: 1px solid var(--border-soft); box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
      <a href="{{ route('contact') }}" target="_blank" class="btn-tactical btn-tactical-outline">
        <i class="fa-solid fa-eye"></i> View Live Contact Page
      </a>
      <button type="submit" class="btn-tactical btn-tactical-primary" style="padding: 10px 24px; font-size: 13.5px;">
        <i class="fa-solid fa-floppy-disk"></i> Save Contact & Footer Settings
      </button>
    </div>
  </form>

</div>
@endsection
