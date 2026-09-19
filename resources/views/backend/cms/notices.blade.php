@extends('layouts.portal')

@section('title', 'Notices Page CMS')
@section('page_title', 'Notices & Circulars Publisher')
@section('page_subtitle', 'Broadcast official academy notices, batch commencement schedules, urgent warnings, and circulars')

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px;">

  @include('backend.cms.partials.nav')

  @if(session('success'))
    <div class="alert alert-success" style="background: rgba(16, 185, 129, 0.15); border: 1px solid var(--brand-mint); color: #065f46; padding: 12px 16px; border-radius: var(--radius-sm); font-size: 13px; font-weight: 600;">
      <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
  @endif

  <!-- Page Header Settings -->
  <form action="{{ route('admin.cms.settings') }}" method="POST">
    @csrf
    <div class="tactical-card" style="margin-bottom: 24px;">
      <div style="border-bottom: 1px solid var(--border-soft); padding-bottom: 14px; margin-bottom: 20px;">
        <h3 style="font-size: 17px; font-weight: 800; color: var(--brand-deep); margin: 0 0 6px 0;">
          <i class="fa-solid fa-bullhorn" style="color: var(--brand-emerald);"></i> Notices Page Introductory Statement
        </h3>
        <p style="font-size: 12.5px; color: var(--text-muted); margin: 0;">
          Header badge and text displayed on `/notices`.
        </p>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 16px; margin-bottom: 14px;">
        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Eyebrow Badge</label>
          <input type="text" name="notices_badge" value="{{ cms('notices_badge', cms('notices_page_badge', 'OFFICIAL ACADEMY NOTICES')) }}" class="form-tactical">
        </div>
        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Page Heading</label>
          <input type="text" name="notices_title" value="{{ cms('notices_title', cms('notices_page_title', 'Notices & Circulars')) }}" class="form-tactical">
        </div>
      </div>
      <div style="margin-bottom: 14px;">
        <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Page Subtitle</label>
        <textarea name="notices_subtitle" rows="2" class="form-tactical">{{ cms('notices_subtitle', cms('notices_page_subtitle', 'Official circulars, batch commencement dates, assessment timetables, and defense wing notifications issued by the Directorate of Admissions.')) }}</textarea>
      </div>
      <div style="display: flex; justify-content: flex-end;">
        <button type="submit" class="btn-tactical btn-tactical-primary">
          <i class="fa-solid fa-floppy-disk"></i> Save Header Texts
        </button>
      </div>
    </div>
  </form>

  <!-- Publish Notice Form -->
  <div class="tactical-card" style="margin-bottom: 24px;">
    <div style="border-bottom: 1px solid var(--border-soft); padding-bottom: 14px; margin-bottom: 20px;">
      <h3 style="font-size: 17px; font-weight: 800; color: var(--brand-deep); margin: 0 0 6px 0;">
        <i class="fa-solid fa-feather-pointed" style="color: var(--accent-gold);"></i> Compose & Publish New Official Notice
      </h3>
      <p style="font-size: 12.5px; color: var(--text-muted); margin: 0;">
        Immediately publishes to the public notice bulletin with optional urgent alert styling.
      </p>
    </div>

    <form action="{{ route('admin.cms.notices.store') }}" method="POST">
      @csrf
      <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 16px; margin-bottom: 14px;">
        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Notice Title *</label>
          <input type="text" name="title" class="form-tactical" placeholder="e.g. Schedule for BMA 95 Long Course Orientation" required>
        </div>
        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Category *</label>
          <select name="category" class="form-tactical" required>
            <option value="Admission">Admission</option>
            <option value="Academic">Academic</option>
            <option value="Exam">Exam</option>
            <option value="General">General</option>
            <option value="Event">Event</option>
          </select>
        </div>
        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Publish Date *</label>
          <input type="date" name="publish_date" class="form-tactical" value="{{ date('Y-m-d') }}" required>
        </div>
      </div>

      <div style="margin-bottom: 14px;">
        <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Notice Content / Description *</label>
        <textarea name="content" rows="4" class="form-tactical" placeholder="Enter complete circular text, instructions, and dates..." required></textarea>
      </div>

      <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div style="display: flex; gap: 20px;">
          <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; cursor: pointer; color: #dc2626;">
            <input type="checkbox" name="is_urgent" value="1" style="width: 16px; height: 16px;">
            <span>Mark as Urgent Priority</span>
          </label>
          <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; cursor: pointer; color: var(--brand-emerald);">
            <input type="checkbox" name="is_pinned" value="1" style="width: 16px; height: 16px;">
            <span>Pin to Top of Bulletin</span>
          </label>
        </div>
        <button type="submit" class="btn-tactical btn-tactical-primary">
          <i class="fa-solid fa-bullhorn"></i> Publish Notice
        </button>
      </div>
    </form>
  </div>

  <!-- Published Notices Archive Table -->
  <div class="tactical-card">
    <div style="border-bottom: 1px solid var(--border-soft); padding-bottom: 14px; margin-bottom: 20px;">
      <h3 style="font-size: 17px; font-weight: 800; color: var(--brand-deep); margin: 0 0 6px 0;">
        <i class="fa-solid fa-list-ul" style="color: var(--brand-emerald);"></i> Published Official Notices ({{ $notices->count() }})
      </h3>
      <p style="font-size: 12.5px; color: var(--text-muted); margin: 0;">
        Archive of active circulars on the bulletin.
      </p>
    </div>

    <div style="overflow-x: auto;">
      <table class="table-tactical" style="width: 100%; font-size: 13px;">
        <thead>
          <tr style="background: var(--surface-subtle); text-align: left;">
            <th style="padding: 10px 14px;">Date</th>
            <th style="padding: 10px 14px;">Category</th>
            <th style="padding: 10px 14px;">Title & Summary</th>
            <th style="padding: 10px 14px;">Flags</th>
            <th style="padding: 10px 14px; text-align: right;">Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($notices as $notice)
            <tr style="border-bottom: 1px solid var(--border-soft);">
              <td style="padding: 12px 14px; white-space: nowrap; color: var(--text-muted);">
                {{ \Carbon\Carbon::parse($notice->publish_date)->format('d M, Y') }}
              </td>
              <td style="padding: 12px 14px;">
                <span class="badge badge-neutral">{{ $notice->category }}</span>
              </td>
              <td style="padding: 12px 14px;">
                <strong style="color: var(--brand-deep); display: block; margin-bottom: 2px;">{{ $notice->title }}</strong>
                <span style="color: var(--text-muted); font-size: 11.5px;">{{ \Illuminate\Support\Str::limit($notice->content, 85) }}</span>
              </td>
              <td style="padding: 12px 14px; white-space: nowrap;">
                @if($notice->is_urgent)
                  <span class="badge badge-danger" style="margin-right: 4px;">Urgent</span>
                @endif
                @if($notice->is_pinned)
                  <span class="badge badge-warning">Pinned</span>
                @endif
              </td>
              <td style="padding: 12px 14px; text-align: right;">
                <form action="{{ route('admin.cms.notices.delete', $notice->id) }}" method="POST" onsubmit="return confirm('Delete this notice?');" style="display: inline;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn-tactical btn-tactical-outline" style="color: #ef4444; border-color: rgba(239,68,68,0.3); font-size: 11px; padding: 4px 10px;">
                    <i class="fa-solid fa-trash-can"></i> Delete
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" style="padding: 24px; text-align: center; color: var(--text-muted);">
                No notices published yet. Use the form above to post your first circular.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
