@extends('layouts.exam')

@section('title', 'Test Session: ' . $exam->title)

@section('content')
<style>
  .mcq-exam-grid {
    display: grid;
    grid-template-columns: 1fr 310px;
    gap: 24px;
    align-items: flex-start;
  }
  @media (max-width: 900px) {
    .mcq-exam-grid {
      grid-template-columns: 1fr !important;
      gap: 20px !important;
    }
    .question-pane {
      padding: 20px 16px !important;
    }
    .mcq-nav-buttons {
      display: flex !important;
      flex-wrap: wrap !important;
      gap: 8px !important;
      width: 100% !important;
    }
    .mcq-nav-buttons button {
      flex: 1 1 auto !important;
      justify-content: center !important;
    }
    .mcq-palette-card {
      position: static !important;
      top: auto !important;
      margin-top: 16px !important;
    }
  }
</style>

<form id="examForm" action="{{ route('external.tests.submit', $exam->id) }}" method="POST">
  @csrf

  <div class="mcq-exam-grid">

    <!-- Left Main Question Container -->
    <div>
      <!-- Protocol & Status Strip -->
      <div style="background: #ffffff; border: 1px solid #e2e8f0; border-left: 4px solid var(--brand-emerald); border-radius: 12px; padding: 12px 18px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.03); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div style="font-size: 13px; color: #334155; display: flex; align-items: center; gap: 10px;">
          <span style="display: inline-flex; align-items: center; gap: 6px; font-weight: 700; color: #059669; font-size: 12px;">
            <i class="fa-solid fa-circle-check"></i> Assessment in Progress
          </span>
          @if($exam->negative_marking_per_wrong > 0)
            <span style="color: #dc2626; font-weight: 700; font-size: 12px;">
              <i class="fa-solid fa-circle-exclamation"></i> -{{ $exam->negative_marking_per_wrong }} per incorrect answer
            </span>
          @endif
        </div>
        <button type="button" class="btn-exam-submit" onclick="confirmFinishExam()" style="padding: 8px 16px; font-size: 12px;">
          <i class="fa-solid fa-flag-checkered"></i> Finish & Submit
        </button>
      </div>

      <div id="questionsContainer">
        @foreach($exam->questions as $idx => $q)
          @php
            $rawOpts = is_array($q->options) ? $q->options : (json_decode($q->options, true) ?? []);
            $opts = [];
            foreach ($rawOpts as $k => $v) {
                if (is_array($v) && isset($v['key'])) {
                    $opts[] = $v;
                } else {
                    $opts[] = ['key' => is_string($k) ? $k : chr(65 + (int)$k), 'text' => $v];
                }
            }
          @endphp
          <div class="question-pane exam-card" id="q-pane-{{ $idx }}" style="{{ $idx === 0 ? '' : 'display: none;' }} padding: 30px;">
            
            <!-- Question Meta Header -->
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 14px; margin-bottom: 22px;">
              <div style="display: flex; align-items: center; gap: 10px;">
                <span class="badge" style="background: #0f172a; color: #fff; font-size: 13px; font-weight: 700; padding: 5px 12px; border-radius: 6px;">
                  Question {{ $idx + 1 }} of {{ $exam->questions->count() }}
                </span>
                <span class="badge" style="background: {{ $q->difficulty === 'hard' ? '#fef2f2' : ($q->difficulty === 'medium' ? '#fffbeb' : '#ecfdf5') }}; color: {{ $q->difficulty === 'hard' ? '#b91c1c' : ($q->difficulty === 'medium' ? '#b45309' : '#047857') }}; border: 1px solid {{ $q->difficulty === 'hard' ? '#fecaca' : ($q->difficulty === 'medium' ? '#fde68a' : '#a7f3d0') }}; font-size: 11.5px; font-weight: 700; padding: 4px 10px; border-radius: 6px;">
                  {{ strtoupper($q->difficulty ?? 'medium') }}
                </span>
              </div>
              <div style="font-size: 13px; color: #64748b;">
                Marks: <strong style="color: #059669;">+{{ $q->marks }}</strong>
                @if($q->negative_marks > 0)
                  | <span style="color: #dc2626; font-weight: 700;">-{{ $q->negative_marks }}</span>
                @endif
              </div>
            </div>

            <!-- Question Text (Bengali & MathJax supported) -->
            <div style="font-size: 17px; font-weight: 600; color: #0f172a; line-height: 1.65; margin-bottom: 26px;">
              {!! App\Services\McqPdfParserService::renderStemContent($q->question_text) !!}
            </div>

            <!-- Options Grid / List -->
            <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 30px;">
              @foreach($opts as $opt)
                <label id="opt-label-{{ $q->id }}-{{ $opt['key'] }}" style="display: flex; align-items: center; gap: 14px; padding: 14px 18px; border-radius: 10px; border: 1.5px solid #e2e8f0; background: #ffffff; cursor: pointer; transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);" onmouseover="if(!this.querySelector('input').checked) { this.style.borderColor='var(--brand-emerald)'; this.style.background='#f0fdf4'; }" onmouseout="if(!this.querySelector('input').checked) { this.style.borderColor='#e2e8f0'; this.style.background='#ffffff'; }">
                  <input type="radio" name="answers[{{ $q->id }}]" value="{{ $opt['key'] }}" onchange="markAnswered({{ $idx }}, '{{ $q->id }}', '{{ $opt['key'] }}')" style="width: 18px; height: 18px; accent-color: #059669;">
                  <span style="font-weight: 800; color: #d97706; font-size: 14.5px; width: 24px; text-align: center;">{{ $opt['key'] }}</span>
                  <span style="font-size: 15px; color: #1e293b; font-weight: 500; line-height: 1.4;">{!! App\Services\McqPdfParserService::renderStemContent($opt['text'] ?? '') !!}</span>
                </label>
              @endforeach
            </div>

            <!-- Navigation Controls -->
            <div class="mcq-nav-buttons" style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #e2e8f0; padding-top: 20px; flex-wrap: wrap; gap: 10px;">
              <button type="button" class="btn-exam-submit" style="background: #f1f5f9; color: #334155 !important; border: 1px solid #cbd5e1; box-shadow: none;" onclick="prevQuestion({{ $idx }})" {{ $idx === 0 ? 'disabled style=opacity:0.4;cursor:not-allowed;' : '' }}>
                <i class="fa-solid fa-arrow-left"></i> Previous
              </button>

              <button type="button" style="background: none; border: 1px solid #cbd5e1; color: #64748b; padding: 8px 16px; border-radius: 8px; font-size: 12.5px; font-weight: 600; cursor: pointer;" onclick="clearChoice({{ $q->id }}, {{ $idx }})">
                <i class="fa-solid fa-eraser"></i> Clear Choice
              </button>

              @if($idx < $exam->questions->count() - 1)
                <button type="button" class="btn-exam-submit" onclick="nextQuestion({{ $idx }})">
                  Next Question <i class="fa-solid fa-arrow-right"></i>
                </button>
              @else
                <button type="button" class="btn-exam-submit" onclick="confirmFinishExam()">
                  <i class="fa-solid fa-check"></i> Submit Test
                </button>
              @endif
            </div>

          </div>
        @endforeach
      </div>
    </div>

    <!-- Right Question Navigation Palette -->
    <div class="exam-card mcq-palette-card" style="position: sticky; top: 88px; padding: 22px;">
      <div style="border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
        <h4 style="font-size: 13px; font-weight: 800; text-transform: uppercase; margin: 0; color: #0f172a; letter-spacing: 0.5px;">
          <i class="fa-solid fa-table-cells" style="color: var(--brand-emerald);"></i> Question Palette
        </h4>
        <span id="answeredCounterBadge" style="font-size: 11px; background: #ecfdf5; color: #047857; padding: 2px 8px; border-radius: 12px; font-weight: 700;">
          0 / {{ $exam->questions->count() }}
        </span>
      </div>

      <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 8px; margin-bottom: 22px; max-height: 380px; overflow-y: auto; padding-right: 2px;">
        @foreach($exam->questions as $idx => $q)
          <button type="button" id="palette-btn-{{ $idx }}" onclick="showQuestion({{ $idx }})" style="height: 38px; border-radius: 8px; border: 1.5px solid #cbd5e1; background: #ffffff; color: #1e293b; font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 13px; cursor: pointer; transition: all 0.2s;">
            {{ $idx + 1 }}
          </button>
        @endforeach
      </div>

      <!-- Palette Legend -->
      <div style="font-size: 11px; color: #64748b; margin-bottom: 18px; border-top: 1px solid #e2e8f0; padding-top: 12px; display: flex; flex-direction: column; gap: 6px;">
        <div style="display: flex; align-items: center; gap: 8px;">
          <span style="width: 12px; height: 12px; border-radius: 3px; background: #059669; display: inline-block;"></span>
          <span>Answered</span>
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
          <span style="width: 12px; height: 12px; border-radius: 3px; background: #ffffff; border: 1.5px solid #cbd5e1; display: inline-block;"></span>
          <span>Unanswered</span>
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
          <span style="width: 12px; height: 12px; border-radius: 3px; border: 2px solid #d97706; display: inline-block;"></span>
          <span>Current Question</span>
        </div>
      </div>

      <button type="button" class="btn-exam-submit" style="width: 100%; justify-content: center; padding: 12px;" onclick="confirmFinishExam()">
        <i class="fa-solid fa-flag-checkered"></i> Submit All Answers
      </button>
    </div>

  </div>
