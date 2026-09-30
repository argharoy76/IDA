@extends('layouts.portal')

@section('title', 'Create New Assessment Module')
@section('page_title', 'Create New Assessment Module')
@section('page_subtitle', 'Configure full examination parameters, access criteria, scoring rules, and scheduled timing')

@section('topbar_actions')
  <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
    <a href="{{ route('admin.exam_management.index') }}" class="btn-tactical" style="background: rgba(255,255,255,0.06); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.12); padding: 8px 16px; font-size: 13px; font-weight: 700; text-decoration: none; border-radius: 8px; display: inline-flex; align-items: center; gap: 7px;">
      <i class="fa-solid fa-arrow-left"></i> Back to Exam Management
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
      <span style="color: #ff5757; font-weight: 700;">Create New Assessment Module</span>
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

  <form action="{{ route('admin.exam_management.store') }}" method="POST" id="createExamForm" enctype="multipart/form-data">
    @csrf

    <!-- SECTION 1: ASSESSMENT IDENTITY & ACCESS CONTROL -->
    <div class="content-panel" style="background: #181c26; border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 16px; padding: 26px 28px; margin-bottom: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.25); position: relative; z-index: 40;">
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
          <input type="text" name="title" value="{{ old('title') }}" required placeholder="e.g. 2026 ISSB Verbal Intelligence Assessment" class="form-tactical" style="width: 100%; font-size: 14.5px; font-weight: 700; padding: 11px 16px;">
        </div>

        {{-- Cascading Dropdown Hierarchy for Assessment Identity & Access Control --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 16px; align-items: start;">
          {{-- Dropdown 1: Branch Wing --}}
          <div>
            <label class="ida-label">1. Branch Wing *</label>
            <select name="branch" id="examBranchSelect" class="form-tactical ida-cascading-select ida-track-select" required style="width: 100%; padding: 11px 16px;">
              <option value="navy" {{ old('branch', $prefillBranch) === 'navy' ? 'selected' : '' }}>Bangladesh Navy</option>
              <option value="police" {{ old('branch', $prefillBranch) === 'police' ? 'selected' : '' }}>Bangladesh Police</option>
              <option value="army" {{ old('branch', $prefillBranch) === 'army' ? 'selected' : '' }}>Bangladesh Army</option>
              <option value="air_force" {{ old('branch', $prefillBranch) === 'air_force' ? 'selected' : '' }}>Bangladesh Air Force</option>
            </select>
          </div>

          {{-- Dropdown 2: Post (Officer Cadet vs Navy Sailor / Soldier Cadet / Airman Cadet) --}}
          <div id="cadreSelectContainer" style="display: block;">
            <label class="ida-label" id="cadreSelectLabel">2. Post *</label>
            <select id="examCadreSelect" class="form-tactical ida-cascading-select ida-track-select" style="width: 100%; padding: 11px 16px;">
              <option value="officer">Officer Cadet</option>
              <option value="soldier" id="soldierCadreOption">Navy Sailor</option>
            </select>
          </div>

          {{-- Dropdown 3: Exam Type (Preliminary vs ISSB for Officer; Hidden for Soldier) --}}
          <div id="examTypeContainer" style="display: block;">
            <label class="ida-label" id="trackSelectLabel">3. Exam Type *</label>
            <select id="examTargetTrackSelect" class="form-tactical ida-cascading-select ida-track-select" style="width: 100%; padding: 11px 16px;">
              <option value="prelim" {{ old('target_track', 'prelim') === 'prelim' ? 'selected' : '' }}>Preliminary</option>
              <option value="issb" {{ old('target_track') === 'issb' ? 'selected' : '' }}>ISSB</option>
            </select>
            <input type="hidden" name="target_track" id="finalTargetTrackInput" value="{{ old('target_track', 'prelim') }}">
          </div>

          {{-- Dropdown 4: Access Type --}}
          <div>
            <label class="ida-label" id="accessTypeLabel">4. Access Type *</label>
            <select name="access_type" class="form-tactical ida-cascading-select ida-track-select" required style="width: 100%; padding: 11px 16px;">
              <option value="free" {{ old('access_type', $prefillType) === 'free' ? 'selected' : '' }}>Free Exam</option>
              <option value="paid" {{ old('access_type', $prefillType) === 'paid' ? 'selected' : '' }}>Cadet Exam</option>
              <option value="both" {{ old('access_type', $prefillType) === 'both' ? 'selected' : '' }}>Both</option>
            </select>
          </div>
        </div>

        {{-- Hidden Category Input automatically updated from selected track & stage --}}
        <input type="hidden" name="category" id="examCategoryInput" value="{{ old('category', 'Preliminary Examination') }}">
      </div>
    </div>

    <!-- SECTION 2: ENGINE CONFIGURATION & SCORING RULES -->
    <div class="content-panel" style="background: #181c26; border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 16px; padding: 26px 28px; margin-bottom: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.25); position: relative; z-index: 30;">
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
            <option value="iq_mcq" {{ old('exam_type') === 'iq_mcq' ? 'selected' : '' }}>Intelligence MCQ (Timed Palette & Questions)</option>
            <option value="word_association" {{ old('exam_type') === 'word_association' ? 'selected' : '' }}>Word Association Test (WAT 15s Flash)</option>
            <option value="non_verbal_iq" {{ old('exam_type') === 'non_verbal_iq' ? 'selected' : '' }}>Non-Verbal Matrix IQ (Visual Analysis)</option>
            <option value="general_aptitude" {{ old('exam_type') === 'general_aptitude' ? 'selected' : '' }}>General Academic Aptitude</option>
          </select>
        </div>

        {{-- Duration, Total Marks, Pass Marks, Negative Marking --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px;">
          <div>
            <label class="ida-label">Duration (Minutes) *</label>
            <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 30) }}" min="1" max="300" class="form-tactical" required style="width: 100%; padding: 11px 16px;">
            <small style="color: #64748b; font-size: 11.5px; margin-top: 4px; display: block;">Total test time limit in minutes.</small>
          </div>

          <div>
            <label class="ida-label">Total Marks *</label>
            <input type="number" step="0.5" name="total_marks" value="{{ old('total_marks', 50) }}" min="1" class="form-tactical" required style="width: 100%; padding: 11px 16px;">
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
            <input type="number" step="0.5" name="pass_marks" id="pass_marks" value="{{ old('pass_marks', 25) }}" min="0" class="form-tactical" required style="width: 100%; padding: 11px 16px; border-color: rgba(16, 185, 129, 0.5);">
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
            <input type="number" step="0.05" name="negative_marking_per_wrong" value="{{ old('negative_marking_per_wrong', 0.25) }}" min="0" class="form-tactical" placeholder="0.25" style="width: 100%; padding: 11px 16px;">
            <small style="color: #64748b; font-size: 11.5px; margin-top: 4px; display: block;">Marks deducted per wrong answer.</small>
          </div>
        </div>
      </div>
    </div>

    <!-- SECTION 3: SCHEDULING & BRIEF DESCRIPTION -->
    <div class="content-panel" style="background: #181c26; border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 16px; padding: 26px 28px; margin-bottom: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.25); position: relative; z-index: 20;">
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
              <option value="open" {{ old('status') === 'open' ? 'selected' : '' }}>🟢 Live Now (Open Access)</option>
              <option value="scheduled" {{ old('status') === 'scheduled' ? 'selected' : '' }}>🕐 Scheduled Countdown</option>
              <option value="closed" {{ old('status') === 'closed' ? 'selected' : '' }}>🔒 Ended (Closed)</option>
              <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>📝 Draft (Hidden)</option>
            </select>
            <small style="color: #64748b; font-size: 11.5px; margin-top: 4px; display: block;">Live opens immediately; Scheduled enforces countdown.</small>
          </div>

          <div>
            <label class="ida-label">Schedule Start Date & Time</label>
            <input type="datetime-local" name="schedule_start" value="{{ old('schedule_start') }}" class="form-tactical" style="width: 100%; padding: 11px 16px;">
            <small style="color: #64748b; font-size: 11.5px; margin-top: 4px; display: block;">Starting date & time (required for scheduled countdowns).</small>
          </div>

          <div>
            <label class="ida-label">Schedule End Date & Time</label>
            <input type="datetime-local" name="schedule_end" value="{{ old('schedule_end') }}" class="form-tactical" style="width: 100%; padding: 11px 16px;">
            <small style="color: #64748b; font-size: 11.5px; margin-top: 4px; display: block;">Optional closing date & time after which test closes.</small>
          </div>
        </div>

        {{-- Brief Description --}}
        <div>
          <label class="ida-label">Brief Description</label>
          <textarea name="description" rows="3" class="form-tactical" placeholder="Summary of topics, verbal IQ sections, or instructions..." style="width: 100%; padding: 12px 16px; line-height: 1.6;">{{ old('description') }}</textarea>
          <small style="color: #64748b; font-size: 11.5px; margin-top: 4px; display: block;">Concise overview of 80 to 100 letters displayed directly on candidate exam cards.</small>
        </div>
      </div>
    </div>

    <!-- SECTION 4: QUESTION PAPER -->
    <div id="section-question-ingestion" class="content-panel" style="background: #181c26; border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 16px; padding: 26px 28px; margin-bottom: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.25); position: relative; z-index: 10;">
      <div style="border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 16px; margin-bottom: 22px;">
        <h3 style="font-size: 18px; font-weight: 800; margin: 0; color: #ffffff; display: flex; align-items: center; gap: 10px;">
          <span style="width: 34px; height: 34px; border-radius: 9px; background: rgba(245, 158, 11, 0.15); color: #fbbf24; display: grid; place-items: center; font-size: 15px;">
            <i class="fa-solid fa-file-circle-question"></i>
          </span>
          Question Paper
        </h3>
      </div>

      <!-- TWO OPTIONS: BULK UPLOAD & SINGLE UPLOAD -->
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

      <!-- QUESTION POOL LIVE PREVIEW -->
      <div style="background: #11141d; border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 20px 22px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 10px;">
          <div style="display: flex; align-items: center; gap: 10px;">
            <h4 style="font-size: 14.5px; font-weight: 800; color: #ffffff; margin: 0; display: flex; align-items: center; gap: 8px;">
              <i class="fa-solid fa-layer-group" style="color: #60a5fa;"></i>
              Loaded Questions Pool
            </h4>
            <span id="questionCountBadge" style="background: rgba(59, 130, 246, 0.2); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.35); font-size: 12px; font-weight: 800; padding: 3px 10px; border-radius: 999px;">
              0 Questions
            </span>
          </div>

          <div style="display: flex; gap: 8px; align-items: center;">
            <button type="button" onclick="focusManualQuestionEntry()" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); border-radius: 6px; padding: 6px 14px; font-size: 12px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
              <i class="fa-solid fa-plus"></i> Add Question
            </button>
            <button type="button" onclick="clearAllQuestions()" style="background: rgba(239, 68, 68, 0.12); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.25); border-radius: 6px; padding: 6px 14px; font-size: 12px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
              <i class="fa-solid fa-trash-can"></i> Clear All
            </button>
          </div>
        </div>

        <!-- Hidden input to transport full questions array on form submit -->
        <input type="hidden" name="questions_json" id="questions_json" value="{{ old('questions_json', '') }}">

        <!-- Questions Cards Container -->
        <div id="questionsPreviewContainer" style="display: flex; flex-direction: column; gap: 12px; max-height: 520px; overflow-y: auto; padding-right: 4px;">
          <div id="emptyQuestionsPlaceholder" style="text-align: center; padding: 36px 20px; color: #64748b; background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.08); border-radius: 10px;">
            <i class="fa-solid fa-clipboard-question" style="font-size: 32px; color: #475569; margin-bottom: 10px; display: block;"></i>
            <div style="font-weight: 700; color: #94a3b8; font-size: 13.5px;">No Questions Loaded Yet</div>
          </div>
        </div>
      </div>

      <!-- QUESTION SELECTION & EXAM PAPER BUILDER (UNDER LOADED PANEL) -->
      <div id="selectionConfigCard" style="background: #11141d; border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 22px 24px; margin-top: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 10px;">
          <div>
            <h4 style="font-size: 14.5px; font-weight: 800; color: #ffffff; margin: 0 0 4px 0; display: flex; align-items: center; gap: 8px;">
              <span style="width: 28px; height: 28px; border-radius: 6px; background: rgba(59, 130, 246, 0.15); color: #60a5fa; display: grid; place-items: center; font-size: 13px;">
                <i class="fa-solid fa-sliders"></i>
              </span>
              Exam Paper Question Selection
            </h4>
            <p style="margin: 0; font-size: 12px; color: #94a3b8;">
              Select how many and which questions from the loaded pool (<span id="summaryPoolTotal">0</span> total) will go into this examination paper.
            </p>
          </div>
          <div id="selectionModeIndicatorBadge" style="background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); font-size: 12px; font-weight: 800; padding: 4px 12px; border-radius: 999px;">
            <i class="fa-solid fa-check"></i> <span id="selectionSummaryText">0 / 0 Questions Selected</span>
          </div>
        </div>

        <!-- Mode Selection Radios -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 14px; margin-bottom: 20px;">
          <label class="selection-mode-option" id="label-mode-automatic" style="background: rgba(59, 130, 246, 0.08); border: 1.5px solid #3b82f6; border-radius: 10px; padding: 14px 16px; cursor: pointer; display: flex; align-items: flex-start; gap: 12px; transition: all 0.2s;">
            <input type="radio" name="selection_mode" id="mode_automatic" value="automatic" checked onchange="handleSelectionModeChange('automatic')" style="accent-color: #3b82f6; margin-top: 3px;">
            <div>
              <div style="font-size: 13.5px; font-weight: 800; color: #ffffff; margin-bottom: 2px;">Automatic Random Selection</div>
              <div style="font-size: 11.5px; color: #94a3b8; line-height: 1.4;">Specify the number of questions, and the system will randomly choose that exact number from all loaded questions.</div>
            </div>
          </label>

          <label class="selection-mode-option" id="label-mode-manual" style="background: rgba(255, 255, 255, 0.02); border: 1.5px solid rgba(255, 255, 255, 0.08); border-radius: 10px; padding: 14px 16px; cursor: pointer; display: flex; align-items: flex-start; gap: 12px; transition: all 0.2s;">
            <input type="radio" name="selection_mode" id="mode_manual" value="manual" onchange="handleSelectionModeChange('manual')" style="accent-color: #3b82f6; margin-top: 3px;">
            <div>
              <div style="font-size: 13.5px; font-weight: 800; color: #ffffff; margin-bottom: 2px;">Manual Selection</div>
              <div style="font-size: 11.5px; color: #94a3b8; line-height: 1.4;">Hand-pick questions from the pool using the checkboxes on each question card.</div>
            </div>
          </label>
        </div>

        <!-- AUTOMATIC MODE CONTROLS -->
        <div id="controls-automatic" style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 10px; padding: 16px 18px;">
          <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;">
            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
              <label class="ida-label" style="margin-bottom: 0; white-space: nowrap;">Questions in Exam:</label>
              <input type="number" id="selected_question_count" name="selected_question_count" value="50" min="1" class="form-tactical" style="width: 110px; padding: 8px 12px; font-weight: 800; font-size: 14px; text-align: center;" oninput="handleRandomCountChange()">
              <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                <button type="button" class="btn-preset-count" onclick="setPresetCount(10)" style="background: rgba(255,255,255,0.06); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.12); padding: 5px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 700; cursor: pointer;">10 Qs</button>
                <button type="button" class="btn-preset-count" onclick="setPresetCount(25)" style="background: rgba(255,255,255,0.06); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.12); padding: 5px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 700; cursor: pointer;">25 Qs</button>
                <button type="button" class="btn-preset-count" onclick="setPresetCount(50)" style="background: rgba(255,255,255,0.06); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.12); padding: 5px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 700; cursor: pointer;">50 Qs</button>
                <button type="button" class="btn-preset-count" onclick="setPresetCount(100)" style="background: rgba(255,255,255,0.06); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.12); padding: 5px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 700; cursor: pointer;">100 Qs</button>
                <button type="button" class="btn-preset-count" onclick="setPresetCount('all')" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3); padding: 5px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 700; cursor: pointer;">All Pool</button>
              </div>
            </div>

            <button type="button" onclick="shuffleAndPickRandomNow()" style="background: #3b82f6; color: #ffffff; border: none; padding: 8px 18px; font-size: 12.5px; font-weight: 800; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 7px; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.35);">
              <i class="fa-solid fa-shuffle"></i> Shuffle & Pick Random Now
            </button>
          </div>
          <div id="randomPickMessage" style="font-size: 11.5px; color: #94a3b8; margin-top: 10px;">
            <i class="fa-solid fa-circle-info" style="color: #60a5fa; margin-right: 4px;"></i>
            When exam is created, exactly <strong id="randomCountDisplay" style="color: #ffffff;">50</strong> questions will be randomly chosen from the <span id="randomPoolTotalDisplay">0</span> loaded questions.
          </div>
        </div>

        <!-- MANUAL MODE CONTROLS -->
        <div id="controls-manual" style="display: none; background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 10px; padding: 16px 18px;">
          <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
            <div style="font-size: 12.5px; color: #cbd5e1;">
              <strong style="color: #ffffff;">Check or uncheck</strong> individual questions directly on the cards above. Quick selectors:
            </div>
            <div style="display: flex; gap: 6px; flex-wrap: wrap;">
              <button type="button" onclick="selectBatchManual('all')" style="background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); padding: 5px 12px; border-radius: 6px; font-size: 11.5px; font-weight: 700; cursor: pointer;">
                <i class="fa-solid fa-check-double"></i> Select All
              </button>
              <button type="button" onclick="selectBatchManual('none')" style="background: rgba(239, 68, 68, 0.12); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.25); padding: 5px 12px; border-radius: 6px; font-size: 11.5px; font-weight: 700; cursor: pointer;">
                <i class="fa-solid fa-xmark"></i> Deselect All
              </button>
              <button type="button" onclick="selectBatchManual(25)" style="background: rgba(255,255,255,0.06); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.12); padding: 5px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 700; cursor: pointer;">First 25</button>
              <button type="button" onclick="selectBatchManual(50)" style="background: rgba(255,255,255,0.06); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.12); padding: 5px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 700; cursor: pointer;">First 50</button>
              <button type="button" onclick="selectBatchManual(100)" style="background: rgba(255,255,255,0.06); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.12); padding: 5px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 700; cursor: pointer;">First 100</button>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- STICKY BOTTOM ACTION TOOLBAR -->
    <div style="background: #181c26; border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 16px; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; margin-bottom: 40px; box-shadow: 0 8px 24px rgba(0,0,0,0.3);">
      <div style="display: flex; align-items: center; gap: 12px;">
        <a href="{{ route('admin.exam_management.index') }}" class="btn-tactical" style="background: rgba(255,255,255,0.06); color: #94a3b8; border: 1px solid rgba(255,255,255,0.12); padding: 10px 20px; font-size: 13px; font-weight: 700; text-decoration: none; border-radius: 8px; display: inline-flex; align-items: center; gap: 7px;">
          <i class="fa-solid fa-xmark"></i> Cancel & Return
        </a>
      </div>

      <div style="display: flex; align-items: center; gap: 12px;">
        <button type="submit" class="btn-primary" style="background: #ff5757; color: #ffffff; padding: 11px 32px; font-size: 14px; font-weight: 800; border-radius: 8px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 9px; box-shadow: 0 4px 14px rgba(255, 87, 87, 0.35);">
          <i class="fa-solid fa-check"></i> Create Question Paper
        </button>
      </div>
    </div>

  </form>
