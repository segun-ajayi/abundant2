<?php

namespace App\Filament\Resources\MemberResource\Pages;

use App\Filament\Resources\MemberResource;
use App\Models\Member;
use App\Models\Setting;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\Page;
use Filament\Support\Enums\Alignment;

class ManageMember extends Page
{
    protected static string $resource = MemberResource::class;

    protected static string $view = 'filament.resources.member-resource.pages.manage-member';

    public $member;
    public function mount(Member $record, $list): void {
        dd($list);
        $this->member = $record;
    }

    protected function getHeaderActions(): array
    {
        $years = [];
        for ($year = Carbon::now()->format('Y'); $year >= 2019; $year--) {
            $years[$year] = $year;
        }
        $setting = Setting::firstOrCreate(['id' => 1], [
            'pDate' => carbon::now()->format('Y-m-d')
        ]);
        $currentMonth = Carbon::parse($setting->pDate)->monthName . ', ' . Carbon::parse($setting->pDate)->format('Y');
        $id = $this->member->member_id;
        $prev = Member::where('member_id', '<', $id)
            ->orderBy('member_id', 'desc')->limit(1)
            ->get();
        $next = Member::where('member_id', '>', $id)
            ->orderBy('member_id', 'asc')->take(1)
            ->get();
//        dd($prev->isEmpty());
        return [
            Action::make('Posting for: ' . $currentMonth)
                ->color('info')
                ->form([
                    Select::make('month')
                        ->label('Choose Month')
                        ->options([
                            "1" => "January",
                            "2" => "February",
                            "3" => "March",
                            "4" => "April",
                            "5" => "May",
                            "6" => "June",
                            "7" => "July",
                            "8" => "August",
                            "9" => "September",
                            "10" => "October",
                            "11" => "November",
                            "12" => "December",
                        ])
                        ->required()
                        ->native(false),
                    Select::make('year')
                        ->label('Select Year')
                        ->options($years)
                        ->required()
                        ->native(false),
                ])->action(fn ($data) => $setting->setMonth($data)),

            Action::make('previous')
                ->color('danger')
                ->hidden(fn () => $prev->isEmpty())
                ->action(fn () => $this->member = $prev[0]),

            Action::make('next')
                ->color('success')
                ->hidden(fn () => $next->isEmpty())
                ->action(fn () => $this->member = $next[0]),
        ];
    }
}
