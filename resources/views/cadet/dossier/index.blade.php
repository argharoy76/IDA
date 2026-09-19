@extends('layouts.portal')

@section('title', 'My Digital Cadet Dossier')
@section('page_title', 'Digital Cadet Dossier: ' . ($student->user->name ?? 'Cadet'))
@section('page_subtitle', 'Personal 360-degree military competence radar, assessor evaluations, and progress milestones')

@section('topbar_actions')
  @if(false)
  <button type="button" onclick="window.print()" class="btn-tactical btn-tactical-outline">
    <i class="fa-solid fa-print"></i> Export Dossier Report
  </button>
  @endif
@endsection

@section('content')
<style>
  .dossier-two-col-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
  }
  @media (max-width: 991px) {
    .dossier-two-col-grid {
      grid-template-columns: 1fr;
      gap: 20px;
    }
    .dossier-scores-grid {
      grid-template-columns: repeat(auto-fit, minmax(65px, 1fr)) !important;
    }
    .dossier-stat-wrap {
      width: 100%;
      justify-content: flex-start;
    }
  }
</style>
<div style="display: flex; flex-direction: column; gap: 24px;">

  <!-- Dossier Module Notice -->
  <div class="tactical-card" style="text-align: center; padding: 48px 24px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 18px;">
    <div style="width: 64px; height: 64px; border-radius: 50%; background: #eff6ff; color: #3b82f6; display: flex; align-items: center; justify-content: center; font-size: 28px; margin: 0 auto 16px auto;">
      <i class="fa-solid fa-id-card-clip"></i>
    </div>
    <h3 style="font-size: 18px; font-weight: 800; color: #0f172a; margin: 0 0 8px 0;">Cadet Dossier Currently Hidden</h3>
    <p style="font-size: 13.5px; color: #64748b; margin: 0 auto 20px auto; max-width: 480px; line-height: 1.5;">
      The 360-degree military competence dossier and evaluations are currently hidden. Please proceed to the Exam portal.
    </p>
    <a href="{{ route('cadet.exams.index') }}" class="btn-tactical btn-tactical-primary" style="display: inline-flex; align-items: center; gap: 8px; text-decoration: none; padding: 10px 20px; border-radius: 10px;">
      <i class="fa-solid fa-clipboard-list"></i> Go to Exams
    </a>
  </div>

</div>

