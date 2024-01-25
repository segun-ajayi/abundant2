<?php

namespace App\Models;

use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use function PHPUnit\Framework\isEmpty;

class Member extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Notifiable;
    protected $dates = ['deleted_at'];
    protected $guarded = [];

    public function referer () : BelongsTo
    {
        return $this->belongsTo(Member::class, 'parent_id');
    }

    public function dividends () : HasMany
    {
        return $this->hasMany(DividendReport::class);
    }

    public function refer () : HasMany
    {
        return $this->hasMany(Member::class, 'parent_id');
    }

    protected function pix(): Attribute
    {
        return Attribute::make(
            get: function (?string $value) {
                if (!$value) {
                    return url('storage/member-photos/nopix.png');
                }
                if (str_starts_with($value, 'member-photos')) {
                    return url('storage/' . $value);
                } else {
                    if (Storage::disk('public')->exists('member-photos/' . $value)) {
                        return url('storage/member-photos/' . $value);
                    } else {
                        return url('storage/member-photos/nopix.png');
                    }
                }
            },
        );
    }

    public function getUnpaidDividend() {
        return $this->dividend()->where('status', 'unpaid')->get();
    }

    public function dividend() {
        return $this->hasMany(Dividend::class);
    }

    public function getDividend($year) {
        return $this->dividend()->where('year', $year)->get();
    }

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

    public function buildingHistory(): hasMany {
        return $this->hasMany(BuildingHistory::class, 'member_id')
            ->orderBy('date', 'desc');
    }

    public function loans() {
        return $this->hasMany(Loan::class, 'member_id');
    }

    public function getActiveLoans() {
        return $this->hasMany(Loan::class, 'member_id')->where('status', 1);
    }

    public function getLastLoan() {
        $loan = $this->loans;
        if (isEmpty($this->loans)) {
            return [];
        }
        return $this->loans->latest();
    }

    public function loanRepayments() {
        return $this->hasMany(LoanRepayment::class)->orderBy('date', 'desc');
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

    public function getLastLoanPay() {
        return $this->loanRepayments->first() ? $this->loanRepayments->first() : [];
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

    public function savingRecord() {
        return $this->hasMany(SavingHistory::class)->orderBy('date', 'desc');
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
        if (!$loan->isEmpty()) {
            return (($loan[0]->amount - $loan[0]->balance) / $loan[0]->amount) *100;
        }
    }

    public function sharePercent() {
        $share = $this->share();
        if ($share->exists()) {
            $per = ($this->share->balance / 20000) * 100;
        } else {
            $per = 0;
        }
        return ceil($per);
    }

    public function creditSavings($saving, $mode, $date = null) {
        $savings = $this->savings();
        if ($savings->doesntExist()) {
            $this->savings()->create([
                'balance' => 0
            ]);
        }
        if (!$date) {
            $date = Setting::find(1)->pDate;
        }
        $savings = $this->savings;
        $savings->balance = $savings->balance + $saving;
        $savings->save();
        SavingHistory::create([
            'date' => $date,
            'saving_id' => $this->savings->id,
            'member_id' => $this->id,
            'credit' => $saving,
            'mode' => $mode,
            'balance' => $this->savings->balance,
            'entered_by' => Auth::id()
        ]);
    }



    public function creditSpecial($saving, $mode) {
        $spe = $this->specialSavings();
        if ($spe->doesntExist()) {
            $spe = $this->specialSavings()->create([
                'balance' => 0
            ]);
        }
//        $spe = $this->specialSavings;
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
            'member_id' => $this->id,
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
            'member_id' => $this->id,
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
            'member_id' => $this->id,
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
            'entered_by' => Auth::id(),
            'member_id' => $this->id
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

    public function shareRecord() {
        return $this->hasMany(ShareHistory::class)->orderBy('date', 'desc');
    }

    public function buildingRecord() {
        return $this->hasMany(BuildingHistory::class)->orderBy('date', 'desc');
    }

    public function referred() {
        return $this->hasMany(Loan::class, 'id', 'refer1');
    }

    public function lastSavings() {
        $last = SavingHistory::where('member_id', $this->id)->orderby('created_at', 'desc')->first();
        if ($last) {
            if ($last->credit) {
                return [
                    'status' => 1,
                    'amount' => $last->credit,
                    'date' => $last->date
                ];
            } else {
                return [
                    'status' => 0,
                    'amount' => $last->debit,
                    'date' => $last->date
                ];
            }
        } else {
            return [
                'status' => '',
                'amount' => '',
                'date' => ''
            ];
        }
    }

    public function workSavings() {
        $members = Member::where('id', '>', 205)->get();
//        dd($members);
        foreach($members as $member) {
            if ($member->share) {
                foreach($member->share->history as $item) {
                    $item->update([
                        'member_id' => $member->id
                    ]);
                }

                $c = 0;
                $prev = 0;
                foreach($member->shareRecord->sortBy('date') as $item) {
                    if (!$c) {
                        $prev = $item->credit - $item->debit;
                        $item->update([
                            'balance' => $prev
                        ]);
                    } else {
                        $item->update([
                            'balance' => $prev += ($item->credit - $item->debit)
                        ]);
                    }
                    $c++;
                }
            }
//            if ($member->savings) {
//                foreach($member->savings->history as $item) {
//                    $item->update([
//                        'member_id' => $member->id
//                    ]);
//                }
//
//                $c = 0;
//                $prev = 0;
//                foreach($member->savingRecord->sortBy('date') as $item) {
//                    if (!$c) {
//                        $prev = $item->credit - $item->debit;
//                        $item->update([
//                            'balance' => $prev
//                        ]);
//                    } else {
//                        $item->update([
//                            'balance' => $prev += ($item->credit - $item->debit)
//                        ]);
//                    }
//                    $c++;
//                }
//            }


//        foreach($members as $member) {
//            if ($member->loans) {
//                foreach ($member->loans as $it) {
//                    foreach ($it->repayments as $item) {
//                        $item->update([
//                            'member_id' => $member->id
//                        ]);
//                    }
//                }
//            }
//        }
//
//        foreach($members as $member) {
//            if ($member->building) {
//                foreach ($member->building->history as $item) {
//                    $item->update([
//                        'member_id' => $member->id
//                    ]);
//                }
//            }
        }
    }

    public function postFine($data) {
        $id = $this->fines()->create([
            'credit' => $data['amount'],
            'mode' => $data['mode'],
            'date' => Setting::find(1)->pDate,
            'reason' => $data['reason'],
            'entered_by' => Auth::id(),
        ]);

        if ($data['mode'] == 'savings') {
            $this->debitSavings($data['amount'], 'fine');
        }
        return 2;
    }

    public function debitSavings($saving, $mode, $date = null) {
        $savings = $this->savings();
        if ($savings->doesntExist()) {
            $this->savings()->create([
                'balance' => 0
            ]);
        }
        if (!$date) {
            $date = Setting::find(1)->pDate;
        }
        $savings = $this->savings;
        $savings->balance = $savings->balance - $saving;
        $savings->save();
        SavingHistory::create([
            'date' => $date,
            'saving_id' => $this->savings->id,
            'member_id' => $this->id,
            'debit' => $saving,
            'mode' => $mode,
            'balance' => $this->savings->balance,
            'entered_by' => Auth::id()
        ]);
    }

    public function buyUtil(Array $data) {

        $ret = $this->utilities()->insertGetId([
            'member_id' => $this->member_id,
            'amount' => $data['amount'],
            'mode' => $data['mode'],
            'date' => Setting::find(1)->pDate,
            'name' => $data['type'],
            'entered_by' => Auth::id(),
        ]);
        if ($data['mode'] == 'savings') {
            $this->debitSavings($data['amount'], $data['type']);
        }

        return $ret;
    }

    public function post($data) {

        $loanRepay = $data['loan'] ? (int) str_replace(',', '', $data['loan']) : 0;
        $loanInterest = $data['interest'] ? (int) str_replace(',', '', $data['interest']) : 0;
        $shares = $data['shares'] ? (int) str_replace(',', '', $data['shares']) : 0;
        $savings = $data['savings'] ? (int) str_replace(',', '', $data['savings']) : 0;
        $building = $data['building'] ? (int) str_replace(',', '', $data['building']) : 0;
        $specialSavings = $data['special'] ? (int) str_replace(',', '', $data['special']) : 0;
        $mode = $data['mode'];

//        dd($this->loanRepay, $this->loanInterest, $this->savings, $this->specialSavings, $this->building, $this->shares);
        $total = $loanRepay + $loanInterest + $savings + $specialSavings + $building + $shares;
        if ($savings) {

            if ($mode == 'special') {
                if ($this->getsSaving() < $total) {
                    Notification::make()
                        ->title('Special savings insufficient')
                        ->danger()
                        ->send();
                    return;
                }
                $this->debitSpecial($savings, "savings");
            }
            if ($mode == 'savings') {
                Notification::make()
                    ->title('Cannot use savings to credit savings')
                    ->danger()
                    ->send();
                return;
            }
            $this->creditSavings($savings, $mode);
        }
        if ($specialSavings) {

            if ($mode == 'savings') {
                if ($this->getsaving() < $total) {
                    Notification::make()
                        ->title('Savings insufficient')
                        ->danger()
                        ->send();
                    return;
                }
                $this->debitSavings($specialSavings, "special");
            }
            if ($this->payMethod == 'special') {
                Notification::make()
                    ->title('Cannot use special savings to credit special savings')
                    ->danger()
                    ->send();
                return;
            }
            $this->creditSpecial($specialSavings, $mode);
        }
        if ($building) {

            if ($mode == 'special') {
                if ($this->getsSaving() < $total) {
                    Notification::make()
                        ->title('Special savings insufficient')
                        ->danger()
                        ->send();
                    return;
                }
                $this->debitSpecial($building, "building");
            }
            if ($mode == 'savings') {
                if ($this->getsaving() < $total) {
                    Notification::make()
                        ->title('Savings insufficient')
                        ->danger()
                        ->send();
                    return;
                }
                $this->debitSavings($building, "building");
            }
            $this->creditBuilding($building, $mode);
        }
        if ($shares) {

            if ($mode == 'special') {
                if ($this->getsSaving() < $total) {
                    Notification::make()
                        ->title('Special savings insufficient')
                        ->danger()
                        ->send();
                    return;
                }
                $this->debitSpecial($shares, "shares");
            }
            if ($mode == 'savings') {
                if ($this->getsaving() < $total) {
                    Notification::make()
                        ->title('Savings insufficient')
                        ->danger()
                        ->send();
                    return;
                }
                $this->debitSavings($shares, "shares");
            }
            $this->creditShare($shares, $mode);
        }
        if ($loanRepay) {
            $amount = $loanRepay;
            $interest = $loanInterest;
            $lo = $this->getLoan();
            if ($mode == 'special') {
                if ($this->getsSaving() < $total) {
                    Notification::make()
                        ->title('Special savings insufficient')
                        ->danger()
                        ->send();
                    return;
                }
            }
            if ($mode == 'savings') {
                if ($this->getsaving() < $total) {
                    Notification::make()
                        ->title('Savings insufficient')
                        ->danger()
                        ->send();
                    return;
                }
            }
            if ($amount > $lo) {
                $am = $amount - $lo;
                $this->creditSavings($am, "Loan excesses");
                $this->creditLoan($lo, $interest, $mode);
            } else {
                $this->creditLoan($amount, $interest, $mode);
            }
        }
        Notification::make()
            ->title("Posted successfully!")
            ->success()
            ->send();
    }

    public function takeLoan($data) {

        $date = Setting::find(1)->pDate;
        Loan::create([
            'member_id' => $this->id,
            'duration' => $data['duration'],
            'loan_type' => $data['type'],
            'amount' => str_replace(',', '', $data['amount']),
            'balance' => str_replace(',', '', $data['amount']),
            'lpDate' => $date,
            'Approved_on' => $date,
            'refer1' => $data['surety'][0],
            'refer2' => $data['surety'][1],
            'refer3' => $data['surety'][2] ?? 0,
            'granted_by' => Auth::id(),
            'mode' => $data['mode'],
        ]);
        Notification::make()
            ->title("Loan processed successfully!")
            ->success()
            ->send();
    }
}
