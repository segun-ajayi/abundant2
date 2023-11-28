<?php

namespace App\Filament\Resources\DividendResource\Pages;

use App\Filament\Resources\DividendResource;
use App\Models\Dividend;
use App\Models\Member;
use App\Models\SavingHistory;
use App\Models\ShareHistory;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use function PHPUnit\Framework\isEmpty;

class CreateDividend extends CreateRecord
{
    protected static string $resource = DividendResource::class;


    protected function afterCreate(): void
    {
        $divs = Dividend::all();

        foreach ($divs as $div) {
            if ($div->reports->count() < 1) {

                $dividend = $div->amount;

                $year = '12-12-' . $div->year;
                $end = Carbon::parse($year)->endOfYear()->format('Y-m-d H:i:s');
//                $end = Carbon::createFromFormat('Y-m-d H:i:s', $end);
                $divider = $div->divider ?? Dividend::getDivider($div->year);

                $members = Member::all();
                $last = Carbon::parse($end)->subMonths(6);

                $shared = 0;
                $count = 0;

                foreach($members as $member) {

                    $savings = $member->getSaving($div->year);
                    $share = $member->getShare($div->year);
                    $mode = $div->year . ' dividend';
                    $amount = ($dividend / $divider) * ($share + $savings);
                    $shared += $amount;
                    $count++;

                    if ($member->savings) {
                        $joo = Carbon::createFromFormat('Y-m-d H:i:s', $member->savings->created_at);
                        if ($joo->between($last, $end)) {
                            $diff = 6 - $joo->diffInMonths($last);
                            $amount = ($amount / 12) * $diff;
                        } else if ($joo->gt($end)){
                            $amount = 0;
                        }
                    }

                    if ($amount === 0) {
                        continue;
                    }

                    $member->dividends()->create([
                        'dividend_id' => $div->id,
                        'amount' => round($amount, 2),
                        'mode' => 'unpaid dividend',
                    ]);
                }

                $excess = $div->amount - $shared;
                $div->update([
                    'shared' => $shared,
                    'excess' => $excess,
                    'paid' => 0,
                    'unpaid' => $count
                ]);
            }
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['shared'] = 0;
        $data['excess'] = 0;
        $data['paid'] = 0;
        $data['unpaid'] = 0;

        return $data;
    }
}
