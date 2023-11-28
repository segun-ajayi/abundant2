<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            Actions\CreateAction::make(),
            Actions\Action::make('New User')
                ->label('New Exco')
                ->url(fn () => UserResource::getUrl('register'))
        ];
    }
}
