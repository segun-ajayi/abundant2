<?php

namespace App\Http\Livewire\Inc;

use App\Models\LoanRepayment;
use App\Models\Member;
use Livewire\Component;

class LoanHistory extends Component
{
    protected $listeners = [
        'refresh' => '$refresh',
        'refreshComponents' => 'mount'];
    public $member;

    public function mount(Member $member) {
        $this->member = $member;
    }

    public function reverse(LoanRepayment $history) {
        $history->deleteHistory();
        $this->emit('toast', 'suc', 'Reverse Successful!');
        $this->emit('refresh');
    }

    public function render()
    {
        return view('livewire.inc.loan-history');
    }
}
