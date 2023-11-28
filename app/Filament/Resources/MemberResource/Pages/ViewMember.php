<?php

namespace App\Filament\Resources\MemberResource\Pages;

use App\Filament\Resources\MemberResource;
use App\Models\Member;
use App\Models\Setting;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Str;

class ViewMember extends ViewRecord
{
    protected static string $resource = MemberResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            MemberResource\Widgets\SharesOverview::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return [
            'md' => 2,
            'xl' => 4,
        ];
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
        $currentMonth = Carbon::parse($setting->pDate)->format('F Y');
        $id = $this->record->member_id;
        $prev = Member::where('member_id', '<', $id)
            ->orderBy('member_id', 'desc')->limit(1)
            ->get();
        $next = Member::where('member_id', '>', $id)
            ->orderBy('member_id', 'asc')->take(1)
            ->get();

        if (!isset($prev[0])) {
            $ret = [
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
                            ->default(Carbon::now()->format('m'))
                            ->required()
                            ->native(false),
                        Select::make('year')
                            ->label('Select Year')
                            ->default(Carbon::now()->format('Y'))
                            ->options($years)
                            ->required()
                            ->native(false),
                    ])->action(fn ($data) => $setting->setMonth($data)),

                Action::make('next')
                    ->color('success')
                    ->hidden(fn () => $next->isEmpty())
                    ->url('/admin/members/' . $next[0]?->id),

                Action::make('search member')
                    ->color('info')
                    ->form([
                        Select::make('search')
                            ->options(Member::all()->pluck('name', 'id')->toArray())
                            ->searchable()
                            ->native(false)
                    ])
                    ->action(function ($data) {
                        $this->redirect('/admin/members/' . $data['search']);
                    }),

//            Action::make('work')
//                ->color('success')
//                ->action(fn () => $this->record->workSavings()),
            ];
        } elseif (!isset($next[0])) {
            $ret = [
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
                            ->default(Carbon::now()->format('m'))
                            ->required()
                            ->native(false),
                        Select::make('year')
                            ->label('Select Year')
                            ->default(Carbon::now()->format('Y'))
                            ->options($years)
                            ->required()
                            ->native(false),
                    ])->action(fn ($data) => $setting->setMonth($data)),

                Action::make('previous')
                    ->color('danger')
                    ->hidden(fn () => $prev->isEmpty())
                    ->url('/admin/members/' . $prev[0]->id),

                Action::make('search member')
                    ->color('info')
                    ->form([
                        Select::make('search')
                            ->options(Member::all()->pluck('name', 'id')->toArray())
                            ->searchable()
                            ->native(false)
                    ])
                    ->action(function ($data) {
                        $this->redirect('/admin/members/' . $data['search']);
                    }),

//            Action::make('work')
//                ->color('success')
//                ->action(fn () => $this->record->workSavings()),
            ];
        } else {
            $ret = [
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
                            ->default(Carbon::now()->format('m'))
                            ->required()
                            ->native(false),
                        Select::make('year')
                            ->label('Select Year')
                            ->default(Carbon::now()->format('Y'))
                            ->options($years)
                            ->required()
                            ->native(false),
                    ])->action(fn ($data) => $setting->setMonth($data)),

                Action::make('previous')
                    ->color('danger')
                    ->hidden(fn () => $prev->isEmpty())
                    ->url('/admin/members/' . $prev[0]->id),

                Action::make('next')
                    ->color('success')
                    ->hidden(fn () => $next->isEmpty())
                    ->url('/admin/members/' . $next[0]?->id),

                Action::make('search member')
                    ->color('info')
                    ->form([
                        Select::make('search')
                            ->options(Member::all()->pluck('name', 'id')->toArray())
                            ->searchable()
                            ->native(false)
                    ])
                    ->action(function ($data) {
                        $this->redirect('/admin/members/' . $data['search']);
                    }),

//            Action::make('work')
//                ->color('success')
//                ->action(fn () => $this->record->workSavings()),
            ];
        }
        return $ret;
    }
}
