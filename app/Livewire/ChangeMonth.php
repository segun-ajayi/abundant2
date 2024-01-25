<?php

namespace App\Livewire;

use App\Models\Setting;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Livewire\Component;

class ChangeMonth extends Component
{

    public array $years;
    public string $month, $year;
    public Setting $setting;

    public function mount()
    {
        for ($year = Carbon::now()->format('Y'); $year >= 2019; $year--) {
            $this->years[$year] = $year;
        }
        $this->setting = Setting::firstOrCreate(['id' => 1], [
            'pDate' => carbon::now()->format('Y-m-d')
        ]);

        $date = Carbon::parse($this->setting->pDate);

        $this->year = $date->format('Y');
        $this->month = $date->format('n');
    }

    public function setMonth()
    {
        if ($this->month === '')
        {
            Notification::make()
                ->title('Please choose a month')
                ->danger()
                ->send();
            return;
        }

        if ($this->year === '') {
            $this->year = Carbon::now()->format('Y');
        }

        $this->setting->setMonth([
            'month' => $this->month,
            'year' => $this->year
        ]);

        Notification::make()
            ->title('Post month changed successfully!')
            ->success()
            ->send();
    }

    public function render()
    {
        return view('livewire.change-month');
    }
}
