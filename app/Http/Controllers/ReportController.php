<?php

namespace App\Http\Controllers;

use App\Exports\MonthlyAnalysis;
use App\Models\Loan;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    public function analysis() {
        return view('admin.monthlyReport');
    }


    public function downloadAnalysis(Request $request) {
        $input = $request->all();
        $validate = Validator::make($input, [
           'daterange' => 'required',
//           'item' => 'required',
        ]);
        if ($validate->fails()) {
            return back(402)->with('err', 'All fields are important, Please try again.');
        }
        $date = explode(' - ', $request->daterange);
        $start = $date[0];
        $end = $date[1];
        $type = $request->type;
        $start = Carbon::parse($start . ' 00:00:00')->format('Y-m-d h:i:s');
        $end = Carbon::parse($end . ' 23:59:59')->addDay()->format('Y-m-d h:i:s');
//        return (new MonthlyAnalysis($start, $end, $type))->download('monthlyAnalysisReport.xlsx');
        $members = Member::all();

//        dd($members->where('member_id', 108)->load('loans'));
        if ($request->item != 'year') {

            foreach ($members as $member) {
                if ($member->savings) {
                    $savingsC = $member->savings->history->whereBetween('date', [$start, $end])->sum('credit');
                    $savingsD = $member->savings->history->whereBetween('date', [$start, $end])->sum('debit');
                } else {
                    $savingsD = 0;
                    $savingsC = 0;
                }
                if ($member->share) {
                    $shareC = $member->share->history->whereBetween('date', [$start, $end])->sum('credit');
                    $shareD = $member->share->history->whereBetween('date', [$start, $end])->sum('debit');
                } else {
                    $shareD = 0;
                    $shareC = 0;
                }
                if ($member->specialSavings) {
                    $specialC = $member->specialSavings->history->whereBetween('date', [$start, $end])->sum('credit');
                    $specialD = $member->specialSavings->history->whereBetween('date', [$start, $end])->sum('debit');
                } else {
                    $specialD = 0;
                    $specialC = 0;
                }
                if ($member->building) {
                    $buildingC = $member->building->history->whereBetween('date', [$start, $end])->sum('credit');
                    $buildingD = $member->building->history->whereBetween('date', [$start, $end])->sum('debit');
                } else {
                    $buildingD = 0;
                    $buildingC = 0;
                }
                if (!$member->loans->isEmpty()) {
                    $cre = 0;
                    $int = 0;
                    $appLoan = $member->loans->whereBetween('approved_on', [$start, $end])->sum('amount');
                    if ($appLoan) {
                        $appLoan = $appLoan * -1;
                    } else {
                        $appLoan = 0;
                    }
                    $loan = $member->loans;
                    if ($loan->count() > 0) {
                        foreach ($loan as $item) {
                            $cre = $cre + $item->repayments->whereBetween('date', [$start, $end])->sum('credit');
                            $int = $int + $item->repayments->whereBetween('date', [$start, $end])->sum('interest');
                        }
                    }
                } else {
                    $cre = 0;
                    $int = 0;
                    $appLoan = 0;
                }
                if (!$member->fines->isEmpty()) {
//                dd($member->fines);
                    $fines = $member->fines->whereBetween('date', [$start, $end])->sum('credit');
//                dd($fines);
                } else {
                    $fines = 0;
                }
                if (!$member->utilities->isEmpty()) {
                    $util = $member->utilities->whereBetween('date', [$start, $end])->sum('amount');
                } else {
                    $util = 0;
                }
                $member->savingsM = $savingsC - $savingsD;
                $member->shareM = $shareC - $shareD;
                $member->specialM = $specialC - $specialD;
                $member->buildingM = $buildingC - $buildingD;
                $member->loanRepay = $cre;
                $member->loanBalance = $member->getLoan();
                $member->interest = $int;
                $member->fines = $fines;
                $member->appLoan = $appLoan;
                $member->util = $util;
                $member->totalSavings = $member->getSaving();

                $member->sum = $member->savingsM + $member->shareM + $member->appLoan +
                    $member->fines + $member->util + $member->specialM + $member->buildingM + $member->loanRepay + $member->interest;
            }
            $filename = 'backup/' . Carbon::parse($start)->format('M_Y') . '.sqlite';
            if (Storage::disk('backup')->exists($filename)) {
                $old = 'backup/' . Carbon::parse($start)->format('M_Y') . '_old.sqlite';
                if (Storage::disk('backup')->exists($old)) {
                    Storage::disk('backup')->delete($old);
                    Storage::disk('backup')->move($filename, $old);
                } else {
                    Storage::disk('backup')->move($filename, $old);
                }
            }
            Storage::disk('backup')->copy('database.sqlite', $filename);
            return view('admin.monthlyReport2', compact('members'));
        } else {
            $d = Member::where('member_id', 139)->first();

            foreach ($members as $member) {
                if ($member->savings) {
                    $savingsC = $member->savings->history->whereBetween('date', [$start, $end])->sum('credit');
                    $savingsD = $member->savings->history->whereBetween('date', [$start, $end])->sum('debit');
                } else {
                    $savingsD = 0;
                    $savingsC = 0;
                }
                if ($member->share) {
                    $shareC = $member->share->history->sum('credit');
                    $shareD = $member->share->history->sum('debit');
                } else {
                    $shareD = 0;
                    $shareC = 0;
                }
                if ($member->specialSavings) {
                    $specialC = $member->specialSavings->history->whereBetween('date', [$start, $end])->sum('credit');
                    $specialD = $member->specialSavings->history->whereBetween('date', [$start, $end])->sum('debit');
                } else {
                    $specialD = 0;
                    $specialC = 0;
                }
                if ($member->building) {
                    $buildingC = $member->building->history->whereBetween('date', [$start, $end])->sum('credit');
                    $buildingD = $member->building->history->whereBetween('date', [$start, $end])->sum('debit');
                } else {
                    $buildingD = 0;
                    $buildingC = 0;
                }
                if (!$member->loans->isEmpty()) {
                    $cre = 0;
                    $int = 0;
                    $appLoan = $member->loans->whereBetween('approved_on', [$start, $end])->sum('amount');
                    if ($appLoan) {
                        $appLoan = $appLoan * -1;
                    } else {
                        $appLoan = 0;
                    }
                    $loan = $member->loans;
                    if ($loan->count() > 0) {
                        foreach ($loan as $item) {
                            $cre = $cre + $item->repayments->whereBetween('date', [$start, $end])->sum('credit');
                            $int = $int + $item->repayments->whereBetween('date', [$start, $end])->sum('interest');
                        }
                    }
                } else {
                    $cre = 0;
                    $int = 0;
                    $appLoan = 0;
                }
                if (!$member->fines->isEmpty()) {
//                dd($member->fines);
                    $fines = $member->fines->whereBetween('date', [$start, $end])->sum('credit');
//                dd($fines);
                } else {
                    $fines = 0;
                }
                if (!$member->utilities->isEmpty()) {
                    $util = $member->utilities->whereBetween('date', [$start, $end])->sum('amount');
                } else {
                    $util = 0;
                }
                $member->savingsM = $savingsC - $savingsD;
                $member->shareM = $shareC - $shareD;
                $member->specialM = $specialC - $specialD;
                $member->buildingM = $buildingC - $buildingD;
                $member->loanRepay = $cre;
                $member->loanBalance = $member->getLoan();
                $member->interest = $int;
                $member->fines = $fines;
                $member->appLoan = $appLoan;
                $member->util = $util;
                $member->totalSavings = $member->getSaving();
//            $member->att = $member->memberAttendance(Carbon::parse($date[1])->format('m'));
                $member->sum = $member->savingsM + $member->shareM + $member->appLoan +
                    $member->fines + $member->util + $member->specialM + $member->buildingM + $member->loanRepay + $member->interest;
            }


            return view('admin.monthlyReport2', compact('members'));
        }
    }

    public function gen() {
        $d = Loan::where('approved_on', null)->get();
        foreach($d as $i) {
            $i->update([
               'approved_on' => $i->created_at
            ]);
        }
    }

}
