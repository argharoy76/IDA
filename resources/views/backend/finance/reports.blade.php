@extends('layouts.portal')

@section('title', 'Financial Reports')
@section('page_title', 'Academy Financial Reports & Statements')
@section('page_subtitle', 'Monthly inflow vs outflow analysis, departmental breakdown, and net surplus')

@section('topbar_actions')
  <a href="{{ route('admin.finance.index') }}" class="btn-tactical btn-tactical-outline">
    <i class="fa-solid fa-arrow-left"></i> Back to Ledger
  </a>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

  <!-- Month & Year Filter Bar -->
  <div class="tactical-card" style="padding: 16px;">
    <form action="{{ route('admin.finance.reports') }}" method="GET" style="display: flex; gap: 14px; align-items: flex-end; flex-wrap: wrap;">
      <div style="width: 180px;">
        <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px;">Report Month</label>
        <select name="month" class="form-tactical" style="height: 40px;">
          @for($m = 1; $m <= 12; $m++)
            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
              {{ date('F', mktime(0, 0, 0, $m, 1)) }}
            </option>
          @endfor
        </select>
      </div>

      <div style="width: 140px;">
        <label style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px;">Report Year</label>
        <select name="year" class="form-tactical" style="height: 40px;">
          @for($y = date('Y'); $y >= date('Y') - 5; $y--)
            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
          @endfor
        </select>
      </div>

      <button type="submit" class="btn-tactical btn-tactical-primary" style="height: 40px;">
        <i class="fa-solid fa-chart-pie"></i> Generate Statement
      </button>

      <button type="button" onclick="window.print()" class="btn-tactical btn-tactical-outline" style="height: 40px;">
        <i class="fa-solid fa-print"></i> Print Report
      </button>
    </form>
  </div>

  <!-- Monthly Net Summary Cards -->
  <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
    <div class="stat-card">
      <div class="stat-icon" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
        <i class="fa-solid fa-arrow-down-left-and-up-right-to-center"></i>
      </div>
      <div>
        <div class="stat-label">Total Inflow ({{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }})</div>
        <div class="stat-value" style="color: #10b981; font-size: 24px;">৳{{ number_format($monthlyInflow, 2) }}</div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon" style="background: rgba(239, 68, 68, 0.15); color: #ef4444;">
        <i class="fa-solid fa-arrow-up-right-from-square"></i>
      </div>
      <div>
        <div class="stat-label">Total Outflow / Expenses</div>
        <div class="stat-value" style="color: #ef4444; font-size: 24px;">৳{{ number_format($monthlyOutflow, 2) }}</div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa;">
        <i class="fa-solid fa-scale-balanced"></i>
      </div>
      <div>
        <div class="stat-label">Net Surplus / (Deficit)</div>
        <div class="stat-value" style="font-size: 24px; color: {{ $netCashFlow >= 0 ? '#10b981' : '#ef4444' }};">
          {{ $netCashFlow >= 0 ? '+' : '' }}৳{{ number_format($netCashFlow, 2) }}
        </div>
      </div>
    </div>
  </div>

  <!-- Two-Column Category Breakdown Tables -->
  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
    <!-- Inflow by Category -->
    <div class="tactical-card" style="padding: 0; overflow: hidden;">
      <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-soft); display: flex; align-items: center; justify-content: space-between;">
        <h3 style="font-size: 15px; font-weight: 700; margin: 0; color: #10b981; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-circle-down"></i> Revenue / Inflow Categories
        </h3>
        <span style="font-size: 13px; font-weight: 700; color: #10b981;">৳{{ number_format($monthlyInflow, 2) }}</span>
      </div>

      <table class="tactical-table">
        <thead>
          <tr>
            <th>Category</th>
            <th>Proportion</th>
            <th style="text-align: right;">Amount (৳)</th>
          </tr>
        </thead>
        <tbody>
          @forelse($inflowByCategory as $item)
            @php
              $percent = $monthlyInflow > 0 ? round(($item->total / $monthlyInflow) * 100, 1) : 0;
            @endphp
            <tr>
              <td>
                <strong style="font-size: 13px;">{{ $item->category->name ?? 'Direct Inflow' }}</strong>
              </td>
              <td>
                <div style="display: flex; align-items: center; gap: 8px;">
                  <div style="flex: 1; height: 6px; background: rgba(255,255,255,0.06); border-radius: 999px; overflow: hidden;">
                    <div style="width: {{ $percent }}%; height: 100%; background: #10b981;"></div>
                  </div>
                  <span style="font-size: 11px; color: var(--text-muted); width: 35px;">{{ $percent }}%</span>
                </div>
              </td>
              <td style="text-align: right; font-family: 'Plus Jakarta Sans', monospace; font-weight: 700; color: #10b981;">
                ৳{{ number_format($item->total, 2) }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="3" style="text-align: center; padding: 28px; color: var(--text-muted);">
                No verified inflow recorded in this period.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Outflow by Category -->
    <div class="tactical-card" style="padding: 0; overflow: hidden;">
      <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-soft); display: flex; align-items: center; justify-content: space-between;">
        <h3 style="font-size: 15px; font-weight: 700; margin: 0; color: #ef4444; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-circle-up"></i> Expenditure / Outflow Categories
        </h3>
        <span style="font-size: 13px; font-weight: 700; color: #ef4444;">৳{{ number_format($monthlyOutflow, 2) }}</span>
      </div>

      <table class="tactical-table">
        <thead>
          <tr>
            <th>Category</th>
            <th>Proportion</th>
            <th style="text-align: right;">Amount (৳)</th>
          </tr>
        </thead>
        <tbody>
          @forelse($outflowByCategory as $item)
            @php
              $percent = $monthlyOutflow > 0 ? round(($item->total / $monthlyOutflow) * 100, 1) : 0;
            @endphp
            <tr>
              <td>
                <strong style="font-size: 13px;">{{ $item->category->name ?? 'Direct Expense' }}</strong>
              </td>
              <td>
                <div style="display: flex; align-items: center; gap: 8px;">
                  <div style="flex: 1; height: 6px; background: rgba(255,255,255,0.06); border-radius: 999px; overflow: hidden;">
                    <div style="width: {{ $percent }}%; height: 100%; background: #ef4444;"></div>
                  </div>
                  <span style="font-size: 11px; color: var(--text-muted); width: 35px;">{{ $percent }}%</span>
                </div>
              </td>
              <td style="text-align: right; font-family: 'Plus Jakarta Sans', monospace; font-weight: 700; color: #ef4444;">
                ৳{{ number_format($item->total, 2) }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="3" style="text-align: center; padding: 28px; color: var(--text-muted);">
                No expenses logged in this period.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
