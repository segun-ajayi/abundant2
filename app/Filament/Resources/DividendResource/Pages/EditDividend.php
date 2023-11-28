<?php

namespace App\Filament\Resources\DividendResource\Pages;

use App\Filament\Resources\DividendResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDividend extends EditRecord
{
    protected static string $resource = DividendResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
