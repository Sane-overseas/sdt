<?php

namespace App\Providers;

use App\Services\AcademicSessionService;
use App\Services\StateService;
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
        View::composer('*', function ($view) {
            $view->with('currentAcademicSession', AcademicSessionService::current());
            $view->with('activeAcademicSession', AcademicSessionService::active());
            $view->with('allAcademicSessions', AcademicSessionService::all());
            $view->with('isReadOnlySessionView', AcademicSessionService::isArchiveView());
            $view->with('currentState', StateService::current());
            $view->with('allStates', StateService::all());
        });
    }
}
