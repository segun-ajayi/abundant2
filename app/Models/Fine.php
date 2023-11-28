<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Fine extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function member() {
        return $this->belongsToMany(Member::class);
    }

    public function enteredBy() {
        return $this->belongsTo(User::class, 'entered_by', 'id');
    }

    public function del($record) {
        $mem = DB::table('fine_member')->where('fine_id', $record['id'])->first();
        $member = Member::find($mem->member_id);
        if ($record->delete()) {
            if ($record['mode'] == 'savings') {
                $member->creditSavings($record['credit'], 'Fine Reversal');
            }
        }
    }
}
