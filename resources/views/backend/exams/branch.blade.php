@extends('layouts.portal')

@section('title', $branchName . ' Examination Deck')
@section('page_title', $branchName . ' Examination Deck')
@section('page_subtitle', 'Executive management sector: Control active exam schedules or create new exam papers from PDF')

@section('topbar_actions')
  <div style="display: flex; gap: 8px; flex-wrap: wrap;">
    <button type="button" class="btn-tactical btn-tactical-primary" onclick="switchTab('creation')">
      <i class="fa-solid fa-file-pdf"></i> Upload MCQ PDF
    </button>
    <button type="button" class="btn-tactical btn-tactical-gold" onclick="switchTab('control')">
      <i class="fa-solid fa-sliders"></i> Exam Control Sector
    </button>
    <a href="{{ route('admin.exams.index') }}" class="btn-tactical btn-tactical-outline">
      <i class="fa-solid fa-arrow-left"></i> All Branches
    </a>
  </div>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

  <!-- Branch Switcher Bar -->
  <div class="tactical-card" style="padding: 12px 18px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
      <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #94a3b8; margin-right: 4px;">Branch Portal:</span>
      <a href="{{ route('admin.exams.index') }}" class="badge badge-navy" style="text-decoration: none; padding: 6px 12px; font-size: 12px;">All Exams</a>
      <a href="{{ route('admin.exams.branch', 'army') }}" class="badge {{ $branch === 'army' ? 'badge-emerald' : 'badge-navy' }}" style="text-decoration: none; padding: 6px 12px; font-size: 12px;">
        <i class="fa-solid fa-shield-halved"></i> Army
      </a>
      <a href="{{ route('admin.exams.branch', 'navy') }}" class="badge {{ $branch === 'navy' ? 'badge-emerald' : 'badge-navy' }}" style="text-decoration: none; padding: 6px 12px; font-size: 12px;">
        <i class="fa-solid fa-anchor"></i> Navy
      </a>
      <a href="{{ route('admin.exams.branch', 'air_force') }}" class="badge {{ $branch === 'air_force' ? 'badge-emerald' : 'badge-navy' }}" style="text-decoration: none; padding: 6px 12px; font-size: 12px;">
        <i class="fa-solid fa-jet-fighter"></i> Air Force
      </a>
      <a href="{{ route('admin.exams.branch', 'police') }}" class="badge {{ $branch === 'police' ? 'badge-emerald' : 'badge-navy' }}" style="text-decoration: none; padding: 6px 12px; font-size: 12px;">
        <i class="fa-solid fa-user-shield"></i> Police
      </a>
    </div>

    <div>
      <span class="badge badge-emerald" style="font-size: 11px;">
        <i class="fa-solid fa-layer-group"></i> {{ $exams->count() }} Total {{ $branchName }} Exam Papers
      </span>
      <span class="badge badge-gold" style="font-size: 11px; margin-left: 6px;">
        <i class="fa-solid fa-database"></i> Question Bank Master Pool: {{ $stats['pool_count'] }} Qs
      </span>
    </div>
  </div>

  <!-- THE TWO SEPARATE PLACES: HIGH CONTRAST SECTOR SWITCHER -->
  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
    <!-- Sector 1: Exam Control & Management -->
    <button type="button" id="tab-btn-control" onclick="switchTab('control')" style="display: flex; align-items: center; gap: 14px; padding: 18px 22px; border-radius: 12px; cursor: pointer; text-align: left; transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1); border: 2px solid {{ $activeTab === 'control' ? '#f59e0b' : 'rgba(255, 255, 255, 0.08)' }}; background: {{ $activeTab === 'control' ? 'rgba(245, 158, 11, 0.14)' : '#141722' }}; color: #ffffff;">
      <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(245, 158, 11, 0.2); color: #f59e0b; display: grid; place-items: center; font-size: 20px; flex-shrink: 0;">
        <i class="fa-solid fa-sliders"></i>
      </div>
      <div>
        <div style="font-size: 15px; font-weight: 700; color: #ffffff;">
          Exam Control & Management
        </div>
      </div>
    </button>

    <!-- Sector 2: Create New Exam from PDF -->
    <button type="button" id="tab-btn-creation" onclick="switchTab('creation')" style="display: flex; align-items: center; gap: 14px; padding: 18px 22px; border-radius: 12px; cursor: pointer; text-align: left; transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1); border: 2px solid {{ $activeTab === 'creation' ? '#10b981' : 'rgba(255, 255, 255, 0.08)' }}; background: {{ $activeTab === 'creation' ? 'rgba(16, 185, 129, 0.14)' : '#141722' }}; color: #ffffff;">
      <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(16, 185, 129, 0.2); color: #10b981; display: grid; place-items: center; font-size: 20px; flex-shrink: 0;">
        <i class="fa-solid fa-file-circle-plus"></i>
      </div>
      <div>
        <div style="font-size: 15px; font-weight: 700; color: #ffffff;">
          Create New Exam from PDF
        </div>
      </div>
    </button>
  </div>


  <!-- ================================================================= -->
  <!-- PLACE 1: EXAM CONTROL & MANAGEMENT SECTOR                         -->
  <!-- ================================================================= -->
  <div id="tab-content-control" style="display: {{ $activeTab === 'control' ? 'flex' : 'none' }}; flex-direction: column; gap: 24px;">
    
    <!-- Control Center Metrics -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px;">
      <div class="tactical-card" style="display: flex; align-items: center; gap: 14px; border-left: 4px solid #10b981; padding: 16px;">
        <div style="width: 42px; height: 42px; border-radius: 8px; background: rgba(16, 185, 129, 0.15); color: #10b981; display: grid; place-items: center; font-size: 18px;">
          <i class="fa-solid fa-tower-broadcast"></i>
        </div>
        <div>
          <div style="font-size: 11px; text-transform: uppercase; color: #94a3b8; font-weight: 700;">Live Now (Running)</div>
          <div id="stat-live-count" style="font-size: 22px; font-weight: 800; color: #10b981;">{{ $stats['live_exams'] }}</div>
        </div>
      </div>

      <div class="tactical-card" style="display: flex; align-items: center; gap: 14px; border-left: 4px solid #f59e0b; padding: 16px;">
        <div style="width: 42px; height: 42px; border-radius: 8px; background: rgba(245, 158, 11, 0.15); color: #f59e0b; display: grid; place-items: center; font-size: 18px;">
          <i class="fa-solid fa-clock"></i>
        </div>
        <div>
          <div style="font-size: 11px; text-transform: uppercase; color: #94a3b8; font-weight: 700;">Scheduled Exams</div>
          <div id="stat-scheduled-count" style="font-size: 22px; font-weight: 800; color: #f59e0b;">{{ $stats['scheduled_exams'] }}</div>
        </div>
      </div>

      <div class="tactical-card" style="display: flex; align-items: center; gap: 14px; border-left: 4px solid #38bdf8; padding: 16px;">
        <div style="width: 42px; height: 42px; border-radius: 8px; background: rgba(56, 189, 248, 0.15); color: #38bdf8; display: grid; place-items: center; font-size: 18px;">
          <i class="fa-solid fa-file-lines"></i>
        </div>
        <div>
          <div style="font-size: 11px; text-transform: uppercase; color: #94a3b8; font-weight: 700;">Drafts / Created</div>
          <div id="stat-draft-count" style="font-size: 22px; font-weight: 800; color: #38bdf8;">{{ $stats['draft_exams'] ?? 0 }}</div>
        </div>
      </div>

      <div class="tactical-card" style="display: flex; align-items: center; gap: 14px; border-left: 4px solid #64748b; padding: 16px;">
        <div style="width: 42px; height: 42px; border-radius: 8px; background: rgba(100, 116, 139, 0.2); color: #94a3b8; display: grid; place-items: center; font-size: 18px;">
          <i class="fa-solid fa-lock"></i>
        </div>
        <div>
          <div style="font-size: 11px; text-transform: uppercase; color: #94a3b8; font-weight: 700;">Closed / Ended</div>
          <div id="stat-closed-count" style="font-size: 22px; font-weight: 800; color: #cbd5e1;">{{ $stats['closed_exams'] }}</div>
        </div>
      </div>

      <div class="tactical-card" style="display: flex; align-items: center; gap: 14px; border-left: 4px solid #8b5cf6; padding: 16px;">
        <div style="width: 42px; height: 42px; border-radius: 8px; background: rgba(139, 92, 246, 0.15); color: #8b5cf6; display: grid; place-items: center; font-size: 18px;">
          <i class="fa-solid fa-users"></i>
        </div>
        <div>
          <div style="font-size: 11px; text-transform: uppercase; color: #94a3b8; font-weight: 700;">Total Submissions</div>
          <div id="stat-submissions-count" style="font-size: 22px; font-weight: 800; color: #a78bfa;">{{ $stats['total_attempts'] }}</div>
        </div>
      </div>
    </div>

    <!-- Active Operations Control Table -->
    <div class="tactical-card">
      <div style="border-bottom: 1px solid var(--border-soft); padding-bottom: 14px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <div>
          <h3 style="font-size: 17px; font-weight: 800; margin: 0; color: #ffffff; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-sliders" style="color: var(--accent-gold);"></i> {{ $branchName }} Exam Control Desk
          </h3>
        </div>

        <!-- Status Filter Pills -->
        <div style="display: flex; gap: 6px; flex-wrap: wrap;">
          <button type="button" class="badge badge-navy filter-btn active" onclick="filterExams('all', this)" style="cursor: pointer; border: none; padding: 6px 12px; font-size: 11.5px;">
            All (<span id="filter-all-count">{{ $exams->count() }}</span>)
          </button>
          <button type="button" class="badge badge-navy filter-btn" onclick="filterExams('open', this)" style="cursor: pointer; border: none; padding: 6px 12px; font-size: 11.5px;">
            🟢 Live (<span id="filter-live-count">{{ $stats['live_exams'] }}</span>)
          </button>
          <button type="button" class="badge badge-navy filter-btn" onclick="filterExams('scheduled', this)" style="cursor: pointer; border: none; padding: 6px 12px; font-size: 11.5px;">
            🕐 Scheduled (<span id="filter-scheduled-count">{{ $stats['scheduled_exams'] }}</span>)
          </button>
          <button type="button" class="badge badge-navy filter-btn" onclick="filterExams('draft', this)" style="cursor: pointer; border: none; padding: 6px 12px; font-size: 11.5px;">
            📝 Drafts (<span id="filter-draft-count">{{ $stats['draft_exams'] ?? 0 }}</span>)
          </button>
          <button type="button" class="badge badge-navy filter-btn" onclick="filterExams('closed', this)" style="cursor: pointer; border: none; padding: 6px 12px; font-size: 11.5px;">
            🔒 Closed (<span id="filter-closed-count">{{ $stats['closed_exams'] }}</span>)
          </button>
        </div>
      </div>

      <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
          <thead>
            <tr style="border-bottom: 2px solid var(--border-soft); text-align: left;">
              <th style="padding: 12px 10px; color: #94a3b8; font-size: 11px; text-transform: uppercase;">Status</th>
              <th style="padding: 12px 10px; color: #94a3b8; font-size: 11px; text-transform: uppercase;">Exam Paper Details</th>
              <th style="padding: 12px 10px; color: #94a3b8; font-size: 11px; text-transform: uppercase;">Schedule Start & End</th>
              <th style="padding: 12px 10px; color: #94a3b8; font-size: 11px; text-transform: uppercase;">Duration & Questions</th>
              <th style="padding: 12px 10px; color: #94a3b8; font-size: 11px; text-transform: uppercase;">Frontend Visibility</th>
              <th style="padding: 12px 10px; color: #94a3b8; font-size: 11px; text-transform: uppercase; text-align: right;">Operations & Editing</th>
            </tr>
          </thead>
          <tbody id="examTableBody">
            @forelse($exams as $exam)
              <tr class="exam-row" id="exam-row-{{ $exam->id }}" data-status="{{ $exam->status }}" style="border-bottom: 1px solid var(--border-soft); transition: all 0.25s ease;">
                {{-- Status Badge --}}
                <td style="padding: 14px 10px;">
                  @if($exam->status === 'open')
                    <span class="badge badge-emerald" style="display: inline-flex; align-items: center; gap: 5px; font-size: 11px;">
                      <span style="width: 7px; height: 7px; border-radius: 50%; background: #10b981; display: inline-block;"></span> LIVE NOW
                    </span>
                  @elseif($exam->status === 'scheduled')
                    <span class="badge badge-gold" style="display: inline-flex; align-items: center; gap: 5px; font-size: 11px;">
                      <i class="fa-regular fa-clock"></i> SCHEDULED
                    </span>
                  @elseif($exam->status === 'closed')
                    <span class="badge badge-navy" style="font-size: 11px; background: rgba(100, 116, 139, 0.2); color: #94a3b8;">
                      <i class="fa-solid fa-lock"></i> CLOSED / ENDED
                    </span>
                  @else
                    <span class="badge badge-navy" style="font-size: 11px;">{{ strtoupper($exam->status) }}</span>
                  @endif
                </td>

                {{-- Exam Title & Category --}}
                <td style="padding: 14px 10px;">
                  <div style="font-weight: 800; color: #ffffff; font-size: 14.5px;">{{ $exam->title }}</div>
                  <div style="font-size: 11.5px; color: #94a3b8; margin-top: 2px;">
                    <span class="badge badge-navy" style="font-size: 10px; padding: 2px 6px;">{{ $exam->category }}</span>
                    @if($exam->description)
                      <span style="margin-left: 6px; color: #cbd5e1;">{{ Str::limit($exam->description, 50) }}</span>
                    @endif
                  </div>
                </td>

                {{-- Schedule Start & End --}}
                <td style="padding: 14px 10px;">
                  <div style="font-size: 12px; color: #e2e8f0;">
                    <span style="color: #94a3b8;">Start:</span> 
                    <strong>{{ $exam->schedule_start ? $exam->schedule_start->format('M d, Y h:i A') : 'Immediate' }}</strong>
                  </div>
                  <div style="font-size: 12px; color: #e2e8f0; margin-top: 2px;">
                    <span style="color: #94a3b8;">End:</span> 
                    <strong>{{ $exam->schedule_end ? $exam->schedule_end->format('M d, Y h:i A') : 'Manual / Open' }}</strong>
                  </div>

                  @if($exam->isScheduledFuture())
                    <div style="font-size: 11px; color: #f59e0b; font-weight: 700; margin-top: 4px;">
                      <i class="fa-regular fa-clock"></i> Unlocks in {{ $exam->schedule_start->diffForHumans(null, true) }}
                    </div>
                  @endif
                </td>

                {{-- Duration & Questions --}}
                <td style="padding: 14px 10px;">
                  <div style="font-size: 12.5px; color: #ffffff; font-weight: 700;">
                    <i class="fa-regular fa-hourglass-half" style="color: #38bdf8;"></i> 
                    @if($exam->duration_minutes >= 60 && $exam->duration_minutes % 60 === 0)
                      {{ $exam->duration_minutes / 60 }} {{ $exam->duration_minutes === 60 ? 'Hour' : 'Hours' }}
                    @else
                      {{ $exam->duration_minutes }} Mins
                    @endif
                  </div>
                  <div style="font-size: 11.5px; color: #94a3b8; margin-top: 3px;">
                    <strong>{{ $exam->questions_count }}</strong> Questions • Pass: <strong>{{ $exam->pass_marks }}/{{ $exam->total_marks }}</strong>
                  </div>
                </td>

                {{-- Public Frontend Toggle --}}
                <td style="padding: 14px 10px;">
                  <button type="button" 
                    id="branch-toggle-pub-{{ $exam->id }}"
                    onclick="togglePublicBranchAjax({{ $exam->id }}, '{{ route('admin.exams.toggle_public', $exam->id) }}', this)"
                    class="badge {{ $exam->is_public_for_external ? 'badge-emerald' : 'badge-navy' }}" 
                    style="cursor: pointer; border: none; font-size: 11px; padding: 5px 12px;" 
                    title="Click to toggle frontend publication">
                    @if($exam->is_public_for_external)
                      <i class="fa-solid fa-eye"></i> Public on Frontend
                    @else
                      <i class="fa-solid fa-eye-slash"></i> Hidden / Private
                    @endif
                  </button>
                </td>

                {{-- Live Operational Controls --}}
                <td style="padding: 14px 10px; text-align: right;">
                  <div style="display: inline-flex; gap: 8px; align-items: center; flex-wrap: wrap; justify-content: flex-end;">
                    
                    {{-- EDIT BUTTON (Goes to the unified exam editor) --}}
                    <a href="{{ route('admin.exams.edit', $exam->id) }}" class="btn-tactical btn-tactical-gold" style="padding: 6px 12px; font-size: 12px; font-weight: 700;" title="Edit Exam Name, Timing, Frontend Details & Questions">
                      <i class="fa-solid fa-pen-to-square"></i> Edit Exam
                    </a>

                    {{-- START NOW / END NOW (AJAX without reload) --}}
                    <span id="start-end-btn-wrap-{{ $exam->id }}">
                      @if($exam->status !== 'open')
                        <button type="button" 
                          onclick="startNowBranchAjax({{ $exam->id }}, '{{ route('admin.exams.start_now', $exam->id) }}', this)"
                          class="btn-tactical btn-tactical-primary" style="padding: 6px 12px; font-size: 11.5px;">
                          <i class="fa-solid fa-play"></i> Start Now
                        </button>
                      @else
                        <button type="button" 
                          onclick="endNowBranchAjax({{ $exam->id }}, '{{ route('admin.exams.end_now', $exam->id) }}', this)"
                          class="btn-tactical btn-tactical-outline" style="padding: 6px 12px; font-size: 11.5px; border-color: #ef4444; color: #ef4444;">
                          <i class="fa-solid fa-stop"></i> End Now
                        </button>
                      @endif
                    </span>

                    {{-- ADJUST SCHEDULE MODAL BUTTON --}}
                    <button type="button" class="btn-tactical btn-tactical-outline" style="padding: 6px 10px; font-size: 11.5px;" onclick="openScheduleModal(this)" data-exam-id="{{ $exam->id }}" data-exam-title="{{ addslashes($exam->title) }}" data-schedule-start="{{ $exam->schedule_start ? $exam->schedule_start->format('Y-m-d\TH:i') : '' }}" data-schedule-end="{{ $exam->schedule_end ? $exam->schedule_end->format('Y-m-d\TH:i') : '' }}" data-status="{{ $exam->status }}" data-is-public="{{ $exam->is_public_for_external ? 1 : 0 }}" data-update-url="{{ route('admin.exams.update_schedule', $exam->id) }}">
                      <i class="fa-regular fa-calendar-days"></i> Schedule
                    </button>

                    <a href="{{ route('admin.exams.questions', $exam->id) }}" class="btn-tactical btn-tactical-outline" style="padding: 6px 10px; font-size: 11.5px;" title="Manage Questions in Paper">
                      <i class="fa-solid fa-list-ol"></i> {{ $exam->questions_count }} Qs
                    </a>

                    <a href="{{ route('admin.exams.attempts', ['exam_id' => $exam->id]) }}" class="btn-tactical btn-tactical-outline" style="padding: 6px 10px; font-size: 11.5px;" title="View Candidate Submissions">
                      <i class="fa-solid fa-users"></i> {{ $exam->attempts_count }}
                    </a>

                    <button type="button" class="btn-tactical btn-tactical-outline btn-delete-exam" 
                      id="del-btn-{{ $exam->id }}"
                      onclick="deleteExamAjax({{ $exam->id }}, '{{ addslashes($exam->title) }}', '{{ $exam->status }}', '{{ route('admin.exams.destroy', $exam->id) }}', this)"
                      style="padding: 6px 8px; font-size: 11.5px; color: #ef4444; border-color: rgba(239, 68, 68, 0.3);" title="Delete Exam Paper">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" style="text-align: center; padding: 40px; color: #94a3b8;">
                  <i class="fa-solid fa-sliders" style="font-size: 32px; opacity: 0.4; margin-bottom: 8px; display: block;"></i>
                  No exams created yet for {{ $branchName }}. Switch to the <strong>"Create New Exam from PDF"</strong> tab above to create your first exam paper.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>


  <!-- ================================================================= -->
  <!-- PLACE 2: CREATE NEW EXAM FROM PDF (Clean 3-Step Wizard Form)      -->
  <!-- ================================================================= -->
  <div id="tab-content-creation" style="display: {{ $activeTab === 'creation' ? 'flex' : 'none' }}; flex-direction: column; gap: 24px;">

    <!-- Wizard Steps Header Indicator -->
    <div class="tactical-card" style="padding: 16px 24px; border-left: 4px solid #10b981;">
      <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
        <div>
          <h3 style="font-size: 18px; font-weight: 800; color: #ffffff; margin: 0; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-wand-magic-sparkles" style="color: #10b981;"></i> {{ $branchName }} Exam Creation Studio
          </h3>
        </div>

        <!-- 3-Step Progress Indicators -->
        <div style="display: flex; align-items: center; gap: 8px;">
          <div id="wiz-badge-1" style="display: flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 6px; background: rgba(16, 185, 129, 0.2); color: #10b981; font-weight: 800; font-size: 12px;">
            <span style="width: 18px; height: 18px; border-radius: 50%; background: #10b981; color: #0f172a; display: grid; place-items: center; font-size: 11px;">1</span> Upload PDF
          </div>
          <i class="fa-solid fa-chevron-right" style="color: #475569; font-size: 11px;"></i>
          <div id="wiz-badge-2" style="display: flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 6px; background: rgba(255,255,255,0.05); color: #94a3b8; font-weight: 700; font-size: 12px;">
            <span style="width: 18px; height: 18px; border-radius: 50%; background: #475569; color: #ffffff; display: grid; place-items: center; font-size: 11px;">2</span> Questions Preview
          </div>
          <i class="fa-solid fa-chevron-right" style="color: #475569; font-size: 11px;"></i>
          <div id="wiz-badge-3" style="display: flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 6px; background: rgba(255,255,255,0.05); color: #94a3b8; font-weight: 700; font-size: 12px;">
            <span style="width: 18px; height: 18px; border-radius: 50%; background: #475569; color: #ffffff; display: grid; place-items: center; font-size: 11px;">3</span> Exam Setup & Publish
          </div>
        </div>
      </div>
    </div>

    <!-- ───────────────────────────────────────────────────────────── -->
    <!-- WIZARD STEP 1: UPLOAD & SCAN PDF                             -->
    <!-- ───────────────────────────────────────────────────────────── -->
    <div id="wizard-step-1" class="tactical-card" style="padding: 28px;">
      <div style="border-bottom: 1px solid var(--border-soft); padding-bottom: 14px; margin-bottom: 22px;">
        <h4 style="font-size: 16px; font-weight: 800; margin: 0; color: #ffffff; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-file-pdf" style="color: #ef4444;"></i> Step 1: Upload MCQ PDF Document
        </h4>
      </div>

      <form id="scanPdfForm" enctype="multipart/form-data">
        @csrf
        <!-- Drag & Drop Zone -->
        <div id="dropZone" style="border: 2px dashed #475569; border-radius: 12px; padding: 36px 20px; text-align: center; background: rgba(255,255,255,0.02); cursor: pointer; transition: all 0.2s ease;">
          <i class="fa-solid fa-cloud-arrow-up" style="font-size: 44px; color: #38bdf8; margin-bottom: 12px; display: block;"></i>
          <h4 id="dropZoneFileName" style="font-weight: 800; font-size: 15px; color: #ffffff; margin: 0 0 6px 0;">
            Click to Browse or Drag & Drop MCQ PDF Here
          </h4>
          <p style="font-size: 12px; color: #94a3b8; margin: 0 0 16px 0;">
            PDF format up to 50MB
          </p>
          <input type="file" id="pdfFileInput" name="pdf_file" accept=".pdf" style="display: none;" onchange="handleFileSelected(this)">
          <button type="button" class="btn-tactical btn-tactical-outline" onclick="document.getElementById('pdfFileInput').click()">
            <i class="fa-solid fa-folder-open"></i> Choose PDF File
          </button>
        </div>

        <!-- Raw Text Paste Expander -->
        <div style="margin-top: 16px;">
          <button type="button" onclick="toggleRawText()" style="background: none; border: none; color: #38bdf8; cursor: pointer; font-size: 12.5px; font-weight: 700; display: flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-chevron-down" id="rawTextArrow"></i> Or paste raw question text directly
          </button>
          <div id="rawTextContainer" style="display: none; margin-top: 10px;">
            <textarea id="rawTextInput" name="raw_text" rows="5" class="form-tactical" placeholder="1. Question prompt&#10;A) Option 1&#10;B) Option 2&#10;C) Option 3&#10;D) Option 4&#10;Ans: B"></textarea>
          </div>
        </div>

        <!-- Scan Progress Loader -->
        <div id="scanLoader" style="display: none; margin-top: 20px; background: rgba(56, 189, 248, 0.08); border: 1px solid rgba(56, 189, 248, 0.25); border-radius: 8px; padding: 18px; text-align: center;">
          <i class="fa-solid fa-spinner fa-spin" style="font-size: 26px; color: #38bdf8; margin-bottom: 8px; display: block;"></i>
          <div style="font-weight: 800; color: #ffffff; font-size: 14px;">Scanning Document Questions...</div>
        </div>

        <!-- Scan Error Display -->
        <div id="scanError" style="display: none; margin-top: 16px; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 8px; padding: 14px; color: #fca5a5; font-size: 13px;">
        </div>

        <!-- Action Bar -->
        <div style="display: flex; justify-content: flex-end; margin-top: 22px; border-top: 1px solid var(--border-soft); padding-top: 18px;">
          <button type="button" id="btnScanPdf" onclick="scanPdf()" class="btn-tactical btn-tactical-primary" style="padding: 12px 28px; font-size: 14px; font-weight: 800;">
            <i class="fa-solid fa-magnifying-glass"></i> Scan PDF Questions
          </button>
        </div>
      </form>
    </div>

    <!-- ───────────────────────────────────────────────────────────── -->
    <!-- WIZARD STEP 2: SCANNED QUESTIONS PREVIEW & VERIFICATION       -->
    <!-- ───────────────────────────────────────────────────────────── -->
    <div id="wizard-step-2" class="tactical-card" style="display: none; padding: 24px;">
      <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-soft); padding-bottom: 14px; margin-bottom: 18px; flex-wrap: wrap; gap: 12px;">
        <div>
          <span class="badge badge-emerald" style="font-size: 11px; margin-bottom: 4px;">SCAN COMPLETE</span>
          <h4 style="font-size: 17px; font-weight: 800; margin: 0; color: #ffffff; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-list-check" style="color: #10b981;"></i> Step 2: Extracted Questions Review (<span id="previewCountBadge">0</span>)
          </h4>
          <p style="font-size: 12px; color: #94a3b8; margin: 2px 0 0 0;">
            All scanned questions are listed below. Click <strong>"Proceed to Exam Setup"</strong> to configure exam timing and select how many questions to include.
          </p>
        </div>

        <div style="display: flex; gap: 10px; align-items: center;">
          <button type="button" onclick="goToStep(1)" class="btn-tactical btn-tactical-outline" style="font-size: 12px;">
            <i class="fa-solid fa-rotate-left"></i> Re-upload PDF
          </button>
          <button type="button" onclick="goToStep(3)" class="btn-tactical btn-tactical-gold" style="font-size: 13.5px; font-weight: 800; padding: 9px 20px;">
            Proceed to Exam Setup <i class="fa-solid fa-arrow-right"></i>
          </button>
        </div>
      </div>

      <!-- Quick Search Bar in Preview -->
      <div style="margin-bottom: 14px;">
        <input type="text" id="previewSearchInput" oninput="filterPreviewQuestions()" placeholder="Search scanned questions..." class="form-tactical" style="max-width: 340px; font-size: 12.5px;">
      </div>

      <!-- Scanned Questions Scroll Container -->
      <div id="scannedQuestionsList" style="max-height: 520px; overflow-y: auto; display: flex; flex-direction: column; gap: 12px; padding-right: 6px;">
      </div>

      <!-- Bottom Proceed Action -->
      <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; border-top: 1px solid var(--border-soft); padding-top: 16px;">
        <button type="button" onclick="goToStep(1)" class="btn-tactical btn-tactical-outline">
          <i class="fa-solid fa-arrow-left"></i> Back to Upload
        </button>
        <button type="button" onclick="goToStep(3)" class="btn-tactical btn-tactical-gold" style="padding: 11px 26px; font-size: 14px; font-weight: 800;">
          Proceed to Exam Setup & Frontend Details <i class="fa-solid fa-arrow-right"></i>
        </button>
      </div>
    </div>

    <!-- ───────────────────────────────────────────────────────────── -->
    <!-- WIZARD STEP 3: EXAM CONFIGURATION & FRONTEND SETUP            -->
    <!-- ───────────────────────────────────────────────────────────── -->
    <div id="wizard-step-3" class="tactical-card" style="display: none; padding: 28px;">
      <div style="border-bottom: 1px solid var(--border-soft); padding-bottom: 14px; margin-bottom: 22px;">
        <span class="badge badge-gold" style="font-size: 11px; margin-bottom: 4px;">FINAL CONFIGURATION</span>
        <h4 style="font-size: 17px; font-weight: 800; margin: 0 0 4px 0; color: #ffffff; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-gear" style="color: var(--accent-gold);"></i> Step 3: Exam Parameters & Front-End Setup
        </h4>
        <p style="font-size: 12.5px; color: #94a3b8; margin: 0;">
          Configure the exam name, duration, date/time schedule, and front-end details shown to candidates. Select how many questions to randomly pick from the scanned PDF.
        </p>
      </div>

      <form action="{{ route('admin.exams.create_from_pdf', $branch) }}" method="POST">
        @csrf
        <input type="hidden" name="questions_json" id="finalQuestionsJson">

        <div style="display: flex; flex-direction: column; gap: 20px;">
          
          <!-- Section 1: Identity & Category -->
          <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 14px;">
            <div>
              <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px; color: #60a5fa;">
                <i class="fa-solid fa-heading"></i> Exam Title / Name (Shown on Frontend) *
              </label>
              <input type="text" name="title" id="createExamTitle" value="{{ $branchName }} Preliminary IQ Assessment" class="form-tactical" style="font-size: 14.5px; font-weight: 700;" required>
            </div>

            <div>
              <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px; color: #94a3b8;">Category / Domain *</label>
              <input type="text" name="category" value="Verbal IQ & Aptitude" class="form-tactical" required>
            </div>
          </div>

          <input type="hidden" name="exam_type" value="iq_mcq">

          <!-- Section 2: Question Sampling & Duration Box -->
          <div style="background: rgba(56, 189, 248, 0.06); border: 1px solid rgba(56, 189, 248, 0.25); border-radius: 10px; padding: 18px;">
            <div style="font-weight: 800; font-size: 13.5px; color: #38bdf8; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
              <i class="fa-solid fa-dice"></i> Question Sampling from Uploaded PDF & Duration
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
              <div>
                <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px; color: #94a3b8;">
                  Questions to Randomly Select for Exam *
                </label>
                <div style="display: flex; gap: 8px; align-items: center;">
                  <input type="number" name="question_count" id="questionCountInput" value="100" min="1" max="1000" class="form-tactical" style="font-size: 16px; font-weight: 800; width: 140px;" required>
                  <span style="font-size: 12px; color: #94a3b8;">out of <strong id="availableScannedBadge" style="color: #38bdf8;">0</strong> scanned questions</span>
                </div>
              </div>

              <div>
                <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px; color: #94a3b8;">
                  Exam Duration *
                </label>
                <div style="display: flex; gap: 6px; margin-bottom: 8px; flex-wrap: wrap;">
                  <button type="button" class="badge badge-navy duration-pill" onclick="setDuration(30, this)" style="cursor: pointer; border: none; padding: 5px 10px; font-size: 11px;">30 Mins</button>
                  <button type="button" class="badge badge-navy duration-pill" onclick="setDuration(45, this)" style="cursor: pointer; border: none; padding: 5px 10px; font-size: 11px;">45 Mins</button>
                  <button type="button" class="badge badge-navy duration-pill active" onclick="setDuration(60, this)" style="cursor: pointer; border: none; padding: 5px 10px; font-size: 11px;">1 Hour (60m)</button>
                  <button type="button" class="badge badge-navy duration-pill" onclick="setDuration(90, this)" style="cursor: pointer; border: none; padding: 5px 10px; font-size: 11px;">1.5 Hours (90m)</button>
                  <button type="button" class="badge badge-navy duration-pill" onclick="setDuration(120, this)" style="cursor: pointer; border: none; padding: 5px 10px; font-size: 11px;">2 Hours (120m)</button>
                </div>
                <input type="number" name="duration_minutes" id="durationInput" value="60" min="1" max="720" class="form-tactical" placeholder="Minutes" required>
              </div>
            </div>
          </div>

          <!-- Section 3: Schedule Start & End Timings -->
          <div style="background: rgba(16, 185, 129, 0.06); border: 1px solid rgba(16, 185, 129, 0.25); border-radius: 10px; padding: 18px;">
            <div style="font-weight: 800; font-size: 13.5px; color: #34d399; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
              <i class="fa-regular fa-clock"></i> Schedule Timings
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px;">
              <div>
                <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px; color: #60a5fa;">
                  <i class="fa-regular fa-calendar-check"></i> Scheduled Start Date & Time
                </label>
                <input type="datetime-local" name="schedule_start" value="{{ now()->addHours(1)->format('Y-m-d\TH:i') }}" class="form-tactical">
              </div>

              <div>
                <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px; color: #f87171;">
                  <i class="fa-regular fa-calendar-xmark"></i> Scheduled End Date & Time (Optional)
                </label>
                <input type="datetime-local" name="schedule_end" class="form-tactical">
              </div>

              <div>
                <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px; color: #94a3b8;">
                  Initial Status *
                </label>
                <select name="status" class="form-tactical" required>
                  <option value="scheduled" selected>🕐 Scheduled (Locked with Countdown)</option>
                  <option value="open">🟢 Open (Live Now for Candidates)</option>
                  <option value="draft">📝 Draft (Private / Admin Only)</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Section 4: Front-End Box & Candidate Rules -->
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
            <div>
              <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px; color: #94a3b8;">
                Exam Description (Shown in Front-End Card)
              </label>
              <textarea name="description" rows="3" class="form-tactical" placeholder="Brief summary of this examination for candidates...">Computerized preliminary screening examination for candidates. Tests verbal reasoning and logical IQ aptitude.</textarea>
            </div>

            <div>
              <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px; color: #94a3b8;">
                Candidate Instructions (Shown Before Test Starts)
              </label>
              <textarea name="instructions" rows="3" class="form-tactical" placeholder="Rules and regulations...">Calculators and external smart devices are strictly prohibited. Each incorrect response incurs negative marks. Test auto-submits when time expires.</textarea>
            </div>
          </div>

          <!-- Section 5: Scoring Parameters & Frontend Toggle -->
          <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px;">
            <div>
              <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px; color: #94a3b8;">Marks / Question *</label>
              <input type="number" step="0.5" name="marks_per_question" value="1.0" class="form-tactical" required>
            </div>
            <div>
              <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px; color: #94a3b8;">Negative Mark / Wrong</label>
              <input type="number" step="0.05" name="negative_marking_per_wrong" value="0.25" class="form-tactical">
            </div>
            <div>
              <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px; color: #94a3b8;">Pass Marks *</label>
              <input type="number" step="1" name="pass_marks" value="40" class="form-tactical" required>
            </div>
            <div>
              <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px; color: #94a3b8;">External Fee (৳)</label>
              <input type="number" step="1" name="fee" value="0" class="form-tactical" placeholder="0 = Free">
            </div>
          </div>

          <!-- Public on Frontend Checkbox -->
          <div style="display: flex; align-items: center; gap: 10px; background: var(--surface-subtle); padding: 12px 16px; border-radius: 8px; border: 1px solid var(--border-soft);">
            <input type="checkbox" name="is_public_for_external" value="1" id="create-public-cb" checked style="width: 18px; height: 18px; accent-color: #10b981;">
            <label for="create-public-cb" style="font-size: 13px; font-weight: 700; cursor: pointer; color: #ffffff;">
              <i class="fa-solid fa-eye" style="color: #10b981;"></i> Make Public on Frontend Assessment Gateway (/online-tests)
            </label>
          </div>

          <!-- Action Buttons -->
          <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px; border-top: 1px solid var(--border-soft); padding-top: 18px;">
            <button type="button" onclick="goToStep(2)" class="btn-tactical btn-tactical-outline">
              <i class="fa-solid fa-arrow-left"></i> Back to Questions Preview
            </button>
            <button type="submit" class="btn-tactical btn-tactical-primary" style="padding: 12px 34px; font-size: 15px; font-weight: 800;">
              <i class="fa-solid fa-rocket"></i> Create & Publish Exam Paper
            </button>
          </div>
        </div>
      </form>
    </div>

  </div>

