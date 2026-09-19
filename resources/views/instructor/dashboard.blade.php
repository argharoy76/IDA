@extends('layouts.portal')

@section('title', 'Instructor Wing Command')
@section('page_title', 'Instructor Flight Deck')
@section('page_subtitle', 'Cadet development tracking, drill evaluations, daily schedule, and muster rolls')

@section('topbar_actions')
  <a href="{{ route('instructor.attendance.index') }}" class="btn-tactical btn-tactical-primary">
    <i class="fa-solid fa-clipboard-user"></i> Roll Call Attendance
  </a>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

  <!-- Instructor Rank & Assigned Batches Strip -->
  <div class="tactical-card" style="background: linear-gradient(135deg, rgba(15, 23, 42, 0.9), rgba(6, 78, 59, 0.35)); border: 1px solid var(--border-soft); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
    <div>
      <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 6px;">
        <span class="badge badge-emerald">{{ $instructor->military_rank ?? 'Assessor' }}</span>
        <span class="badge badge-navy">{{ $instructor->specialization ?? 'ISSB Assessor' }}</span>
      </div>
      <h2 style="font-size: 22px; font-weight: 800; margin: 0; color: #fff;">
        {{ $instructor->user->name ?? auth()->user()->name }}
      </h2>
      <p style="font-size: 13px; color: var(--text-muted); margin: 4px 0 0 0;">
        Military ID / Code: <strong style="color: var(--accent-gold); font-family: 'Plus Jakarta Sans', monospace;">{{ $instructor->instructor_code ?? 'INST-01' }}</strong> | Assigned Batches: 
        @forelse($instructor->batches ?? [] as $b)
          <span style="color: #fff; font-weight: 600;">{{ $b->name }}</span>{{ !$loop->last ? ', ' : '' }}
        @empty
          <span style="color: var(--text-muted);">No Squadrons Assigned</span>
        @endforelse
      </p>
    </div>

    <div style="display: flex; gap: 10px;">
      <a href="{{ route('instructor.students.index') }}" class="btn-tactical btn-tactical-outline">
        <i class="fa-solid fa-users"></i> View All Assigned Cadets ({{ $assignedStudents->count() }})
      </a>
    </div>
  </div>

  <!-- Key Metrics Strip -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
    <div class="stat-card">
      <div class="stat-icon" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
        <i class="fa-solid fa-users"></i>
      </div>
      <div>
        <div class="stat-label">Assigned Cadets</div>
        <div class="stat-value">{{ $assignedStudents->count() }}</div>
      </div>
    </div>

    <!-- At Risk Early Warning -->
    <div class="stat-card" style="border: 1px solid {{ $studentsRequiringAttention->count() > 0 ? 'rgba(239, 68, 68, 0.4)' : 'var(--border-soft)' }};">
      <div class="stat-icon" style="background: rgba(239, 68, 68, 0.15); color: #ef4444;">
        <i class="fa-solid fa-triangle-exclamation"></i>
      </div>
      <div>
        <div class="stat-label">Cadets Requiring Attention</div>
        <div class="stat-value" style="color: #ef4444;">{{ $studentsRequiringAttention->count() }}</div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa;">
        <i class="fa-solid fa-calendar-day"></i>
      </div>
      <div>
        <div class="stat-label">Classes Scheduled Today</div>
        <div class="stat-value">{{ $todayClasses->count() }}</div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon" style="background: rgba(217, 119, 6, 0.15); color: #d97706;">
        <i class="fa-solid fa-eye"></i>
      </div>
      <div>
        <div class="stat-label">Logged Observations</div>
        <div class="stat-value">{{ $recentObservations->count() }}</div>
      </div>
    </div>
  </div>

  <!-- At-Risk Early Warning Alert (If any) -->
  @if($studentsRequiringAttention->count() > 0)
    <div class="tactical-card" style="border: 1px solid rgba(239, 68, 68, 0.4); padding: 0; overflow: hidden;">
      <div style="padding: 14px 20px; background: rgba(239, 68, 68, 0.08); border-bottom: 1px solid rgba(239, 68, 68, 0.2); display: flex; align-items: center; justify-content: space-between;">
        <h3 style="font-size: 14px; font-weight: 700; color: #ef4444; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-bell"></i> Cadet Early Warning Alert: Immediate Intervention Recommended
        </h3>
        <span class="badge badge-danger">{{ $studentsRequiringAttention->count() }} Cadets Flagged</span>
      </div>

      <table class="tactical-table">
        <thead>
          <tr>
            <th>Cadet ID</th>
            <th>Name</th>
            <th>Squadron</th>
            <th>Attendance %</th>
            <th>Critical Development Areas</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($studentsRequiringAttention as $atRisk)
            <tr>
              <td>
                <span style="font-family: 'Plus Jakarta Sans', monospace; font-weight: 700; color: var(--accent-gold);">{{ $atRisk->student_id_code }}</span>
              </td>
              <td>
                <strong style="color: var(--text-main);">{{ $atRisk->user->name ?? 'Cadet' }}</strong>
              </td>
              <td>{{ $atRisk->currentBatch->name ?? 'Squadron' }}</td>
              <td>
                <span style="font-family: 'Plus Jakarta Sans', monospace; font-weight: 800; color: {{ $atRisk->attendance_percentage < 75 ? '#ef4444' : '#10b981' }};">
                  {{ $atRisk->attendance_percentage }}%
                </span>
              </td>
              <td>
                @forelse($atRisk->weaknesses->where('priority', 'critical') as $w)
                  <span class="badge badge-danger" style="margin-right: 4px;">{{ $w->title }}</span>
                @empty
                  <span style="font-size: 12px; color: var(--text-muted);">Attendance below military 75% threshold</span>
                @endforelse
              </td>
              <td>
                <a href="{{ route('instructor.students.show', $atRisk->id) }}" class="btn-tactical btn-tactical-primary" style="padding: 4px 10px; font-size: 11px;">
                  <i class="fa-solid fa-address-card"></i> Open Dossier
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif

  <!-- Two Column Layout: Today's Routine & Recent Observations -->
  <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 20px;">
    <!-- Today's Routine Schedule -->
    <div class="tactical-card" style="padding: 0; overflow: hidden;">
      <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-soft); display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-size: 15px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i class="fa-regular fa-clock" style="color: var(--accent-gold);"></i> Today's Schedule ({{ date('d M Y') }})
        </h3>
      </div>

      <table class="tactical-table">
        <thead>
          <tr>
            <th>Time</th>
            <th>Batch / Squadron</th>
            <th>Subject / Drill</th>
            <th>Room / Ground</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($todayClasses as $c)
            <tr>
              <td>
                <span style="font-family: 'Plus Jakarta Sans', monospace; font-weight: 700; color: #60a5fa;">
                  {{ \Carbon\Carbon::parse($c->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($c->end_time)->format('h:i A') }}
                </span>
              </td>
              <td><strong>{{ $c->batch->name ?? 'All' }}</strong></td>
              <td>{{ $c->subject }}</td>
              <td><small style="color: var(--text-muted);">{{ $c->room_no ?? 'Tactical Ground' }}</small></td>
              <td>
                <span class="badge {{ $c->status === 'completed' ? 'badge-emerald' : 'badge-gold' }}">{{ strtoupper($c->status) }}</span>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" style="text-align: center; padding: 36px; color: var(--text-muted);">
                No classes assigned to you for today.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Recent Recorded Observations -->
    <div class="tactical-card" style="padding: 0; overflow: hidden;">
      <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-soft);">
        <h3 style="font-size: 15px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-clipboard-check" style="color: var(--accent-gold);"></i> Your Recent Observations
        </h3>
      </div>

      <div style="padding: 16px; display: flex; flex-direction: column; gap: 12px;">
        @forelse($recentObservations as $obs)
          <div style="background: var(--surface-subtle); border: 1px solid var(--border-soft); border-radius: 8px; padding: 12px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
              <strong style="color: var(--text-main); font-size: 13.5px;">{{ $obs->student->user->name ?? 'Cadet' }}</strong>
              <span class="badge badge-gold">{{ $obs->category }}</span>
            </div>
            <div style="display: flex; align-items: center; gap: 4px; margin-bottom: 6px;">
              @for($i = 1; $i <= 5; $i++)
                <i class="fa-solid fa-star" style="font-size: 11px; color: {{ $i <= $obs->rating ? '#f59e0b' : '#cbd5e1' }};"></i>
              @endfor
              <span style="font-size: 11px; color: var(--text-muted); margin-left: 6px;">Rating {{ $obs->rating }}/5</span>
            </div>
            <p style="font-size: 12px; color: var(--text-muted); margin: 0;">{{ Str::limit($obs->observation_text, 90) }}</p>
          </div>
        @empty
          <div style="text-align: center; padding: 32px; color: var(--text-muted);">
            No observations recorded yet. Select a cadet from your assigned squadron to add performance evaluations.
          </div>
        @endforelse
      </div>
    </div>
  </div>

</div>
@endsection
