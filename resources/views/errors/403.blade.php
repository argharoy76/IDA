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

      <div style="background: #fef2f2; border: 1px solid #fee2e2; border-radius: var(--radius-sm); padding: 16px; margin-bottom: 24px; text-align: left;">
        <span style="font-size: 11.5px; font-weight: 800; color: #991b1b; text-transform: uppercase; display: block; margin-bottom: 6px;">
          <i class="fa-solid fa-shield-halved"></i> Access Restricted:
        </span>
        <p style="font-size: 12.5px; color: #7f1d1d; margin: 0;">
          Your current security clearance level does not permit access to this section. If you require higher administrative privileges, please contact the Academy Command Authority.
        </p>
      </div>

      <div style="display: flex; gap: 10px; justify-content: center;">
        <a href="{{ route('dashboard') }}" class="btn-primary" style="flex: 1; justify-content: center; font-size: 13px;">
          <i class="fa-solid fa-table-cells-large"></i> Go to My Portal
        </a>
        <a href="{{ route('logout.get') }}" class="btn-tactical btn-tactical-outline" style="flex: 1; justify-content: center; font-size: 13px; color: #ef4444; border-color: #fecaca;">
          <i class="fa-solid fa-right-from-bracket"></i> Sign Out
        </a>
      </div>
    @else
      <p style="font-size: 14px; color: var(--text-muted); line-height: 1.6; margin-bottom: 24px;">
        Please authenticate with an authorized Academy Administrator account to access this command page.
      </p>

      <div style="display: flex; gap: 10px; justify-content: center;">
        <a href="{{ route('admin.login') }}" class="btn-primary" style="flex: 1; justify-content: center; font-size: 13.5px; padding: 12px 20px;">
          <i class="fa-solid fa-lock"></i> Officer Command Sign In
        </a>
        <a href="{{ route('home') }}" class="btn-secondary" style="flex: 1; justify-content: center; font-size: 13.5px; padding: 12px 20px;">
          <i class="fa-solid fa-house"></i> Public Homepage
        </a>
      </div>
    @endauth

  </div>
</div>
@endsection
