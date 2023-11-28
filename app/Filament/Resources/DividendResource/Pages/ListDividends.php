<?php

namespace App\Filament\Resources\DividendResource\Pages;

use App\Filament\Resources\DividendResource;
use App\Models\Dividend;
use App\Models\DividendReport;
use App\Models\Member;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;

class ListDividends extends ListRecords
{
    protected static string $resource = DividendResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
//            Actions\Action::make('run')
//                ->action(fn () => $this->runAct()),
        ];
    }

    private function runAct()
    {
        $divs = \DB::table('dividendsOld')
            ->select(DB::raw("DISTINCT year"), )->get();

        foreach ($divs as $item) {
            $f = \DB::table('dividendsOld')->where('year', $item->year)->get();

            Dividend::create([
                'year' => $f[0]->year,
                'shared' => $f->sum('amount'),
                'amount' => $f[0]->total,
                'excess' => $f[0]->total - $f->sum('amount'),
                'paid' => $f->where('status', 'paid')->count(),
                'unpaid' => $f->where('status', 'unpaid')->count()
            ]);

            foreach ($f as $it) {
                $divId = Dividend::where('year', $it->year)->first()->id;
                if (Member::find($it->member_id)) {
                    DividendReport::create([
                        'member_id' => $it->member_id,
                        'dividend_id' => $divId,
                        'amount' => $it->amount,
                        'mode' => $it->mode,
                        'status' => $it->status == 'paid',
                        'pDate' => $it->pDate ? $it->pDate : now(),
                    ]);
                }
            }
        }
    }
}
