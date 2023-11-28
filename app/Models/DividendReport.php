<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DividendReport extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function member() : BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function dividend() : BelongsTo
    {
        return $this->belongsTo(Dividend::class, 'dividend_id');
    }
}
