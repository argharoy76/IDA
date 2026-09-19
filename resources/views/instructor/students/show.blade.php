@extends('layouts.portal')

@section('title', 'Cadet Dossier: ' . ($student->user->name ?? 'Cadet'))
@section('page_title', 'Cadet Dossier: ' . ($student->user->name ?? 'Cadet'))
@section('page_subtitle', 'Military profile, 5-axis competence radar, instructor observations, and targeted improvement plans')

@section('topbar_actions')
  <div style="display: flex; gap: 8px;">
    <a href="{{ route('instructor.students.index') }}" class="btn-tactical btn-tactical-outline">
      <i class="fa-solid fa-arrow-left"></i> Back to Roster
    </a>
    <button type="button" class="btn-tactical btn-tactical-primary" onclick="openObsModal()">
      <i class="fa-solid fa-eye"></i> Log Observation
    </button>
  </div>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

  <!-- Cadet Identification & Quick Stats Strip -->
  <div class="tactical-card" style="background: linear-gradient(135deg, rgba(15, 23, 42, 0.9), rgba(6, 78, 59, 0.35)); border: 1px solid var(--border-soft); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
    <div style="display: flex; align-items: center; gap: 18px;">
      <div style="width: 64px; height: 64px; border-radius: 50%; background: linear-gradient(135deg, var(--brand-deep), var(--accent-navy)); border: 2px solid var(--accent-gold); color: #fff; display: grid; place-items: center; font-size: 24px; font-weight: 800;">
        {{ strtoupper(substr($student->user->name ?? 'C', 0, 1)) }}
      </div>
      <div>
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
          <span class="badge badge-gold" style="font-family: 'Plus Jakarta Sans', monospace; font-size: 12px;">{{ $student->student_id_code }}</span>
          <span class="badge badge-navy">Roll: {{ $student->roll_number }}</span>
          @if($student->requires_attention)
            <span class="badge badge-danger"><i class="fa-solid fa-triangle-exclamation"></i> INTERVENTION REQUIRED</span>
          @endif
        </div>
        <h2 style="font-size: 22px; font-weight: 800; margin: 0; color: #fff;">{{ $student->user->name ?? 'Cadet' }}</h2>
        <div style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">
          Squadron: <strong style="color: #fff;">{{ $student->currentBatch->name ?? 'Squadron' }}</strong> | Course: {{ $student->currentCourse->name ?? 'Course' }}
        </div>
      </div>
    </div>

    <!-- Right Quick Badges -->
    <div style="display: flex; gap: 16px;">
      <div style="text-align: center; padding: 10px 16px; background: rgba(0,0,0,0.3); border-radius: 8px;">
        <div style="font-size: 10px; text-transform: uppercase; color: var(--text-muted);">Attendance</div>
        <div style="font-size: 20px; font-weight: 800; font-family: 'Plus Jakarta Sans', monospace; color: {{ $student->attendance_percentage < 75 ? '#ef4444' : '#10b981' }};">
          {{ $student->attendance_percentage }}%
        </div>
      </div>

      <div style="text-align: center; padding: 10px 16px; background: rgba(0,0,0,0.3); border-radius: 8px;">
        <div style="font-size: 10px; text-transform: uppercase; color: var(--text-muted);">Observations</div>
        <div style="font-size: 20px; font-weight: 800; font-family: 'Plus Jakarta Sans', monospace; color: #60a5fa;">
          {{ $student->observations->count() }}
        </div>
      </div>
    </div>
  </div>

  <!-- Two Column Layout: Competence Radar + Strengths/Weaknesses -->
  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
    <!-- 5-Axis Radar Chart -->
    <div class="tactical-card">
      <div style="border-bottom: 1px solid var(--border-soft); padding-bottom: 12px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-size: 15px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-chart-pie" style="color: var(--accent-gold);"></i> 5-Axis Competence Radar
        </h3>
        <span style="font-size: 11px; color: var(--text-muted);">Military Standard (100-pt scale)</span>
      </div>

      <div style="height: 300px; display: flex; align-items: center; justify-content: center;">
        <canvas id="radarChart"></canvas>
      </div>

      @if($student->dossier)
        <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 8px; margin-top: 14px; text-align: center;">
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

    <!-- Strengths & Weaknesses Tracker -->
    <div class="tactical-card">
      <div style="border-bottom: 1px solid var(--border-soft); padding-bottom: 12px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-size: 15px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-list-check" style="color: var(--accent-gold);"></i> Strengths & Weaknesses
        </h3>
        <button type="button" class="btn-tactical btn-tactical-outline" style="padding: 4px 10px; font-size: 11px;" onclick="openSwModal()">
          <i class="fa-solid fa-plus"></i> Add Item
        </button>
      </div>

      <!-- Strengths -->
      <div style="margin-bottom: 16px;">
        <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: #10b981; margin-bottom: 8px;">
          <i class="fa-solid fa-shield-check"></i> Key Strengths
        </div>
        <div style="display: flex; flex-direction: column; gap: 8px;">
          @forelse($student->strengths as $st)
            <div style="padding: 8px 12px; background: rgba(16, 185, 129, 0.08); border-left: 3px solid #10b981; border-radius: 4px; display: flex; justify-content: space-between; align-items: center;">
              <div>
                <strong style="font-size: 12.5px; color: var(--text-main);">{{ $st->title }}</strong>
                <div style="font-size: 11px; color: var(--text-muted);">{{ $st->category }}</div>
              </div>
              <span class="badge badge-emerald">{{ strtoupper($st->status) }}</span>
            </div>
          @empty
            <div style="font-size: 12px; color: var(--text-muted);">No key strengths recorded yet.</div>
          @endforelse
        </div>
      </div>

      <!-- Weaknesses -->
      <div>
        <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: #ef4444; margin-bottom: 8px;">
          <i class="fa-solid fa-triangle-exclamation"></i> Growth Areas / Weaknesses
        </div>
        <div style="display: flex; flex-direction: column; gap: 8px;">
          @forelse($student->weaknesses as $wk)
            <div style="padding: 8px 12px; background: rgba(239, 68, 68, 0.08); border-left: 3px solid #ef4444; border-radius: 4px; display: flex; justify-content: space-between; align-items: center;">
              <div>
                <strong style="font-size: 12.5px; color: var(--text-main);">{{ $wk->title }}</strong>
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

  <!-- Improvement Tasks & Actionable Plans -->
  <div class="tactical-card" style="padding: 0; overflow: hidden;">
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-soft); display: flex; justify-content: space-between; align-items: center;">
      <h3 style="font-size: 15px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-bullseye" style="color: var(--accent-gold);"></i> Actionable Improvement Plans
      </h3>
      <button type="button" class="btn-tactical btn-tactical-outline" style="padding: 4px 10px; font-size: 11px;" onclick="openPlanModal()">
        <i class="fa-solid fa-plus"></i> Assign New Plan
      </button>
    </div>

    <table class="tactical-table">
      <thead>
        <tr>
          <th>Problem Area</th>
          <th>Target Objective</th>
          <th>Assigned Task</th>
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
            <td><small style="color: var(--text-muted);">{{ $plan->assigned_task ?? 'General practice' }}</small></td>
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
            <td colspan="6" style="text-align: center; padding: 24px; color: var(--text-muted);">
              No active improvement tasks assigned.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Dated Instructor Observations Log -->
  <div class="tactical-card" style="padding: 0; overflow: hidden;">
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-soft);">
      <h3 style="font-size: 15px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-comments" style="color: var(--accent-gold);"></i> Chronological Instructor Observations Log
      </h3>
    </div>

    <div style="padding: 16px; display: flex; flex-direction: column; gap: 14px;">
      @forelse($student->observations as $obs)
        <div style="background: rgba(15, 23, 42, 0.4); border: 1px solid var(--border-soft); border-radius: 8px; padding: 14px;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <div style="display: flex; align-items: center; gap: 10px;">
              <span class="badge badge-navy">{{ $obs->category }}</span>
              <span class="badge {{ $obs->visibility === 'student_visible' ? 'badge-emerald' : 'badge-gold' }}">
                {{ strtoupper(str_replace('_', ' ', $obs->visibility)) }}
              </span>
            </div>
            <span style="font-size: 12px; color: var(--text-muted);">{{ \Carbon\Carbon::parse($obs->observation_date)->format('d M Y') }}</span>
          </div>

          <div style="display: flex; align-items: center; gap: 4px; margin-bottom: 8px;">
            @for($i = 1; $i <= 5; $i++)
              <i class="fa-solid fa-star" style="font-size: 12px; color: {{ $i <= $obs->rating ? '#f59e0b' : '#cbd5e1' }};"></i>
            @endfor
            <span style="font-size: 11px; color: var(--text-muted); margin-left: 6px;">Evaluated {{ $obs->rating }}/5</span>
          </div>

          <p style="font-size: 13.5px; color: var(--text-body); line-height: 1.6; margin: 0 0 10px 0;">
            {{ $obs->observation_text }}
          </p>

          @if($obs->recommended_action)
            <div style="font-size: 12px; color: var(--brand-deep); background: var(--surface-subtle); border: 1px solid var(--border-soft); padding: 8px 12px; border-radius: 6px;">
              <strong>Action Directive:</strong> {{ $obs->recommended_action }}
            </div>
          @endif
        </div>
      @empty
        <div style="text-align: center; padding: 32px; color: var(--text-muted);">
          No observations recorded yet. Use the "Log Observation" button to enter formal military notes.
        </div>
      @endforelse
    </div>
  </div>

