<?php

namespace App\Filament\Resources\UtilityResource\Pages;

use App\Filament\Resources\UtilityResource;
use App\Models\Fine;
use App\Models\Member;
use App\Models\Setting;
use App\Models\Utility;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateUtility extends CreateRecord
{
    protected static string $resource = UtilityResource::class;

    protected function handleRecordCreation(array $data): Utility
    {
        $member = Member::find($data['member_id']);
        $data['type'] = $data['name'];

        return Utility::find($member->buyUtil($data));
    }
}
