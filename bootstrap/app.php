<?php

use App\Http\Middleware\AdminPermissionMiddleware;
use App\Http\Middleware\RedirectAdmin;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SuperAdminMiddleware;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('subscriptions:send-reminders')->daily();
        $schedule->command('payment:backfill-audit-rows')->everyFiveMinutes();
        $schedule->command('payment:pending-reminders')->everyMinute();
        $schedule->command('payment:reconcile-ccavenue')
            ->everyThirtySeconds()
            ->withoutOverlapping(1);
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'redirect.admin' => RedirectAdmin::class,
            'admin.permission' => AdminPermissionMiddleware::class,
            'admin.super' => SuperAdminMiddleware::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            '/ccavenue/callback',
            'ccavenue/callback',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