</div>

<!-- Modal: Log Observation -->
<div id="obsModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center;">
  <div class="tactical-card" style="width: 100%; max-width: 540px; margin: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-soft); padding-bottom: 12px; margin-bottom: 16px;">
      <h3 style="font-size: 15px; font-weight: 700; margin: 0; color: var(--accent-gold);">
        <i class="fa-solid fa-eye"></i> Log Cadet Performance Observation
      </h3>
      <button onclick="closeObsModal()" style="background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 18px;">&times;</button>
    </div>

    <form action="{{ route('instructor.observations.store') }}" method="POST">
      @csrf
      <input type="hidden" name="student_id" value="{{ $student->id }}">

      <div style="display: flex; flex-direction: column; gap: 14px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Domain Category *</label>
            <select name="category" class="form-tactical" required>
              <option value="Leadership & Initiative">Leadership & Initiative</option>
              <option value="IQ & Situation Reaction">IQ & Situation Reaction</option>
              <option value="English Speech & GD">English Speech & GD</option>
              <option value="Physical Obstacle & Endurance">Physical Obstacle & Endurance</option>
              <option value="Military Bearing & Discipline">Military Bearing & Discipline</option>
            </select>
          </div>

          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Assessor Rating (1-5) *</label>
            <select name="rating" class="form-tactical" required>
              <option value="5">5 - Outstanding</option>
              <option value="4" selected>4 - Above Average</option>
              <option value="3">3 - Satisfactory</option>
              <option value="2">2 - Needs Work</option>
              <option value="1">1 - Deficient</option>
            </select>
          </div>
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Observation Body *</label>
          <textarea name="observation_text" rows="3" class="form-tactical" placeholder="Detailed tactical behavior, reaction to stress, clarity of thought..." required></textarea>
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Recommended Action</label>
          <input type="text" name="recommended_action" class="form-tactical" placeholder="e.g. Daily extempore speech drill, obstacle rope practice">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Visibility Protocol *</label>
            <select name="visibility" class="form-tactical" required>
              <option value="student_visible">Cadet Visible</option>
              <option value="instructor_only">Instructor Wing Only</option>
              <option value="admin_only">Command HQ Only</option>
            </select>
          </div>

          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Follow-up Date</label>
            <input type="date" name="follow_up_date" class="form-tactical">
          </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
          <button type="button" onclick="closeObsModal()" class="btn-tactical btn-tactical-outline">Cancel</button>
          <button type="submit" class="btn-tactical btn-tactical-primary">Save Observation</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Add Strength / Weakness -->
