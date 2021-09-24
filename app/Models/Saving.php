<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saving extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function history() {
        return $this->hasMany(SavingHistory::class);
    }

    public function member() {
        return $this->belongsTo(Member::class);
    }
}
