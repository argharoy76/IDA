@extends('layouts.portal')

@section('title', 'Cadet Dossier - ' . $student->user->name)
@section('page_title', 'Digital Student Dossier')
@section('page_subtitle', 'Comprehensive 360-degree military competence, observation & development record')

@section('content')
<!-- Cadet Central Profile Header Card -->
<div class="content-panel" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); margin-bottom: 24px; position: relative;">
  <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px;">
    
    <!-- Cadet Bio Meta -->
    <div style="display: flex; gap: 20px; align-items: center;">
      <div style="width: 80px; height: 80px; border-radius: 20px; background: linear-gradient(135deg, var(--brand-deep), var(--accent-navy)); color: #fff; display: grid; place-items: center; font-size: 32px; font-weight: 800; box-shadow: 0 8px 20px var(--brand-glow);">
        {{ strtoupper(substr($student->user->name, 0, 1)) }}
      </div>
      <div>
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
          <h2 style="font-size: 24px; font-weight: 800; color: var(--brand-deep);">{{ $student->user->name }}</h2>
          <span class="badge badge-emerald" style="font-size: 13px; font-weight: 800;">{{ $student->student_id_code }}</span>
          <span class="badge badge-blue">Roll: #{{ $student->roll_number ?? '00' }}</span>
        </div>
        <p style="font-size: 13.5px; color: var(--text-muted); margin-bottom: 8px;">
          <i class="fa-solid fa-crosshairs" style="color: var(--brand-emerald);"></i> Target: <strong>{{ $student->target_wing }} Wing</strong> &nbsp;|&nbsp;
          <i class="fa-solid fa-layer-group" style="color: var(--accent-gold);"></i> Squad: <strong>{{ $student->currentBatch->batch_name ?? 'Unallocated' }} ({{ $student->currentBatch->batch_code ?? 'None' }})</strong> &nbsp;|&nbsp;
          <i class="fa-solid fa-calendar" style="color: var(--text-subtle);"></i> Admitted: <strong>{{ $student->admission_date ? $student->admission_date->format('d M, Y') : 'N/A' }}</strong>
        </p>
        <div style="display: flex; gap: 12px; font-size: 12.5px;">
          <span><i class="fa-solid fa-droplet" style="color: #ef4444;"></i> Blood: <strong>{{ $student->blood_group ?? 'Unknown' }}</strong></span>
          <span><i class="fa-solid fa-phone" style="color: var(--brand-mint);"></i> {{ $student->user->phone }}</span>
          <span><i class="fa-solid fa-shield" style="color: var(--accent-navy);"></i> Primary Mentor: <strong>{{ $student->currentBatch->primaryInstructor->user->name ?? 'Command Assessor' }}</strong></span>
        </div>
      </div>
    </div>

    <!-- Readiness Score & Action Buttons -->
    <div style="display: flex; align-items: center; gap: 14px; flex-wrap: wrap;">
      <div style="background: #11141d; border: 2px solid #10b981; border-radius: 14px; padding: 12px 18px; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
        <small style="display: block; font-size: 10.5px; font-weight: 800; color: #8c96a8; text-transform: uppercase;">READINESS INDEX</small>
        <strong style="font-size: 26px; color: #10b981; font-family: 'Poppins', sans-serif;">{{ $student->dossier->readiness_score ?? 65 }}%</strong>
        <span class="badge badge-emerald" style="font-size: 10px; display: block; margin-top: 2px;">{{ $student->dossier->overall_status ?? 'On Track' }}</span>
      </div>

      <div style="display: flex; flex-direction: column; gap: 8px;">
        <button type="button" onclick="openModal('observationModal')" class="btn-primary" style="font-size: 12px; padding: 7px 14px;">
          <i class="fa-solid fa-pen-to-square"></i> Record Observation
        </button>
        <button type="button" onclick="openModal('strengthWeaknessModal')" class="btn-secondary" style="font-size: 12px; padding: 7px 14px;">
          <i class="fa-solid fa-plus"></i> Add Strength / Weakness
        </button>
        <button type="button" onclick="openModal('transferBatchModal')" class="btn-secondary" style="font-size: 12px; padding: 7px 14px;">
          <i class="fa-solid fa-arrows-rotate"></i> Transfer Squad
        </button>
      </div>
    </div>
  </div>
