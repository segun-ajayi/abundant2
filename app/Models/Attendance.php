<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $with = ['members'];

//    protected static function boot()
//    {
//        parent::boot();
//
//        // Order by name ASC
//        static::addGlobalScope('order', function (Builder $builder) {
//            $builder->orderBy('month', 'asc');
//        });
//    }

    public function members() {
        return $this->belongsToMany(Member::class);
    }

    public function getMonth() {
        $arr = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul',
            8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'];
//        dd($arr[$this->month]);
        return $arr[$this->month];
    }

    public function markAttendance($mark, $member) {
        if ($mark == 'true') {
            $this->members()->attach($member);
        } else {
            $this->members()->detach($member);
        }
    }
}
