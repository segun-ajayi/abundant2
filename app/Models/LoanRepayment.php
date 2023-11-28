<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanRepayment extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function loan() {
        return $this->belongsTo(Loan::class, 'loan_id');
    }

    public function deleteHistory() {
        $loan = $this->loan;
        $lastPay = $loan->repayments()->where('id', '<', $this->id)->max('date');
        if (!$lastPay) {
            $lastPay = Carbon::now()->subMonth()->format('Y-m-d');
        }
        $this->delete();
        $loan->update([
            'balance' => $loan->balance + $this->credit,
            'lpDate' => $lastPay
        ]);
    }
}
