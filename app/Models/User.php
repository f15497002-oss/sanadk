<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'emergency_code',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function seizures()
    {
        return $this->hasMany(Seizure::class);
    }

    public function vitalSigns()
    {
        return $this->hasMany(VitalSign::class);
    }

    public function emergencyContacts()
    {
        return $this->hasMany(EmergencyContact::class);
    }

    public function doctors()
    {
        return $this->belongsToMany(User::class, 'patient_doctors', 'patient_id', 'doctor_id');
    }

    public function patients()
    {
        return $this->belongsToMany(User::class, 'patient_doctors', 'doctor_id', 'patient_id');
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function dailyEntries()
    {
        return $this->hasMany(DailyEntry::class);
    }

    public function appNotifications()
    {
        return $this->hasMany(AppNotification::class);
    }
}