{{-- Hidden dossier competence metrics and charts - Code preserved intact for future activation --}}
@if(false)
<div style="display: flex; flex-direction: column; gap: 24px;">

  <!-- Identification Header -->
  <div class="tactical-card cadet-emerald-hero" style="background: linear-gradient(135deg, #064e3b 0%, #047857 100%) !important; color: #ffffff; border: 1px solid #059669; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
    <div style="display: flex; align-items: center; gap: 18px; flex-wrap: wrap;">
      <div style="width: 70px; height: 70px; border-radius: 50%; background: linear-gradient(135deg, #022c22, #064e3b); border: 2px solid #f59e0b; color: #fff; display: grid; place-items: center; font-size: 26px; font-weight: 800; flex-shrink: 0;">
        {{ strtoupper(substr($student->user->name ?? 'C', 0, 1)) }}
      </div>
      <div>
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px; flex-wrap: wrap;">
          <span class="badge" style="background: #fffbeb; color: #b45309; border: 1px solid #fde68a; font-family: 'Poppins', monospace; font-size: 13px; font-weight: 700;">{{ $student->student_id_code }}</span>
          <span class="badge" style="background: rgba(255,255,255,0.18); color: #ffffff; border: 1px solid rgba(255,255,255,0.25);">Roll: {{ $student->roll_number }}</span>
          <span class="badge" style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;">ACTIVE SERVICE CADET</span>
        </div>
        <h2 style="font-size: 24px; font-weight: 800; margin: 0; color: #ffffff !important;">Cadet {{ $student->user->name ?? 'Cadet' }}</h2>
        <div style="font-size: 13px; color: #d1fae5; margin-top: 4px;">
          Course: <strong style="color: #ffffff;">{{ $student->currentCourse->name ?? 'ISSB Course' }}</strong> | Squadron: <strong style="color: #ffffff;">{{ $student->currentBatch->name ?? 'Squadron' }}</strong>
        </div>
      </div>
    </div>

    <!-- Quick Stats -->
    <div class="dossier-stat-wrap" style="display: flex; gap: 14px; flex-wrap: wrap;">
      <div style="text-align: center; padding: 10px 18px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.15); border-radius: 8px; flex: 1; min-width: 120px;">
        <div style="font-size: 10px; text-transform: uppercase; color: #a7f3d0;">Attendance</div>
        <div style="font-size: 22px; font-weight: 800; font-family: 'Poppins', monospace; color: {{ $student->attendance_percentage < 75 ? '#f87171' : '#34d399' }};">
          {{ $student->attendance_percentage }}%
        </div>
      </div>

      <div style="text-align: center; padding: 10px 18px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.15); border-radius: 8px; flex: 1; min-width: 140px;">
        <div style="font-size: 10px; text-transform: uppercase; color: #a7f3d0;">Overall Competence</div>
        @php
          $avg = $student->dossier ? round(($student->dossier->iq_score + $student->dossier->leadership_score + $student->dossier->communication_score + $student->dossier->physical_fitness_score + $student->dossier->discipline_score) / 5, 1) : 0;
        @endphp
        <div style="font-size: 22px; font-weight: 800; font-family: 'Poppins', monospace; color: #93c5fd;">
          {{ $avg }}/100
        </div>
      </div>
    </div>
  </div>

  <!-- Radar Chart & Strengths / Weaknesses Grid -->
  <div class="dossier-two-col-grid">
    <!-- 5-Axis Competence Radar -->
    <div class="tactical-card">
      <div style="border-bottom: 1px solid var(--border-soft); padding-bottom: 12px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-size: 15px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-chart-pie" style="color: var(--accent-gold);"></i> 5-Axis Competence Radar
        </h3>
        <span style="font-size: 11px; color: var(--text-muted);">Military Standard (100 Scale)</span>
      </div>

      <div style="height: 300px; display: flex; align-items: center; justify-content: center;">
        <canvas id="cadetRadar"></canvas>
      </div>

      @if($student->dossier)
        <div class="dossier-scores-grid" style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 8px; margin-top: 14px; text-align: center;">
          <div style="background: var(--surface-subtle); border: 1px solid var(--border-soft); padding: 10px 4px; border-radius: 8px;">
            <div style="font-size: 10.5px; color: var(--text-muted); font-weight: 700;">IQ</div>
            <strong style="color: #2563eb; font-size: 16px; font-weight: 800;">{{ $student->dossier->iq_score }}</strong>
          </div>
          <div style="background: var(--surface-subtle); border: 1px solid var(--border-soft); padding: 10px 4px; border-radius: 8px;">
            <div style="font-size: 10.5px; color: var(--text-muted); font-weight: 700;">LEADER</div>
            <strong style="color: #059669; font-size: 16px; font-weight: 800;">{{ $student->dossier->leadership_score }}</strong>
          </div>
          <div style="background: var(--surface-subtle); border: 1px solid var(--border-soft); padding: 10px 4px; border-radius: 8px;">
            <div style="font-size: 10.5px; color: var(--text-muted); font-weight: 700;">COMM</div>
            <strong style="color: #d97706; font-size: 16px; font-weight: 800;">{{ $student->dossier->communication_score }}</strong>
          </div>
          <div style="background: var(--surface-subtle); border: 1px solid var(--border-soft); padding: 10px 4px; border-radius: 8px;">
            <div style="font-size: 10.5px; color: var(--text-muted); font-weight: 700;">PHYS</div>
            <strong style="color: #db2777; font-size: 16px; font-weight: 800;">{{ $student->dossier->physical_fitness_score }}</strong>
          </div>
          <div style="background: var(--surface-subtle); border: 1px solid var(--border-soft); padding: 10px 4px; border-radius: 8px;">
            <div style="font-size: 10.5px; color: var(--text-muted); font-weight: 700;">DISC</div>
            <strong style="color: #7c3aed; font-size: 16px; font-weight: 800;">{{ $student->dossier->discipline_score }}</strong>
          </div>
        </div>
      @endif
    </div>

    <!-- Strengths & Growth Areas -->
    <div class="tactical-card">
      <div style="border-bottom: 1px solid var(--border-soft); padding-bottom: 12px; margin-bottom: 16px;">
        <h3 style="font-size: 15px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-list-check" style="color: var(--accent-gold);"></i> Strengths & Growth Areas
        </h3>
      </div>

      <!-- Strengths -->
      <div style="margin-bottom: 18px;">
        <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #10b981; margin-bottom: 8px;">
          <i class="fa-solid fa-shield-check"></i> Recognized Strengths
        </div>
        <div style="display: flex; flex-direction: column; gap: 8px;">
          @forelse($student->strengths as $st)
            <div style="padding: 8px 12px; background: rgba(16, 185, 129, 0.08); border-left: 3px solid #10b981; border-radius: 4px; display: flex; justify-content: space-between; align-items: center;">
              <div>
                <strong style="font-size: 13px; color: var(--text-main);">{{ $st->title }}</strong>
                <div style="font-size: 11px; color: var(--text-muted);">{{ $st->category }}</div>
              </div>
              <span class="badge badge-emerald">{{ strtoupper($st->status) }}</span>
            </div>
          @empty
            <div style="font-size: 12px; color: var(--text-muted);">Pending initial assessor evaluation.</div>
          @endforelse
        </div>
      </div>

      <!-- Weaknesses -->
      <div>
        <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #ef4444; margin-bottom: 8px;">
          <i class="fa-solid fa-triangle-exclamation"></i> Growth Areas Under Development
        </div>
        <div style="display: flex; flex-direction: column; gap: 8px;">
          @forelse($student->weaknesses as $wk)
            <div style="padding: 8px 12px; background: rgba(239, 68, 68, 0.08); border-left: 3px solid #ef4444; border-radius: 4px; display: flex; justify-content: space-between; align-items: center;">
              <div>
                <strong style="font-size: 13px; color: var(--text-main);">{{ $wk->title }}</strong>
                <div style="font-size: 11px; color: var(--text-muted);">{{ $wk->category }} | Priority: {{ strtoupper($wk->priority) }}</div>
              </div>
              <span class="badge {{ $wk->status === 'resolved' ? 'badge-emerald' : ($wk->status === 'improving' ? 'badge-gold' : 'badge-danger') }}">
                {{ strtoupper($wk->status) }}
              </span>
            </div>
          @empty
            <div style="font-size: 12px; color: var(--text-muted);">No recorded weaknesses.</div>
          @endforelse
        </div>
      </div>
    </div>
  </div>

  <!-- Improvement Plans -->
  <div class="tactical-card" style="padding: 0; overflow: hidden;">
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-soft);">
      <h3 style="font-size: 15px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-bullseye" style="color: var(--accent-gold);"></i> Assigned Action Plans
      </h3>
    </div>

    <div style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
      <table class="tactical-table">
        <thead>
          <tr>
            <th>Focus Area</th>
            <th>Target Goal</th>
            <th>Prescribed Routine / Task</th>
            <th>Deadline</th>
            <th>Progress</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($student->improvementPlans as $plan)
            <tr>
              <td><strong>{{ $plan->problem_description }}</strong></td>
              <td>{{ $plan->target_objective }}</td>
              <td><small style="color: var(--text-muted);">{{ $plan->assigned_task ?? 'Continuous practice' }}</small></td>
              <td><span style="font-size: 12px;">{{ \Carbon\Carbon::parse($plan->deadline)->format('d M Y') }}</span></td>
              <td>
                <div style="display: flex; align-items: center; gap: 8px;">
                  <div style="flex: 1; height: 6px; background: rgba(255,255,255,0.06); border-radius: 999px; overflow: hidden;">
                    <div style="width: {{ $plan->progress_percentage }}%; height: 100%; background: #10b981;"></div>
                  </div>
                  <span style="font-size: 11px; color: var(--text-muted);">{{ $plan->progress_percentage }}%</span>
                </div>
              </td>
              <td>
                <span class="badge {{ $plan->status === 'completed' ? 'badge-emerald' : 'badge-gold' }}">{{ strtoupper($plan->status) }}</span>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" style="text-align: center; padding: 28px; color: var(--text-muted);">
                No improvement plans assigned currently.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- Instructor Observations Log (Student-Visible Only) -->
  <div class="tactical-card" style="padding: 0; overflow: hidden;">
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-soft);">
      <h3 style="font-size: 15px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-comments" style="color: var(--accent-gold);"></i> Assessor Feedback & Directives
      </h3>
    </div>

    <div style="padding: 16px; display: flex; flex-direction: column; gap: 14px;">
      @forelse($student->observations as $obs)
        <div style="background: rgba(15, 23, 42, 0.4); border: 1px solid var(--border-soft); border-radius: 8px; padding: 14px;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <div style="display: flex; align-items: center; gap: 10px;">
              <span class="badge badge-navy">{{ $obs->category }}</span>
              <div style="display: flex; align-items: center; gap: 3px;">
                @for($i = 1; $i <= 5; $i++)
                  <i class="fa-solid fa-star" style="font-size: 11px; color: {{ $i <= $obs->rating ? '#f59e0b' : 'rgba(255,255,255,0.1)' }};"></i>
                @endfor
              </div>
            </div>
            <span style="font-size: 12px; color: var(--text-muted);">{{ \Carbon\Carbon::parse($obs->observation_date)->format('d M Y') }}</span>
          </div>

          <p style="font-size: 13.5px; color: var(--text-body); line-height: 1.6; margin: 0 0 10px 0;">
            {{ $obs->observation_text }}
          </p>

          @if($obs->recommended_action)
            <div style="font-size: 12px; color: var(--brand-deep); background: var(--surface-subtle); border: 1px solid var(--border-soft); padding: 8px 12px; border-radius: 6px;">
              <strong>Assessor Directive:</strong> {{ $obs->recommended_action }}
            </div>
          @endif
        </div>
      @empty
        <div style="text-align: center; padding: 32px; color: var(--text-muted);">
          No assessor feedback shared yet. Keep participating actively in mock ISSB and physical drills.
        </div>
      @endforelse
    </div>
  </div>

