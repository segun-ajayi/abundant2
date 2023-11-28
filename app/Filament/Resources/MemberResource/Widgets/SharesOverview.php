<?php

namespace App\Filament\Resources\MemberResource\Widgets;

use App\Models\Member;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SharesOverview extends BaseWidget
{
    public Member $record;
    protected function getStats(): array
    {
        $per = $this->record->sharePercent();
        $last = $this->record->lastSavings();
        $loanPer = $this->record->loanPercent();

        $savings = $this->record->getSavingH()->pluck('credit')->toArray();

        if ($this->record->getLoan()) {
            return [
                Stat::make('Shares', "₦" . number_format($this->record->share?->balance, 2, '.', ','))
                    ->description($per . '% of minimum shares.')
//                    ->descriptionIcon('heroicon-m-arrow-trending-down')
                    ->color($per < 100 ? 'warning' : 'success'),
                Stat::make('Building', "₦" . number_format($this->record->getBuilding(Carbon::now()->format('Y')), 2, '.', ','))
                    ->description("Last year: ₦" . number_format($this->record->getBuilding(Carbon::now()->subYear()), 2, '.', ','))
//                ->descriptionIcon('heroicon-m-arrow-trending-down')
                    ->color('danger'),
                Stat::make('Savings', "₦" . number_format($this->record->getSaving(), 2, '.', ','))
                    ->description($last['amount'] ? "Last posting on " . Carbon::parse($last['date'])->format('M, Y') . ": ₦" . number_format($last['amount'], 2, '.', ',') : '')
//                ->descriptionIcon('heroicon-m-arrow-trending-up')
                    ->color($this->record->lastSavings()['status'] ? 'success' : 'danger')
                    ->chart($savings),
                Stat::make('Loan', "₦" . number_format($this->record->getLoan(), 2, '.', ','))
                    ->description(ceil($loanPer) . '% paid')
//                    ->descriptionIcon('heroicon-m-arrow-trending-up')
                    ->color('success'),
            ];
        } else {
            return [
                Stat::make('Shares', "₦" . number_format($this->record->share?->balance, 2, '.', ','))
                    ->description($per . '% of minimum shares.')
                    ->descriptionIcon('heroicon-m-arrow-trending-down')
                    ->color('warning'),
                Stat::make('Building', "₦" . number_format($this->record->getBuilding(Carbon::now()->format('Y')), 2, '.', ','))
                    ->description("Last year: ₦" . number_format($this->record->getBuilding(Carbon::now()->subYear()), 2, '.', ','))
//                ->descriptionIcon('heroicon-m-arrow-trending-down')
                    ->color('danger'),
                Stat::make('Savings', "₦" . number_format($this->record->getSaving(), 2, '.', ','))
                    ->description($last['amount'] ? "Last posting on " . Carbon::parse($last['date'])->format('M, Y') . ": ₦" . number_format($last['amount'], 2, '.', ',') : '')
//                ->descriptionIcon('heroicon-m-arrow-trending-up')
                    ->color($this->record->lastSavings()['status'] ? 'success' : 'danger')
                    ->chart($savings),
//                Stat::make('Loan', "₦" . number_format($this->record->getLoan(), 2, '.', ','))
//                    ->description('3% increase')
//                    ->descriptionIcon('heroicon-m-arrow-trending-up')
//                    ->color('success'),
            ];
        }


    }
}
