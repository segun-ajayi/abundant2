<?php

namespace App\Filament\Widgets;


use App\Models\Loan;
use App\Models\SavingHistory;
use App\Models\ShareHistory;
use Filament\Widgets\ChartWidget;

class CapitalLoanChart extends ChartWidget
{
    protected static ?string $heading = 'Savings/Shares against Total Loan';

    protected static ?string $maxHeight = '15rem';

//    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $loan = Loan::where('status', 1)->sum('balance');
        $savings = SavingHistory::all()->sum('credit')
            - SavingHistory::all()->sum('debit');

        $shares = ShareHistory::all()->sum('credit')
            - ShareHistory::all()->sum('debit');
        $capital = $savings + $shares;
        return [
            'labels' => ['Total Savings and Shares', 'Total Active Loan'],
            'datasets' => [
                [
                    'label' => 'My First Dataset',
                    'data' => [$capital, $loan],
                    'backgroundColor' => ['rgb(0, 255, 0)', 'rgb(255, 0, 0)'],
                    'hoverOffset' => 8
                ]
            ]
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
