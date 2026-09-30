@php
  $size = $size ?? 46;
  $customLogo = cms('site_logo') ?: cms('site_crest');
  $resolvedLogo = null;

  if ($customLogo && (str_starts_with($customLogo, 'http') || file_exists(public_path(ltrim($customLogo, '/'))))) {
      $resolvedLogo = str_starts_with($customLogo, 'http') ? $customLogo : asset(ltrim($customLogo, '/'));
  } elseif (file_exists(public_path('images/logo.png'))) {
      $resolvedLogo = asset('images/logo.png');
  } elseif (file_exists(public_path('images/logo.jpeg'))) {
      $resolvedLogo = asset('images/logo.jpeg');
  } elseif (file_exists(public_path('logo.png'))) {
      $resolvedLogo = asset('logo.png');
  }
@endphp

@if($resolvedLogo)
  <div class="brand-crest-wrap brand-crest-3d-wrap" style="height: {{ $size }}px; display: inline-flex; align-items: center; justify-content: center;">
    <img src="{{ $resolvedLogo }}" alt="{{ cms('site_name', 'Imperial Defence Academy') }}" style="max-height: {{ $size }}px; width: auto; max-width: 100%; object-fit: contain; filter: drop-shadow(0 4px 12px rgba(0,0,0,0.5));">
  </div>
@endif