</div>

<!-- 5-Axis Competence Radar & Quick Diagnostics -->
<div style="display: grid; grid-template-columns: 1fr 1.3fr; gap: 24px; margin-bottom: 24px;">
  <!-- Radar Chart Panel -->
  <div class="content-panel" style="margin-bottom: 0;">
    <div class="panel-header">
      <h3><i class="fa-solid fa-radar" style="color: var(--brand-emerald);"></i> 5-Axis Military Competence Profile</h3>
      <span class="badge badge-blue">Evaluated by Faculty</span>
    </div>
    <div style="max-width: 380px; margin: 0 auto; height: 280px; position: relative;">
      <canvas id="competenceRadarChart"></canvas>
    </div>
    <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 6px; text-align: center; margin-top: 14px; border-top: 1px solid var(--border-soft); padding-top: 12px; font-size: 11px;">
      <div><strong>IQ</strong><span style="display: block; color: var(--brand-emerald); font-weight: 800;">{{ $student->dossier?->iq_grade ?? 'A' }}</span></div>
      <div><strong>Leadership</strong><span style="display: block; color: var(--brand-emerald); font-weight: 800;">{{ $student->dossier?->leadership_grade ?? 'A-' }}</span></div>
      <div><strong>Comm</strong><span style="display: block; color: var(--brand-emerald); font-weight: 800;">{{ $student->dossier?->communication_grade ?? 'B+' }}</span></div>
      <div><strong>Physical</strong><span style="display: block; color: var(--brand-emerald); font-weight: 800;">{{ $student->dossier?->physical_grade ?? 'A-' }}</span></div>
      <div><strong>Discipline</strong><span style="display: block; color: var(--brand-emerald); font-weight: 800;">{{ $student->dossier?->discipline_grade ?? 'A' }}</span></div>
    </div>
  </div>

  <!-- Key Summary & Overview Diagnostics -->
  <div class="content-panel" style="margin-bottom: 0; display: flex; flex-direction: column; justify-content: space-between;">
    <div>
      <div class="panel-header">
        <h3><i class="fa-solid fa-clipboard-check" style="color: var(--accent-gold);"></i> Assessor's Summary Evaluation</h3>
        <span class="badge badge-emerald">{{ $student->attendance_percentage }}% Attendance</span>
      </div>
      <p style="font-size: 14px; color: var(--text-main); line-height: 1.7; margin-bottom: 18px; font-style: italic; background: var(--surface-subtle); padding: 14px 18px; border-radius: var(--radius-sm); border-left: 3px solid var(--brand-emerald);">
        "{{ $student->dossier?->summary_notes ?? 'Candidate demonstrates strong character, high intellectual stamina, and leadership potential. Continuous monitoring of extempore speaking fluency underway.' }}"
      </p>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
        <div style="background: var(--surface-subtle); padding: 12px; border-radius: var(--radius-sm);">
          <small style="display: block; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">IDENTIFIED STRENGTHS</small>
          <strong style="font-size: 18px; color: var(--brand-emerald);">{{ $student->strengths->count() }} Key Competencies</strong>
        </div>
        <div style="background: var(--surface-subtle); padding: 12px; border-radius: var(--radius-sm);">
          <small style="display: block; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">TARGET WEAKNESSES</small>
          <strong style="font-size: 18px; color: #ef4444;">{{ $student->weaknesses->count() }} Active Improvement Focus</strong>
        </div>
      </div>
    </div>

    <div style="border-top: 1px solid var(--border-soft); padding-top: 16px; margin-top: 16px; display: flex; justify-content: space-between; align-items: center;">
      <span style="font-size: 13px; color: var(--text-muted);">
        Outstanding Fees: <strong style="color: {{ $student->total_due_fee > 0 ? '#ef4444' : 'var(--brand-emerald)' }};">৳{{ number_format($student->total_due_fee, 0) }}</strong>
      </span>
      <button type="button" onclick="openModal('improvementPlanModal')" class="btn-gold" style="padding: 7px 16px; font-size: 12px;">
        <i class="fa-solid fa-bullseye"></i> Assign Improvement Plan
      </button>
    </div>
  </div>
</div>

