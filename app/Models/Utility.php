<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Utility extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function member () {
        return $this->belongsTo(Member::class, 'member_id', 'id');
    }

    public function enteredBy () {
        return $this->belongsTo(User::class, 'entered_by', 'id');
    }

    public function del(Member $member, $record) {
        if ($record->delete()) {
            if ($record['mode'] == 'savings') {
                $member->creditSavings($record['amount'], 'Utility Reversal');
            }
        }
    }
}
