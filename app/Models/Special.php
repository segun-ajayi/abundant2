<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Special extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function member() {
        return $this->belongsTo(Member::class);
    }

    public function history() {
        return $this->hasMany(SpecialHistory::class);
    }
}
