@extends('layouts.portal')

@section('title', 'Edit Course: ' . $course->title)
@section('page_title', 'Edit Course')
@section('page_subtitle', 'Configure preparatory program identity, branch wing, officer track, duration, and tuition fee')

@section('topbar_actions')
  <a href="{{ route('admin.courses.index', ['branch' => $course->branch]) }}" class="btn-tactical" style="background: rgba(255,255,255,0.06); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.12); padding: 8px 16px; font-size: 13px; font-weight: 700; text-decoration: none; border-radius: 8px; display: inline-flex; align-items: center; gap: 7px;">
    <i class="fa-solid fa-arrow-left"></i> Back to Courses
  </a>
@endsection

@section('content')
<div style="max-width: 900px; margin: 0 auto; width: 100%;">

  {{-- Breadcrumb --}}
  <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #64748b; margin-bottom: 24px;">
    <a href="{{ route('admin.dashboard') }}" style="color: #94a3b8; text-decoration: none;">Dashboard</a>
    <i class="fa-solid fa-chevron-right" style="font-size: 10px;"></i>
    <a href="{{ route('admin.courses.index') }}" style="color: #94a3b8; text-decoration: none;">Courses</a>
    <i class="fa-solid fa-chevron-right" style="font-size: 10px;"></i>
    <span style="color: #ff5757; font-weight: 700;">Edit Course</span>
  </div>

  @if(session('success'))
    <div style="background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.35); border-radius: 10px; padding: 12px 16px; margin-bottom: 20px; color: #34d399; font-size: 13px; display: flex; align-items: center; gap: 8px;">
      <i class="fa-solid fa-circle-check"></i>
      <span>{{ session('success') }}</span>
    </div>
  @endif

  @if(isset($errors) && $errors->any())
    <div style="background: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.35); border-radius: 12px; padding: 16px 20px; margin-bottom: 24px; color: #fca5a5; font-size: 13px;">
      <div style="font-weight: 700; margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-triangle-exclamation" style="font-size: 16px;"></i>
        <span>Please resolve the following errors:</span>
      </div>
      <ul style="margin: 0; padding-left: 22px; font-size: 12.5px; line-height: 1.6;">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="content-panel" style="background: #181c26; border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 16px; padding: 26px 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.25);">
    <div style="border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 16px; margin-bottom: 22px; display: flex; justify-content: space-between; align-items: center;">
      <h3 style="font-size: 16px; font-weight: 800; margin: 0; color: #ffffff; display: flex; align-items: center; gap: 10px;">
        <span style="width: 34px; height: 34px; border-radius: 9px; background: rgba(255, 87, 87, 0.15); color: #ff5757; display: grid; place-items: center; font-size: 15px;">
          <i class="fa-solid fa-pen-to-square"></i>
        </span>
        Course Identity &amp; Curriculum Parameters
      </h3>
      <span class="badge" style="background: rgba(255,255,255,0.06); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.12); font-size: 11px;">
        ID #{{ $course->id }}
      </span>
    </div>

    <form action="{{ route('admin.courses.update', $course->id) }}" method="POST">
      @csrf
      @method('PUT')

      <div style="display: grid; grid-template-columns: 3fr 1fr; gap: 16px; margin-bottom: 18px;">
        <div>
          <label class="ida-label">Course Title *</label>
          <input type="text" name="title" value="{{ old('title', $course->title) }}" required class="form-tactical" style="width: 100%; padding: 11px 16px; font-size: 14px; font-weight: 700;">
        </div>
        <div>
          <label class="ida-label">Course Code</label>
          <input type="text" name="course_code" value="{{ old('course_code', $course->course_code) }}" class="form-tactical" style="width: 100%; padding: 11px 16px; font-size: 14px;">
        </div>
      </div>

      {{-- Standardized Lineup: Branch and Dynamic Program Track --}}
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 18px;">
        <div>
          <label class="ida-label">Branch Wing *</label>
          <select name="branch" id="editPageBranchSelect" class="form-tactical" required style="width: 100%; padding: 11px 16px; font-size: 13.5px;" onchange="updateEditPageTracks()">
            <option value="navy" {{ old('branch', $course->branch ?? $course->branch_key) === 'navy' ? 'selected' : '' }}>Navy (BNA)</option>
            <option value="police" {{ old('branch', $course->branch ?? $course->branch_key) === 'police' ? 'selected' : '' }}>Police Service</option>
            <option value="army" {{ old('branch', $course->branch ?? $course->branch_key) === 'army' ? 'selected' : '' }}>Army (BMA)</option>
            <option value="air_force" {{ old('branch', $course->branch ?? $course->branch_key) === 'air_force' ? 'selected' : '' }}>Air Force (BAFA)</option>
          </select>
        </div>

        <div>
          <label class="ida-label">Officer / Program Track *</label>
          <select name="target_track" id="editPageTrackSelect" class="form-tactical" required style="width: 100%; padding: 11px 16px; font-size: 13.5px;">
            <!-- Dynamically populated via JS -->
          </select>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 18px;">
        <div>
          <label class="ida-label">Duration *</label>
          <input type="text" name="duration" value="{{ old('duration', $course->duration) }}" required class="form-tactical" style="width: 100%; padding: 11px 16px; font-size: 13.5px;">
        </div>
        <div>
          <label class="ida-label">Tuition Fee (BDT) *</label>
          <input type="number" name="fee" value="{{ old('fee', (int)$course->fee) }}" required class="form-tactical" style="width: 100%; padding: 11px 16px; font-size: 13.5px;">
        </div>
        <div>
          <label class="ida-label">Admission Status *</label>
          <select name="admission_status" class="form-tactical" style="width: 100%; padding: 11px 16px; font-size: 13.5px;">
            <option value="open" {{ old('admission_status', $course->admission_status) === 'open' ? 'selected' : '' }}>Open</option>
            <option value="upcoming" {{ old('admission_status', $course->admission_status) === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
            <option value="closed" {{ old('admission_status', $course->admission_status) === 'closed' ? 'selected' : '' }}>Closed</option>
          </select>
        </div>
      </div>

      <div style="margin-bottom: 18px;">
        <label class="ida-label">Eligibility Criteria</label>
        <input type="text" name="eligibility" value="{{ old('eligibility', $course->eligibility) }}" class="form-tactical" style="width: 100%; padding: 11px 16px; font-size: 13.5px;">
      </div>

      <div style="margin-bottom: 18px;">
        <label class="ida-label">Schedule Information</label>
        <input type="text" name="schedule_info" value="{{ old('schedule_info', $course->schedule_info) }}" class="form-tactical" style="width: 100%; padding: 11px 16px; font-size: 13.5px;">
      </div>

      <div style="margin-bottom: 18px;">
        <label class="ida-label">Description &amp; Overview</label>
        <textarea name="description" rows="4" class="form-tactical" style="width: 100%; padding: 11px 16px; font-size: 13px; line-height: 1.5;">{{ old('description', $course->description) }}</textarea>
      </div>

      <div style="margin-bottom: 24px;">
        <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; color: #ffffff; cursor: pointer;">
          <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $course->is_featured) ? 'checked' : '' }}>
          <span>Feature on Academy Homepage</span>
        </label>
      </div>

      <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(255,255,255,0.08); padding-top: 18px;">
        <a href="{{ route('admin.courses.index', ['branch' => $course->branch]) }}" class="btn-tactical" style="background: rgba(255,255,255,0.06); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.12); padding: 10px 20px; font-size: 13px; font-weight: 600; text-decoration: none; border-radius: 8px;">
          Cancel &amp; Return
        </a>
        <button type="submit" class="btn-primary" style="background: #ff5757; color: #ffffff; border: none; padding: 10px 28px; font-size: 13.5px; font-weight: 800; border-radius: 8px; cursor: pointer;">
          <i class="fa-solid fa-floppy-disk"></i> Update Course
        </button>
      </div>
    </form>
  </div>