</div>

<script>
  let loadedQuestions = [];

  document.addEventListener('DOMContentLoaded', function() {
    const rawVal = document.getElementById('questions_json').value;
    if (rawVal && rawVal.trim().length > 0) {
      try {
        const parsed = JSON.parse(rawVal);
        if (Array.isArray(parsed)) {
          loadedQuestions = parsed;
          renderQuestionsList();
        }
      } catch(e) {}
    }

    // Form submit validation & question selection handling
    const form = document.getElementById('createExamForm');
    if (form) {
      form.addEventListener('submit', function(e) {
        if (!loadedQuestions || loadedQuestions.length === 0) {
          e.preventDefault();
          showIngestionAlert('Questions are mandatory! Please upload or add at least one question before creating this module.', 'error');
          const target = document.getElementById('section-question-ingestion');
          if (target) {
            target.scrollIntoView({ behavior: 'smooth' });
          }
          return false;
        }

        const mode = document.querySelector('input[name="selection_mode"]:checked')?.value || 'automatic';

        if (mode === 'automatic') {
          const autoCount = parseInt(document.getElementById('selected_question_count').value) || loadedQuestions.length;
          const countToPick = Math.max(1, Math.min(autoCount, loadedQuestions.length));
          // Random shuffle sample for the exam paper
          const shuffled = [...loadedQuestions].sort(() => 0.5 - Math.random());
          const selectedQuestions = shuffled.slice(0, countToPick);
          document.getElementById('questions_json').value = JSON.stringify(selectedQuestions);
        } else {
          // Manual selection mode: pick only checked questions
          const manualSelected = loadedQuestions.filter(q => q.selected !== false);
          if (manualSelected.length === 0) {
            e.preventDefault();
            showIngestionAlert('Please select at least one question using the checkboxes for manual mode.', 'error');
            const target = document.getElementById('selectionConfigCard');
            if (target) {
              target.scrollIntoView({ behavior: 'smooth' });
            }
            return false;
          }
          document.getElementById('questions_json').value = JSON.stringify(manualSelected);
        }
      });
    }
  });

  function showIngestionAlert(message, type) {
    const box = document.getElementById('ingestionAlert');
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
    document.getElementById('ingestionAlert').style.display = 'none';
  }

  function switchQTab(tab) {
    document.querySelectorAll('.q-tab-btn').forEach(b => {
      b.style.background = 'rgba(255,255,255,0.04)';
      b.style.color = '#94a3b8';
      b.style.borderColor = 'rgba(255,255,255,0.08)';
      b.style.boxShadow = 'none';
    });
    document.querySelectorAll('.q-tab-panel').forEach(p => p.style.display = 'none');

    const activeBtn = document.getElementById('tab-btn-' + tab);
    const activePanel = document.getElementById('tab-panel-' + tab);
    if (activeBtn && activePanel) {
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

**2. Definite Gaussian Moment Integral**
For a positive parameter $\\alpha > 0$, evaluate the Gaussian moment integral:
$$\\int_{-\\infty}^{\\infty} x^2 e^{-\\alpha x^2} \\, dx$$
A) $\\frac{1}{2}\\sqrt{\\frac{\\pi}{\\alpha^3}}$
B) $\\sqrt{\\frac{\\pi}{\\alpha^3}}$
C) $\\frac{1}{4}\\sqrt{\\frac{\\pi}{\\alpha}}$
D) $\\frac{\\sqrt{\\pi}}{2\\alpha}$
Ans: A

