<?php

namespace App\Http\Controllers;

use App\Models\Utility;
use Illuminate\Http\Request;

class UtilityController extends Controller
{
    public function index(){
        $utilities = Utility::all();
        return view('member.utility', compact('utilities'));
    }
}
