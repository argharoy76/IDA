@extends('layouts.portal')

@section('title', 'My Attendance')
@section('page_title', 'Cadet Muster & Attendance Record')
@section('page_subtitle', 'Military roll call tracking, parade attendance percentage, and session logs')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

  <!-- Attendance Module Notice -->
  <div class="tactical-card" style="text-align: center; padding: 48px 24px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 18px;">
    <div style="width: 64px; height: 64px; border-radius: 50%; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 28px; margin: 0 auto 16px auto;">
      <i class="fa-solid fa-clipboard-user"></i>
    </div>
    <h3 style="font-size: 18px; font-weight: 800; color: #0f172a; margin: 0 0 8px 0;">Attendance Section Currently Hidden</h3>
    <p style="font-size: 13.5px; color: #64748b; margin: 0 auto 20px auto; max-width: 480px; line-height: 1.5;">
      Muster roll call records and parade logs are currently hidden. Please proceed to the Exam portal.
    </p>
    <a href="{{ route('cadet.exams.index') }}" class="btn-tactical btn-tactical-primary" style="display: inline-flex; align-items: center; gap: 8px; text-decoration: none; padding: 10px 20px; border-radius: 10px;">
      <i class="fa-solid fa-clipboard-list"></i> Go to Exams
    </a>
  </div>

  {{-- Hidden attendance records and metrics - Code preserved intact for future activation --}}
  @if(false)
  <!-- Attendance Metrics Cards -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
    <!-- Percentage Card -->
    <div class="stat-card" style="border: 1px solid {{ $percentage < 75 ? 'rgba(239, 68, 68, 0.4)' : 'rgba(16, 185, 129, 0.4)' }};">
      <div class="stat-icon" style="background: {{ $percentage < 75 ? 'rgba(239, 68, 68, 0.15)' : 'rgba(16, 185, 129, 0.15)' }}; color: {{ $percentage < 75 ? '#ef4444' : '#10b981' }};">
        <i class="fa-solid fa-percent"></i>
      </div>
      <div>
        <div class="stat-label">Attendance Rate</div>
        <div class="stat-value" style="color: {{ $percentage < 75 ? '#ef4444' : '#10b981' }}; font-size: 28px;">
          {{ $percentage }}%
        </div>
        <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;">
          {{ $percentage >= 75 ? 'Meets ISSB eligibility criteria' : 'Attention: Below mandatory 75%' }}
        </div>
      </div>
    </div>

    <!-- Total Sessions -->
    <div class="stat-card">
      <div class="stat-icon" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa;">
        <i class="fa-solid fa-calendar-check"></i>
      </div>
      <div>
        <div class="stat-label">Total Roll Calls</div>
        <div class="stat-value">{{ $totalSessions }}</div>
        <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;">Recorded drills & lectures</div>
      </div>
    </div>

    <!-- Present Count -->
    <div class="stat-card">
      <div class="stat-icon" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
        <i class="fa-solid fa-check"></i>
      </div>
      <div>
        <div class="stat-label">Present / On Parade</div>
        <div class="stat-value" style="color: #10b981;">{{ $presentCount }}</div>
        <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;">Sessions attended</div>
      </div>
    </div>

    <!-- Absent Count -->
    <div class="stat-card">
      <div class="stat-icon" style="background: rgba(239, 68, 68, 0.15); color: #ef4444;">
        <i class="fa-solid fa-xmark"></i>
      </div>
      <div>
        <div class="stat-label">Absent / Unexcused</div>
        <div class="stat-value" style="color: #ef4444;">{{ $absentCount }}</div>
        <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;">Late: {{ $lateCount }}</div>
      </div>
    </div>
  </div>

  <!-- Attendance History Table -->
  <div class="tactical-card" style="padding: 0; overflow: hidden;">
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-soft); display: flex; justify-content: space-between; align-items: center;">
      <h3 style="font-size: 15px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-clock-rotate-left" style="color: var(--accent-gold);"></i> Attendance Log
      </h3>
      <span style="font-size: 12px; color: var(--text-muted);">Showing last {{ $attendances->count() }} records</span>
    </div>

    <div style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
      <table class="tactical-table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Subject / Drill Session</th>
            <th>Instructing Officer</th>
            <th>Muster Status</th>
            <th>Remarks</th>
          </tr>
        </thead>
        <tbody>
          @forelse($attendances as $att)
            <tr>
              <td>
                <span style="font-weight: 700; color: var(--text-main);">{{ \Carbon\Carbon::parse($att->date)->format('d M Y (D)') }}</span>
              </td>
              <td>
                <strong>{{ $att->routine->subject ?? 'Squadron Parade / Drill' }}</strong>
              </td>
              <td>
                <small style="color: var(--text-muted);">{{ $att->routine->instructor->user->name ?? 'Squadron Instructor' }}</small>
              </td>
              <td>
                @if($att->status === 'present')
                  <span class="badge badge-emerald"><i class="fa-solid fa-check"></i> PRESENT</span>
                @elseif($att->status === 'absent')
                  <span class="badge badge-danger"><i class="fa-solid fa-xmark"></i> ABSENT</span>
                @elseif($att->status === 'late')
                  <span class="badge badge-gold"><i class="fa-solid fa-clock"></i> LATE</span>
                @else
                  <span class="badge badge-navy"><i class="fa-solid fa-shield"></i> EXCUSED</span>
                @endif
              </td>
              <td>
                <span style="font-size: 12px; color: var(--text-muted);">{{ $att->remarks ?: '—' }}</span>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-muted);">
                No attendance records logged for your profile yet.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($attendances->hasPages())
      <div style="padding: 16px 20px; border-top: 1px solid var(--border-soft);">
        {{ $attendances->links() }}
      </div>
    @endif
  </div>
  @endif

</div>
@endsection
