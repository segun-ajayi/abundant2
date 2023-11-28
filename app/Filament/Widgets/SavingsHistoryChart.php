<?php

namespace App\Filament\Widgets;

use App\Models\SavingHistory;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class SavingsHistoryChart extends ChartWidget
{
    protected static ?string $heading = 'Savings per Month';

    protected function getData(): array
    {
        $data = Trend::model(SavingHistory::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->sum('credit');

        return [
            'datasets' => [
                [
                    'label' => 'Total savings per month',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => Carbon::parse($value->date)->format('M')),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
