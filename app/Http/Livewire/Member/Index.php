<?php

namespace App\Http\Livewire\Member;

use App\Models\Loan;
use App\Models\Member;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    protected $listeners = [
        'refresh' => '$refresh',
        'refre' => 'refreshComponents'
        ];

    public $member, $loan, $isOpenFine = false, $isOpenUtil = false, $isOpenPost = false, $isOpenLoan = false, $isOpenExco = false;
    public $fineAmount, $fineReason, $payMethod, $utilityType, $price, $value, $gt, $withdrawFrom;
    public $loanType, $duration, $amount, $surety = [], $members, $isOpenWithdraw = false;
    public $user, $savings, $loanRepay, $loanInterest, $shares, $building, $specialSavings, $username;

    public function mount($member) {
        $this->members = Member::all();
        $this->user = Auth::user();
        $this->member = $member;
        $this->loan = $this->member->loans()->where('status', 1)->first();
        $this->emit('refreshComponents', $this->member);
    }

    public function refreshComponents() {
        dd('dfdfs');
        $this->emit('refreshComponents', $this->member);
    }

    public function updatedGt($value) {
        $mem = Member::where('member_id', $value)->first();
        if ($mem) {
            $this->mount($mem);
        }
    }

    public function reverseloan() {
        $this->loan->delete();
        $this->loan = null;
        $this->emit('refresh');
    }

    public function withdrawModal() {
        $this->isOpenWithdraw = true;
    }

    public function excoModal() {
        $this->isOpenExco = true;
    }

    public function fineModal() {
        $this->isOpenFine = true;
    }

    public function utilModal() {
        $this->isOpenUtil = true;
    }

    public function postModal() {
        $this->isOpenPost = true;
    }

    public function loanModal() {
        $this->isOpenLoan = true;
    }

    public function prev() {
        $mem = Member::where('member_id', '<', $this->member->member_id)->max('member_id');
        if ($mem) {
            $this->mount(Member::where('member_id', $mem)->first());
        }
    }

    public function next() {
        $mem = Member::where('member_id', '>', $this->member->member_id)->min('member_id');
        if ($mem) {
            $this->mount(Member::where('member_id', $mem)->first());
        }
    }

    public function fine() {
        $this->validate([
            'fineAmount' => 'required',
            'fineReason' => 'required',
            'payMethod' => 'required',
        ]);

        $amount = str_replace(',', '', $this->fineAmount);
        $this->member->fines()->create([
            'credit' => $amount,
            'mode' => $this->payMethod,
            'date' => Setting::find(1)->pDate,
            'reason' => $this->fineReason,
            'entered_by' => $this->user->id,
        ]);
        if ($this->payMethod == 'savings') {
            $this->member->debitSavings($amount, 'fine');
        }
        $this->isOpenFine = false;
        $this->reset('fineAmount','fineReason','payMethod');
        $this->emit('toast', 'suc', 'Member fined successfully!');
        $this->emit('refresh');
    }

    public function buyUtil() {
        $this->validate([
            'price' => 'required',
            'utilityType' => 'required',
            'payMethod' => 'required',
        ]);

        $amount = str_replace(',', '', $this->price);
        $this->member->utilities()->create([
            'amount' => $amount,
            'mode' => $this->payMethod,
            'date' => Setting::find(1)->pDate,
            'name' => $this->utilityType,
            'entered_by' => $this->user->id,
        ]);
        if ($this->payMethod == 'savings') {
            $this->member->debitSavings($amount, $this->utilityType);
        }

        $this->isOpenUtil = false;
        $this->reset('price','utilityType','payMethod');
        $this->emit('toast', 'suc', 'Utility purchased successfully!');
        $this->emit('refresh');
    }

    public function updatedLoanRepay($value) {
        if ($this->value) {
            $this->reset('value');
            return;
        }
        $value = $value ? str_replace(',', '', $value) : 0;
        $this->loanInterest = $this->member->getAccumulatedInterest();
        $this->loanRepay = $value - $this->loanInterest;
    }

    public function updatedLoanInterest($value) {
        $this->value = $value;
    }

    public function post() {
        $this->validate([
            'payMethod' => 'required',
        ]);
        $this->loanInterest = $this->loanInterest ? str_replace(',', '', $this->loanInterest) : 0;
        $this->loanRepay = $this->loanRepay ? str_replace(',', '', $this->loanRepay) : 0;
        $this->shares = $this->shares ? str_replace(',', '', $this->shares) : 0;
        $this->savings = $this->savings ? str_replace(',', '', $this->savings) : 0;
        $this->building = $this->building ? str_replace(',', '', $this->building) : 0;
        $this->specialSavings = $this->specialSavings ? str_replace(',', '', $this->specialSavings) : 0;
//        dd($this->loanRepay, $this->loanInterest, $this->savings, $this->specialSavings, $this->building, $this->shares);
        $total = $this->loanRepay + $this->loanInterest + $this->savings + $this->specialSavings + $this->building + $this->shares;
        if ($this->savings) {
            $amount = str_replace(',', '', $this->savings);
            if ($this->payMethod == 'special') {
                if ($this->member->getsSaving() < $total) {
                    $this->emit('toast', 'err', 'Special savings insufficient');
                    return;
                }
                $this->member->debitSpecial($amount, "Posted to Savings");
            }
            if ($this->payMethod == 'savings') {
                $this->emit('toast', 'err', 'Cannot use savings to credit savings');
                return;
            }
            $this->member->creditSavings($amount, $this->payMethod);
        }
        if ($this->specialSavings) {
            $amount = str_replace(',', '', $this->specialSavings);
            if ($this->payMethod == 'savings') {
                if ($this->member->getsaving() < $total) {
                    $this->emit('toast', 'err', 'Savings insufficient');
                    return;
                }
                $this->member->debitSavings($amount, "Posted to Special Savings");
            }
            if ($this->payMethod == 'special') {
                $this->emit('toast', 'err', 'Cannot use special savings to credit special savings');
                return;
            }
            $this->member->creditSpecial($amount, $this->payMethod);
        }
        if ($this->building) {
            $amount = str_replace(',', '', $this->building);
            if ($this->payMethod == 'special') {
                if ($this->member->getsSaving() < $total) {
                    $this->emit('toast', 'err', 'Special savings insufficient');
                    return;
                }
                $this->member->debitSpecial($amount, "Posted to Building");
            }
            if ($this->payMethod == 'savings') {
                if ($this->member->getsaving() < $total) {
                    $this->emit('toast', 'err', 'Savings insufficient');
                    return;
                }
                $this->member->debitSavings($amount, "Posted to Building");
            }
            $this->member->creditBuilding($amount, $this->payMethod);
        }
        if ($this->shares) {
            $amount = str_replace(',', '', $this->shares);
            if ($this->payMethod == 'special') {
                if ($this->member->getsSaving() < $total) {
                    $this->emit('toast', 'err', 'Special savings insufficient');
                    return;
                }
                $this->member->debitSpecial($amount, "Posted to Shares");
            }
            if ($this->payMethod == 'savings') {
                if ($this->member->getsaving() < $total) {
                    $this->emit('toast', 'err', 'Savings insufficient');
                    return;
                }
                $this->member->debitSavings($amount, "Posted to Special Shares");
            }
            $this->member->creditShare($amount, $this->payMethod);
        }
        if ($this->loanRepay) {
            $amount = str_replace(',', '', $this->loanRepay);
            $interest = str_replace(',', '', $this->loanInterest);
            $lo = $this->member->getLoan();
            if ($this->payMethod == 'special') {
                if ($this->member->getsSaving() < $total) {
                    $this->emit('toast', 'err', 'Special savings insufficient');
                    return;
                }
                $this->member->debitSpecial($amount, "Loan Repayment");
            }
            if ($this->payMethod == 'savings') {
                if ($this->member->getsaving() < $total) {
                    $this->emit('toast', 'err', 'Savings insufficient');
                    return;
                }
                $this->member->debitSavings($amount, "Loan Repayment");
            }
            if ($amount > $lo) {
                $am = $amount - $lo;
                $this->member->creditSavings($am, "Loan excesses");
                $this->member->creditLoan($lo, $interest, $this->payMethod);
            } else {
                $this->member->creditLoan($amount, $interest, $this->payMethod);
            }
        }


        $this->isOpenPost = false;
        $this->reset('savings', 'loanRepay', 'loanInterest', 'shares', 'building', 'specialSavings', 'payMethod');
        $this->emit('toast', 'suc', 'Post successful!');
        $this->emit('refresh');
    }

    public function giveLoan() {
        if (!$this->duration) {
            $this->duration = 12;
        }
        if (!$this->loanType) {
            $this->loanType = 'normal';
        }
        if (!$this->payMethod) {
            $this->payMethod = 'bank';
        }

        $this->validate([
           'loanType' => 'required',
           'surety' => 'required|max:3|min:2',
           'duration' => 'required',
           'amount' => 'required',
           'payMethod' => 'required'
        ], [
            'surety.max' => 'Please provide maximum of three (3) sureties.',
            'surety.min' => 'Please provide minimum of two (2) sureties.'
        ]);
        $date = Setting::find(1)->pDate;
        Loan::create([
            'member_id' => $this->member->id,
            'duration' => $this->duration,
            'loan_type' => $this->loanType,
            'amount' => str_replace(',', '', $this->amount),
            'balance' => str_replace(',', '', $this->amount),
            'lpDate' => $date,
            'Approved_on' => $date,
            'refer1' => $this->surety[0],
            'refer2' => $this->surety[1],
            'refer3' => $this->surety[2] ?? 0,
            'granted_by' => $this->user->id,
            'mode' => $this->payMethod,
        ]);
        $this->isOpenLoan = false;
        $this->reset('surety', 'loanType', 'duration', 'amount', 'payMethod');
        $this->emit('toast', 'suc', 'loan Approved successfully!');
        $this->mount($this->member);
    }

    public function withdraw() {
        if (!$this->payMethod) {
            $this->payMethod = 'bank';
        }

        $this->validate([
           'withdrawFrom' => 'required',
           'amount' => 'required',
           'payMethod' => 'required'
        ], [
            'withdrawFrom.required' => 'Please select an account to withdraw from.'
        ]);
        $this->amount = str_replace(',', '', $this->amount);

        if ($this->withdrawFrom == 'saving') {
            $this->member->debitSavings($this->amount, $this->payMethod);
        }
        if ($this->withdrawFrom == 'special') {
            $this->member->debitSpecial($this->amount, $this->payMethod);
        }
        if ($this->withdrawFrom == 'shares') {
            $this->member->debitShare($this->amount, $this->payMethod);
        }

        $this->isOpenWithdraw = false;
        $this->reset('withdrawFrom', 'amount', 'payMethod');
        $this->emit('toast', 'suc', 'Withdraw successful!');
        $this->mount($this->member);
        $this->emit('refresh');
    }

    public function exco() {

        $this->validate([
           'username' => 'required|string|min:6|max:12',
        ]);

        $user = User::create([
            'username' => $this->username,
            'password' => bcrypt('welcome123'),
        ]);

        $this->member->update([
            'user_id' => $user->id
        ]);

        $this->isOpenExco = false;
        $this->reset('username');
        $this->emit('toast', 'suc', 'Member made exco successfully!');
        $this->emit('refresh');
    }

    public function removeExco() {

        $user = User::find($this->member->user_id);
        $user->delete();
        $this->member->update([
            'user_id' => 0
        ]);

        $this->emit('toast', 'suc', 'Member removed from excos successfully');
        $this->emit('refresh');
    }
    public function makeAdmin() {

        $user = User::find($this->member->user_id);
        $user->update([
            'role' => 'admin'
        ]);

        $this->emit('toast', 'suc', 'Administrator privileges granted!');
        $this->emit('refresh');
    }
    public function revokeAdmin() {
        $user = User::find($this->member->user_id);
        $user->update([
            'role' => 'user'
        ]);

        $this->emit('toast', 'suc', 'Administrator privileges removed!');
        $this->emit('refresh');
    }

    public function render()
    {
        return view('livewire.member.index');
    }
}