</div>


<!-- ==================================================== -->
<!-- MODAL: ADJUST SCHEDULE & OPERATIONAL CONTROLS        -->
<!-- ==================================================== -->
<div id="scheduleModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 9999; justify-content: center; align-items: center; padding: 20px;">
  <div class="tactical-card" style="max-width: 520px; width: 100%; background: #0f172a; border: 1px solid var(--border-soft); border-radius: 12px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);">
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-soft); padding-bottom: 14px; margin-bottom: 18px;">
      <h3 style="font-size: 16px; font-weight: 800; margin: 0; color: #ffffff; display: flex; align-items: center; gap: 8px;">
        <i class="fa-regular fa-clock" style="color: var(--accent-gold);"></i> Adjust Schedule & Operational Timers
      </h3>
      <button type="button" onclick="document.getElementById('scheduleModal').style.display='none'" style="background: none; border: none; font-size: 18px; cursor: pointer; color: #94a3b8;">✕</button>
    </div>

    <form id="scheduleForm" method="POST">
      @csrf

      <div style="display: flex; flex-direction: column; gap: 14px;">
        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px; color: #94a3b8;">Exam Paper</label>
          <div id="modal-exam-title" style="font-weight: 800; color: #ffffff; font-size: 14px;"></div>
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px; color: #60a5fa;">
            <i class="fa-regular fa-calendar-check"></i> Scheduled Start Time
          </label>
          <input type="datetime-local" name="schedule_start" id="modal-schedule-start" class="form-tactical">
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px; color: #f87171;">
            <i class="fa-regular fa-calendar-xmark"></i> Scheduled End Time (Optional)
          </label>
          <input type="datetime-local" name="schedule_end" id="modal-schedule-end" class="form-tactical">
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px; color: #94a3b8;">Operational Status</label>
          <select name="status" id="modal-status" class="form-tactical">
            <option value="scheduled">Scheduled (Countdown Locked)</option>
            <option value="open">Open (Live Now)</option>
            <option value="closed">Closed / Ended</option>
            <option value="draft">Draft (Private)</option>
          </select>
        </div>

        <div style="display: flex; align-items: center; gap: 10px; background: var(--surface-subtle); padding: 10px 12px; border-radius: 6px; border: 1px solid var(--border-soft);">
          <input type="checkbox" name="is_public_for_external" value="1" id="modal-public-cb" style="width: 18px; height: 18px; accent-color: #10b981;">
          <label for="modal-public-cb" style="font-size: 12.5px; font-weight: 700; cursor: pointer; color: #ffffff;">
            Public on Frontend Assessment Gateway (/online-tests)
          </label>
        </div>

        <div style="display: flex; gap: 10px; justify-content: flex-end; border-top: 1px solid var(--border-soft); padding-top: 16px;">
          <button type="button" onclick="document.getElementById('scheduleModal').style.display='none'" class="btn-tactical btn-tactical-outline">Cancel</button>
          <button type="submit" id="btnSaveSchedule" class="btn-tactical btn-tactical-primary">
            <i class="fa-solid fa-check"></i> Save Schedule
          </button>
        </div>
      </div>
    </form>
  </div>
