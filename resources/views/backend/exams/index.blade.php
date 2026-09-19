@extends('layouts.portal')

@section('title', 'Online Examination Command Engine')
@section('page_title', 'Online Examination Engine')
@section('page_subtitle', '4-Branch Question Bank, Bulk MCQ PDF Importer & Timed Exam Control')

@section('topbar_actions')
  <div style="display: flex; gap: 8px; flex-wrap: wrap;">
    <a href="{{ route('admin.exams.branch', 'army') }}" class="btn-tactical btn-tactical-primary">
      <i class="fa-solid fa-shield-halved"></i> Army Portal
    </a>
    <a href="{{ route('admin.exams.create') }}" class="btn-tactical btn-tactical-outline">
      <i class="fa-solid fa-plus-circle"></i> Create Custom Exam
    </a>
  </div>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

  <!-- 4-Branch Command Cards -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
    <!-- Army -->
    <a href="{{ route('admin.exams.branch', 'army') }}" class="tactical-card" style="text-decoration: none; border-left: 4px solid #059669; transition: transform 0.15s ease, box-shadow 0.15s ease;">
      <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
        <span style="width: 40px; height: 40px; border-radius: 8px; background: rgba(5, 150, 105, 0.12); color: #059669; display: grid; place-items: center; font-size: 18px;">
          <i class="fa-solid fa-shield-halved"></i>
        </span>
        <span class="badge badge-emerald" style="font-size: 10px;">{{ $stats['army_count'] }} EXAMS</span>
      </div>
      <h3 style="font-size: 16px; font-weight: 800; color: var(--brand-deep); margin: 0 0 4px 0;">Bangladesh Army</h3>
      <p style="font-size: 12px; color: var(--text-muted); margin: 0 0 12px 0;">BMA Long Course, Verbal & Non-Verbal IQ, WAT</p>
      <div style="display: flex; justify-content: space-between; font-size: 11.5px; border-top: 1px solid var(--border-soft); padding-top: 8px; color: #059669; font-weight: 700;">
        <span>Open Army Command</span>
        <i class="fa-solid fa-arrow-right"></i>
      </div>
    </a>

    <!-- Navy -->
    <a href="{{ route('admin.exams.branch', 'navy') }}" class="tactical-card" style="text-decoration: none; border-left: 4px solid #1e3a8a; transition: transform 0.15s ease, box-shadow 0.15s ease;">
      <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
        <span style="width: 40px; height: 40px; border-radius: 8px; background: rgba(30, 58, 138, 0.12); color: #1e3a8a; display: grid; place-items: center; font-size: 18px;">
          <i class="fa-solid fa-anchor"></i>
        </span>
        <span class="badge badge-navy" style="font-size: 10px;">{{ $stats['navy_count'] }} EXAMS</span>
      </div>
      <h3 style="font-size: 16px; font-weight: 800; color: var(--brand-deep); margin: 0 0 4px 0;">Bangladesh Navy</h3>
      <p style="font-size: 12px; color: var(--text-muted); margin: 0 0 12px 0;">Officer Cadet, Maritime Intelligence, Naval Science</p>
      <div style="display: flex; justify-content: space-between; font-size: 11.5px; border-top: 1px solid var(--border-soft); padding-top: 8px; color: #1e3a8a; font-weight: 700;">
        <span>Open Navy Command</span>
        <i class="fa-solid fa-arrow-right"></i>
      </div>
    </a>

    <!-- Air Force -->
    <a href="{{ route('admin.exams.branch', 'air_force') }}" class="tactical-card" style="text-decoration: none; border-left: 4px solid #0284c7; transition: transform 0.15s ease, box-shadow 0.15s ease;">
      <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
        <span style="width: 40px; height: 40px; border-radius: 8px; background: rgba(2, 132, 199, 0.12); color: #0284c7; display: grid; place-items: center; font-size: 18px;">
          <i class="fa-solid fa-jet-fighter"></i>
        </span>
        <span class="badge badge-navy" style="font-size: 10px; background: rgba(2, 132, 199, 0.15); color: #0284c7;">{{ $stats['air_force_count'] }} EXAMS</span>
      </div>
      <h3 style="font-size: 16px; font-weight: 800; color: var(--brand-deep); margin: 0 0 4px 0;">Bangladesh Air Force</h3>
      <p style="font-size: 12px; color: var(--text-muted); margin: 0 0 12px 0;">BAFA Flight Cadets, Aviation Aptitude, Spatial</p>
      <div style="display: flex; justify-content: space-between; font-size: 11.5px; border-top: 1px solid var(--border-soft); padding-top: 8px; color: #0284c7; font-weight: 700;">
        <span>Open Air Force Command</span>
        <i class="fa-solid fa-arrow-right"></i>
      </div>
    </a>

    <!-- Police -->
    <a href="{{ route('admin.exams.branch', 'police') }}" class="tactical-card" style="text-decoration: none; border-left: 4px solid #d97706; transition: transform 0.15s ease, box-shadow 0.15s ease;">
      <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
        <span style="width: 40px; height: 40px; border-radius: 8px; background: rgba(217, 119, 6, 0.12); color: #d97706; display: grid; place-items: center; font-size: 18px;">
          <i class="fa-solid fa-user-shield"></i>
        </span>
        <span class="badge badge-gold" style="font-size: 10px;">{{ $stats['police_count'] }} EXAMS</span>
      </div>
      <h3 style="font-size: 16px; font-weight: 800; color: var(--brand-deep); margin: 0 0 4px 0;">Bangladesh Police</h3>
      <p style="font-size: 12px; color: var(--text-muted); margin: 0 0 12px 0;">SI / ASP Screening, Legal Basics, Analytical</p>
      <div style="display: flex; justify-content: space-between; font-size: 11.5px; border-top: 1px solid var(--border-soft); padding-top: 8px; color: #d97706; font-weight: 700;">
        <span>Open Police Command</span>
        <i class="fa-solid fa-arrow-right"></i>
      </div>
    </a>
  </div>

  <!-- Filter & Quick Action Row -->
  <div class="tactical-card" style="padding: 12px 18px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
      <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: var(--text-muted); margin-right: 4px;">Filter List:</span>
      <a href="{{ route('admin.exams.index') }}" class="badge {{ !request('branch') ? 'badge-emerald' : 'badge-navy' }}" style="text-decoration: none; padding: 5px 12px;">All ({{ $stats['total_exams'] }})</a>
      <a href="{{ route('admin.exams.index', ['branch' => 'army']) }}" class="badge {{ request('branch') === 'army' ? 'badge-emerald' : 'badge-navy' }}" style="text-decoration: none; padding: 5px 12px;">Army ({{ $stats['army_count'] }})</a>
      <a href="{{ route('admin.exams.index', ['branch' => 'navy']) }}" class="badge {{ request('branch') === 'navy' ? 'badge-emerald' : 'badge-navy' }}" style="text-decoration: none; padding: 5px 12px;">Navy ({{ $stats['navy_count'] }})</a>
      <a href="{{ route('admin.exams.index', ['branch' => 'air_force']) }}" class="badge {{ request('branch') === 'air_force' ? 'badge-emerald' : 'badge-navy' }}" style="text-decoration: none; padding: 5px 12px;">Air Force ({{ $stats['air_force_count'] }})</a>
      <a href="{{ route('admin.exams.index', ['branch' => 'police']) }}" class="badge {{ request('branch') === 'police' ? 'badge-emerald' : 'badge-navy' }}" style="text-decoration: none; padding: 5px 12px;">Police ({{ $stats['police_count'] }})</a>
    </div>

    <div>
      <span class="badge badge-gold" style="font-size: 11px;">
        <i class="fa-solid fa-boxes-stacked"></i> Master Pool: <strong>{{ $stats['total_pool_questions'] }}</strong> Questions
      </span>
    </div>
  </div>

  <!-- Exams Grid -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
    @forelse($exams as $exam)
      <div class="tactical-card" id="exam-card-{{ $exam->id }}" style="display: flex; flex-direction: column; justify-content: space-between; transition: all 0.25s ease;">
        <div>
          <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
            <div style="display: flex; gap: 6px; align-items: center;">
              @if($exam->status === 'open')
                <span class="badge badge-emerald"><i class="fa-solid fa-circle" style="font-size: 8px;"></i> LIVE NOW</span>
              @elseif($exam->status === 'scheduled')
                <span class="badge badge-gold"><i class="fa-regular fa-clock"></i> SCHEDULED</span>
              @else
                <span class="badge badge-navy">{{ strtoupper($exam->status) }}</span>
              @endif

              @if($exam->branch)
                <span class="badge badge-navy" style="font-size: 10px;">{{ strtoupper($exam->branch) }}</span>
              @endif
            </div>

            <!-- Public Toggle Form -->
            <form action="{{ route('admin.exams.toggle_public', $exam->id) }}" method="POST" style="display: inline;">
              @csrf
              <button type="submit" class="badge {{ $exam->is_public_for_external ? 'badge-emerald' : 'badge-navy' }}" style="cursor: pointer; border: none; font-size: 10.5px; padding: 3px 8px;" title="Click to toggle public frontend access">
                @if($exam->is_public_for_external)
                  <i class="fa-solid fa-eye"></i> Public
                @else
                  <i class="fa-solid fa-eye-slash"></i> Hidden
                @endif
              </button>
            </form>
          </div>

          <h3 style="font-size: 16px; font-weight: 800; margin: 0 0 6px 0; color: var(--brand-deep);">
            {{ $exam->title }}
          </h3>
          <p style="font-size: 12.5px; color: var(--text-muted); line-height: 1.5; margin: 0 0 14px 0;">
            {{ Str::limit($exam->description, 95) }}
          </p>

          <!-- Scheduled Start / Live Timer Indicator -->
          @if($exam->schedule_start)
            <div style="background: #f8fafc; border: 1px solid var(--border-soft); border-radius: 6px; padding: 8px 12px; margin-bottom: 14px; font-size: 11.5px; display: flex; justify-content: space-between; align-items: center;">
              <span style="color: var(--text-muted);"><i class="fa-regular fa-calendar-check"></i> Starts:</span>
              <strong style="color: {{ $exam->isScheduledFuture() ? '#f59e0b' : '#059669' }};">
                {{ $exam->schedule_start->format('M d, h:i A') }}
              </strong>
            </div>
          @endif

          <!-- Key Metrics Grid -->
          <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; background: var(--surface-subtle); padding: 10px; border-radius: 8px; margin-bottom: 14px; border: 1px solid var(--border-soft);">
            <div style="text-align: center;">
              <div style="font-size: 10px; color: var(--text-muted); text-transform: uppercase;">Duration</div>
              <strong style="font-size: 13px; font-family: monospace; color: #3b82f6;">{{ $exam->duration_minutes }}m</strong>
            </div>
            <div style="text-align: center; border-left: 1px solid var(--border-soft); border-right: 1px solid var(--border-soft);">
              <div style="font-size: 10px; color: var(--text-muted); text-transform: uppercase;">Marks</div>
              <strong style="font-size: 13px; font-family: monospace; color: var(--accent-gold);">{{ $exam->total_marks }}</strong>
            </div>
            <div style="text-align: center;">
              <div style="font-size: 10px; color: var(--text-muted); text-transform: uppercase;">Pass Mark</div>
              <strong style="font-size: 13px; font-family: monospace; color: #10b981;">{{ $exam->pass_marks }}</strong>
            </div>
          </div>

          <!-- Items Count / Stats -->
          <div style="display: flex; gap: 12px; font-size: 11.5px; color: var(--text-muted); margin-bottom: 14px;">
            @if($exam->exam_type === 'word_association')
              <div><i class="fa-solid fa-bolt" style="color: #f59e0b;"></i> <strong>{{ $exam->wat_words_count }}</strong> Flash Words</div>
            @else
              <div><i class="fa-solid fa-list-check" style="color: #10b981;"></i> <strong>{{ $exam->questions_count }}</strong> Questions</div>
            @endif
            <div><i class="fa-solid fa-users" style="color: #60a5fa;"></i> <strong>{{ $exam->attempts_count }}</strong> Attempts</div>
          </div>
        </div>

        <!-- Action Links -->
        <div style="display: flex; gap: 6px; border-top: 1px solid var(--border-soft); padding-top: 12px; flex-wrap: wrap; align-items: center;">
          @if($exam->status !== 'open')
            <form action="{{ route('admin.exams.start_now', $exam->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Immediately start exam now for all candidates?');">
              @csrf
              <button type="submit" class="btn-tactical btn-tactical-primary" style="padding: 6px 10px; font-size: 11px;">
                <i class="fa-solid fa-play"></i> Start Now
              </button>
            </form>
          @endif

          @if($exam->exam_type === 'word_association')
            <a href="{{ route('admin.exams.wat_words', $exam->id) }}" class="btn-tactical btn-tactical-outline" style="padding: 6px 10px; font-size: 11.5px; flex: 1; text-align: center;">
              <i class="fa-solid fa-bolt"></i> WAT Words ({{ $exam->wat_words_count }})
            </a>
          @else
            <a href="{{ route('admin.exams.questions', $exam->id) }}" class="btn-tactical btn-tactical-outline" style="padding: 6px 10px; font-size: 11.5px; flex: 1; text-align: center;">
              <i class="fa-solid fa-list-check"></i> Questions ({{ $exam->questions_count }})
            </a>
          @endif

          <a href="{{ route('admin.exams.edit', $exam->id) }}" class="btn-tactical btn-tactical-outline" style="padding: 6px 8px; font-size: 11.5px;" title="Settings">
            <i class="fa-solid fa-gear"></i>
          </a>

          <button type="button" class="btn-tactical btn-tactical-outline" 
            onclick="deleteExamIndexAjax({{ $exam->id }}, '{{ addslashes($exam->title) }}', '{{ route('admin.exams.destroy', $exam->id) }}', this)"
            style="padding: 6px 8px; font-size: 11.5px; color: #ef4444; border-color: rgba(239, 68, 68, 0.3);" title="Delete Exam Paper">
            <i class="fa-solid fa-trash"></i>
          </button>
        </div>
      </div>
    @empty
      <div style="grid-column: span 3; text-align: center; padding: 60px 20px;" class="tactical-card">
        <i class="fa-solid fa-file-pen" style="font-size: 40px; color: var(--text-muted); opacity: 0.4; margin-bottom: 14px; display: block;"></i>
        <h4 style="font-size: 16px; margin-bottom: 6px;">No Assessment Modules Configured</h4>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 20px;">Use the 4-branch portals above to upload MCQ PDFs and generate exam papers.</p>
        <a href="{{ route('admin.exams.branch', 'army') }}" class="btn-tactical btn-tactical-primary">
          <i class="fa-solid fa-shield-halved"></i> Open Army Portal
        </a>
      </div>
    @endforelse
  </div>

