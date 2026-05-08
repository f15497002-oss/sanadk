<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sleep_quality',
        'stress_level',
        'medication_taken',
        'activity_level',
        'entry_date',
    ];

    protected $casts = [
        'medication_taken' => 'boolean',
        'entry_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
