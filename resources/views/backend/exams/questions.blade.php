@extends('layouts.portal')

@section('title', 'Question Bank: ' . $exam->title)
@section('page_title', 'Question Bank: ' . $exam->title)
@section('page_subtitle', 'Add MCQ items, correct answer keys, difficulty level, and explanations')

@section('topbar_actions')
  <a href="{{ route('admin.exams.index') }}" class="btn-tactical btn-tactical-outline">
    <i class="fa-solid fa-arrow-left"></i> Back to Exam Bank
  </a>
@endsection

@section('content')
<div style="display: grid; grid-template-columns: 1fr 1.3fr; gap: 24px; align-items: flex-start;">

  <!-- Form: Add Question -->
  <div class="tactical-card">
    <div style="border-bottom: 1px solid var(--border-soft); padding-bottom: 12px; margin-bottom: 18px;">
      <h3 style="font-size: 15px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px; color: var(--accent-gold);">
        <i class="fa-solid fa-circle-plus"></i> Add New MCQ Question
      </h3>
    </div>

    <form action="{{ route('admin.exams.questions.store', $exam->id) }}" method="POST">
      @csrf

      <div style="display: flex; flex-direction: column; gap: 14px;">
        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Question Text *</label>
          <textarea name="question_text" rows="3" class="form-tactical" placeholder="e.g. Which of the following numbers completes the series: 3, 7, 15, 31, ...?" required>{{ old('question_text') }}</textarea>
          @error('question_text') <span style="color: #ef4444; font-size: 11px;">{{ $message }}</span> @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px; color: #60a5fa;">Option A *</label>
            <input type="text" name="option_a" value="{{ old('option_a') }}" class="form-tactical" placeholder="Option A text" required>
          </div>
          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px; color: #60a5fa;">Option B *</label>
            <input type="text" name="option_b" value="{{ old('option_b') }}" class="form-tactical" placeholder="Option B text" required>
          </div>
          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px; color: #60a5fa;">Option C *</label>
            <input type="text" name="option_c" value="{{ old('option_c') }}" class="form-tactical" placeholder="Option C text" required>
          </div>
          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px; color: #60a5fa;">Option D *</label>
            <input type="text" name="option_d" value="{{ old('option_d') }}" class="form-tactical" placeholder="Option D text" required>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px; color: #10b981;">Correct Key *</label>
            <select name="correct_answer" class="form-tactical" required>
              <option value="A" {{ old('correct_answer') === 'A' ? 'selected' : '' }}>A</option>
              <option value="B" {{ old('correct_answer') === 'B' ? 'selected' : '' }}>B</option>
              <option value="C" {{ old('correct_answer') === 'C' ? 'selected' : '' }}>C</option>
              <option value="D" {{ old('correct_answer') === 'D' ? 'selected' : '' }}>D</option>
            </select>
          </div>

          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Marks *</label>
            <input type="number" step="0.5" name="marks" value="{{ old('marks', 1.0) }}" class="form-tactical" required>
          </div>

          <div>
            <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Difficulty *</label>
            <select name="difficulty" class="form-tactical" required>
              <option value="easy">Easy</option>
              <option value="medium" selected>Medium</option>
              <option value="hard">Hard</option>
            </select>
          </div>
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Negative Marks (Optional)</label>
          <input type="number" step="0.05" name="negative_marks" value="{{ old('negative_marks', $exam->negative_marking_per_wrong) }}" class="form-tactical">
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Explanation / Solution Hint</label>
          <textarea name="explanation" rows="2" class="form-tactical" placeholder="Explanation revealed to candidate during post-test review...">{{ old('explanation') }}</textarea>
        </div>

        <button type="submit" class="btn-tactical btn-tactical-primary" style="margin-top: 8px;">
          <i class="fa-solid fa-plus"></i> Save Question to Bank
        </button>
      </div>
    </form>
  </div>

  <!-- Questions List -->
  <div class="tactical-card" style="padding: 0; overflow: hidden;">
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-soft); display: flex; justify-content: space-between; align-items: center;">
      <h3 style="font-size: 15px; font-weight: 700; margin: 0;">
        <i class="fa-solid fa-list-ol" style="color: var(--accent-gold);"></i> Configured Questions ({{ $exam->questions->count() }})
      </h3>
    </div>

    <div style="padding: 16px; display: flex; flex-direction: column; gap: 16px; max-height: 800px; overflow-y: auto;">
      @forelse($exam->questions as $idx => $q)
        <div style="background: var(--surface-subtle); border: 1px solid var(--border-soft); border-radius: 8px; padding: 16px;">
          <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
            <div style="display: flex; align-items: center; gap: 8px;">
              <span style="font-weight: 800; color: var(--brand-deep); font-size: 14px;">#{{ $idx + 1 }}</span>
              <span class="badge {{ $q->difficulty === 'hard' ? 'badge-danger' : ($q->difficulty === 'medium' ? 'badge-gold' : 'badge-emerald') }}">
                {{ strtoupper($q->difficulty) }}
              </span>
            </div>
            <div style="font-size: 12px; color: var(--text-muted);">
              Marks: <strong style="color: #059669;">+{{ $q->marks }}</strong> / <span style="color: #ef4444;">-{{ $q->negative_marks }}</span>
            </div>
          </div>

          <p style="font-weight: 700; font-size: 14px; margin: 0 0 12px 0; color: var(--text-main); line-height: 1.5;">
            {{ $q->question_text }}
          </p>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 10px;">
            @php $opts = is_array($q->options) ? $q->options : json_decode($q->options, true) ?? []; @endphp
            @foreach($opts as $opt)
              <div style="padding: 8px 12px; border-radius: 6px; font-size: 12.5px; background: {{ $opt['key'] === $q->correct_answer ? '#ecfdf5' : '#ffffff' }}; border: 1.5px solid {{ $opt['key'] === $q->correct_answer ? '#059669' : 'var(--border-soft)' }}; color: var(--text-main);">
                <strong style="color: {{ $opt['key'] === $q->correct_answer ? '#059669' : 'var(--brand-deep)' }};">{{ $opt['key'] }}:</strong> {{ $opt['text'] }}
              </div>
            @endforeach
          </div>

          @if($q->explanation)
            <div style="font-size: 12px; color: #92400e; background: #fffbeb; border: 1px solid #fde68a; padding: 10px 14px; border-radius: 6px;">
              <strong style="color: #92400e;">Explanation:</strong> {{ $q->explanation }}
            </div>
          @endif
        </div>
      @empty
        <div style="text-align: center; padding: 40px; color: var(--text-muted);">
          No questions added to this exam yet. Use the form on the left to populate questions.
        </div>
      @endforelse
    </div>
  </div>

</div>
@endsection
