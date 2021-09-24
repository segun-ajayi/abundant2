<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Member;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{

    public function index() {
        return view('admin.attendance');
    }


    public function markAttendance() {
        $pMonth = Setting::firstOrCreate(['id' => 1], [
            'pDate' => carbon::now()->format('Y-m-d')
        ]);
//        dd($pMonth);
        $year = Carbon::now()->format('Y');
        $month = Carbon::parse($pMonth->pDate)->format('m');
        $att = Attendance::where('year', $year)->where('month', $month)->first();
        if (!$att) {
            Attendance::create([
                'year' => $year,
                'month' => $month,
                'date' => Carbon::now()->format('Y-m-d'),
            ]);
        }
        $attendance = Attendance::where('year', $year)->get();
        $members = Member::all();
        $months = [];
        for ($x = 1; $x <= $month; $x++) {
            switch ($x) {
                case 1:
                    $m = 'Jan';
                break;
                case 2:
                    $m = 'Feb';
                break;
                case 3:
                    $m = 'Mar';
                break;
                case 4:
                    $m = 'Apr';
                break;
                case 5:
                    $m = 'May';
                break;
                case 6:
                    $m = 'Jun';
                break;
                case 7:
                    $m = 'Jul';
                break;
                case 8:
                    $m = 'Aug';
                break;
                case 9:
                    $m = 'Sep';
                break;
                case 10:
                    $m = 'Oct';
                break;
                case 11:
                    $m = 'Nov';
                break;
                case 12:
                    $m = 'Dec';
                break;
            }
            array_push($months, $m);
//            foreach ($members as $item) {
////                dd($item->attendance);
////                if (!isset($item->att)) {
////                    $item->att = [];
////                }
////                $att = $item->attendance->where('month', $x)->first();
//////                dd($item);
////                if ($att) {
////                    $item->$x = 1;
////                } else {
////                    $item->$x = 0;
////                }
//                $item->$m = 0;
//            }
        }
//        dd(Carbon::now()->formatLocalized('%b'));
        foreach($members as $item) {
//            dd($item->attendance);
            foreach($item->attendance as $at) {
                $y = $at->year;
                $m = Carbon::parse('2020-' . intval($at->month) . '-20')->formatLocalized('%b');
                if ($y == Carbon::now()->format('Y')) {
                    $item->$m = 1;
                }
            }
        }
        $pMonth = Carbon::parse($pMonth->pDate)->monthName;
        return view('admin.markAttendance', compact('members', 'months', 'pMonth'));
    }

    public function mark(Request $request) {
        $input = $request->all();
        $validate = Validator::make($input, [
            "member" => "required",
            "val" => "required",
            "month" => "required",
        ]);
        if ($validate->fails()) {
            return redirect('/markAttendance')->with('err', 'Something went wrong, please try again');
        }
        $att = Attendance::where('year', Carbon::now()->format('Y'))
                            ->where('month', Carbon::parse($request->month)->format('m'))->first();
        if (!$att) {
            $att = Attendance::create([
                'year' => Carbon::now()->format('Y'),
                'month' => Carbon::parse($request->month)->format('m'),
                'date' => Carbon::now()->format('Y-m-d'),
            ]);
        }
        $att->markAttendance($request->val, $request->member);
        return response([
           'e' => $att
        ]);
    }
}