</div>

<script>
function deleteExamIndexAjax(examId, examTitle, deleteUrl, btn) {
  if (!confirm(`Are you sure you want to permanently delete the exam "${examTitle}"? This cannot be undone.`)) {
    return;
  }

  const card = document.getElementById('exam-card-' + examId);
  const originalHtml = btn.innerHTML;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
  btn.disabled = true;

  if (card) {
    card.style.opacity = '0.35';
    card.style.pointerEvents = 'none';
  }

  fetch(deleteUrl, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ _method: 'DELETE' })
  })
  .then(res => {
    if (!res.ok) throw new Error('HTTP ' + res.status);
    return res.json();
  })
  .then(data => {
    if (data.success) {
      if (card) {
        card.style.transition = 'all 0.35s cubic-bezier(0.16, 1, 0.3, 1)';
        card.style.transform = 'scale(0.9)';
        card.style.opacity = '0';
        setTimeout(() => card.remove(), 350);
      }
      if (window.showToast) {
        window.showToast(data.message || 'Exam deleted successfully.', 'success');
      }
    } else {
      btn.innerHTML = originalHtml;
      btn.disabled = false;
      if (card) {
        card.style.opacity = '1';
        card.style.pointerEvents = 'auto';
      }
      alert(data.message || 'Failed to delete exam.');
    }
  })
  .catch(err => {
    btn.innerHTML = originalHtml;
    btn.disabled = false;
    if (card) {
      card.style.opacity = '1';
      card.style.pointerEvents = 'auto';
    }
    alert('An error occurred while deleting the exam.');
  });
}
</script>
@endsection