<!-- Detailed Tabbed Modules (SRS Sections 31-38) -->
<div class="content-panel">
  <!-- Tab Navigation -->
  <div style="display: flex; gap: 8px; border-bottom: 1px solid var(--border-soft); padding-bottom: 12px; margin-bottom: 24px; flex-wrap: wrap;">
    <button type="button" onclick="switchTab('tab-sw', this)" class="dossier-tab-btn active"><i class="fa-solid fa-scale-balanced"></i> Strengths & Weaknesses ({{ $student->strengthsWeaknesses->count() }})</button>
    <button type="button" onclick="switchTab('tab-obs', this)" class="dossier-tab-btn"><i class="fa-solid fa-eye"></i> Instructor Observations ({{ $student->observations->count() }})</button>
    <button type="button" onclick="switchTab('tab-plans', this)" class="dossier-tab-btn"><i class="fa-solid fa-bullseye"></i> Improvement Plans ({{ $student->improvementPlans->count() }})</button>
    <button type="button" onclick="switchTab('tab-assessments', this)" class="dossier-tab-btn"><i class="fa-solid fa-chart-line"></i> Periodic Progression ({{ $student->performanceAssessments->count() }})</button>
    <button type="button" onclick="switchTab('tab-timeline', this)" class="dossier-tab-btn"><i class="fa-solid fa-clock-rotate-left"></i> Development Timeline ({{ $student->timelines->count() }})</button>
    <button type="button" onclick="switchTab('tab-exams', this)" class="dossier-tab-btn"><i class="fa-solid fa-feather-pointed"></i> Exam History ({{ $student->examAttempts->count() }})</button>
    <button type="button" onclick="switchTab('tab-finance', this)" class="dossier-tab-btn"><i class="fa-solid fa-file-invoice-dollar"></i> Fees & Payments ({{ $student->invoices->count() }})</button>
  </div>

  <!-- TAB 1: STRENGTHS & WEAKNESSES -->
  <div id="tab-sw" class="dossier-tab-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
      <h3 style="font-size: 16px; font-weight: 800;">Identified Strengths & Targeted Weaknesses</h3>
      <button type="button" onclick="openModal('strengthWeaknessModal')" class="btn-primary" style="padding: 6px 12px; font-size: 12px;">
        <i class="fa-solid fa-plus"></i> Add Item
      </button>
    </div>

    <div class="table-responsive">
      <table class="tactical-table">
        <thead>
          <tr>
            <th>Type</th>
            <th>Category & Title</th>
            <th>Description & Context</th>
            <th>Priority</th>
            <th>Status</th>
            <th>Identified Date</th>
            <th>Assessor</th>
          </tr>
        </thead>
        <tbody>
          @forelse($student->strengthsWeaknesses as $sw)
            <tr>
              <td>
                <span class="badge {{ $sw->type === 'strength' ? 'badge-emerald' : 'badge-red' }}">
                  {{ strtoupper($sw->type) }}
                </span>
              </td>
              <td>
                <strong style="display: block;">{{ $sw->title }}</strong>
                <small style="color: var(--text-muted);">{{ $sw->category }}</small>
              </td>
              <td><span style="font-size: 13px;">{{ $sw->description }}</span></td>
              <td>
                @if($sw->priority === 'critical')
                  <span class="badge badge-red">CRITICAL</span>
                @elseif($sw->priority === 'high')
                  <span class="badge badge-amber">HIGH</span>
                @else
                  <span class="badge badge-blue">{{ strtoupper($sw->priority) }}</span>
                @endif
              </td>
              <td>
                <span class="badge {{ $sw->status === 'resolved' ? 'badge-emerald' : ($sw->status === 'improving' ? 'badge-blue' : 'badge-amber') }}">
                  {{ ucfirst($sw->status) }}
                </span>
              </td>
              <td>{{ $sw->identified_date ? $sw->identified_date->format('d M, Y') : '--' }}</td>
              <td>{{ $sw->instructor->user->name ?? 'Board Panel' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="7" style="text-align: center; padding: 24px; color: var(--text-muted);">
                No strengths or weaknesses recorded for this cadet yet.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- TAB 2: INSTRUCTOR OBSERVATIONS -->
  <div id="tab-obs" class="dossier-tab-content" style="display: none;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
      <h3 style="font-size: 16px; font-weight: 800;">Dated Military Observations & Recommendations</h3>
      <button type="button" onclick="openModal('observationModal')" class="btn-primary" style="padding: 6px 12px; font-size: 12px;">
        <i class="fa-solid fa-plus"></i> Record Observation
      </button>
    </div>

    <div style="display: flex; flex-direction: column; gap: 14px;">
      @forelse($student->observations as $obs)
        <div style="background: var(--surface-subtle); padding: 18px; border-radius: var(--radius-sm); border-left: 4px solid var(--brand-deep);">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <div style="display: flex; align-items: center; gap: 10px;">
              <span class="badge badge-emerald">{{ $obs->category }}</span>
              <strong style="font-size: 14px;">{{ $obs->instructor->user->name ?? 'Assessor' }}</strong>
              <small style="color: var(--text-muted); font-size: 11px;">{{ $obs->observation_date->format('d F, Y') }}</small>
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
              <span style="font-weight: 800; color: var(--accent-gold); font-size: 14px;">
                @for($i = 1; $i <= 5; $i++)
                  <i class="fa-solid fa-star" style="color: {{ $i <= $obs->rating ? 'var(--accent-gold)' : '#cbd5e1' }}; font-size: 12px;"></i>
                @endfor
                {{ $obs->rating }}/5
              </span>
              <span class="badge {{ $obs->visibility === 'student_visible' ? 'badge-blue' : 'badge-amber' }}">
                {{ str_replace('_', ' ', $obs->visibility) }}
              </span>
            </div>
          </div>

          <p style="font-size: 13.5px; color: var(--text-main); line-height: 1.6; margin-bottom: 10px;">
            {{ $obs->observation_text }}
          </p>

          @if($obs->recommended_action)
            <div style="background: #fff; padding: 10px 14px; border-radius: var(--radius-sm); border: 1px solid var(--border-soft); font-size: 12.5px;">
              <strong style="color: var(--brand-deep);"><i class="fa-solid fa-lightbulb" style="color: var(--accent-gold);"></i> Recommended Action:</strong>
              <span style="color: var(--text-muted);">{{ $obs->recommended_action }}</span>
            </div>
          @endif
        </div>
      @empty
        <p style="color: var(--text-muted); text-align: center; padding: 30px;">No observations recorded yet.</p>
      @endforelse
    </div>
  </div>

  <!-- TAB 3: ACTIONABLE IMPROVEMENT PLANS -->
  <div id="tab-plans" class="dossier-tab-content" style="display: none;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
      <h3 style="font-size: 16px; font-weight: 800;">Targeted Weakness Improvement Plans</h3>
      <button type="button" onclick="openModal('improvementPlanModal')" class="btn-primary" style="padding: 6px 12px; font-size: 12px;">
        <i class="fa-solid fa-plus"></i> Assign Plan
      </button>
    </div>

    <div style="display: flex; flex-direction: column; gap: 14px;">
      @forelse($student->improvementPlans as $plan)
        <div style="background: var(--surface-subtle); padding: 18px; border-radius: var(--radius-sm); border: 1px solid var(--border-soft);">
          <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
            <div>
              <h4 style="font-size: 15px; font-weight: 800; color: var(--brand-deep);">{{ $plan->target_objective }}</h4>
              <small style="color: var(--text-muted); font-size: 12px;">Addressing: <strong>{{ $plan->problem_description }}</strong></small>
            </div>
            <span class="badge {{ $plan->status === 'completed' ? 'badge-emerald' : 'badge-blue' }}">
              {{ ucfirst(str_replace('_', ' ', $plan->status)) }}
            </span>
          </div>

          <div style="background: #fff; padding: 12px; border-radius: var(--radius-sm); border: 1px solid var(--border-soft); margin-bottom: 12px; font-size: 13px;">
            <strong style="color: var(--brand-deep); display: block; margin-bottom: 4px;">Assigned Cadet Task:</strong>
            <span>{{ $plan->assigned_task }}</span>
          </div>

          <div style="display: flex; justify-content: space-between; align-items: center; font-size: 12px; color: var(--text-muted);">
            <div>
              Mentor: <strong>{{ $plan->responsibleInstructor->user->name ?? 'Instructor' }}</strong> &nbsp;|&nbsp;
              Deadline: <strong style="color: #ef4444;">{{ $plan->deadline->format('d M, Y') }}</strong>
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
              <span>Progress: <strong>{{ $plan->progress_percentage }}%</strong></span>
              <div style="width: 100px; height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden;">
                <div style="width: {{ $plan->progress_percentage }}%; height: 100%; background: var(--brand-emerald);"></div>
              </div>
            </div>
          </div>
        </div>
      @empty
        <p style="color: var(--text-muted); text-align: center; padding: 30px;">No active improvement plans assigned.</p>
      @endforelse
    </div>
  </div>

  <!-- TAB 4: PERIODIC ASSESSMENTS PROGRESSION (SRS Section 35) -->
  <div id="tab-assessments" class="dossier-tab-content" style="display: none;">
    <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 16px;">Historical Performance Progression Records</h3>
    <div style="display: flex; flex-direction: column; gap: 16px;">
      @forelse($student->performanceAssessments as $pa)
        <div style="background: var(--surface-subtle); padding: 18px; border-radius: var(--radius-sm); border: 1px solid var(--border-soft);">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <div>
              <strong style="font-size: 15px; color: var(--brand-deep);">{{ $pa->period_label }}</strong>
              <small style="display: block; color: var(--text-muted);">Assessment Date: {{ $pa->assessment_date->format('d M, Y') }}</small>
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
              <span class="badge badge-emerald">Overall: {{ $pa->overall_rating }}/5</span>
            </div>
          </div>

          @if(!empty($pa->ratings))
            <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; margin-bottom: 12px;">
              @foreach($pa->ratings as $subject => $score)
                <div style="background: #fff; padding: 8px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border-soft); text-align: center;">
                  <small style="display: block; font-size: 11px; color: var(--text-muted);">{{ $subject }}</small>
                  <strong style="font-size: 15px; color: var(--brand-deep);">{{ $score }}%</strong>
                </div>
              @endforeach
            </div>
          @endif

          <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 0;">
            <i class="fa-solid fa-comment-dots" style="color: var(--brand-emerald);"></i> {{ $pa->remarks }}
          </p>
        </div>
      @empty
        <p style="color: var(--text-muted); text-align: center; padding: 30px;">No historical structured assessments recorded.</p>
      @endforelse
    </div>
  </div>

  <!-- TAB 5: DEVELOPMENT TIMELINE (SRS Section 37) -->
  <div id="tab-timeline" class="dossier-tab-content" style="display: none;">
    <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 20px;">Chronological Cadet Development Timeline</h3>
    <div class="timeline-track">
      @forelse($student->timelines as $event)
        <div class="timeline-item">
          <div class="timeline-dot" style="border-color: {{ $event->badge_color === 'red' ? '#ef4444' : ($event->badge_color === 'purple' ? '#9333ea' : 'var(--brand-emerald)') }};"></div>
          <div class="timeline-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
              <strong style="font-size: 14px; color: var(--brand-deep);"><i class="fa-solid {{ $event->icon }}" style="color: var(--brand-emerald);"></i> {{ $event->title }}</strong>
              <small style="font-size: 11.5px; font-weight: 700; color: var(--text-muted);">{{ $event->event_date->format('d M, Y') }}</small>
            </div>
            <p style="font-size: 13px; color: var(--text-muted); line-height: 1.5; margin-bottom: 0;">
              {{ $event->description }}
            </p>
          </div>
        </div>
      @empty
        <p style="color: var(--text-muted); text-align: center; padding: 30px;">No timeline events recorded.</p>
      @endforelse
    </div>
  </div>

  <!-- TAB 6: EXAM HISTORY -->
  <div id="tab-exams" class="dossier-tab-content" style="display: none;">
    <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 16px;">Assessment Examinations & Test Attempts</h3>
    <div class="table-responsive">
      <table class="tactical-table">
        <thead>
          <tr>
            <th>Examination</th>
            <th>Type</th>
            <th>Score</th>
            <th>Percentage</th>
            <th>Result Status</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          @forelse($student->examAttempts as $att)
            <tr>
              <td><strong>{{ $att->exam->title ?? 'Mock Exam' }}</strong></td>
              <td><span class="badge badge-blue">{{ strtoupper($att->exam->exam_type ?? 'IQ') }}</span></td>
              <td><strong>{{ $att->score }} / {{ $att->total_marks }}</strong></td>
              <td><strong>{{ $att->percentage }}%</strong></td>
              <td>
                <span class="badge {{ $att->result_status === 'passed' ? 'badge-emerald' : 'badge-red' }}">
                  {{ strtoupper($att->result_status) }}
                </span>
              </td>
              <td>{{ $att->created_at->format('d M, Y') }}</td>
            </tr>
          @empty
            <tr><td colspan="6" style="text-align: center; padding: 24px; color: var(--text-muted);">No exam attempts found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- TAB 7: FEES & PAYMENTS -->
  <div id="tab-finance" class="dossier-tab-content" style="display: none;">
    <h3 style="font-size: 16px; font-weight: 800; margin-bottom: 16px;">Fee Invoices & Verified Payment Receipts</h3>
    <div class="table-responsive">
      <table class="tactical-table">
        <thead>
          <tr>
            <th>Invoice No</th>
            <th>Title</th>
            <th>Net Amount</th>
            <th>Paid</th>
            <th>Due</th>
            <th>Status</th>
            <th>Due Date</th>
          </tr>
        </thead>
        <tbody>
          @forelse($student->invoices as $inv)
            <tr>
              <td><strong>{{ $inv->invoice_number }}</strong></td>
              <td>{{ $inv->title }}</td>
              <td>৳{{ number_format($inv->net_amount, 0) }}</td>
              <td><span style="color: var(--brand-emerald); font-weight: 700;">৳{{ number_format($inv->paid_amount, 0) }}</span></td>
              <td><span style="color: {{ $inv->due_amount > 0 ? '#ef4444' : 'var(--text-muted)' }}; font-weight: 700;">৳{{ number_format($inv->due_amount, 0) }}</span></td>
              <td>
                <span class="badge {{ $inv->status === 'paid' ? 'badge-emerald' : ($inv->status === 'partially_paid' ? 'badge-blue' : 'badge-amber') }}">
                  {{ ucfirst(str_replace('_', ' ', $inv->status)) }}
                </span>
              </td>
              <td>{{ $inv->due_date->format('d M, Y') }}</td>
            </tr>
          @empty
            <tr><td colspan="7" style="text-align: center; padding: 24px; color: var(--text-muted);">No invoices found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- MODAL 1: RECORD OBSERVATION -->
<div id="observationModal" class="modal-backdrop" style="display: none;">
  <div class="modal-dialog">
    <div class="panel-header">
      <h3>Record Instructor Observation</h3>
      <button type="button" onclick="closeModal('observationModal')" style="background: none; border: none; font-size: 18px; cursor: pointer;">&times;</button>
    </div>
    <form action="{{ route('admin.dossiers.store_observation', $student->id) }}" method="POST">
      @csrf
      <div class="form-group">
        <label class="form-label">Evaluating Instructor *</label>
        <select name="instructor_id" required class="form-control">
          @foreach($instructors as $inst)
            <option value="{{ $inst->id }}">{{ $inst->user->name }} ({{ $inst->designation }})</option>
          @endforeach
        </select>
      </div>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
        <div class="form-group">
          <label class="form-label">Category *</label>
          <select name="category" class="form-control">
            <option value="Leadership & Command">Leadership & Command</option>
            <option value="Progressive Ground Task">Progressive Ground Task (PGT)</option>
            <option value="Group Discussion & Speech">Group Discussion & Speech</option>
            <option value="Psychological Screening">Psychological Screening (WAT/TAT)</option>
            <option value="Physical Agility">Physical Agility & Obstacles</option>
            <option value="Interview Posture">Interview Posture & Composure</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Rating (1 to 5) *</label>
          <select name="rating" class="form-control">
            <option value="5">5 — Exceptional / High Potential</option>
            <option value="4" selected>4 — Good / Recommended Level</option>
            <option value="3">3 — Average / Meets Standard</option>
            <option value="2">2 — Needs Improvement</option>
            <option value="1">1 — Critical Concern</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Observation Details *</label>
        <textarea name="observation_text" required rows="3" class="form-control" placeholder="Specific notes on cadet behavior, vocal command, or technical bridging..."></textarea>
      </div>
      <div class="form-group">
        <label class="form-label">Recommended Action</label>
        <input type="text" name="recommended_action" class="form-control" placeholder="e.g. Speech drill, rope bridging practice">
      </div>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
        <div class="form-group">
          <label class="form-label">Visibility Protocol</label>
          <select name="visibility" class="form-control">
            <option value="student_visible">Student Visible</option>
            <option value="instructor_only">Instructor Only</option>
            <option value="admin_only">Admin / Command Only</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Follow-up Date</label>
          <input type="date" name="follow_up_date" class="form-control">
        </div>
      </div>
      <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; padding: 12px;">Save Observation</button>
    </form>
  </div>
</div>

<!-- MODAL 2: ADD STRENGTH / WEAKNESS -->
<div id="strengthWeaknessModal" class="modal-backdrop" style="display: none;">
  <div class="modal-dialog">
    <div class="panel-header">
      <h3>Record Strength or Weakness</h3>
      <button type="button" onclick="closeModal('strengthWeaknessModal')" style="background: none; border: none; font-size: 18px; cursor: pointer;">&times;</button>
    </div>
    <form action="{{ route('admin.dossiers.store_strength_weakness', $student->id) }}" method="POST">
      @csrf
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
        <div class="form-group">
          <label class="form-label">Entry Type *</label>
          <select name="type" class="form-control">
            <option value="strength">Strength</option>
            <option value="weakness">Weakness</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Category *</label>
          <select name="category" class="form-control">
            <option value="Leadership">Leadership</option>
            <option value="Communication">Communication & English</option>
            <option value="IQ">IQ & Reasoning</option>
            <option value="Mathematics">Mathematics</option>
            <option value="Physical">Physical Stamina</option>
            <option value="Discipline">Discipline & Punctuality</option>
            <option value="General Knowledge">General Knowledge</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Title / Competency *</label>
        <input type="text" name="title" required class="form-control" placeholder="e.g. Rapid non-verbal matrix solving OR Hesitation in English">
      </div>
      <div class="form-group">
        <label class="form-label">Description & Context</label>
        <textarea name="description" rows="2" class="form-control" placeholder="Detailed notes..."></textarea>
      </div>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
        <div class="form-group">
          <label class="form-label">Priority *</label>
          <select name="priority" class="form-control">
            <option value="low">Low</option>
            <option value="medium" selected>Medium</option>
            <option value="high">High</option>
            <option value="critical">Critical (Triggers Alert)</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Status *</label>
          <select name="status" class="form-control">
            <option value="identified">Identified</option>
            <option value="improving">Improving</option>
            <option value="improved">Improved</option>
            <option value="resolved">Resolved</option>
          </select>
        </div>
      </div>
      <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; padding: 12px;">Save Competency</button>
    </form>
  </div>
</div>

<!-- MODAL 3: ASSIGN IMPROVEMENT PLAN -->
<div id="improvementPlanModal" class="modal-backdrop" style="display: none;">
  <div class="modal-dialog">
    <div class="panel-header">
      <h3>Assign Actionable Improvement Plan</h3>
      <button type="button" onclick="closeModal('improvementPlanModal')" style="background: none; border: none; font-size: 18px; cursor: pointer;">&times;</button>
    </div>
    <form action="{{ route('admin.dossiers.store_improvement_plan', $student->id) }}" method="POST">
      @csrf
      <div class="form-group">
        <label class="form-label">Target Weakness / Problem *</label>
        <input type="text" name="problem_description" required class="form-control" placeholder="e.g. Hesitation in 3-minute military lecturette">
      </div>
      <div class="form-group">
        <label class="form-label">Specific Objective *</label>
        <input type="text" name="target_objective" required class="form-control" placeholder="e.g. Fluent 3-minute presentation without filler pauses">
      </div>
      <div class="form-group">
        <label class="form-label">Assigned Cadet Task *</label>
        <textarea name="assigned_task" required rows="2" class="form-control" placeholder="e.g. Record and submit 3-minute speech on geopolitical topic daily..."></textarea>
      </div>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
        <div class="form-group">
          <label class="form-label">Responsible Mentor</label>
          <select name="responsible_instructor_id" class="form-control">
            @foreach($instructors as $inst)
              <option value="{{ $inst->id }}">{{ $inst->user->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Target Deadline *</label>
          <input type="date" name="deadline" required class="form-control" value="{{ date('Y-m-d', strtotime('+14 days')) }}">
        </div>
      </div>
      <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; padding: 12px;">Assign Plan</button>
    </form>
  </div>
</div>

<!-- MODAL 4: TRANSFER SQUAD BATCH -->
<div id="transferBatchModal" class="modal-backdrop" style="display: none;">
  <div class="modal-dialog">
    <div class="panel-header">
      <h3>Transfer Cadet to Another Squad Batch</h3>
      <button type="button" onclick="closeModal('transferBatchModal')" style="background: none; border: none; font-size: 18px; cursor: pointer;">&times;</button>
    </div>
    <form action="{{ route('admin.students.transfer_batch', $student->id) }}" method="POST">
      @csrf
      <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px;">
        Current Squad: <strong>{{ $student->currentBatch->batch_name ?? 'None' }} ({{ $student->currentBatch->batch_code ?? 'None' }})</strong>. Historical attendance and records will remain preserved.
      </p>
      <div class="form-group">
        <label class="form-label">Select Target Squad Batch *</label>
        <select name="new_batch_id" required class="form-control">
          @foreach($batches as $b)
            <option value="{{ $b->id }}" {{ $student->current_batch_id == $b->id ? 'disabled' : '' }}>
              {{ $b->batch_name }} ({{ $b->batch_code }}) - {{ $b->course->title ?? '' }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Transfer Rationale / Notes</label>
        <textarea name="transfer_notes" rows="2" class="form-control" placeholder="Reason for squad reallocation..."></textarea>
      </div>
      <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; padding: 12px;">Confirm Squad Transfer</button>
    </form>
  </div>
</div>

<style>
.dossier-tab-btn {
  background: #11141d;
  border: 1px solid rgba(255, 255, 255, 0.08);
  padding: 8px 16px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 600;
  color: #8c96a8;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}
.dossier-tab-btn:hover {
  background: rgba(255, 255, 255, 0.05);
  color: #ffffff;
}
.dossier-tab-btn.active {
  background: rgba(255, 87, 87, 0.14);
  color: #ff7575;
  border-color: rgba(255, 87, 87, 0.4);
}
</style>

@endsection

@section('scripts')
<script>
  function switchTab(tabId, btn) {
    document.querySelectorAll('.dossier-tab-content').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.dossier-tab-btn').forEach(el => el.classList.remove('active'));
    document.getElementById(tabId).style.display = 'block';
    btn.classList.add('active');
  }

  function openModal(id) { document.getElementById(id).style.display = 'grid'; }
  function closeModal(id) { document.getElementById(id).style.display = 'none'; }

  // 5-Axis Radar Chart Initialization
  document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('competenceRadarChart');
    if (ctx) {
      new Chart(ctx, {
        type: 'radar',
        data: {
          labels: ['Verbal & Non-Verbal IQ', 'Leadership & Command', 'English & Expression', 'Physical Obstacles', 'Military Discipline'],
          datasets: [{
            label: 'Cadet Profile Score',
            data: [
              {{ ($student->dossier?->iq_grade ?? 'A') === 'A+' ? 95 : (($student->dossier?->iq_grade ?? 'A') === 'A' ? 88 : 75) }},
              {{ ($student->dossier?->leadership_grade ?? 'A') === 'A' ? 90 : 82 }},
              {{ ($student->dossier?->communication_grade ?? 'B+') === 'B+' ? 78 : 65 }},
              {{ ($student->dossier?->physical_grade ?? 'A-') === 'A-' ? 86 : 74 }},
              92
            ],
            backgroundColor: 'rgba(16, 185, 129, 0.25)',
            borderColor: '#059669',
            pointBackgroundColor: '#064e3b',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: '#059669',
            borderWidth: 2
          }, {
            label: 'ISSB Benchmark',
            data: [70, 70, 70, 70, 70],
            borderColor: '#94a3b8',
            borderDash: [4, 4],
            borderWidth: 1,
            pointRadius: 0,
            fill: false
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            r: {
              angleLines: { color: '#e2e8f0' },
              grid: { color: '#f1f5f9' },
              suggestedMin: 40,
              suggestedMax: 100,
              ticks: { display: false }
            }
          },
          plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } }
          }
        }
      });
    }
  });
</script>
@endsection
