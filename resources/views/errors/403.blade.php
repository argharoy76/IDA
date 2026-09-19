@extends('layouts.public')

@section('title', 'Administrative Access Required | Imperial Defence Academy')

@section('content')
<div style="max-width: 600px; margin: 60px auto; padding: 0 24px; text-align: center;">
  <div style="background: var(--surface); border: 1px solid var(--border-soft); border-radius: var(--radius-lg); padding: 44px 36px; box-shadow: var(--shadow-hover);">
    
    <div style="width: 72px; height: 72px; border-radius: 20px; background: #fef3c7; color: #d97706; display: grid; place-items: center; font-size: 32px; margin: 0 auto 20px; border: 1px solid #fde68a;">
      <i class="fa-solid fa-user-lock"></i>
    </div>

    <span class="badge badge-gold" style="margin-bottom: 12px; font-size: 11px;">ACCESS CONTROL (HTTP 403)</span>
    <h2 style="font-size: 24px; font-weight: 800; color: var(--brand-deep); margin-bottom: 8px;">
      Administrative Privileges Required
    </h2>

    @auth
      <p style="font-size: 14px; color: var(--text-muted); line-height: 1.6; margin-bottom: 20px;">
        You are currently signed in as <strong style="color: var(--text-main);">{{ auth()->user()->name }}</strong> 
        with the role <span class="badge badge-navy" style="font-size: 11px;">{{ strtoupper(str_replace('_', ' ', auth()->user()->role)) }}</span>.
        This section is reserved for Super Administrators and Finance Officers.
      </p>

      <div style="background: #f8fafc; border: 1px solid var(--border-soft); border-radius: var(--radius-sm); padding: 16px; margin-bottom: 24px; text-align: left;">
        <span style="font-size: 11.5px; font-weight: 800; color: var(--brand-deep); text-transform: uppercase; display: block; margin-bottom: 6px;">
          <i class="fa-solid fa-wand-magic-sparkles" style="color: var(--accent-gold);"></i> Instant Solution:
        </span>
        <p style="font-size: 12.5px; color: var(--text-body); margin: 0;">
          Click the button below to instantly switch to the <strong>Super Admin</strong> account and gain full access to the Executive Dashboard and Website CMS.
        </p>
      </div>

      <div style="display: flex; flex-direction: column; gap: 10px;">
        <a href="{{ route('quick_admin') }}" class="btn-primary" style="justify-content: center; padding: 12px 20px; font-size: 14px;">
          <i class="fa-solid fa-crown" style="color: #fef3c7;"></i> Switch to Super Admin Account (1-Click) &rarr;
        </a>
        
        <div style="display: flex; gap: 10px; justify-content: center;">
          <a href="{{ route('dashboard') }}" class="btn-secondary" style="flex: 1; justify-content: center; font-size: 13px;">
            <i class="fa-solid fa-table-cells-large"></i> Go to My Portal
          </a>
          <a href="{{ route('logout.get') }}" class="btn-tactical btn-tactical-outline" style="flex: 1; justify-content: center; font-size: 13px; color: #ef4444; border-color: #fecaca;">
            <i class="fa-solid fa-right-from-bracket"></i> Sign Out
          </a>
        </div>
      </div>
    @else
      <p style="font-size: 14px; color: var(--text-muted); line-height: 1.6; margin-bottom: 24px;">
        Please authenticate with an authorized Academy Administrator account to access this page.
      </p>

      <a href="{{ route('quick_admin') }}" class="btn-primary" style="justify-content: center; padding: 12px 20px; font-size: 14px;">
        <i class="fa-solid fa-crown"></i> 1-Click Super Admin Sign In
      </a>
    @endauth

  </div>
</div>
@endsection