</div>

<style>
  .ida-label {
    display: block; font-size: 11px; font-weight: 700; color: #94a3b8;
    text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;
  }
  .form-tactical {
    background: #11141d;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    color: #ffffff;
    box-sizing: border-box;
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s;
  }
  .form-tactical:focus {
    border-color: #ff5757;
    box-shadow: 0 0 0 3px rgba(255, 87, 87, 0.18);
  }
</style>

<script>
  const editPagePresets = {
    navy: [
      { value: 'preliminary', label: 'Preliminary (Officer Exam 1)' },
      { value: 'issb', label: 'ISSB Special Masterclass (Officer Exam 2 - Universal Tri-Services)' },
      { value: 'soldier', label: 'Sailor (Navy Non-Commissioned)' }
    ],
    police: [
      { value: 'constable', label: 'Constable Recruitment Course' },
      { value: 'si', label: 'Sub-Inspector (SI) Comprehensive Track' },
      { value: 'asi', label: 'Assistant Sub-Inspector (ASI) Track' }
    ],
    army: [
      { value: 'preliminary', label: 'Preliminary (Officer Exam 1)' },
      { value: 'issb', label: 'ISSB Special Masterclass (Officer Exam 2 - Universal Tri-Services)' },
      { value: 'soldier', label: 'Soldier / Sainik (Army Non-Commissioned)' }
    ],
    air_force: [
      { value: 'preliminary', label: 'Preliminary (Officer Exam 1)' },
      { value: 'issb', label: 'ISSB Special Masterclass (Officer Exam 2 - Universal Tri-Services)' },
      { value: 'soldier', label: 'Airman (Air Force Non-Commissioned)' }
    ]
  };

  function updateEditPageTracks(preselectedValue = null) {
    const branchEl = document.getElementById('editPageBranchSelect');
    const trackEl = document.getElementById('editPageTrackSelect');
    if (!branchEl || !trackEl) return;

    const branch = branchEl.value;
    const tracks = editPagePresets[branch] || editPagePresets.navy;
    const fallback = (branch === 'police') ? 'si' : 'preliminary';
    const targetVal = preselectedValue || trackEl.value || fallback;

    trackEl.innerHTML = tracks.map(t => `
      <option value="${t.value}" ${t.value === targetVal ? 'selected' : ''}>${t.label}</option>
    `).join('');
  }

  document.addEventListener('DOMContentLoaded', function() {
    updateEditPageTracks("{{ old('target_track', $course->target_track ?? $course->program_track) }}");
  });
</script>
@endsection