<div id="swModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center;">
  <div class="tactical-card" style="width: 100%; max-width: 500px; margin: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-soft); padding-bottom: 12px; margin-bottom: 16px;">
      <h3 style="font-size: 15px; font-weight: 700; margin: 0;">Record Strength / Weakness</h3>
      <button onclick="closeSwModal()" style="background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 18px;">&times;</button>
    </div>

    <form action="{{ route('instructor.strengths_weaknesses.store') }}" method="POST">
      @csrf
      <input type="hidden" name="student_id" value="{{ $student->id }}">

      <div style="display: flex; flex-direction: column; gap: 12px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Type *</label>
            <select name="type" class="form-tactical" required>
              <option value="strength">Strength</option>
              <option value="weakness">Weakness</option>
            </select>
          </div>
          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Priority *</label>
            <select name="priority" class="form-tactical" required>
              <option value="low">Low</option>
              <option value="medium" selected>Medium</option>
              <option value="high">High</option>
              <option value="critical">Critical</option>
            </select>
          </div>
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Category *</label>
          <input type="text" name="category" class="form-tactical" placeholder="e.g. Communication, Endurance, Matrix IQ" required>
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Title / Summary *</label>
          <input type="text" name="title" class="form-tactical" placeholder="e.g. Hesitates during English extempore" required>
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Status *</label>
          <select name="status" class="form-tactical" required>
            <option value="identified">Identified</option>
            <option value="improving">Improving</option>
            <option value="improved">Improved</option>
            <option value="resolved">Resolved</option>
          </select>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
          <button type="button" onclick="closeSwModal()" class="btn-tactical btn-tactical-outline">Cancel</button>
          <button type="submit" class="btn-tactical btn-tactical-primary">Save to Dossier</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Add Improvement Plan -->
