@extends('layouts.portal')

@section('title', 'Edit Assessment Module')
@section('page_title', 'Edit Assessment: ' . $exam->title)
@section('page_subtitle', 'Configure full examination parameters, access criteria, scoring rules, and scheduled timing')

@section('topbar_actions')
  <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
    <a href="{{ request('return_to', route('admin.exam_management.index')) }}" class="btn-tactical" style="background: rgba(255,255,255,0.06); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.12); padding: 8px 16px; font-size: 13px; font-weight: 700; text-decoration: none; border-radius: 8px; display: inline-flex; align-items: center; gap: 7px;">
      <i class="fa-solid fa-arrow-left"></i> Back to Exam Management
    </a>
    <a href="{{ route('admin.exams.questions', $exam->id) }}" class="btn-tactical" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3); padding: 8px 16px; font-size: 13px; font-weight: 700; text-decoration: none; border-radius: 8px; display: inline-flex; align-items: center; gap: 7px;">
      <i class="fa-solid fa-list-ol"></i> Manage Questions ({{ $exam->questions_count ?? $exam->questions()->count() }})
    </a>
  </div>
@endsection

@section('content')
<div style="max-width: 1200px; margin: 0 auto; width: 100%;">

  <!-- Breadcrumb & Quick Action Bar -->
  <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 24px;">
    <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #64748b;">
      <a href="{{ route('admin.dashboard') }}" style="color: #94a3b8; text-decoration: none;">Dashboard</a>
      <i class="fa-solid fa-chevron-right" style="font-size: 10px;"></i>
      <a href="{{ route('admin.exam_management.index') }}" style="color: #94a3b8; text-decoration: none;">Exam Management</a>
      <i class="fa-solid fa-chevron-right" style="font-size: 10px;"></i>
      <span style="color: #ff5757; font-weight: 700;">Edit: {{ Str::limit($exam->title, 40) }}</span>
    </div>

    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
      <a href="{{ route('admin.exam_management.index', ['type' => 'free']) }}" class="btn-tactical" style="background: rgba(255,255,255,0.04); color: #10b981; border: 1px solid rgba(16,185,129,0.25); padding: 6px 12px; font-size: 12px; font-weight: 600; text-decoration: none; border-radius: 6px; display: inline-flex; align-items: center; gap: 6px;">
        <i class="fa-solid fa-unlock-keyhole"></i> Free Exams
      </a>
      <a href="{{ route('admin.exam_management.index', ['type' => 'paid']) }}" class="btn-tactical" style="background: rgba(255,255,255,0.04); color: #60a5fa; border: 1px solid rgba(96,165,250,0.25); padding: 6px 12px; font-size: 12px; font-weight: 600; text-decoration: none; border-radius: 6px; display: inline-flex; align-items: center; gap: 6px;">
        <i class="fa-solid fa-user-graduate"></i> Cadet Exams
      </a>
      <a href="{{ route('admin.exam_management.index', ['type' => 'all']) }}" class="btn-tactical" style="background: rgba(255,255,255,0.04); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.1); padding: 6px 12px; font-size: 12px; font-weight: 600; text-decoration: none; border-radius: 6px; display: inline-flex; align-items: center; gap: 6px;">
        <i class="fa-solid fa-layer-group"></i> All Exams
      </a>
    </div>
  </div>

  {{-- Notification Alerts --}}
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

  <form action="{{ route('admin.exams.update', $exam->id) }}" method="POST" id="editExamForm" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="return_to" value="{{ request('return_to', route('admin.exam_management.index')) }}">

    <!-- SECTION 1: ASSESSMENT IDENTITY & ACCESS CONTROL -->
    <div class="content-panel" style="background: #181c26; border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 16px; padding: 26px 28px; margin-bottom: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.25);">
      <div style="border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 16px; margin-bottom: 22px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <h3 style="font-size: 16px; font-weight: 800; margin: 0; color: #ffffff; display: flex; align-items: center; gap: 10px;">
          <span style="width: 34px; height: 34px; border-radius: 9px; background: rgba(255, 87, 87, 0.15); color: #ff5757; display: grid; place-items: center; font-size: 15px;">
            <i class="fa-solid fa-id-card"></i>
          </span>
          Assessment Identity & Access Control
        </h3>
        <span style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">Section 1 of 4</span>
      </div>

      <div style="display: flex; flex-direction: column; gap: 18px;">
        {{-- Assessment Title --}}
        <div>
          <label class="ida-label">Assessment Title *</label>
          <input type="text" name="title" value="{{ old('title', $exam->title) }}" required placeholder="e.g. 2026 ISSB Verbal Intelligence Assessment" class="form-tactical" style="width: 100%; font-size: 14.5px; font-weight: 700; padding: 11px 16px;">
          <small style="color: #64748b; font-size: 11.5px; margin-top: 4px; display: block;">Official examination title visible to candidates across all platforms.</small>
        </div>

        {{-- Quick One-Click Examination Stage Presets --}}
        <div style="background: rgba(255,255,255,0.03); border: 1px dashed rgba(255,255,255,0.14); border-radius: 12px; padding: 14px 16px;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 9px; flex-wrap: wrap; gap: 6px;">
            <label style="font-size: 11px; font-weight: 800; color: #ff8585; text-transform: uppercase; letter-spacing: 0.6px; margin: 0; display: flex; align-items: center; gap: 6px;">
              <i class="fa-solid fa-wand-magic-sparkles"></i> One-Click Examination Category &amp; Track Presets:
            </label>
            <span style="font-size: 11px; color: #64748b;">Instantly selects wing, candidate clearance stage &amp; rules</span>
          </div>
          <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <button type="button" onclick="selectFullTrackPreset('navy', 'prelim', 'Navy Preliminary IQ &amp; Academic')" class="btn-tactical" style="padding: 6px 13px; font-size: 11px; font-weight: 700; background: rgba(56,189,248,0.12); color: #38bdf8; border: 1px solid rgba(56,189,248,0.35); border-radius: 6px;">
              ⚓ Navy Prelim
            </button>
            <button type="button" onclick="selectFullTrackPreset('army', 'prelim', 'Army Preliminary IQ &amp; Academic')" class="btn-tactical" style="padding: 6px 13px; font-size: 11px; font-weight: 700; background: rgba(249,115,22,0.12); color: #fb923c; border: 1px solid rgba(249,115,22,0.35); border-radius: 6px;">
              🪖 Army Prelim
            </button>
            <button type="button" onclick="selectFullTrackPreset('air_force', 'prelim', 'Air Force Preliminary IQ &amp; Academic')" class="btn-tactical" style="padding: 6px 13px; font-size: 11px; font-weight: 700; background: rgba(96,165,250,0.12); color: #60a5fa; border: 1px solid rgba(96,165,250,0.35); border-radius: 6px;">
              ✈️ Air Force Prelim
            </button>
            <button type="button" onclick="selectFullTrackPreset('army', 'issb', 'Tri-Services ISSB Assessment')" class="btn-tactical" style="padding: 6px 15px; font-size: 11.5px; font-weight: 800; background: rgba(234,179,8,0.18); color: #facc15; border: 1.5px solid rgba(234,179,8,0.5); border-radius: 6px;">
              🌟 ISSB Masterclass (Tri-Services)
            </button>
            <button type="button" onclick="selectFullTrackPreset('police', 'constable', 'Police Constable Recruitment Test')" class="btn-tactical" style="padding: 6px 13px; font-size: 11px; font-weight: 700; background: rgba(129,140,248,0.12); color: #818cf8; border: 1px solid rgba(129,140,248,0.35); border-radius: 6px;">
              👮 Police Prelim (Constable)
            </button>
            <button type="button" onclick="selectFullTrackPreset('police', 'si', 'Police Sub-Inspector (SI) Test')" class="btn-tactical" style="padding: 6px 13px; font-size: 11px; font-weight: 700; background: rgba(192,132,252,0.12); color: #c084fc; border: 1px solid rgba(192,132,252,0.35); border-radius: 6px;">
              🔍 Police SI
            </button>
            <button type="button" onclick="selectFullTrackPreset('police', 'asi', 'Police Assistant Sub-Inspector (ASI) Test')" class="btn-tactical" style="padding: 6px 13px; font-size: 11px; font-weight: 700; background: rgba(168,85,247,0.12); color: #a855f7; border: 1px solid rgba(168,85,247,0.35); border-radius: 6px;">
              🛡️ Police Assistant SI (ASI)
            </button>
          </div>
        </div>

        {{-- Branch Wing, Target Track & Access Type --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 18px;">
          <div>
            <label class="ida-label">Branch Wing *</label>
            <select name="branch" id="examBranchSelect" class="form-tactical" required style="width: 100%; padding: 11px 16px;">
              <option value="army" {{ old('branch', $exam->branch) === 'army' ? 'selected' : '' }}>Bangladesh Army</option>
              <option value="navy" {{ old('branch', $exam->branch) === 'navy' ? 'selected' : '' }}>Bangladesh Navy</option>
              <option value="air_force" {{ old('branch', $exam->branch) === 'air_force' ? 'selected' : '' }}>Bangladesh Air Force</option>
              <option value="police" {{ old('branch', $exam->branch) === 'police' ? 'selected' : '' }}>Bangladesh Police</option>
            </select>
            <small style="color: #64748b; font-size: 11.5px; margin-top: 4px; display: block;">Branch authority governing this module.</small>
          </div>

          <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 7px;">
              <label class="ida-label" style="margin: 0;">Target Track / Stage *</label>
              <span id="trackRuleBadge" style="font-size: 9.5px; font-weight: 800; padding: 2px 7px; border-radius: 4px; background: rgba(255,87,87,0.15); color: #ff8585;">Track Rule</span>
            </div>
            <select name="target_track" id="examTargetTrackSelect" class="form-tactical" required style="width: 100%; padding: 11px 16px;">
              <!-- Dynamically populated based on branch -->
            </select>
            <small id="trackEligibilityHint" style="color: #94a3b8; font-size: 11.5px; margin-top: 4px; display: block; line-height: 1.4;">
              Candidate eligibility rules for this stage.
            </small>
          </div>

          <div>
            <label class="ida-label">Access Type *</label>
            <select name="access_type" class="form-tactical" required style="width: 100%; padding: 11px 16px;">
              <option value="free" {{ old('access_type', $exam->access_type ?? ($exam->is_paid_for_external ? 'paid' : 'free')) === 'free' ? 'selected' : '' }}>Free Exam</option>
              <option value="paid" {{ in_array(old('access_type', $exam->access_type ?? ($exam->is_paid_for_external ? 'paid' : 'free')), ['paid', 'cadet']) ? 'selected' : '' }}>Cadet Exam</option>
              <option value="both" {{ old('access_type', $exam->access_type ?? ($exam->is_paid_for_external ? 'paid' : 'free')) === 'both' ? 'selected' : '' }}>Both</option>
            </select>
            <small style="color: #64748b; font-size: 11.5px; margin-top: 4px; display: block;">Select candidate eligibility: Free Exam, Cadet Exam, or Both.</small>
          </div>
        </div>

        {{-- Category (Totally Editable) & Quick Track Badges --}}
        <div>
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 7px; flex-wrap: wrap; gap: 8px;">
            <label class="ida-label" style="margin: 0;">Category / Preparation Track *</label>
            <div style="display: flex; gap: 6px; flex-wrap: wrap;" id="categoryQuickPills">
              <!-- Dynamically populated from selected branch -->
            </div>
          </div>
          <input type="text" name="category" id="examCategoryInput" value="{{ old('category', $exam->category) }}" required placeholder="e.g. Verbal IQ, WAT, Psychological, Spatial Matrix" class="form-tactical" style="width: 100%; padding: 11px 16px;">
          <small style="color: #64748b; font-size: 11.5px; margin-top: 4px; display: block;">Custom topic or preparatory program track. Click any quick pill above to auto-fill.</small>
        </div>
      </div>
    </div>

    <!-- SECTION 2: ENGINE CONFIGURATION & SCORING RULES -->
    <div class="content-panel" style="background: #181c26; border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 16px; padding: 26px 28px; margin-bottom: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.25);">
      <div style="border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 16px; margin-bottom: 22px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <h3 style="font-size: 16px; font-weight: 800; margin: 0; color: #ffffff; display: flex; align-items: center; gap: 10px;">
          <span style="width: 34px; height: 34px; border-radius: 9px; background: rgba(59, 130, 246, 0.15); color: #60a5fa; display: grid; place-items: center; font-size: 15px;">
            <i class="fa-solid fa-sliders"></i>
          </span>
          Engine Configuration & Scoring Rules
        </h3>
        <span style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">Section 2 of 4</span>
      </div>

      <div style="display: flex; flex-direction: column; gap: 18px;">
        {{-- Exam Engine Type --}}
        <div>
          <label class="ida-label">Exam Engine Type *</label>
          <select name="exam_type" class="form-tactical" required style="width: 100%; padding: 11px 16px;">
            <option value="iq_mcq" {{ old('exam_type', $exam->exam_type) === 'iq_mcq' ? 'selected' : '' }}>Intelligence MCQ (Timed Palette & Questions)</option>
            <option value="word_association" {{ old('exam_type', $exam->exam_type) === 'word_association' ? 'selected' : '' }}>Word Association Test (WAT 15s Flash)</option>
            <option value="non_verbal_iq" {{ old('exam_type', $exam->exam_type) === 'non_verbal_iq' ? 'selected' : '' }}>Non-Verbal Matrix IQ (Visual Analysis)</option>
            <option value="general_aptitude" {{ old('exam_type', $exam->exam_type) === 'general_aptitude' ? 'selected' : '' }}>General Academic Aptitude</option>
          </select>
        </div>

        {{-- Duration, Total Marks, Pass Marks, Negative Marking --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px;">
          <div>
            <label class="ida-label">Duration (Minutes) *</label>
            <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $exam->duration_minutes) }}" min="1" max="300" class="form-tactical" required style="width: 100%; padding: 11px 16px;">
            <small style="color: #64748b; font-size: 11.5px; margin-top: 4px; display: block;">Total test time limit in minutes.</small>
          </div>

          <div>
            <label class="ida-label">Total Marks *</label>
            <input type="number" step="0.5" name="total_marks" value="{{ old('total_marks', (float)$exam->total_marks) }}" min="1" class="form-tactical" required style="width: 100%; padding: 11px 16px;">
            <small style="color: #64748b; font-size: 11.5px; margin-top: 4px; display: block;">Maximum obtainable score.</small>
          </div>

          <div style="background: rgba(16, 185, 129, 0.05); border: 1.5px solid rgba(16, 185, 129, 0.35); border-radius: 12px; padding: 14px 16px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
              <label class="ida-label" style="margin-bottom: 0; display: flex; align-items: center; gap: 6px; color: #34d399; font-weight: 700;">
                <i class="fa-solid fa-award"></i> Minimum Result for Qualification (Pass Mark) *
              </label>
              <span style="font-size: 10px; font-weight: 800; background: rgba(16, 185, 129, 0.2); color: #34d399; padding: 2px 8px; border-radius: 6px; text-transform: uppercase;">
                Result Connector
              </span>
            </div>
            <input type="number" step="0.5" name="pass_marks" id="pass_marks" value="{{ old('pass_marks', (float)$exam->pass_marks) }}" min="0" class="form-tactical" required style="width: 100%; padding: 11px 16px; border-color: rgba(16, 185, 129, 0.5);">
            <div style="margin-top: 8px; display: flex; flex-direction: column; gap: 4px;">
              <small style="color: #94a3b8; font-size: 11.5px; line-height: 1.4;">
                Minimum score required to qualify candidate. Connects directly to candidate scorecard & history status:
              </small>
              <div style="display: flex; gap: 12px; font-size: 11px; font-weight: 700; margin-top: 3px; flex-wrap: wrap;">
                <span style="color: #34d399; display: inline-flex; align-items: center; gap: 5px;">
                  <i class="fa-solid fa-circle-check"></i> Score &ge; Pass Mark &rarr; <span style="background: rgba(16,185,129,0.2); padding: 1px 6px; border-radius: 4px;">QUALIFIED</span>
                </span>
                <span style="color: #f87171; display: inline-flex; align-items: center; gap: 5px;">
                  <i class="fa-solid fa-circle-xmark"></i> Score &lt; Pass Mark &rarr; <span style="background: rgba(239,68,68,0.2); padding: 1px 6px; border-radius: 4px;">NOT QUALIFIED</span>
                </span>
              </div>
            </div>
          </div>

          <div>
            <label class="ida-label">Negative Marking *</label>
            <input type="number" step="0.05" name="negative_marking_per_wrong" value="{{ old('negative_marking_per_wrong', (float)($exam->negative_marking_per_wrong ?? 0.25)) }}" min="0" class="form-tactical" placeholder="0.25" style="width: 100%; padding: 11px 16px;">
            <small style="color: #64748b; font-size: 11.5px; margin-top: 4px; display: block;">Marks deducted per wrong answer.</small>
          </div>
        </div>
      </div>
    </div>

    <!-- SECTION 3: SCHEDULING & BRIEF DESCRIPTION -->
    <div class="content-panel" style="background: #181c26; border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 16px; padding: 26px 28px; margin-bottom: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.25);">
      <div style="border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 16px; margin-bottom: 22px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <h3 style="font-size: 16px; font-weight: 800; margin: 0; color: #ffffff; display: flex; align-items: center; gap: 10px;">
          <span style="width: 34px; height: 34px; border-radius: 9px; background: rgba(16, 185, 129, 0.15); color: #34d399; display: grid; place-items: center; font-size: 15px;">
            <i class="fa-solid fa-calendar-clock"></i>
          </span>
          Scheduling & Candidate Overview
        </h3>
        <span style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">Section 3 of 4</span>
      </div>

      <div style="display: flex; flex-direction: column; gap: 18px;">
        {{-- Schedule Status, Schedule Start Date & Time, Schedule End Date & Time --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 18px;">
          <div>
            <label class="ida-label">Schedule Status *</label>
            <select name="status" class="form-tactical" required style="width: 100%; padding: 11px 16px;">
              <option value="open" {{ old('status', $exam->status) === 'open' ? 'selected' : '' }}>🟢 Live Now (Open Access)</option>
              <option value="scheduled" {{ old('status', $exam->status) === 'scheduled' ? 'selected' : '' }}>🕐 Scheduled Countdown</option>
              <option value="closed" {{ old('status', $exam->status) === 'closed' ? 'selected' : '' }}>🔒 Ended (Closed)</option>
              <option value="draft" {{ old('status', $exam->status) === 'draft' ? 'selected' : '' }}>📝 Draft (Hidden)</option>
            </select>
            <small style="color: #64748b; font-size: 11.5px; margin-top: 4px; display: block;">Live opens immediately; Scheduled enforces countdown.</small>
          </div>

          <div>
            <label class="ida-label">Schedule Start Date & Time</label>
            <input type="datetime-local" name="schedule_start" value="{{ old('schedule_start', $exam->schedule_start ? $exam->schedule_start->format('Y-m-d\TH:i') : '') }}" class="form-tactical" style="width: 100%; padding: 11px 16px;">
            <small style="color: #64748b; font-size: 11.5px; margin-top: 4px; display: block;">Starting date & time (required for scheduled countdowns).</small>
          </div>

          <div>
            <label class="ida-label">Schedule End Date & Time</label>
            <input type="datetime-local" name="schedule_end" value="{{ old('schedule_end', $exam->schedule_end ? $exam->schedule_end->format('Y-m-d\TH:i') : '') }}" class="form-tactical" style="width: 100%; padding: 11px 16px;">
            <small style="color: #64748b; font-size: 11.5px; margin-top: 4px; display: block;">Optional closing date & time after which test closes.</small>
          </div>
        </div>

        {{-- Brief Description --}}
        <div>
          <label class="ida-label">Brief Description</label>
          <textarea name="description" rows="3" class="form-tactical" placeholder="Summary of topics, verbal IQ sections, or instructions..." style="width: 100%; padding: 12px 16px; line-height: 1.6;">{{ old('description', $exam->description) }}</textarea>
          <small style="color: #64748b; font-size: 11.5px; margin-top: 4px; display: block;">Concise overview of 80 to 100 letters displayed directly on candidate exam cards.</small>
        </div>
      </div>
    </div>

    <!-- SECTION 4: QUESTION BANK & ADD QUESTIONS -->
    <div id="section-questions-management" class="content-panel" style="background: #181c26; border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 16px; padding: 26px 28px; margin-bottom: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.25);">
      <div style="border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 16px; margin-bottom: 22px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <h3 style="font-size: 16px; font-weight: 800; margin: 0; color: #ffffff; display: flex; align-items: center; gap: 10px;">
          <span style="width: 34px; height: 34px; border-radius: 9px; background: rgba(59, 130, 246, 0.15); color: #60a5fa; display: grid; place-items: center; font-size: 15px;">
            <i class="fa-solid fa-list-check"></i>
          </span>
          Question Bank & Add Questions
          <span style="background: rgba(59, 130, 246, 0.2); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.35); font-size: 11px; padding: 2px 8px; border-radius: 4px; font-weight: 800; letter-spacing: 0.5px;">
            <span id="existingCountBadge">{{ $exam->questions->count() }}</span> CURRENT QUESTIONS
          </span>
        </h3>
        <span style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">Section 4 of 4</span>
      </div>

      <p style="color: #94a3b8; font-size: 13px; line-height: 1.6; margin-top: 0; margin-bottom: 18px;">
        Manage existing questions assigned to this examination module, or expand the question bank by uploading from PDF, loading from JSON, or adding via Text Editor.
      </p>

      <!-- SUBSECTION A: EXISTING QUESTIONS LIST -->
      <div style="margin-bottom: 26px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 8px;">
          <h4 style="color: #ffffff; font-size: 14px; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-database" style="color: #10b981;"></i> Current Module Questions
          </h4>
          <span style="color: #64748b; font-size: 11.5px;">Clicking delete permanently removes the question from this assessment.</span>
        </div>

        <div id="existingQuestionsContainer" style="display: flex; flex-direction: column; gap: 10px; max-height: 420px; overflow-y: auto; padding-right: 4px;">
          @forelse($exam->questions as $q)
            @php
              $opts = is_array($q->options) ? $q->options : json_decode($q->options, true) ?? [];
              $optA = ''; $optB = ''; $optC = ''; $optD = '';
              foreach ($opts as $o) {
                $k = strtoupper($o['key'] ?? '');
                if ($k === 'A') $optA = $o['text'] ?? '';
                elseif ($k === 'B') $optB = $o['text'] ?? '';
                elseif ($k === 'C') $optC = $o['text'] ?? '';
                elseif ($k === 'D') $optD = $o['text'] ?? '';
              }
            @endphp
            <div id="existing-q-card-{{ $q->id }}" class="q-card" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 10px; padding: 14px 18px; transition: border-color 0.2s, opacity 0.25s;">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; flex-wrap: wrap; gap: 8px;">
                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                  <span style="background: #334155; color: #cbd5e1; font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 4px;">#{{ $loop->iteration }}</span>
                  <span id="existing-q-ans-badge-{{ $q->id }}" style="background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.35); font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 4px;">
                    <i class="fa-solid fa-check"></i> Ans: {{ $q->correct_answer }}
                  </span>
                  <span id="existing-q-marks-badge-{{ $q->id }}" style="font-size: 11px; color: #94a3b8;">
                    {{ $q->marks ? $q->marks . ' Marks' : '' }}
                  </span>
                </div>
                <div style="display: flex; align-items: center; gap: 6px;">
                  <button type="button" onclick="toggleEditExistingQuestion({{ $q->id }})" style="background: rgba(59, 130, 246, 0.12); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.25); font-size: 12px; font-weight: 700; cursor: pointer; padding: 4px 10px; border-radius: 6px; display: inline-flex; align-items: center; gap: 5px;">
                    <i class="fa-solid fa-pen-to-square"></i> Edit Question
                  </button>
                  <button type="button" onclick="deleteExistingQuestion({{ $q->id }})" style="background: rgba(239, 68, 68, 0.12); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.25); font-size: 12px; font-weight: 700; cursor: pointer; padding: 4px 10px; border-radius: 6px; display: inline-flex; align-items: center; gap: 5px;">
                    <i class="fa-solid fa-trash-can"></i> Delete
                  </button>
                </div>
              </div>

              <!-- NORMAL DISPLAY VIEW -->
              <div id="existing-q-view-{{ $q->id }}">
                <div id="existing-q-text-view-{{ $q->id }}" style="color: #f8fafc; font-size: 14px; font-weight: 500; line-height: 1.75; letter-spacing: 0.01em; margin-bottom: 14px;">
                  {!! App\Services\McqPdfParserService::renderStemContent($q->question_text) !!}
                </div>

                <div id="existing-q-opts-view-{{ $q->id }}" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 13.5px;">
                  @foreach($opts as $opt)
                    @php
                      $isCorrect = strtoupper($opt['key'] ?? '') === strtoupper($q->correct_answer);
                    @endphp
                    <div style="padding: 10px 14px; min-height: 46px; border-radius: 8px; display: flex; align-items: center; gap: 9px; {{ $isCorrect ? 'background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #34d399; font-weight: 700;' : 'background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); color: #cbd5e1;' }}">
                      <strong style="color: {{ $isCorrect ? '#34d399' : '#60a5fa' }}; font-size: 13px;">{{ $opt['key'] ?? '' }})</strong> <div style="display: inline-block; overflow-x: auto;">{!! App\Services\McqPdfParserService::renderStemContent($opt['text'] ?? '') !!}</div>
                    </div>
                  @endforeach
                </div>

                <div id="existing-q-expl-view-{{ $q->id }}" style="margin-top: 10px; font-size: 11.5px; color: #94a3b8; background: rgba(255,255,255,0.02); border-left: 2px solid #3b82f6; padding: 6px 10px; border-radius: 0 4px 4px 0; {{ empty($q->explanation) ? 'display: none;' : '' }}">
                  <strong style="color: #60a5fa;">Explanation:</strong> <span class="expl-content">{!! App\Services\McqPdfParserService::renderStemContent($q->explanation) !!}</span>
                </div>
              </div>

              <!-- INLINE EDITING FORM -->
              <div id="existing-q-edit-box-{{ $q->id }}" style="display: none; background: rgba(0,0,0,0.28); border: 1px solid rgba(59, 130, 246, 0.35); border-radius: 8px; padding: 14px; margin-top: 10px;">
                <div style="margin-bottom: 10px;">
                  <label class="ida-label" style="font-size: 11px;">Edit Question Text *</label>
                  <textarea id="edit-existing-text-{{ $q->id }}" rows="2" class="form-tactical" style="width: 100%; padding: 8px 12px; font-size: 13px;">{{ $q->question_text }}</textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">
                  <div>
                    <label class="ida-label" style="font-size: 11px;">Option A *</label>
                    <input type="text" id="edit-existing-opt-a-{{ $q->id }}" value="{{ $optA }}" class="form-tactical" style="width: 100%; padding: 7px 10px; font-size: 12.5px;">
                  </div>
                  <div>
                    <label class="ida-label" style="font-size: 11px;">Option B *</label>
                    <input type="text" id="edit-existing-opt-b-{{ $q->id }}" value="{{ $optB }}" class="form-tactical" style="width: 100%; padding: 7px 10px; font-size: 12.5px;">
                  </div>
                  <div>
                    <label class="ida-label" style="font-size: 11px;">Option C *</label>
                    <input type="text" id="edit-existing-opt-c-{{ $q->id }}" value="{{ $optC }}" class="form-tactical" style="width: 100%; padding: 7px 10px; font-size: 12.5px;">
                  </div>
                  <div>
                    <label class="ida-label" style="font-size: 11px;">Option D *</label>
                    <input type="text" id="edit-existing-opt-d-{{ $q->id }}" value="{{ $optD }}" class="form-tactical" style="width: 100%; padding: 7px 10px; font-size: 12.5px;">
                  </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 2fr; gap: 10px; margin-bottom: 12px;">
                  <div>
                    <label class="ida-label" style="font-size: 11px;">Correct Answer Key *</label>
                    <select id="edit-existing-ans-{{ $q->id }}" class="form-tactical" style="width: 100%; padding: 7px 10px; font-size: 12.5px; font-weight: 700;">
                      <option value="A" {{ strtoupper($q->correct_answer) === 'A' ? 'selected' : '' }}>Option A</option>
                      <option value="B" {{ strtoupper($q->correct_answer) === 'B' ? 'selected' : '' }}>Option B</option>
                      <option value="C" {{ strtoupper($q->correct_answer) === 'C' ? 'selected' : '' }}>Option C</option>
                      <option value="D" {{ strtoupper($q->correct_answer) === 'D' ? 'selected' : '' }}>Option D</option>
                    </select>
                  </div>
                  <div>
                    <label class="ida-label" style="font-size: 11px;">Marks *</label>
                    <input type="number" step="0.5" id="edit-existing-marks-{{ $q->id }}" value="{{ $q->marks ?? 1.0 }}" class="form-tactical" style="width: 100%; padding: 7px 10px; font-size: 12.5px;">
                  </div>
                  <div>
                    <label class="ida-label" style="font-size: 11px;">Explanation (Optional)</label>
                    <input type="text" id="edit-existing-expl-{{ $q->id }}" value="{{ $q->explanation }}" class="form-tactical" style="width: 100%; padding: 7px 10px; font-size: 12.5px;">
                  </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 8px;">
                  <button type="button" onclick="toggleEditExistingQuestion({{ $q->id }})" style="background: rgba(255,255,255,0.06); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.12); padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer;">
                    Cancel
                  </button>
                  <button type="button" onclick="saveExistingQuestion({{ $q->id }})" style="background: #10b981; color: #ffffff; border: none; padding: 6px 16px; border-radius: 6px; font-size: 12px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);">
                    <i class="fa-solid fa-check"></i> Save Changes to Question
                  </button>
                </div>
              </div>

            </div>
          @empty
            <div id="noExistingQuestionsMsg" style="text-align: center; padding: 30px; color: #64748b; background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.08); border-radius: 10px; font-size: 13px;">
              No questions found in this assessment yet. Add questions below.
            </div>
          @endforelse
        </div>
      </div>

      <!-- SUBSECTION B: ADD NEW QUESTIONS -->
      <div style="border-top: 1px solid rgba(255,255,255,0.08); padding-top: 22px;">
        <h4 style="color: #ffffff; font-size: 15px; font-weight: 800; margin: 0 0 14px 0; display: flex; align-items: center; gap: 8px;">
          <span style="width: 26px; height: 26px; border-radius: 6px; background: rgba(245, 158, 11, 0.15); color: #fbbf24; display: grid; place-items: center; font-size: 13px;">
            <i class="fa-solid fa-plus"></i>
          </span>
          Add to Question Paper
        </h4>

        <!-- Ingestion Mode Navigation Tabs: ONLY 2 OPTIONS -->
        <div style="display: flex; gap: 10px; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 14px; margin-bottom: 20px; flex-wrap: wrap;">
          <button type="button" class="q-tab-btn active" id="tab-btn-bulk" onclick="switchQTab('bulk')" style="background: rgba(168, 85, 247, 0.15); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.4); border-radius: 8px; padding: 10px 22px; font-size: 13.5px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; gap: 9px; box-shadow: 0 4px 12px rgba(168, 85, 247, 0.15);">
            <i class="fa-solid fa-layer-group"></i> Bulk Upload
          </button>
          <button type="button" class="q-tab-btn" id="tab-btn-single" onclick="switchQTab('single')" style="background: rgba(255,255,255,0.04); color: #94a3b8; border: 1px solid rgba(255,255,255,0.08); border-radius: 8px; padding: 10px 22px; font-size: 13.5px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; gap: 9px;">
            <i class="fa-solid fa-circle-plus"></i> Single Upload
          </button>
        </div>

        <!-- Ingestion Feedback Alerts -->
        <div id="ingestionAlert" style="display: none; border-radius: 10px; padding: 14px 18px; margin-bottom: 20px; font-size: 13px; line-height: 1.5;"></div>

        <!-- OPTION 1: BULK UPLOAD -->
        <div id="tab-panel-bulk" class="q-tab-panel" style="display: block;">
          <div style="background: #11141d; border: 1px solid rgba(168, 85, 247, 0.25); border-radius: 12px; padding: 20px; margin-bottom: 20px;">
            <textarea id="bulkQuestionInput" rows="7" class="form-tactical" placeholder="" style="width: 100%; height: 190px; padding: 14px 16px; font-size: 13.5px; line-height: 1.6; font-family: 'Fira Code', Consolas, monospace; resize: vertical; overflow-y: auto;"></textarea>

            <div style="display: flex; justify-content: flex-end; align-items: center; margin-top: 14px;">
              <button type="button" id="btnTransformBulk" onclick="transformBulkQuestions()" style="background: linear-gradient(135deg, #a855f7 0%, #6366f1 100%); color: #ffffff; border: none; padding: 11px 30px; font-size: 14px; font-weight: 800; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 16px rgba(168, 85, 247, 0.4);">
                <i class="fa-solid fa-cloud-arrow-down"></i> Load Question
              </button>
            </div>
          </div>
        </div>

        <!-- OPTION 2: SINGLE UPLOAD -->
        <div id="tab-panel-single" class="q-tab-panel" style="display: none;">
          <div style="background: #11141d; border: 1px solid rgba(59, 130, 246, 0.25); border-radius: 12px; padding: 20px; margin-bottom: 20px;">
            <div style="display: flex; flex-direction: column; gap: 14px;">
              <div>
                <label class="ida-label">Question</label>
                <textarea id="singleQuestionText" rows="3" class="form-tactical" placeholder="" oninput="updateSinglePreview()" style="width: 100%; padding: 10px 14px; font-size: 13.5px;"></textarea>
              </div>

              <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div>
                  <label class="ida-label">Option A</label>
                  <input type="text" id="singleOptionA" class="form-tactical" placeholder="" oninput="updateSinglePreview()" style="width: 100%; padding: 9px 14px;">
                </div>
                <div>
                  <label class="ida-label">Option B</label>
                  <input type="text" id="singleOptionB" class="form-tactical" placeholder="" oninput="updateSinglePreview()" style="width: 100%; padding: 9px 14px;">
                </div>
                <div>
                  <label class="ida-label">Option C</label>
                  <input type="text" id="singleOptionC" class="form-tactical" placeholder="" oninput="updateSinglePreview()" style="width: 100%; padding: 9px 14px;">
                </div>
                <div>
                  <label class="ida-label">Option D</label>
                  <input type="text" id="singleOptionD" class="form-tactical" placeholder="" oninput="updateSinglePreview()" style="width: 100%; padding: 9px 14px;">
                </div>
              </div>

              <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 14px;">
                <div>
                  <label class="ida-label">Correct Answer</label>
                  <select id="singleCorrectAnswer" class="form-tactical" style="width: 100%; padding: 9px 14px; font-weight: 700;">
                    <option value="A">Option A</option>
                    <option value="B">Option B</option>
                    <option value="C">Option C</option>
                    <option value="D">Option D</option>
                  </select>
                </div>
                <div>
                  <label class="ida-label">Explanation</label>
                  <input type="text" id="singleExplanation" class="form-tactical" placeholder="" oninput="updateSinglePreview()" style="width: 100%; padding: 9px 14px;">
                </div>
              </div>

              <!-- Real-time Live MathJax Preview Box for Single Question -->
              <div id="singleLivePreviewBox" style="display: none; background: #0b0e14; border: 1px solid rgba(59, 130, 246, 0.35); border-radius: 10px; padding: 14px 18px;">
                <div style="font-size: 11px; font-weight: 800; color: #60a5fa; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">
                  <i class="fa-solid fa-eye"></i> Live Preview
                </div>
                <div id="singlePreviewStatement" style="color: #ffffff; font-size: 13.5px; font-weight: 600; line-height: 1.5; margin-bottom: 10px;"></div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 12px;">
                  <div style="padding: 6px 10px; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); border-radius: 6px; color: #cbd5e1;">
                    <strong>A)</strong> <span id="singlePreviewOptA"></span>
                  </div>
                  <div style="padding: 6px 10px; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); border-radius: 6px; color: #cbd5e1;">
                    <strong>B)</strong> <span id="singlePreviewOptB"></span>
                  </div>
                  <div style="padding: 6px 10px; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); border-radius: 6px; color: #cbd5e1;">
                    <strong>C)</strong> <span id="singlePreviewOptC"></span>
                  </div>
                  <div style="padding: 6px 10px; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); border-radius: 6px; color: #cbd5e1;">
                    <strong>D)</strong> <span id="singlePreviewOptD"></span>
                  </div>
                </div>
                <div id="singlePreviewExplBox" style="display: none; margin-top: 8px; font-size: 11.5px; color: #94a3b8; background: rgba(255,255,255,0.02); border-left: 2px solid #3b82f6; padding: 5px 8px;">
                  <strong style="color: #60a5fa;">Explanation:</strong> <span id="singlePreviewExpl"></span>
                </div>
              </div>

              <div style="display: flex; justify-content: flex-end; margin-top: 6px;">
                <button type="button" onclick="addSingleManualQuestion()" style="background: #10b981; color: #ffffff; border: none; padding: 10px 28px; font-size: 14px; font-weight: 800; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 7px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);">
                  <i class="fa-solid fa-plus"></i> Load Question
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- NEWLY ADDED QUESTIONS PREVIEW CONTAINER -->
        <div style="background: #11141d; border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 20px 22px;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 10px;">
            <div style="display: flex; align-items: center; gap: 10px;">
              <h4 style="font-size: 14px; font-weight: 800; color: #ffffff; margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-plus-circle" style="color: #f59e0b;"></i>
                Newly Added Questions to Append
              </h4>
              <span id="newQuestionsCountBadge" style="background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.35); font-size: 12px; font-weight: 800; padding: 3px 10px; border-radius: 999px;">
                0 New Questions
              </span>
            </div>

            <button type="button" onclick="clearAllNewQuestions()" style="background: rgba(239, 68, 68, 0.12); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.25); border-radius: 6px; padding: 6px 14px; font-size: 12px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
              <i class="fa-solid fa-trash-can"></i> Clear New
            </button>
          </div>

          <!-- Hidden input to transport new questions array on form submit -->
          <input type="hidden" name="questions_json" id="questions_json" value="">

          <!-- Questions Cards Container -->
          <div id="newQuestionsPreviewContainer" style="display: flex; flex-direction: column; gap: 12px; max-height: 400px; overflow-y: auto; padding-right: 4px;">
            <div id="emptyNewQuestionsPlaceholder" style="text-align: center; padding: 32px 20px; color: #64748b; background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.08); border-radius: 10px;">
              <i class="fa-solid fa-cloud-arrow-up" style="font-size: 30px; color: #475569; margin-bottom: 8px; display: block;"></i>
              <div style="font-weight: 700; color: #94a3b8; font-size: 13.5px;">No New Questions Loaded</div>
            </div>
          </div>
        </div>

      </div>

    </div>

    <!-- STICKY BOTTOM ACTION TOOLBAR -->
    <div style="background: #181c26; border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 16px; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; margin-bottom: 40px; box-shadow: 0 8px 24px rgba(0,0,0,0.3);">
      <div style="display: flex; align-items: center; gap: 12px;">
        <a href="{{ request('return_to', route('admin.exam_management.index')) }}" class="btn-tactical" style="background: rgba(255,255,255,0.06); color: #94a3b8; border: 1px solid rgba(255,255,255,0.12); padding: 10px 20px; font-size: 13px; font-weight: 700; text-decoration: none; border-radius: 8px; display: inline-flex; align-items: center; gap: 7px;">
          <i class="fa-solid fa-xmark"></i> Cancel & Return
        </a>
      </div>

      <div style="display: flex; align-items: center; gap: 12px;">
        <button type="submit" class="btn-primary" style="background: #ff5757; color: #ffffff; padding: 11px 32px; font-size: 14px; font-weight: 800; border-radius: 8px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 9px; box-shadow: 0 4px 14px rgba(255, 87, 87, 0.35);">
          <i class="fa-solid fa-check"></i> Save Question Paper
        </button>
      </div>
    </div>

  </form>
