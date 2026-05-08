<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmergencyContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'contact_user_id',
        'name',
        'phone',
        'relationship',
        'notify_on_prediction',
        'notify_on_seizure',
        'status',
    ];

    protected $casts = [
        'notify_on_prediction' => 'boolean',
        'notify_on_seizure' => 'boolean',
        'status' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contactUser()
    {
        return $this->belongsTo(User::class, 'contact_user_id');
    }
}
