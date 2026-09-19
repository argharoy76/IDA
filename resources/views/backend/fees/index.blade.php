@extends('layouts.portal')

@section('title', 'Fees')
@section('page_title', 'Fees & Invoices')
@section('page_subtitle', 'Manage student invoices, payment records, dues, and waivers')

@section('topbar_actions')
  <a href="{{ route('admin.fees.create_invoice') }}" class="btn-tactical btn-tactical-primary">
    <i class="fa-solid fa-file-circle-plus"></i> Create Invoice
  </a>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

  <!-- Fee Metric Overview Strip -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
    <div class="stat-card">
      <div class="stat-icon" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
        <i class="fa-solid fa-hand-holding-dollar"></i>
      </div>
      <div>
        <div class="stat-label">Total Collected</div>
        <div class="stat-value" style="color: #10b981;">৳{{ number_format($totalCollected, 2) }}</div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon" style="background: rgba(239, 68, 68, 0.15); color: #ef4444;">
        <i class="fa-solid fa-clock-rotate-left"></i>
      </div>
      <div>
        <div class="stat-label">Total Outstanding Dues</div>
        <div class="stat-value" style="color: #ef4444;">৳{{ number_format($totalOutstanding, 2) }}</div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon" style="background: rgba(217, 119, 6, 0.15); color: #d97706;">
        <i class="fa-solid fa-file-invoice"></i>
      </div>
      <div>
        <div class="stat-label">Total Generated Invoices</div>
        <div class="stat-value">{{ $invoices->total() }}</div>
      </div>
    </div>
  </div>

  <!-- Filter & Search Toolbar -->
  <div class="tactical-card" style="padding: 16px;">
    <form action="{{ route('admin.fees.index') }}" method="GET" style="display: flex; gap: 14px; flex-wrap: wrap; align-items: flex-end;">
      <div style="flex: 1; min-width: 240px;">
        <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px;">Search Cadets / Invoice #</label>
        <div style="position: relative;">
          <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 12px; color: var(--text-muted);"></i>
          <input type="text" name="search" value="{{ request('search') }}" placeholder="Invoice #, Cadet ID, or Name..." class="form-tactical" style="padding-left: 36px; height: 42px;">
        </div>
      </div>

      <div style="width: 200px;">
        <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px;">Status Filter</label>
        <select name="status" class="form-tactical" style="height: 42px;">
          <option value="">All Payment Statuses</option>
          <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
          <option value="partially_paid" {{ request('status') === 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
          <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
          <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
        </select>
      </div>

      <button type="submit" class="btn-tactical btn-tactical-outline" style="height: 42px;">
        <i class="fa-solid fa-filter"></i> Apply Filter
      </button>

      @if(request()->hasAny(['search', 'status']))
        <a href="{{ route('admin.fees.index') }}" class="btn-tactical" style="height: 42px; background: rgba(255,255,255,0.06); color: var(--text-muted);">
          <i class="fa-solid fa-xmark"></i> Clear
        </a>
      @endif
    </form>
  </div>

  <!-- Invoices Table -->
  <div class="tactical-card" style="padding: 0; overflow: hidden;">
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-soft); display: flex; justify-content: space-between; align-items: center;">
      <h3 style="font-size: 15px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-receipt" style="color: var(--accent-gold);"></i> All Issued Invoices Ledger
      </h3>
      <span style="font-size: 12px; color: var(--text-muted);">Showing {{ $invoices->firstItem() ?? 0 }} - {{ $invoices->lastItem() ?? 0 }} of {{ $invoices->total() }}</span>
    </div>

    <div style="overflow-x: auto;">
      <table class="tactical-table">
        <thead>
          <tr>
            <th>Invoice ID</th>
            <th>Cadet Info</th>
            <th>Fee Category</th>
            <th>Gross</th>
            <th>Waiver / Disc</th>
            <th>Net Total</th>
            <th>Paid</th>
            <th>Due Balance</th>
            <th>Due Date</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($invoices as $inv)
            <tr>
              <td>
                <span style="font-family: 'Plus Jakarta Sans', monospace; font-weight: 700; color: #60a5fa;">{{ $inv->invoice_number }}</span>
                <div style="font-size: 10px; color: var(--text-muted);">{{ $inv->created_at->format('d M Y') }}</div>
              </td>
              <td>
                @if($inv->student)
                  <a href="{{ route('admin.students.show', $inv->student->id) }}" style="font-weight: 700; color: var(--text-main); text-decoration: none;">
                    {{ $inv->student->user->name ?? 'Cadet' }}
                  </a>
                  <div style="font-size: 11px; color: var(--accent-gold); font-family: 'Plus Jakarta Sans', monospace;">{{ $inv->student->student_id_code }}</div>
                @else
                  <span style="color: var(--text-muted);">Direct / External</span>
                @endif
              </td>
              <td>
                <div style="font-weight: 600; font-size: 13px;">{{ $inv->title }}</div>
                <small style="color: var(--text-muted);">{{ $inv->feeType->name ?? 'General Fee' }}</small>
              </td>
              <td style="font-family: 'Plus Jakarta Sans', monospace;">৳{{ number_format($inv->gross_amount, 2) }}</td>
              <td style="font-family: 'Plus Jakarta Sans', monospace; color: #d97706;">
                -৳{{ number_format($inv->discount_amount + $inv->waiver_amount, 2) }}
              </td>
              <td style="font-family: 'Plus Jakarta Sans', monospace; font-weight: 700;">৳{{ number_format($inv->net_amount, 2) }}</td>
              <td style="font-family: 'Plus Jakarta Sans', monospace; color: #10b981; font-weight: 700;">৳{{ number_format($inv->paid_amount, 2) }}</td>
              <td style="font-family: 'Plus Jakarta Sans', monospace; color: {{ $inv->due_amount > 0 ? '#ef4444' : '#10b981' }}; font-weight: 700;">
                ৳{{ number_format($inv->due_amount, 2) }}
              </td>
              <td>
                <div style="font-size: 12px; color: {{ \Carbon\Carbon::parse($inv->due_date)->isPast() && $inv->due_amount > 0 ? '#ef4444' : 'var(--text-main)' }};">
                  {{ \Carbon\Carbon::parse($inv->due_date)->format('d M Y') }}
                </div>
              </td>
              <td>
                <span class="badge {{ $inv->status === 'paid' ? 'badge-emerald' : ($inv->status === 'partially_paid' ? 'badge-gold' : ($inv->status === 'overdue' ? 'badge-danger' : 'badge-navy')) }}">
                  {{ strtoupper(str_replace('_', ' ', $inv->status)) }}
                </span>
              </td>
              <td>
                <a href="{{ route('admin.payments.verification', ['status' => 'all']) }}" class="btn-tactical btn-tactical-outline" style="padding: 4px 10px; font-size: 11px;">
                  <i class="fa-solid fa-receipt"></i> Payments ({{ $inv->payments->count() }})
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="11" style="text-align: center; padding: 48px; color: var(--text-muted);">
                <i class="fa-solid fa-receipt" style="font-size: 32px; margin-bottom: 12px; display: block; opacity: 0.4;"></i>
                No invoice records found matching criteria.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($invoices->hasPages())
      <div style="padding: 16px 20px; border-top: 1px solid var(--border-soft);">
        {{ $invoices->links() }}
      </div>
    @endif
  </div>

</div>
@endsection