</div>


<!-- ========================================== -->
<!-- JAVASCRIPT: TAB SWITCHING & WIZARD ENGINE  -->
<!-- ========================================== -->
<script>
let scannedQuestionsData = [];

function switchTab(tab) {
  const controlContent = document.getElementById('tab-content-control');
  const creationContent = document.getElementById('tab-content-creation');
  const btnControl = document.getElementById('tab-btn-control');
  const btnCreation = document.getElementById('tab-btn-creation');

  if (tab === 'control') {
    controlContent.style.display = 'flex';
    creationContent.style.display = 'none';
    btnControl.style.border = '2px solid #f59e0b';
    btnControl.style.background = 'rgba(245, 158, 11, 0.14)';
    btnCreation.style.border = '2px solid rgba(255, 255, 255, 0.08)';
    btnCreation.style.background = '#141722';
  } else {
    controlContent.style.display = 'none';
    creationContent.style.display = 'flex';
    btnCreation.style.border = '2px solid #10b981';
    btnCreation.style.background = 'rgba(16, 185, 129, 0.14)';
    btnControl.style.border = '2px solid rgba(255, 255, 255, 0.08)';
    btnControl.style.background = '#141722';
  }

  // Update URL without page reload
  const url = new URL(window.location);
  url.searchParams.set('tab', tab);
  window.history.replaceState({}, '', url);

  if (window.MathJax && window.MathJax.typesetPromise) {
    MathJax.typesetPromise().catch(function() {});
  }
}

