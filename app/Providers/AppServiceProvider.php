<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use App\Models\CmsSetting;

if (!function_exists('cms')) {
    function cms(string $key, $default = null) {
        static $cached = null;
        if ($cached === null) {
            try {
                if (Schema::hasTable('cms_settings')) {
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
        if (array_key_exists($key, $cached)) {
            return $cached[$key] ?? '';
        }
        return $default;
    }
}

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share CMS settings across all views
        View::composer('*', function ($view) {
            try {
                if (Schema::hasTable('cms_settings')) {
                    $cms = Cache::remember('cms_settings_map', 3600, function () {
                        return CmsSetting::pluck('value', 'key')->toArray();
                    });
                    $view->with('cms', $cms);
                } else {
                    $view->with('cms', []);
                }
            } catch (\Throwable $e) {
                $view->with('cms', []);
            }
        });
    }
}
