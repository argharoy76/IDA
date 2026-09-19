@php
  $currentRoute = request()->route()->getName();
  
  $liveLinks = [
    'admin.cms.home' => route('home'),
    'admin.cms.courses' => route('courses'),
    'admin.cms.online_tests' => route('online_tests'),
    'admin.cms.about' => route('about'),
    'admin.cms.classes' => route('classes'),
    'admin.cms.gallery' => route('gallery'),
    'admin.cms.notices' => route('notices'),
    'admin.cms.contact' => route('contact'),
    'admin.cms.branding' => route('home'),
    'admin.cms.inquiries' => route('contact'),
    'admin.cms.index' => route('home'),
  ];
  
  $currentLiveUrl = $liveLinks[$currentRoute] ?? route('home');

  $navItems = [
    ['route' => 'admin.cms.index', 'icon' => 'fa-solid fa-gauge-high', 'label' => 'Overview Hub'],
    ['route' => 'admin.cms.home', 'icon' => 'fa-solid fa-house', 'label' => 'Home Page'],
    ['route' => 'admin.cms.about', 'icon' => 'fa-solid fa-landmark', 'label' => 'About Page'],
    ['route' => 'admin.cms.courses', 'icon' => 'fa-solid fa-book-bookmark', 'label' => 'Courses Page'],
    ['route' => 'admin.cms.classes', 'icon' => 'fa-regular fa-calendar-days', 'label' => 'Classes & Routines'],
    ['route' => 'admin.cms.online_tests', 'icon' => 'fa-solid fa-crosshairs', 'label' => 'Online Tests Page'],
    ['route' => 'admin.cms.gallery', 'icon' => 'fa-solid fa-images', 'label' => 'Gallery Page'],
    ['route' => 'admin.cms.notices', 'icon' => 'fa-solid fa-bullhorn', 'label' => 'Notices Page'],
    ['route' => 'admin.cms.contact', 'icon' => 'fa-solid fa-address-book', 'label' => 'Contact & Footer'],
    ['route' => 'admin.cms.branding', 'icon' => 'fa-solid fa-palette', 'label' => 'Branding & Colors'],
    ['route' => 'admin.cms.inquiries', 'icon' => 'fa-solid fa-envelope-open-text', 'label' => 'Admission Inquiries'],
  ];
@endphp

<div class="web-management-nav-bar" style="background: #11141d; border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 14px; padding: 10px 14px; margin-bottom: 22px; box-shadow: 0 4px 20px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
  <!-- Horizontal Page Switcher Tabs -->
  <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap; flex: 1; min-width: 0;">
    @foreach($navItems as $item)
      @php
        $isActive = request()->routeIs($item['route']);
      @endphp
      <a href="{{ route($item['route']) }}" 
         class="cms-nav-pill {{ $isActive ? 'active' : '' }}" 
         style="font-size: 12px; font-weight: {{ $isActive ? '700' : '500' }}; padding: 7px 13px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s ease; {{ $isActive ? 'background: rgba(16, 185, 129, 0.18); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.4); box-shadow: 0 0 12px rgba(16, 185, 129, 0.15);' : 'background: rgba(255, 255, 255, 0.04); color: #cbd5e1; border: 1px solid rgba(255, 255, 255, 0.08);' }}"
         onmouseover="if(!this.classList.contains('active')){this.style.background='rgba(255,255,255,0.09)'; this.style.color='#ffffff'; this.style.borderColor='rgba(255,255,255,0.18)';}"
         onmouseout="if(!this.classList.contains('active')){this.style.background='rgba(255,255,255,0.04)'; this.style.color='#cbd5e1'; this.style.borderColor='rgba(255,255,255,0.08)';}">
        <i class="{{ $item['icon'] }}" style="{{ $isActive ? 'color: #34d399;' : 'color: #94a3b8;' }}"></i>
        <span>{{ $item['label'] }}</span>
      </a>
    @endforeach
  </div>

  <!-- Direct Live Preview Shortcut -->
  <div style="flex-shrink: 0;">
    <a href="{{ $currentLiveUrl }}" target="_blank" 
       style="font-size: 12px; font-weight: 600; padding: 7px 14px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; background: rgba(217, 119, 6, 0.12); color: #fbbf24; border: 1px solid rgba(217, 119, 6, 0.35); transition: all 0.2s ease;"
       onmouseover="this.style.background='rgba(217, 119, 6, 0.22)'; this.style.borderColor='rgba(217, 119, 6, 0.6)';"
       onmouseout="this.style.background='rgba(217, 119, 6, 0.12)'; this.style.borderColor='rgba(217, 119, 6, 0.35)';">
      <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 11px;"></i> Preview Live Page
    </a>
  </div>
</div>