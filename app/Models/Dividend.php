<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dividend extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function reports () : HasMany
    {
        return $this->hasMany(DividendReport::class);
    }

    public static function getDivider(int $year): float
    {

        $savings = SavingHistory::whereYear('date', '<=', $year)->sum('credit')
                        - SavingHistory::whereYear('date', '<=', $year)->sum('debit');

        $shares = ShareHistory::whereYear('date', '<=', $year)->sum('credit')
            - ShareHistory::whereYear('date', '<=', $year)->sum('debit');

        $fines = Fine::whereYear('date', '<=', $year)->sum('credit');

        $util = Utility::whereYear('date', '<=', $year)->sum('amount');

        $int = LoanRepayment::whereYear('date', '<=', $year)->sum('interest');

        return round($savings + $shares + $util + $fines + $int, 2);
    }
}
