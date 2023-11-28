<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/admin/login');
});

Route::middleware(['auth:sanctum'])->group(function() {
    Route::get('/manageMember/{record}', function (\App\Models\Member $record) {
        return redirect(\App\Filament\Resources\MemberResource::getUrl('manage', [$record]));
    })->name('member.manage');

    Route::get('/manage/{member}', function (\App\Models\Member $member) {
        return view('filament.resources.member-resource.pages.manage-member', ['member' => $member]);
    });
});