<div id="planModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center;">
  <div class="tactical-card" style="width: 100%; max-width: 540px; margin: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-soft); padding-bottom: 12px; margin-bottom: 16px;">
      <h3 style="font-size: 15px; font-weight: 700; margin: 0; color: var(--accent-gold);">Assign Targeted Improvement Plan</h3>
      <button onclick="closePlanModal()" style="background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 18px;">&times;</button>
    </div>

    <form action="{{ route('instructor.improvement_plans.store') }}" method="POST">
      @csrf
      <input type="hidden" name="student_id" value="{{ $student->id }}">

      <div style="display: flex; flex-direction: column; gap: 12px;">
        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Problem Identified *</label>
          <input type="text" name="problem_description" class="form-tactical" placeholder="e.g. Non-verbal matrix reasoning speed" required>
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Target Objective *</label>
          <input type="text" name="target_objective" class="form-tactical" placeholder="e.g. Complete 50 matrix puzzles under 30 minutes with 80% accuracy" required>
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Assigned Task / Routine</label>
          <textarea name="assigned_task" rows="2" class="form-tactical" placeholder="Specific daily drills or reading requirements..."></textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Deadline *</label>
            <input type="date" name="deadline" value="{{ date('Y-m-d', strtotime('+14 days')) }}" class="form-tactical" required>
          </div>
          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Status *</label>
            <select name="status" class="form-tactical" required>
              <option value="pending">Pending</option>
              <option value="in_progress" selected>In Progress</option>
              <option value="completed">Completed</option>
            </select>
          </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
          <button type="button" onclick="closePlanModal()" class="btn-tactical btn-tactical-outline">Cancel</button>
          <button type="submit" class="btn-tactical btn-tactical-primary">Assign Plan</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Chart.js Radar Initialization -->
<script>
  function openObsModal() { document.getElementById('obsModal').style.display = 'flex'; }
  function closeObsModal() { document.getElementById('obsModal').style.display = 'none'; }
  function openSwModal() { document.getElementById('swModal').style.display = 'flex'; }
  function closeSwModal() { document.getElementById('swModal').style.display = 'none'; }
  function openPlanModal() { document.getElementById('planModal').style.display = 'flex'; }
  function closePlanModal() { document.getElementById('planModal').style.display = 'none'; }

  document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('radarChart').getContext('2d');
    new Chart(ctx, {
      type: 'radar',
      data: {
        labels: ['Intelligence (IQ)', 'Leadership', 'Communication', 'Physical Fitness', 'Discipline'],
        datasets: [{
          label: 'Current Cadet Competence',
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
          pointHoverBackgroundColor: '#fff',
          pointHoverBorderColor: '#10b981'
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
        plugins: {
          legend: { display: false }
        }
      }
    });
  });
</script>
@endsection
