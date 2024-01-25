<?php

namespace App\Filament\Resources\DividendResource\Pages;

use App\Exports\DividendReportExport;
use App\Filament\Resources\DividendResource;
use App\Models\Dividend;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use JetBrains\PhpStorm\NoReturn;
use Maatwebsite\Excel\Facades\Excel;

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
            Actions\ActionGroup::make([
                Actions\Action::make('download1')
                    ->label('Excel')
                    ->action(fn (Dividend $record) => $this->download($record))
                    ->color('info')
                    ->icon('heroicon-o-table-cells'),
                Actions\Action::make('download2')
                    ->label('PDF')
                    ->action(fn (Dividend $record) => $this->downloadPDF($record))
                    ->color('danger')
                    ->icon('heroicon-o-document'),
            ])
                ->icon('heroicon-o-arrow-down-on-square-stack')
                ->button()
                ->label('Download')
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

    private function download(Dividend $record)
    {
        return Excel::download(new DividendReportExport($record), $record->year . '_dividend_report.xlsx');

    }

    private function downloadPDF(Dividend $record)
    {

        return Excel::download(new DividendReportExport($record), $record->year . '_dividend_report.pdf', \Maatwebsite\Excel\Excel::MPDF);

    }
}
