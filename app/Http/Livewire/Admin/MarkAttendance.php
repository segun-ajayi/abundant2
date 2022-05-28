<?php

namespace App\Http\Livewire\Admin;

use App\Models\Attendance;
use App\Models\Member;
use App\Models\Setting;
use Carbon\Carbon;
use Livewire\Component;

class MarkAttendance extends Component
{
    public $attendance = [], $members = [], $months = [], $pMonth, $year, $mark;

    public function markAttendance($attendance, $member) {
        $att = Attendance::find($attendance);

        if ($att->members->contains('id', $member)) {
            $att->members()->detach($member);
        } else {
            $att->members()->attach($member);
        }
    }

    public function updatedMark($year) {
        dd($year);
    }

    public function updatedYear($year) {
        $this->attendance = Attendance::where('year', $year)->orderby('month')->get();

        $this->emit('toast', 'suc', 'Updated');
    }

    public function mount() {
        $year = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');
        $att = Attendance::where('year', $year)->where('month', $month)->first();
        if (!$att) {
            Attendance::create([
                'year' => $year,
                'month' => $month,
                'date' => Carbon::now()->format('Y-m-d'),
            ]);
        }
        $this->attendance = Attendance::where('year', $year)->orderby('month')->get();
        $this->members = Member::all();
    }

    public function render()
    {
        return view('livewire.admin.mark-attendance');
    }
}
