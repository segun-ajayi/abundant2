<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function setMonth(array $data) {

        $year = Carbon::now()->format('Y');

        $date = $data['year'] . '/' . $data['month'] . '/16';
        Setting::updateOrCreate(['id' => 1], [
            'pDate' => carbon::parse($date)->format('Y-m-d')
        ]);
    }
}
