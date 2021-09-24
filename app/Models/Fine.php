<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fine extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function member() {
        return $this->belongsToMany(Member::class);
    }
}
