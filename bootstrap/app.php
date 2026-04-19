<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule) {
        // Send payment reminders daily at 9 AM
        $schedule->command('notifications:payment-reminders --days=3')
            ->dailyAt('09:00')
            ->withoutOverlapping()
            ->runInBackground();

        // Send weekly summary every Monday at 8 AM
        $schedule->command('notifications:weekly-summary')
            ->weeklyOn(1, '08:00')
            ->withoutOverlapping()
            ->runInBackground();

        // Sync bank accounts every 6 hours
        $schedule->command('sync:accounts')
            ->everySixHours()
            ->withoutOverlapping()
            ->runInBackground();

        // Clean up expired remember tokens daily at 3 AM
        $schedule->call(function () {
            \App\Models\RememberToken::cleanupExpired();
        })->dailyAt('03:00')
            ->name('remember-tokens-cleanup');
    })
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\RememberMeAutoLogin::class, // Must be before TrackUserSession
            \App\Http\Middleware\TrackUserSession::class,
            \App\Http\Middleware\PreventBackHistory::class,
        ]);

        // Register authentication middleware alias
        $middleware->alias([
            'auth.session' => \App\Http\Middleware\EnsureAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
