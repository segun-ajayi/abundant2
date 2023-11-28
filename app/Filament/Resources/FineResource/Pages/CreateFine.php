<?php

namespace App\Filament\Resources\FineResource\Pages;

use App\Filament\Resources\FineResource;
use App\Models\Fine;
use App\Models\Member;
use App\Models\Setting;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateFine extends CreateRecord
{
    protected static string $resource = FineResource::class;

    protected function handleRecordCreation(array $data): Fine
    {
        $member = Member::find($data['member']);
        $data2 = [
            'amount' => $data['amount'],
            'mode' => $data['mode'],
            'reason' => $data['reason'],
            'date' => Setting::find(1)->pDate,
            'entered_by' => Auth::id(),
        ];

        $id = $member->postFine($data2);

        return Fine::find($id);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