</form>
@endsection

@section('scripts')
<script>
  let currentQ = 0;
  const totalQ = {{ $exam->questions->count() }};
  let durationSeconds = Math.max(0, Math.floor(Number({{ (int) floor($remainingSeconds) }})));
  const timerElement = document.getElementById('countdownTimer');
  const timerCard = document.getElementById('examTimerCard');
  const timerIcon = document.getElementById('timerIcon');

  function formatTime(totalSec) {
    totalSec = Math.max(0, Math.floor(totalSec));
    const h = Math.floor(totalSec / 3600);
    const m = Math.floor((totalSec % 3600) / 60);
    const s = Math.floor(totalSec % 60);
    const pad = (n) => (n < 10 ? '0' : '') + n;
    if (h > 0) {
      return pad(h) + ':' + pad(m) + ':' + pad(s);
    }
    return pad(m) + ':' + pad(s);
  }

  if (timerElement) {
    timerElement.innerText = formatTime(durationSeconds);
  }

  const timerInterval = setInterval(function() {
    durationSeconds--;
    if (durationSeconds <= 0) {
      clearInterval(timerInterval);
      if (timerElement) timerElement.innerText = '00:00';
      alert('Time has expired! Your test is being submitted automatically.');
      document.getElementById('examForm').submit();
      return;
    }

    if (timerElement) {
      timerElement.innerText = formatTime(durationSeconds);
    }

    if (durationSeconds < 300 && durationSeconds >= 60) {
      if (timerCard) timerCard.className = 'exam-timer-card warning';
      if (timerIcon) timerIcon.style.color = '#d97706';
    } else if (durationSeconds < 60) {
      if (timerCard) timerCard.className = 'exam-timer-card danger';
      if (timerIcon) timerIcon.style.color = '#dc2626';
    }
  }, 1000);

  function showQuestion(idx) {
    document.querySelectorAll('.question-pane').forEach(el => el.style.display = 'none');
    const target = document.getElementById('q-pane-' + idx);
    if (target) {
      target.style.display = 'block';
      if (window.typesetMathJax) {
        window.typesetMathJax(target);
      } else if (window.MathJax && window.MathJax.typesetPromise) {
        MathJax.typesetPromise([target]).catch(function() {});
      }
    }
    currentQ = idx;
    updatePaletteCurrent();
  }

  function nextQuestion(idx) { if (idx < totalQ - 1) showQuestion(idx + 1); }
  function prevQuestion(idx) { if (idx > 0) showQuestion(idx - 1); }

  function markAnswered(idx, qId, optKey) {
    // Style the label
    document.querySelectorAll(`label[id^="opt-label-${qId}-"]`).forEach(l => {
      l.style.borderColor = '#e2e8f0';
      l.style.background = '#ffffff';
    });
    const chosen = document.getElementById(`opt-label-${qId}-${optKey}`);
    if (chosen) {
      chosen.style.borderColor = 'var(--brand-emerald)';
      chosen.style.background = '#ecfdf5';
    }

    // Mark palette
    const btn = document.getElementById('palette-btn-' + idx);
    if (btn) {
      btn.style.background = '#059669';
      btn.style.borderColor = '#059669';
      btn.style.color = '#ffffff';
    }

    updateAnsweredCounter();
  }

  function clearChoice(qId, idx) {
    document.querySelectorAll('input[name="answers[' + qId + ']"]').forEach(r => r.checked = false);
    document.querySelectorAll(`label[id^="opt-label-${qId}-"]`).forEach(l => {
      l.style.borderColor = '#e2e8f0';
      l.style.background = '#ffffff';
    });
    const btn = document.getElementById('palette-btn-' + idx);
    if (btn) {
      btn.style.background = '#ffffff';
      btn.style.borderColor = '#cbd5e1';
      btn.style.color = '#1e293b';
    }
    updateAnsweredCounter();
  }

  function updateAnsweredCounter() {
    const answeredCount = document.querySelectorAll('input[type="radio"]:checked').length;
    const badge = document.getElementById('answeredCounterBadge');
    if (badge) badge.innerText = `${answeredCount} / ${totalQ}`;
  }

  function updatePaletteCurrent() {
    document.querySelectorAll('[id^="palette-btn-"]').forEach((btn, idx) => {
      if (idx === currentQ) {
        btn.style.boxShadow = '0 0 0 2.5px #d97706';
      } else {
        btn.style.boxShadow = 'none';
      }
    });
  }

  function confirmFinishExam() {
    const answeredCount = document.querySelectorAll('input[type="radio"]:checked').length;
    const countEl = document.getElementById('modalAnsweredCount');
    if (countEl) countEl.innerText = answeredCount;
    const modal = document.getElementById('submitModal');
    if (modal) modal.style.display = 'flex';
  }

  function closeSubmitModal() {
    const modal = document.getElementById('submitModal');
    if (modal) modal.style.display = 'none';
  }

  function executeSubmit() {
    document.getElementById('examForm').submit();
  }

  // Keyboard navigation
  document.addEventListener('keydown', function(e) {
    if (e.key === 'ArrowRight' && !e.target.matches('input, textarea')) {
      nextQuestion(currentQ);
    } else if (e.key === 'ArrowLeft' && !e.target.matches('input, textarea')) {
      prevQuestion(currentQ);
    }
  });

  updatePaletteCurrent();
</script>
@endsection
