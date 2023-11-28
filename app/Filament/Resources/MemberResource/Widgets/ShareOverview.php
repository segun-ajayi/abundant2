<?php

namespace App\Filament\Resources\MemberResource\Widgets;

use App\Models\Member;
use Filament\Widgets\Widget;

class ShareOverview extends Widget
{
    protected static string $view = 'filament.resources.member-resource.widgets.share-overview';

    public Member $record;

    public function mount() {
//        dd($this->record);
    }
}
