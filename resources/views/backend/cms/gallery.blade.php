@extends('layouts.portal')

@section('title', 'Gallery Page CMS')
@section('page_title', 'Campus Gallery Editor')
@section('page_subtitle', 'Upload training drill photographs, obstacle course sessions, parade activities, and ceremonies')

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
          <i class="fa-solid fa-images" style="color: var(--brand-emerald);"></i> Gallery Page Introductory Statement
        </h3>
        <p style="font-size: 12.5px; color: var(--text-muted); margin: 0;">
          Header badge and text displayed on `/gallery`.
        </p>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 16px; margin-bottom: 14px;">
        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Eyebrow Badge</label>
          <input type="text" name="gallery_badge" value="{{ cms('gallery_badge', cms('gallery_page_badge', 'LIFE AT IMPERIAL DEFENCE ACADEMY')) }}" class="form-tactical">
        </div>
        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Page Heading</label>
          <input type="text" name="gallery_title" value="{{ cms('gallery_title', cms('gallery_page_title', 'Campus & Training Gallery')) }}" class="form-tactical">
        </div>
      </div>
      <div style="margin-bottom: 14px;">
        <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Page Subtitle</label>
        <textarea name="gallery_subtitle" rows="2" class="form-tactical">{{ cms('gallery_subtitle', cms('gallery_page_subtitle', 'Visual chronicles of discipline, outdoor leadership tasks, physical conditioning, psychological sessions, and ceremonial milestones.')) }}</textarea>
      </div>
      <div style="display: flex; justify-content: flex-end;">
        <button type="submit" class="btn-tactical btn-tactical-primary">
          <i class="fa-solid fa-floppy-disk"></i> Save Header Texts
        </button>
      </div>
    </div>
  </form>

  <!-- Photo Upload Form -->
  <div class="tactical-card" style="margin-bottom: 24px;">
    <div style="border-bottom: 1px solid var(--border-soft); padding-bottom: 14px; margin-bottom: 20px;">
      <h3 style="font-size: 17px; font-weight: 800; color: var(--brand-deep); margin: 0 0 6px 0;">
        <i class="fa-solid fa-cloud-arrow-up" style="color: var(--accent-gold);"></i> Upload New Photo to Archive
      </h3>
      <p style="font-size: 12.5px; color: var(--text-muted); margin: 0;">
        Add new images to the public gallery with category filtering.
      </p>
    </div>

    <form action="{{ route('admin.cms.gallery.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-bottom: 14px;">
        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Photo Title *</label>
          <input type="text" name="title" class="form-tactical" placeholder="e.g. Obstacle Course Drill Simulation" required>
        </div>
        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Category *</label>
          <select name="category" class="form-tactical" required>
            <option value="training">Training</option>
            <option value="cadets">Cadets</option>
            <option value="campus">Campus</option>
            <option value="drills">Drills</option>
            <option value="ceremonies">Ceremonies</option>
          </select>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-bottom: 16px;">
        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Caption / Narrative</label>
          <input type="text" name="caption" class="form-tactical" placeholder="Brief description of the event or session">
        </div>
        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Select Image File</label>
          <input type="file" name="image_file" class="form-tactical" accept="image/*">
        </div>
      </div>

      <div style="display: flex; justify-content: flex-end;">
        <button type="submit" class="btn-tactical btn-tactical-primary">
          <i class="fa-solid fa-plus"></i> Upload Photo
        </button>
      </div>
    </form>
  </div>

  <!-- Photo Archive Grid -->
  <div class="tactical-card">
    <div style="border-bottom: 1px solid var(--border-soft); padding-bottom: 14px; margin-bottom: 20px;">
      <h3 style="font-size: 17px; font-weight: 800; color: var(--brand-deep); margin: 0 0 6px 0;">
        <i class="fa-solid fa-photo-film" style="color: var(--brand-emerald);"></i> Current Public Photos ({{ $gallery->count() }})
      </h3>
      <p style="font-size: 12.5px; color: var(--text-muted); margin: 0;">
        Manage or remove existing gallery items.
      </p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px;">
      @forelse($gallery as $item)
        <div style="background: var(--surface-subtle); border: 1px solid var(--border-soft); border-radius: var(--radius-sm); overflow: hidden; display: flex; flex-direction: column;">
          <div style="height: 140px; background: #0f172a; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative;">
            @if($item->image_path && file_exists(public_path($item->image_path)))
              <img src="{{ asset($item->image_path) }}" alt="{{ $item->title }}" style="width: 100%; height: 100%; object-fit: cover;">
            @else
              <div style="text-align: center; color: #94a3b8;">
                <i class="fa-solid fa-image" style="font-size: 32px; margin-bottom: 6px;"></i>
                <div style="font-size: 11px;">Default Theme Graphic</div>
              </div>
            @endif
            <span class="badge badge-primary" style="position: absolute; top: 8px; left: 8px; font-size: 10px; text-transform: uppercase;">
              {{ $item->category }}
            </span>
          </div>

          <div style="padding: 12px; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
              <strong style="color: var(--brand-deep); font-size: 13px; display: block; margin-bottom: 4px;">{{ $item->title }}</strong>
              @if($item->caption)
                <p style="font-size: 11.5px; color: var(--text-muted); margin: 0 0 8px 0; line-height: 1.4;">{{ $item->caption }}</p>
              @endif
            </div>
            <div style="margin-top: 10px; display: flex; justify-content: flex-end;">
              <form action="{{ route('admin.cms.gallery.delete', $item->id) }}" method="POST" onsubmit="return confirm('Delete this gallery photo?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-tactical btn-tactical-outline" style="color: #ef4444; border-color: rgba(239,68,68,0.3); font-size: 11px; padding: 4px 10px;">
                  <i class="fa-solid fa-trash-can"></i> Delete
                </button>
              </form>
            </div>
          </div>
        </div>
      @empty
        <div style="grid-column: 1 / -1; padding: 30px; text-align: center; color: var(--text-muted);">
          <i class="fa-solid fa-camera-retro" style="font-size: 36px; margin-bottom: 10px; color: #cbd5e1;"></i>
          <p style="margin: 0; font-size: 13px;">No photos uploaded yet. Use the upload form above to add photographs.</p>
        </div>
      @endforelse
    </div>
  </div>

</div>
@endsection
