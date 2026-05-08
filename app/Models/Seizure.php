<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seizure extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'start_time',
        'end_time',
        'type',
        'notes',
        'is_predicted',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_predicted' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
