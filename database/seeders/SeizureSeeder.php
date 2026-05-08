<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Seizure;
use App\Models\VitalSign;
use Illuminate\Support\Facades\Hash;

class SeizureSeeder extends Seeder
{
    public function run(): void
    {
        // Create Patient
        $patient = User::create([
            'name' => 'أحمد محمد',
            'email' => 'patient@sanadk.com',
            'password' => Hash::make('password'),
            'role' => 'patient',
            'phone' => '0500000001',
        ]);

        // Create Doctor
        $doctor = User::create([
            'name' => 'د. سارة خالد',
            'email' => 'doctor@sanadk.com',
            'password' => Hash::make('password'),
            'role' => 'doctor',
            'phone' => '0500000002',
        ]);

        // Link Patient to Doctor
        $patient->doctors()->attach($doctor->id);

        // Create Family Member
        $family = User::create([
            'name' => 'خالد محمد',
            'email' => 'family@sanadk.com',
            'password' => Hash::make('password'),
            'role' => 'family',
            'phone' => '0500000003',
        ]);

        // Add some sample data
        VitalSign::create([
            'user_id' => $patient->id,
            'heart_rate' => 75,
            'oxygen_level' => 98,
            'temperature' => 37.0,
        ]);

        Seizure::create([
            'user_id' => $patient->id,
            'start_time' => now()->subDays(2),
            'end_time' => now()->subDays(2)->addMinutes(3),
            'is_predicted' => false,
        ]);
    }
}