</div>

<!-- Radar Chart JS -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('cadetRadar').getContext('2d');
    new Chart(ctx, {
      type: 'radar',
      data: {
        labels: ['Intelligence (IQ)', 'Leadership', 'Communication', 'Physical Fitness', 'Discipline'],
        datasets: [{
          label: 'Cadet Proficiency',
          data: [
            {{ $student->dossier->iq_score ?? 75 }},
            {{ $student->dossier->leadership_score ?? 75 }},
            {{ $student->dossier->communication_score ?? 75 }},
            {{ $student->dossier->physical_fitness_score ?? 75 }},
            {{ $student->dossier->discipline_score ?? 80 }}
          ],
          backgroundColor: 'rgba(16, 185, 129, 0.25)',
          borderColor: '#10b981',
          pointBackgroundColor: '#d97706',
          pointBorderColor: '#fff',
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          r: {
            angleLines: { color: 'rgba(255, 255, 255, 0.1)' },
            grid: { color: 'rgba(255, 255, 255, 0.08)' },
            pointLabels: { color: '#94a3b8', font: { size: 11, family: "'Plus Jakarta Sans', sans-serif" } },
            ticks: { display: false, min: 0, max: 100 }
          }
        },
        plugins: { legend: { display: false } }
      }
    });
  });
</script>
@endif
@endsection