</div>

<script>
  let newLoadedQuestions = [];

  async function deleteExistingQuestion(qId) {
    if (!confirm('Are you sure you want to permanently remove this question from this exam module?')) {
      return;
    }

    try {
      const res = await fetch("{{ route('admin.exams.delete_exam_question', ['examId' => $exam->id, 'questionId' => ':qId']) }}".replace(':qId', qId), {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        }
      });
      const data = await res.json();
      if (data.success) {
        const card = document.getElementById('existing-q-card-' + qId);
        if (card) {
          card.style.opacity = '0';
          setTimeout(() => {
            card.remove();
            const badge = document.getElementById('existingCountBadge');
            if (badge) badge.textContent = data.remaining_count;
            if (data.remaining_count === 0) {
              const cont = document.getElementById('existingQuestionsContainer');
              if (cont) {
                cont.innerHTML = `
                  <div id="noExistingQuestionsMsg" style="text-align: center; padding: 30px; color: #64748b; background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.08); border-radius: 10px; font-size: 13px;">
                    No questions found in this assessment yet. Add questions below.
                  </div>
                `;
              }
            }
          }, 250);
        }
        showIngestionAlert('Question removed successfully.', 'success');
      } else {
        alert(data.message || 'Failed to remove question.');
      }
    } catch (err) {
      alert('Error communicating with server: ' + err.message);
    }
  }

  function toggleEditExistingQuestion(qId) {
    const view = document.getElementById('existing-q-view-' + qId);
    const editBox = document.getElementById('existing-q-edit-box-' + qId);
    const card = document.getElementById('existing-q-card-' + qId);
    if (!view || !editBox) return;

    if (editBox.style.display === 'none' || !editBox.style.display) {
      editBox.style.display = 'block';
      view.style.display = 'none';
      if (card) card.style.borderColor = 'rgba(59, 130, 246, 0.45)';
    } else {
      editBox.style.display = 'none';
      view.style.display = 'block';
      if (card) card.style.borderColor = 'rgba(255, 255, 255, 0.08)';
    }
  }

  async function saveExistingQuestion(qId) {
    const text = (document.getElementById('edit-existing-text-' + qId)?.value || '').trim();
    const optA = (document.getElementById('edit-existing-opt-a-' + qId)?.value || '').trim();
    const optB = (document.getElementById('edit-existing-opt-b-' + qId)?.value || '').trim();
    const optC = (document.getElementById('edit-existing-opt-c-' + qId)?.value || '').trim();
    const optD = (document.getElementById('edit-existing-opt-d-' + qId)?.value || '').trim();
    const ans = document.getElementById('edit-existing-ans-' + qId)?.value || 'A';
    const marks = parseFloat(document.getElementById('edit-existing-marks-' + qId)?.value) || 1.0;
    const expl = (document.getElementById('edit-existing-expl-' + qId)?.value || '').trim();

    if (!text) {
      alert('Question text cannot be empty.');
      return;
    }
    if (!optA || !optB) {
      alert('Option A and Option B are required.');
      return;
    }

    try {
      const res = await fetch("{{ route('admin.exams.update_exam_question', ['examId' => $exam->id, 'questionId' => ':qId']) }}".replace(':qId', qId), {
        method: 'PUT',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          question_text: text,
          option_a: optA,
          option_b: optB,
          option_c: optC,
          option_d: optD,
          correct_answer: ans,
          marks: marks,
          explanation: expl
        })
      });

      const data = await res.json();
      if (data.success) {
        // Update DOM display elements
        const textView = document.getElementById('existing-q-text-view-' + qId);
        if (textView) textView.innerHTML = renderMathSafe(text);

        const ansBadge = document.getElementById('existing-q-ans-badge-' + qId);
        if (ansBadge) ansBadge.innerHTML = '<i class="fa-solid fa-check"></i> Ans: ' + escapeHtml(ans);

        const marksBadge = document.getElementById('existing-q-marks-badge-' + qId);
        if (marksBadge) marksBadge.textContent = marks + ' Marks';

        const optsView = document.getElementById('existing-q-opts-view-' + qId);
        if (optsView) {
          const optsData = [
            { key: 'A', text: optA },
            { key: 'B', text: optB },
            { key: 'C', text: optC },
            { key: 'D', text: optD },
          ];
          optsView.innerHTML = optsData.map(o => `
            <div style="padding: 7px 12px; border-radius: 6px; ${o.key === ans ? 'background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #34d399; font-weight: 700;' : 'background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); color: #cbd5e1;'}">
              <strong>${o.key})</strong> ${renderMathSafe(o.text)}
            </div>
          `).join('');
        }

        const explView = document.getElementById('existing-q-expl-view-' + qId);
        if (explView) {
          if (expl) {
            explView.style.display = 'block';
            const span = explView.querySelector('.expl-content');
            if (span) span.innerHTML = renderMathSafe(expl);
          } else {
            explView.style.display = 'none';
          }
        }

        toggleEditExistingQuestion(qId);
        showIngestionAlert('Question updated in exam paper successfully!', 'success');

        if (window.MathJax && window.MathJax.typesetPromise) {
          MathJax.typesetPromise().catch(function() {});
        }
      } else {
        alert(data.message || 'Failed to update question.');
      }
    } catch (err) {
      alert('Error updating question: ' + err.message);
    }
  }

  async function addSingleQuestionDirectlyToExam() {
    const text = (document.getElementById('manualQuestionText')?.value || '').trim();
    const optA = (document.getElementById('manualOptionA')?.value || '').trim();
    const optB = (document.getElementById('manualOptionB')?.value || '').trim();
    const optC = (document.getElementById('manualOptionC')?.value || '').trim();
    const optD = (document.getElementById('manualOptionD')?.value || '').trim();
    const ans = document.getElementById('manualCorrectAnswer')?.value || 'A';
    const expl = (document.getElementById('manualExplanation')?.value || '').trim();

    if (!text) {
      showIngestionAlert('Please provide question text statement.', 'error');
      return;
    }
    if (!optA || !optB) {
      showIngestionAlert('Please provide at least Option A and Option B.', 'error');
      return;
    }

    const btn = document.getElementById('btnAddDirectQuestion');
    if (btn) btn.disabled = true;

    try {
      const res = await fetch("{{ route('admin.exams.add_single_question', $exam->id) }}", {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          question_text: text,
          option_a: optA,
          option_b: optB,
          option_c: optC,
          option_d: optD,
          correct_answer: ans,
          explanation: expl
        })
      });

      const data = await res.json();
      if (data.success) {
        // Clear placeholder if it was empty
        const noMsg = document.getElementById('noExistingQuestionsMsg');
        if (noMsg) noMsg.remove();

        const q = data.question;
        const total = data.total_questions;
        const cont = document.getElementById('existingQuestionsContainer');
        const badge = document.getElementById('existingCountBadge');
        if (badge) badge.textContent = total;

        const newCardHtml = `
          <div id="existing-q-card-${q.id}" class="q-card" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(16, 185, 129, 0.35); border-radius: 10px; padding: 14px 18px; transition: border-color 0.2s, opacity 0.25s;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; flex-wrap: wrap; gap: 8px;">
              <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                <span style="background: #10b981; color: #000000; font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 4px;">#${total}</span>
                <span id="existing-q-ans-badge-${q.id}" style="background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.35); font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 4px;">
                  <i class="fa-solid fa-check"></i> Ans: ${escapeHtml(q.correct_answer)}
                </span>
                <span id="existing-q-marks-badge-${q.id}" style="font-size: 11px; color: #94a3b8;">${q.marks} Marks</span>
              </div>
              <div style="display: flex; align-items: center; gap: 6px;">
                <button type="button" onclick="toggleEditExistingQuestion(${q.id})" style="background: rgba(59, 130, 246, 0.12); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.25); font-size: 12px; font-weight: 700; cursor: pointer; padding: 4px 10px; border-radius: 6px; display: inline-flex; align-items: center; gap: 5px;">
                  <i class="fa-solid fa-pen-to-square"></i> Edit Question
                </button>
                <button type="button" onclick="deleteExistingQuestion(${q.id})" style="background: rgba(239, 68, 68, 0.12); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.25); font-size: 12px; font-weight: 700; cursor: pointer; padding: 4px 10px; border-radius: 6px; display: inline-flex; align-items: center; gap: 5px;">
                  <i class="fa-solid fa-trash-can"></i> Delete
                </button>
              </div>
            </div>

            <!-- NORMAL DISPLAY VIEW -->
            <div id="existing-q-view-${q.id}">
              <div id="existing-q-text-view-${q.id}" style="color: #ffffff; font-size: 13.5px; font-weight: 600; line-height: 1.5; margin-bottom: 10px;">
                ${renderMathSafe(q.question_text)}
              </div>
              <div id="existing-q-opts-view-${q.id}" style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 12.5px;">
                <div style="padding: 7px 12px; border-radius: 6px; ${q.correct_answer === 'A' ? 'background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #34d399; font-weight: 700;' : 'background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); color: #cbd5e1;'}">
                  <strong>A)</strong> ${renderMathSafe(q.option_a)}
                </div>
                <div style="padding: 7px 12px; border-radius: 6px; ${q.correct_answer === 'B' ? 'background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #34d399; font-weight: 700;' : 'background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); color: #cbd5e1;'}">
                  <strong>B)</strong> ${renderMathSafe(q.option_b)}
                </div>
                <div style="padding: 7px 12px; border-radius: 6px; ${q.correct_answer === 'C' ? 'background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #34d399; font-weight: 700;' : 'background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); color: #cbd5e1;'}">
                  <strong>C)</strong> ${renderMathSafe(q.option_c)}
                </div>
                <div style="padding: 7px 12px; border-radius: 6px; ${q.correct_answer === 'D' ? 'background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #34d399; font-weight: 700;' : 'background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); color: #cbd5e1;'}">
                  <strong>D)</strong> ${renderMathSafe(q.option_d)}
                </div>
              </div>
              <div id="existing-q-expl-view-${q.id}" style="margin-top: 10px; font-size: 11.5px; color: #94a3b8; background: rgba(255,255,255,0.02); border-left: 2px solid #3b82f6; padding: 6px 10px; border-radius: 0 4px 4px 0; ${q.explanation ? '' : 'display: none;'}">
                <strong style="color: #60a5fa;">Explanation:</strong> <span class="expl-content">${renderMathSafe(q.explanation || '')}</span>
              </div>
            </div>

            <!-- INLINE EDITING FORM -->
            <div id="existing-q-edit-box-${q.id}" style="display: none; background: rgba(0,0,0,0.28); border: 1px solid rgba(59, 130, 246, 0.35); border-radius: 8px; padding: 14px; margin-top: 10px;">
              <div style="margin-bottom: 10px;">
                <label class="ida-label" style="font-size: 11px;">Edit Question Text *</label>
                <textarea id="edit-existing-text-${q.id}" rows="2" class="form-tactical" style="width: 100%; padding: 8px 12px; font-size: 13px;">${escapeHtml(q.question_text)}</textarea>
              </div>
              <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">
                <div>
                  <label class="ida-label" style="font-size: 11px;">Option A *</label>
                  <input type="text" id="edit-existing-opt-a-${q.id}" value="${escapeHtml(q.option_a)}" class="form-tactical" style="width: 100%; padding: 7px 10px; font-size: 12.5px;">
                </div>
                <div>
                  <label class="ida-label" style="font-size: 11px;">Option B *</label>
                  <input type="text" id="edit-existing-opt-b-${q.id}" value="${escapeHtml(q.option_b)}" class="form-tactical" style="width: 100%; padding: 7px 10px; font-size: 12.5px;">
                </div>
                <div>
                  <label class="ida-label" style="font-size: 11px;">Option C *</label>
                  <input type="text" id="edit-existing-opt-c-${q.id}" value="${escapeHtml(q.option_c)}" class="form-tactical" style="width: 100%; padding: 7px 10px; font-size: 12.5px;">
                </div>
                <div>
                  <label class="ida-label" style="font-size: 11px;">Option D *</label>
                  <input type="text" id="edit-existing-opt-d-${q.id}" value="${escapeHtml(q.option_d)}" class="form-tactical" style="width: 100%; padding: 7px 10px; font-size: 12.5px;">
                </div>
              </div>
              <div style="display: grid; grid-template-columns: 1fr 1fr 2fr; gap: 10px; margin-bottom: 12px;">
                <div>
                  <label class="ida-label" style="font-size: 11px;">Correct Answer Key *</label>
                  <select id="edit-existing-ans-${q.id}" class="form-tactical" style="width: 100%; padding: 7px 10px; font-size: 12.5px; font-weight: 700;">
                    <option value="A" ${q.correct_answer === 'A' ? 'selected' : ''}>Option A</option>
                    <option value="B" ${q.correct_answer === 'B' ? 'selected' : ''}>Option B</option>
                    <option value="C" ${q.correct_answer === 'C' ? 'selected' : ''}>Option C</option>
                    <option value="D" ${q.correct_answer === 'D' ? 'selected' : ''}>Option D</option>
                  </select>
                </div>
                <div>
                  <label class="ida-label" style="font-size: 11px;">Marks *</label>
                  <input type="number" step="0.5" id="edit-existing-marks-${q.id}" value="${q.marks}" class="form-tactical" style="width: 100%; padding: 7px 10px; font-size: 12.5px;">
                </div>
                <div>
                  <label class="ida-label" style="font-size: 11px;">Explanation (Optional)</label>
                  <input type="text" id="edit-existing-expl-${q.id}" value="${escapeHtml(q.explanation || '')}" class="form-tactical" style="width: 100%; padding: 7px 10px; font-size: 12.5px;">
                </div>
              </div>
              <div style="display: flex; justify-content: flex-end; gap: 8px;">
                <button type="button" onclick="toggleEditExistingQuestion(${q.id})" style="background: rgba(255,255,255,0.06); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.12); padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer;">Cancel</button>
                <button type="button" onclick="saveExistingQuestion(${q.id})" style="background: #10b981; color: #ffffff; border: none; padding: 6px 16px; border-radius: 6px; font-size: 12px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; gap: 5px;"><i class="fa-solid fa-check"></i> Save Changes to Question</button>
              </div>
            </div>
          </div>
        `;

        if (cont) {
          cont.insertAdjacentHTML('beforeend', newCardHtml);
          const newCard = document.getElementById('existing-q-card-' + q.id);
          if (newCard) newCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        // Reset manual form fields
        document.getElementById('manualQuestionText').value = '';
        document.getElementById('manualOptionA').value = '';
        document.getElementById('manualOptionB').value = '';
        document.getElementById('manualOptionC').value = '';
        document.getElementById('manualOptionD').value = '';
        document.getElementById('manualExplanation').value = '';

        showIngestionAlert('Question created and added directly into examination paper!', 'success');

        if (window.MathJax && window.MathJax.typesetPromise) {
          MathJax.typesetPromise().catch(function() {});
        }
      } else {
        alert(data.message || 'Failed to add question.');
      }
    } catch (err) {
      alert('Error adding question to exam: ' + err.message);
    } finally {
      if (btn) btn.disabled = false;
    }
  }

  function showIngestionAlert(message, type) {
    const box = document.getElementById('ingestionAlert');
    if (!box) return;
    box.style.display = 'block';
    if (type === 'error') {
      box.style.background = 'rgba(239, 68, 68, 0.12)';
      box.style.border = '1px solid rgba(239, 68, 68, 0.35)';
      box.style.color = '#fca5a5';
      box.innerHTML = '<i class="fa-solid fa-circle-exclamation" style="margin-right: 6px;"></i> ' + escapeHtml(message);
    } else {
      box.style.background = 'rgba(16, 185, 129, 0.12)';
      box.style.border = '1px solid rgba(16, 185, 129, 0.35)';
      box.style.color = '#6ee7b7';
      box.innerHTML = '<i class="fa-solid fa-circle-check" style="margin-right: 6px;"></i> ' + escapeHtml(message);
    }
  }

  function hideIngestionAlert() {
    const box = document.getElementById('ingestionAlert');
    if (box) box.style.display = 'none';
  }

  function switchQTab(tab) {
    document.querySelectorAll('.q-tab-btn').forEach(b => {
      b.style.background = 'rgba(255,255,255,0.04)';
      b.style.color = '#94a3b8';
      b.style.borderColor = 'rgba(255,255,255,0.08)';
      b.style.boxShadow = 'none';
      b.classList.remove('active');
    });
    document.querySelectorAll('.q-tab-panel').forEach(p => p.style.display = 'none');

    const activeBtn = document.getElementById('tab-btn-' + tab);
    const activePanel = document.getElementById('tab-panel-' + tab);
    if (activeBtn && activePanel) {
      activeBtn.classList.add('active');
      activePanel.style.display = 'block';
      if (tab === 'bulk') {
        activeBtn.style.background = 'rgba(168, 85, 247, 0.15)';
        activeBtn.style.color = '#c084fc';
        activeBtn.style.borderColor = 'rgba(168, 85, 247, 0.4)';
        activeBtn.style.boxShadow = '0 4px 12px rgba(168, 85, 247, 0.15)';
      } else {
        activeBtn.style.background = 'rgba(59, 130, 246, 0.15)';
        activeBtn.style.color = '#60a5fa';
        activeBtn.style.borderColor = 'rgba(59, 130, 246, 0.4)';
        activeBtn.style.boxShadow = '0 4px 12px rgba(59, 130, 246, 0.15)';
      }
    }
  }

  function insertSampleBulkEquations() {
    const sample = `**1. Characteristic Polynomial and Matrix Trace**
Let $A \\in \\mathbb{C}^{3 \\times 3}$ have characteristic polynomial:
$$p(\\lambda) = \\det(\\lambda I - A) = \\lambda^3 - 3\\lambda^2 + 4$$
What is the trace of the inverse matrix, $\\operatorname{tr}(A^{-1})$?
* **A)** $0$
* **B)** $\\frac{3}{4}$
* **C)** $-1$
* **D)** $1$
Ans: B
Explanation: By Cayley-Hamilton and Newton sums, $\\operatorname{tr}(A^{-1}) = \\frac{3}{4}$.

---

2. Solve the definite integral: $\\int_0^x 2t \\, dt = 16$
A) $x = 2$
B) $x = 4$
C) $x = 8$
D) $x = \\pm 4$
Ans: B
Explanation: $\\int_0^x 2t \\, dt = [t^2]_0^x = x^2 = 16 \\implies x = 4$.

---

3. A particle moves with velocity $\\vec{v}(t) = 3t^2 \\hat{i} + 2t \\hat{j}$. What is acceleration at $t = 2 \\, \\text{s}$?
[Diagram: Particle trajectory in xy-plane showing velocity vector tangent to curve at t=2s]
A) $12\\hat{i} + 2\\hat{j} \\, \\text{m/s}^2$
B) $6\\hat{i} + 2\\hat{j} \\, \\text{m/s}^2$
C) $14 \\, \\text{m/s}^2$
D) None of the above
Ans: A`;
    document.getElementById('bulkQuestionInput').value = sample;
  }

  async function transformBulkQuestions() {
    hideIngestionAlert();
    const textVal = document.getElementById('bulkQuestionInput').value.trim();

    if (!textVal) {
      showIngestionAlert('Please paste your questions or equations into the Bulk Upload box.', 'error');
      return;
    }

    const btn = document.getElementById('btnTransformBulk');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Parsing Equations & Setting Paper...';

    const formData = new FormData();
    formData.append('raw_text', textVal);

    try {
      const res = await fetch("{{ route('admin.exams.scan_questions_global') }}", {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: formData
      });
      const data = await res.json();
      btn.disabled = false;
      btn.innerHTML = originalText;

      if (!data.success) {
        showIngestionAlert(data.message || 'Failed to parse questions.', 'error');
        return;
      }

      const incoming = data.questions || [];
      if (incoming.length === 0) {
        showIngestionAlert('No MCQ questions recognized in the input text.', 'error');
        return;
      }

      newLoadedQuestions = newLoadedQuestions.concat(incoming);
      renderNewQuestionsList();
      showIngestionAlert('Successfully transformed and staged ' + incoming.length + ' questions into the Question Paper Set!', 'success');
      
      const container = document.getElementById('newQuestionsPreviewContainer');
      if (container) container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    } catch(err) {
      btn.disabled = false;
      btn.innerHTML = originalText;
      showIngestionAlert('Error processing equations: ' + err.message, 'error');
    }
  }

  function updateSinglePreview() {
    const qText = document.getElementById('singleQuestionText').value.trim();
    const optA = document.getElementById('singleOptionA').value.trim();
    const optB = document.getElementById('singleOptionB').value.trim();
    const optC = document.getElementById('singleOptionC').value.trim();
    const optD = document.getElementById('singleOptionD').value.trim();
    const expl = document.getElementById('singleExplanation').value.trim();
    const previewBox = document.getElementById('singleLivePreviewBox');

    if (!qText && !optA && !optB && !optC && !optD) {
      previewBox.style.display = 'none';
      return;
    }

    previewBox.style.display = 'block';
    document.getElementById('singlePreviewStatement').innerHTML = renderMathSafe(qText || 'Question statement...');
    document.getElementById('singlePreviewOptA').innerHTML = renderMathSafe(optA || '—');
    document.getElementById('singlePreviewOptB').innerHTML = renderMathSafe(optB || '—');
    document.getElementById('singlePreviewOptC').innerHTML = renderMathSafe(optC || '—');
    document.getElementById('singlePreviewOptD').innerHTML = renderMathSafe(optD || '—');

    const explBox = document.getElementById('singlePreviewExplBox');
    if (expl) {
      explBox.style.display = 'block';
      document.getElementById('singlePreviewExpl').innerHTML = renderMathSafe(expl);
    } else {
      explBox.style.display = 'none';
    }

    if (window.typesetMathJax) {
      window.typesetMathJax(previewBox);
    } else if (window.MathJax && window.MathJax.typesetPromise) {
      try {
        if (window.MathJax.typesetClear) window.MathJax.typesetClear([previewBox]);
        MathJax.typesetPromise([previewBox]).catch(function() {});
      } catch(e) {}
    }
  }

  function addSingleManualQuestion() {
    hideIngestionAlert();
    const qText = document.getElementById('singleQuestionText').value.trim();
    const optA = document.getElementById('singleOptionA').value.trim();
    const optB = document.getElementById('singleOptionB').value.trim();
    const optC = document.getElementById('singleOptionC').value.trim();
    const optD = document.getElementById('singleOptionD').value.trim();
    const correct = document.getElementById('singleCorrectAnswer').value;
    const explanation = document.getElementById('singleExplanation').value.trim();

    if (!qText) {
      showIngestionAlert('Please provide the question statement.', 'error');
      return;
    }
    if (!optA || !optB) {
      showIngestionAlert('Please provide at least Option A and Option B.', 'error');
      return;
    }

    const newQ = {
      index: newLoadedQuestions.length + 1,
      question_text: qText,
      option_a: optA,
      option_b: optB,
      option_c: optC || 'None of the above',
      option_d: optD || 'All of the above',
      correct_answer: correct,
      explanation: explanation || null,
      is_valid: true,
      errors: []
    };

    newLoadedQuestions.push(newQ);
    renderNewQuestionsList();

    // Clear single form
    document.getElementById('singleQuestionText').value = '';
    document.getElementById('singleOptionA').value = '';
    document.getElementById('singleOptionB').value = '';
    document.getElementById('singleOptionC').value = '';
    document.getElementById('singleOptionD').value = '';
    document.getElementById('singleExplanation').value = '';
    document.getElementById('singleLivePreviewBox').style.display = 'none';

    showIngestionAlert('Question added to Question Paper Set successfully!', 'success');
  }

  function removeNewQuestion(index) {
    newLoadedQuestions.splice(index, 1);
    renderNewQuestionsList();
  }

  function clearAllNewQuestions() {
    if (newLoadedQuestions.length === 0) return;
    newLoadedQuestions = [];
    renderNewQuestionsList();
    showIngestionAlert('Cleared newly staged questions.', 'success');
  }

  function startEditingNewQuestion(idx) {
    if (!newLoadedQuestions[idx]) return;
    newLoadedQuestions[idx].is_editing = true;
    renderNewQuestionsList();
  }

  function cancelEditingNewQuestion(idx) {
    if (!newLoadedQuestions[idx]) return;
    delete newLoadedQuestions[idx].is_editing;
    renderNewQuestionsList();
  }

  function saveEditingNewQuestion(idx) {
    const q = newLoadedQuestions[idx];
    if (!q) return;

    const qText = (document.getElementById('edit-staged-text-' + idx)?.value || '').trim();
    const optA = (document.getElementById('edit-staged-opt-a-' + idx)?.value || '').trim();
    const optB = (document.getElementById('edit-staged-opt-b-' + idx)?.value || '').trim();
    const optC = (document.getElementById('edit-staged-opt-c-' + idx)?.value || '').trim();
    const optD = (document.getElementById('edit-staged-opt-d-' + idx)?.value || '').trim();
    const ans = document.getElementById('edit-staged-ans-' + idx)?.value || 'A';
    const expl = (document.getElementById('edit-staged-expl-' + idx)?.value || '').trim();

    if (!qText) {
      alert('Question text cannot be empty.');
      return;
    }
    if (!optA || !optB) {
      alert('Option A and Option B are required.');
      return;
    }

    q.question_text = qText;
    q.option_a = optA;
    q.option_b = optB;
    q.option_c = optC;
    q.option_d = optD;
    q.correct_answer = ans;
    q.explanation = expl || null;
    delete q.is_editing;

    renderNewQuestionsList();
    showIngestionAlert(`Staged question #${idx + 1} updated!`, 'success');
  }

  function renderNewQuestionsList() {
    const container = document.getElementById('newQuestionsPreviewContainer');
    const badge = document.getElementById('newQuestionsCountBadge');
    const jsonHidden = document.getElementById('questions_json');

    badge.textContent = newLoadedQuestions.length + ' New Questions';
    jsonHidden.value = newLoadedQuestions.length > 0 ? JSON.stringify(newLoadedQuestions) : '';

    if (newLoadedQuestions.length === 0) {
      container.innerHTML = `
        <div id="emptyNewQuestionsPlaceholder" style="text-align: center; padding: 32px 20px; color: #64748b; background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.08); border-radius: 10px;">
          <i class="fa-solid fa-cloud-arrow-up" style="font-size: 30px; color: #475569; margin-bottom: 8px; display: block;"></i>
          <div style="font-weight: 700; color: #94a3b8; font-size: 13.5px; margin-bottom: 4px;">No New Questions Staged</div>
          <div style="font-size: 12px;">Use the Bulk Upload or Single Upload options above to append additional questions to this exam.</div>
        </div>
      `;
      return;
    }

    let html = '';
    newLoadedQuestions.forEach((q, idx) => {
      const qText = q.question_text || q.question || '';
      const optA = q.option_a || (q.options && q.options[0] ? (q.options[0].text || q.options[0]) : '');
      const optB = q.option_b || (q.options && q.options[1] ? (q.options[1].text || q.options[1]) : '');
      const optC = q.option_c || (q.options && q.options[2] ? (q.options[2].text || q.options[2]) : '');
      const optD = q.option_d || (q.options && q.options[3] ? (q.options[3].text || q.options[3]) : '');
      const correct = (q.correct_answer || 'A').toUpperCase();
      const expl = q.explanation || '';
      const isEditing = q.is_editing === true;

      html += `
        <div class="q-preview-card" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 10px; padding: 14px 18px;">
          <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 8px;">
            <div style="display: flex; align-items: center; gap: 8px;">
              <span style="background: #f59e0b; color: #000000; font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 4px;">+New #${idx + 1}</span>
              <span style="background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.35); font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 4px;">
                <i class="fa-solid fa-check"></i> Ans: ${escapeHtml(correct)}
              </span>
            </div>
            <div style="display: flex; align-items: center; gap: 6px;">
              ${!isEditing ? `
                <button type="button" onclick="startEditingNewQuestion(${idx})" style="background: rgba(59, 130, 246, 0.12); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.25); font-size: 12px; font-weight: 700; cursor: pointer; padding: 4px 8px; border-radius: 4px; display: inline-flex; align-items: center; gap: 5px;">
                  <i class="fa-solid fa-pen-to-square"></i> Edit
                </button>
              ` : ''}
              <button type="button" onclick="removeNewQuestion(${idx})" style="background: none; border: none; color: #ef4444; font-size: 13px; cursor: pointer; padding: 4px 8px; border-radius: 4px; display: inline-flex; align-items: center; gap: 5px;" title="Remove this question">
                <i class="fa-solid fa-trash-can"></i> Remove
              </button>
            </div>
          </div>

          <!-- NORMAL DISPLAY VIEW -->
          <div id="staged-q-view-${idx}" style="${isEditing ? 'display: none;' : 'display: block;'}">
            <div style="color: #f8fafc; font-size: 14px; font-weight: 500; line-height: 1.75; letter-spacing: 0.01em; margin-bottom: 14px;">
              ${renderMathSafe(qText)}
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 13.5px;">
              <div style="padding: 10px 14px; min-height: 46px; border-radius: 8px; display: flex; align-items: center; gap: 9px; ${correct === 'A' ? 'background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #34d399; font-weight: 700;' : 'background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); color: #cbd5e1;'}">
                <strong style="color: ${correct === 'A' ? '#34d399' : '#60a5fa'}; font-size: 13px;">A)</strong> <div style="display: inline-block; overflow-x: auto;">${renderMathSafe(optA)}</div>
              </div>
              <div style="padding: 10px 14px; min-height: 46px; border-radius: 8px; display: flex; align-items: center; gap: 9px; ${correct === 'B' ? 'background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #34d399; font-weight: 700;' : 'background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); color: #cbd5e1;'}">
                <strong style="color: ${correct === 'B' ? '#34d399' : '#60a5fa'}; font-size: 13px;">B)</strong> <div style="display: inline-block; overflow-x: auto;">${renderMathSafe(optB)}</div>
              </div>
              <div style="padding: 10px 14px; min-height: 46px; border-radius: 8px; display: flex; align-items: center; gap: 9px; ${correct === 'C' ? 'background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #34d399; font-weight: 700;' : 'background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); color: #cbd5e1;'}">
                <strong style="color: ${correct === 'C' ? '#34d399' : '#60a5fa'}; font-size: 13px;">C)</strong> <div style="display: inline-block; overflow-x: auto;">${renderMathSafe(optC)}</div>
              </div>
              <div style="padding: 10px 14px; min-height: 46px; border-radius: 8px; display: flex; align-items: center; gap: 9px; ${correct === 'D' ? 'background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #34d399; font-weight: 700;' : 'background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); color: #cbd5e1;'}">
                <strong style="color: ${correct === 'D' ? '#34d399' : '#60a5fa'}; font-size: 13px;">D)</strong> <div style="display: inline-block; overflow-x: auto;">${renderMathSafe(optD)}</div>
              </div>
            </div>

            ${expl ? `
              <div style="margin-top: 10px; font-size: 11.5px; color: #94a3b8; background: rgba(255,255,255,0.02); border-left: 2px solid #3b82f6; padding: 6px 10px; border-radius: 0 4px 4px 0;">
                <strong style="color: #60a5fa;">Explanation:</strong> ${renderMathSafe(expl)}
              </div>
            ` : ''}
          </div>

          <!-- INLINE EDITING FORM FOR STAGED QUESTION -->
          <div id="staged-q-edit-box-${idx}" style="${isEditing ? 'display: block;' : 'display: none;'} background: rgba(0,0,0,0.28); border: 1px solid rgba(245, 158, 11, 0.35); border-radius: 8px; padding: 14px; margin-top: 6px;">
            <div style="margin-bottom: 10px;">
              <label class="ida-label" style="font-size: 11px;">Edit Question Text *</label>
              <textarea id="edit-staged-text-${idx}" rows="2" class="form-tactical" style="width: 100%; padding: 8px 12px; font-size: 13px;">${escapeHtml(qText)}</textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">
              <div>
                <label class="ida-label" style="font-size: 11px;">Option A *</label>
                <input type="text" id="edit-staged-opt-a-${idx}" value="${escapeHtml(optA)}" class="form-tactical" style="width: 100%; padding: 7px 10px; font-size: 12.5px;">
              </div>
              <div>
                <label class="ida-label" style="font-size: 11px;">Option B *</label>
                <input type="text" id="edit-staged-opt-b-${idx}" value="${escapeHtml(optB)}" class="form-tactical" style="width: 100%; padding: 7px 10px; font-size: 12.5px;">
              </div>
              <div>
                <label class="ida-label" style="font-size: 11px;">Option C *</label>
                <input type="text" id="edit-staged-opt-c-${idx}" value="${escapeHtml(optC)}" class="form-tactical" style="width: 100%; padding: 7px 10px; font-size: 12.5px;">
              </div>
              <div>
                <label class="ida-label" style="font-size: 11px;">Option D *</label>
                <input type="text" id="edit-staged-opt-d-${idx}" value="${escapeHtml(optD)}" class="form-tactical" style="width: 100%; padding: 7px 10px; font-size: 12.5px;">
              </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 10px; margin-bottom: 12px;">
              <div>
                <label class="ida-label" style="font-size: 11px;">Correct Answer Key *</label>
                <select id="edit-staged-ans-${idx}" class="form-tactical" style="width: 100%; padding: 7px 10px; font-size: 12.5px; font-weight: 700;">
                  <option value="A" ${correct === 'A' ? 'selected' : ''}>Option A</option>
                  <option value="B" ${correct === 'B' ? 'selected' : ''}>Option B</option>
                  <option value="C" ${correct === 'C' ? 'selected' : ''}>Option C</option>
                  <option value="D" ${correct === 'D' ? 'selected' : ''}>Option D</option>
                </select>
              </div>
              <div>
                <label class="ida-label" style="font-size: 11px;">Explanation (Optional)</label>
                <input type="text" id="edit-staged-expl-${idx}" value="${escapeHtml(expl)}" class="form-tactical" style="width: 100%; padding: 7px 10px; font-size: 12.5px;">
              </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 8px;">
              <button type="button" onclick="cancelEditingNewQuestion(${idx})" style="background: rgba(255,255,255,0.06); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.12); padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer;">
                Cancel
              </button>
              <button type="button" onclick="saveEditingNewQuestion(${idx})" style="background: #10b981; color: #ffffff; border: none; padding: 6px 16px; border-radius: 6px; font-size: 12px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; gap: 5px;">
                <i class="fa-solid fa-check"></i> Save Changes
              </button>
            </div>
          </div>
        </div>
      `;
    });

    container.innerHTML = html;

    if (window.typesetMathJax) {
      window.typesetMathJax(container);
    } else if (window.MathJax && window.MathJax.typesetPromise) {
      try {
        if (window.MathJax.typesetClear) window.MathJax.typesetClear([container]);
        MathJax.typesetPromise([container]).catch(function() {});
      } catch(e) {}
    }
  }

  /**
   * Render text with LaTeX math preserved for MathJax rendering.
   * Extracts LaTeX regions ($...$, $$...$$, \(...\), \[...\]), escapes
   * surrounding text for XSS safety, then re-inserts LaTeX unescaped.
   * Use ONLY for innerHTML display — never for input/textarea values.
   */
  function renderMathSafe(str) {
    if (!str) return '';
    str = String(str);

    // 0. Auto-synthesize bracket matrices [[1, 2], [3, 4]] into KaTeX/MathJax \begin{pmatrix}
    str = str.replace(/(?:\$)?\[\s*(\[\s*[^\[\]]+?\s*\](?:\s*,\s*\[\s*[^\[\]]+?\s*\])*)\s*\](?:\$)?/g, function(m, inner) {
      const rowRegex = /\[\s*([^\[\]]+?)\s*\]/g;
      const rows = [];
      let rowMatch;
      while ((rowMatch = rowRegex.exec(inner)) !== null) {
        const elements = rowMatch[1].split(',').map(function(e) { return e.trim(); });
        rows.push(elements.join(' & '));
      }
      if (rows.length > 0) {
        return '$\\begin{pmatrix} ' + rows.join(' \\\\ ') + ' \\end{pmatrix}$';
      }
      return m;
    });

    const placeholders = [];
    let idx = 0;

    // Helper to modernize operator spacing: \det( -> \det\,(    \operatorname{tr}( -> \operatorname{tr}\,(
    function modernizeMathOperators(mathText) {
      return mathText.replace(/\\(det|operatorname\{[^{}]+\}|dim|ker|deg|gcd|max|min|sup|inf|lim|limsup|liminf|ln|lg|log|exp|sin|cos|tan|sec|csc|cot|sinh|cosh|tanh)\s*(\(|\[|\\\{)/g, function(m, op, delim) {
        return '\\' + op + '\\,' + delim;
      });
    }

    // 1. MASK ALL VALID MATH DELIMITERS FIRST TO PROTECT FORMULAS FROM CORRUPTION!
    // 1.1 $$...$$ (display math)
    str = str.replace(/\$\$([\s\S]+?)\$\$/g, function(match) {
      const ph = '__MATHPH_' + (idx++) + '__';
      placeholders.push({ ph: ph, val: modernizeMathOperators(match) });
      return ph;
    });
    // 1.2 \[...\] (display math)
    str = str.replace(/\\\[([\s\S]+?)\\\]/g, function(match) {
      const ph = '__MATHPH_' + (idx++) + '__';
      placeholders.push({ ph: ph, val: modernizeMathOperators(match) });
      return ph;
    });
    // 1.3 \(...\) (inline math)
    str = str.replace(/\\\(([\s\S]+?)\\\)/g, function(match) {
      const ph = '__MATHPH_' + (idx++) + '__';
      placeholders.push({ ph: ph, val: modernizeMathOperators(match) });
      return ph;
    });
    // 1.4 $...$ (inline math)
    str = str.replace(/\$([^\$]+?)\$/g, function(match) {
      const ph = '__MATHPH_' + (idx++) + '__';
      placeholders.push({ ph: ph, val: modernizeMathOperators(match) });
      return ph;
    });
    // 1.5 Standalone \begin{...}...\end{...} environments
    str = str.replace(/\\begin\{([a-zA-Z*]+)\}[\s\S]*?\\end\{\1\}/g, function(match) {
      const ph = '__MATHPH_' + (idx++) + '__';
      placeholders.push({ ph: ph, val: '$$' + modernizeMathOperators(match) + '$$' });
      return ph;
    });
    // 1.6 [Diagram: ...] specification blocks
    str = str.replace(/\[Diagram:\s*([^\]]+)\]/gi, function(match, desc) {
      const ph = '__MATHPH_' + (idx++) + '__';
      const diagramHtml = `<div class="stem-diagram-callout" style="display: flex; align-items: flex-start; gap: 9px; margin: 8px 0; background: rgba(59, 130, 246, 0.1); border: 1px dashed rgba(59, 130, 246, 0.4); border-radius: 8px; padding: 9px 12px; color: #93c5fd; font-size: 12px; line-height: 1.5;"><i class="fa-solid fa-bezier-curve" style="color: #60a5fa; margin-top: 3px; font-size: 13px;"></i><div><strong style="color: #bfdbfe;">[Diagram Specification]:</strong> ${escapeHtml(desc)}</div></div>`;
      placeholders.push({ ph: ph, val: diagramHtml });
      return ph;
    });

    // 2. Map raw Unicode Greek letters & symbols in non-math text to LaTeX math expressions
    const unicodeMap = {
      'α': '$\\alpha$', 'β': '$\\beta$', 'γ': '$\\gamma$', 'δ': '$\\delta$',
      'ε': '$\\epsilon$', 'ζ': '$\\zeta$', 'η': '$\\eta$', 'θ': '$\\theta$',
      'ι': '$\\iota$', 'κ': '$\\kappa$', 'λ': '$\\lambda$', 'μ': '$\\mu$',
      'ν': '$\\nu$', 'ξ': '$\\xi$', 'π': '$\\pi$', 'ρ': '$\\rho$',
      'σ': '$\\sigma$', 'τ': '$\\tau$', 'υ': '$\\upsilon$', 'φ': '$\\phi$',
      'χ': '$\\chi$', 'ψ': '$\\psi$', 'ω': '$\\omega$',
      'Γ': '$\\Gamma$', 'Δ': '$\\Delta$', 'Θ': '$\\Theta$', 'Λ': '$\\Lambda$',
      'Ξ': '$\\Xi$', 'Π': '$\\Pi$', 'Σ': '$\\Sigma$', 'Φ': '$\\Phi$',
      'Ψ': '$\\Psi$', 'Ω': '$\\Omega$',
      '∞': '$\\infty$', '≠': '$\\ne$', '≤': '$\\le$', '≥': '$\\ge$',
      '±': '$\\pm$', '×': '$\\times$', '÷': '$\\div$', '·': '$\\cdot$'
    };
    for (const [char, latex] of Object.entries(unicodeMap)) {
      str = str.split(char).join(latex);
    }

    // 3. Wrap bare LaTeX commands OUTSIDE math mode in $...$
    const greekOrSymbols = 'lambda|alpha|beta|gamma|delta|epsilon|zeta|eta|theta|iota|kappa|mu|nu|xi|pi|rho|sigma|tau|upsilon|phi|chi|psi|omega|Delta|Gamma|Theta|Lambda|Xi|Pi|Sigma|Phi|Psi|Omega|nabla|partial|infty|approx|ne|leq|geq|times|div|pm|mp|cdot|int|oint|sum|prod|cup|cap|subset|subseteq|in|notin|forall|exists';
    
    // Complex commands with arguments
    str = str.replace(/(?<![\$\\])\\(frac\{[^{}]+\}\{[^{}]+\}|sqrt(?:\[[^\]]+\])?\{[^{}]+\}|vec\{[^{}]+\}|hat\{[^{}]+\}|dot\{[^{}]+\}|overline\{[^{}]+\}|mathbf\{[^{}]+\})(?!\$)/g, function(m) {
      return '$' + m + '$';
    });

    // Greek letters & math symbols with optional subscripts/superscripts
    try {
      const symRegex = new RegExp('(?<![\\$\\\])\\\\(' + greekOrSymbols + ')(?:[_^](?:\\{[^{}]+\\}|[a-zA-Z0-9]+))*(?![a-zA-Z])(?!\\$)', 'g');
      str = str.replace(symRegex, function(m) {
        return '$' + m + '$';
      });
    } catch(e) {}

    // Strip any raw or rogue HTML tags (like <strong>, <b>, <p>) from input text
    str = str.replace(/<\/?(strong|b|p|div|span)[^>]*>/gi, '');

    // 4. Escape the remaining non-LaTeX text for safe HTML display
    str = str
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');

    // 5. AFTER escaping, safely format markdown bold (**text**) without exposing raw HTML tags
    str = str.replace(/\*\*([^\*]+?)\*\*/g, function(match, inner) {
      return '<span style="font-weight: 700; color: #ffffff;">' + inner + '</span>';
    });

    // 6. Convert newlines into clean paragraph & line breaks so titles and equations breathe
    str = str.replace(/\r\n/g, '\n').replace(/\r/g, '\n');
    str = str.replace(/\n\s*\n/g, '<div style="margin-bottom: 10px;"></div>');
    str = str.replace(/\n/g, '<br>');

    // 7. Re-insert LaTeX regions unescaped using a function callback to avoid JS $$ / $& pattern interpretation!
    for (const p of placeholders) {
      str = str.replace(p.ph, function() { return p.val; });
    }

    return str;
  }

  function escapeHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  const branchTracks = {
    army: [
      { value: 'prelim', label: 'Army Preliminary Examination (Army Exclusive)', hint: '🪖 Exclusive: Only cadets enrolled in Bangladesh Army course can conduct this prelim exam.', badge: 'Army Prelim' },
      { value: 'issb', label: 'ISSB Special Masterclass (Universal Tri-Services)', hint: '🌟 Tri-Services Clearance: Every student with an ISSB course or ANY military course (Army, Navy, or Air Force) can conduct this exam!', badge: 'ISSB Universal' },
      { value: 'general', label: 'General Program', hint: 'Open to enrolled Bangladesh Army candidates.', badge: 'Army General' }
    ],
    navy: [
      { value: 'prelim', label: 'Navy Preliminary Examination (Navy Exclusive)', hint: '⚓ Exclusive: Only cadets enrolled in Bangladesh Navy course can conduct this prelim exam.', badge: 'Navy Prelim' },
      { value: 'issb', label: 'ISSB Special Masterclass (Universal Tri-Services)', hint: '🌟 Tri-Services Clearance: Every student with an ISSB course or ANY military course (Army, Navy, or Air Force) can conduct this exam!', badge: 'ISSB Universal' },
      { value: 'general', label: 'General Program', hint: 'Open to enrolled Bangladesh Navy candidates.', badge: 'Navy General' }
    ],
    air_force: [
      { value: 'prelim', label: 'Air Force Preliminary Examination (Air Force Exclusive)', hint: '✈️ Exclusive: Only cadets enrolled in Bangladesh Air Force course can conduct this prelim exam.', badge: 'Air Force Prelim' },
      { value: 'issb', label: 'ISSB Special Masterclass (Universal Tri-Services)', hint: '🌟 Tri-Services Clearance: Every student with an ISSB course or ANY military course (Army, Navy, or Air Force) can conduct this exam!', badge: 'ISSB Universal' },
      { value: 'general', label: 'General Program', hint: 'Open to enrolled Bangladesh Air Force candidates.', badge: 'Air Force General' }
    ],
    police: [
      { value: 'constable', label: 'Police Constable / Preliminary Recruitment', hint: '👮 Exclusive: Open to cadets enrolled in the Police Constable track.', badge: 'Constable' },
      { value: 'si', label: 'Police Sub-Inspector (SI) Track', hint: '🔍 Exclusive: Open to cadets enrolled in the Sub-Inspector (SI) track.', badge: 'Sub-Inspector' },
      { value: 'asi', label: 'Police Assistant Sub-Inspector (ASI) Track', hint: '🛡️ Exclusive: Open to cadets enrolled in the Assistant Sub-Inspector (ASI) track.', badge: 'Assistant SI' },
      { value: 'general', label: 'General Police Program', hint: 'Open to all enrolled Bangladesh Police candidates.', badge: 'Police General' }
    ]
  };

  function selectFullTrackPreset(branch, track, suggestedCategory = '') {
    const branchEl = document.getElementById('examBranchSelect');
    if (branchEl) {
      branchEl.value = branch;
      updateCategoryPills();
      updateTargetTracks(track);
    }
    const catInput = document.getElementById('examCategoryInput');
    if (catInput && suggestedCategory) {
      catInput.value = suggestedCategory;
    }
  }

  const branchPills = {
    army: [
      { name: 'Preliminary IQ', track: 'prelim' },
      { name: 'ISSB Special', track: 'issb' },
      { name: 'Verbal IQ', track: 'prelim' },
      { name: 'Psychological', track: 'issb' }
    ],
    navy: [
      { name: 'Preliminary IQ', track: 'prelim' },
      { name: 'ISSB Special', track: 'issb' },
      { name: 'Naval Aptitude', track: 'prelim' },
      { name: 'Psychological', track: 'issb' }
    ],
    air_force: [
      { name: 'Preliminary IQ', track: 'prelim' },
      { name: 'ISSB Special', track: 'issb' },
      { name: 'Aviation Aptitude', track: 'prelim' },
      { name: 'Psychological', track: 'issb' }
    ],
    police: [
      { name: 'Constable Recruitment', track: 'constable' },
      { name: 'Sub-Inspector (SI)', track: 'si' },
      { name: 'Assistant Sub-Inspector (ASI)', track: 'asi' },
      { name: 'General Aptitude', track: 'general' }
    ]
  };

  function updateCategoryPills() {
    const branchEl = document.getElementById('examBranchSelect');
    const container = document.getElementById('categoryQuickPills');
    if (!branchEl || !container) return;

    const branch = branchEl.value;
    const pills = branchPills[branch] || branchPills.army;
    container.innerHTML = pills.map(p => `
      <button type="button" onclick="setExamCategory('${p.name}', '${p.track}')" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); color: #cbd5e1; border-radius: 6px; padding: 2px 8px; font-size: 11px; font-weight: 600; cursor: pointer; transition: all 0.15s;" onmouseover="this.style.borderColor='#ff5757'; this.style.color='#ff5757'" onmouseout="this.style.borderColor='rgba(255,255,255,0.12)'; this.style.color='#cbd5e1'">
        + ${p.name}
      </button>
    `).join('');
  }

  function updateTargetTracks(preselectedValue = null) {
    const branchEl = document.getElementById('examBranchSelect');
    const trackEl = document.getElementById('examTargetTrackSelect');
    const hintEl = document.getElementById('trackEligibilityHint');
    const badgeEl = document.getElementById('trackRuleBadge');
    if (!branchEl || !trackEl) return;

    const branch = branchEl.value;
    const tracks = branchTracks[branch] || branchTracks.army;
    const currentVal = preselectedValue || trackEl.value || (branch === 'police' ? 'si' : 'prelim');

    trackEl.innerHTML = tracks.map(t => `
      <option value="${t.value}" ${t.value === currentVal ? 'selected' : ''}>${t.label}</option>
    `).join('');

    function updateHint() {
      const selectedTrack = trackEl.value;
      const matched = tracks.find(t => t.value === selectedTrack) || tracks[0];
      if (hintEl) hintEl.textContent = matched.hint;
      if (badgeEl) {
        badgeEl.textContent = matched.badge;
        if (matched.value === 'issb') {
          badgeEl.style.background = 'rgba(234, 179, 8, 0.2)';
          badgeEl.style.color = '#facc15';
        } else if (matched.value === 'prelim') {
          badgeEl.style.background = 'rgba(59, 130, 246, 0.2)';
          badgeEl.style.color = '#60a5fa';
        } else if (['constable', 'si', 'asi'].includes(matched.value)) {
          badgeEl.style.background = 'rgba(168, 85, 247, 0.2)';
          badgeEl.style.color = '#c084fc';
        } else {
          badgeEl.style.background = 'rgba(255, 87, 87, 0.15)';
          badgeEl.style.color = '#ff8585';
        }
      }
    }

    trackEl.onchange = updateHint;
    updateHint();
  }

  function setExamCategory(val, track = null) {
    const input = document.getElementById('examCategoryInput');
    if (input) input.value = val;

    if (track) {
      const trackEl = document.getElementById('examTargetTrackSelect');
      if (trackEl) {
        trackEl.value = track;
        trackEl.dispatchEvent(new Event('change'));
      }
    }
  }

  document.addEventListener('DOMContentLoaded', function() {
    if (window.typesetMathJax) {
      window.typesetMathJax();
    } else if (window.MathJax && window.MathJax.typesetPromise) {
      MathJax.typesetPromise().catch(function() {});
    }

    const branchEl = document.getElementById('examBranchSelect');
    if (branchEl) {
      branchEl.addEventListener('change', function() {
        updateCategoryPills();
        updateTargetTracks();
      });
      updateCategoryPills();
      updateTargetTracks("{{ old('target_track', $exam->target_track) }}");
    }
  });
</script>

<style>
  .ida-label {
    display: block;
    font-size: 11.5px;
    font-weight: 800;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    margin-bottom: 7px;
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
  .q-card:hover, .q-preview-card:hover {
    border-color: rgba(255, 255, 255, 0.18) !important;
  }
</style>
@endsection