<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $with = ['members'];

    public function members() {
        return $this->belongsToMany(Member::class);
    }

    public function markAttendance($mark, $member) {
        if ($mark == 'true') {
            $this->members()->attach($member);
        } else {
            $this->members()->detach($member);
        }
    }
}
