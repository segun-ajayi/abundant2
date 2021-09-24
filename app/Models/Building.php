<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function history() {
        return $this->hasMany(BuildingHistory::class, 'building_id');
    }

    public function member() {
        return $this->belongsTo(Member::class);
    }
}
