@extends('layouts.exam')

@section('title', 'WAT Simulation: ' . $exam->title)

@section('content')
<style>
  @media (max-width: 640px) {
    .wat-card-responsive {
      padding: 24px 16px !important;
      min-height: 400px !important;
    }
  }
</style>

<div style="max-width: 820px; margin: 0 auto;">

  <form id="watForm" action="{{ route('external.tests.submit', $exam->id) }}" method="POST">
    @csrf

    <div class="exam-card wat-card-responsive" style="padding: 44px 36px; text-align: center; position: relative; min-height: 480px; display: flex; flex-direction: column; justify-content: space-between;">

      <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 16px; margin-bottom: 24px;">
        <span class="badge" style="background: #0f172a; color: #fff; font-size: 13px; font-weight: 700; padding: 5px 12px; border-radius: 6px;" id="wordProgress">
          Word 1 of {{ $exam->watWords->count() }}
        </span>
        
        <div style="display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-bolt" style="color: #d97706;"></i>
          <span style="font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 700;">Word Timer:</span>
          <span id="wordTimerDisplay" style="font-family: 'Poppins', monospace; font-size: 22px; font-weight: 800; color: #059669; width: 44px;">15s</span>
        </div>
      </div>

      <div style="height: 8px; background: #e2e8f0; border-radius: 999px; overflow: hidden; margin-bottom: 36px;">
        <div id="wordTimeBar" style="width: 100%; height: 100%; background: #059669; transition: width 1s linear;"></div>
      </div>

      <div style="margin: 20px 0;">
        <div style="font-size: 12px; text-transform: uppercase; letter-spacing: 2px; color: #64748b; font-weight: 700; margin-bottom: 14px;">Prompt Word</div>
        <div id="flashWordBox" style="font-family: 'Poppins', sans-serif; font-size: clamp(28px, 8vw, 52px); font-weight: 900; letter-spacing: 3px; color: #0f172a; text-transform: uppercase;">
          LOADING...
        </div>
      </div>

      <div style="max-width: 620px; margin: 0 auto 30px auto; width: 100%;">
        <label style="display: block; font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; margin-bottom: 8px; text-align: left;">
          Your Spontaneous Meaningful Sentence:
        </label>
        <input type="text" id="sentenceInput" class="form-tactical" placeholder="Type first reaction immediately..." style="height: 52px; font-size: 16px; padding: 0 18px; border-radius: 10px; border: 1.5px solid #cbd5e1; width: 100%;" autofocus autocomplete="off">
      </div>

      <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #e2e8f0; padding-top: 20px;">
        <span style="font-size: 12.5px; color: #64748b;"><i class="fa-regular fa-clock"></i> Auto-advances every 15 seconds</span>
        <button type="button" class="btn-exam-submit" onclick="advanceWord()">
          Next Word <i class="fa-solid fa-arrow-right"></i>
        </button>
      </div>

      <div id="hiddenInputs"></div>
    </div>
  </form>

</div>
@endsection

@section('scripts')
<script>
  const watWords = @json($exam->watWords);
  let currentIndex = 0;
  let wordTimer = null;
  let wordSecondsLeft = 15;
  let wordStartTime = Date.now();

  function startWord(index) {
    if (index >= watWords.length) {
      finishWat();
      return;
    }

    const currentWord = watWords[index];
    const duration = currentWord.display_seconds || 15;
    wordSecondsLeft = duration;
    wordStartTime = Date.now();

    document.getElementById('wordProgress').innerText = `Word ${index + 1} of ${watWords.length}`;
    document.getElementById('flashWordBox').innerText = currentWord.word;
    document.getElementById('sentenceInput').value = '';
    document.getElementById('sentenceInput').focus();

    updateTimerDisplay(duration, duration);

    clearInterval(wordTimer);
    wordTimer = setInterval(function() {
      wordSecondsLeft--;
      updateTimerDisplay(wordSecondsLeft, duration);

      if (wordSecondsLeft <= 0) {
        clearInterval(wordTimer);
        advanceWord();
      }
    }, 1000);
  }

  function updateTimerDisplay(remaining, total) {
    const display = document.getElementById('wordTimerDisplay');
    const bar = document.getElementById('wordTimeBar');
    display.innerText = remaining + 's';

    const pct = Math.max(0, (remaining / total) * 100);
    bar.style.width = pct + '%';

    if (remaining <= 3) {
      display.style.color = '#dc2626';
      bar.style.background = '#dc2626';
    } else {
      display.style.color = '#059669';
      bar.style.background = '#059669';
    }
  }

  function advanceWord() {
    clearInterval(wordTimer);
    const currentWord = watWords[currentIndex];
    const inputVal = document.getElementById('sentenceInput').value.trim();
    const spent = Math.min(15, Math.round((Date.now() - wordStartTime) / 1000));

    const container = document.getElementById('hiddenInputs');
    
    const inputResp = document.createElement('input');
    inputResp.type = 'hidden';
    inputResp.name = `wat_responses[${currentWord.id}]`;
    inputResp.value = inputVal;
    container.appendChild(inputResp);

    const inputTime = document.createElement('input');
    inputTime.type = 'hidden';
    inputTime.name = `wat_times[${currentWord.id}]`;
    inputTime.value = spent;
    container.appendChild(inputTime);

    currentIndex++;
    startWord(currentIndex);
  }

  function finishWat() {
    clearInterval(wordTimer);
    document.getElementById('watForm').submit();
  }

  document.getElementById('sentenceInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      advanceWord();
    }
  });

  document.addEventListener('DOMContentLoaded', function() {
    startWord(0);
  });
</script>
@endsection
