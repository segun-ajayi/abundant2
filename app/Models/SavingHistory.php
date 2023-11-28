<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavingHistory extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function savings() {
        return $this->belongsTo(Saving::class, 'saving_id');
    }

    public function member() {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function deleteHistory() {
        $saving = $this->savings;
        $saving->update([
            'balance' => $saving->balance - $this->credit + $this->debit
        ]);
        $this->delete();
    }
}
