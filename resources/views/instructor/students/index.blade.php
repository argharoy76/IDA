@extends('layouts.portal')

@section('title', 'Assigned Cadets')
@section('page_title', 'Cadets Under Command')
@section('page_subtitle', 'Monitor development, record dated observations, track radar proficiencies, and assign improvement tasks')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

  <!-- Students Table -->
  <div class="tactical-card" style="padding: 0; overflow: hidden;">
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-soft); display: flex; justify-content: space-between; align-items: center;">
      <h3 style="font-size: 15px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-users" style="color: var(--accent-gold);"></i> Squadron Roster
      </h3>
      <span style="font-size: 12px; color: var(--text-muted);">Total: {{ $students->total() }} Cadets</span>
    </div>

    <div style="overflow-x: auto;">
      <table class="tactical-table">
        <thead>
          <tr>
            <th>Cadet ID</th>
            <th>Name & Roll</th>
            <th>Squadron / Batch</th>
            <th>Attendance %</th>
            <th>Competence Radar Avg</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($students as $cadet)
            <tr>
              <td>
                <span style="font-family: 'Plus Jakarta Sans', monospace; font-weight: 700; color: var(--accent-gold);">
                  {{ $cadet->student_id_code }}
                </span>
              </td>
              <td>
                <div style="font-weight: 700; color: var(--text-main);">{{ $cadet->user->name ?? 'Cadet' }}</div>
                <div style="font-size: 11px; color: var(--text-muted);">Roll: {{ $cadet->roll_number }}</div>
              </td>
              <td>
                <strong>{{ $cadet->currentBatch->name ?? 'Squadron' }}</strong>
              </td>
              <td>
                <div style="display: flex; align-items: center; gap: 8px;">
                  <span style="font-family: 'Plus Jakarta Sans', monospace; font-weight: 800; color: {{ $cadet->attendance_percentage < 75 ? '#ef4444' : '#10b981' }};">
                    {{ $cadet->attendance_percentage }}%
                  </span>
                  @if($cadet->attendance_percentage < 75)
                    <span class="badge badge-danger" style="font-size: 9px;">LOW</span>
                  @endif
                </div>
              </td>
              <td>
                @if($cadet->dossier)
                  @php
                    $avg = round(($cadet->dossier->iq_score + $cadet->dossier->leadership_score + $cadet->dossier->communication_score + $cadet->dossier->physical_fitness_score + $cadet->dossier->discipline_score) / 5, 1);
                  @endphp
                  <span style="font-family: 'Plus Jakarta Sans', monospace; font-weight: 700; color: #60a5fa;">{{ $avg }}/100</span>
                @else
                  <span style="color: var(--text-muted); font-size: 12px;">Unassessed</span>
                @endif
              </td>
              <td>
                @if($cadet->requires_attention)
                  <span class="badge badge-danger"><i class="fa-solid fa-triangle-exclamation"></i> ATTENTION</span>
                @else
                  <span class="badge badge-emerald">OPTIMAL</span>
                @endif
              </td>
              <td>
                <a href="{{ route('instructor.students.show', $cadet->id) }}" class="btn-tactical btn-tactical-primary" style="padding: 4px 10px; font-size: 11px;">
                  <i class="fa-solid fa-address-card"></i> Dossier & Observations
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" style="text-align: center; padding: 48px; color: var(--text-muted);">
                No cadets assigned to your batches.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($students->hasPages())
      <div style="padding: 16px 20px; border-top: 1px solid var(--border-soft);">
        {{ $students->links() }}
      </div>
    @endif
  </div>

</div>
@endsection
