<?php

namespace App\Filament\Pages;

use App\Models\Member;
use App\Models\Setting;
use App\Tables\Columns\markAttendance;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;

class Attendance extends Page implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Functions';

    protected static ?int $navigationSort = 2;

    public $attendance, $current = [];

    #[Url(as: 'year', keep: true)]
    public int $currentYear = 0;

    protected function getHeaderActions(): array
    {
        $years = [];
        $year = (int) Carbon::now()->format('Y');
        if ($this->currentYear == $year) {
            $year--;
        }
        for ($y = $year; $y >= 2019; $y--) {
            $years[$y] = $y;
        }
        return [
            Action::make('Other Years')
                ->color('primary')
                ->form([
                    Select::make('year')
                        ->label('Choose Year')
                        ->options($years)
                        ->native('false')
                        ->required()
                ])
                ->action(fn (array $data) => $this->redirect('/admin/attendance?year=' . $data['year']))
        ];
    }

    public function mount(): void {
        if (!$this->currentYear) {
            $this->currentYear = (int) Carbon::now()->format('Y');
            $month = (int) Carbon::now()->format('n') + 1;
        } else {
            $month = 13;
        }

        for ($i = 1; $i < $month; $i++) {
            $att = \App\Models\Attendance::where('year', $this->currentYear)->where('month', $i)->first();
            if (!$att) {
                \App\Models\Attendance::create([
                    'year' => $this->currentYear,
                    'month' => $i,
                    'date' => Carbon::parse("$i/14/$this->currentYear")->format('Y-m-d'),
                ]);
            }
        }
        $this->attendance = \App\Models\Attendance::where('year', $this->currentYear)->orderBy('month', 'asc')->get();

    }

    public function mark(\App\Models\Attendance $attendance, Member $member, bool $state) {
        if ($attendance->members->contains('id', $member->id)) {
            $attendance->members()->detach($member);
        } else {
            $attendance->members()->attach($member);
        }
    }

    public function table(Table $table): Table
    {
        $column = [
            TextColumn::make('#')
                ->rowIndex(),
            TextColumn::make('name')
                ->searchable(),
        ];
        foreach ($this->attendance->sortBy('month') as $item) {
            $n = Carbon::parse("$item->month/12/2021")->format('M');
            $this->current[$n] = $item;

            $column[] = markAttendance::make($n)->state($n);
        }

        return $table
            ->defaultPaginationPageOption(25)
            ->query(Member::query())
            ->columns($column)
            ->filters([
                // ...
            ])
            ->actions([
                // ...
            ])
            ->bulkActions([
                // ...
            ]);
    }


    protected static string $view = 'filament.pages.attendance';

    private function changeYear(int $year): void
    {
        $this->currentYear = $year;
        $this->mount($year);
//        $this->table->resetTable();
    }

    protected function queryString(): array
    {
        return [
            'currentYear' => [
                'as' => 'year',
            ],
        ];
    }

    protected static ?string $slug = 'attendance';
}
