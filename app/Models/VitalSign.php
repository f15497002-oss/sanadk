<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VitalSign extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'heart_rate',
        'eeg_signal',
        'emg_signal',
        'oxygen_level',
        'temperature',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
