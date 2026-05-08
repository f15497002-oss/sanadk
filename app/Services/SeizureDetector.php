<?php

namespace App\Services;

use App\Models\VitalSign;
use App\Models\Seizure;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SeizureDetector
{
    /**
     * Analyze vital signs to detect or predict seizures.
     */
    public function analyze(User $user, array $data)
    {
        // Save vital signs
        $vitalSign = VitalSign::create([
            'user_id' => $user->id,
            'heart_rate' => $data['heart_rate'] ?? null,
            'eeg_signal' => $data['eeg_signal'] ?? null,
            'emg_signal' => $data['emg_signal'] ?? null,
            'oxygen_level' => $data['oxygen_level'] ?? null,
            'temperature' => $data['temperature'] ?? null,
        ]);

        $results = [
            'seizure_detected' => false,
            'prediction_score' => 0,
            'alert_level' => 'normal',
        ];

        // Simple logic for simulation (can be replaced with real AI model call)
        if (($data['heart_rate'] ?? 0) > 120 && ($data['eeg_signal'] ?? 0) > 0.8) {
            $results['seizure_detected'] = true;
            $results['alert_level'] = 'emergency';
            
            // Log seizure
            Seizure::create([
                'user_id' => $user->id,
                'start_time' => now(),
                'is_predicted' => false,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
            ]);
        } elseif (($data['heart_rate'] ?? 0) > 100 || ($data['eeg_signal'] ?? 0) > 0.6) {
            $results['prediction_score'] = 0.75;
            $results['alert_level'] = 'warning';
        }

        return $results;
    }
}
