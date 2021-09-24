<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Member;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $validate = Validator::make($input, [
            'type' => 'required',
            'duration' => 'required',
            'amount' => 'required',
            'surety' => 'required',
            'member' => 'required',
            'mode' => 'required',
        ]);
        if($validate->fails()) {
            return back()->with('error', 'Please fill all fields.');
        }
        $member = Member::find($request->member);
        $date = Setting::find(1)->pDate;
        $loan = Loan::create([
            'member_id' => $request->member,
            'duration' => $request->duration,
            'loan_type' => $request->type,
            'amount' => str_replace(',', '', $request->amount),
            'balance' => str_replace(',', '', $request->amount),
            'lpDate' => $date,
            'Approved_on' => $date,
            'refer1' => $request->surety[0],
            'refer2' => $request->surety[1],
            'granted_by' => Auth::id(),
            'mode' => $request->mode,
        ]);
        if ($loan) {
            return redirect()->back()->with('suc', 'Loan approved successfully');
        } else {
            return redirect()->back()->with('err', 'Loan approval failed, please refresh page and try again');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Loan  $loan
     * @return \Illuminate\Http\Response
     */
    public function show(Loan $loan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Loan  $loan
     * @return \Illuminate\Http\Response
     */
    public function edit(Loan $loan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Loan  $loan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Loan $loan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Loan  $loan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Loan $loan)
    {
        //
    }
}
