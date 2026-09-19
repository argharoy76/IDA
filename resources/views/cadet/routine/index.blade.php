@extends('layouts.portal')

@section('title', 'My Class Routine')
@section('page_title', 'Cadet Academic & Drill Routine')
@section('page_subtitle', 'Weekly schedule of physical training, obstacle runs, classroom lectures, and psychological drills')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

  <!-- Routine Module Notice -->
  <div class="tactical-card" style="text-align: center; padding: 48px 24px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 18px;">
    <div style="width: 64px; height: 64px; border-radius: 50%; background: #eff6ff; color: #3b82f6; display: flex; align-items: center; justify-content: center; font-size: 28px; margin: 0 auto 16px auto;">
      <i class="fa-regular fa-calendar-days"></i>
    </div>
    <h3 style="font-size: 18px; font-weight: 800; color: #0f172a; margin: 0 0 8px 0;">Routine Section Currently Hidden</h3>
    <p style="font-size: 13.5px; color: #64748b; margin: 0 auto 20px auto; max-width: 480px; line-height: 1.5;">
      Class timetables and daily drill schedule rosters are currently hidden. Please proceed to the Exam portal.
    </p>
    <a href="{{ route('cadet.exams.index') }}" class="btn-tactical btn-tactical-primary" style="display: inline-flex; align-items: center; gap: 8px; text-decoration: none; padding: 10px 20px; border-radius: 10px;">
      <i class="fa-solid fa-clipboard-list"></i> Go to Exams
    </a>
  </div>

  {{-- Hidden routine tables and schedules - Code preserved intact for future activation --}}
  @if(false)
  <!-- Squadron Info Strip -->
  <div class="tactical-card cadet-emerald-hero" style="background: linear-gradient(135deg, #064e3b 0%, #047857 100%) !important; color: #ffffff; border: 1px solid #059669; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
    <div>
      <span class="badge" style="background: #fffbeb; color: #b45309; border: 1px solid #fde68a; font-weight: 700; margin-bottom: 6px;">SCHEDULE CALENDAR</span>
      <h3 style="font-size: 18px; font-weight: 800; margin: 0; color: #ffffff !important;">
        {{ $student->currentBatch->name ?? 'Squadron' }} Class & Drill Roster
      </h3>
      <p style="font-size: 13px; color: #d1fae5; margin: 4px 0 0 0;">
        Course: <strong style="color: #ffffff;">{{ $student->currentCourse->name ?? 'ISSB Course' }}</strong> | Lead Assessor: {{ $student->currentBatch->primaryInstructor->user->name ?? 'Staff' }}
      </p>
    </div>

    <button type="button" onclick="window.print()" class="btn-tactical" style="background: rgba(255,255,255,0.18); color: #ffffff; border: 1px solid rgba(255,255,255,0.35);">
      <i class="fa-solid fa-print"></i> Print Weekly Routine
    </button>
  </div>

  <!-- Today's Routine Schedule -->
  <div class="tactical-card" style="padding: 0; overflow-x: auto; -webkit-overflow-scrolling: touch;">
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-soft); display: flex; align-items: center; justify-content: space-between;">
      <h3 style="font-size: 15px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-sun" style="color: #f59e0b;"></i> Today's Drills ({{ date('l, d M Y') }})
      </h3>
      <span class="badge badge-emerald">{{ $todayClasses->count() }} Sessions Scheduled</span>
    </div>

    <table class="tactical-table">
      <thead>
        <tr>
          <th>Time Slot</th>
          <th>Subject / Military Drill</th>
          <th>Officer / Instructor</th>
          <th>Location / Ground</th>
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
            <td>
              <strong style="color: var(--text-main); font-size: 13.5px;">{{ $c->subject }}</strong>
              @if($c->topic) <div style="font-size: 11px; color: var(--text-muted);">{{ $c->topic }}</div> @endif
            </td>
            <td>{{ $c->instructor->user->name ?? 'Instructor Wing' }}</td>
            <td><span class="badge badge-navy">{{ $c->room_no ?? 'Tactical Drill Ground' }}</span></td>
            <td>
              <span class="badge {{ $c->status === 'completed' ? 'badge-emerald' : 'badge-gold' }}">{{ strtoupper($c->status) }}</span>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" style="text-align: center; padding: 32px; color: var(--text-muted);">
              No classes scheduled for today.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Full Weekly Calendar View -->
  <div class="tactical-card" style="padding: 0; overflow-x: auto; -webkit-overflow-scrolling: touch;">
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-soft);">
      <h3 style="font-size: 15px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px;">
        <i class="fa-regular fa-calendar-days" style="color: var(--accent-gold);"></i> Full Current Week Schedule
      </h3>
    </div>

    <table class="tactical-table">
      <thead>
        <tr>
          <th>Date & Day</th>
          <th>Time</th>
          <th>Subject / Session</th>
          <th>Instructor</th>
          <th>Ground / Hall</th>
        </tr>
      </thead>
      <tbody>
        @forelse($weekRoutines as $w)
          <tr>
            <td>
              <span style="font-weight: 700; color: var(--text-main);">{{ \Carbon\Carbon::parse($w->class_date)->format('D, d M Y') }}</span>
            </td>
            <td>
              <span style="font-family: 'Plus Jakarta Sans', monospace; color: #60a5fa;">
                {{ \Carbon\Carbon::parse($w->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($w->end_time)->format('h:i A') }}
              </span>
            </td>
            <td><strong>{{ $w->subject }}</strong></td>
            <td>{{ $w->instructor->user->name ?? 'Faculty Assessor' }}</td>
            <td>{{ $w->room_no ?? 'Drill Ground' }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-muted);">
              No routine sessions posted for this week.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @endif

</div>
@endsection
