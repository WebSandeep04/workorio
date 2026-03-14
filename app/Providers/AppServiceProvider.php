<?php

namespace App\Providers;

use App\Models\SalesRecord;
use App\Observers\SalesRecordObserver;
use App\View\Components\FormBuilderEmbed;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

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
        SalesRecord::observe(SalesRecordObserver::class);
        \App\Models\Calling::observe(\App\Observers\CallingObserver::class);
        Blade::component('form-builder', FormBuilderEmbed::class);
    }
}
