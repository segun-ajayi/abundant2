<?php

namespace App\Http\Livewire\Admin;

use App\Models\Dividend;
use App\Models\Member;
use App\Models\SavingHistory;
use App\Models\ShareHistory;
use Carbon\Carbon;
use Livewire\Component;

class Divivend extends Component
{
    public $dividend, $divider, $year, $caution = false, $isLoading = false;

    public function continue() {
        $this->validate([
            'dividend' => 'required',
            'year' => 'required'
        ]);
        $this->caution = false;
        $this->isLoading = true;
        $this->process();
    }

    public function submit() {

        $this->validate([
            'dividend' => 'required',
            'year' => 'required'
        ]);
        $this->isLoading = true;
        $y = Dividend::where('year', $this->year)->first();
        if ($y) {
            $this->isLoading = false;
            $this->caution = true;
            return;
        }
        $this->process();
    }

    private function process()
    {
        $div = Dividend::where('year', $this->year)->get();
        if ($div->count() > 0) {
            foreach ($div as $item) {
                if ($item->status === 'paid' && $item->mode === 'savings') {
                    $item->member->savings->history()->where('mode', $this->year . ' Dividend')->delete();
                } elseif ($item->status === 'paid' && $item->mode === 'special') {
                    $item->member->specialSavings->history()->where('mode', $this->year . ' Dividend')->delete();
                } elseif ($item->status === 'paid' && $item->mode === 'cash') {
                    $item->member->debitSavings($item->amount, $this->year . ' Dividend Deduction');
                } elseif ($item->status === 'paid' && $item->mode === 'share') {
                    $item->member->share->history()->where('mode', $this->year . ' Dividend')->delete();
                }
                $item->delete();
            }
        }
        $dividend = str_replace(',', '', $this->dividend);

        $year = '12-12-' . $this->year;
        $end = Carbon::parse($year)->endOfYear()->format('Y-m-d H:i:s');
        $end = Carbon::createFromFormat('Y-m-d H:i:s', $end);
        if ($this->divider) {
            $divider = str_replace(',', '', $this->divider);
        } else {
            $totalSavings = SavingHistory::where('date', '<=',$end)->sum('credit')
                - SavingHistory::where('date', '<=',$end)->sum('debit');
            $totalShares = ShareHistory::where('date', '<=',$end)->sum('credit')
                - ShareHistory::where('date', '<=',$end)->sum('debit');
            $divider = $totalSavings + $totalShares;
        }
//        dd($end, $totalSavings, ShareHistory::where('date', '<=',$end)->sum('debit'));
        $members = Member::all();
        $last = Carbon::parse($end)->subMonths(6);

        foreach($members as $member) {
            if ($member->savings) {
                $savings = $member->savings->history->where('date', '<=',$end)->sum('credit')
                    - $member->savings->history->where('date', '<=',$end)->sum('debit');
            } else {
                $savings = 0;
            }
            if ($member->share) {
                $share = $member->share->history->sum('credit')
                    - $member->share->history->sum('debit');
            } else {
                $share = 0;
            }
            $mode = $this->year . ' dividend';
            $amount = ($dividend / $divider) * ($share + $savings);

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

            $member->dividend()->create([
                'total' => $dividend,
                'year' => $this->year,
                'amount' => round($amount, 2),
                'mode' => 'unpaid dividend',
                'status' => 'unpaid'
            ]);
        }
        $this->isLoading = false;
    }

    public function render()
    {
        return view('livewire.admin.divivend');
    }
}
