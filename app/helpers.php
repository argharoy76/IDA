<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use App\Models\CmsSetting;

if (!function_exists('cms_clear_cache')) {
    function cms_clear_cache() {
        Cache::forget('cms_settings_map');
        cms('', null, true);
    }
}

if (!function_exists('cms')) {
    function cms(string $key, $default = null, bool $fresh = false) {
        static $cached = null;
        if ($fresh) {
            $cached = null;
            return null;
        }
        if ($cached === null) {
            try {
                if (class_exists(CmsSetting::class) && Schema::hasTable('cms_settings')) {
                    $cached = Cache::remember('cms_settings_map', 3600, function () {
                        return CmsSetting::pluck('value', 'key')->toArray();
                    });
                } else {
                    $cached = [];
                }
            } catch (\Throwable $e) {
                $cached = [];
            }
        }
        // If explicitly configured in cms_settings table, respect saved value (including empty/null for removal)
        if (array_key_exists($key, $cached)) {
            return $cached[$key] ?? '';
        }

        // Only fall back to default if key was never configured in database
        return $default;
    }
}

if (!function_exists('cms_hero_slides')) {
    function cms_hero_slides(): array {
        $defaultSlides = [
            'https://images.unsplash.com/photo-1541872703-74c5e44368f9?q=80&w=1600&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1508614589041-895b88991e3e?q=80&w=1600&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1519074069444-1ba4ea16e901?q=80&w=1600&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1526778548025-fa2f459cd5c1?q=80&w=1600&auto=format&fit=crop',
            'https://images.unsplash.com/photo-1579952363873-27f3bade9f55?q=80&w=1600&auto=format&fit=crop',
        ];

        $raw = cms('hero_slides');
        if ($raw) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded) && !empty($decoded)) {
                return array_values($decoded);
            }
        }

        // Backward compatibility if single hero_bg_image was set
        $legacySingle = cms('hero_bg_image');
        if ($legacySingle && file_exists(public_path($legacySingle))) {
            return [$legacySingle];
        }

        return $defaultSlides;
    }
}