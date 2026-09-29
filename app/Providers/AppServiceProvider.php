<?php

namespace App\Providers;

use App\Services\AcademicSessionService;
use App\Services\StateService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        require_once app_path('Helpers/media.php');
    }

    public function boot(): void
    {
        Paginator::useBootstrapFour();

        // utf8mb4: 255 chars * 4 bytes = 1020 > MySQL's 1000-byte index limit on this host
        Schema::defaultStringLength(191);

        View::composer('*', function ($view) {
            $view->with('currentAcademicSession', AcademicSessionService::current());
            $view->with('activeAcademicSession', AcademicSessionService::active());
            $view->with('allAcademicSessions', AcademicSessionService::all());
            $view->with('isReadOnlySessionView', AcademicSessionService::isArchiveView());
            $view->with('currentState', StateService::current());
            $view->with('allStates', StateService::all());

            if (\Illuminate\Support\Facades\Auth::check() && (int) \Illuminate\Support\Facades\Auth::user()->role === 1) {
                $view->with('pendingClaimNotifications', \App\Services\ClaimNotificationService::getPendingClaimNotifications());
                $view->with('pendingClaimsCount', \App\Services\ClaimNotificationService::getPendingClaimsCount());
            }
        });
    }
}
