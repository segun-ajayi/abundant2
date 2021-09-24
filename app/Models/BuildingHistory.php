<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildingHistory extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function building() {
        return $this->belongsTo(Building::class);
    }

    public function deleteHistory() {
        $saving = $this->building;
        $saving->update([
            'balance' => $saving->balance - $this->credit + $this->debit
        ]);
        $this->delete();
    }
}
