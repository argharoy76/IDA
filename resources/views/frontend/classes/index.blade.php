@extends('layouts.public')

@section('title', 'Academy Classes & Training Timetable | Imperial Defence Academy')

@section('content')
<!-- Hero Header (Luminous Seafoam #85c9cc & Slate Aesthetic) -->
<section class="page-hero" style="position: relative; background: linear-gradient(135deg, #f0f9fa 0%, #e6f4f5 50%, #f8fafc 100%); border-bottom: 1px solid rgba(133, 201, 204, 0.35); padding: 75px 24px 65px; text-align: center; overflow: hidden;">
  <div style="position: absolute; inset: 0; background: radial-gradient(circle at 50% 20%, rgba(133, 201, 204, 0.25) 0%, transparent 70%); pointer-events: none;"></div>
  <div class="ida-container" style="position: relative; z-index: 2; max-width: 800px; margin: 0 auto; padding: 0 20px; text-align: center;">
    @if(filled(cms('classes_badge', 'Academic & Military Wings')))
    <span data-aos="fade-down" style="display: inline-flex; align-items: center; gap: 6px; background: rgba(133, 201, 204, 0.22); border: 1px solid #85c9cc; padding: 5px 16px; border-radius: 9999px; font-size: 11.5px; font-weight: 800; color: #082d2f; margin-bottom: 16px; letter-spacing: 1px; text-transform: uppercase; box-shadow: 0 2px 8px rgba(133, 201, 204, 0.25);">
      <i class="fa-solid fa-graduation-cap" style="color: #082d2f;"></i> {{ cms('classes_badge', 'Academic & Military Wings') }}
    </span>
    @endif

    @if(filled(cms('classes_title', 'Classes & Training Schedule')))
    <h1 data-aos="zoom-in" data-aos-delay="150" style="font-size: clamp(30px, 4.5vw, 42px); font-weight: 900; color: #082d2f; margin: 0 0 14px 0; font-family: 'Roboto', sans-serif; letter-spacing: -0.02em;">
      {{ cms('classes_title', 'Classes & Training Schedule') }}
    </h1>
    @endif

    @if(filled(cms('classes_subtitle', 'Discover our structured daily drill rosters, academic lecture schedules, physical obstacle drills, and psychological assessment sessions designed for armed forces aspirants.')))
    <p data-aos="fade-up" data-aos-delay="250" style="font-size: 16px; color: #475569; max-width: 680px; margin: 0 auto; line-height: 1.75; font-weight: 400;">
      {{ cms('classes_subtitle', 'Discover our structured daily drill rosters, academic lecture schedules, physical obstacle drills, and psychological assessment sessions designed for armed forces aspirants.') }}
    </p>
    @endif
  </div>
</section>

<!-- Weekly Training Blueprint -->
<section style="padding: 60px 0; background: #f8fafc;">
  <div class="ida-container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; margin-bottom: 48px;">
      <!-- Card 1: Morning Drill -->
      <div data-aos="fade-up" data-aos-delay="100" class="classical-card" style="background: #ffffff; border: 1px solid var(--border-soft); border-radius: 18px; padding: 28px; display: flex; flex-direction: column; gap: 14px; box-shadow: var(--shadow-card);">
        <div style="width: 52px; height: 52px; border-radius: 14px; background: #eef8f8; color: #082d2f; border: 1px solid #85c9cc; display: flex; align-items: center; justify-content: center; font-size: 22px;">
          <i class="fa-solid fa-person-running"></i>
        </div>
        <h3 style="font-size: 18px; font-weight: 800; color: #082d2f; margin: 0; font-family: 'Roboto', sans-serif;">{{ cms('class_card1_title', 'Morning Physical Drills (PT)') }}</h3>
        <p style="font-size: 13.5px; color: #64748b; margin: 0; line-height: 1.65;">
          {{ cms('class_card1_desc', '06:00 AM – 07:30 AM: Calisthenics, endurance running, push-up/chin-up drills, and tactical obstacle negotiation under retired armed forces physical instructors.') }}
        </p>
        <span style="font-size: 12.5px; font-weight: 700; color: #082d2f; margin-top: auto; display: flex; align-items: center; gap: 6px;">
          <i class="fa-regular fa-clock" style="color: #85c9cc;"></i> {{ cms('class_card1_schedule', 'Mon, Wed, Fri • Parade Ground') }}
        </span>
      </div>

      <!-- Card 2: Academic Theory -->
      <div data-aos="fade-up" data-aos-delay="200" class="classical-card" style="background: #ffffff; border: 1px solid var(--border-soft); border-radius: 18px; padding: 28px; display: flex; flex-direction: column; gap: 14px; box-shadow: var(--shadow-card);">
        <div style="width: 52px; height: 52px; border-radius: 14px; background: #eef8f8; color: #082d2f; border: 1px solid #85c9cc; display: flex; align-items: center; justify-content: center; font-size: 22px;">
          <i class="fa-solid fa-book-open"></i>
        </div>
        <h3 style="font-size: 18px; font-weight: 800; color: #082d2f; margin: 0; font-family: 'Roboto', sans-serif;">{{ cms('class_card2_title', 'Academic & IQ Lectures') }}</h3>
        <p style="font-size: 13.5px; color: #64748b; margin: 0; line-height: 1.65;">
          {{ cms('class_card2_desc', '10:00 AM – 01:00 PM: General Knowledge, Higher Mathematics, Physics, English Comprehension, and Verbal/Non-Verbal Intelligence test mastery.') }}
        </p>
        <span style="font-size: 12.5px; font-weight: 700; color: #082d2f; margin-top: auto; display: flex; align-items: center; gap: 6px;">
          <i class="fa-regular fa-clock" style="color: #85c9cc;"></i> {{ cms('class_card2_schedule', 'Daily • Lecture Wing Alpha & Bravo') }}
        </span>
      </div>

      <!-- Card 3: Psychology & GTO -->
      <div data-aos="fade-up" data-aos-delay="300" class="classical-card" style="background: #ffffff; border: 1px solid var(--border-soft); border-radius: 18px; padding: 28px; display: flex; flex-direction: column; gap: 14px; box-shadow: var(--shadow-card);">
        <div style="width: 52px; height: 52px; border-radius: 14px; background: #eef8f8; color: #082d2f; border: 1px solid #85c9cc; display: flex; align-items: center; justify-content: center; font-size: 22px;">
          <i class="fa-solid fa-brain"></i>
        </div>
        <h3 style="font-size: 18px; font-weight: 800; color: #082d2f; margin: 0; font-family: 'Roboto', sans-serif;">{{ cms('class_card3_title', 'Psychology & GTO Drills') }}</h3>
        <p style="font-size: 13.5px; color: #64748b; margin: 0; line-height: 1.65;">
          {{ cms('class_card3_desc', '03:00 PM – 05:30 PM: PGT, HGT, Command Tasks, Group Discussions, Extempore Speech, TAT story writing, and WAT drills.') }}
        </p>
        <span style="font-size: 12.5px; font-weight: 700; color: #082d2f; margin-top: auto; display: flex; align-items: center; gap: 6px;">
          <i class="fa-regular fa-clock" style="color: #85c9cc;"></i> {{ cms('class_card3_schedule', 'Tue, Thu, Sat • Tactical Ground') }}
        </span>
      </div>
    </div>

    <!-- Active Batches Table -->
    <div data-aos="fade-up" data-aos-delay="400" style="background: #ffffff; border: 1px solid var(--border-soft); border-radius: 20px; overflow: hidden; box-shadow: var(--shadow-card);">
      <div style="padding: 20px 24px; border-bottom: 1px solid var(--border-soft); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; background: #fcfdfe;">
        <div>
          <h3 style="font-size: 18px; font-weight: 800; color: #082d2f; margin: 0; font-family: 'Roboto', sans-serif;">Current Ongoing Classes & Batches</h3>
          <p style="font-size: 13px; color: #64748b; margin: 4px 0 0 0;">Review class schedules across Army, Navy, and Air Force preparation wings.</p>
        </div>
        <a href="{{ route('courses') }}" class="btn-primary" style="padding: 8px 18px; font-size: 12.5px; text-decoration: none;">
          View All Courses
        </a>
      </div>

      <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13.5px;">
          <thead>
            <tr style="background: #f1f8f8; color: #082d2f; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
              <th style="padding: 14px 20px; font-weight: 700;">Squadron / Batch</th>
              <th style="padding: 14px 20px; font-weight: 700;">Target Wing</th>
              <th style="padding: 14px 20px; font-weight: 700;">Timing Slot</th>
              <th style="padding: 14px 20px; font-weight: 700;">Class Days</th>
              <th style="padding: 14px 20px; font-weight: 700;">Admission Status</th>
            </tr>
          </thead>
          <tbody style="color: #334155;">
            @if(isset($batches) && $batches->isNotEmpty())
              @foreach($batches as $batch)
                <tr style="border-top: 1px solid var(--border-soft);">
                  <td style="padding: 16px 20px; font-weight: 700; color: #082d2f;">
                    {{ $batch->name }} <span style="font-size: 11px; color: #64748b; font-weight: normal;">({{ $batch->code }})</span>
                  </td>
                  <td style="padding: 16px 20px;">
                    <span style="background: #eef8f8; color: #082d2f; border: 1px solid #85c9cc; padding: 4px 10px; border-radius: 6px; font-weight: 700; font-size: 11px;">
                      {{ $batch->course->title ?? $batch->course->category ?? 'Defense Wing' }}
                    </span>
                  </td>
                  <td style="padding: 16px 20px;">{{ $batch->schedule_summary ?? $batch->time_slot ?? '09:00 AM – 01:00 PM' }}</td>
                  <td style="padding: 16px 20px;">{{ $batch->days_of_week ?? 'Saturday to Thursday' }}</td>
                  <td style="padding: 16px 20px;">
                    <span style="color: #082d2f; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                      <span style="width: 8px; height: 8px; border-radius: 50%; background: #85c9cc;"></span>
                      {{ ucfirst($batch->status) }} • Active
                    </span>
                  </td>
                </tr>
              @endforeach
            @else
              <tr style="border-top: 1px solid var(--border-soft);">
                <td style="padding: 16px 20px; font-weight: 700; color: #082d2f;">94 BMA Long Course Alpha</td>
                <td style="padding: 16px 20px;"><span style="background: #eef8f8; color: #082d2f; border: 1px solid #85c9cc; padding: 4px 10px; border-radius: 6px; font-weight: 700; font-size: 11px;">Bangladesh Army</span></td>
                <td style="padding: 16px 20px;">09:00 AM – 01:00 PM</td>
                <td style="padding: 16px 20px;">Saturday to Thursday</td>
                <td style="padding: 16px 20px;"><span style="color: #082d2f; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;"><span style="width: 8px; height: 8px; border-radius: 50%; background: #85c9cc;"></span> Ongoing • Open</span></td>
              </tr>
              <tr style="border-top: 1px solid var(--border-soft);">
                <td style="padding: 16px 20px; font-weight: 700; color: #082d2f;">2026-A Navy Officer Cadet Bravo</td>
                <td style="padding: 16px 20px;"><span style="background: #eef8f8; color: #082d2f; border: 1px solid #85c9cc; padding: 4px 10px; border-radius: 6px; font-weight: 700; font-size: 11px;">Bangladesh Navy</span></td>
                <td style="padding: 16px 20px;">02:00 PM – 06:00 PM</td>
                <td style="padding: 16px 20px;">Saturday to Thursday</td>
                <td style="padding: 16px 20px;"><span style="color: #082d2f; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;"><span style="width: 8px; height: 8px; border-radius: 50%; background: #85c9cc;"></span> Ongoing • Open</span></td>
              </tr>
              <tr style="border-top: 1px solid var(--border-soft);">
                <td style="padding: 16px 20px; font-weight: 700; color: #082d2f;">90 BAF GDP & Aircrew Charlie</td>
                <td style="padding: 16px 20px;"><span style="background: #eef8f8; color: #082d2f; border: 1px solid #85c9cc; padding: 4px 10px; border-radius: 6px; font-weight: 700; font-size: 11px;">Bangladesh Air Force</span></td>
                <td style="padding: 16px 20px;">09:00 AM – 01:00 PM</td>
                <td style="padding: 16px 20px;">Saturday to Thursday</td>
                <td style="padding: 16px 20px;"><span style="color: #b45309; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;"><span style="width: 8px; height: 8px; border-radius: 50%; background: #f59e0b;"></span> Limited Seats</span></td>
              </tr>
            @endif
          </tbody>
        </table>
      </div>
    </div>

  </div>
</section>
@endsection
