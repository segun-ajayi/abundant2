<?php

namespace App\Filament\Resources\MemberResource\Widgets;

use App\Models\Member;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SharesOverview extends BaseWidget
{
    public Member $record;

    public function goTo(string $url)
    {
        $this->dispatch('open-modal', id: $url);
    }

    protected function getStats(): array
    {
        $per = $this->record->sharePercent();
        $last = $this->record->lastSavings();
        $loanPer = $this->record->loanPercent();

        $lastLoan = $this->record->getLastLoanPay();

        $savings = $this->record->getSavingH()->pluck('credit')->toArray();

//        if ($this->record->getLoan()) {
            return [
                Stat::make('Shares', "₦" . number_format($this->record->share?->balance, 2, '.', ','))
                    ->description($per . '% of minimum shares.')
//                    ->descriptionIcon('heroicon-m-arrow-trending-down')
                    ->color($per < 100 ? 'warning' : 'success')
                    ->extraAttributes([
                            'class' => 'cursor-pointer',
                            'wire:click' => "goTo('share')",
                        ]),
                Stat::make('Building', "₦" . number_format($this->record->getBuilding(Carbon::now()->format('Y')), 2, '.', ','))
                    ->description("Last year: ₦" . number_format($this->record->getBuilding(Carbon::now()->subYear()), 2, '.', ','))
//                ->descriptionIcon('heroicon-m-arrow-trending-down')
                    ->color('danger'),
                Stat::make('Savings', "₦" . number_format($this->record->getSaving(), 2, '.', ','))
                    ->description($last['amount'] ? "Last posting on " . Carbon::parse($last['date'])->format('M, Y') . ": ₦" . number_format($last['amount'], 2, '.', ',') : '')
//                ->descriptionIcon('heroicon-m-arrow-trending-up')
                    ->color($this->record->lastSavings()['status'] ? 'success' : 'danger')
                    ->chart($savings)
                    ->extraAttributes([
                        'class' => 'cursor-pointer',
                        'wire:click' => "goTo('withdrawSavings')",
                    ]),
                Stat::make('Loan', $this->record->getLoan() ? "₦" . number_format($this->record->getLoan(), 2, '.', ',') : 'No Active Loan')
                    ->description(isset($lastLoan['credit']) ? "Last posting on " . Carbon::parse($lastLoan['date'])->format('M, Y') . ": ₦" . number_format($lastLoan['credit'] + $lastLoan['interest'], 2, '.', ',') : '')
//                    ->descriptionIcon('heroicon-m-arrow-trending-up')
                    ->color('success'),
            ];

    }
}
