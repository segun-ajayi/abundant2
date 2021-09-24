<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\BuildingHistory;
use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Models\Member;
use App\Models\Saving;
use App\Models\SavingHistory;
use App\Models\Share;
use App\Models\ShareHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('member.searchMember');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $state = true;
        $validate = Validator::make($input, [
            'member' => 'required',
            'mode' => 'required',
        ]);
        if($validate->fails()) {
            return back()->with('error', 'Please enter mode of payment.');
        }
        $member = Member::find($request->member);
        if ($request->savings) {
            $amount = str_replace(',', '', $request->savings);
            $member->creditSavings($amount, $request->mode);
        }
        if ($request->spesavings) {
            $amount = str_replace(',', '', $request->spesavings);
            $member->creditSpecial($amount, $request->mode);
        }
        if ($request->building) {
            $amount = str_replace(',', '', $request->building);
            $member->creditBuilding($amount, $request->mode);
        }
        if ($request->shares) {
            $amount = str_replace(',', '', $request->shares);
            $member->creditShare($amount, $request->mode);
        }
        if ($request->loan) {
            $amount = str_replace(',', '', $request->loan);
            $interest = str_replace(',', '', $request->interest);
            $lo = $member->getLoan();
            if ($amount > $lo) {
                $am = $amount - $lo;
                $member->creditSavings($am, "Loan excesses");
                $state = $member->creditLoan($lo, $interest, $request->mode);
            } else {
                $state = $member->creditLoan($amount, $interest, $request->mode);
            }
        }

        if ($state) {
            return redirect()->back()->with('suc', 'Posted successfully.');
        } else {
            return redirect()->back()->with('err', 'Something went wrong, please check values and try again.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function reLoan(Request $request)
    {
//        dd($request->all());
        if ($request->id == '') {
            return redirect()->back()->with('err', 'Something went wrong, Please try again');
        }
        $loanR = LoanRepayment::find($request->id);
        $loan = $loanR->loan;
        $lastPay = $loan->repayments()->where('id', '<', $request->id)->max('date');
//        dd($lastPay);
        if (!$lastPay) {
            $lastPay = Carbon::now()->subMonth()->format('Y-m-d');
        }
        $loan->update([
            'balance' => $loan->balance + $loanR->credit,
            'lpDate' => $lastPay
        ]);
        $loanR->delete();
        return response([
           'message' => 'Reversal successful',
           'status' => 228
        ]);
    }


    public function RreLoan(Loan $loan)
    {
        $loan->delete();
        return response([
           'message' => 'Reversal successful',
           'status' => 200
        ]);
    }


    public function reSavings(Request $request)
    {
        if ($request->id == '') {
            return redirect()->back()->with('err', 'Something went wrong, Please try again');
        }
        $savingR = SavingHistory::find($request->id);
        $saving = Saving::find($savingR->saving_id);
        $saving->update([
            'balance' => $saving->balance - $savingR->credit + $savingR->debit
        ]);
        $savingR->delete();
        return response([
           'message' => 'Reversal successful',
           'status' => 228
        ]);
    }


    public function reShare(Request $request)
    {
        if ($request->id == '') {
            return redirect()->back()->with('err', 'Something went wrong, Please try again');
        }
        $savingR = ShareHistory::find($request->id);
        $saving = Share::find($savingR->share_id);
        $saving->update([
            'balance' => $saving->balance - $savingR->credit + $savingR->debit
        ]);
        $savingR->delete();
        return response([
           'message' => 'Reversal successful',
           'status' => 228
        ]);
    }


    public function reBuilding(Request $request)
    {
        if ($request->id == '') {
            return redirect()->back()->with('err', 'Something went wrong, Please try again');
        }
        $savingR = BuildingHistory::find($request->id);
        $saving = Building::find($savingR->building_id);
        $saving->update([
            'balance' => $saving->balance - $savingR->credit + $savingR->debit
        ]);
        $savingR->delete();
        return response([
           'message' => 'Reversal successful',
           'status' => 228
        ]);
    }


    public function withdraw(Request $request)
    {
        $request->validate([
           'from' => 'required',
           'amount' => 'required',
           'member' => 'required',
        ]);

        $member = Member::find($request->member);
        if ($request->from == 'saving') {
            $member->debitSavings($request->amount, $request->mode);
        }
        if ($request->from == 'special') {
            $member->debitSpecial($request->amount, $request->mode);
        }
        if ($request->from == 'shares') {
            $member->debitShare($request->amount, $request->mode);
        }

        return redirect()->back()->with('suc', 'Withdrawal made successfully!');
    }
}

