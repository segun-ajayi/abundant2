<?php

use App\Http\Controllers\AttendanceController as AttendanceController;
use App\Http\Controllers\HomeController as HomeController;
use App\Http\Controllers\LoanController as LoanController;
use App\Http\Controllers\MemberController as MemberController;
use App\Http\Controllers\PostController as PostController;
use App\Http\Controllers\ReportController as ReportController;
use App\Http\Controllers\SettingController as SettingController;
use App\Http\Controllers\UserController as UserController;
use App\Http\Controllers\UtilityController as UtilityController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware(['auth:sanctum', 'verified'])->group(function (){
    Route::get('/home', [HomeController::class, 'index'])->name('home');

// Members
    Route::get('/members', [MemberController::class, 'index'])->name('members');
    Route::get('/member/{member}', [MemberController::class, 'member'])->name('member');
    Route::get('/edit_members/{member}', [MemberController::class, 'edit'])->name('edit_member');
    Route::post('/edit_member/{member}', [MemberController::class, 'editMember'])->name('editMember');
    Route::post('/fine_member', [MemberController::class, 'fine'])->name('fine');
    Route::post('/but_util', [MemberController::class, 'buyUtil'])->name('buyUtil');
    Route::post('/searchMember', [MemberController::class, 'searchMember'])->name('searchMember');
    Route::post('/createMember', [MemberController::class, 'createMember'])->name('createMember');
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance');
    Route::get('/markAttendance', function () {
        return view('admin.markAttendance');
    })->name('markAttendance');
    Route::post('/mark', [AttendanceController::class, 'mark'])->name('markee');

// Posts
    Route::get('/post', [PostController::class, 'index'])->name('post');
    Route::post('/postInc', [PostController::class, 'store'])->name('postInc');
    Route::post('/pmonth', [SettingController::class, 'pmonth'])->name('pmonth');

// Loans
    Route::get('/loan', [LoanController::class, 'index'])->name('loan');
    Route::post('/giveLoan', [LoanController::class, 'store'])->name('giveLoan');

// Reports
    Route::get('/dividend_report', [ReportController::class, 'dividend'])->name('dividendReport');
    Route::get('/monthly_analysis', [ReportController::class, 'analysis'])->name('analysis');
    Route::post('/monthly_analysis', [ReportController::class, 'downloadAnalysis'])->name('downloadAnalysis');
    Route::post('/posting_report', [ReportController::class, 'postingReport'])->name('postingReport');

// Utility
    Route::get('/utility', [UtilityController::class, 'index'])->name('index');

// Settings
    Route::get('/my_profile/{member}', [MemberController::class, 'my_profile'])->name('my_profile');

    Route::middleware(['admin'])->group(function() {
        Route::get('/add_member', [MemberController::class, 'add_member'])->name('add_member');
        Route::get('/upload_member', [MemberController::class, 'upload_member'])->name('upload_member');
        Route::post('/upload_member', [MemberController::class, 'upload'])->name('upload');
        Route::post('/withdraw', [PostController::class, 'withdraw'])->name('withdraw');
        Route::get('/dividend', function() {
            return view('admin.dividend');
        })->name('dividend');

        Route::post('/delete_members/', [MemberController::class, 'destroy'])->name('delete_member');
        Route::post('/perm_member/', [MemberController::class, 'perm']);
        Route::post('/restore_member/', [MemberController::class, 'restore']);
        Route::get('/deleted_members', [MemberController::class, 'deleted'])->name('deleted_members');

        //Excos (Users)
//        Route::get('/excos', [UserController::class, 'index'])->name('index');
        Route::post('/remove_exco', [UserController::class, 'remove_exco'])->name('remove_exco');
        Route::post('/changePassword', [UserController::class, 'changePassword'])->name('changePassword');
        Route::post('/mkExco', [UserController::class, 'mkExco'])->name('mkExco');
        Route::post('/reLoan', [PostController::class, 'reLoan'])->name('reloan');
        Route::get('/RreLoan/{loan}', [PostController::class, 'RreLoan'])->name('rr');
        Route::post('/reSavings', [PostController::class, 'reSavings'])->name('reSavings');
        Route::post('/reShare', [PostController::class, 'reShare'])->name('reShare');
        Route::post('/reBuilding', [PostController::class, 'reBuilding'])->name('reBuilding');
        Route::get('/revokeAdmin/{user}', [UserController::class, 'revokeAdmin'])->name('revokeAdmin');
        Route::get('/makeAdmin/{user}', [UserController::class, 'makeAdmin'])->name('makeAdmin');
    });
});
