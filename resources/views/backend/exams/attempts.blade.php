@extends('layouts.portal')

@section('title', 'Exam Attempts & Results')
@section('page_title', 'Cadet & Candidate Exam Attempts')
@section('page_subtitle', 'Review completed test submissions, scores, negative marking tallies, and WAT candidate responses')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

  <!-- Exam Filter Strip -->
  <div class="tactical-card" style="padding: 16px;">
    <form action="{{ route('admin.exams.attempts') }}" method="GET" style="display: flex; gap: 14px; align-items: flex-end; flex-wrap: wrap;">
      <div style="flex: 1; min-width: 260px;">
        <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px;">Filter by Assessment Module</label>
        <select name="exam_id" class="form-tactical" style="height: 40px;">
          <option value="">All Exams & Assessments</option>
          @foreach($exams as $ex)
            <option value="{{ $ex->id }}" {{ request('exam_id') == $ex->id ? 'selected' : '' }}>
              {{ $ex->title }} ({{ strtoupper(str_replace('_', ' ', $ex->exam_type)) }})
            </option>
          @endforeach
        </select>
      </div>

      <button type="submit" class="btn-tactical btn-tactical-outline" style="height: 40px;">
        <i class="fa-solid fa-filter"></i> Apply Filter
      </button>

      @if(request()->filled('exam_id'))
        <a href="{{ route('admin.exams.attempts') }}" class="btn-tactical" style="height: 40px; background: rgba(255,255,255,0.06); color: var(--text-muted);">
          <i class="fa-solid fa-xmark"></i> Clear
        </a>
      @endif
    </form>
  </div>

  <!-- Submissions Table -->
  <div class="tactical-card" style="padding: 0; overflow: hidden;">
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-soft); display: flex; justify-content: space-between; align-items: center;">
      <h3 style="font-size: 15px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-square-poll-vertical" style="color: var(--accent-gold);"></i> Submission Log
      </h3>
      <span style="font-size: 12px; color: var(--text-muted);">Total Attempts: {{ $attempts->total() }}</span>
    </div>

    <div style="overflow-x: auto;">
      <table class="tactical-table">
        <thead>
          <tr>
            <th>Attempt #</th>
            <th>Candidate / Cadet</th>
            <th>Assessment Exam</th>
            <th>Type</th>
            <th>Score</th>
            <th>Result</th>
            <th>Submitted At</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($attempts as $a)
            <tr>
              <td>
                <span style="font-family: 'Plus Jakarta Sans', monospace; font-weight: 700; color: #60a5fa;">ATT-{{ sprintf('%04d', $a->id) }}</span>
              </td>
              <td>
                @if($a->student)
                  <a href="{{ route('admin.students.show', $a->student->id) }}" style="font-weight: 700; color: var(--text-main); text-decoration: none;">
                    {{ $a->student->user->name ?? 'Cadet' }}
                  </a>
                  <div style="font-size: 11px; color: var(--accent-gold); font-family: 'Plus Jakarta Sans', monospace;">{{ $a->student->student_id_code }}</div>
                @else
                  <strong>{{ $a->user->name ?? 'External Candidate' }}</strong>
                  <div style="font-size: 11px; color: var(--text-muted);">External Applicant</div>
                @endif
              </td>
              <td>
                <div style="font-weight: 600; font-size: 13px;">{{ $a->exam->title ?? 'Exam' }}</div>
              </td>
              <td>
                <span class="badge badge-navy" style="font-size: 10px;">
                  {{ strtoupper(str_replace('_', ' ', $a->exam->exam_type ?? 'mcq')) }}
                </span>
              </td>
              <td>
                <div style="font-family: 'Plus Jakarta Sans', monospace; font-size: 14px; font-weight: 800; color: {{ $a->is_passed ? '#10b981' : '#ef4444' }};">
                  {{ number_format($a->score, 1) }} / {{ $a->total_marks }}
                </div>
                <div style="font-size: 10px; color: var(--text-muted);">
                  Correct: {{ $a->correct_answers }} | Wrong: {{ $a->wrong_answers }}
                </div>
              </td>
              <td>
                @if($a->status === 'in_progress')
                  <span class="badge badge-gold"><i class="fa-solid fa-clock"></i> IN PROGRESS</span>
                @elseif($a->is_passed)
                  <span class="badge badge-emerald"><i class="fa-solid fa-check"></i> PASSED ({{ round($a->percentage) }}%)</span>
                @else
                  <span class="badge badge-danger"><i class="fa-solid fa-xmark"></i> FAILED ({{ round($a->percentage) }}%)</span>
                @endif
              </td>
              <td>
                <div style="font-size: 12px;">{{ $a->submitted_at ? \Carbon\Carbon::parse($a->submitted_at)->format('d M Y, h:i A') : 'Ongoing' }}</div>
              </td>
              <td>
                <a href="{{ route('admin.exams.attempts.show', $a->id) }}" class="btn-tactical btn-tactical-outline" style="padding: 4px 10px; font-size: 11px;">
                  <i class="fa-solid fa-eye"></i> View Paper
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" style="text-align: center; padding: 48px; color: var(--text-muted);">
                No exam attempts logged yet.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($attempts->hasPages())
      <div style="padding: 16px 20px; border-top: 1px solid var(--border-soft);">
        {{ $attempts->links() }}
      </div>
    @endif
  </div>

</div>
@endsection