function handleFileSelected(input) {
  if (input.files && input.files[0]) {
    const file = input.files[0];
    document.getElementById('dropZoneFileName').innerHTML = 
      `<span style="color: #34d399;"><i class="fa-solid fa-file-pdf"></i> ${file.name}</span> (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
  }
}

function toggleRawText() {
  const container = document.getElementById('rawTextContainer');
  const arrow = document.getElementById('rawTextArrow');
  if (container.style.display === 'none') {
    container.style.display = 'block';
    arrow.className = 'fa-solid fa-chevron-up';
  } else {
    container.style.display = 'none';
    arrow.className = 'fa-solid fa-chevron-down';
  }
}

function goToStep(step) {
  document.getElementById('wizard-step-1').style.display = (step === 1) ? 'block' : 'none';
  document.getElementById('wizard-step-2').style.display = (step === 2) ? 'block' : 'none';
  document.getElementById('wizard-step-3').style.display = (step === 3) ? 'block' : 'none';

  // Update badges
  for (let i = 1; i <= 3; i++) {
    const badge = document.getElementById('wiz-badge-' + i);
    if (i === step) {
      badge.style.background = 'rgba(16, 185, 129, 0.2)';
      badge.style.color = '#10b981';
      badge.querySelector('span').style.background = '#10b981';
      badge.querySelector('span').style.color = '#0f172a';
    } else if (i < step) {
      badge.style.background = 'rgba(56, 189, 248, 0.15)';
      badge.style.color = '#38bdf8';
      badge.querySelector('span').style.background = '#38bdf8';
      badge.querySelector('span').style.color = '#0f172a';
    } else {
      badge.style.background = 'rgba(255,255,255,0.05)';
      badge.style.color = '#94a3b8';
      badge.querySelector('span').style.background = '#475569';
      badge.querySelector('span').style.color = '#ffffff';
    }
  }

  window.scrollTo({ top: document.getElementById('tab-content-creation').offsetTop - 30, behavior: 'smooth' });

  if (window.MathJax && window.MathJax.typesetPromise) {
    MathJax.typesetPromise().catch(function() {});
  }
}

async function scanPdf() {
  const fileInput = document.getElementById('pdfFileInput');
  const rawTextInput = document.getElementById('rawTextInput');
  const loader = document.getElementById('scanLoader');
  const errorDiv = document.getElementById('scanError');
  const btn = document.getElementById('btnScanPdf');

  errorDiv.style.display = 'none';

  if ((!fileInput.files || !fileInput.files[0]) && (!rawTextInput.value || rawTextInput.value.trim().length === 0)) {
    errorDiv.textContent = 'Please choose a PDF file or paste question text before scanning.';
    errorDiv.style.display = 'block';
    return;
  }

  loader.style.display = 'block';
  btn.disabled = true;

  const formData = new FormData();
  if (fileInput.files && fileInput.files[0]) {
    formData.append('pdf_file', fileInput.files[0]);
  }
  if (rawTextInput.value && rawTextInput.value.trim().length > 0) {
    formData.append('raw_text', rawTextInput.value);
  }

  try {
    const response = await fetch("{{ route('admin.exams.scan_pdf', $branch) }}", {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json',
      },
      body: formData
    });

    const data = await response.json();
    loader.style.display = 'none';
    btn.disabled = false;

    if (!data.success) {
      errorDiv.textContent = data.message || 'Scanning failed. Please verify format.';
      errorDiv.style.display = 'block';
      return;
    }

    scannedQuestionsData = data.questions || [];
    renderScannedPreview(scannedQuestionsData);

    // Set badge counts
    document.getElementById('previewCountBadge').textContent = scannedQuestionsData.length;
    document.getElementById('availableScannedBadge').textContent = scannedQuestionsData.length;
    document.getElementById('finalQuestionsJson').value = JSON.stringify(scannedQuestionsData);

    // Default question count: 100 or less
    const defaultPick = Math.min(100, scannedQuestionsData.length);
    document.getElementById('questionCountInput').value = defaultPick;
    document.getElementById('questionCountInput').max = scannedQuestionsData.length;

    goToStep(2);
  } catch (err) {
    loader.style.display = 'none';
    btn.disabled = false;
    errorDiv.textContent = 'Network or processing error during scan: ' + err.message;
    errorDiv.style.display = 'block';
  }
}

function renderScannedPreview(questions) {
  const container = document.getElementById('scannedQuestionsList');
  container.innerHTML = '';

  if (!questions || questions.length === 0) {
    container.innerHTML = '<div style="text-align: center; color: #94a3b8; padding: 30px;">No questions to preview.</div>';
    return;
  }

  questions.forEach((q, idx) => {
    const card = document.createElement('div');
    card.className = 'scanned-q-item';
    card.dataset.text = (q.question_text || '').toLowerCase();
    card.style = 'background: var(--surface-subtle); border: 1px solid var(--border-soft); border-radius: 8px; padding: 14px 16px;';

    card.innerHTML = `
      <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
        <div style="display: flex; align-items: center; gap: 8px;">
          <span style="font-weight: 800; color: #38bdf8; font-size: 13.5px;">#${q.index || (idx + 1)}</span>
          <span class="badge ${q.is_valid ? 'badge-emerald' : 'badge-gold'}" style="font-size: 10.5px;">
            ${q.is_valid ? 'VALID' : 'REVIEW'}
          </span>
        </div>
        <div style="font-size: 12px; color: #94a3b8;">
          Key: <strong style="color: #10b981; font-size: 13.5px; background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; padding: 2px 8px; border-radius: 4px;">${q.correct_answer || 'NONE'}</strong>
        </div>
      </div>
      <p style="font-weight: 700; font-size: 13.5px; margin: 0 0 10px 0; color: #ffffff; line-height: 1.5;">
        ${escapeHtml(q.question_text || '')}
      </p>
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 8px; font-size: 12px;">
        <div style="padding: 6px 10px; border-radius: 5px; background: ${q.correct_answer === 'A' ? 'rgba(16,185,129,0.15)' : 'rgba(255,255,255,0.02)'}; border: 1px solid ${q.correct_answer === 'A' ? '#10b981' : 'var(--border-soft)'};">
          <strong style="color: ${q.correct_answer === 'A' ? '#34d399' : '#94a3b8'};">A)</strong> ${escapeHtml(q.option_a || '')}
        </div>
        <div style="padding: 6px 10px; border-radius: 5px; background: ${q.correct_answer === 'B' ? 'rgba(16,185,129,0.15)' : 'rgba(255,255,255,0.02)'}; border: 1px solid ${q.correct_answer === 'B' ? '#10b981' : 'var(--border-soft)'};">
          <strong style="color: ${q.correct_answer === 'B' ? '#34d399' : '#94a3b8'};">B)</strong> ${escapeHtml(q.option_b || '')}
        </div>
        <div style="padding: 6px 10px; border-radius: 5px; background: ${q.correct_answer === 'C' ? 'rgba(16,185,129,0.15)' : 'rgba(255,255,255,0.02)'}; border: 1px solid ${q.correct_answer === 'C' ? '#10b981' : 'var(--border-soft)'};">
          <strong style="color: ${q.correct_answer === 'C' ? '#34d399' : '#94a3b8'};">C)</strong> ${escapeHtml(q.option_c || '')}
        </div>
        <div style="padding: 6px 10px; border-radius: 5px; background: ${q.correct_answer === 'D' ? 'rgba(16,185,129,0.15)' : 'rgba(255,255,255,0.02)'}; border: 1px solid ${q.correct_answer === 'D' ? '#10b981' : 'var(--border-soft)'};">
          <strong style="color: ${q.correct_answer === 'D' ? '#34d399' : '#94a3b8'};">D)</strong> ${escapeHtml(q.option_d || '')}
        </div>
      </div>
      ${q.explanation ? `<div style="margin-top: 8px; font-size: 11.5px; color: #f59e0b; background: rgba(245, 158, 11, 0.08); padding: 6px 10px; border-radius: 4px;"><strong>Explanation:</strong> ${escapeHtml(q.explanation)}</div>` : ''}
    `;

    container.appendChild(card);
  });
}

function filterPreviewQuestions() {
  const query = (document.getElementById('previewSearchInput').value || '').toLowerCase();
  document.querySelectorAll('.scanned-q-item').forEach(item => {
    item.style.display = (item.dataset.text.includes(query)) ? 'block' : 'none';
  });
}

function setDuration(minutes, btn) {
  document.getElementById('durationInput').value = minutes;
  document.querySelectorAll('.duration-pill').forEach(p => p.classList.remove('active'));
  if (btn) btn.classList.add('active');
}

function filterExams(status, btn) {
  document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
  if (btn) btn.classList.add('active');

  document.querySelectorAll('.exam-row').forEach(row => {
    if (status === 'all' || row.dataset.status === status) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
}

function openScheduleModal(btn) {
  document.getElementById('scheduleForm').action = btn.dataset.updateUrl;
  document.getElementById('modal-exam-title').textContent = btn.dataset.examTitle;
  document.getElementById('modal-schedule-start').value = btn.dataset.scheduleStart;
  document.getElementById('modal-schedule-end').value = btn.dataset.scheduleEnd;
  document.getElementById('modal-status').value = btn.dataset.status;
  document.getElementById('modal-public-cb').checked = btn.dataset.isPublic === '1';
  document.getElementById('scheduleModal').style.display = 'flex';
}

document.addEventListener('DOMContentLoaded', function() {
  const schedForm = document.getElementById('scheduleForm');
  if (schedForm) {
    schedForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const saveBtn = document.getElementById('btnSaveSchedule');
      const origHtml = saveBtn ? saveBtn.innerHTML : '';
      if (saveBtn) {
        saveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';
        saveBtn.disabled = true;
      }

      const formData = new FormData(schedForm);

      fetch(schedForm.action, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: formData
      })
      .then(res => {
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res.json();
      })
      .then(data => {
        if (saveBtn) {
          saveBtn.innerHTML = '<i class="fa-solid fa-circle-check"></i> Saved!';
        }
        setTimeout(() => {
          document.getElementById('scheduleModal').style.display = 'none';
          if (saveBtn) {
            saveBtn.innerHTML = origHtml;
            saveBtn.disabled = false;
          }
        }, 500);

        if (data.success && data.exam) {
          const exam = data.exam;
          const row = document.getElementById('exam-row-' + exam.id);
          if (row) {
            const statusCell = row.cells[0];
            if (statusCell) {
              if (exam.status === 'open') {
                statusCell.innerHTML = `<span class="badge badge-emerald" style="display: inline-flex; align-items: center; gap: 5px; font-size: 11px;"><span style="width: 7px; height: 7px; border-radius: 50%; background: #10b981; display: inline-block;"></span> LIVE NOW</span>`;
              } else if (exam.status === 'scheduled') {
                statusCell.innerHTML = `<span class="badge badge-gold" style="display: inline-flex; align-items: center; gap: 5px; font-size: 11px;"><i class="fa-regular fa-clock"></i> SCHEDULED</span>`;
              } else if (exam.status === 'closed') {
                statusCell.innerHTML = `<span class="badge badge-navy" style="font-size: 11px; background: rgba(100, 116, 139, 0.2); color: #94a3b8;"><i class="fa-solid fa-lock"></i> CLOSED / ENDED</span>`;
              } else {
                statusCell.innerHTML = `<span class="badge badge-navy" style="font-size: 11px;">${(exam.status || '').toUpperCase()}</span>`;
              }
            }

            const schedBtn = row.querySelector('button[onclick="openScheduleModal(this)"]');
            if (schedBtn) {
              schedBtn.dataset.scheduleStart = exam.schedule_start ? exam.schedule_start.substring(0, 16) : '';
              schedBtn.dataset.scheduleEnd = exam.schedule_end ? exam.schedule_end.substring(0, 16) : '';
              schedBtn.dataset.status = exam.status;
              schedBtn.dataset.isPublic = exam.is_public_for_external ? '1' : '0';
            }

            const pubBtn = document.getElementById('branch-toggle-pub-' + exam.id);
            if (pubBtn) {
              if (exam.is_public_for_external) {
                pubBtn.className = 'badge badge-emerald';
                pubBtn.innerHTML = '<i class="fa-solid fa-eye"></i> Public on Frontend';
              } else {
                pubBtn.className = 'badge badge-navy';
                pubBtn.innerHTML = '<i class="fa-solid fa-eye-slash"></i> Hidden / Private';
              }
            }
          }

          if (window.showToast) {
            window.showToast(data.message || 'Schedule updated successfully.', 'success');
          }
        }
      })
      .catch(err => {
        if (saveBtn) {
          saveBtn.innerHTML = origHtml;
          saveBtn.disabled = false;
        }
        alert('An error occurred while saving the schedule.');
      });
    });
  }
});

function togglePublicBranchAjax(examId, url, btn) {
  const origHtml = btn.innerHTML;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
  btn.disabled = true;

  fetch(url, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    }
  })
  .then(res => {
    if (!res.ok) throw new Error('HTTP ' + res.status);
    return res.json();
  })
  .then(data => {
    btn.disabled = false;
    if (data.success) {
      if (data.is_public) {
        btn.className = 'badge badge-emerald';
        btn.innerHTML = '<i class="fa-solid fa-eye"></i> Public on Frontend';
      } else {
        btn.className = 'badge badge-navy';
        btn.innerHTML = '<i class="fa-solid fa-eye-slash"></i> Hidden / Private';
      }
      if (window.showToast) {
        window.showToast(data.message || 'Visibility updated.', 'success');
      }
    } else {
      btn.innerHTML = origHtml;
      alert(data.message || 'Failed to update visibility.');
    }
  })
  .catch(err => {
    btn.disabled = false;
    btn.innerHTML = origHtml;
    alert('An error occurred while updating visibility.');
  });
}

function startNowBranchAjax(examId, url, btn) {
  if (!confirm('Immediately start this exam now? All candidates can begin.')) return;
  const origHtml = btn.innerHTML;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
  btn.disabled = true;

  fetch(url, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json'
    }
  })
  .then(res => {
    if (!res.ok) throw new Error('HTTP ' + res.status);
    return res.json();
  })
  .then(data => {
    btn.disabled = false;
    if (data.success) {
      const wrap = document.getElementById('start-end-btn-wrap-' + examId);
      if (wrap) {
        wrap.innerHTML = `
          <button type="button" 
            onclick="endNowBranchAjax(${examId}, '{{ url('admin/exams') }}/${examId}/end-now', this)"
            class="btn-tactical btn-tactical-outline" style="padding: 6px 12px; font-size: 11.5px; border-color: #ef4444; color: #ef4444;">
            <i class="fa-solid fa-stop"></i> End Now
          </button>
        `;
      }
      const row = document.getElementById('exam-row-' + examId);
      if (row && row.cells[0]) {
        row.cells[0].innerHTML = `<span class="badge badge-emerald" style="display: inline-flex; align-items: center; gap: 5px; font-size: 11px;"><span style="width: 7px; height: 7px; border-radius: 50%; background: #10b981; display: inline-block;"></span> LIVE NOW</span>`;
      }
      if (window.showToast) {
        window.showToast(data.message || 'Exam started successfully.', 'success');
      }
    } else {
      btn.innerHTML = origHtml;
      alert(data.message || 'Failed to start exam.');
    }
  })
  .catch(err => {
    btn.disabled = false;
    btn.innerHTML = origHtml;
    alert('An error occurred while starting the exam.');
  });
}

function endNowBranchAjax(examId, url, btn) {
  if (!confirm('End and close this exam now? No new attempts will be allowed.')) return;
  const origHtml = btn.innerHTML;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
  btn.disabled = true;

  fetch(url, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json'
    }
  })
  .then(res => {
    if (!res.ok) throw new Error('HTTP ' + res.status);
    return res.json();
  })
  .then(data => {
    btn.disabled = false;
    if (data.success) {
      const wrap = document.getElementById('start-end-btn-wrap-' + examId);
      if (wrap) {
        wrap.innerHTML = `
          <button type="button" 
            onclick="startNowBranchAjax(${examId}, '{{ url('admin/exams') }}/${examId}/start-now', this)"
            class="btn-tactical btn-tactical-primary" style="padding: 6px 12px; font-size: 11.5px;">
            <i class="fa-solid fa-play"></i> Start Now
          </button>
        `;
      }
      const row = document.getElementById('exam-row-' + examId);
      if (row && row.cells[0]) {
        row.cells[0].innerHTML = `<span class="badge badge-navy" style="font-size: 11px; background: rgba(100, 116, 139, 0.2); color: #94a3b8;"><i class="fa-solid fa-lock"></i> CLOSED / ENDED</span>`;
      }
      if (window.showToast) {
        window.showToast(data.message || 'Exam closed successfully.', 'success');
      }
    } else {
      btn.innerHTML = origHtml;
      alert(data.message || 'Failed to close exam.');
    }
  })
  .catch(err => {
    btn.disabled = false;
    btn.innerHTML = origHtml;
    alert('An error occurred while closing the exam.');
  });
}

function deleteExamAjax(examId, examTitle, examStatus, deleteUrl, btn) {
  if (!confirm(`Are you sure you want to permanently delete the exam paper "${examTitle}"? This cannot be undone.`)) {
    return;
  }

  const row = document.getElementById('exam-row-' + examId);
  const originalHtml = btn.innerHTML;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
  btn.disabled = true;

  if (row) {
    row.style.opacity = '0.35';
    row.style.pointerEvents = 'none';
  }

  fetch(deleteUrl, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ _method: 'DELETE' })
  })
  .then(res => {
    if (!res.ok) throw new Error('HTTP ' + res.status);
    return res.json();
  })
  .then(data => {
    if (data.success) {
      if (row) {
        row.style.transition = 'all 0.35s cubic-bezier(0.16, 1, 0.3, 1)';
        row.style.transform = 'translateX(25px)';
        row.style.opacity = '0';
        setTimeout(() => {
          row.remove();
          const remainingRows = document.querySelectorAll('#examTableBody tr.exam-row');
          if (remainingRows.length === 0) {
            const tableBody = document.getElementById('examTableBody');
            if (tableBody) {
              tableBody.innerHTML = `
                <tr id="emptyExamsRow">
                  <td colspan="6" style="text-align: center; padding: 40px; color: #94a3b8;">
                    <i class="fa-solid fa-sliders" style="font-size: 32px; opacity: 0.4; margin-bottom: 8px; display: block;"></i>
                    No exams created yet for {{ $branchName }}. Switch to the <strong>"Create New Exam from PDF"</strong> tab above to create your first exam paper.
                  </td>
                </tr>
              `;
            }
          }
        }, 350);
      }

      // Decrement counter numbers
      const dec = (id) => {
        const el = document.getElementById(id);
        if (el) {
          const val = Math.max(0, parseInt(el.textContent || '0') - 1);
          el.textContent = val;
        }
      };

      dec('filter-all-count');
      if (examStatus === 'open') {
        dec('stat-live-count');
        dec('filter-live-count');
      } else if (examStatus === 'scheduled') {
        dec('stat-scheduled-count');
        dec('filter-scheduled-count');
      } else if (examStatus === 'draft') {
        dec('stat-draft-count');
        dec('filter-draft-count');
      } else if (examStatus === 'closed') {
        dec('stat-closed-count');
        dec('filter-closed-count');
      }

      if (window.showToast) {
        window.showToast(data.message || 'Exam deleted successfully.', 'success');
      }
    } else {
      btn.innerHTML = originalHtml;
      btn.disabled = false;
      if (row) {
        row.style.opacity = '1';
        row.style.pointerEvents = 'auto';
      }
      alert(data.message || 'Failed to delete exam.');
    }
  })
  .catch(err => {
    btn.innerHTML = originalHtml;
    btn.disabled = false;
    if (row) {
      row.style.opacity = '1';
      row.style.pointerEvents = 'auto';
    }
    alert('An error occurred while deleting the exam. Please try again.');
  });
}

function escapeHtml(text) {
  const div = document.createElement('div');
  div.innerText = text;
  return div.innerHTML;
}
</script>
@endsection

