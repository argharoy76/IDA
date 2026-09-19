@extends('layouts.portal')

@section('title', 'Classes & Routines - Web Management')
@section('page_title', 'Classes & Routines Editor')
@section('page_subtitle', 'Manage classes timetable headers, daily training drill blueprint cards, and review active squad batches')

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px;">

  @include('backend.cms.partials.nav')

  @if(session('success'))
    <div class="alert alert-success" style="background: rgba(16, 185, 129, 0.15); border: 1px solid var(--brand-mint); color: #065f46; padding: 12px 16px; border-radius: var(--radius-sm); font-size: 13px; font-weight: 600;">
      <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
  @endif

  <form action="{{ route('admin.cms.settings') }}" method="POST">
    @csrf

    <!-- Page Header Settings -->
    <div class="tactical-card" style="margin-bottom: 24px;">
      <div style="border-bottom: 1px solid var(--border-soft); padding-bottom: 14px; margin-bottom: 20px;">
        <h3 style="font-size: 17px; font-weight: 800; color: var(--brand-deep); margin: 0 0 6px 0;">
          <i class="fa-regular fa-calendar-days" style="color: var(--brand-emerald);"></i> Classes & Routine Page Header
        </h3>
        <p style="font-size: 12.5px; color: var(--text-muted); margin: 0;">
          Customize the header badge, main title, and introductory text displayed on <code>/classes</code>.
        </p>
      </div>

      <div style="display: flex; flex-direction: column; gap: 16px;">
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 16px;">
          <div>
            <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Eyebrow Badge</label>
            <input type="text" name="classes_badge" value="{{ cms('classes_badge', 'Academic & Military Wings') }}" class="form-tactical">
          </div>
          <div>
            <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Page Heading</label>
            <input type="text" name="classes_title" value="{{ cms('classes_title', 'Classes & Training Schedule') }}" class="form-tactical">
          </div>
        </div>

        <div>
          <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Page Subtitle</label>
          <textarea name="classes_subtitle" rows="2" class="form-tactical">{{ cms('classes_subtitle', 'Discover our structured daily drill rosters, academic lecture schedules, physical obstacle drills, and psychological assessment sessions designed for armed forces aspirants.') }}</textarea>
        </div>
      </div>
    </div>

    <!-- Daily Drill Blueprint (3 Cards) -->
    <div class="tactical-card" style="margin-bottom: 24px;">
      <div style="border-bottom: 1px solid var(--border-soft); padding-bottom: 14px; margin-bottom: 20px;">
        <h3 style="font-size: 17px; font-weight: 800; color: var(--brand-deep); margin: 0 0 6px 0;">
          <i class="fa-solid fa-list-check" style="color: var(--accent-gold);"></i> Daily Training Blueprint (3 Roster Blocks)
        </h3>
        <p style="font-size: 12.5px; color: var(--text-muted); margin: 0;">
          Edit the titles, timings, locations, and descriptions for the three highlighted routine pillars.
        </p>
      </div>

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 18px;">
        <!-- Blueprint Card 1: Morning PT -->
        <div style="background: var(--surface-subtle); border: 1px solid var(--border-soft); border-radius: var(--radius-sm); padding: 18px; display: flex; flex-direction: column; gap: 12px;">
          <div style="font-size: 12px; font-weight: 800; color: #3b82f6; text-transform: uppercase; display: flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-person-running"></i> Roster Block 1 (Physical Drills)
          </div>
          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; margin-bottom: 4px;">Card Title</label>
            <input type="text" name="class_card1_title" value="{{ cms('class_card1_title', 'Morning Physical Drills (PT)') }}" class="form-tactical">
          </div>
          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; margin-bottom: 4px;">Description & Modules</label>
            <textarea name="class_card1_desc" rows="3" class="form-tactical">{{ cms('class_card1_desc', '06:00 AM – 07:30 AM: Calisthenics, endurance running, push-up/chin-up drills, and tactical obstacle negotiation under retired armed forces physical instructors.') }}</textarea>
          </div>
          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; margin-bottom: 4px;">Schedule & Location Badge</label>
            <input type="text" name="class_card1_schedule" value="{{ cms('class_card1_schedule', 'Mon, Wed, Fri • Parade Ground') }}" class="form-tactical">
          </div>
        </div>

        <!-- Blueprint Card 2: Academic Lectures -->
        <div style="background: var(--surface-subtle); border: 1px solid var(--border-soft); border-radius: var(--radius-sm); padding: 18px; display: flex; flex-direction: column; gap: 12px;">
          <div style="font-size: 12px; font-weight: 800; color: #10b981; text-transform: uppercase; display: flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-book-open"></i> Roster Block 2 (Academic & IQ)
          </div>
          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; margin-bottom: 4px;">Card Title</label>
            <input type="text" name="class_card2_title" value="{{ cms('class_card2_title', 'Academic & IQ Lectures') }}" class="form-tactical">
          </div>
          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; margin-bottom: 4px;">Description & Modules</label>
            <textarea name="class_card2_desc" rows="3" class="form-tactical">{{ cms('class_card2_desc', '10:00 AM – 01:00 PM: General Knowledge, Higher Mathematics, Physics, English Comprehension, and Verbal/Non-Verbal Intelligence test mastery.') }}</textarea>
          </div>
          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; margin-bottom: 4px;">Schedule & Location Badge</label>
            <input type="text" name="class_card2_schedule" value="{{ cms('class_card2_schedule', 'Daily • Lecture Wing Alpha & Bravo') }}" class="form-tactical">
          </div>
        </div>

        <!-- Blueprint Card 3: Psychology & GTO -->
        <div style="background: var(--surface-subtle); border: 1px solid var(--border-soft); border-radius: var(--radius-sm); padding: 18px; display: flex; flex-direction: column; gap: 12px;">
          <div style="font-size: 12px; font-weight: 800; color: #f59e0b; text-transform: uppercase; display: flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-brain"></i> Roster Block 3 (Psychology & GTO)
          </div>
          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; margin-bottom: 4px;">Card Title</label>
            <input type="text" name="class_card3_title" value="{{ cms('class_card3_title', 'Psychology & GTO Drills') }}" class="form-tactical">
          </div>
          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; margin-bottom: 4px;">Description & Modules</label>
            <textarea name="class_card3_desc" rows="3" class="form-tactical">{{ cms('class_card3_desc', '03:00 PM – 05:30 PM: PGT, HGT, Command Tasks, Group Discussions, Extempore Speech, TAT story writing, and WAT drills.') }}</textarea>
          </div>
          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; margin-bottom: 4px;">Schedule & Location Badge</label>
            <input type="text" name="class_card3_schedule" value="{{ cms('class_card3_schedule', 'Tue, Thu, Sat • Tactical Ground') }}" class="form-tactical">
          </div>
        </div>
      </div>
    </div>

    <!-- Active Batches Preview -->
    <div class="tactical-card" style="margin-bottom: 24px;">
      <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-soft); padding-bottom: 14px; margin-bottom: 16px; flex-wrap: wrap; gap: 10px;">
        <div>
          <h3 style="font-size: 17px; font-weight: 800; color: var(--brand-deep); margin: 0 0 4px 0;">
            <i class="fa-solid fa-layer-group" style="color: var(--brand-emerald);"></i> Active Squad Batches Sync
          </h3>
          <p style="font-size: 12.5px; color: var(--text-muted); margin: 0;">
            The squad table on <code>/classes</code> automatically syncs from your central Batches module.
          </p>
        </div>
        <a href="{{ route('admin.batches.index') }}" class="btn-tactical btn-tactical-outline" style="font-size: 11.5px; padding: 6px 14px;">
          <i class="fa-solid fa-arrow-up-right-from-square"></i> Manage Batches
        </a>
      </div>

      <div style="overflow-x: auto;">
        <table class="table-tactical" style="width: 100%;">
          <thead>
            <tr>
              <th>Squadron / Batch Name</th>
              <th>Course / Wing</th>
              <th>Primary Instructor</th>
              <th>Timing Slot</th>
              <th>Days</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($batches as $batch)
              <tr>
                <td style="font-weight: 700; color: var(--brand-deep);">{{ $batch->name }} ({{ $batch->code }})</td>
                <td><span class="badge badge-primary">{{ $batch->course->title ?? 'General' }}</span></td>
                <td>{{ $batch->instructor->user->name ?? 'Staff Assessor' }}</td>
                <td>{{ $batch->time_slot ?? '09:00 AM – 01:00 PM' }}</td>
                <td>{{ $batch->days_of_week ?? 'Sat – Thu' }}</td>
                <td>
                  <span class="badge badge-success" style="text-transform: capitalize;">{{ $batch->status }}</span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 18px;">
                  No active batches recorded in database. The public page will show the academy default squad roster.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- Submit Button -->
    <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 10px;">
      <a href="{{ route('classes') }}" target="_blank" class="btn-tactical btn-tactical-outline" style="text-decoration: none;">
        <i class="fa-solid fa-arrow-up-right-from-square"></i> Preview Live Classes Page
      </a>
      <button type="submit" class="btn-tactical btn-tactical-primary" style="padding: 10px 24px; font-weight: 700;">
        <i class="fa-solid fa-floppy-disk"></i> Save Classes & Routines Settings
      </button>
    </div>
  </form>
</div>
@endsection
