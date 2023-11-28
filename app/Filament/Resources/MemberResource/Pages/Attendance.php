<?php

namespace App\Filament\Resources\MemberResource\Pages;

use App\Filament\Resources\MemberResource;
use Filament\Resources\Pages\Page;

class Attendance extends Page
{
    protected static string $resource = MemberResource::class;


    protected static string $view = 'filament.resources.member-resource.pages.attendance';
}
