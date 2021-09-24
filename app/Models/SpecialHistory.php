<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialHistory extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function special() {
        return $this->belongsTo(Special::class);
    }

    public function deleteHistory() {
        $saving = $this->special;
        $saving->update([
            'balance' => $saving->balance - $this->credit + $this->debit
        ]);
        $this->delete();
    }
}
