<?php

namespace App\Filament\Pages;

use App\Exports\MonthlyReportExport;
use App\Models\Member;
use App\Tables\Columns\ReportColumn;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as Bully;
use Illuminate\Support\Facades\Storage;
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

    public $start, $end;

    public $members;

    public string $viewPage = 'form';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('excel')
                ->label('Export')
                ->action(fn () => $this->exportMonthly())
                ->color('success')
                ->icon('heroicon-o-table-cells'),
            Action::make('pdf')
                ->label('Export')
                ->action(fn () => $this->exportMonthly('pdf'))
                ->icon('heroicon-o-document')
                ->color('danger')
        ];
    }

    private function exportMonthly (?string $type = null)
    {
        if ($type == 'pdf') {
            return Excel::download(new MonthlyReportExport($this->start, $this->end), 'report.pdf', \Maatwebsite\Excel\Excel::MPDF);
        }
        return Excel::download(new MonthlyReportExport($this->start, $this->end), 'report.xlsx');
    }

    protected function getCreateFormAction(): Action
    {
        return Action::make('create')
            ->label(__('filament-panels::resources/pages/create-record.form.actions.create.label'))
            ->submit('create')
            ->keyBindings(['mod+s']);
    }

    public function getAct(): array
    {
        return [$this->getCreateFormAction()];
    }

    public function create() {
        $data = $this->form->getState();
        $month = $data['month'];
        $year = $data['year'];
        $type = $data['type'];

        if (!$year) {
            $year = (int) Carbon::now()->format('Y');
        }

        if (!$type) {
            $type = 'monthly';
        }
        $this->viewPage = 'table';

        $this->start = Carbon::parse($month . '/16/' . $year)->startOfMonth();
        $this->end = Carbon::parse($month . '/16/' . $year)->endOfMonth();
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
                    Section::make()
                        ->schema([
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
                                ->required()
                                ->searchable(),
                            Select::make('year')
                                ->options($years)
                                ->native(false),
                            Select::make('type')
                                ->options([
                                    'month' => 'Monthly',
                                    'year' => 'Yearly'
                                ])
                                ->default('month')
                                ->native(false)
                        ])->columns(3)
//                        ->statePath('data')
                ]);
    }
}
