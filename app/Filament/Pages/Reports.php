<?php

namespace App\Filament\Pages;

use App\Exports\MonthlyReportExport;
use App\Exports\YearlyReportExport;
use App\Models\Dividend;
use App\Models\Member;
use App\Tables\Columns\ReportColumn;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Maatwebsite\Excel\Facades\Excel;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class Reports extends Page implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Admin';

    protected static string $view = 'filament.pages.reports';

    public ?string $month, $year, $type;

    public Carbon $start, $end;

    public $members;

    public string $viewPage = 'form';
    private bool $visible = false;


//    protected function getHeaderActions(): array
//    {
//        return [
//            Action::make('excel')
//                ->label('Export')
//                ->action(fn () => $this->exportMonthly())
//                ->color('success')
//                ->icon('heroicon-o-table-cells'),
////                ->visible($this->visible),
//            Action::make('pdf')
//                ->label('Export')
//                ->action(fn () => $this->exportMonthly('pdf'))
//                ->icon('heroicon-o-document')
//                ->color('danger')
////                ->visible($this->visible),
//        ];
//    }

    private function exportMonthly (?string $type = null)
    {
        $period = $this->type;
        if ($period === 'dividend') {
            dd($period);
        }

        if ($period === 'year') {
            $add = (int) $this->start->format('Y');
            if ($type == 'pdf') {
                return Excel::download(new YearlyReportExport($this->start), $add . ' report.pdf', \Maatwebsite\Excel\Excel::MPDF);
            }
            return Excel::download(new YearlyReportExport($this->start), $add . ' report.xlsx');
        }

        if ($period === 'month') {
            if ($type == 'pdf') {
                return Excel::download(new MonthlyReportExport($this->start, $this->end), $this->start->format('F, Y') . ' report.pdf', \Maatwebsite\Excel\Excel::MPDF);
            }
            return Excel::download(new MonthlyReportExport($this->start, $this->end), $this->start->format('F, Y') . ' report.xlsx');
        }

    }

    protected function getCreateFormAction(): array
    {
        return [
            Action::make('create')
                ->label('Generate')
                ->submit('create')
                ->hidden($this->visible),
        ];
    }

    public function getAct(): array
    {
        return $this->getCreateFormAction();
    }

    public function create() {
        $data = $this->form->getState();
        if (!isset($data['month']) && !isset($data['type'])) {
            Notification::make()
                ->title('Please fill in necessary details!')
                ->color('danger')
                ->send();
            return;
        }

        if ($data['type'] === 'month' && !isset($data['month'])) {
            $data['month'] = (int) Carbon::now()->format('n') - 1;
        }

        if ($data['type'] === 'year' && !isset($data['year'])) {
            $data['year'] = (int) Carbon::now()->format('Y');
        }

        if ($data['type'] === 'dividend' && !isset($data['year'])) {
            $data['year'] = (int) Carbon::now()->format('Y') - 1;
        }


        $year = $data['year'];
        $type = $data['type'];

        if (!$year) {
            $year = (int) Carbon::now()->format('Y');
        }

        if ($type === 'month') {
            $this->viewPage = 'table';
            $month = $data['month'];

            $this->start = Carbon::parse($month . '/16/' . $year)->startOfMonth();
            $this->end = Carbon::parse($month . '/16/' . $year)->endOfMonth();
        } elseif ($type === 'year') {
            $this->viewPage = 'table';

            $this->start = Carbon::parse('10/16/' . $year)->startOfYear();
            $this->end = Carbon::parse('10/16/' . $year)->endOfYear();
        } else {
            return $this->redirect('/admin/dividends/' . $year);
        }
    }

    public function table(Table $table): Table
    {
        $int = 0;
        $column = [
            TextColumn::make('name')
                ->searchable(),
            ReportColumn::make('savings')->state(function (Member $record) {
                if ($record->savings) {
                    $savingsC = $record->savings->history->whereBetween('date', [$this->start, $this->end])->sum('credit');
                    $savingsD = $record->savings->history->whereBetween('date', [$this->start, $this->end])->sum('debit');
                    return $savingsC - $savingsD;
                } else {
                    return 0;
                }
            }),
            ReportColumn::make('total savings')->state(function (Member $record) {
                return $record->getSaving();
            }),
            ReportColumn::make('shares')->state(function (Member $record) {
                if ($record->share) {
                    $shareC = $record->share->history->whereBetween('date', [$this->start, $this->end])->sum('credit');
                    $shareD = $record->share->history->whereBetween('date', [$this->start, $this->end])->sum('debit');

                    return $shareC - $shareD;
                } else {
                    return 0;
                }
            }),
            ReportColumn::make('Total Shares')->state(function (Member $record) {
                return $record->getShare();
            }),
            ReportColumn::make('Approved Loan')->state(function (Member $record) {
                $st = $record->where('id', $record->id)->first();
                return $st->appLoan;
            }),
            ReportColumn::make('Loan Repayment')->state(function (Member $record) use ($int) {
                if (!$record->loans->isEmpty()) {
                    $cre = 0;
                    $int = 0;
                    $appLoan = $record->loans->whereBetween('approved_on', [$this->start, $this->end])->sum('amount');
                    if ($appLoan) {
                        $appLoan = $appLoan * -1;
                    } else {
                        $appLoan = 0;
                    }
                    $loan = $record->loans;
                    if ($loan->count() > 0) {
                        foreach ($loan as $item) {
                            $cre = $cre + $item->repayments->whereBetween('date', [$this->start, $this->end])->sum('credit');
                            $int = $int + $item->repayments->whereBetween('date', [$this->start, $this->end])->sum('interest');
                        }
                    }

                    return $cre;
                } else {
                    return 0;
                }
            }),
            ReportColumn::make('Loan Balance')->state(function (Member $record) {
                return $record->getLoan();
            }),
            ReportColumn::make('Loan Interest')->state(function (Member $record) use ($int) {
                return $int;
            }),
            ReportColumn::make('Utility')->state(function (Member $record) {
                if (!$record->utilities->isEmpty()) {
                    return $record->utilities->whereBetween('date', [$this->start, $this->end])->sum('amount');
                } else {
                    return 0;
                }
            }),
            ReportColumn::make('Fine')->state(function (Member $record) {
                if (!$record->fines->isEmpty()) {
                    return $record->fines->whereBetween('date', [$this->start, $this->end])->sum('credit');
                } else {
                    return 0;
                }
            }),
            ReportColumn::make('Building')->state(function (Member $record) {
                if ($record->building) {
                    $buildingC = $record->building->history->whereBetween('date', [$this->start, $this->end])->sum('credit');
                    $buildingD = $record->building->history->whereBetween('date', [$this->start, $this->end])->sum('debit');

                    return $buildingC - $buildingD;
                } else {
                    return 0;
                }
            }),
        ];


        return $table
            ->defaultPaginationPageOption(25)
            ->query(Member::query()->orderBy('member_id'))
            ->headerActions([
                \Filament\Tables\Actions\Action::make('excel')
                    ->label('Export')
                    ->action(fn () => $this->exportMonthly())
                    ->color('success')
                    ->icon('heroicon-o-table-cells'),
                \Filament\Tables\Actions\Action::make('pdf')
                    ->label('Export')
                    ->action(fn () => $this->exportMonthly('pdf'))
                    ->icon('heroicon-o-document')
                    ->color('danger')
            ])
            ->columns($column)
            ->filters([
                // ...
            ])
            ->actions([
                // ...
            ])
            ->bulkActions([
                ExportBulkAction::make()
//                    ->withFilename('segun')
            ]);
    }

    public function form(Form $form): Form
    {
        $years = [];
        $year = (int) Carbon::now()->format('Y');
        for ($i = $year; $i >= 2019; $i--) {
            $years[$i] = $i;
        }

        return $form
                ->schema([
                    Group::make()
                        ->schema([
                            Section::make('Monthly Report')
                                ->schema([
                                    Select::make('type')
                                        ->options([
                                            'month' => 'Monthly',
                                            'year' => 'Yearly',
                                            'dividend' => 'Dividend'
                                        ])
                                        ->default('month')
                                        ->live()
                                        ->native(false),
                                    Select::make('month')
                                        ->options([
                                            '1' => 'January',
                                            '2' => 'February',
                                            '3' => 'March',
                                            '4' => 'April',
                                            '5' => 'May',
                                            '6' => 'June',
                                            '7' => 'July',
                                            '8' => 'August',
                                            '9' => 'September',
                                            '10' => 'October',
                                            '11' => 'November',
                                            '12' => 'December',
                                        ])
                                        ->native(false)
                                        ->searchable()
                                        ->visible(fn (Get $get) => $get('type') === 'month'),
                                    Select::make('year')
                                        ->options(function (Get $get) use ($years) {
                                            if ($get('type') === 'dividend') {
                                                return Dividend::orderBy('year', 'desc')->pluck('year', 'id')->toArray();
                                            } else {
                                                return $years;
                                            }
                                        })
                                        ->native(false)
                                        ->visible(fn (Get $get) => $get('type') !== null),
                                ])
                        ])
                        ->columnSpan(2)
                ])->columns(3);
    }
}
