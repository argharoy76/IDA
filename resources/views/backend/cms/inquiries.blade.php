@extends('layouts.portal')

@section('title', 'Admission Inquiries CMS')
@section('page_title', 'Admission Inquiries & Leads')
@section('page_subtitle', 'Review candidate requests submitted through public contact forms and track follow-up progress')

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px;">

  @include('backend.cms.partials.nav')

  @if(session('success'))
    <div class="alert alert-success" style="background: rgba(16, 185, 129, 0.15); border: 1px solid var(--brand-mint); color: #065f46; padding: 12px 16px; border-radius: var(--radius-sm); font-size: 13px; font-weight: 600;">
      <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
  @endif

  <div class="tactical-card">
    <div style="border-bottom: 1px solid var(--border-soft); padding-bottom: 14px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
      <div>
        <h3 style="font-size: 17px; font-weight: 800; color: var(--brand-deep); margin: 0 0 4px 0;">
          <i class="fa-solid fa-envelope-open-text" style="color: var(--accent-gold);"></i> Admission Inquiries ({{ $inquiries->count() }})
        </h3>
        <p style="font-size: 12.5px; color: var(--text-muted); margin: 0;">
          Incoming communications from prospective cadets and parents.
        </p>
      </div>

      <div style="display: flex; gap: 8px;">
        <span class="badge badge-danger">{{ $inquiries->where('status', 'unread')->count() }} Unread</span>
        <span class="badge badge-info">{{ $inquiries->where('status', 'read')->count() }} Read</span>
        <span class="badge badge-success">{{ $inquiries->where('status', 'replied')->count() }} Replied</span>
      </div>
    </div>

    <div style="overflow-x: auto;">
      <table class="table-tactical" style="width: 100%; font-size: 13px;">
        <thead>
          <tr style="background: var(--surface-subtle); text-align: left;">
            <th style="padding: 10px 14px;">Date</th>
            <th style="padding: 10px 14px;">Applicant Name</th>
            <th style="padding: 10px 14px;">Contact Details</th>
            <th style="padding: 10px 14px;">Subject & Message</th>
            <th style="padding: 10px 14px;">Status</th>
            <th style="padding: 10px 14px; text-align: right;">Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($inquiries as $inq)
            <tr style="border-bottom: 1px solid var(--border-soft); {{ $inq->status === 'unread' ? 'background: rgba(254, 242, 242, 0.5);' : '' }}">
              <td style="padding: 12px 14px; white-space: nowrap; color: var(--text-muted);">
                {{ $inq->created_at->format('d M, Y h:i A') }}
              </td>
              <td style="padding: 12px 14px; font-weight: 700; color: var(--brand-deep);">
                {{ $inq->name }}
              </td>
              <td style="padding: 12px 14px;">
                <div style="color: var(--brand-emerald); font-weight: 500;">{{ $inq->email }}</div>
                @if($inq->phone)
                  <div style="color: var(--text-muted); font-size: 11.5px;"><i class="fa-solid fa-phone"></i> {{ $inq->phone }}</div>
                @endif
              </td>
              <td style="padding: 12px 14px; max-width: 320px;">
                <strong style="color: var(--brand-deep); display: block; margin-bottom: 2px;">{{ $inq->subject }}</strong>
                <p style="margin: 0; font-size: 12px; color: var(--text-body); line-height: 1.4;">{{ $inq->message }}</p>
              </td>
              <td style="padding: 12px 14px; white-space: nowrap;">
                @if($inq->status === 'unread')
                  <span class="badge badge-danger">Unread</span>
                @elseif($inq->status === 'read')
                  <span class="badge badge-info">Read</span>
                @else
                  <span class="badge badge-success">Replied</span>
                @endif
              </td>
              <td style="padding: 12px 14px; text-align: right; white-space: nowrap;">
                <form action="{{ route('admin.cms.inquiries.status', $inq->id) }}" method="POST" style="display: inline-block; margin-right: 4px;">
                  @csrf
                  @if($inq->status === 'unread')
                    <input type="hidden" name="status" value="read">
                    <button type="submit" class="btn-tactical btn-tactical-outline" style="font-size: 11px; padding: 4px 8px;" title="Mark as Read">
                      <i class="fa-regular fa-envelope-open"></i>
                    </button>
                  @elseif($inq->status === 'read')
                    <input type="hidden" name="status" value="replied">
                    <button type="submit" class="btn-tactical btn-tactical-outline" style="font-size: 11px; padding: 4px 8px; color: var(--brand-emerald); border-color: var(--brand-emerald);" title="Mark as Replied">
                      <i class="fa-solid fa-reply"></i>
                    </button>
                  @endif
                </form>

                <form action="{{ route('admin.cms.inquiries.delete', $inq->id) }}" method="POST" onsubmit="return confirm('Delete this inquiry?');" style="display: inline-block;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn-tactical btn-tactical-outline" style="color: #ef4444; border-color: rgba(239,68,68,0.3); font-size: 11px; padding: 4px 8px;" title="Delete">
                    <i class="fa-solid fa-trash-can"></i>
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" style="padding: 24px; text-align: center; color: var(--text-muted);">
                No admission inquiries received yet.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
