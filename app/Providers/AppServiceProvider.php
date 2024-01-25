<?php

namespace App\Providers;

use App\Filament\Resources\MemberResource\Pages\ViewMember;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        FilamentView::registerRenderHook(
            'panels::page.header.actions.before',
            fn (): string => Blade::render('@livewire(\'change-month\')'),
            scopes: [
                ViewMember::class
            ]
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
