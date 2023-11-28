<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShareHistory extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function share() {
        return $this->belongsTo(Share::class, 'share_id');
    }

    public function member() {
        return $this->belongsTo(Member::class);
    }

    public function deleteHistory() {
        $saving = $this->share;
        $saving->update([
            'balance' => $saving->balance - $this->credit + $this->debit
        ]);
        $this->delete();
    }
}