---

**3. Linear Systems with Matrix Product**
Given $[[1, 2], [3, 4]] [[x, y], [z, w]] = [[1, 0], [0, 1]]$, what is $(x, y, z, w)$?
A) (-2, 1, 3, 1/2)
B) (2, -1, 3/2, -1)
C) (-2, 1, 3/2, -1/2)
D) (-2, 0, 3/2, 0)
Ans: C`;
    document.getElementById('bulkQuestionInput').value = sample;
  }

  async function transformBulkQuestions() {
    hideIngestionAlert();
    const textVal = document.getElementById('bulkQuestionInput').value.trim();
    if (!textVal) {
      showIngestionAlert('Please paste questions with equations or text into the bulk area first.', 'error');
      return;
    }

    const btn = document.getElementById('btnTransformBulk');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Loading Questions...';

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

      loadedQuestions = loadedQuestions.concat(incoming);
      renderQuestionsList();
      showIngestionAlert('Loaded ' + incoming.length + ' questions into the Question Pool!', 'success');
      
      const container = document.getElementById('questionsPreviewContainer');
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
        window.MathJax.typesetPromise([previewBox]).catch(function() {});
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
      index: loadedQuestions.length + 1,
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

    loadedQuestions.push(newQ);
    renderQuestionsList();

    // Clear single form
    document.getElementById('singleQuestionText').value = '';
    document.getElementById('singleOptionA').value = '';
    document.getElementById('singleOptionB').value = '';
    document.getElementById('singleOptionC').value = '';
    document.getElementById('singleOptionD').value = '';
    document.getElementById('singleExplanation').value = '';
    document.getElementById('singleLivePreviewBox').style.display = 'none';

    showIngestionAlert('Question loaded into Question Pool successfully!', 'success');
  }

  function focusManualQuestionEntry() {
    switchQTab('single');
    const input = document.getElementById('singleQuestionText');
    if (input) {
      input.scrollIntoView({ behavior: 'smooth', block: 'center' });
      input.focus();
    }
  }

  function toggleQuestionSelection(idx, isChecked) {
    if (loadedQuestions[idx]) {
      loadedQuestions[idx].selected = isChecked;
      const card = document.getElementById('q-card-' + idx);
      if (card) {
        card.style.borderColor = isChecked ? 'rgba(59, 130, 246, 0.45)' : 'rgba(255, 255, 255, 0.08)';
        const exclTag = card.querySelector('.q-excluded-tag');
        if (exclTag) {
          exclTag.style.display = isChecked ? 'none' : 'inline';
        }
      }
      updateSelectionSummary();
    }
  }

  function handleSelectionModeChange(mode) {
    const lblAuto = document.getElementById('label-mode-automatic');
    const lblManual = document.getElementById('label-mode-manual');
    const ctrlAuto = document.getElementById('controls-automatic');
    const ctrlManual = document.getElementById('controls-manual');

    if (mode === 'automatic') {
      if (lblAuto) {
        lblAuto.style.background = 'rgba(59, 130, 246, 0.08)';
        lblAuto.style.borderColor = '#3b82f6';
      }
      if (lblManual) {
        lblManual.style.background = 'rgba(255, 255, 255, 0.02)';
        lblManual.style.borderColor = 'rgba(255, 255, 255, 0.08)';
      }
      if (ctrlAuto) ctrlAuto.style.display = 'block';
      if (ctrlManual) ctrlManual.style.display = 'none';
    } else {
      if (lblManual) {
        lblManual.style.background = 'rgba(59, 130, 246, 0.08)';
        lblManual.style.borderColor = '#3b82f6';
      }
      if (lblAuto) {
        lblAuto.style.background = 'rgba(255, 255, 255, 0.02)';
        lblAuto.style.borderColor = 'rgba(255, 255, 255, 0.08)';
      }
      if (ctrlAuto) ctrlAuto.style.display = 'none';
      if (ctrlManual) ctrlManual.style.display = 'block';
    }
    updateSelectionSummary();
  }

  function handleRandomCountChange() {
    updateSelectionSummary();
  }

  function setPresetCount(count) {
    const input = document.getElementById('selected_question_count');
    if (!input) return;
    if (count === 'all') {
      input.value = Math.max(1, loadedQuestions.length);
    } else {
      input.value = Math.max(1, count);
    }
    updateSelectionSummary();
  }

  function shuffleAndPickRandomNow() {
    if (loadedQuestions.length === 0) {
      showIngestionAlert('No questions in pool to shuffle.', 'error');
      return;
    }

    const input = document.getElementById('selected_question_count');
    const count = Math.min(loadedQuestions.length, Math.max(1, parseInt(input.value) || 50));

    // Fisher-Yates shuffle
    for (let i = loadedQuestions.length - 1; i > 0; i--) {
      const j = Math.floor(Math.random() * (i + 1));
      [loadedQuestions[i], loadedQuestions[j]] = [loadedQuestions[j], loadedQuestions[i]];
    }

    // Mark first N as selected, others as unselected
    loadedQuestions.forEach((q, idx) => {
      q.selected = idx < count;
    });

    renderQuestionsList();
    showIngestionAlert(`Randomly shuffled pool and marked ${count} questions for this exam paper!`, 'success');
  }

  function selectBatchManual(type) {
    if (loadedQuestions.length === 0) return;

    if (type === 'all') {
      loadedQuestions.forEach(q => q.selected = true);
    } else if (type === 'none') {
      loadedQuestions.forEach(q => q.selected = false);
    } else if (typeof type === 'number') {
      loadedQuestions.forEach((q, idx) => {
        q.selected = idx < type;
      });
    }

    renderQuestionsList();
  }

  function startEditingQuestion(idx) {
    if (!loadedQuestions[idx]) return;
    loadedQuestions[idx].is_editing = true;
    renderQuestionsList();
  }

  function cancelEditingQuestion(idx) {
    if (!loadedQuestions[idx]) return;
    delete loadedQuestions[idx].is_editing;
    renderQuestionsList();
  }

  function saveEditingQuestion(idx) {
    const q = loadedQuestions[idx];
    if (!q) return;

    const qText = (document.getElementById('edit-q-text-' + idx)?.value || '').trim();
    const optA = (document.getElementById('edit-opt-a-' + idx)?.value || '').trim();
    const optB = (document.getElementById('edit-opt-b-' + idx)?.value || '').trim();
    const optC = (document.getElementById('edit-opt-c-' + idx)?.value || '').trim();
    const optD = (document.getElementById('edit-opt-d-' + idx)?.value || '').trim();
    const ans = document.getElementById('edit-ans-' + idx)?.value || 'A';
    const expl = (document.getElementById('edit-expl-' + idx)?.value || '').trim();

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

    renderQuestionsList();
    showIngestionAlert(`Question Q#${idx + 1} updated successfully in loaded pool!`, 'success');
  }

  function updateSelectionSummary() {
    const poolTotal = loadedQuestions.length;
    const summaryPoolEl = document.getElementById('summaryPoolTotal');
    const randomPoolTotalEl = document.getElementById('randomPoolTotalDisplay');
    const randomCountDispEl = document.getElementById('randomCountDisplay');
    const badgeEl = document.getElementById('selectionSummaryText');

    if (summaryPoolEl) summaryPoolEl.textContent = poolTotal;
    if (randomPoolTotalEl) randomPoolTotalEl.textContent = poolTotal;

    const mode = document.querySelector('input[name="selection_mode"]:checked')?.value || 'automatic';

    if (mode === 'automatic') {
      const input = document.getElementById('selected_question_count');
      const count = Math.min(poolTotal, Math.max(1, parseInt(input ? input.value : 50) || 50));
      if (randomCountDispEl) randomCountDispEl.textContent = count;
      if (badgeEl) badgeEl.textContent = `${count} / ${poolTotal} Questions (Random)`;
    } else {
      const selectedCount = loadedQuestions.filter(q => q.selected !== false).length;
      if (badgeEl) badgeEl.textContent = `${selectedCount} / ${poolTotal} Questions Selected`;
    }
  }

  function removeQuestion(index) {
    loadedQuestions.splice(index, 1);
    renderQuestionsList();
  }

  function clearAllQuestions() {
    if (loadedQuestions.length === 0) return;
    if (confirm('Are you sure you want to clear all ' + loadedQuestions.length + ' questions from this module?')) {
      loadedQuestions = [];
      renderQuestionsList();
      showIngestionAlert('All questions cleared.', 'success');
    }
  }

  function renderQuestionsList() {
    const container = document.getElementById('questionsPreviewContainer');
    const badge = document.getElementById('questionCountBadge');
    const jsonHidden = document.getElementById('questions_json');
    const selectionCard = document.getElementById('selectionConfigCard');

    badge.textContent = loadedQuestions.length + ' Questions';
    jsonHidden.value = JSON.stringify(loadedQuestions);

    if (selectionCard) {
      selectionCard.style.display = loadedQuestions.length > 0 ? 'block' : 'none';
    }

    if (loadedQuestions.length === 0) {
      container.innerHTML = `
        <div id="emptyQuestionsPlaceholder" style="text-align: center; padding: 36px 20px; color: #64748b; background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.08); border-radius: 10px;">
          <i class="fa-solid fa-clipboard-question" style="font-size: 32px; color: #475569; margin-bottom: 10px; display: block;"></i>
          <div style="font-weight: 700; color: #94a3b8; font-size: 13.5px;">No Questions Loaded Yet</div>
        </div>
      `;
      updateSelectionSummary();
      return;
    }

    let html = '';
    loadedQuestions.forEach((q, idx) => {
      const qText = q.question_text || q.question || '';
      const optA = q.option_a || (q.options && q.options[0] ? (q.options[0].text || q.options[0]) : '');
      const optB = q.option_b || (q.options && q.options[1] ? (q.options[1].text || q.options[1]) : '');
      const optC = q.option_c || (q.options && q.options[2] ? (q.options[2].text || q.options[2]) : '');
      const optD = q.option_d || (q.options && q.options[3] ? (q.options[3].text || q.options[3]) : '');
      const correct = (q.correct_answer || 'A').toUpperCase();
      const expl = q.explanation || '';
      const isSelected = q.selected !== false;
      const isEditing = q.is_editing === true;

      html += `
        <div id="q-card-${idx}" class="q-preview-card" style="background: rgba(255,255,255,0.03); border: 1px solid ${isSelected ? 'rgba(59, 130, 246, 0.4)' : 'rgba(255,255,255,0.08)'}; border-radius: 10px; padding: 14px 18px; transition: border-color 0.2s;">
          <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 10px;">
            <div style="display: flex; align-items: center; gap: 10px;">
              <label style="display: inline-flex; align-items: center; gap: 7px; cursor: pointer; margin: 0;">
                <input type="checkbox" class="q-card-checkbox" ${isSelected ? 'checked' : ''} onchange="toggleQuestionSelection(${idx}, this.checked)" style="width: 16px; height: 16px; accent-color: #3b82f6; cursor: pointer;">
                <span style="background: #3b82f6; color: #ffffff; font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 4px;">Q#${idx + 1}</span>
              </label>
              <span style="background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.35); font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 4px;">
                <i class="fa-solid fa-check"></i> Ans: ${escapeHtml(correct)}
              </span>
              <span class="q-excluded-tag" style="display: ${isSelected ? 'none' : 'inline'}; font-size: 11px; color: #f87171; font-weight: 700;">
                <i class="fa-solid fa-ban"></i> Excluded
              </span>
            </div>
            <div style="display: flex; align-items: center; gap: 6px;">
              ${!isEditing ? `
                <button type="button" onclick="startEditingQuestion(${idx})" style="background: rgba(59, 130, 246, 0.12); border: 1px solid rgba(59, 130, 246, 0.25); color: #60a5fa; font-size: 12px; font-weight: 700; cursor: pointer; padding: 4px 10px; border-radius: 6px; display: inline-flex; align-items: center; gap: 5px;">
                  <i class="fa-solid fa-pen-to-square"></i> Edit
                </button>
              ` : ''}
              <button type="button" onclick="removeQuestion(${idx})" style="background: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.25); color: #ef4444; font-size: 12px; font-weight: 700; cursor: pointer; padding: 4px 10px; border-radius: 6px; display: inline-flex; align-items: center; gap: 5px;" title="Remove this question">
                <i class="fa-solid fa-trash-can"></i> Remove
              </button>
            </div>
          </div>

          <!-- NORMAL DISPLAY VIEW -->
          <div id="q-view-${idx}" style="${isEditing ? 'display: none;' : 'display: block;'}">
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

          <!-- INLINE EDITING FORM -->
          <div id="q-edit-box-${idx}" style="${isEditing ? 'display: block;' : 'display: none;'} background: rgba(0,0,0,0.28); border: 1px solid rgba(59, 130, 246, 0.35); border-radius: 8px; padding: 14px; margin-top: 6px;">
            <div style="margin-bottom: 10px;">
              <label class="ida-label" style="font-size: 11px;">Edit Question Text *</label>
              <textarea id="edit-q-text-${idx}" rows="2" class="form-tactical" style="width: 100%; padding: 8px 12px; font-size: 13px;">${escapeHtml(qText)}</textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px;">
              <div>
                <label class="ida-label" style="font-size: 11px;">Option A *</label>
                <input type="text" id="edit-opt-a-${idx}" value="${escapeHtml(optA)}" class="form-tactical" style="width: 100%; padding: 7px 10px; font-size: 12.5px;">
              </div>
              <div>
                <label class="ida-label" style="font-size: 11px;">Option B *</label>
                <input type="text" id="edit-opt-b-${idx}" value="${escapeHtml(optB)}" class="form-tactical" style="width: 100%; padding: 7px 10px; font-size: 12.5px;">
              </div>
              <div>
                <label class="ida-label" style="font-size: 11px;">Option C *</label>
                <input type="text" id="edit-opt-c-${idx}" value="${escapeHtml(optC)}" class="form-tactical" style="width: 100%; padding: 7px 10px; font-size: 12.5px;">
              </div>
              <div>
                <label class="ida-label" style="font-size: 11px;">Option D *</label>
                <input type="text" id="edit-opt-d-${idx}" value="${escapeHtml(optD)}" class="form-tactical" style="width: 100%; padding: 7px 10px; font-size: 12.5px;">
              </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 10px; margin-bottom: 12px;">
              <div>
                <label class="ida-label" style="font-size: 11px;">Correct Answer Key *</label>
                <select id="edit-ans-${idx}" class="form-tactical" style="width: 100%; padding: 7px 10px; font-size: 12.5px; font-weight: 700;">
                  <option value="A" ${correct === 'A' ? 'selected' : ''}>Option A</option>
                  <option value="B" ${correct === 'B' ? 'selected' : ''}>Option B</option>
                  <option value="C" ${correct === 'C' ? 'selected' : ''}>Option C</option>
                  <option value="D" ${correct === 'D' ? 'selected' : ''}>Option D</option>
                </select>
              </div>
              <div>
                <label class="ida-label" style="font-size: 11px;">Explanation (Optional)</label>
                <input type="text" id="edit-expl-${idx}" value="${escapeHtml(expl)}" class="form-tactical" style="width: 100%; padding: 7px 10px; font-size: 12.5px;">
              </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 8px;">
              <button type="button" onclick="cancelEditingQuestion(${idx})" style="background: rgba(255,255,255,0.06); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.12); padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 700; cursor: pointer;">
                Cancel
              </button>
              <button type="button" onclick="saveEditingQuestion(${idx})" style="background: #10b981; color: #ffffff; border: none; padding: 6px 16px; border-radius: 6px; font-size: 12px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; gap: 5px;">
                <i class="fa-solid fa-check"></i> Save Changes
              </button>
            </div>
          </div>
        </div>
      `;
    });

    container.innerHTML = html;
    updateSelectionSummary();

    if (window.typesetMathJax) {
      window.typesetMathJax(container);
    } else if (window.MathJax && window.MathJax.typesetPromise) {
      try {
        if (window.MathJax.typesetClear) window.MathJax.typesetClear([container]);
        window.MathJax.typesetPromise([container]).catch(function() {});
      } catch(e) {}
    }
  }

  /**
   * Render text with LaTeX math preserved for MathJax rendering.
   * Extracts LaTeX regions ($...$, $$...$$, \(...\), \[...\]), escapes
   * surrounding text for XSS safety, then re-inserts LaTeX unescaped.
   * Uses callback substitution to prevent JavaScript $$ / $& replacement bugs.
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

  const hierarchyConfig = {
    navy: {
      soldierLabel: 'Navy Sailor',
      soldierCategory: 'Navy Sailor Recruitment Test',
      cadres: {
        officer: [
          { value: 'prelim', label: 'Preliminary', defaultCategory: 'Navy Preliminary IQ & Academic' },
          { value: 'issb', label: 'ISSB', defaultCategory: 'Navy ISSB Masterclass' }
        ]
      }
    },
    police: {
      ranks: [
        { value: 'constable', label: 'Constable', defaultCategory: 'Police Constable Recruitment Test' },
        { value: 'si', label: 'Sub-Inspector (SI)', defaultCategory: 'Police Sub-Inspector (SI) Test' },
        { value: 'asi', label: 'Assistant Sub-Inspector (ASI)', defaultCategory: 'Police Assistant Sub-Inspector (ASI) Test' }
      ]
    },
    army: {
      soldierLabel: 'Soldier Cadet',
      soldierCategory: 'Army Soldier Recruitment Test',
      cadres: {
        officer: [
          { value: 'prelim', label: 'Preliminary', defaultCategory: 'Army Preliminary IQ & Academic' },
          { value: 'issb', label: 'ISSB', defaultCategory: 'Army ISSB Masterclass' }
        ]
      }
    },
    air_force: {
      soldierLabel: 'Airman Cadet',
      soldierCategory: 'Air Force Airman Recruitment Test',
      cadres: {
        officer: [
          { value: 'prelim', label: 'Preliminary', defaultCategory: 'Air Force Preliminary IQ & Academic' },
          { value: 'issb', label: 'ISSB', defaultCategory: 'Air Force Airman ISSB Masterclass' }
        ]
      }
    }
  };

  function updateCascadingFormHierarchy(branch, cadre = null, targetTrack = null) {
    const branchEl = document.getElementById('examBranchSelect');
    const cadreContainer = document.getElementById('cadreSelectContainer');
    const cadreEl = document.getElementById('examCadreSelect');
    const cadreLabel = document.getElementById('cadreSelectLabel');
    const soldierOption = document.getElementById('soldierCadreOption');
    const examTypeContainer = document.getElementById('examTypeContainer');
    const trackEl = document.getElementById('examTargetTrackSelect');
    const trackLabel = document.getElementById('trackSelectLabel');
    const accessTypeLabel = document.getElementById('accessTypeLabel');
    const hiddenTrackInput = document.getElementById('finalTargetTrackInput');
    const hiddenCatInput = document.getElementById('examCategoryInput');

    if (!branchEl || !trackEl) return;

    if (branchEl.value !== branch) {
      branchEl.value = branch;
    }

    let activeTracks = [];

    if (branch === 'police') {
      if (cadreContainer) cadreContainer.style.display = 'none';
      if (examTypeContainer) examTypeContainer.style.display = 'block';
      if (trackLabel) trackLabel.textContent = '2. Exam Type *';
      if (accessTypeLabel) accessTypeLabel.textContent = '3. Access Type *';
      activeTracks = hierarchyConfig.police.ranks;
      const validVal = activeTracks.some(t => t.value === targetTrack) ? targetTrack : (activeTracks.some(t => t.value === trackEl.value) ? trackEl.value : 'si');
      trackEl.innerHTML = activeTracks.map(t => `<option value="${t.value}" ${t.value === validVal ? 'selected' : ''}>${t.label}</option>`).join('');
      if (hiddenTrackInput) hiddenTrackInput.value = validVal;
      const matched = activeTracks.find(t => t.value === validVal);
      if (matched && hiddenCatInput && matched.defaultCategory) {
        hiddenCatInput.value = matched.defaultCategory;
      }
    } else {
      if (cadreContainer) cadreContainer.style.display = 'block';
      if (cadreLabel) cadreLabel.textContent = '2. Post *';
      const wingConf = hierarchyConfig[branch] || hierarchyConfig.navy;
      if (soldierOption) soldierOption.textContent = wingConf.soldierLabel;

      const activeCadre = cadre || (cadreEl ? cadreEl.value : null) || (targetTrack === 'soldier' ? 'soldier' : 'officer');
      if (cadreEl) cadreEl.value = activeCadre;

      if (activeCadre === 'soldier') {
        // Soldier/Sailor/Airman: Exam Type disappears! ISSB is strictly for Officer.
        if (examTypeContainer) examTypeContainer.style.display = 'none';
        if (accessTypeLabel) accessTypeLabel.textContent = '3. Access Type *';
        if (hiddenTrackInput) hiddenTrackInput.value = 'soldier';
        if (hiddenCatInput && wingConf.soldierCategory) {
          hiddenCatInput.value = wingConf.soldierCategory;
        }
      } else {
        // Officer: Exam Type is visible with Preliminary and ISSB
        if (examTypeContainer) examTypeContainer.style.display = 'block';
        if (trackLabel) trackLabel.textContent = '3. Exam Type *';
        if (accessTypeLabel) accessTypeLabel.textContent = '4. Access Type *';
        activeTracks = wingConf.cadres.officer;
        const validVal = (targetTrack === 'issb' || (targetTrack !== 'soldier' && trackEl.value === 'issb')) ? 'issb' : 'prelim';
        trackEl.innerHTML = activeTracks.map(t => `<option value="${t.value}" ${t.value === validVal ? 'selected' : ''}>${t.label}</option>`).join('');
        if (hiddenTrackInput) hiddenTrackInput.value = validVal;
        const matched = activeTracks.find(t => t.value === validVal) || activeTracks[0];
        if (matched && hiddenCatInput && matched.defaultCategory) {
          hiddenCatInput.value = matched.defaultCategory;
        }
      }
    }

    if (typeof window.refreshTacticalSelect === 'function') {
      window.refreshTacticalSelect(branchEl);
      window.refreshTacticalSelect(cadreEl);
      window.refreshTacticalSelect(trackEl);
    }
  }

  function onExamFormBranchChange(branch) {
    updateCascadingFormHierarchy(branch, null, null);
  }

  function onExamFormCadreChange(cadre) {
    const branchEl = document.getElementById('examBranchSelect');
    const branch = branchEl ? branchEl.value : 'navy';
    updateCascadingFormHierarchy(branch, cadre, null);
  }

  function onExamFormTrackChange(track) {
    const hiddenTrackInput = document.getElementById('finalTargetTrackInput');
    if (hiddenTrackInput) hiddenTrackInput.value = track;
    const branchEl = document.getElementById('examBranchSelect');
    const cadreEl = document.getElementById('examCadreSelect');
    const branch = branchEl ? branchEl.value : 'navy';
    const cadre = cadreEl ? cadreEl.value : 'officer';
    updateCascadingFormHierarchy(branch, cadre, track);
  }

  document.addEventListener('DOMContentLoaded', function() {
    const branchEl = document.getElementById('examBranchSelect');
    const initialBranch = branchEl ? branchEl.value : 'navy';
    const initialTrack = "{{ old('target_track', 'prelim') }}";
    const initialCadre = (initialTrack === 'soldier') ? 'soldier' : 'officer';

    if (branchEl) {
      branchEl.addEventListener('change', function() {
        onExamFormBranchChange(this.value);
      });
      const cadreEl = document.getElementById('examCadreSelect');
      if (cadreEl) {
        cadreEl.addEventListener('change', function() {
          onExamFormCadreChange(this.value);
        });
      }
      const trackEl = document.getElementById('examTargetTrackSelect');
      if (trackEl) {
        trackEl.addEventListener('change', function() {
          onExamFormTrackChange(this.value);
        });
      }

      updateCascadingFormHierarchy(initialBranch, initialCadre, initialTrack);

      if (typeof window.initTacticalSelect === 'function') {
        document.querySelectorAll('.ida-cascading-select, .ida-track-select').forEach(function(sel) {
          window.initTacticalSelect(sel);
        });
      }
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
  .form-tactical, select.form-tactical, .ida-cascading-select {
    color-scheme: dark !important;
    background-color: #11141d !important;
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    box-sizing: border-box;
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s;
  }
  select.form-tactical option, .ida-cascading-select option {
    background-color: #11141d !important;
    color: #ffffff !important;
    font-size: 13px !important;
    font-weight: 600 !important;
  }
  .form-tactical:focus, select.form-tactical:focus, .ida-cascading-select:focus {
    border-color: #ff5757 !important;
    box-shadow: 0 0 0 3px rgba(255, 87, 87, 0.18) !important;
  }
  .q-preview-card:hover {
    border-color: rgba(255, 255, 255, 0.18) !important;
  }
</style>
@endsection
