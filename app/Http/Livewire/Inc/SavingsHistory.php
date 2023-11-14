<?php

namespace App\Http\Livewire\Inc;

use App\Models\Member;
use App\Models\SavingHistory;
use Illuminate\Support\Str;
use Livewire\Component;

class SavingsHistory extends Component
{
    protected $listeners = [
        'refresh' => '$refresh',
        'refreshComponents' => 'mount'];
    public $member;

    public function mount(Member $member) {
        $this->member = $member;
    }

    public function reverse(SavingHistory $history) {
        if (Str::contains($history->mode, 'Dividend')) {
            $div = $this->member->dividend()->where('year', Str::before($history->mode, ' '))->first();
            $div->update([
                'status' => 'unpaid',
                'mode' => '',
            ]);
        }

        $history->deleteHistory();
        $this->emit('toast', 'suc', 'Reverse Successful!');
        $this->emit('refresh');
    }

    public function render()
    {
        return view('livewire.inc.savings-history');
    }
}
