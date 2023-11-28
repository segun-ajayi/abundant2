<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ReportsPage extends Page
{
//    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.reports-page';
    public $members;

    public function mount($members): void {
        dd($members);
    }
}
