@extends('layouts.portal')

@section('title', 'Candidate Flight Deck')
@section('page_title', 'Candidate Online Assessment Portal')
@section('page_subtitle', 'Computerized ISSB Intelligence tests, WAT simulation, and official candidate scorecard tracking')

@section('topbar_actions')
  <a href="{{ route('external.tests.index') }}" class="btn-tactical btn-tactical-primary">
    <i class="fa-solid fa-crosshairs"></i> Browse Test Catalog
  </a>
@endsection

@section('content')
<style>
  @media (max-width: 768px) {
    .candidate-hero-banner {
      flex-direction: column !important;
      align-items: flex-start !important;
      padding: 20px 16px !important;
    }
    .candidate-hero-stats {
      width: 100% !important;
      justify-content: space-between !important;
    }
    .candidate-hero-stats > div {
      flex: 1 !important;
    }
  }
</style>

<div style="display: flex; flex-direction: column; gap: 24px;">

  <!-- Candidate Hero Banner -->
  <div class="tactical-card candidate-hero-banner" style="background: linear-gradient(135deg, rgba(15, 23, 42, 0.95), rgba(30, 58, 138, 0.35)); border: 1px solid var(--border-soft); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
    <div style="display: flex; align-items: center; gap: 18px;">
      <div style="width: 64px; height: 64px; border-radius: 50%; background: linear-gradient(135deg, #1e3a8a, #0f172a); border: 2px solid #60a5fa; color: #fff; display: grid; place-items: center; font-size: 24px; font-weight: 800; flex-shrink: 0;">
        {{ strtoupper(substr($user->name, 0, 1)) }}
      </div>
      <div>
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px; flex-wrap: wrap;">
          <span class="badge badge-navy" style="font-family: 'Plus Jakarta Sans', monospace; font-size: 13px;">
            {{ $student->student_id_code ?? 'EXT-' . date('Y') . '-' . sprintf('%03d', $user->id) }}
          </span>
          <span class="badge badge-emerald">REGISTERED CANDIDATE</span>
        </div>
        <h2 style="font-size: 22px; font-weight: 800; margin: 0; color: #fff;">{{ $user->name }}</h2>
        <p style="font-size: 13px; color: var(--text-muted); margin: 4px 0 0 0; word-break: break-word;">
          Candidate Email: <strong style="color: #fff;">{{ $user->email }}</strong> | External Candidate Testing Access
        </p>
      </div>
    </div>

    <!-- Quick Stats -->
    <div class="candidate-hero-stats" style="display: flex; gap: 14px;">
      <div style="text-align: center; padding: 10px 18px; background: rgba(0,0,0,0.3); border-radius: 8px;">
        <div style="font-size: 10px; text-transform: uppercase; color: var(--text-muted);">Tests Taken</div>
        <div style="font-size: 22px; font-weight: 800; font-family: 'Plus Jakarta Sans', monospace; color: #60a5fa;">
          {{ $myAttempts->count() }}
        </div>
      </div>

      <div style="text-align: center; padding: 10px 18px; background: rgba(0,0,0,0.3); border-radius: 8px;">
        <div style="font-size: 10px; text-transform: uppercase; color: var(--text-muted);">Passed</div>
        <div style="font-size: 22px; font-weight: 800; font-family: 'Plus Jakarta Sans', monospace; color: #10b981;">
          {{ $myAttempts->where('is_passed', true)->count() }}
        </div>
      </div>
    </div>
  </div>

  <!-- Available Online Assessments Grid -->
  <div>
    <h3 style="font-size: 16px; font-weight: 700; margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px; color: var(--accent-gold);">
      <i class="fa-solid fa-crosshairs"></i> Available Practice & Mock Assessments
    </h3>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
      @forelse($availableTests as $test)
        <div class="tactical-card" style="display: flex; flex-direction: column; justify-content: space-between;">
          <div>
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
              <span class="badge badge-emerald">PUBLIC TESTING</span>
              <span class="badge badge-navy">{{ strtoupper(str_replace('_', ' ', $test->exam_type)) }}</span>
            </div>

            <h3 style="font-size: 17px; font-weight: 800; margin: 0 0 8px 0; color: var(--brand-deep);">{{ $test->title }}</h3>
            <p style="font-size: 12.5px; color: var(--text-muted); line-height: 1.5; margin: 0 0 16px 0;">
              {{ $test->description ?? 'Official military screening test simulator.' }}
            </p>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; background: var(--surface-subtle); padding: 12px; border-radius: 8px; margin-bottom: 16px; border: 1px solid var(--border-soft);">
              <div style="text-align: center;">
                <div style="font-size: 10px; color: var(--text-muted); text-transform: uppercase;">Duration</div>
                <strong style="font-size: 13px; font-family: 'Plus Jakarta Sans', monospace; color: #60a5fa;">{{ $test->duration_minutes }} min</strong>
              </div>
              <div style="text-align: center; border-left: 1px solid var(--border-soft); border-right: 1px solid var(--border-soft);">
                <div style="font-size: 10px; color: var(--text-muted); text-transform: uppercase;">Total Marks</div>
                <strong style="font-size: 13px; font-family: 'Plus Jakarta Sans', monospace; color: var(--accent-gold);">{{ $test->total_marks }}</strong>
              </div>
              <div style="text-align: center;">
                <div style="font-size: 10px; color: var(--text-muted); text-transform: uppercase;">Fee</div>
                <strong style="font-size: 13px; font-family: 'Plus Jakarta Sans', monospace; color: #10b981;">
                  {{ $test->fee > 0 ? '৳' . number_format($test->fee, 0) : 'FREE' }}
                </strong>
              </div>
            </div>
          </div>

          <div style="display: flex; gap: 10px;">
            <a href="{{ route('external.tests.start', $test->id) }}" class="btn-tactical btn-tactical-primary" style="flex: 1; text-align: center; justify-content: center;">
              <i class="fa-solid fa-play"></i> Start Test Now
            </a>
          </div>
        </div>
      @empty
        <div style="grid-column: span 3; text-align: center; padding: 48px; color: var(--text-muted);" class="tactical-card">
          No public tests open at the moment.
        </div>
      @endforelse
    </div>
  </div>

  <!-- My Test Attempts Table -->
  <div class="tactical-card" style="padding: 0; overflow-x: auto; -webkit-overflow-scrolling: touch;">
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-soft);">
      <h3 style="font-size: 15px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-square-poll-vertical" style="color: var(--accent-gold);"></i> My Past Assessment Attempts & Scorecards
      </h3>
    </div>

    <table class="tactical-table">
      <thead>
        <tr>
          <th>Attempt ID</th>
          <th>Assessment Test</th>
          <th>Type</th>
          <th>Score</th>
          <th>Result</th>
          <th>Submitted At</th>
          <th>Scorecard</th>
        </tr>
      </thead>
      <tbody>
        @forelse($myAttempts as $at)
          <tr>
            <td><span style="font-family: 'Plus Jakarta Sans', monospace; font-weight: 700; color: #60a5fa;">ATT-{{ sprintf('%04d', $at->id) }}</span></td>
            <td><strong>{{ $at->exam->title ?? 'Exam' }}</strong></td>
            <td><span class="badge badge-navy" style="font-size: 10px;">{{ strtoupper(str_replace('_', ' ', $at->exam->exam_type ?? 'mcq')) }}</span></td>
            <td>
              <span style="font-family: 'Plus Jakarta Sans', monospace; font-weight: 800; color: {{ $at->is_passed ? '#10b981' : '#ef4444' }};">
                {{ number_format($at->score, 1) }} / {{ $at->total_marks }}
              </span>
            </td>
            <td>
              @if($at->status === 'in_progress')
                <span class="badge badge-gold">IN PROGRESS</span>
              @elseif($at->is_passed)
                <span class="badge badge-emerald"><i class="fa-solid fa-check"></i> QUALIFIED ({{ round($at->percentage) }}%)</span>
              @else
                <span class="badge badge-danger"><i class="fa-solid fa-xmark"></i> FAILED ({{ round($at->percentage) }}%)</span>
              @endif
            </td>
            <td><span style="font-size: 12px;">{{ $at->submitted_at ? \Carbon\Carbon::parse($at->submitted_at)->format('d M Y, h:i A') : $at->created_at->format('d M Y') }}</span></td>
            <td>
              <a href="{{ route('external.tests.result', $at->id) }}" class="btn-tactical btn-tactical-outline" style="padding: 4px 10px; font-size: 11px;">
                <i class="fa-solid fa-eye"></i> View Paper
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" style="text-align: center; padding: 36px; color: var(--text-muted);">
              You haven't completed any online assessments yet. Choose an assessment above to begin!
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

</div>
@endsection
