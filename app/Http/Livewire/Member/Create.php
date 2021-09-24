<?php

namespace App\Http\Livewire\Member;

use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public $member_id, $name, $address, $sex, $marital, $phone,
        $phone2, $email, $profession, $purpose, $referrer, $nok,
        $nok_address, $nok_phone, $nok_phone2, $picture, $members = [], $av;

    public function mount() {
        $this->members = Member::all();
        $av = DB::select('select distinct member_id+1 as s from members where member_id + 1 not in(select distinct member_id from members)');
        foreach ($av as $a) {
            if (!$this->av) {
                $this->av = $a->s;
                continue;
            }
            $this->av = $this->av . ', ' . $a->s;
        }
    }

    public function create() {
        $this->validate([
            'member_id' => 'required|unique:members',
            'picture' => 'nullable|file|mimes:jpg,jpeg,png'
        ]);

        $member = Member::create([
            'member_id' => $this->member_id,
            'name' => $this->name,
            'address' => $this->address,
            'sex' => $this->sex,
            'marital' => $this->marital,
            'phone' => $this->phone,
            'phone2' => $this->phone2,
            'email' => $this->email,
            'profession' => $this->profession,
            'purpose' => $this->purpose,
            'referrer' => $this->referrer,
            'nok' => $this->nok,
            'nok_address' => $this->nok_address,
            'nok_phone' => $this->nok_phone,
            'nok_phone2' => $this->nok_phone2,
        ]);
        if ($this->picture) {
            $photo_name = $member->id . Carbon::now()->timestamp . '.' . $this->picture->getClientOriginalExtension();
            $this->picture->storePubliclyAs('public/member-photos/', $photo_name);
        } else {
            $photo_name = 'nopix.png';
        }
        $member->update([
            'pix' => $photo_name
        ]);

        $this->reset();
        $this->emit('toast', 'suc', 'Member created successfully!');
        return redirect()->route('member', $member);
    }

    public function render()
    {
        return view('livewire.member.create');
    }
}
