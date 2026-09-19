@extends('layouts.portal')

@section('title', 'Web Management Hub')
@section('page_title', 'Web Management Overview Hub')
@section('page_subtitle', 'Manage every public page, section headings, text, dynamic background sliders, routines, and candidate inquiries')

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px;">

  @include('backend.cms.partials.nav')

  @if(session('success'))
    <div class="alert alert-success" style="background: rgba(16, 185, 129, 0.15); border: 1px solid var(--brand-mint); color: #065f46; padding: 12px 16px; border-radius: var(--radius-sm); font-size: 13px; font-weight: 600;">
      <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
  @endif

  <!-- Frontend Page Control Hub Grid -->
  <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 18px;">

    <!-- Card 1: Home Page -->
    <div class="tactical-card" style="display: flex; flex-direction: column; justify-content: space-between;">
      <div>
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
          <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(5, 150, 105, 0.1); color: var(--brand-emerald); display: grid; place-items: center; font-size: 18px;">
            <i class="fa-solid fa-house"></i>
          </div>
          <span class="badge badge-success">Frontend Root (/)</span>
        </div>
        <h4 style="font-size: 16px; font-weight: 800; color: var(--brand-deep); margin: 0 0 6px 0;">Home Page</h4>
        <p style="font-size: 12.5px; color: var(--text-muted); margin: 0 0 16px 0; line-height: 1.5;">
          Dynamic hero background slider, headlines, word rotators, 3 CTA buttons, trust metrics, 4 pillars, and 15 OLQ evaluation matrix.
        </p>
      </div>
      <div style="display: flex; gap: 8px; border-top: 1px solid var(--border-soft); padding-top: 12px;">
        <a href="{{ route('admin.cms.home') }}" class="btn-tactical btn-tactical-primary" style="font-size: 12px; flex: 1; text-align: center;">
          <i class="fa-solid fa-sliders"></i> Edit Home Page
        </a>
        <a href="{{ url('/') }}" target="_blank" class="btn-tactical btn-tactical-outline" style="font-size: 12px;" title="Live Preview">
          <i class="fa-solid fa-arrow-up-right-from-square"></i>
        </a>
      </div>
    </div>

    <!-- Card 2: Courses Page -->
    <div class="tactical-card" style="display: flex; flex-direction: column; justify-content: space-between;">
      <div>
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
          <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(217, 119, 6, 0.1); color: var(--accent-gold); display: grid; place-items: center; font-size: 18px;">
            <i class="fa-solid fa-book-bookmark"></i>
          </div>
          <span class="badge badge-info">{{ $courses->count() }} Programs</span>
        </div>
        <h4 style="font-size: 16px; font-weight: 800; color: var(--brand-deep); margin: 0 0 6px 0;">Courses Page</h4>
        <p style="font-size: 12.5px; color: var(--text-muted); margin: 0 0 16px 0; line-height: 1.5;">
          Course page introductory badge and title, curriculum details, fees, and batch schedules.
        </p>
      </div>
      <div style="display: flex; gap: 8px; border-top: 1px solid var(--border-soft); padding-top: 12px;">
        <a href="{{ route('admin.cms.courses') }}" class="btn-tactical btn-tactical-primary" style="font-size: 12px; flex: 1; text-align: center;">
          <i class="fa-solid fa-sliders"></i> Edit Courses Page
        </a>
        <a href="{{ url('/courses') }}" target="_blank" class="btn-tactical btn-tactical-outline" style="font-size: 12px;" title="Live Preview">
          <i class="fa-solid fa-arrow-up-right-from-square"></i>
        </a>
      </div>
    </div>

    <!-- Card 2.5: Classes & Routines Page -->
    <div class="tactical-card" style="display: flex; flex-direction: column; justify-content: space-between;">
      <div>
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
          <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(16, 185, 129, 0.1); color: var(--brand-mint); display: grid; place-items: center; font-size: 18px;">
            <i class="fa-solid fa-calendar-days"></i>
          </div>
          <span class="badge badge-success">Daily Routines</span>
        </div>
        <h4 style="font-size: 16px; font-weight: 800; color: var(--brand-deep); margin: 0 0 6px 0;">Classes & Routines</h4>
        <p style="font-size: 12.5px; color: var(--text-muted); margin: 0 0 16px 0; line-height: 1.5;">
          Header badge, title, subtitle, 3 training blueprints (Morning PT, Academics, GTO Drills), and live batch schedules.
        </p>
      </div>
      <div style="display: flex; gap: 8px; border-top: 1px solid var(--border-soft); padding-top: 12px;">
        <a href="{{ route('admin.cms.classes') }}" class="btn-tactical btn-tactical-primary" style="font-size: 12px; flex: 1; text-align: center;">
          <i class="fa-solid fa-sliders"></i> Edit Routines
        </a>
        <a href="{{ url('/classes') }}" target="_blank" class="btn-tactical btn-tactical-outline" style="font-size: 12px;" title="Live Preview">
          <i class="fa-solid fa-arrow-up-right-from-square"></i>
        </a>
      </div>
    </div>

    <!-- Card 3: Online Tests Page -->
    <div class="tactical-card" style="display: flex; flex-direction: column; justify-content: space-between;">
      <div>
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
          <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(239, 68, 68, 0.1); color: #ef4444; display: grid; place-items: center; font-size: 18px;">
            <i class="fa-solid fa-crosshairs"></i>
          </div>
          <span class="badge badge-warning">Simulations</span>
        </div>
        <h4 style="font-size: 16px; font-weight: 800; color: var(--brand-deep); margin: 0 0 6px 0;">Online Tests Page</h4>
        <p style="font-size: 12.5px; color: var(--text-muted); margin: 0 0 16px 0; line-height: 1.5;">
          Online exam banner, examination protocol, test question banks, and candidate mock evaluations.
        </p>
      </div>
      <div style="display: flex; gap: 8px; border-top: 1px solid var(--border-soft); padding-top: 12px;">
        <a href="{{ route('admin.cms.online_tests') }}" class="btn-tactical btn-tactical-primary" style="font-size: 12px; flex: 1; text-align: center;">
          <i class="fa-solid fa-sliders"></i> Edit Tests Page
        </a>
        <a href="{{ url('/online-tests') }}" target="_blank" class="btn-tactical btn-tactical-outline" style="font-size: 12px;" title="Live Preview">
          <i class="fa-solid fa-arrow-up-right-from-square"></i>
        </a>
      </div>
    </div>

    <!-- Card 4: About Page -->
    <div class="tactical-card" style="display: flex; flex-direction: column; justify-content: space-between;">
      <div>
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
          <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(59, 130, 246, 0.1); color: #3b82f6; display: grid; place-items: center; font-size: 18px;">
            <i class="fa-solid fa-landmark"></i>
          </div>
          <span class="badge badge-neutral">Institutional</span>
        </div>
        <h4 style="font-size: 16px; font-weight: 800; color: var(--brand-deep); margin: 0 0 6px 0;">About Page</h4>
        <p style="font-size: 12.5px; color: var(--text-muted); margin: 0 0 16px 0; line-height: 1.5;">
          Sacred mission, strategic vision, campus history, and the 3 specialized facility feature cards.
        </p>
      </div>
      <div style="display: flex; gap: 8px; border-top: 1px solid var(--border-soft); padding-top: 12px;">
        <a href="{{ route('admin.cms.about') }}" class="btn-tactical btn-tactical-primary" style="font-size: 12px; flex: 1; text-align: center;">
          <i class="fa-solid fa-sliders"></i> Edit About Page
        </a>
        <a href="{{ url('/about') }}" target="_blank" class="btn-tactical btn-tactical-outline" style="font-size: 12px;" title="Live Preview">
          <i class="fa-solid fa-arrow-up-right-from-square"></i>
        </a>
      </div>
    </div>

    <!-- Card 5: Gallery Page -->
    <div class="tactical-card" style="display: flex; flex-direction: column; justify-content: space-between;">
      <div>
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
          <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(168, 85, 247, 0.1); color: #a855f7; display: grid; place-items: center; font-size: 18px;">
            <i class="fa-solid fa-images"></i>
          </div>
          <span class="badge badge-info">{{ $gallery->count() }} Photos</span>
        </div>
        <h4 style="font-size: 16px; font-weight: 800; color: var(--brand-deep); margin: 0 0 6px 0;">Gallery Page</h4>
        <p style="font-size: 12.5px; color: var(--text-muted); margin: 0 0 16px 0; line-height: 1.5;">
          Upload campus drills, cadet ceremonies, and obstacle course photo archives with category tags.
        </p>
      </div>
      <div style="display: flex; gap: 8px; border-top: 1px solid var(--border-soft); padding-top: 12px;">
        <a href="{{ route('admin.cms.gallery') }}" class="btn-tactical btn-tactical-primary" style="font-size: 12px; flex: 1; text-align: center;">
          <i class="fa-solid fa-sliders"></i> Edit Gallery Page
        </a>
        <a href="{{ url('/gallery') }}" target="_blank" class="btn-tactical btn-tactical-outline" style="font-size: 12px;" title="Live Preview">
          <i class="fa-solid fa-arrow-up-right-from-square"></i>
        </a>
      </div>
    </div>

    <!-- Card 6: Notices Page -->
    <div class="tactical-card" style="display: flex; flex-direction: column; justify-content: space-between;">
      <div>
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
          <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(245, 158, 11, 0.1); color: #f59e0b; display: grid; place-items: center; font-size: 18px;">
            <i class="fa-solid fa-bullhorn"></i>
          </div>
          <span class="badge badge-warning">{{ $notices->count() }} Notices</span>
        </div>
        <h4 style="font-size: 16px; font-weight: 800; color: var(--brand-deep); margin: 0 0 6px 0;">Notices Page</h4>
        <p style="font-size: 12.5px; color: var(--text-muted); margin: 0 0 16px 0; line-height: 1.5;">
          Publish official notices, batch circulars, and announcements with urgent and pinned priority flags.
        </p>
      </div>
      <div style="display: flex; gap: 8px; border-top: 1px solid var(--border-soft); padding-top: 12px;">
        <a href="{{ route('admin.cms.notices') }}" class="btn-tactical btn-tactical-primary" style="font-size: 12px; flex: 1; text-align: center;">
          <i class="fa-solid fa-sliders"></i> Edit Notices Page
        </a>
        <a href="{{ url('/notices') }}" target="_blank" class="btn-tactical btn-tactical-outline" style="font-size: 12px;" title="Live Preview">
          <i class="fa-solid fa-arrow-up-right-from-square"></i>
        </a>
      </div>
    </div>

    <!-- Card 7: Contact & Footer -->
    <div class="tactical-card" style="display: flex; flex-direction: column; justify-content: space-between;">
      <div>
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
          <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(14, 165, 233, 0.1); color: #0ea5e9; display: grid; place-items: center; font-size: 18px;">
            <i class="fa-solid fa-address-book"></i>
          </div>
          <span class="badge badge-neutral">Channels</span>
        </div>
        <h4 style="font-size: 16px; font-weight: 800; color: var(--brand-deep); margin: 0 0 6px 0;">Contact & Footer</h4>
        <p style="font-size: 12.5px; color: var(--text-muted); margin: 0 0 16px 0; line-height: 1.5;">
          Boyra campus address, phone hotlines, official emails, bKash & Nagad accounts, and footer copyright.
        </p>
      </div>
      <div style="display: flex; gap: 8px; border-top: 1px solid var(--border-soft); padding-top: 12px;">
        <a href="{{ route('admin.cms.contact') }}" class="btn-tactical btn-tactical-primary" style="font-size: 12px; flex: 1; text-align: center;">
          <i class="fa-solid fa-sliders"></i> Edit Contact & Footer
        </a>
        <a href="{{ url('/contact') }}" target="_blank" class="btn-tactical btn-tactical-outline" style="font-size: 12px;" title="Live Preview">
          <i class="fa-solid fa-arrow-up-right-from-square"></i>
        </a>
      </div>
    </div>

    <!-- Card 8: Branding & Colors -->
    <div class="tactical-card" style="display: flex; flex-direction: column; justify-content: space-between;">
      <div>
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
          <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(16, 185, 129, 0.1); color: var(--brand-mint); display: grid; place-items: center; font-size: 18px;">
            <i class="fa-solid fa-palette"></i>
          </div>
          <span class="badge badge-success">Theme Engine</span>
        </div>
        <h4 style="font-size: 16px; font-weight: 800; color: var(--brand-deep); margin: 0 0 6px 0;">Branding & Colors</h4>
        <p style="font-size: 12.5px; color: var(--text-muted); margin: 0 0 16px 0; line-height: 1.5;">
          5 dynamic color pickers, upload custom logos & crests, site title, and announcement ticker.
        </p>
      </div>
      <div style="display: flex; gap: 8px; border-top: 1px solid var(--border-soft); padding-top: 12px;">
        <a href="{{ route('admin.cms.branding') }}" class="btn-tactical btn-tactical-primary" style="font-size: 12px; flex: 1; text-align: center;">
          <i class="fa-solid fa-sliders"></i> Edit Theme Colors
        </a>
        <a href="{{ url('/') }}" target="_blank" class="btn-tactical btn-tactical-outline" style="font-size: 12px;" title="Live Preview">
          <i class="fa-solid fa-arrow-up-right-from-square"></i>
        </a>
      </div>
    </div>

  </div>

  <!-- Admission Inquiries Summary Table -->
  <div class="tactical-card" style="margin-top: 10px;">
    <div style="border-bottom: 1px solid var(--border-soft); padding-bottom: 14px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
      <div>
        <h3 style="font-size: 17px; font-weight: 800; color: var(--brand-deep); margin: 0 0 4px 0;">
          <i class="fa-solid fa-envelope-open-text" style="color: var(--accent-gold);"></i> Admission Inquiries ({{ $inquiries->count() }})
        </h3>
        <p style="font-size: 12.5px; color: var(--text-muted); margin: 0;">
          Direct communication submitted from the public contact page.
        </p>
      </div>
      <div style="display: flex; gap: 8px;">
        <a href="{{ route('admin.cms.inquiries') }}" class="btn-tactical btn-tactical-primary" style="font-size: 12px;">
          <i class="fa-solid fa-arrow-right"></i> Open Inquiries Manager
        </a>
      </div>
    </div>

    <div style="overflow-x: auto;">
      <table class="table-tactical" style="width: 100%; font-size: 13px;">
        <thead>
          <tr style="background: var(--surface-subtle); text-align: left;">
            <th style="padding: 10px 14px;">Date</th>
            <th style="padding: 10px 14px;">Applicant Name</th>
            <th style="padding: 10px 14px;">Contact Details</th>
            <th style="padding: 10px 14px;">Subject & Message</th>
            <th style="padding: 10px 14px;">Status</th>
            <th style="padding: 10px 14px; text-align: right;">Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($inquiries as $inq)
            <tr style="border-bottom: 1px solid var(--border-soft); {{ $inq->status === 'unread' ? 'background: rgba(254, 242, 242, 0.5);' : '' }}">
              <td style="padding: 12px 14px; white-space: nowrap; color: var(--text-muted);">
                {{ $inq->created_at->format('d M, Y h:i A') }}
              </td>
              <td style="padding: 12px 14px; font-weight: 700; color: var(--brand-deep);">
                {{ $inq->name }}
              </td>
              <td style="padding: 12px 14px;">
                <div style="color: var(--brand-emerald); font-weight: 500;">{{ $inq->email }}</div>
                @if($inq->phone)
                  <div style="color: var(--text-muted); font-size: 11.5px;"><i class="fa-solid fa-phone"></i> {{ $inq->phone }}</div>
                @endif
              </td>
              <td style="padding: 12px 14px; max-width: 320px;">
                <strong style="color: var(--brand-deep); display: block; margin-bottom: 2px;">{{ $inq->subject }}</strong>
                <p style="margin: 0; font-size: 12px; color: var(--text-body); line-height: 1.4;">{{ $inq->message }}</p>
              </td>
              <td style="padding: 12px 14px; white-space: nowrap;">
                @if($inq->status === 'unread')
                  <span class="badge badge-danger">Unread</span>
                @elseif($inq->status === 'read')
                  <span class="badge badge-info">Read</span>
                @else
                  <span class="badge badge-success">Replied</span>
                @endif
              </td>
              <td style="padding: 12px 14px; text-align: right; white-space: nowrap;">
                <form action="{{ route('admin.cms.inquiries.status', $inq->id) }}" method="POST" style="display: inline-block; margin-right: 4px;">
                  @csrf
                  @if($inq->status === 'unread')
                    <input type="hidden" name="status" value="read">
                    <button type="submit" class="btn-tactical btn-tactical-outline" style="font-size: 11px; padding: 4px 8px;" title="Mark as Read">
                      <i class="fa-regular fa-envelope-open"></i>
                    </button>
                  @elseif($inq->status === 'read')
                    <input type="hidden" name="status" value="replied">
                    <button type="submit" class="btn-tactical btn-tactical-outline" style="font-size: 11px; padding: 4px 8px; color: var(--brand-emerald); border-color: var(--brand-emerald);" title="Mark as Replied">
                      <i class="fa-solid fa-reply"></i>
                    </button>
                  @endif
                </form>

                <form action="{{ route('admin.cms.inquiries.delete', $inq->id) }}" method="POST" onsubmit="return confirm('Delete this inquiry?');" style="display: inline-block;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn-tactical btn-tactical-outline" style="color: #ef4444; border-color: rgba(239,68,68,0.3); font-size: 11px; padding: 4px 8px;" title="Delete">
                    <i class="fa-solid fa-trash-can"></i>
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" style="padding: 24px; text-align: center; color: var(--text-muted);">
                No admission inquiries received yet.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
