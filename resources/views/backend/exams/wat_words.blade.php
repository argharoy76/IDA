@extends('layouts.portal')

@section('title', 'WAT Words: ' . $exam->title)
@section('page_title', 'WAT Flash Sequence: ' . $exam->title)
@section('page_subtitle', 'Word Association Test words flashed for 15 seconds each to evaluate psychological reflexes')

@section('topbar_actions')
  <a href="{{ route('admin.exams.index') }}" class="btn-tactical btn-tactical-outline">
    <i class="fa-solid fa-arrow-left"></i> Back to Exam Bank
  </a>
@endsection

@section('content')
<div style="display: grid; grid-template-columns: 1fr 1.3fr; gap: 24px; align-items: flex-start;">

  <!-- Form: Add WAT Word -->
  <div class="tactical-card">
    <div style="border-bottom: 1px solid var(--border-soft); padding-bottom: 12px; margin-bottom: 18px;">
      <h3 style="font-size: 15px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px; color: var(--accent-gold);">
        <i class="fa-solid fa-bolt"></i> Add Word to Flash Sequence
      </h3>
    </div>

    <form action="{{ route('admin.exams.wat_words.store', $exam->id) }}" method="POST">
      @csrf

      <div style="display: flex; flex-direction: column; gap: 14px;">
        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Prompt Word (English / Bengali) *</label>
          <input type="text" name="word" value="{{ old('word') }}" placeholder="e.g. COURAGE, FEAR, LEADER, DUTY..." class="form-tactical" style="text-transform: uppercase; font-family: 'Plus Jakarta Sans', monospace; font-size: 16px; font-weight: 700;" required>
          @error('word') <span style="color: #ef4444; font-size: 11px;">{{ $message }}</span> @enderror
        </div>

        <div>
          <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 6px;">Flash Duration (Seconds) *</label>
          <input type="number" name="display_seconds" value="{{ old('display_seconds', 15) }}" min="5" max="30" class="form-tactical" required>
          @error('display_seconds') <span style="color: #ef4444; font-size: 11px;">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="btn-tactical btn-tactical-primary" style="margin-top: 8px;">
          <i class="fa-solid fa-plus"></i> Append Word to Sequence
        </button>
      </div>
    </form>
  </div>

  <!-- Words Sequence List -->
  <div class="tactical-card" style="padding: 0; overflow: hidden;">
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-soft); display: flex; justify-content: space-between; align-items: center;">
      <h3 style="font-size: 15px; font-weight: 700; margin: 0;">
        <i class="fa-solid fa-layer-group" style="color: var(--accent-gold);"></i> Sequence Words ({{ $exam->watWords->count() }})
      </h3>
    </div>

    <table class="tactical-table">
      <thead>
        <tr>
          <th>Seq #</th>
          <th>Prompt Word</th>
          <th>Flash Duration</th>
          <th>Total Expected Time</th>
        </tr>
      </thead>
      <tbody>
        @forelse($exam->watWords as $idx => $w)
          <tr>
            <td>
              <span style="font-family: 'Plus Jakarta Sans', monospace; font-weight: 800; color: #60a5fa;">#{{ $w->order_seq ?? ($idx + 1) }}</span>
            </td>
            <td>
              <span style="font-size: 16px; font-weight: 800; letter-spacing: 1px; color: var(--brand-deep);">
                {{ $w->word }}
              </span>
            </td>
            <td>
              <span class="badge badge-gold">{{ $w->display_seconds }} Seconds</span>
            </td>
            <td>
              <span style="font-size: 12px; color: var(--text-muted);">{{ ($idx + 1) * $w->display_seconds }}s into test</span>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="4" style="text-align: center; padding: 40px; color: var(--text-muted);">
              No WAT words added yet. Add flash sequence words using the form on the left.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

</div>
@endsection
