<?php

namespace App\Http\Livewire\Admin;

use App\Models\Setting;
use Carbon\Carbon;
use Livewire\Component;

class PayMonth extends Component
{
    protected $listeners = ['refresh' => '$refresh'];

    public $pMonth, $month, $isOpen = false, $years = [], $year;

    public function mount() {
        for ($year = Carbon::now()->format('Y'); $year >= 2019; $year--) {
            array_push($this->years, $year);
        }
        $pMonth = Setting::firstOrCreate(['id' => 1], [
            'pDate' => carbon::now()->format('Y-m-d')
        ]);
        $this->pMonth = Carbon::parse($pMonth->pDate)->monthName . ', ' . Carbon::parse($pMonth->pDate)->format('Y');

    }

    public function change() {
        $this->isOpen = true;
    }

    public function post()
    {
        if (!$this->year) {
            $this->year = Carbon::now()->format('Y');
        }

        $this->validate([
            'month' => 'required'
        ]);

        $date = $this->year . '/' . $this->month . '/16';
        Setting::updateOrCreate(['id' => 1], [
            'pDate' => carbon::parse($date)->format('Y-m-d')
        ]);
        $this->reset();
        $this->emit('toast', 'suc', 'Change Successful!');
        $this->emit( 'refre');
        $this->mount();
    }

    public function render()
    {
        return view('livewire.admin.pay-month');
    }
}
