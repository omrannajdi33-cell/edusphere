<?php

namespace App\Providers;

use App\Modules\CompetencyModuleRegistry;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CompetencyModuleRegistry::class);
    }

    public function boot(): void
    {
        Carbon::setLocale('fr');
    }
}
