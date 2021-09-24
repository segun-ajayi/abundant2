<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index() {
        $excos = User::all();
        return view('member.excos', compact('excos'));
    }

    public function remove_exco(Request $request) {
        $input = $request->all();
        $validate = Validator::make($input, [
            'id' => 'required'
        ]);
        if ($validate->fails()) {
            return response()->json('Something when wrong, please refresh and try again.', 402);
        }
        $member = Member::find($request->id);
        $user = User::find($member->user_id);
        $user->delete();
        $member->update([
            'user_id' => 0
        ]);
        return response()->json('Member removed from excos successfully', 200);
    }

    public function mkExco(Request $request) {
        $input = $request->all();
        $validate = Validator::make($input, [
            'username' => 'required',
            'member' => 'required'
        ]);
        if ($validate->fails()) {
            return redirect()->back()->with('err', 'Username is compulsory.');
        }
        $member = Member::find($request->member);
        $user = User::create([
           'username' => $request->username,
           'password' => bcrypt('welcome123'),
        ]);
        $member->update([
            'user_id' => $user->id
        ]);
        return redirect()->back()->with('suc', 'Member made excos successfully');
    }

    public function changePassword(Request $request) {
        $input = $request->all();
        $validate = Validator::make($input, [
            'old_password' => 'required',
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        if ($validate->fails()) {
            return response()->json($validate->errors(), 419);
        }
        $user = Auth::user();
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['old_password' => 'Old password does not match our records.'], 419);
        }
        $user->update([
            'password' => bcrypt($request->password)
        ]);
        return response()->json(['suc' => 'Password changed successfully.'], 200);
    }

    public function revokeAdmin(User $user) {
        $user->update([
            'role' => 'user'
        ]);
        return 'Administrator privileges removed!';
    }

    public function makeAdmin(User $user) {
        $user->update([
            'role' => 'admin'
        ]);
        return 'Administrator privileges granted!';
    }
}
