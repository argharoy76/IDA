@extends('layouts.public')

@section('title', 'Academy Classes & Training Timetable | Imperial Defence Academy')

@section('content')
<!-- Hero Header -->
<section class="page-hero" style="background: linear-gradient(135deg, #050b14 0%, #0d1b2a 100%); padding: 70px 0 50px 0; border-bottom: 1px solid rgba(255,255,255,0.08);">
  <div class="ida-container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px; text-align: center;">
    <span style="display: inline-block; padding: 4px 14px; border-radius: 9999px; background: rgba(255, 87, 87, 0.12); color: #ff5757; font-weight: 800; font-size: 11px; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 12px; border: 1px solid rgba(255, 87, 87, 0.25);">
      <i class="fa-solid fa-graduation-cap"></i> {{ cms('classes_badge', 'Academic & Military Wings') }}
    </span>
    <h1 style="font-size: 38px; font-weight: 900; color: #ffffff; margin: 0 0 14px 0; letter-spacing: -0.5px;">
      {{ cms('classes_title', 'Classes & Training Schedule') }}
    </h1>
    <p style="font-size: 16px; color: #94a3b8; max-width: 680px; margin: 0 auto; line-height: 1.6;">
      {{ cms('classes_subtitle', 'Discover our structured daily drill rosters, academic lecture schedules, physical obstacle drills, and psychological assessment sessions designed for armed forces aspirants.') }}
    </p>
  </div>
</section>

