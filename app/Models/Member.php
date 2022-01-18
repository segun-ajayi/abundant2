<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class Member extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Notifiable;
    protected $dates = ['deleted_at'];
    protected $guarded = [];


    public function share() {
        return $this->hasOne(Share::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function isAdmin() {
        if ($this->user_id) {
            $user = User::find($this->user_id);
//            dd($user->role);
            if($user->role == 'user') {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    public function attendance() {
        return $this->belongsToMany(Attendance::class);
    }

    public function savings() {
        return $this->hasOne(Saving::class);
    }

    public function specialSavings() {
        return $this->hasOne(Special::class, 'member_id');
    }

    public function fines() {
        return $this->belongsToMany(Fine::class);
    }

    public function building() {
        return $this->hasOne(Building::class, 'member_id');
    }

    public function loans() {
        return $this->hasMany(Loan::class, 'member_id');
    }

    public function utilities() {
        return $this->hasMany(Utility::class);
    }

    public function getLoan($year = NULL) {
        if ($year) {
            $sav = $this->loans()->whereYear('created_at', $year)->sum('amount');
            return $sav;
        }
        $loan = $this->loans()->where('status', 1)->get();
        if ($loan->isEmpty()) {
            return 0;
        }
        return $loan[0]->balance;
    }

    public function getLoanSureties() {
        $loan = $this->loans()->where('status', 1)->first();
        if (!$loan) {
            return '';
        }
        $a = Member::find($loan->refer1);
        if ($a) {
            $a = $a->name;
        } else {
            $a = '';
        }
        $b = Member::find($loan->refer2);
        if ($b) {
            $b = $b->name;
        } else {
            $b = '';
        }
        $c = Member::find($loan->refer3);
        if ($c) {
            $c = $c->name;
        } else {
            $c = '';
        }
        return $a . ', ' . $b . ', ' . $c;
    }

    public function getLoanT() {
        $loan = $this->loans()->where('status', 1)->get();
        if ($loan->isEmpty()) {
            return 0;
        }
        return $loan[0]->loan_type;
    }

    public function getLoanRate() {
        if ($this->getLoanT() == 'normal') {
            return config('loan.loan_interest');
        }
        return config('loan.emergency_interest');
    }

    public function getLastPay() {
        $loan = $this->loans()->where('status', 1)->get();
        if ($loan->isEmpty()) {
            return 0;
        }
        $last = Carbon::createFromFormat('Y-m-d', $loan[0]->lpDate);
        $now = Carbon::createFromFormat('Y-m-d', Setting::find(1)->pDate);
        return $now->diffInMonths($last);
    }

    public function getAccumulatedInterest($year = NULL) {
        if ($year) {
            $sav = $this->loans()->whereYear('created_at', $year)->get();
            $rep = 0;
            $int = 0;
            foreach ($sav as $item) {
                $rep = $rep + $item->repayments()->whereYear('date', $year)->sum('credit');
                $int = $int + $item->repayments()->whereYear('date', $year)->sum('interest');
            }
            return  [
                'totalRepayments' => $rep,
                'totalInterest' => $int
            ];
        }
        $balance = $this->getLoan();
        $rate = $this->getLoanRate();
        $multi = $this->getLastPay();
        return ceil(($balance * $rate) * $multi);
    }

    public function getSavingH() {
        $sh = $this->savings;
        if (!$sh) {
            return collect([]);
        }
        return $sh->history->sortByDesc('created_at')->take(5);
    }

    public function getShareH() {
        $sh = Share::where('member_id', $this->id)->first();
        if (!$sh) {
            return collect([]);
        }
        return ShareHistory::where('share_id', $sh->id)->orderByDesc('created_at')->get();
    }

    public function getBuildingH() {
        $sh = $this->building;
        if (!$sh) {
            return collect([]);
        }
        $year = Carbon::parse(Setting::find(1)->pDate)->format('Y');
        return BuildingHistory::where('building_id', $sh->id)
            ->whereYear('date', $year)
            ->orderByDesc('date')->get();
    }

    public function hasActiveLoan() {
        $sh = $this->loans()->where('status', 1)->first();
        return $sh;
    }

    public function getLoanH() {
        $sh = $this->loans()->where('status', 1)->get();
        if ($sh->isEmpty()) {
            return collect([]);
        }
        return $sh[0]->repayments->sortByDesc('created_at')->take(5);
    }

    public function getSaving($year = NULL, $cum = 1) {
        if ($year) {
            if ($this->savings) {
                if ($cum === 1) {
                    $sav = $this->savings->history()->whereYear('date', '<=', $year)->sum('credit');
                    $deb = $this->savings->history()->whereYear('date', '<=', $year)->sum('debit');
                } else {
                    $sav = $this->savings->history()->whereYear('date', $year)->sum('credit');
                    $deb = $this->savings->history()->whereYear('date', $year)->sum('debit');
                }
            } else {
                $sav = 0;
                $deb = 0;
            }
            return  $sav - $deb;
        }
        $savings = $this->savings;
        return $savings ? $savings->balance : 0;
    }

    public function getsSaving($year = NULL, $cum = 1) {
        if ($year) {
            if ($this->specialSavings) {
                if ($cum === 1) {
                    $sav = $this->specialSavings->history()->whereYear('date', '<=',$year)->sum('credit');
                    $deb = $this->specialSavings->history()->whereYear('date', '<=',$year)->sum('debit');
                } else {
                    $sav = $this->specialSavings->history()->whereYear('date', $year)->sum('credit');
                    $deb = $this->specialSavings->history()->whereYear('date', $year)->sum('debit');
                }
            } else {
                $sav = 0;
                $deb = 0;
            }

            return  $sav - $deb;
        }
        $savings = $this->specialSavings;
        return $savings ? $savings->balance : 0;
    }

    public function getUtility($year = NULL) {
        if ($year) {
            if ($this->utilities) {
                $sav = $this->utilities()->whereYear('date', $year)->sum('amount');
            } else {
                $sav = 0;
            }
            return  $sav;
        }
        $savings = $this->utilities()->sum('amount');
        return $savings ?? 0;
    }

    public function getFine($year = NULL) {
        if ($year) {
            if ($this->fines) {
                $sav = $this->fines()->whereYear('date', $year)->sum('credit');
            } else {
                $sav = 0;
            }
            return  $sav;
        }
        $savings = $this->fines()->sum('credit');
        return $savings ?? 0;
    }

    public function getShare($year = NULL, $cum = 1) {
        if ($year) {
            if ($this->share) {
                if ($cum === 1) {
                    $sav = $this->share->history()->whereYear('date', '<=',$year)->sum('credit');
                    $deb = $this->share->history()->whereYear('date', '<=',$year)->sum('debit');
                } else {
                    $sav = $this->share->history()->whereYear('date', $year)->sum('credit');
                    $deb = $this->share->history()->whereYear('date', $year)->sum('debit');
                }
            } else {
                $sav = 0;
                $deb = 0;
            }

            return  $sav - $deb;
        }
        $share = $this->share;
        return $share ? $share->balance : 0;
    }

    public function getBuilding($year = NULL) {
        if (!$year) {
            $year = Carbon::parse(Setting::find(1)->pDate)->format('Y');
        }
        $building = $this->building;
        if (!$building) {
            $building = $this->building()->create([
                'balance' => 0
            ]);
        }
        $credit = BuildingHistory::where('building_id', $building->id)
            ->whereYear('date', $year)
            ->sum('credit');
        $debit = BuildingHistory::where('building_id', $building->id)
            ->whereYear('date', $year)
            ->sum('debit');
        return $credit - $debit;
    }

    public function loanPercent() {
        $loan = $this->loans()->where('status', 1)->get();
        return (($loan[0]->amount - $loan[0]->balance) / $loan[0]->amount) *100;
    }

    public function sharePercent() {
        $share = $this->share();
        if ($share->exists()) {
            $per = ($this->share->balance / 15000) * 100;
        } else {
            $per = 0;
        }
        return $per;
    }

    public function creditSavings($saving, $mode) {
        $savings = $this->savings();
        if ($savings->doesntExist()) {
            $this->savings()->create([
                'balance' => 0
            ]);
        }
        $savings = $this->savings;
        $savings->balance = $savings->balance + $saving;
        $savings->save();
        $savings->history()->create([
            'date' => Setting::find(1)->pDate,
            'credit' => $saving,
            'mode' => $mode,
            'entered_by' => Auth::id()
        ]);
    }

    public function debitSavings($saving, $mode) {
        $savings = $this->savings();
        if ($savings->doesntExist()) {
            $this->savings()->create([
                'balance' => 0
            ]);
        }
        $savings = $this->savings;
        $savings->balance = $savings->balance - $saving;
        $savings->save();
        $savings->history()->create([
            'date' => Setting::find(1)->pDate,
            'debit' => $saving,
            'mode' => $mode,
            'entered_by' => Auth::id()
        ]);
    }

    public function creditSpecial($saving, $mode) {
        $spe = $this->specialSavings();
        if ($spe->doesntExist()) {
            $this->specialSavings()->create([
                'balance' => 0
            ]);
        }
        $spe = $this->specialSavings;
        $spe->balance = $spe->balance + $saving;
        $spe->save();
        $spe->history()->create([
            'date' => Setting::find(1)->pDate,
            'credit' => $saving,
            'mode' => $mode,
            'entered_by' => Auth::id()
        ]);
    }

    public function debitSpecial($saving, $mode) {
        $spe = $this->specialSavings();
        if ($spe->doesntExist()) {
            $this->specialSavings()->create([
                'balance' => 0
            ]);
        }
        $spe = $this->specialSavings;
        $spe->balance = $spe->balance - $saving;
        $spe->save();
        $spe->history()->create([
            'date' => Setting::find(1)->pDate,
            'debit' => $saving,
            'mode' => $mode,
            'entered_by' => Auth::id()
        ]);
    }

    public function creditBuilding($build, $mode) {
        $builds = $this->building();
        if ($builds->doesntExist()) {
            $this->building()->create([
                'balance' => 0
            ]);
        }
        $builds = $this->building;
        $builds->balance = $builds->balance + $build;
        $builds->save();
        $builds->history()->create([
            'date' => Setting::find(1)->pDate,
            'credit' => $build,
            'mode' => $mode,
            'entered_by' => Auth::id()
        ]);
    }

    public function creditShare($share, $mode) {
        $shares = $this->share();
        if ($shares->doesntExist()) {
            $this->share()->create([
                'balance' => 0
            ]);
        }
        $shares = $this->share;
        $shares->balance = $shares->balance + $share;
        $shares->save();
        $shares->history()->create([
            'date' => Setting::find(1)->pDate,
            'credit' => $share,
            'mode' => $mode,
            'entered_by' => Auth::id()
        ]);
    }

    public function debitShare($saving, $mode) {
        $spe = $this->share();
        if ($spe->doesntExist()) {
            $this->share()->create([
                'balance' => 0
            ]);
        }
        $spe = $this->share;
        $spe->balance = $spe->balance - $saving;
        $spe->save();
        $spe->history()->create([
            'date' => Setting::find(1)->pDate,
            'debit' => $saving,
            'mode' => $mode,
            'entered_by' => Auth::id()
        ]);
    }

    public function creditLoan($loan, $interest, $mode) {
        if (!$interest) {
            $interest = 0;
        }
        $loans = $this->loans()->where('status', 1)->get();
        if ($loans->isEmpty()) {
            return;
        }
        if ($mode == "savings" || $mode == "special") {
            $amm = $loan + $interest;
            if ($mode == "savings") {
                if ($this->getSaving() < $amm) {
                    return false;
                }
                $this->debitSavings($amm, "service loan");
            }
            if ($mode == "special") {
                if ($this->getsSaving() < $amm) {
                    return false;
                }
                $this->debitSpecial($amm, "service loan");
            }
        }
        $loans[0]->balance = $loans[0]->balance - $loan;
        $loans[0]->lpDate = Setting::find(1)->pDate;
        $loans[0]->save();
        $loans = $this->loans()->where('status', 1)->get();
        if ($loans[0]->balance < 1) {
            $loans[0]->status = 0;
            $loans[0]->save();
        }
        $loans[0]->repayments()->create([
            'date' => Setting::find(1)->pDate,
            'credit' => $loan,
            'interest' => $interest,
            'mode' => $mode,
            'entered_by' => Auth::id()
        ]);
        return true;
    }

    public function memberAttendance($mon) {
        $att = $this->attendance->where('year', Carbon::now()->format('Y'))
            ->where('month', '<=', $mon)->count();
        $att = $att * 1;
        $per = ($att/$mon) * 100;
        return $per;
    }
}
