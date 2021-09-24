<?php

namespace App\Http\Controllers;

use App\Http\Resources\MemberResource;
use App\Imports\MembersImport;
use App\Models\Member;
use App\Models\Setting;
use App\Models\Utility;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class MemberController extends Controller
{
    public function upload_member() {
        return view('member.upload');
    }

    public function upload(Request $request) {
        $input = $request->all();
        $validate = Validator::make($input, [
            'members' => 'required|file|mimes:xlsx,csv'
        ]);
        if($validate->fails()) {
            return back()->with('error', 'Please upload an excel file, .xlsx or .csv');
        }
        $uploaded = $request->members;
        $fileName = Carbon::now()->timestamp . '.' . $uploaded->getClientOriginalExtension();
        $file = $uploaded->storePubliclyAs(
            'uploads', $fileName, 'public'
        );
        Excel::queueImport(new MembersImport(), $uploaded);
        return redirect('home')->with('suc', 'Members uploaded successfully!');
    }

    public function index() {
        $members = Member::all();
        return view('member.index', compact('members'));
    }

    public function deleted() {
        $members = Member::onlyTrashed()->get();
        return view('member.deleted', compact('members'));
    }

    public function member(Member $member) {
        $prev = Member::where('member_id', '<', $member->member_id)->max('member_id');
        $next = Member::where('member_id', '>', $member->member_id)->min('member_id');
        if (!$prev) {
            $prev = Member::where('member_id', '<', 1000)->max('member_id');
        }
        if (!$next || $next > 1000) {
            $next = Member::where('member_id', '<', 1000)->min('member_id');
        }
        $prev = Member::where('member_id', $prev)->get('id');
        $next = Member::where('member_id', $next)->get('id');
        $members = Member::all();
        $utilities = Utility::all();
        $loan = $member->loans()->where('status', 1)->get();
        if (!$loan->isEmpty()) {
            $loan = $loan[0];
        }
        $pMonth = Setting::firstOrCreate(['id' => 1], [
            'pDate' => carbon::now()->format('Y-m-d')
        ]);
//        dd($pMonth);
        $pMonth = Carbon::parse($pMonth->pDate)->monthName . ', ' . Carbon::parse($pMonth->pDate)->format('Y');
        return view('member.member', compact('member', 'members', 'loan', 'utilities', 'next', 'prev', 'pMonth'));
    }

    public function searchMember(Request $request) {
        $input = $request->all();
        $validate = Validator::make($input, [
            'memberId' => 'required'
        ]);
        if ($validate->fails()) {
            return redirect()->back()->with('err', 'Member ID is compulsory!');
        }
        $member = Member::where('member_id', $request->memberId)->first();
        if (!$member) {
            return redirect()->back()->with('err', 'Member ID not associated with any member');
        }
        return redirect('/member/' . $member->id);
    }

    public function edit(Member $member) {
        $members = Member::all();
        return view('member.edit', compact('member', 'members'));
    }

    public function add_member() {
        $members = Member::all();
        return view('member.create', compact('members'));
    }

    public function editMember(Request $request, Member $member) {
//        dd($request->all());
        $input = $request->all();
        $validate = Validator::make($input, [
            'member_id' => 'required',
//            'name' => 'required',
//            'address' => 'required',
//            'sex' => 'required',
//            'marital' => 'required',
//            'phone' => 'required',
//            'email' => 'required',
//            'profession' => 'required',
//            'purpose' => 'required',
//            'nok' => 'required',
//            'nok_address' => 'required',
//            'nok_phone' => 'required',
        ]);
        if ($validate->fails()) {
            return redirect()->back()->with('errors', $validate->errors());
        }
        $member->update([
            'member_id' => $request->member_id,
            'name' => $request->name,
            'address' => $request->address,
            'sex' => $request->sex,
            'marital' => $request->marital,
            'phone' => $request->phone,
            'phone2' => $request->phone2,
            'email' => $request->email,
            'profession' => $request->profession,
            'purpose' => $request->purpose,
            'referrer' => $request->referrer,
            'nok' => $request->nok,
            'nok_address' => $request->nok_address,
            'nok_phone' => $request->nok_phone,
            'nok_phone2' => $request->nok_phone2,
//            'pix' => $request->member_id . '.jpg'
        ]);
        if ($request->picture) {
            $uploaded = $request->file('picture');
            $fileName = $member->member_id . '.' . $uploaded->getClientOriginalExtension();
            File::put(public_path(). '/img/members/' . $fileName, File::get($uploaded));
            $member->update([
                'pix' => $fileName
            ]);
        }
        return redirect('/member/' . $member->id)->with('suc', 'Member updated successfully!');
    }


    public function createMember(Request $request) {
//        dd($request->all());
        $request->validate([
            'member_id' => 'required|unique:members',
            'picture' => 'nullable|file|mimes:jpg,jpeg,png'
        ]);

        $member = Member::create([
            'member_id' => $request->member_id,
            'name' => $request->name,
            'address' => $request->address,
            'sex' => $request->sex,
            'marital' => $request->marital,
            'phone' => $request->phone,
            'phone2' => $request->phone2,
            'email' => $request->email,
            'profession' => $request->profession,
            'purpose' => $request->purpose,
            'referrer' => $request->referrer,
            'nok' => $request->nok,
            'nok_address' => $request->nok_address,
            'nok_phone' => $request->nok_phone,
            'nok_phone2' => $request->nok_phone2,
        ]);
        if ($request->picture) {
            $uploaded = $request->file('picture');
            $fileName = $member->id . Carbon::now()->timestamp . '.' . $uploaded->getClientOriginalExtension();
            File::put(public_path(). '/img/members/' . $fileName, File::get($uploaded));
        } else {
            $fileName = 'nopix.png';
        }
        $member->update([
           'pix' => $fileName
        ]);
        return redirect('/member/' . $member->id)->with('suc', 'Member created successfully!');
    }

    public function destroy(Request $request) {
        $input = $request->all();
        $validate = Validator::make($input, [
           'id' => 'required'
        ]);
        if ($validate->fails()) {
            return response()->json('Something when wrong, please refresh and try again.', 402);
        }
        $member = Member::find($request->id);
        $member->delete();
        return response()->json('Member deleted successfully', 200);
    }

    public function restore(Request $request) {
        $input = $request->all();
        $validate = Validator::make($input, [
           'id' => 'required'
        ]);
        if ($validate->fails()) {
            return response()->json('Something when wrong, please refresh and try again.', 402);
        }
        $member = Member::withTrashed()->where('id', $request->id)->first();
        if ($member->trashed()) {
            $member->restore();
            return response()->json('Member restored successfully', 200);
        }
        return response()->json('Something when wrong, please refresh and try again.', 402);
    }

    public function perm(Request $request) {
        $input = $request->all();
        $validate = Validator::make($input, [
           'id' => 'required'
        ]);
        if ($validate->fails()) {
            return response()->json('Something when wrong, please refresh and try again.', 402);
        }
        $member = Member::withTrashed()->where('id', $request->id)->first();
        if ($member->trashed()) {
            $member->forceDelete();
            return response()->json('Member deleted successfully', 200);
        }
        return response()->json('Something when wrong, please refresh and try again.', 402);
    }

    public function buyUtil(Request $request) {
        $input = $request->all();
        $validate = Validator::make($input, [
            'type' => 'required',
            'price' => 'required',
            'pay' => 'required',
            'member' => 'required',
        ]);
        if ($validate->fails()) {
            return redirect()->back()->with('valerr', $validate->errors());
        }
        $member = Member::find($request->member);
        $request->price = str_replace(',', '', $request->price);
        $member->utilities()->create([
            'amount' => $request->price,
            'mode' => $request->pay,
            'date' => Setting::find(1)->pDate,
            'name' => $request->type,
            'entered_by' => Auth::id(),
        ]);
        if ($request->pay == 'savings') {
            $member->debitSavings($request->price, $request->type);
        }
        return redirect()->back()->with('suc', 'Utility entered successfully!');
    }

    public function fine(Request $request) {
//        dd($request->all());
        $input = $request->all();
        $validate = Validator::make($input, [
            'amount' => 'required',
            'reason' => 'required',
            'pay' => 'required',
            'member' => 'required',
        ]);
        if ($validate->fails()) {
            return redirect()->back()->with('valerr', $validate->errors());
        }
        $member = Member::find($request->member);
        $request->amount = str_replace(',', '', $request->amount);
        $member->fines()->create([
            'credit' => $request->amount,
            'mode' => $request->pay,
            'date' => Setting::find(1)->pDate,
            'reason' => $request->reason,
            'entered_by' => Auth::id(),
        ]);
        if ($request->pay == 'savings') {
            $member->debitSavings($request->amount, 'fine');
        }
        return redirect()->back()->with('suc', 'Member fined successfully!');
    }
}
