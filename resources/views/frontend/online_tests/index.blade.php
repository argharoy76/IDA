@extends('layouts.public')

@section('title', 'Online Assessment Platform | Imperial Defence Academy')

@section('content')
<!-- Page Header (Luminous Seafoam #85c9cc & Slate Aesthetic) -->
<section style="position: relative; background: linear-gradient(135deg, #f0f9fa 0%, #e6f4f5 50%, #f8fafc 100%); border-bottom: 1px solid rgba(133, 201, 204, 0.35); padding: 75px 24px 65px; text-align: center; overflow: hidden;">
  <div style="position: absolute; inset: 0; background: radial-gradient(circle at 50% 20%, rgba(133, 201, 204, 0.25) 0%, transparent 70%); pointer-events: none;"></div>
  <div style="position: relative; z-index: 2; max-width: 800px; margin: 0 auto;">
    @if(filled(cms('online_tests_badge', 'DEFENCE ASSESSMENT GATEWAY')))
    <span data-aos="fade-down" style="display: inline-flex; align-items: center; gap: 6px; background: rgba(133, 201, 204, 0.22); border: 1px solid #85c9cc; padding: 5px 16px; border-radius: 9999px; font-size: 11.5px; font-weight: 800; color: #082d2f; margin-bottom: 16px; letter-spacing: 1px; text-transform: uppercase; box-shadow: 0 2px 8px rgba(133, 201, 204, 0.25);">
      {{ cms('online_tests_badge', 'DEFENCE ASSESSMENT GATEWAY') }}
    </span>
    @endif

    @if(filled(cms('online_tests_title', 'IDA Online Assessment Platform')))
    <h1 data-aos="zoom-in" data-aos-delay="150" style="font-size: clamp(30px, 4.5vw, 42px); font-weight: 900; color: #082d2f; font-family: 'Roboto', sans-serif; letter-spacing: -0.02em; margin-bottom: 14px;">
      {{ cms('online_tests_title', 'IDA Online Assessment Platform') }}
    </h1>
    @endif

    @if(filled(cms('online_tests_subtitle', 'A high-precision, server-timed examination engine open to enrolled academic cadets and external aspirants across Bangladesh.')))
    <p data-aos="fade-up" data-aos-delay="250" style="color: #475569; font-size: 16px; line-height: 1.75; margin: 0 auto; max-width: 700px; font-weight: 400;">
      {{ cms('online_tests_subtitle', 'A high-precision, server-timed examination engine open to enrolled academic cadets and external aspirants across Bangladesh.') }}
    </p>
    @endif
  </div>
</section>

<section style="max-width: 1240px; margin: 50px auto; padding: 0 24px;">

  <!-- Branch Switcher Navigation -->
  <div class="content-panel classical-card" data-aos="fade-up" style="margin-bottom: 32px; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
      <span style="font-size: 12px; font-weight: 800; text-transform: uppercase; color: var(--text-muted); margin-right: 6px;">Select Branch:</span>
      
      <a href="{{ route('online_tests') }}" class="btn-secondary {{ empty($activeBranch) ? 'active' : '' }}" style="text-decoration: none; padding: 8px 18px; font-size: 13px; font-weight: 700; border-radius: 9999px; {{ empty($activeBranch) ? 'background: #85c9cc; color: #082d2f !important; border-color: #72bcc0; box-shadow: 0 4px 12px rgba(133, 201, 204, 0.35);' : 'background: #ffffff; color: #334155; border-color: #cbd5e1; box-shadow: 0 2px 6px rgba(0,0,0,0.04);' }}">
        All Branches ({{ $branchCounts['all'] ?? count($exams) }})
      </a>

      <a href="{{ route('online_tests', ['branch' => 'army']) }}" class="btn-secondary {{ $activeBranch === 'army' ? 'active' : '' }}" style="text-decoration: none; padding: 8px 18px; font-size: 13px; font-weight: 700; border-radius: 9999px; {{ $activeBranch === 'army' ? 'background: #85c9cc; color: #082d2f !important; border-color: #72bcc0; box-shadow: 0 4px 12px rgba(133, 201, 204, 0.35);' : 'background: #ffffff; color: #334155; border-color: #cbd5e1; box-shadow: 0 2px 6px rgba(0,0,0,0.04);' }}">
        <i class="fa-solid fa-shield-halved"></i> Bangladesh Army ({{ $branchCounts['army'] ?? 0 }})
      </a>

      <a href="{{ route('online_tests', ['branch' => 'navy']) }}" class="btn-secondary {{ $activeBranch === 'navy' ? 'active' : '' }}" style="text-decoration: none; padding: 8px 18px; font-size: 13px; font-weight: 700; border-radius: 9999px; {{ $activeBranch === 'navy' ? 'background: #85c9cc; color: #082d2f !important; border-color: #72bcc0; box-shadow: 0 4px 12px rgba(133, 201, 204, 0.35);' : 'background: #ffffff; color: #334155; border-color: #cbd5e1; box-shadow: 0 2px 6px rgba(0,0,0,0.04);' }}">
        <i class="fa-solid fa-anchor"></i> Bangladesh Navy ({{ $branchCounts['navy'] ?? 0 }})
      </a>

      <a href="{{ route('online_tests', ['branch' => 'air_force']) }}" class="btn-secondary {{ $activeBranch === 'air_force' ? 'active' : '' }}" style="text-decoration: none; padding: 8px 18px; font-size: 13px; font-weight: 700; border-radius: 9999px; {{ $activeBranch === 'air_force' ? 'background: #85c9cc; color: #082d2f !important; border-color: #72bcc0; box-shadow: 0 4px 12px rgba(133, 201, 204, 0.35);' : 'background: #ffffff; color: #334155; border-color: #cbd5e1; box-shadow: 0 2px 6px rgba(0,0,0,0.04);' }}">
        <i class="fa-solid fa-jet-fighter"></i> Air Force ({{ $branchCounts['air_force'] ?? 0 }})
      </a>

      <a href="{{ route('online_tests', ['branch' => 'police']) }}" class="btn-secondary {{ $activeBranch === 'police' ? 'active' : '' }}" style="text-decoration: none; padding: 8px 18px; font-size: 13px; font-weight: 700; border-radius: 9999px; {{ $activeBranch === 'police' ? 'background: #85c9cc; color: #082d2f !important; border-color: #72bcc0; box-shadow: 0 4px 12px rgba(133, 201, 204, 0.35);' : 'background: #ffffff; color: #334155; border-color: #cbd5e1; box-shadow: 0 2px 6px rgba(0,0,0,0.04);' }}">
        <i class="fa-solid fa-user-shield"></i> Bangladesh Police ({{ $branchCounts['police'] ?? 0 }})
      </a>
    </div>
  </div>

  <!-- Four-Step Protocol -->
  @if(filled(cms('online_tests_step_heading', 'Four-Step Assessment Protocol for External Candidates')))
  <div class="content-panel classical-card" data-aos="fade-up" data-aos-delay="100" style="margin-bottom: 44px; padding: 26px;">
    <div style="text-align: center; margin-bottom: 22px;">
      <span style="font-size: 11px; font-weight: 800; color: #082d2f; text-transform: uppercase; letter-spacing: 0.8px; display: inline-block; background: rgba(133, 201, 204, 0.25); border: 1px solid #85c9cc; padding: 4px 12px; border-radius: 20px; margin-bottom: 6px;">How It Works</span>
      <h2 style="font-size: 22px; font-weight: 800; color: #082d2f; font-family: 'Roboto', sans-serif;">{{ cms('online_tests_step_heading', 'Four-Step Assessment Protocol for External Candidates') }}</h2>
    </div>
    <div class="grid-cols-4-responsive" style="text-align: center; gap: 16px;">
      <div style="background: var(--surface-subtle); padding: 18px 14px; border-radius: var(--radius-sm); border: 1px solid var(--border-soft);">
        <div style="width: 38px; height: 38px; border-radius: 50%; background: #85c9cc; color: #082d2f; display: grid; place-items: center; font-weight: 900; font-size: 15px; margin: 0 auto 10px; border: 1px solid #72bcc0; box-shadow: 0 2px 6px rgba(133, 201, 204, 0.4);">1</div>
        <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 4px; font-family: 'Roboto', sans-serif; color: #082d2f;">{{ cms('online_tests_step1_title', 'Free Registration') }}</h4>
        <small style="color: var(--text-muted); font-size: 11.5px; line-height: 1.4; display: block;">{{ cms('online_tests_step1_desc', 'Create your profile in 60 seconds') }}</small>
      </div>
      <div style="background: var(--surface-subtle); padding: 18px 14px; border-radius: var(--radius-sm); border: 1px solid var(--border-soft);">
        <div style="width: 38px; height: 38px; border-radius: 50%; background: #85c9cc; color: #082d2f; display: grid; place-items: center; font-weight: 900; font-size: 15px; margin: 0 auto 10px; border: 1px solid #72bcc0; box-shadow: 0 2px 6px rgba(133, 201, 204, 0.4);">2</div>
        <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 4px; font-family: 'Roboto', sans-serif; color: #082d2f;">{{ cms('online_tests_step2_title', 'Select Mock Test') }}</h4>
        <small style="color: var(--text-muted); font-size: 11.5px; line-height: 1.4; display: block;">{{ cms('online_tests_step2_desc', 'Choose Army, Navy, Air Force, or Police') }}</small>
      </div>
      <div style="background: var(--surface-subtle); padding: 18px 14px; border-radius: var(--radius-sm); border: 1px solid var(--border-soft);">
        <div style="width: 38px; height: 38px; border-radius: 50%; background: #85c9cc; color: #082d2f; display: grid; place-items: center; font-weight: 900; font-size: 15px; margin: 0 auto 10px; border: 1px solid #72bcc0; box-shadow: 0 2px 6px rgba(133, 201, 204, 0.4);">3</div>
        <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 4px; font-family: 'Roboto', sans-serif; color: #082d2f;">{{ cms('online_tests_step3_title', 'Wait for Start Timer') }}</h4>
        <small style="color: var(--text-muted); font-size: 11.5px; line-height: 1.4; display: block;">{{ cms('online_tests_step3_desc', 'Countdown unlocks test access at scheduled time') }}</small>
      </div>
      <div style="background: var(--surface-subtle); padding: 18px 14px; border-radius: var(--radius-sm); border: 1px solid var(--border-soft);">
        <div style="width: 38px; height: 38px; border-radius: 50%; background: #85c9cc; color: #082d2f; display: grid; place-items: center; font-weight: 900; font-size: 15px; margin: 0 auto 10px; border: 1px solid #72bcc0; box-shadow: 0 2px 6px rgba(133, 201, 204, 0.4);">4</div>
        <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 4px; font-family: 'Roboto', sans-serif; color: #082d2f;">{{ cms('online_tests_step4_title', 'Live Timed Test') }}</h4>
        <small style="color: var(--text-muted); font-size: 11.5px; line-height: 1.4; display: block;">{{ cms('online_tests_step4_desc', 'Instant scorecards & detailed solutions') }}</small>
      </div>
    </div>
  </div>
  @endif

  <!-- Available Test Cards Grid -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
    @forelse($exams as $exam)
      @php
        $isFuture = $exam->isScheduledFuture();
        $remSeconds = $exam->getSecondsUntilStart();
        $examStartUrl = auth()->check()
          ? (auth()->user()->isAcademicStudent() ? route('cadet.exams.start', $exam->id) : route('external.tests.start', $exam->id))
          : route('register', ['type' => 'external']);
      @endphp

      <div class="content-panel classical-card" data-aos="fade-up" data-aos-delay="{{ 100 + ($loop->index % 6) * 80 }}" style="display: flex; flex-direction: column; justify-content: space-between; height: 100%; margin-bottom: 0; padding: 24px; border-top: 4px solid {{ $exam->branchColor() }}; position: relative;">
        <div>
          <!-- Header Badges -->
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; gap: 8px; flex-wrap: wrap;">
            <div style="display: flex; gap: 6px; align-items: center; flex-wrap: wrap;">
              <span class="badge" style="background: {{ $exam->branchColor() }}; color: #ffffff; font-size: 11px;">
                <i class="fa-solid {{ $exam->branchIcon() }}"></i> {{ $exam->branchLabel() }}
              </span>
              <span class="badge badge-emerald" style="font-size: 10.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                <i class="fa-solid fa-unlock-keyhole"></i> Free Exam
              </span>
              <span class="badge badge-navy" style="font-size: 10.5px;">{{ $exam->category }}</span>
              <span class="badge" style="background: rgba(8, 45, 47, 0.05); color: #082d2f; border: 1px solid rgba(133, 201, 204, 0.3); font-size: 10.5px;">
                <i class="fa-solid fa-layer-group" style="color: #85c9cc;"></i> {{ $exam->trackLabel() }}
              </span>
            </div>

            <span id="badge-status-{{ $exam->id }}">
              @if($isFuture)
                <span class="badge badge-gold" style="font-size: 10.5px; display: inline-flex; align-items: center; gap: 4px;">
                  <i class="fa-regular fa-clock"></i> SCHEDULED
                </span>
              @else
                <span class="badge badge-emerald" style="font-size: 10.5px; display: inline-flex; align-items: center; gap: 5px;">
                  <span style="width: 7px; height: 7px; border-radius: 50%; background: #082d2f; display: inline-block; animation: pulse 1.5s infinite;"></span> LIVE NOW
                </span>
              @endif
            </span>
          </div>

          <h3 style="font-size: 17.5px; font-weight: 800; color: #082d2f; margin-bottom: 8px; font-family: 'Roboto', sans-serif;">
            {{ $exam->title }}
          </h3>

          <p style="font-size: 13px; color: var(--text-muted); line-height: 1.6; margin-bottom: 16px;">
            {{ Str::limit($exam->description, 130) }}
          </p>

          <!-- Scheduled Countdown Box if Future -->
          @if($isFuture)
            <div id="countdown-box-{{ $exam->id }}" class="exam-countdown-box" data-seconds="{{ $remSeconds }}" data-exam-id="{{ $exam->id }}" data-url="{{ $examStartUrl }}" style="background: #fffbeb; border: 1.5px solid #fde68a; border-radius: 8px; padding: 12px 14px; margin-bottom: 16px; text-align: center;">
              <div style="font-size: 11px; font-weight: 700; color: #b45309; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
                <i class="fa-solid fa-hourglass-half"></i> Examination Starts In:
              </div>
              <div class="timer-digits" style="font-family: 'Roboto', monospace; font-size: 20px; font-weight: 900; color: #92400e;">
                Calculating...
              </div>
              <small style="display: block; font-size: 11px; color: #78350f; margin-top: 2px;">
                Scheduled for: <strong>{{ $exam->schedule_start->format('h:i A, M d, Y') }}</strong>
              </small>
            </div>
          @endif

          <!-- Exam Metrics -->
          <div style="background: var(--surface-subtle); padding: 12px 14px; border-radius: var(--radius-sm); margin-bottom: 18px; font-size: 12px; border-left: 3px solid {{ $exam->branchColor() }};">
            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
              <span style="color: var(--text-muted);">Duration:</span>
              <strong>{{ $exam->duration_minutes }} Minutes</strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
              <span style="color: var(--text-muted);">Total Marks:</span>
              <strong>{{ $exam->total_marks }}</strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
              <span style="color: var(--text-muted);">Negative Marking:</span>
              <strong style="color: #dc2626;">-{{ $exam->negative_marking_per_wrong ?? 0 }} / wrong</strong>
            </div>
            <div style="display: flex; justify-content: space-between;">
              <span style="color: var(--text-muted);">Pass Mark:</span>
              <strong style="color: #082d2f;">{{ $exam->pass_marks }}</strong>
            </div>
          </div>
        </div>

        <!-- Footer Action & Pricing -->
        <div style="border-top: 1px solid var(--border-soft); padding-top: 16px; display: flex; justify-content: space-between; align-items: center; gap: 12px;">
          <div>
            <small style="display: block; font-size: 10px; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">TEST ACCESS</small>
            <strong style="font-size: 15px; color: #082d2f; font-family: 'Roboto', sans-serif; font-weight: 800; display: inline-flex; align-items: center; gap: 5px;">
              <i class="fa-solid fa-circle-check" style="color: #85c9cc;"></i> Free Exam
            </strong>
          </div>

          <div id="btn-container-{{ $exam->id }}">
            @if($isFuture)
              <button type="button" class="btn-primary" disabled style="opacity: 0.65; cursor: not-allowed; background: #64748b; border: 1px solid #475569; padding: 8px 16px; font-size: 12.5px;" title="Locked until scheduled start time">
                <i class="fa-solid fa-lock"></i> Locked until {{ $exam->schedule_start->format('h:i A') }}
              </button>
            @else
              @auth
                @if(auth()->user()->isAcademicStudent())
                  <a href="{{ route('cadet.exams.start', $exam->id) }}" class="btn-primary" style="padding: 8px 16px; font-size: 12.5px;">
                    Start Exam <i class="fa-solid fa-play"></i>
                  </a>
                @else
                  <a href="{{ route('external.tests.start', $exam->id) }}" class="btn-primary" style="padding: 8px 16px; font-size: 12.5px;">
                    Take Test <i class="fa-solid fa-play"></i>
                  </a>
                @endif
              @else
                <a href="{{ route('register', ['type' => 'external']) }}" class="btn-gold" style="padding: 8px 16px; font-size: 12.5px;">
                  Register to Take <i class="fa-solid fa-arrow-right"></i>
                </a>
              @endauth
            @endif
          </div>
        </div>
      </div>
    @empty
      <div style="grid-column: span 3; text-align: center; padding: 50px 20px;" class="content-panel classical-card">
        <i class="fa-solid fa-clipboard-list" style="font-size: 40px; color: var(--text-muted); opacity: 0.4; margin-bottom: 12px; display: block;"></i>
        <h4 style="font-size: 16px; margin-bottom: 6px; color: #082d2f;">No Active Tests in This Branch</h4>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px;">Check back shortly or select another branch category above.</p>
        <a href="{{ route('online_tests') }}" class="btn-primary" style="padding: 8px 16px; font-size: 12.5px;">
          View All Branches
        </a>
      </div>
    @endforelse
  </div>
</section>

<!-- Client-Side Real-Time Countdown & Auto-Unlock Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  const countdownBoxes = document.querySelectorAll('.exam-countdown-box');

  countdownBoxes.forEach(function (box) {
    let secondsLeft = parseInt(box.getAttribute('data-seconds'), 10);
    const examId = box.getAttribute('data-exam-id');
    const examUrl = box.getAttribute('data-url');
    const timerDisplay = box.querySelector('.timer-digits');

    function updateTimer() {
      if (secondsLeft <= 0) {
        // Time has arrived! Automatically unlock the exam
        timerDisplay.innerHTML = '<span style="color: #082d2f; font-weight: 800;"><i class="fa-solid fa-circle-check" style="color: #85c9cc;"></i> STARTING NOW!</span>';
        
        // Update badge
        const badgeElem = document.getElementById('badge-status-' + examId);
        if (badgeElem) {
          badgeElem.innerHTML = '<span class="badge badge-emerald" style="font-size: 10.5px; display: inline-flex; align-items: center; gap: 5px;"><span style="width: 7px; height: 7px; border-radius: 50%; background: #082d2f; display: inline-block;"></span> LIVE NOW</span>';
        }

        // Swap button
        const btnContainer = document.getElementById('btn-container-' + examId);
        if (btnContainer) {
          btnContainer.innerHTML = '<a href="' + examUrl + '" class="btn-primary" style="padding: 8px 18px; font-size: 12.5px; background: #85c9cc; color: #082d2f; border-color: #72bcc0; font-weight: 800; animation: pulse 2s infinite;">Start Exam Now <i class="fa-solid fa-play"></i></a>';
        }

        return; // stop decrementing
      }

      const hours = Math.floor(secondsLeft / 3600);
      const minutes = Math.floor((secondsLeft % 3600) / 60);
      const seconds = Math.floor(secondsLeft % 60);

      const pad = (n) => (n < 10 ? '0' + n : n);
      timerDisplay.textContent = pad(hours) + 'h : ' + pad(minutes) + 'm : ' + pad(seconds) + 's';

      secondsLeft--;
      setTimeout(updateTimer, 1000);
    }

    updateTimer();
  });
});
</script>

<style>
@keyframes pulse {
  0% { transform: scale(0.97); opacity: 0.85; }
  50% { transform: scale(1.03); opacity: 1; }
  100% { transform: scale(0.97); opacity: 0.85; }
}
</style>
@endsection
