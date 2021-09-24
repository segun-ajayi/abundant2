<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Share extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function history() {
        return $this->hasMany(ShareHistory::class, 'share_id');
    }

    public function member() {
        return $this->belongsTo(Member::class);
    }
}
