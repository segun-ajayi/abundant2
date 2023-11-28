<?php

namespace App\Filament\Resources\DividendResource\Pages;

use App\Filament\Resources\DividendResource;
use App\Models\Dividend;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use JetBrains\PhpStorm\NoReturn;

class ViewDividend extends ViewRecord
{
    protected static string $resource = DividendResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('pay')
                ->color('success')
                ->requiresConfirmation()
                ->action(fn (Dividend $record) => $this->pay($record))
                ->visible(function (Dividend $record) {
                    return $record->reports->where('status', false)->count();
                }),
            Actions\Action::make('reverse')
                ->color('danger')
                ->requiresConfirmation()
                ->action(fn (Dividend $record) => $this->reverse($record))
                ->visible(function (Dividend $record) {
                    return $record->reports->where('status', true)->count();
                }),
        ];
    }

    private function pay(Dividend $record)
    {
        $paid = 0;
        $unpaid = 0;
        foreach ($record->reports as $item) {
            if (!$item->status) {
                $item->member->creditSavings($item->amount, 'dividend', Carbon::now()->format('Y-m-d'));

                $item->update([
                    'status' => true
                ]);
                $paid++;
            } else {
                $unpaid++;
            }
        }

        $record->update([
            'paid' => $paid,
            'unpaid' => $unpaid
        ]);
    }

    private function reverse (Dividend $record): Void {
        $paid = 0;
        $unpaid = 0;
        foreach ($record->reports as $item) {
            if ($item->status) {
                $item->member->debitSavings($item->amount, 'dividend', Carbon::now()->format('Y-m-d'));

                $item->update([
                    'status' => false
                ]);
                $unpaid++;
            } else {
                $paid++;
            }
        }

        $record->update([
            'paid' => 0,
            'unpaid' => $unpaid + $paid
        ]);
    }
}