<!-- Weekly Training Blueprint -->
<section style="padding: 60px 0; background: #070d17;">
  <div class="ida-container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; margin-bottom: 48px;">
      <!-- Card 1: Morning Drill -->
      <div style="background: #0b1320; border: 1px solid rgba(255,255,255,0.07); border-radius: 18px; padding: 28px; display: flex; flex-direction: column; gap: 14px;">
        <div style="width: 52px; height: 52px; border-radius: 14px; background: rgba(59, 130, 246, 0.15); color: #60a5fa; display: flex; align-items: center; justify-content: center; font-size: 22px;">
          <i class="fa-solid fa-person-running"></i>
        </div>
        <h3 style="font-size: 18px; font-weight: 800; color: #ffffff; margin: 0;">{{ cms('class_card1_title', 'Morning Physical Drills (PT)') }}</h3>
        <p style="font-size: 13.5px; color: #8c96a8; margin: 0; line-height: 1.6;">
          {{ cms('class_card1_desc', '06:00 AM – 07:30 AM: Calisthenics, endurance running, push-up/chin-up drills, and tactical obstacle negotiation under retired armed forces physical instructors.') }}
        </p>
        <span style="font-size: 12px; font-weight: 700; color: #60a5fa; margin-top: auto;">{{ cms('class_card1_schedule', 'Mon, Wed, Fri • Parade Ground') }}</span>
      </div>

      <!-- Card 2: Academic Theory -->
      <div style="background: #0b1320; border: 1px solid rgba(255,255,255,0.07); border-radius: 18px; padding: 28px; display: flex; flex-direction: column; gap: 14px;">
        <div style="width: 52px; height: 52px; border-radius: 14px; background: rgba(16, 185, 129, 0.15); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 22px;">
          <i class="fa-solid fa-book-open"></i>
        </div>
        <h3 style="font-size: 18px; font-weight: 800; color: #ffffff; margin: 0;">{{ cms('class_card2_title', 'Academic & IQ Lectures') }}</h3>
        <p style="font-size: 13.5px; color: #8c96a8; margin: 0; line-height: 1.6;">
          {{ cms('class_card2_desc', '10:00 AM – 01:00 PM: General Knowledge, Higher Mathematics, Physics, English Comprehension, and Verbal/Non-Verbal Intelligence test mastery.') }}
        </p>
        <span style="font-size: 12px; font-weight: 700; color: #10b981; margin-top: auto;">{{ cms('class_card2_schedule', 'Daily • Lecture Wing Alpha & Bravo') }}</span>
      </div>

      <!-- Card 3: Psychology & GTO -->
      <div style="background: #0b1320; border: 1px solid rgba(255,255,255,0.07); border-radius: 18px; padding: 28px; display: flex; flex-direction: column; gap: 14px;">
        <div style="width: 52px; height: 52px; border-radius: 14px; background: rgba(245, 158, 11, 0.15); color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 22px;">
          <i class="fa-solid fa-brain"></i>
        </div>
        <h3 style="font-size: 18px; font-weight: 800; color: #ffffff; margin: 0;">{{ cms('class_card3_title', 'Psychology & GTO Drills') }}</h3>
        <p style="font-size: 13.5px; color: #8c96a8; margin: 0; line-height: 1.6;">
          {{ cms('class_card3_desc', '03:00 PM – 05:30 PM: PGT, HGT, Command Tasks, Group Discussions, Extempore Speech, TAT story writing, and WAT drills.') }}
        </p>
        <span style="font-size: 12px; font-weight: 700; color: #f59e0b; margin-top: auto;">{{ cms('class_card3_schedule', 'Tue, Thu, Sat • Tactical Ground') }}</span>
      </div>
    </div>

    <!-- Active Batches Table -->
    <div style="background: #0b1320; border: 1px solid rgba(255,255,255,0.08); border-radius: 20px; overflow: hidden;">
      <div style="padding: 20px 24px; border-bottom: 1px solid rgba(255,255,255,0.07); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
        <div>
          <h3 style="font-size: 17px; font-weight: 800; color: #ffffff; margin: 0;">Current Ongoing Classes & Batches</h3>
          <p style="font-size: 12.5px; color: #8c96a8; margin: 4px 0 0 0;">Review class schedules across Army, Navy, and Air Force preparation wings.</p>
        </div>
        <a href="{{ route('courses') }}" class="btn-tactical btn-tactical-primary" style="padding: 8px 18px; font-size: 12px; text-decoration: none;">
          View All Courses
        </a>
      </div>

      <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13.5px;">
          <thead>
            <tr style="background: rgba(255,255,255,0.02); color: #64748b; font-size: 11.5px; text-transform: uppercase; letter-spacing: 0.5px;">
              <th style="padding: 14px 20px; font-weight: 700;">Squadron / Batch</th>
              <th style="padding: 14px 20px; font-weight: 700;">Target Wing</th>
              <th style="padding: 14px 20px; font-weight: 700;">Timing Slot</th>
              <th style="padding: 14px 20px; font-weight: 700;">Class Days</th>
              <th style="padding: 14px 20px; font-weight: 700;">Admission Status</th>
            </tr>
          </thead>
          <tbody style="color: #cbd5e1;">
            @if(isset($batches) && $batches->isNotEmpty())
              @foreach($batches as $batch)
                <tr style="border-top: 1px solid rgba(255,255,255,0.05);">
                  <td style="padding: 16px 20px; font-weight: 700; color: #ffffff;">
                    {{ $batch->name }} <span style="font-size: 11px; color: #94a3b8; font-weight: normal;">({{ $batch->code }})</span>
                  </td>
                  <td style="padding: 16px 20px;">
                    <span style="background: rgba(16, 185, 129, 0.15); color: #10b981; padding: 3px 8px; border-radius: 6px; font-weight: 700; font-size: 11px;">
                      {{ $batch->course->title ?? $batch->course->category ?? 'Defense Wing' }}
                    </span>
                  </td>
                  <td style="padding: 16px 20px;">{{ $batch->schedule_summary ?? $batch->time_slot ?? '09:00 AM – 01:00 PM' }}</td>
                  <td style="padding: 16px 20px;">{{ $batch->days_of_week ?? 'Saturday to Thursday' }}</td>
                  <td style="padding: 16px 20px;">
                    <span style="color: #10b981; font-weight: 700;">{{ ucfirst($batch->status) }} • Active</span>
                  </td>
                </tr>
              @endforeach
            @else
              <tr style="border-top: 1px solid rgba(255,255,255,0.05);">
                <td style="padding: 16px 20px; font-weight: 700; color: #ffffff;">94 BMA Long Course Alpha</td>
                <td style="padding: 16px 20px;"><span style="background: rgba(16, 185, 129, 0.15); color: #10b981; padding: 3px 8px; border-radius: 6px; font-weight: 700; font-size: 11px;">Bangladesh Army</span></td>
                <td style="padding: 16px 20px;">09:00 AM – 01:00 PM</td>
                <td style="padding: 16px 20px;">Saturday to Thursday</td>
                <td style="padding: 16px 20px;"><span style="color: #10b981; font-weight: 700;">Ongoing • Open</span></td>
              </tr>
              <tr style="border-top: 1px solid rgba(255,255,255,0.05);">
                <td style="padding: 16px 20px; font-weight: 700; color: #ffffff;">2026-A Navy Officer Cadet Bravo</td>
                <td style="padding: 16px 20px;"><span style="background: rgba(59, 130, 246, 0.15); color: #60a5fa; padding: 3px 8px; border-radius: 6px; font-weight: 700; font-size: 11px;">Bangladesh Navy</span></td>
                <td style="padding: 16px 20px;">02:00 PM – 06:00 PM</td>
                <td style="padding: 16px 20px;">Saturday to Thursday</td>
                <td style="padding: 16px 20px;"><span style="color: #10b981; font-weight: 700;">Ongoing • Open</span></td>
              </tr>
              <tr style="border-top: 1px solid rgba(255,255,255,0.05);">
                <td style="padding: 16px 20px; font-weight: 700; color: #ffffff;">90 BAF GDP & Aircrew Charlie</td>
                <td style="padding: 16px 20px;"><span style="background: rgba(168, 85, 247, 0.15); color: #c084fc; padding: 3px 8px; border-radius: 6px; font-weight: 700; font-size: 11px;">Bangladesh Air Force</span></td>
                <td style="padding: 16px 20px;">09:00 AM – 01:00 PM</td>
                <td style="padding: 16px 20px;">Saturday to Thursday</td>
                <td style="padding: 16px 20px;"><span style="color: #f59e0b; font-weight: 700;">Limited Seats</span></td>
              </tr>
            @endif
          </tbody>
        </table>
      </div>
    </div>

  </div>
</section>
@endsection
