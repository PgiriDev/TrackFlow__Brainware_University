<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Services\CurrencyService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register CurrencyService as a singleton
        $this->app->singleton(CurrencyService::class, function ($app) {
            return new CurrencyService();
        });

        // Register user settings as a singleton (cached per request)
        $this->app->singleton('user.settings', function ($app) {
            $userId = session('user_id');
            if (!$userId)
                return null;

            return cache()->remember("user_settings:{$userId}", 300, function () use ($userId) {
                return DB::table('user_settings')->where('user_id', $userId)->first();
            });
        });

        // Register user preferences as a singleton (cached per request)
        $this->app->singleton('user.preferences', function ($app) {
            $userId = session('user_id');
            if (!$userId)
                return null;

            return cache()->remember("user_preferences:{$userId}", 300, function () use ($userId) {
                return DB::table('user_preferences')->where('user_id', $userId)->first();
            });
        });

        // Register categories cache per user
        $this->app->singleton('user.categories', function ($app) {
            $userId = session('user_id');
            if (!$userId)
                return collect();

            return cache()->remember("user_categories:{$userId}", 300, function () use ($userId) {
                return \App\Models\Category::where('user_id', $userId)
                    ->orWhereNull('user_id')
                    ->get()
                    ->keyBy('id');
            });
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Prevent lazy loading in development to catch N+1 issues
        // Model::preventLazyLoading(!$this->app->isProduction());

        // Use strict mode in development
        if (!$this->app->isProduction()) {
            // Log slow queries in development
            DB::listen(function ($query) {
                if ($query->time > 100) {
                    \Log::warning('Slow Query: ' . $query->sql, [
                        'bindings' => $query->bindings,
                        'time' => $query->time . 'ms'
                    ]);
                }
            });
        }
    }
}
