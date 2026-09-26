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
        // Enforce HTTPS when deployed in production or behind SSL terminating proxies (Hostinger / Cloudflare)
        // Never force HTTPS on localhost or 127.0.0.1 to avoid cross-scheme origin mismatch (419 Page Expired)
        $host = request()->getHost();
        $isLocalhost = in_array($host, ['localhost', '127.0.0.1', '::1']) || app()->environment('local', 'testing');

        if (!$isLocalhost && (
            config('app.env') === 'production' || 
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') || 
            (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] == 1))
        )) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Safe Auto-Healing for Hostinger Production Database (Zero Data Loss)
        // Automatically adds target_track and target_tracks columns if missing on production deployment
        try {
            if (Schema::hasTable('exams') && !Schema::hasColumn('exams', 'target_track')) {
                Schema::table('exams', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->string('target_track', 50)->nullable()->index()->after('branch');
                });
            }
            if (Schema::hasTable('students') && !Schema::hasColumn('students', 'target_tracks')) {
                Schema::table('students', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->string('target_tracks', 150)->nullable()->after('target_wing');
                });
            }
        } catch (\Throwable $e) {
            // Silently pass if DDL permissions restricted or table locked
        }

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
