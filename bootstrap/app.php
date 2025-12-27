<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        \App\Providers\AuthServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            \Illuminate\Support\Facades\Route::middleware('web')
                ->group(base_path('routes/academic.php'));
            \Illuminate\Support\Facades\Route::middleware('web')
                ->group(base_path('routes/fyp.php'));
            \Illuminate\Support\Facades\Route::middleware('web')
                ->group(base_path('routes/ip.php'));
            \Illuminate\Support\Facades\Route::middleware('web')
                ->group(base_path('routes/osh.php'));
            \Illuminate\Support\Facades\Route::middleware('web')
                ->group(base_path('routes/li.php'));
            \Illuminate\Support\Facades\Route::middleware('web')
                ->group(base_path('routes/industry.php'));
            \Illuminate\Support\Facades\Route::middleware('web')
                ->group(base_path('routes/admin.php'));
            \Illuminate\Support\Facades\Route::middleware('web')
                ->group(base_path('routes/coordinator.php'));
            \Illuminate\Support\Facades\Route::middleware('web')
                ->group(base_path('routes/placement.php'));
            \Illuminate\Support\Facades\Route::middleware('web')
                ->group(base_path('routes/resume-inspection.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'role' => \App\Http\Middleware\EnsureRole::class,
            'active.role' => \App\Http\Middleware\CheckActiveRole::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        // Update expired agreements daily at midnight
        $schedule->command('agreements:update-expired')->daily();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
