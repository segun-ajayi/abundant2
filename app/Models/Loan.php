<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function repayments() {
        return $this->hasMany(LoanRepayment::class);
    }

    public function member() {
        return $this->belongsTo(Member::class);
    }

    public function referer() {
        return $this->belongsTo(Member::class, 'refer1', 'id');
    }

    public function referer2() {
        return $this->belongsTo(Member::class, 'refer2', 'id');
    }

    public function referer3() {
        return $this->belongsTo(Member::class, 'refer3', 'id');
    }
}
