@extends('layouts.portal')

@section('title', 'Edit Cadet: ' . $student->user->name)
@section('page_title', 'Update Cadet Details')

@section('content')
<div style="max-width: 1100px; margin: 0 auto;">

  <!-- Top Action Bar -->
  <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 24px;">
    <div style="display: flex; gap: 10px;">
      <a href="{{ route('admin.student_accounts.index') }}" class="btn-tactical" style="background: rgba(255,255,255,0.06); color: #94a3b8; border: 1px solid rgba(255,255,255,0.12); padding: 8px 16px; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-arrow-left"></i> Back to Cadet Management
      </a>
      <a href="{{ route('admin.student_accounts.show', $student->id) }}" class="btn-tactical" style="background: rgba(255,255,255,0.06); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.12); padding: 8px 16px; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-eye"></i> View Details
      </a>
    </div>

    <span style="font-size: 12px; color: #64748b;">
      ID: #{{ $student->id }}
    </span>
  </div>

  @if(session('success'))
    <div style="background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.35); border-radius: 10px; padding: 14px 18px; margin-bottom: 24px; color: #34d399; font-size: 13.5px; display: flex; align-items: center; gap: 10px;">
      <i class="fa-solid fa-circle-check" style="font-size: 16px;"></i>
      <span>{{ session('success') }}</span>
    </div>
  @endif

  @if(session('error'))
    <div style="background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.35); border-radius: 10px; padding: 14px 18px; margin-bottom: 24px; color: #f87171; font-size: 13.5px; display: flex; align-items: center; gap: 10px;">
      <i class="fa-solid fa-triangle-exclamation" style="font-size: 16px;"></i>
      <span>{{ session('error') }}</span>
    </div>
  @endif

  @if($errors->any())
    <div style="background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.35); border-radius: 10px; padding: 16px 20px; margin-bottom: 24px; color: #fca5a5; font-size: 13px;">
      <strong style="display: block; margin-bottom: 6px;"><i class="fa-solid fa-triangle-exclamation"></i> Please resolve the following errors:</strong>
      <ul style="margin: 0; padding-left: 20px; line-height: 1.6;">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @php
    $currentId = old('custom_id', $student->student_id_code ?: ($student->user->account_id ?? ''));
    $enrolledCourseIds = old('course_ids', $student->courses->pluck('id')->toArray());
    if (empty($enrolledCourseIds) && $student->current_course_id) {
      $enrolledCourseIds = [(int)$student->current_course_id];
    }
    $activeWing = old('branch_wing', $currentWing ?? ($student->target_wing ? (str_contains(strtolower($student->target_wing), 'navy') ? 'Navy' : (str_contains(strtolower($student->target_wing), 'police') ? 'Police' : (str_contains(strtolower($student->target_wing), 'air') ? 'Air Force' : (str_contains(strtolower($student->target_wing), 'general') ? 'General' : 'Army')))) : 'Army'));
    $activeTracks = old('category_tracks', $currentTracks ?? $student->getTargetTracks());
    if (!is_array($activeTracks)) {
      $activeTracks = [];
    }
  @endphp

  <!-- Main Update Form -->
  <form method="POST" action="{{ route('admin.student_accounts.update', $student->id) }}" id="updateStudentForm">
    @csrf
    @method('PUT')

    <!-- Account Identity -->
    <div class="content-panel" style="margin-bottom: 24px;">
      <div class="panel-header" style="margin-bottom: 18px;">
        <h3 style="font-size: 15px; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-id-card-clip" style="color: #ff5757;"></i>
          <span>Account Identity</span>
        </h3>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px;">
        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">
            Cadet Login ID *
          </label>
          <input type="text" name="custom_id" required value="{{ $currentId }}"
                 class="form-control"
                 style="font-family: monospace; font-size: 14px; font-weight: 700; color: #ff8585; text-transform: uppercase;"
                 placeholder="e.g. 250236">
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">
            Registration Type *
          </label>
          <select name="student_type" class="form-control" style="font-size: 13.5px;">
            <option value="offline" {{ old('student_type', $student->student_type) === 'offline' ? 'selected' : '' }}>Offline (Staff Enrolled)</option>
            <option value="online" {{ old('student_type', $student->student_type) === 'online' ? 'selected' : '' }}>Online (Self-Registered)</option>
            <option value="academic" {{ old('student_type', $student->student_type) === 'academic' ? 'selected' : '' }}>Academic Cadet</option>
            <option value="external" {{ old('student_type', $student->student_type) === 'external' ? 'selected' : '' }}>External Candidate</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Personal Information -->
    <div class="content-panel" style="margin-bottom: 24px;">
      <div class="panel-header" style="margin-bottom: 18px;">
        <h3 style="font-size: 15px; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-user" style="color: #8c96a8;"></i>
          <span>Personal Information</span>
        </h3>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px;">Full Name *</label>
          <input type="text" name="name" required value="{{ old('name', $student->user->name) }}" class="form-control" placeholder="Candidate's legal name">
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px;">Email Address *</label>
          <input type="email" name="email" required value="{{ old('email', $student->user->email) }}" class="form-control" placeholder="student@example.com">
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px;">Mobile / WhatsApp Number *</label>
          <input type="text" name="phone" required value="{{ old('phone', $student->user->phone) }}" class="form-control" placeholder="01XXXXXXXXX">
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px;">Age *</label>
          <input type="number" name="age" min="10" max="100" value="{{ old('age', $student->age) }}" class="form-control" placeholder="e.g. 21">
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px;">Gender *</label>
          <select name="gender" class="form-control">
            <option value="male" {{ old('gender', $student->gender) === 'male' ? 'selected' : '' }}>Male</option>
            <option value="female" {{ old('gender', $student->gender) === 'female' ? 'selected' : '' }}>Female</option>
            <option value="other" {{ old('gender', $student->gender) === 'other' ? 'selected' : '' }}>Other</option>
          </select>
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px;">Educational Institution</label>
          <input type="text" name="institution" value="{{ old('institution', $student->institution) }}" class="form-control" placeholder="College / University">
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px;">HSC / Passing Year</label>
          <input type="text" name="hsc_year" value="{{ old('hsc_year', $student->hsc_year) }}" class="form-control" placeholder="e.g. 2025">
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px;">Home District</label>
          <input type="text" name="district" value="{{ old('district', $student->district) }}" class="form-control" placeholder="e.g. Khulna, Dhaka">
        </div>

        <div style="grid-column: span 2;">
          <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px;">Full Address *</label>
          <textarea name="address" required rows="2" class="form-control" placeholder="House, Road, Area, Thana, District">{{ old('address', $student->address) }}</textarea>
        </div>
      </div>
    </div>

    <!-- Service Wing & Examination Category Allocation Panel -->
    <div class="content-panel" style="margin-bottom: 24px;">
      <div class="panel-header" style="margin-bottom: 18px;">
        <h3 style="font-size: 15px; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-sitemap" style="color: #ff5757;"></i>
          <span>Service Wing &amp; Category Track Allocation</span>
        </h3>
        <span style="font-size: 11px; color: #8c96a8;">Configure course branch and examination categories (Prelim, ISSB, Police sub-tracks)</span>
      </div>

      <!-- Step 1: Select Service Course / Branch -->
      <div style="margin-bottom: 20px;">
        <label style="display: block; font-size: 11.5px; font-weight: 800; color: #cbd5e1; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 10px;">
          Step 1: Select Course / Branch *
        </label>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 10px;">
          @foreach([
            'Army' => ['label' => 'Bangladesh Army', 'icon' => 'fa-person-military-rifle', 'color' => '#f97316'],
            'Navy' => ['label' => 'Bangladesh Navy', 'icon' => 'fa-anchor', 'color' => '#38bdf8'],
            'Air Force' => ['label' => 'Bangladesh Air Force', 'icon' => 'fa-jet-fighter', 'color' => '#60a5fa'],
            'Police' => ['label' => 'Bangladesh Police', 'icon' => 'fa-shield-halved', 'color' => '#c084fc'],
            'General' => ['label' => 'General / Tri-Service', 'icon' => 'fa-globe', 'color' => '#34d399']
          ] as $wingKey => $wingMeta)
            @php $isSelectedWing = ($activeWing === $wingKey); @endphp
            <label class="wing-radio-card" id="wing_card_{{ Str::slug($wingKey) }}"
                   style="display: flex; align-items: center; gap: 10px; padding: 12px 14px; border-radius: 10px; cursor: pointer; transition: all 0.2s; border: 1.5px solid {{ $isSelectedWing ? '#ff5757' : 'rgba(255,255,255,0.08)' }}; background: {{ $isSelectedWing ? 'rgba(255,87,87,0.1)' : 'rgba(255,255,255,0.02)' }};">
              <input type="radio" name="branch_wing" value="{{ $wingKey }}"
                     {{ $isSelectedWing ? 'checked' : '' }}
                     onchange="onBranchWingChanged('{{ $wingKey }}')"
                     style="accent-color: #ff5757; width: 16px; height: 16px;">
              <div>
                <strong style="display: block; font-size: 12.5px; color: {{ $isSelectedWing ? '#ffffff' : '#cbd5e1' }};">
                  <i class="fa-solid {{ $wingMeta['icon'] }}" style="color: {{ $wingMeta['color'] }}; margin-right: 4px;"></i> {{ $wingMeta['label'] }}
                </strong>
              </div>
            </label>
          @endforeach
        </div>
        <input type="hidden" name="target_wing" id="composedTargetWingHidden" value="{{ $student->target_wing }}">
      </div>

      <!-- Step 2: Dynamic Category / Track Place Selection -->
      <div id="categoryTracksContainer" style="padding-top: 18px; border-top: 1px solid rgba(255,255,255,0.06);">
        
        <!-- A. Military Tracks Container (Army, Navy, Air Force, General) -->
        <div id="militaryTracksSection" style="{{ $activeWing === 'Police' ? 'display: none;' : 'display: block;' }}">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 8px;">
            <div>
              <label style="display: block; font-size: 11.5px; font-weight: 800; color: #cbd5e1; text-transform: uppercase; letter-spacing: 0.6px; margin: 0;">
                Step 2: Select Examination Category / Track:
              </label>
              <span style="font-size: 11.5px; color: #94a3b8;">
                Candidate can be placed in <strong style="color: #60a5fa;">Preliminary</strong>, <strong style="color: #facc15;">ISSB</strong>, or <strong style="color: #34d399;">both can be selected</strong>:
              </span>
            </div>
            <!-- Quick Preset Pills -->
            <div style="display: flex; gap: 6px; flex-wrap: wrap;">
              <button type="button" onclick="setMilitaryPreset('prelim')" class="btn-tactical" style="padding: 4px 10px; font-size: 11px; background: rgba(59,130,246,0.12); color: #60a5fa; border: 1px solid rgba(59,130,246,0.3); border-radius: 6px;">
                <i class="fa-solid fa-file-pen"></i> Prelim Only
              </button>
              <button type="button" onclick="setMilitaryPreset('issb')" class="btn-tactical" style="padding: 4px 10px; font-size: 11px; background: rgba(234,179,8,0.12); color: #facc15; border: 1px solid rgba(234,179,8,0.3); border-radius: 6px;">
                <i class="fa-solid fa-star"></i> ISSB Only
              </button>
              <button type="button" onclick="setMilitaryPreset('both')" class="btn-tactical" style="padding: 4px 12px; font-size: 11px; font-weight: 700; background: rgba(16,185,129,0.15); color: #34d399; border: 1px solid rgba(16,185,129,0.35); border-radius: 6px;">
                <i class="fa-solid fa-check-double"></i> Both (Prelim + ISSB)
              </button>
            </div>
          </div>

          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 12px;">
            <!-- Preliminary Card -->
            @php $hasPrelimChecked = in_array('prelim', $activeTracks); @endphp
            <label id="card_track_prelim" class="track-card"
                   style="display: block; cursor: pointer; border: 1.5px solid {{ $hasPrelimChecked ? '#3b82f6' : 'rgba(255,255,255,0.08)' }}; background: {{ $hasPrelimChecked ? 'rgba(59,130,246,0.09)' : 'rgba(255,255,255,0.02)' }}; border-radius: 10px; padding: 14px; transition: all 0.2s;">
              <div style="display: flex; align-items: flex-start; gap: 10px;">
                <input type="checkbox" name="category_tracks[]" value="prelim" id="input_track_prelim"
                       {{ $hasPrelimChecked ? 'checked' : '' }}
                       onchange="onTrackCheckboxChanged()"
                       style="accent-color: #3b82f6; width: 18px; height: 18px; margin-top: 2px;">
                <div style="flex: 1;">
                  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                    <strong style="font-size: 13px; color: #ffffff;">Preliminary (Prelim)</strong>
                    <span style="font-size: 9.5px; font-weight: 700; color: #60a5fa; background: rgba(59,130,246,0.18); border: 1px solid rgba(59,130,246,0.35); padding: 1px 6px; border-radius: 4px;">Branch Exclusive</span>
                  </div>
                  <p style="font-size: 11.5px; color: #94a3b8; margin: 0; line-height: 1.45;">
                    Authorized to conduct Preliminary written, IQ, and branch-specific academic exam modules for <strong id="prelimBranchText" style="color: #cbd5e1;">{{ $activeWing }}</strong>.
                  </p>
                </div>
              </div>
            </label>

            <!-- ISSB Card -->
            @php $hasIssbChecked = in_array('issb', $activeTracks); @endphp
            <label id="card_track_issb" class="track-card"
                   style="display: block; cursor: pointer; border: 1.5px solid {{ $hasIssbChecked ? '#eab308' : 'rgba(255,255,255,0.08)' }}; background: {{ $hasIssbChecked ? 'rgba(234,179,8,0.09)' : 'rgba(255,255,255,0.02)' }}; border-radius: 10px; padding: 14px; transition: all 0.2s;">
              <div style="display: flex; align-items: flex-start; gap: 10px;">
                <input type="checkbox" name="category_tracks[]" value="issb" id="input_track_issb"
                       {{ $hasIssbChecked ? 'checked' : '' }}
                       onchange="onTrackCheckboxChanged()"
                       style="accent-color: #eab308; width: 18px; height: 18px; margin-top: 2px;">
                <div style="flex: 1;">
                  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                    <strong style="font-size: 13px; color: #ffffff;">ISSB Masterclass</strong>
                    <span style="font-size: 9.5px; font-weight: 700; color: #facc15; background: rgba(234,179,8,0.18); border: 1px solid rgba(234,179,8,0.35); padding: 1px 6px; border-radius: 4px;">Tri-Services Unlocked</span>
                  </div>
                  <p style="font-size: 11.5px; color: #94a3b8; margin: 0; line-height: 1.45;">
                    Full clearance to conduct <strong style="color: #facc15;">ANY exam set for ISSB</strong> across Bangladesh Army, Navy, and Air Force.
                  </p>
                </div>
              </div>
            </label>
          </div>
        </div>

        <!-- B. Police Tracks Container (Constable, SI, ASI) -->
        <div id="policeTracksSection" style="{{ $activeWing === 'Police' ? 'display: block;' : 'display: none;' }}">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 8px;">
            <div>
              <label style="display: block; font-size: 11.5px; font-weight: 800; color: #cbd5e1; text-transform: uppercase; letter-spacing: 0.6px; margin: 0;">
                Step 2: Select Police Examination Track(s):
              </label>
              <span style="font-size: 11.5px; color: #94a3b8;">
                Select <strong style="color: #c084fc;">Sub-Inspector (SI)</strong>, <strong style="color: #a855f7;">Assistant SI (ASI)</strong>, or <strong style="color: #818cf8;">Constable</strong> (combinations allowed):
              </span>
            </div>
            <!-- Quick Preset Pills -->
            <div style="display: flex; gap: 6px; flex-wrap: wrap;">
              <button type="button" onclick="setPolicePreset('si')" class="btn-tactical" style="padding: 4px 10px; font-size: 11px; background: rgba(192,132,252,0.12); color: #c084fc; border: 1px solid rgba(192,132,252,0.3); border-radius: 6px;">
                SI Only
              </button>
              <button type="button" onclick="setPolicePreset('asi')" class="btn-tactical" style="padding: 4px 10px; font-size: 11px; background: rgba(168,85,247,0.12); color: #a855f7; border: 1px solid rgba(168,85,247,0.3); border-radius: 6px;">
                ASI Only
              </button>
              <button type="button" onclick="setPolicePreset('si_asi')" class="btn-tactical" style="padding: 4px 10px; font-size: 11px; font-weight: 700; background: rgba(192,132,252,0.18); color: #c084fc; border: 1.5px solid rgba(192,132,252,0.4); border-radius: 6px;">
                SI &amp; ASI
              </button>
              <button type="button" onclick="setPolicePreset('constable')" class="btn-tactical" style="padding: 4px 10px; font-size: 11px; background: rgba(129,140,248,0.12); color: #818cf8; border: 1px solid rgba(129,140,248,0.3); border-radius: 6px;">
                Constable
              </button>
              <button type="button" onclick="setPolicePreset('all')" class="btn-tactical" style="padding: 4px 12px; font-size: 11px; font-weight: 700; background: rgba(16,185,129,0.15); color: #34d399; border: 1px solid rgba(16,185,129,0.35); border-radius: 6px;">
                All 3 Tracks
              </button>
            </div>
          </div>

          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 12px;">
            <!-- Constable Card -->
            @php $hasConstableChecked = in_array('constable', $activeTracks); @endphp
            <label id="card_track_constable" class="track-card"
                   style="display: block; cursor: pointer; border: 1.5px solid {{ $hasConstableChecked ? '#818cf8' : 'rgba(255,255,255,0.08)' }}; background: {{ $hasConstableChecked ? 'rgba(129,140,248,0.09)' : 'rgba(255,255,255,0.02)' }}; border-radius: 10px; padding: 14px; transition: all 0.2s;">
              <div style="display: flex; align-items: flex-start; gap: 10px;">
                <input type="checkbox" name="category_tracks[]" value="constable" id="input_track_constable"
                       {{ $hasConstableChecked ? 'checked' : '' }}
                       onchange="onTrackCheckboxChanged()"
                       style="accent-color: #818cf8; width: 18px; height: 18px; margin-top: 2px;">
                <div style="flex: 1;">
                  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                    <strong style="font-size: 13px; color: #ffffff;">Police Constable</strong>
                    <span style="font-size: 9.5px; font-weight: 700; color: #818cf8; background: rgba(129,140,248,0.18); border: 1px solid rgba(129,140,248,0.35); padding: 1px 6px; border-radius: 4px;">Constable Track</span>
                  </div>
                  <p style="font-size: 11.5px; color: #94a3b8; margin: 0; line-height: 1.45;">
                    Authorized for Police Constable recruitment tests, physical aptitude &amp; preliminary exams.
                  </p>
                </div>
              </div>
            </label>

            <!-- Sub-Inspector (SI) Card -->
            @php $hasSiChecked = in_array('si', $activeTracks); @endphp
            <label id="card_track_si" class="track-card"
                   style="display: block; cursor: pointer; border: 1.5px solid {{ $hasSiChecked ? '#c084fc' : 'rgba(255,255,255,0.08)' }}; background: {{ $hasSiChecked ? 'rgba(192,132,252,0.09)' : 'rgba(255,255,255,0.02)' }}; border-radius: 10px; padding: 14px; transition: all 0.2s;">
              <div style="display: flex; align-items: flex-start; gap: 10px;">
                <input type="checkbox" name="category_tracks[]" value="si" id="input_track_si"
                       {{ $hasSiChecked ? 'checked' : '' }}
                       onchange="onTrackCheckboxChanged()"
                       style="accent-color: #c084fc; width: 18px; height: 18px; margin-top: 2px;">
                <div style="flex: 1;">
                  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                    <strong style="font-size: 13px; color: #ffffff;">Sub-Inspector (SI)</strong>
                    <span style="font-size: 9.5px; font-weight: 700; color: #c084fc; background: rgba(192,132,252,0.18); border: 1px solid rgba(192,132,252,0.35); padding: 1px 6px; border-radius: 4px;">SI Track</span>
                  </div>
                  <p style="font-size: 11.5px; color: #94a3b8; margin: 0; line-height: 1.45;">
                    Authorized for Police Sub-Inspector (SI) cadet recruitment, IQ, psychometric and specialized exams.
                  </p>
                </div>
              </div>
            </label>

            <!-- Assistant Sub-Inspector (ASI) Card -->
            @php $hasAsiChecked = in_array('asi', $activeTracks); @endphp
            <label id="card_track_asi" class="track-card"
                   style="display: block; cursor: pointer; border: 1.5px solid {{ $hasAsiChecked ? '#a855f7' : 'rgba(255,255,255,0.08)' }}; background: {{ $hasAsiChecked ? 'rgba(168,85,247,0.09)' : 'rgba(255,255,255,0.02)' }}; border-radius: 10px; padding: 14px; transition: all 0.2s;">
              <div style="display: flex; align-items: flex-start; gap: 10px;">
                <input type="checkbox" name="category_tracks[]" value="asi" id="input_track_asi"
                       {{ $hasAsiChecked ? 'checked' : '' }}
                       onchange="onTrackCheckboxChanged()"
                       style="accent-color: #a855f7; width: 18px; height: 18px; margin-top: 2px;">
                <div style="flex: 1;">
                  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                    <strong style="font-size: 13px; color: #ffffff;">Assistant Sub-Inspector (ASI)</strong>
                    <span style="font-size: 9.5px; font-weight: 700; color: #a855f7; background: rgba(168,85,247,0.18); border: 1px solid rgba(168,85,247,0.35); padding: 1px 6px; border-radius: 4px;">ASI Track</span>
                  </div>
                  <p style="font-size: 11.5px; color: #94a3b8; margin: 0; line-height: 1.45;">
                    Authorized for Police Assistant Sub-Inspector (ASI) departmental, IQ, and recruitment assessments.
                  </p>
                </div>
              </div>
            </label>
          </div>
        </div>

        <!-- Live Entitlement Status Card -->
        <div id="liveEntitlementPreview" style="margin-top: 14px; padding: 12px 16px; background: rgba(255,255,255,0.03); border: 1px dashed rgba(255,255,255,0.12); border-radius: 8px; font-size: 12px; color: #cbd5e1; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
          <div style="display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-certificate" style="color: #ff5757; font-size: 15px;"></i>
            <span id="liveClearanceSummaryText">Candidate will be cleared for testing based on selected category tracks.</span>
          </div>
          <button type="button" onclick="autoSelectMatchingCourses()" class="btn-tactical" style="padding: 5px 12px; font-size: 11px; background: rgba(255,87,87,0.12); color: #ff8585; border: 1px solid rgba(255,87,87,0.3); border-radius: 6px;">
            <i class="fa-solid fa-wand-magic-sparkles"></i> Auto-Select Matching Courses Below
          </button>
        </div>

      </div>
    </div>

    <!-- Enrolled Courses -->
    <div class="content-panel" style="margin-bottom: 24px;">
      <div class="panel-header" style="margin-bottom: 14px;">
        <h3 style="font-size: 15px; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-graduation-cap" style="color: #ff5757;"></i>
          <span>Enrolled Courses &amp; Exam Clearances</span>
        </h3>
      </div>

      <div style="background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.12); border-radius: 10px; padding: 12px 16px; margin-top: 10px; margin-bottom: 14px; font-size: 12px; color: #cbd5e1; line-height: 1.5;">
        <strong style="color: #ff8585; display: flex; align-items: center; gap: 6px; margin-bottom: 4px;">
          <i class="fa-solid fa-shield-halved"></i> Automated Testing Entitlement Rules:
        </strong>
        <ul style="margin: 0; padding-left: 20px; font-size: 11.5px; color: #94a3b8; line-height: 1.6;">
          <li><strong>Preliminary Track:</strong> Branch-locked (Army course unlocks Army Prelims, Navy course unlocks Navy Prelims, Air Force unlocks AF Prelims).</li>
          <li><strong>ISSB Track:</strong> Enrolling in <span style="color: #facc15;">ANY</span> military course (Army, Navy, or Air Force) gives the cadet complete clearance for <span style="color: #facc15;">ALL ISSB examinations</span> across all branches!</li>
          <li><strong>Police Track:</strong> Clears testing in Constable, Sub-Inspector (SI), and Assistant Sub-Inspector (ASI) tracks.</li>
        </ul>
      </div>

      <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 14px; margin-top: 14px;">
        @foreach($courses as $course)
          @php
            $isEnrolled = in_array($course->id, $enrolledCourseIds);
          @endphp
          <label style="display: block; cursor: pointer; border: 1.5px solid {{ $isEnrolled ? '#ff5757' : 'rgba(255,255,255,0.08)' }}; background: {{ $isEnrolled ? 'rgba(255,87,87,0.08)' : 'rgba(255,255,255,0.02)' }}; border-radius: 12px; padding: 16px; transition: all 0.2s;"
                 id="course_card_{{ $course->id }}"
                 class="student-course-card"
                 data-branch="{{ $course->branch_key }}"
                 data-track="{{ $course->program_track }}"
                 data-course-id="{{ $course->id }}">
            <div style="display: flex; align-items: flex-start; gap: 12px;">
              <input type="checkbox" name="course_ids[]" value="{{ $course->id }}"
                     {{ $isEnrolled ? 'checked' : '' }}
                     style="accent-color: #ff5757; width: 18px; height: 18px; margin-top: 2px;"
                     onchange="toggleCourseCardStyle(this, 'course_card_{{ $course->id }}')">
              <div style="flex: 1;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px;">
                  <strong style="font-size: 13.5px; color: #ffffff; display: block; margin-bottom: 2px;">{{ $course->title }}</strong>
                </div>
                <div style="display: flex; gap: 10px; font-size: 11.5px; color: #94a3b8; margin-top: 6px;">
                  <span style="color: #ffffff; font-weight: 700;">৳{{ number_format($course->course_fee, 0) }}</span>
                  <span>&bull;</span>
                  <span>{{ $course->duration_weeks ?? 12 }} weeks</span>
                  <span>&bull;</span>
                  <span style="text-transform: capitalize; color: #cbd5e1;">{{ $course->branch_key }}</span>
                </div>
              </div>
            </div>
          </label>
        @endforeach
      </div>
    </div>

    <!-- Payment Details -->
    <div class="content-panel" id="payment_section" style="margin-bottom: 24px;">
      <div class="panel-header" style="margin-bottom: 16px;">
        <h3 style="font-size: 15px; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-file-invoice-dollar" style="color: #ff5757;"></i>
          <span>Payment Details</span>
        </h3>
      </div>

      <!-- Financial Metric Strip -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin-bottom: 20px;">
        <div style="background: #0f121a; border: 1px solid rgba(255,255,255,0.06); border-radius: 10px; padding: 12px 14px;">
          <div style="font-size: 10.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Total Billed</div>
          <div style="font-size: 18px; font-weight: 800; color: #fff; margin-top: 2px;">৳{{ number_format($totalBilled, 2) }}</div>
        </div>
        <div style="background: #0f121a; border: 1px solid rgba(255,255,255,0.06); border-radius: 10px; padding: 12px 14px;">
          <div style="font-size: 10.5px; font-weight: 700; color: #8c96a8; text-transform: uppercase; letter-spacing: 0.5px;">Total Paid</div>
          <div style="font-size: 18px; font-weight: 800; color: #ffffff; margin-top: 2px;">৳{{ number_format($totalPaid, 2) }}</div>
        </div>
        <div style="background: #0f121a; border: 1px solid {{ $totalDue > 0 ? 'rgba(239,68,68,0.25)' : 'rgba(255,255,255,0.06)' }}; border-radius: 10px; padding: 12px 14px;">
          <div style="font-size: 10.5px; font-weight: 700; color: {{ $totalDue > 0 ? '#f87171' : '#64748b' }}; text-transform: uppercase; letter-spacing: 0.5px;">Outstanding Dues</div>
          <div style="font-size: 18px; font-weight: 800; color: {{ $totalDue > 0 ? '#f87171' : '#64748b' }}; margin-top: 2px;">৳{{ number_format($totalDue, 2) }}</div>
        </div>
      </div>

      <!-- Subsection A: Course Payment Clearance -->
      <div style="margin-bottom: 22px;">
        <label style="display: block; font-size: 11.5px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
          Course Payment Clearance Status
        </label>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 10px;">
          @foreach($courses as $c)
            @php
              $pivotCourse = $student->courses->firstWhere('id', $c->id);
              $currentPayStatus = old('course_payment_status.' . $c->id, $pivotCourse ? ($pivotCourse->pivot->payment_status ?? 'paid') : 'paid');
            @endphp
            <div style="background: #0f121a; border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; padding: 10px 14px; display: flex; justify-content: space-between; align-items: center; gap: 10px;">
              <div style="min-width: 0; flex: 1;">
                <div style="font-size: 12px; font-weight: 700; color: #ffffff; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $c->title }}</div>
                <div style="font-size: 10.5px; color: #cbd5e1; font-weight: 600;">Fee: ৳{{ number_format($c->course_fee, 0) }}</div>
              </div>
              <div style="width: 130px; flex-shrink: 0;">
                <select name="course_payment_status[{{ $c->id }}]" class="form-control" style="font-size: 11.5px; padding: 6px 8px; height: auto;">
                  <option value="paid" {{ $currentPayStatus === 'paid' ? 'selected' : '' }}>Paid in Full</option>
                  <option value="partial" {{ $currentPayStatus === 'partial' ? 'selected' : '' }}>Partial Paid</option>
                  <option value="unpaid" {{ $currentPayStatus === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                  <option value="exempt" {{ $currentPayStatus === 'exempt' ? 'selected' : '' }}>Exempt</option>
                </select>
              </div>
            </div>
          @endforeach
        </div>
      </div>

      <!-- Subsection B: Issued Invoices & Status Adjustments -->
      @if($invoices->isNotEmpty())
        <div style="margin-bottom: 22px;">
          <label style="display: block; font-size: 11.5px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
            Issued Fee Invoices (Edit Dues & Status)
          </label>
          <div style="background: #0f121a; border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 12px; min-width: 580px;">
              <thead>
                <tr style="background: rgba(255,255,255,0.02); border-bottom: 1px solid rgba(255,255,255,0.06); text-align: left; color: #64748b; font-size: 10px; text-transform: uppercase;">
                  <th style="padding: 8px 12px;">Invoice #</th>
                  <th style="padding: 8px 12px;">Category</th>
                  <th style="padding: 8px 12px;">Net Total</th>
                  <th style="padding: 8px 12px;">Paid Amount</th>
                  <th style="padding: 8px 12px;">Due Balance</th>
                  <th style="padding: 8px 12px;">Invoice Status</th>
                </tr>
              </thead>
              <tbody>
                @foreach($invoices as $inv)
                  <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);">
                    <td style="padding: 8px 12px;">
                      <code style="font-family: monospace; color: #cbd5e1; font-size: 11px;">{{ $inv->invoice_number }}</code>
                    </td>
                    <td style="padding: 8px 12px; color: #fff;">
                      {{ $inv->title }}
                    </td>
                    <td style="padding: 8px 12px; font-family: monospace; color: #94a3b8;">
                      ৳{{ number_format($inv->net_amount, 2) }}
                    </td>
                    <td style="padding: 8px 12px;">
                      <input type="number" step="0.01" min="0" name="invoice_paid_amount[{{ $inv->id }}]" value="{{ old('invoice_paid_amount.' . $inv->id, $inv->paid_amount) }}"
                             class="form-control" style="font-size: 11.5px; padding: 4px 8px; height: auto; max-width: 100px; font-family: monospace;">
                    </td>
                    <td style="padding: 8px 12px;">
                      <input type="number" step="0.01" min="0" name="invoice_due_amount[{{ $inv->id }}]" value="{{ old('invoice_due_amount.' . $inv->id, $inv->due_amount) }}"
                             class="form-control" style="font-size: 11.5px; padding: 4px 8px; height: auto; max-width: 100px; font-family: monospace; color: {{ $inv->due_amount > 0 ? '#f87171' : '#cbd5e1' }};">
                    </td>
                    <td style="padding: 8px 12px;">
                      <select name="invoice_status[{{ $inv->id }}]" class="form-control" style="font-size: 11px; padding: 4px 8px; height: auto;">
                        <option value="paid" {{ old('invoice_status.' . $inv->id, $inv->status) === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="partially_paid" {{ old('invoice_status.' . $inv->id, $inv->status) === 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                        <option value="pending" {{ old('invoice_status.' . $inv->id, $inv->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="overdue" {{ old('invoice_status.' . $inv->id, $inv->status) === 'overdue' ? 'selected' : '' }}>Overdue</option>
                      </select>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      @endif

      <!-- Subsection C: Record New Payment Voucher (Optional) -->
      <div style="background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.1); border-radius: 10px; padding: 16px;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
          <strong style="font-size: 12.5px; color: #fff; display: flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-circle-plus" style="color: #ff5757;"></i> Record New Payment Voucher (Optional)
          </strong>
          <small style="color: #64748b;">Instant payment credit</small>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px;">
          <div>
            <label style="display: block; font-size: 10.5px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px;">Payment Amount (৳)</label>
            <input type="number" step="0.01" min="1" name="new_payment_amount" value="{{ old('new_payment_amount') }}"
                   class="form-control" placeholder="e.g. 5000" style="font-family: monospace;">
          </div>
          <div>
            <label style="display: block; font-size: 10.5px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px;">Payment Method</label>
            <select name="new_payment_method" class="form-control">
              <option value="Cash / Office Receipt">Cash / Office Receipt</option>
              <option value="bKash">bKash</option>
              <option value="Nagad">Nagad</option>
              <option value="Bank Deposit">Bank Deposit</option>
              <option value="Online Gateway">Online Gateway</option>
            </select>
          </div>
          <div>
            <label style="display: block; font-size: 10.5px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px;">Transaction ID / Receipt #</label>
            <input type="text" name="new_payment_trx" value="{{ old('new_payment_trx') }}"
                   class="form-control" placeholder="e.g. BKASH-TX123, REC-0098">
          </div>
          <div>
            <label style="display: block; font-size: 10.5px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px;">Verification Status</label>
            <select name="new_payment_status" class="form-control">
              <option value="approved">Approved / Verified</option>
              <option value="pending">Pending Review</option>
            </select>
          </div>
          @if($invoices->isNotEmpty())
            <div style="grid-column: span 2;">
              <label style="display: block; font-size: 10.5px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px;">Apply to Invoice (Optional)</label>
              <select name="new_payment_invoice_id" class="form-control">
                <option value="">-- Apply as General Cadet Payment --</option>
                @foreach($invoices as $inv)
                  <option value="{{ $inv->id }}">
                    Invoice {{ $inv->invoice_number }} — {{ $inv->title }} (Due: ৳{{ number_format($inv->due_amount, 2) }})
                  </option>
                @endforeach
              </select>
            </div>
          @endif
        </div>
      </div>

    </div>

    <!-- Reset Password -->
    <div class="content-panel" style="margin-bottom: 24px;">
      <div class="panel-header" style="margin-bottom: 14px;">
        <h3 style="font-size: 15px; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-key" style="color: #8c96a8;"></i>
          <span>Reset Password (Optional)</span>
        </h3>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px;">New Password</label>
          <input type="password" name="password" minlength="6" class="form-control" placeholder="Enter new password (min 6 chars)">
        </div>
        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px;">Confirm New Password</label>
          <input type="password" name="password_confirmation" minlength="6" class="form-control" placeholder="Re-enter new password">
        </div>
      </div>
    </div>

    <!-- Submit Toolbar -->
    <div style="display: flex; justify-content: flex-end; align-items: center; gap: 12px; background: #131722; border: 1px solid rgba(255,255,255,0.08); border-radius: 14px; padding: 14px 20px; margin-bottom: 30px;">
      <a href="{{ route('admin.student_accounts.index') }}" class="btn-tactical" style="background: rgba(255,255,255,0.06); color: #94a3b8; border: 1px solid rgba(255,255,255,0.1); padding: 9px 18px; font-size: 13px; text-decoration: none; border-radius: 8px;">
        Cancel
      </a>
      <button type="submit" class="btn-tactical" style="background: #ff5757; color: #ffffff; padding: 9px 24px; font-size: 13px; font-weight: 700; border: none; border-radius: 8px; cursor: pointer;">
        <i class="fa-solid fa-check"></i> Save Changes
      </button>
    </div>

  </form>

  <!-- Danger Zone -->
  <div class="content-panel" style="border: 1px solid rgba(239, 68, 68, 0.3); background: rgba(239, 68, 68, 0.02); margin-bottom: 40px;">
    <div class="panel-header" style="border-bottom: 1px solid rgba(239, 68, 68, 0.15); margin-bottom: 16px;">
      <h3 style="font-size: 15px; font-weight: 800; color: #ef4444; margin: 0; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-triangle-exclamation" style="color: #ef4444;"></i>
        <span>Delete Account</span>
      </h3>
    </div>

    <p style="font-size: 12px; color: #94a3b8; margin: 0 0 16px; line-height: 1.5;">
      Once deleted, this cadet account, enrollment records, and exam scores cannot be recovered.
    </p>

    <form method="POST" action="{{ route('admin.student_accounts.destroy', $student->id) }}" id="dangerDeleteForm">
      @csrf
      @method('DELETE')

      <div style="max-width: 520px; margin-bottom: 16px;">
        <label style="display: block; font-size: 11px; font-weight: 700; color: #f87171; text-transform: uppercase; margin-bottom: 6px;">
          Type <code style="color: #ffffff; background: rgba(239, 68, 68, 0.25); padding: 2px 6px; border-radius: 4px;">{{ $currentId }}</code> or <code style="color: #ffffff; background: rgba(239, 68, 68, 0.25); padding: 2px 6px; border-radius: 4px;">DELETE</code> to confirm:
        </label>
        <input type="text" name="confirm_delete" id="dangerConfirmInput" required autocomplete="off"
               class="form-control" style="font-family: monospace; font-size: 13.5px; font-weight: 700; color: #ff8585; border-color: rgba(239,68,68,0.35); text-transform: uppercase;"
               placeholder="Type confirmation here..."
               oninput="checkDangerDelete()">
      </div>

      <label style="display: flex; align-items: center; gap: 8px; font-size: 12px; color: #cbd5e1; cursor: pointer; margin-bottom: 18px;">
        <input type="checkbox" id="dangerAckCheckbox" style="accent-color: #ef4444; width: 16px; height: 16px;" onchange="checkDangerDelete()">
        <span>I understand this deletion is permanent.</span>
      </label>

      <button type="submit" id="dangerDeleteBtn" disabled class="btn-tactical"
              style="background: #ef4444; color: #ffffff; border: none; padding: 10px 22px; font-size: 13px; font-weight: 700; opacity: 0.35; cursor: not-allowed; transition: all 0.2s;">
        <i class="fa-solid fa-trash-can"></i> Delete Cadet Account
      </button>
    </form>
  </div>

</div>

<script>
  function toggleCourseCardStyle(checkbox, cardId) {
    var card = document.getElementById(cardId);
    if (card) {
      if (checkbox.checked) {
        card.style.borderColor = '#ff5757';
        card.style.background = 'rgba(255, 87, 87, 0.08)';
      } else {
        card.style.borderColor = 'rgba(255,255,255,0.08)';
        card.style.background = 'rgba(255,255,255,0.02)';
      }
    }
  }

  // Dynamic Service Wing & Category Track Handling
  function onBranchWingChanged(selectedWing) {
    // 1. Highlight active wing card
    var wingCards = document.querySelectorAll('.wing-radio-card');
    wingCards.forEach(function(card) {
      var radio = card.querySelector('input[type="radio"]');
      if (radio && radio.value === selectedWing) {
        card.style.borderColor = '#ff5757';
        card.style.background = 'rgba(255, 87, 87, 0.1)';
        var strong = card.querySelector('strong');
        if (strong) strong.style.color = '#ffffff';
      } else {
        card.style.borderColor = 'rgba(255, 255, 255, 0.08)';
        card.style.background = 'rgba(255, 255, 255, 0.02)';
        var strong = card.querySelector('strong');
        if (strong) strong.style.color = '#cbd5e1';
      }
    });

    // 2. Toggle Military vs Police sections
    var milSec = document.getElementById('militaryTracksSection');
    var polSec = document.getElementById('policeTracksSection');
    var prelimText = document.getElementById('prelimBranchText');

    if (selectedWing === 'Police') {
      if (milSec) milSec.style.display = 'none';
      if (polSec) polSec.style.display = 'block';
    } else {
      if (milSec) milSec.style.display = 'block';
      if (polSec) polSec.style.display = 'none';
      if (prelimText) {
        var wingNameMap = {
          'Army': 'Bangladesh Army',
          'Navy': 'Bangladesh Navy',
          'Air Force': 'Bangladesh Air Force',
          'General': 'Tri-Services / General'
        };
        prelimText.textContent = wingNameMap[selectedWing] || selectedWing;
      }
    }

    onTrackCheckboxChanged();
  }

  function onTrackCheckboxChanged() {
    var trackMap = {
      'prelim': { id: 'card_track_prelim', border: '#3b82f6', bg: 'rgba(59, 130, 246, 0.09)' },
      'issb': { id: 'card_track_issb', border: '#eab308', bg: 'rgba(234, 179, 8, 0.09)' },
      'constable': { id: 'card_track_constable', border: '#818cf8', bg: 'rgba(129, 140, 248, 0.09)' },
      'si': { id: 'card_track_si', border: '#c084fc', bg: 'rgba(192, 132, 252, 0.09)' },
      'asi': { id: 'card_track_asi', border: '#a855f7', bg: 'rgba(168, 85, 247, 0.09)' }
    };

    for (var key in trackMap) {
      var item = trackMap[key];
      var input = document.getElementById('input_track_' + key);
      var card = document.getElementById(item.id);
      if (input && card) {
        if (input.checked) {
          card.style.borderColor = item.border;
          card.style.background = item.bg;
        } else {
          card.style.borderColor = 'rgba(255, 255, 255, 0.08)';
          card.style.background = 'rgba(255, 255, 255, 0.02)';
        }
      }
    }

    updateComposedTargetWingAndSummary();
  }

  function setMilitaryPreset(preset) {
    var prelim = document.getElementById('input_track_prelim');
    var issb = document.getElementById('input_track_issb');
    if (!prelim || !issb) return;

    if (preset === 'prelim') {
      prelim.checked = true;
      issb.checked = false;
    } else if (preset === 'issb') {
      prelim.checked = false;
      issb.checked = true;
    } else if (preset === 'both') {
      prelim.checked = true;
      issb.checked = true;
    }

    onTrackCheckboxChanged();
  }

  function setPolicePreset(preset) {
    var c = document.getElementById('input_track_constable');
    var si = document.getElementById('input_track_si');
    var asi = document.getElementById('input_track_asi');
    if (!c || !si || !asi) return;

    if (preset === 'si') {
      si.checked = true;
      asi.checked = false;
      c.checked = false;
    } else if (preset === 'asi') {
      si.checked = false;
      asi.checked = true;
      c.checked = false;
    } else if (preset === 'si_asi') {
      si.checked = true;
      asi.checked = true;
      c.checked = false;
    } else if (preset === 'constable') {
      si.checked = false;
      asi.checked = false;
      c.checked = true;
    } else if (preset === 'all') {
      si.checked = true;
      asi.checked = true;
      c.checked = true;
    }

    onTrackCheckboxChanged();
  }

  function updateComposedTargetWingAndSummary() {
    var selectedRadio = document.querySelector('input[name="branch_wing"]:checked');
    var wing = selectedRadio ? selectedRadio.value : 'Army';
    var summaryEl = document.getElementById('liveClearanceSummaryText');
    var hiddenWing = document.getElementById('composedTargetWingHidden');

    var composedTitle = wing;

    if (wing === 'Police') {
      var c = document.getElementById('input_track_constable') ? document.getElementById('input_track_constable').checked : false;
      var si = document.getElementById('input_track_si') ? document.getElementById('input_track_si').checked : false;
      var asi = document.getElementById('input_track_asi') ? document.getElementById('input_track_asi').checked : false;

      var parts = [];
      if (c) parts.push('Constable');
      if (si) parts.push('Sub-Inspector (SI)');
      if (asi) parts.push('Assistant Sub-Inspector (ASI)');

      if (parts.length > 0) {
        composedTitle = 'Police - ' + parts.join(', ');
        if (summaryEl) {
          summaryEl.innerHTML = '<span style="color: #34d399; font-weight: 700;"><i class="fa-solid fa-check"></i> Clearance:</span> Cadet is authorized for <strong>' + parts.join(', ') + '</strong> police examination tests.';
        }
      } else {
        composedTitle = 'Police';
        if (summaryEl) {
          summaryEl.innerHTML = '<span style="color: #facc15; font-weight: 700;"><i class="fa-solid fa-triangle-exclamation"></i> Notice:</span> No police track selected yet. Please select Constable, SI, ASI or combinations.';
        }
      }
    } else {
      var prelim = document.getElementById('input_track_prelim') ? document.getElementById('input_track_prelim').checked : false;
      var issb = document.getElementById('input_track_issb') ? document.getElementById('input_track_issb').checked : false;

      if (prelim && issb) {
        composedTitle = wing + ' - Prelim & ISSB';
        if (summaryEl) {
          summaryEl.innerHTML = '<span style="color: #34d399; font-weight: 700;"><i class="fa-solid fa-check-double"></i> Full Clearance:</span> Authorized for <strong style="color: #60a5fa;">' + wing + ' Preliminary Exams</strong> + <strong style="color: #facc15;">Tri-Services ISSB Exams</strong> across Army, Navy & Air Force!';
        }
      } else if (prelim) {
        composedTitle = wing + ' - Preliminary';
        if (summaryEl) {
          summaryEl.innerHTML = '<span style="color: #60a5fa; font-weight: 700;"><i class="fa-solid fa-file-pen"></i> Branch Clearance:</span> Authorized for <strong style="color: #60a5fa;">' + wing + ' Preliminary Exams</strong> only.';
        }
      } else if (issb) {
        composedTitle = wing + ' - ISSB';
        if (summaryEl) {
          summaryEl.innerHTML = '<span style="color: #facc15; font-weight: 700;"><i class="fa-solid fa-star"></i> Tri-Services Clearance:</span> Authorized for <strong style="color: #facc15;">All ISSB Exams</strong> across Army, Navy & Air Force!';
        }
      } else {
        composedTitle = wing;
        if (summaryEl) {
          summaryEl.innerHTML = '<span style="color: #facc15; font-weight: 700;"><i class="fa-solid fa-triangle-exclamation"></i> Notice:</span> No category track selected yet. Please select Preliminary, ISSB, or both.';
        }
      }
    }

    if (hiddenWing) {
      hiddenWing.value = composedTitle;
    }
  }

  function autoSelectMatchingCourses() {
    var selectedRadio = document.querySelector('input[name="branch_wing"]:checked');
    var wing = selectedRadio ? selectedRadio.value.toLowerCase() : '';
    
    var isPolice = wing === 'police';
    var prelimChecked = document.getElementById('input_track_prelim') ? document.getElementById('input_track_prelim').checked : false;
    var issbChecked = document.getElementById('input_track_issb') ? document.getElementById('input_track_issb').checked : false;
    var cChecked = document.getElementById('input_track_constable') ? document.getElementById('input_track_constable').checked : false;
    var siChecked = document.getElementById('input_track_si') ? document.getElementById('input_track_si').checked : false;
    var asiChecked = document.getElementById('input_track_asi') ? document.getElementById('input_track_asi').checked : false;

    var courseCards = document.querySelectorAll('.student-course-card');
    var matchedCount = 0;

    courseCards.forEach(function(card) {
      var branch = (card.getAttribute('data-branch') || '').toLowerCase();
      var track = (card.getAttribute('data-track') || '').toLowerCase();
      var text = (card.innerText || '').toLowerCase();
      var checkbox = card.querySelector('input[type="checkbox"]');
      if (!checkbox) return;

      var shouldSelect = false;

      if (isPolice) {
        if (branch.includes('police') || text.includes('police')) {
          if (cChecked && (text.includes('constable') || track.includes('constable'))) {
            shouldSelect = true;
          }
          if (siChecked && (text.includes('si') || text.includes('sub-inspector')) && !text.includes('assistant')) {
            shouldSelect = true;
          }
          if (asiChecked && (text.includes('asi') || text.includes('assistant sub-inspector') || text.includes('assistant'))) {
            shouldSelect = true;
          }
          if (!cChecked && !siChecked && !asiChecked) {
            shouldSelect = true;
          }
        }
      } else {
        var branchMatch = false;
        if (wing === 'navy' && (branch.includes('navy') || text.includes('navy'))) branchMatch = true;
        else if (wing === 'army' && (branch.includes('army') || text.includes('army'))) branchMatch = true;
        else if ((wing === 'air force' || wing === 'airforce' || wing === 'air_force') && (branch.includes('air') || text.includes('air') || text.includes('bafa'))) branchMatch = true;
        else if (wing === 'general') branchMatch = true;

        if (branchMatch && prelimChecked) {
          if (text.includes('prelim') || track.includes('prelim') || !text.includes('issb')) {
            shouldSelect = true;
          }
        }

        if (issbChecked && (text.includes('issb') || track.includes('issb'))) {
          shouldSelect = true;
        }
      }

      if (shouldSelect) {
        checkbox.checked = true;
        matchedCount++;
      }
      toggleCourseCardStyle(checkbox, card.id);
    });

    var summaryEl = document.getElementById('liveClearanceSummaryText');
    if (summaryEl) {
      summaryEl.innerHTML = '<span style="color: #ff5757; font-weight: 700;"><i class="fa-solid fa-wand-magic-sparkles"></i> Auto-selected ' + matchedCount + ' matching course(s) below!</span>';
      setTimeout(function() {
        updateComposedTargetWingAndSummary();
      }, 2500);
    }
  }

  document.addEventListener('DOMContentLoaded', function() {
    var selectedRadio = document.querySelector('input[name="branch_wing"]:checked');
    if (selectedRadio) {
      onBranchWingChanged(selectedRadio.value);
    } else {
      updateComposedTargetWingAndSummary();
    }
  });

  function checkDangerDelete() {
    var expected = "{{ strtoupper(trim($currentId)) }}";
    var typed = (document.getElementById('dangerConfirmInput').value || '').trim().toUpperCase();
    var ack = document.getElementById('dangerAckCheckbox').checked;
    var btn = document.getElementById('dangerDeleteBtn');

    var isMatch = (typed === expected && typed.length > 0) || typed === 'DELETE';

    if (isMatch && ack) {
      btn.disabled = false;
      btn.style.opacity = '1';
      btn.style.cursor = 'pointer';
      btn.style.background = 'linear-gradient(135deg, #ef4444, #b91c1c)';
      btn.style.boxShadow = '0 4px 14px rgba(239, 68, 68, 0.4)';
    } else {
      btn.disabled = true;
      btn.style.opacity = '0.35';
      btn.style.cursor = 'not-allowed';
      btn.style.boxShadow = 'none';
    }
  }
</script>
@endsection