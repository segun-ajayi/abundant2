<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function pmonth(Request $request) {
        $jan = Carbon::now()->format('n');
        $month = $request->pMonth;
        if ($jan == 1 && $month == 12) {
            $date = Carbon::now()->subYear()->format('Y') . '/' . $month . '/16';
        } else {
            $date = Carbon::now()->year . '/' . $month . '/16';
        }
        Setting::updateOrCreate(['id' => 1], [
            'pDate' => carbon::parse($date)->format('Y-m-d')
        ]);
        return response([
            'message' => 'Month Changed successfully',
            'status' => 1
        ]);
    }
}
