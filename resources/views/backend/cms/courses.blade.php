@extends('layouts.portal')

@section('title', 'Courses - Web Management')
@section('page_title', 'Courses Page Editor')
@section('page_subtitle', 'Manage courses catalog header, program descriptions, batches, and enrollment portals')

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
          <i class="fa-solid fa-book-bookmark" style="color: var(--brand-emerald);"></i> Courses Page Header & Introductory Statement
        </h3>
        <p style="font-size: 12.5px; color: var(--text-muted); margin: 0;">
          Configure the title, eyebrow badge, and summary displayed on the top of the public <code>/courses</code> directory.
        </p>
      </div>

      <div style="display: flex; flex-direction: column; gap: 16px;">
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 16px;">
          <div>
            <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Eyebrow Badge</label>
            <input type="text" name="courses_badge" value="{{ cms('courses_badge', cms('courses_page_badge', 'ACADEMIC PROGRAMS')) }}" class="form-tactical">
          </div>
          <div>
            <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Page Heading</label>
            <input type="text" name="courses_title" value="{{ cms('courses_title', cms('courses_page_title', 'Officer Cadet Preparatory Courses')) }}" class="form-tactical">
          </div>
        </div>

        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Page Subtitle</label>
          <textarea name="courses_subtitle" rows="2" class="form-tactical">{{ cms('courses_subtitle', cms('courses_page_subtitle', 'Comprehensive training programs tailored specifically for Bangladesh Army, Navy, Airforce, and direct ISSB board preparation.')) }}</textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px;">
          <a href="{{ route('courses') }}" target="_blank" class="btn-tactical btn-tactical-outline">
            <i class="fa-solid fa-eye"></i> View Live Courses Page
          </a>
          <button type="submit" class="btn-tactical btn-tactical-primary">
            <i class="fa-solid fa-floppy-disk"></i> Save Header Texts
          </button>
        </div>
      </div>
    </div>
  </form>

  <!-- Live Programs Directory -->
  <div class="tactical-card">
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-soft); padding-bottom: 14px; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
      <div>
        <h3 style="font-size: 17px; font-weight: 800; color: var(--brand-deep); margin: 0 0 4px 0;">
          <i class="fa-solid fa-graduation-cap" style="color: var(--accent-gold);"></i> Active Preparatory Programs ({{ $courses->count() }})
        </h3>
        <p style="font-size: 12.5px; color: var(--text-muted); margin: 0;">
          Live course programs currently showcased on the public website.
        </p>
      </div>
      <div style="display: flex; gap: 8px;">
        <a href="{{ route('admin.courses.index') }}" class="btn-tactical btn-tactical-primary" style="font-size: 12px;">
          <i class="fa-solid fa-sliders"></i> Full Course Manager
        </a>
        <a href="{{ route('admin.courses.create') }}" class="btn-tactical btn-tactical-outline" style="font-size: 12px;">
          <i class="fa-solid fa-plus"></i> Add New Course
        </a>
      </div>
    </div>

    <div style="overflow-x: auto;">
      <table class="table-tactical" style="width: 100%; font-size: 13px;">
        <thead>
          <tr style="background: var(--surface-subtle); text-align: left;">
            <th style="padding: 10px 14px;">Course Title & Slug</th>
            <th style="padding: 10px 14px;">Target Wing / Category</th>
            <th style="padding: 10px 14px;">Duration</th>
            <th style="padding: 10px 14px;">Course Fee</th>
            <th style="padding: 10px 14px;">Active Batches</th>
            <th style="padding: 10px 14px; text-align: right;">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($courses as $c)
            <tr style="border-bottom: 1px solid var(--border-soft);">
              <td style="padding: 12px 14px;">
                <strong style="color: var(--brand-deep); display: block;">{{ $c->title }}</strong>
                <span class="badge badge-neutral" style="font-size: 10px;">{{ $c->slug }}</span>
              </td>
              <td style="padding: 12px 14px; text-transform: capitalize;">
                <span class="badge badge-success">{{ str_replace('_', ' ', $c->category ?? 'General') }}</span>
              </td>
              <td style="padding: 12px 14px;">{{ $c->duration ?? '8 Weeks' }}</td>
              <td style="padding: 12px 14px; font-weight: 700; color: var(--brand-emerald);">
                ৳{{ number_format($c->fee ?? 0, 0) }}
              </td>
              <td style="padding: 12px 14px;">
                <span class="badge badge-info">{{ $c->batches_count ?? 0 }} Batches</span>
              </td>
              <td style="padding: 12px 14px; text-align: right;">
                <a href="{{ route('admin.courses.edit', $c->id) }}" class="btn-tactical btn-tactical-outline" style="font-size: 11px; padding: 4px 10px;">
                  <i class="fa-solid fa-pen-to-square"></i> Edit
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
