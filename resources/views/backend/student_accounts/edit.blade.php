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
    <input type="hidden" name="submit_action" id="form_submit_action" value="save_update">

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

      <div style="text-align: right; margin-top: 18px; border-top: 1px solid rgba(255,255,255,0.06); padding-top: 14px;">
        <button type="submit" name="submit_action" value="save_update" onclick="submitAjaxSaveUpdate(event, this)" class="btn-tactical btn-tactical-primary" style="font-size: 13px; font-weight: 700; padding: 8px 18px; cursor: pointer;">
          <i class="fa-solid fa-save" style="margin-right: 6px;"></i> Save Update
        </button>
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
        <div style="text-align: right; margin-top: 20px; border-top: 1px solid rgba(255,255,255,0.06); padding-top: 16px;" class="injected-save-btn">
            <button type="submit" name="submit_action" value="save_update" onclick="submitAjaxSaveUpdate(event, this)" class="btn-tactical btn-tactical-primary" style="font-size: 13px; font-weight: 700; padding: 8px 16px; cursor: pointer;">
                <i class="fa-solid fa-save" style="margin-right: 6px;"></i> Save Update
            </button>
        </div>

      </div>
    <!-- Service Wing & Examination Category Allocation Panel -->
    <div class="content-panel" style="margin-bottom: 24px;">
      <div class="panel-header" style="margin-bottom: 18px;">
        <h3 style="font-size: 15px; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-layer-group" style="color: #ff5757;"></i>
          <span>Cadet Category</span>
        </h3>
      </div>

      <!-- Step 1: Select Service Course / Branch -->
      <div style="margin-bottom: 16px;">
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
          @foreach([
            'Army' => ['label' => 'Bangladesh Army', 'icon' => 'fa-person-military-rifle', 'color' => '#f97316'],
            'Navy' => ['label' => 'Bangladesh Navy', 'icon' => 'fa-anchor', 'color' => '#38bdf8'],
            'Air Force' => ['label' => 'Bangladesh Air Force', 'icon' => 'fa-jet-fighter', 'color' => '#60a5fa'],
            'Police' => ['label' => 'Bangladesh Police', 'icon' => 'fa-shield-halved', 'color' => '#c084fc'],
            'General' => ['label' => 'General / Tri-Service', 'icon' => 'fa-globe', 'color' => '#34d399']
          ] as $wingKey => $wingMeta)
            @php $isSelectedWing = ($activeWing === $wingKey); @endphp
            <label class="wing-radio-card" id="wing_card_{{ Str::slug($wingKey) }}"
                   style="display: flex; align-items: center; gap: 8px; padding: 10px 14px; border-radius: 8px; cursor: pointer; transition: all 0.2s; border: 1.5px solid {{ $isSelectedWing ? '#ff5757' : 'rgba(255,255,255,0.08)' }}; background: {{ $isSelectedWing ? 'rgba(255,87,87,0.1)' : 'rgba(255,255,255,0.02)' }};">
              <input type="radio" name="branch_wing" value="{{ $wingKey }}"
                     {{ $isSelectedWing ? 'checked' : '' }}
                     onchange="onBranchWingChanged('{{ $wingKey }}')"
                     style="accent-color: #ff5757; width: 14px; height: 14px; margin: 0;">
              <div>
                <strong style="display: block; font-size: 12px; color: {{ $isSelectedWing ? '#ffffff' : '#cbd5e1' }};">
                  <i class="fa-solid {{ $wingMeta['icon'] }}" style="color: {{ $wingMeta['color'] }}; margin-right: 4px;"></i> {{ $wingMeta['label'] }}
                </strong>
              </div>
            </label>
          @endforeach
        </div>
        <input type="hidden" name="target_wing" id="composedTargetWingHidden" value="{{ $student->target_wing }}">
      </div>

            <!-- Dynamic Category / Track Place Selection -->
      <div id="categoryTracksContainer" style="padding-top: 18px; border-top: 1px solid rgba(255,255,255,0.06);">
        
        <!-- A. Military Tracks Container (Army, Navy, Air Force, General) -->
        <div id="militaryTracksSection" style="{{ $activeWing === 'Police' ? 'display: none;' : 'display: block;' }}">
          
          <label style="display: block; font-size: 11.5px; font-weight: 800; color: #cbd5e1; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 12px;">
            Select Cadet Rank Level
          </label>

          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; margin-bottom: 18px;">
             @php
                $isSoldierChecked = in_array('soldier', $activeTracks);
                $isPrelimChecked = in_array('prelim', $activeTracks);
                $isIssbChecked = in_array('issb', $activeTracks);
                $isOfficer = $isPrelimChecked || $isIssbChecked;
             @endphp

             <!-- Soldier Rank Card -->
             <label style="display: block; cursor: pointer; border: 1.5px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.02); border-radius: 12px; padding: 14px; transition: all 0.2s;" id="ui_rank_soldier">
               <div style="display: flex; align-items: flex-start; gap: 12px;">
                 <input type="radio" name="military_rank_level" value="soldier" onchange="toggleMilitaryRank()" style="accent-color: #10b981; width: 18px; height: 18px; margin-top: 2px;" {{ $isSoldierChecked ? 'checked' : '' }}>
                 <div>
                   <strong style="display: block; font-size: 13.5px; color: #ffffff; margin-bottom: 2px;">
                     <i class="fa-solid fa-person-rifle" style="color: #10b981; margin-right: 4px;"></i> Soldier / Sainik
                   </strong>
                   <span style="font-size: 11.5px; color: #94a3b8;">Non-Commissioned Track</span>
                 </div>
               </div>
             </label>

             <!-- Officer Rank Card -->
             <label style="display: block; cursor: pointer; border: 1.5px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.02); border-radius: 12px; padding: 14px; transition: all 0.2s;" id="ui_rank_officer">
               <div style="display: flex; align-items: flex-start; gap: 12px;">
                 <input type="radio" name="military_rank_level" value="officer" onchange="toggleMilitaryRank()" style="accent-color: #60a5fa; width: 18px; height: 18px; margin-top: 2px;" {{ $isOfficer ? 'checked' : '' }}>
                 <div>
                   <strong style="display: block; font-size: 13.5px; color: #ffffff; margin-bottom: 2px;">
                     <i class="fa-solid fa-star" style="color: #60a5fa; margin-right: 4px;"></i> Officer Cadet
                   </strong>
                   <span style="font-size: 11.5px; color: #94a3b8;">Commissioned Track</span>
                 </div>
               </div>
             </label>
          </div>

          <div style="display: none;">
            <input type="checkbox" name="category_tracks[]" value="soldier" id="input_track_soldier" {{ $isSoldierChecked ? 'checked' : '' }}>
          </div>

          <!-- Officer Sub-Tracks (Prelim vs ISSB) -->
          <div id="officerTracksSection" style="display: {{ $isOfficer ? 'block' : 'none' }}; margin-bottom: 20px; border-left: 2px solid #60a5fa; padding-left: 16px; margin-left: 8px;">
            <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 12px;">
              Officer Examinations &amp; Clearances
            </label>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 12px;">
               <!-- Prelim Checkbox Card -->
               <label style="display: block; cursor: pointer; border: 1.5px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.02); border-radius: 10px; padding: 12px 14px; transition: all 0.2s;" id="ui_track_prelim">
                 <div style="display: flex; align-items: flex-start; gap: 12px;">
                   <input type="checkbox" name="category_tracks[]" value="prelim" id="input_track_prelim" {{ $isPrelimChecked ? 'checked' : '' }} style="accent-color: #60a5fa; width: 16px; height: 16px; margin-top: 2px;" onchange="onOfficerTrackChanged()">
                   <div style="flex: 1;">
                     <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3px;">
                       <strong style="font-size: 13px; color: #ffffff;">Preliminary</strong>
                       <span style="font-size: 9.5px; font-weight: 700; color: #60a5fa; background: rgba(59,130,246,0.15); border: 1px solid rgba(59,130,246,0.3); padding: 1px 6px; border-radius: 4px;">Branch Exclusive</span>
                     </div>
                     <span style="font-size: 11px; color: #94a3b8;">Written &amp; IQ for specific branch</span>
                   </div>
                 </div>
               </label>

               <!-- ISSB Checkbox Card -->
               <label style="display: block; cursor: pointer; border: 1.5px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.02); border-radius: 10px; padding: 12px 14px; transition: all 0.2s;" id="ui_track_issb">
                 <div style="display: flex; align-items: flex-start; gap: 12px;">
                   <input type="checkbox" name="category_tracks[]" value="issb" id="input_track_issb" {{ $isIssbChecked ? 'checked' : '' }} style="accent-color: #facc15; width: 16px; height: 16px; margin-top: 2px;" onchange="onOfficerTrackChanged()">
                   <div style="flex: 1;">
                     <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3px;">
                       <strong style="font-size: 13px; color: #ffffff;">ISSB Masterclass</strong>
                       <span style="font-size: 9.5px; font-weight: 700; color: #facc15; background: rgba(234,179,8,0.15); border: 1px solid rgba(234,179,8,0.3); padding: 1px 6px; border-radius: 4px;">Universal Tri-Service</span>
                     </div>
                     <span style="font-size: 11px; color: #94a3b8;">One clearance unlocks all 3 forces</span>
                   </div>
                 </div>
               </label>
            </div>
          </div>
        </div>

        <!-- B. Police Tracks Container (Constable, SI, ASI) -->
        <div id="policeTracksSection" style="{{ $activeWing === 'Police' ? 'display: block;' : 'display: none;' }}">
          <label style="display: block; font-size: 11.5px; font-weight: 800; color: #cbd5e1; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 12px;">
            Select Police Track Level(s)
          </label>
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; margin-bottom: 16px;">
             @php
                $hasConstableChecked = in_array('constable', $activeTracks);
                $hasSiChecked = in_array('si', $activeTracks);
                $hasAsiChecked = in_array('asi', $activeTracks);
             @endphp
             <label style="display: block; cursor: pointer; border: 1.5px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.02); border-radius: 10px; padding: 12px 14px; transition: all 0.2s;" id="ui_track_constable">
               <div style="display: flex; align-items: center; gap: 10px;">
                 <input type="checkbox" name="category_tracks[]" value="constable" id="input_track_constable" {{ $hasConstableChecked ? 'checked' : '' }} style="accent-color: #818cf8; width: 16px; height: 16px;" onchange="onPoliceTrackChanged()">
                 <strong style="font-size: 13px; color: #ffffff;">Constable</strong>
               </div>
             </label>

             <label style="display: block; cursor: pointer; border: 1.5px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.02); border-radius: 10px; padding: 12px 14px; transition: all 0.2s;" id="ui_track_si">
               <div style="display: flex; align-items: center; gap: 10px;">
                 <input type="checkbox" name="category_tracks[]" value="si" id="input_track_si" {{ $hasSiChecked ? 'checked' : '' }} style="accent-color: #c084fc; width: 16px; height: 16px;" onchange="onPoliceTrackChanged()">
                 <strong style="font-size: 13px; color: #ffffff;">Sub-Inspector (SI)</strong>
               </div>
             </label>

             <label style="display: block; cursor: pointer; border: 1.5px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.02); border-radius: 10px; padding: 12px 14px; transition: all 0.2s;" id="ui_track_asi">
               <div style="display: flex; align-items: center; gap: 10px;">
                 <input type="checkbox" name="category_tracks[]" value="asi" id="input_track_asi" {{ $hasAsiChecked ? 'checked' : '' }} style="accent-color: #a855f7; width: 16px; height: 16px;" onchange="onPoliceTrackChanged()">
                 <strong style="font-size: 13px; color: #ffffff;">Assistant SI (ASI)</strong>
               </div>
             </label>
          </div>
        </div>

      </div>

    

        <div style="text-align: right; margin-top: 20px; border-top: 1px solid rgba(255,255,255,0.06); padding-top: 16px;" class="injected-save-btn">
            <button type="submit" name="submit_action" value="save_update" onclick="submitAjaxSaveUpdate(event, this)" class="btn-tactical btn-tactical-primary" style="font-size: 13px; font-weight: 700; padding: 8px 16px; cursor: pointer;">
                <i class="fa-solid fa-save" style="margin-right: 6px;"></i> Save Update
            </button>
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

      <!-- Branch Filter Dropdown -->
      <div style="margin-bottom: 16px; border-bottom: 1px solid rgba(255,255,255,0.06); padding-bottom: 16px;">
        <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 8px;">Filter by Branch</label>
        <select id="courseFilterDropdown" onchange="filterCourses(this.value)" class="form-control" style="background-color: #0f121a; border: 1px solid rgba(255,255,255,0.1); color: #ffffff; padding: 10px 14px; border-radius: 8px; font-size: 13.5px; font-weight: 600; width: 100%; max-width: 350px; cursor: pointer; appearance: auto;">
          <option value="none" selected>Select Branch...</option>
          <option value="all">🌐 Show All Courses</option>
          <option value="Army">🪖 Bangladesh Army</option>
          <option value="Navy">⚓ Bangladesh Navy</option>
          <option value="Air Force">✈️ Bangladesh Air Force</option>
          <option value="Police">🛡️ Bangladesh Police</option>
        </select>
      </div>

      <!-- Compact Courses List -->
      <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 10px;" id="coursesGridList">
        @foreach($courses as $course)
          @php
            $isEnrolled = in_array($course->id, $enrolledCourseIds);
            
            // Map category to a cleaner branch string if branch_key isn't strictly set
            $catLower = strtolower($course->category ?? '');
            $titleLower = strtolower($course->title ?? '');
            $filterTag = 'General';
            if (str_contains($catLower, 'army') || str_contains($titleLower, 'army')) $filterTag = 'Army';
            elseif (str_contains($catLower, 'navy') || str_contains($titleLower, 'navy')) $filterTag = 'Navy';
            elseif (str_contains($catLower, 'air') || str_contains($titleLower, 'air') || str_contains($titleLower, 'bafa')) $filterTag = 'Air Force';
            elseif (str_contains($catLower, 'police') || str_contains($titleLower, 'police')) $filterTag = 'Police';
          @endphp
          <label class="course-item" data-branch="{{ $filterTag }}"
                 id="course_card_{{ $course->id }}"
                 style="display: flex; align-items: center; gap: 12px; padding: 10px 14px; border: 1.5px solid {{ $isEnrolled ? '#ff5757' : 'rgba(255,255,255,0.08)' }}; background: {{ $isEnrolled ? 'rgba(255,87,87,0.08)' : 'rgba(255,255,255,0.02)' }}; border-radius: 8px; cursor: pointer; transition: all 0.2s;">
            <input type="checkbox" name="course_ids[]" value="{{ $course->id }}"
                   {{ $isEnrolled ? 'checked' : '' }}
                   style="accent-color: #ff5757; width: 16px; height: 16px; margin: 0;"
                   onchange="toggleCourseCardStyleCompact(this, 'course_card_{{ $course->id }}')">
            <div style="flex: 1; min-width: 0;">
              <strong style="display: block; font-size: 13px; color: #ffffff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 2px;" title="{{ $course->title }}">{{ $course->title }}</strong>
              <span style="font-size: 10.5px; color: #cbd5e1; font-family: monospace; font-weight: 700; background: rgba(255,255,255,0.06); padding: 2px 6px; border-radius: 4px; display: inline-block;">
                <i class="fa-solid fa-barcode" style="color: #94a3b8; margin-right: 3px;"></i> {{ $course->course_code ?? 'CODE-TBA' }}
              </span>
            </div>
          </label>
        @endforeach
      </div>
    

        <div style="text-align: right; margin-top: 20px; border-top: 1px solid rgba(255,255,255,0.06); padding-top: 16px;" class="injected-save-btn">
            <button type="submit" name="submit_action" value="save_update" onclick="submitAjaxSaveUpdate(event, this)" class="btn-tactical btn-tactical-primary" style="font-size: 13px; font-weight: 700; padding: 8px 18px; cursor: pointer;">
                <i class="fa-solid fa-save" style="margin-right: 6px;"></i> Save Update
            </button>
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

      @if($student->courses->count() > 0)
          <!-- Dropdown Menu for Enrolled Courses -->
          <div style="margin-bottom: 24px;">
              <select id="enrolled_course_payment_selector" class="form-control" onchange="switchPaymentCourse(this.value)" style="background-color: #0f121a; border: 1px solid rgba(255,255,255,0.1); color: #ffffff; padding: 12px 14px; border-radius: 8px; font-size: 14px; font-weight: 700; width: 100%; max-width: 450px; cursor: pointer; appearance: auto;">
                  <option value="none" selected disabled>-- Choose Enrolled Course --</option>
                  @foreach($student->courses as $c)
                      <option value="pay_card_{{ $c->id }}">{{ $c->title }} ({{ $c->course_code ?? 'TBA' }})</option>
                  @endforeach
              </select>
          </div>

          <!-- Course Details Cards Container -->
          <div id="payment_cards_container">
              @foreach($student->courses as $c)
                  @php
                      $currentPayStatus = old('course_payment_status.' . $c->id, $c->pivot->payment_status ?? 'paid');
                      $coursePrice = $c->fee ?? 0;
                      $paidAmount = old('course_paid_amount.' . $c->id, $c->pivot->paid_amount ?? 0);
                      $dueAmount = old('course_due_amount.' . $c->id, $c->pivot->due_amount ?? 0);
                  @endphp
                  <div id="pay_card_{{ $c->id }}" class="payment-course-card" style="display: none; border: 1px solid rgba(255,255,255,0.08); padding: 24px; border-radius: 12px; background: rgba(255,255,255,0.02);">
                      
                      <!-- Display Fields (Read-only overview) -->
                      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 16px; margin-bottom: 24px;">
                          <div style="background: #0f121a; padding: 16px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.05); box-shadow: 0 4px 6px rgba(0,0,0,0.2);">
                              <div style="font-size: 11px; color: #94a3b8; text-transform: uppercase; font-weight: 700; margin-bottom: 6px; letter-spacing: 0.5px;">Course Price</div>
                              <div style="font-size: 22px; color: #ffffff; font-weight: 800;">৳{{ number_format($coursePrice, 0) }}</div>
                          </div>
                          <div style="background: #0f121a; padding: 16px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.05); box-shadow: 0 4px 6px rgba(0,0,0,0.2);">
                              <div style="font-size: 11px; color: #94a3b8; text-transform: uppercase; font-weight: 700; margin-bottom: 6px; letter-spacing: 0.5px;">Paid Amount</div>
                              <div style="font-size: 22px; color: #10b981; font-weight: 800;">৳{{ number_format($paidAmount, 0) }}</div>
                          </div>
                          <div style="background: #0f121a; padding: 16px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.05); box-shadow: 0 4px 6px rgba(0,0,0,0.2);">
                              <div style="font-size: 11px; color: #94a3b8; text-transform: uppercase; font-weight: 700; margin-bottom: 6px; letter-spacing: 0.5px;">Payable / Due</div>
                              <div style="font-size: 22px; color: #ef4444; font-weight: 800;">৳{{ number_format($dueAmount, 0) }}</div>
                          </div>
                      </div>
                      
                      <!-- Editable Section -->
                      <div style="border-top: 1px dashed rgba(255,255,255,0.1); padding-top: 20px;">
                          <label style="display: block; font-size: 13.5px; font-weight: 800; color: #ffffff; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                              <i class="fa-solid fa-pen-to-square" style="color: #60a5fa;"></i> Edit Payment Today
                          </label>
                          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                              <div>
                                  <label style="font-size: 11px; font-weight: 700; color: #94a3b8; margin-bottom: 8px; display: block; text-transform: uppercase;">Update Paid Amount (৳)</label>
                                  <input type="number" step="0.01" min="0" name="course_paid_amount[{{ $c->id }}]" value="{{ $paidAmount }}" class="form-control" style="font-family: monospace; font-size: 15px; font-weight: 700; background: #0f121a; border-color: rgba(255,255,255,0.1); padding: 12px;">
                              </div>
                              <div>
                                  <label style="font-size: 11px; font-weight: 700; color: #94a3b8; margin-bottom: 8px; display: block; text-transform: uppercase;">Update Due Amount (৳)</label>
                                  <input type="number" step="0.01" min="0" name="course_due_amount[{{ $c->id }}]" value="{{ $dueAmount }}" class="form-control" style="font-family: monospace; font-size: 15px; font-weight: 700; color: #f87171; background: #0f121a; border-color: rgba(255,255,255,0.1); padding: 12px;">
                              </div>
                              <div>
                                  <label style="font-size: 11px; font-weight: 700; color: #94a3b8; margin-bottom: 8px; display: block; text-transform: uppercase;">Clearance Status</label>
                                  <select name="course_payment_status[{{ $c->id }}]" class="form-control" style="font-size: 14px; font-weight: 700; background: #0f121a; border-color: rgba(255,255,255,0.1); padding: 12px; appearance: auto;">
                                      <option value="paid" {{ $currentPayStatus === 'paid' ? 'selected' : '' }}>Paid in Full</option>
                                      <option value="partial" {{ $currentPayStatus === 'partial' ? 'selected' : '' }}>Partial Paid</option>
                                      <option value="unpaid" {{ $currentPayStatus === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                      <option value="exempt" {{ $currentPayStatus === 'exempt' ? 'selected' : '' }}>Exempt</option>
                                  </select>
                              </div>
                          </div>
                      </div>
                      
                  </div>
              @endforeach
          </div>
      @else
          <!-- Empty State -->
          <div style="background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.1); border-radius: 12px; padding: 40px; text-align: center;">
              <i class="fa-solid fa-box-open" style="font-size: 32px; color: #64748b; margin-bottom: 16px;"></i>
              <div style="font-size: 15px; color: #cbd5e1; font-weight: 700;">No Enrolled Courses Saved Yet</div>
              <div style="font-size: 13px; color: #64748b; margin-top: 8px;">Select courses from the section above and click Save. Payment details will appear here.</div>
          </div>
      @endif
    
        <div style="text-align: right; margin-top: 20px; border-top: 1px solid rgba(255,255,255,0.06); padding-top: 16px;" class="injected-save-btn">
            <button type="submit" name="submit_action" value="save_update" onclick="submitAjaxSaveUpdate(event, this)" class="btn-tactical btn-tactical-primary" style="font-size: 13px; font-weight: 700; padding: 8px 16px; cursor: pointer;">
                <i class="fa-solid fa-save" style="margin-right: 6px;"></i> Save Update
            </button>
        </div>

      </div>
    <!-- Reset Password -->
    <div class="content-panel" style="margin-bottom: 24px;">
      <div class="panel-header" style="margin-bottom: 14px;">
        <h3 style="font-size: 15px; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-key" style="color: #8c96a8;"></i>
          <span>Reset Password</span>
        </h3>
      </div>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
        @php
          $canWatchCadetPassword = auth()->check() && auth()->user()->canAccessCadetPasswords();
        @endphp
        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px;">Current / Previous Password</label>
          @if($canWatchCadetPassword)
            @php
              $currentCadetPlainPassword = $student->plain_password ?: ($student->user->plain_password ?: 'password');
            @endphp
            <div style="position: relative;">
              <input type="password" id="cadet_current_password_input" readonly
                     value="{{ $currentCadetPlainPassword }}"
                     class="form-control"
                     style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.1); color: #cbd5e1; padding: 10px 42px 10px 14px; border-radius: 6px; font-family: monospace; font-size: 14px; height: 42px; width: 100%; box-sizing: border-box;"
                     placeholder="{{ empty($currentCadetPlainPassword) ? 'Encrypted / Not Recorded' : '' }}">
              <button type="button" onclick="togglePasswordVisibility('cadet_current_password_input', this)"
                      style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94a3b8; cursor: pointer; padding: 4px; font-size: 15px;"
                      title="Watch Password">
                <i class="fa-solid fa-eye"></i>
              </button>
            </div>
          @else
            <div style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08); color: #64748b; padding: 10px 14px; border-radius: 6px; font-family: monospace; font-size: 13px; height: 42px; box-sizing: border-box; display: flex; align-items: center; gap: 8px;">
              <i class="fa-solid fa-lock" style="color: #64748b;"></i>
              <span>•••••••• (Restricted)</span>
            </div>
          @endif
        </div>
        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px;">New Password</label>
          <div style="position: relative;">
              <input type="password" id="new_password_input" name="password" minlength="6" class="form-control" placeholder="Enter new password (min 6 chars)" style="padding-right: 40px; height: 42px;">
              <button type="button" onclick="togglePasswordVisibility('new_password_input', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94a3b8; cursor: pointer;">
                  <i class="fa-solid fa-eye"></i>
              </button>
          </div>
        </div>
        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px;">Confirm New Password</label>
          <div style="position: relative;">
              <input type="password" id="confirm_password_input" name="password_confirmation" minlength="6" class="form-control" placeholder="Re-enter new password" style="padding-right: 40px; height: 42px;">
              <button type="button" onclick="togglePasswordVisibility('confirm_password_input', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94a3b8; cursor: pointer;">
                  <i class="fa-solid fa-eye"></i>
              </button>
          </div>
        </div>
      </div>

      <div style="text-align: right; margin-top: 18px; border-top: 1px solid rgba(255,255,255,0.06); padding-top: 14px;">
        <button type="submit" name="submit_action" value="save_update" onclick="submitAjaxSaveUpdate(event, this)" class="btn-tactical btn-tactical-primary" style="font-size: 13px; font-weight: 700; padding: 8px 18px; cursor: pointer;">
          <i class="fa-solid fa-save" style="margin-right: 6px;"></i> Save Update
        </button>
      </div>
    </div>

    <!-- Submit Toolbar -->
    <div style="display: flex; justify-content: flex-end; align-items: center; gap: 12px; background: #131722; border: 1px solid rgba(255,255,255,0.08); border-radius: 14px; padding: 14px 20px; margin-bottom: 30px;">
      <a href="{{ route('admin.student_accounts.index') }}" class="btn-tactical" style="background: rgba(255,255,255,0.06); color: #94a3b8; border: 1px solid rgba(255,255,255,0.1); padding: 9px 18px; font-size: 13px; text-decoration: none; border-radius: 8px;">
        Cancel
      </a>
      <button type="submit" name="submit_action" value="save_update" onclick="submitAjaxSaveUpdate(event, this)" class="btn-tactical" style="background: rgba(255,255,255,0.08); color: #ffffff; border: 1px solid rgba(255,255,255,0.2); padding: 9px 20px; font-size: 13px; font-weight: 700; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-save"></i> Save Update
      </button>
      <button type="submit" name="submit_action" value="save_changes" onclick="document.getElementById('form_submit_action').value='save_changes'" class="btn-tactical" style="background: #ff5757; color: #ffffff; padding: 9px 24px; font-size: 13px; font-weight: 700; border: none; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
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
    document.getElementById('composedTargetWingHidden').value = selectedWing;
    var allCards = document.querySelectorAll('.wing-radio-card');
    allCards.forEach(function(card) {
      card.style.borderColor = 'rgba(255,255,255,0.08)';
      card.style.background = 'rgba(255,255,255,0.02)';
      var strong = card.querySelector('strong');
      if (strong) strong.style.color = '#cbd5e1';
    });

    var activeId = 'wing_card_' + selectedWing.toLowerCase().replace(/\s+/g, '-');
    var activeCard = document.getElementById(activeId);
    if (activeCard) {
      activeCard.style.borderColor = '#ff5757';
      activeCard.style.background = 'rgba(255,87,87,0.1)';
      var aStrong = activeCard.querySelector('strong');
      if (aStrong) aStrong.style.color = '#ffffff';
    }

    if (selectedWing === 'Police') {
      document.getElementById('militaryTracksSection').style.display = 'none';
      document.getElementById('policeTracksSection').style.display = 'block';
    } else {
      document.getElementById('militaryTracksSection').style.display = 'block';
      document.getElementById('policeTracksSection').style.display = 'none';
    }
  }

  function toggleMilitaryRank() {
    var rank = document.querySelector('input[name="military_rank_level"]:checked');
    if (!rank) return;

    var soldierInput = document.getElementById('input_track_soldier');
    var prelimInput = document.getElementById('input_track_prelim');
    var issbInput = document.getElementById('input_track_issb');

    var uiSoldier = document.getElementById('ui_rank_soldier');
    var uiOfficer = document.getElementById('ui_rank_officer');

    if(uiSoldier) { uiSoldier.style.borderColor = 'rgba(255,255,255,0.1)'; uiSoldier.style.background = 'rgba(255,255,255,0.02)'; }
    if(uiOfficer) { uiOfficer.style.borderColor = 'rgba(255,255,255,0.1)'; uiOfficer.style.background = 'rgba(255,255,255,0.02)'; }

    if (rank.value === 'soldier') {
      soldierInput.checked = true;
      prelimInput.checked = false;
      issbInput.checked = false;
      document.getElementById('officerTracksSection').style.display = 'none';
      if(uiSoldier) { uiSoldier.style.borderColor = '#10b981'; uiSoldier.style.background = 'rgba(16, 185, 129, 0.08)'; }
    } else {
      soldierInput.checked = false;
      document.getElementById('officerTracksSection').style.display = 'block';
      if(uiOfficer) { uiOfficer.style.borderColor = '#60a5fa'; uiOfficer.style.background = 'rgba(59, 130, 246, 0.08)'; }
    }
    
    onOfficerTrackChanged();
  }

  function onOfficerTrackChanged() {
    var prelim = document.getElementById('input_track_prelim');
    var issb = document.getElementById('input_track_issb');
    
    var uiPrelim = document.getElementById('ui_track_prelim');
    var uiIssb = document.getElementById('ui_track_issb');

    if(uiPrelim && prelim) {
      uiPrelim.style.borderColor = prelim.checked ? '#3b82f6' : 'rgba(255,255,255,0.1)';
      uiPrelim.style.background = prelim.checked ? 'rgba(59, 130, 246, 0.08)' : 'rgba(255,255,255,0.02)';
    }

    if(uiIssb && issb) {
      uiIssb.style.borderColor = issb.checked ? '#eab308' : 'rgba(255,255,255,0.1)';
      uiIssb.style.background = issb.checked ? 'rgba(234, 179, 8, 0.08)' : 'rgba(255,255,255,0.02)';
    }
  }

  function onPoliceTrackChanged() {
    var tracks = [
      { id: 'input_track_constable', ui: 'ui_track_constable', color: '#818cf8', bg: 'rgba(129, 140, 248, 0.08)' },
      { id: 'input_track_si', ui: 'ui_track_si', color: '#c084fc', bg: 'rgba(192, 132, 252, 0.08)' },
      { id: 'input_track_asi', ui: 'ui_track_asi', color: '#a855f7', bg: 'rgba(168, 85, 247, 0.08)' }
    ];
    tracks.forEach(function(t) {
      var input = document.getElementById(t.id);
      var ui = document.getElementById(t.ui);
      if(input && ui) {
        ui.style.borderColor = input.checked ? t.color : 'rgba(255,255,255,0.1)';
        ui.style.background = input.checked ? t.bg : 'rgba(255,255,255,0.02)';
      }
    });
  }

  document.addEventListener('DOMContentLoaded', function() {
    toggleMilitaryRank();
    onOfficerTrackChanged();
    onPoliceTrackChanged();
    filterCourses('none');
  });


  function filterCourses(branch) {
    // Filter course items
    var courses = document.querySelectorAll('.course-item');
    courses.forEach(function(course) {
      if (branch === 'none') {
        course.style.display = 'none';
      } else if (branch === 'all' || course.getAttribute('data-branch') === branch) {
        course.style.display = 'flex';
      } else {
        course.style.display = 'none';
      }
    });
  }

  function toggleCourseCardStyleCompact(checkbox, cardId) {
    var card = document.getElementById(cardId);
    if (!card) return;
    if (checkbox.checked) {
      card.style.borderColor = '#ff5757';
      card.style.background = 'rgba(255,87,87,0.08)';
    } else {
      card.style.borderColor = 'rgba(255,255,255,0.08)';
      card.style.background = 'rgba(255,255,255,0.02)';
    }
  }


  function switchPaymentCourse(cardId) {
    var cards = document.querySelectorAll('.payment-course-card');
    cards.forEach(function(card) {
      if (card.id === cardId) {
        card.style.display = 'block';
      } else {
        card.style.display = 'none';
      }
    });
  }

  function togglePasswordVisibility(inputId, btn) {
      var input = document.getElementById(inputId);
      var icon = btn.querySelector('i');
      if (input.type === 'password') {
          input.type = 'text';
          icon.classList.remove('fa-eye');
          icon.classList.add('fa-eye-slash');
          icon.style.color = '#ff5757';
      } else {
          input.type = 'password';
          icon.classList.remove('fa-eye-slash');
          icon.classList.add('fa-eye');
          icon.style.color = '#94a3b8';
      }
  }

  function showTacticalNotification(message, isSuccess) {
    if (typeof isSuccess === 'undefined') isSuccess = true;
    var existingToast = document.getElementById('tactical_ajax_toast');
    if (existingToast && existingToast.parentNode) {
      existingToast.parentNode.removeChild(existingToast);
    }

    var toast = document.createElement('div');
    toast.id = 'tactical_ajax_toast';
    toast.style.position = 'fixed';
    toast.style.bottom = '28px';
    toast.style.right = '28px';
    toast.style.zIndex = '999999';
    toast.style.display = 'flex';
    toast.style.alignItems = 'center';
    toast.style.gap = '12px';
    toast.style.padding = '14px 22px';
    toast.style.borderRadius = '10px';
    toast.style.fontSize = '13.5px';
    toast.style.fontWeight = '700';
    toast.style.boxShadow = '0 12px 30px rgba(0,0,0,0.6), 0 2px 10px rgba(0,0,0,0.4)';
    toast.style.transition = 'all 0.3s cubic-bezier(0.16, 1, 0.3, 1)';
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(16px)';
    toast.style.pointerEvents = 'none';

    if (isSuccess) {
      toast.style.background = '#0a101d';
      toast.style.border = '1.5px solid #10b981';
      toast.style.color = '#34d399';
      toast.innerHTML = '<i class="fa-solid fa-circle-check" style="font-size: 18px; color: #10b981;"></i> <span>' + message + '</span>';
    } else {
      toast.style.background = '#0a101d';
      toast.style.border = '1.5px solid #ef4444';
      toast.style.color = '#f87171';
      toast.innerHTML = '<i class="fa-solid fa-triangle-exclamation" style="font-size: 18px; color: #ef4444;"></i> <span>' + message + '</span>';
    }

    document.body.appendChild(toast);
    setTimeout(function() {
      toast.style.opacity = '1';
      toast.style.transform = 'translateY(0)';
    }, 20);

    setTimeout(function() {
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(16px)';
      setTimeout(function() {
        if (toast.parentNode) {
          toast.parentNode.removeChild(toast);
        }
      }, 350);
    }, 4500);
  }

  function submitAjaxSaveUpdate(event, btn) {
    if (event) {
      event.preventDefault();
      event.stopPropagation();
    }

    var form = document.getElementById('updateStudentForm');
    if (!form) return;

    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }

    var originalHtml = btn ? btn.innerHTML : '<i class="fa-solid fa-save" style="margin-right: 6px;"></i> Save Update';
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin" style="margin-right: 6px;"></i> Saving...';
    }

    var formData = new FormData(form);
    formData.set('submit_action', 'save_update');

    fetch(form.action, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    })
    .then(function(response) {
      return response.json().then(function(data) {
        return { status: response.status, ok: response.ok, body: data };
      }).catch(function() {
        return { status: response.status, ok: response.ok, body: null };
      });
    })
    .then(function(res) {
      if (btn) {
        btn.disabled = false;
      }
      if (res.ok && res.body && res.body.success) {
        if (btn) {
          btn.innerHTML = '<i class="fa-solid fa-circle-check" style="margin-right: 6px; color: #34d399;"></i> Saved!';
          setTimeout(function() {
            btn.innerHTML = originalHtml;
          }, 2500);
        }

        // Update password display if student password was updated or returned
        if (res.body.plain_password) {
          var pwInput = document.getElementById('cadet_current_password_input');
          if (pwInput) {
            pwInput.value = res.body.plain_password;
          }
          var np = document.getElementById('new_password_input');
          var cp = document.getElementById('confirm_password_input');
          if (np) np.value = '';
          if (cp) cp.value = '';
        }

        // Update confirmation placeholder if ID was changed
        if (res.body.login_id) {
          var dangerConfirm = document.getElementById('dangerConfirmInput');
          var dangerLabels = document.querySelectorAll('#dangerDeleteForm code');
          if (dangerLabels && dangerLabels.length > 0) {
            dangerLabels[0].textContent = res.body.login_id;
          }
        }

        showTacticalNotification(res.body.message || 'Cadet details updated successfully!', true);
      } else {
        if (btn) {
          btn.innerHTML = originalHtml;
        }
        var errorMsg = 'Failed to update cadet details.';
        if (res.body && res.body.errors) {
          var keys = Object.keys(res.body.errors);
          if (keys.length > 0 && res.body.errors[keys[0]].length > 0) {
            errorMsg = res.body.errors[keys[0]][0];
          }
        } else if (res.body && res.body.message) {
          errorMsg = res.body.message;
        }
        showTacticalNotification(errorMsg, false);
      }
    })
    .catch(function(err) {
      if (btn) {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
      }
      showTacticalNotification('Network error while saving. Please check your connection.', false);
    });
  }

  document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('updateStudentForm');
    if (form) {
      form.addEventListener('submit', function(e) {
        if (document.getElementById('form_submit_action').value === 'save_update') {
          e.preventDefault();
          var activeBtn = document.activeElement;
          if (!activeBtn || !activeBtn.classList || !activeBtn.classList.contains('btn-tactical')) {
            activeBtn = form.querySelector('button[value="save_update"]');
          }
          submitAjaxSaveUpdate(e, activeBtn);
        }
      });
    }
  });
</script>
@endsection