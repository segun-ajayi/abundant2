<?php

namespace App\Filament\Resources\UtilityResource\Pages;

use App\Filament\Resources\UtilityResource;
use App\Models\Utility;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Tables\Actions\Action;

class EditUtility extends EditRecord
{
    protected static string $resource = UtilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('reverse')
                ->action(fn(Utility $record) => $record->del($record->member, $record)),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterSave(): void
    {
        if (count($this->record->getChanges()) && array_key_exists('mode', $this->record->getChanges())) {
            if ($this->record->getChanges()['mode'] == 'savings') {
                $this->record->member->debitSavings($this->record->amount, 'Utility');
            }
        }
    }
}
