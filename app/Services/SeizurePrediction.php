<?php

namespace App\Services;

use App\Models\User;
use App\Models\Seizure;
use App\Models\VitalSign;
use App\Models\Device;
use App\Models\AppNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use App\Services\OpenAIService;

class SeizurePrediction
{
    protected $openAIService;

    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }
    /**
     * Learn from past seizures to predict future ones.
     */
    public function updateModel(User $user)
    {
        $pastSeizures = Seizure::where('user_id', $user->id)->get();
        
        foreach ($pastSeizures as $seizure) {
            // Analyze vital signs 30 minutes before each seizure
            $preSeizureVitals = VitalSign::where('user_id', $user->id)
                ->where('created_at', '<', $seizure->start_time)
                ->where('created_at', '>', $seizure->start_time->subMinutes(30))
                ->get();
            
            // Logic to identify patterns (Simulation)
            if ($preSeizureVitals->count() > 0) {
                $avgHeartRate = $preSeizureVitals->avg('heart_rate');
                Log::info("Pattern identified for user {$user->id}: Heart rate spikes to {$avgHeartRate} before seizures.");
            }
        }
    }

    public function predict(User $user, $currentVitals)
    {
        // Simple prediction logic based on learned patterns
        if ($currentVitals['heart_rate'] > 105) {
            return [
                'probability' => 0.85,
                'time_to_event' => '10-15 minutes',
                'instructions' => 'يرجى الجلوس في مكان آمن، إرخاء الملابس الضيقة، وإبلاغ من حولك.'
            ];
        }
        
        return ['probability' => 0.1, 'time_to_event' => null];
    }

    /**
     * Analyze real-time data from devices (EEG, ECG, EMG) using AI
     */
    public function analyzeDeviceData(User $user, array $deviceData)
    {
        // Get patient history for better analysis
        $patientHistory = $this->getPatientHistory($user);

        // Use AI for activity detection
        $activity = $this->openAIService->analyzeActivity($deviceData, $patientHistory);

        // Use AI for risk prediction
        $aiRiskPrediction = $this->openAIService->predictSeizureRisk($deviceData, $patientHistory);

        // Use local algorithm for risk prediction
        $localRiskPrediction = $this->calculateLocalRiskPrediction($deviceData, $patientHistory);

        // Compare and combine results
        $combinedPrediction = $this->combinePredictions($aiRiskPrediction, $localRiskPrediction);

        // Get AI recommendations
        $recommendations = $this->openAIService->generateRecommendations(
            $deviceData,
            $combinedPrediction['risk_level'],
            $patientHistory
        );

        $analysis = [
            'activity' => $activity,
            'risk_level' => $combinedPrediction['risk_level'],
            'probability' => $combinedPrediction['probability'],
            'time_to_event' => $combinedPrediction['time_to_event'],
            'recommendations' => $recommendations,
            'emergency_trigger' => $combinedPrediction['risk_level'] === 'high',
            'ai_explanation' => $combinedPrediction['explanation']
        ];

        // Create prediction alerts for family members if risk is medium or high
        if ($combinedPrediction['risk_level'] === 'medium' || $combinedPrediction['risk_level'] === 'high') {
            $this->createPredictionAlert($user, $analysis);
        }

        // Log AI analysis
        Log::info("AI Analysis for user {$user->id}: Activity={$activity}, Risk={$combinedPrediction['risk_level']} ({$combinedPrediction['probability']}), Time={$combinedPrediction['time_to_event']}");

        return $analysis;
    }

    /**
     * Calculate risk prediction using local algorithm
     */
    private function calculateLocalRiskPrediction(array $deviceData, array $patientHistory)
    {
        $riskScore = 0;
        $timeToEvent = 'غير محدد';

        // Analyze EEG data
        if (isset($deviceData['eeg'])) {
            $eeg = $deviceData['eeg'];
            $beta = $eeg['beta'] ?? 0;
            $theta = $eeg['theta'] ?? 0;
            $delta = $eeg['delta'] ?? 0;

            // High beta waves indicate stress/alertness
            if ($beta > 20) {
                $riskScore += 30;
                $timeToEvent = 'خلال 30 دقيقة';
            } elseif ($beta > 15) {
                $riskScore += 20;
                $timeToEvent = 'خلال ساعة';
            }

            // Abnormal theta/delta ratios
            if ($theta > $delta * 2) {
                $riskScore += 25;
                $timeToEvent = 'خلال 45 دقيقة';
            }
        }

        // Analyze ECG data
        if (isset($deviceData['ecg'])) {
            $ecg = $deviceData['ecg'];
            $heartRate = $ecg['heart_rate'] ?? 70;
            $systolic = $ecg['blood_pressure_systolic'] ?? 120;

            // High heart rate
            if ($heartRate > 100) {
                $riskScore += 35;
                $timeToEvent = 'خلال 20 دقيقة';
            } elseif ($heartRate > 90) {
                $riskScore += 20;
                $timeToEvent = 'خلال ساعة';
            }

            // High blood pressure
            if ($systolic > 140) {
                $riskScore += 25;
                $timeToEvent = 'خلال 40 دقيقة';
            }
        }

        // Analyze EMG data
        if (isset($deviceData['emg'])) {
            $emg = $deviceData['emg'];
            $tension = $emg['tension'] ?? 0;
            $nerveSignals = $emg['nerve_signals'] ?? 0;

            // High muscle tension
            if ($tension > 70) {
                $riskScore += 40;
                $timeToEvent = 'خلال 15 دقيقة';
            } elseif ($tension > 50) {
                $riskScore += 25;
                $timeToEvent = 'خلال 30 دقيقة';
            }

            // Abnormal nerve signals
            if ($nerveSignals > 80) {
                $riskScore += 30;
                $timeToEvent = 'خلال 25 دقيقة';
            }
        }

        // Factor in patient history
        $recentSeizures = 0;
        foreach ($patientHistory as $item) {
            if (strpos($item, 'عدد النوبات') !== false) {
                preg_match('/(\d+)/', $item, $matches);
                $recentSeizures = $matches[1] ?? 0;
                break;
            }
        }

        if ($recentSeizures > 5) {
            $riskScore += 20;
        } elseif ($recentSeizures > 2) {
            $riskScore += 10;
        }

        // Determine risk level
        $probability = min(0.95, $riskScore / 100);
        $riskLevel = 'low';
        if ($probability > 0.6) {
            $riskLevel = 'high';
        } elseif ($probability > 0.3) {
            $riskLevel = 'medium';
        }

        return [
            'risk_level' => $riskLevel,
            'probability' => $probability,
            'time_to_event' => $timeToEvent,
            'explanation' => "خوارزمية محلية: خطر {$riskLevel} بنسبة " . round($probability * 100) . "%، {$timeToEvent}"
        ];
    }

    /**
     * Combine AI and local predictions
     */
    private function combinePredictions(array $aiPrediction, array $localPrediction)
    {
        // Weight AI prediction more heavily (70%) vs local (30%)
        $aiWeight = 0.7;
        $localWeight = 0.3;

        $combinedProbability = ($aiPrediction['probability'] * $aiWeight) + ($localPrediction['probability'] * $localWeight);

        // Determine combined risk level
        $riskLevel = 'low';
        if ($combinedProbability > 0.6) {
            $riskLevel = 'high';
        } elseif ($combinedProbability > 0.3) {
            $riskLevel = 'medium';
        }

        // Choose time to event (prefer AI if available, fallback to local)
        $timeToEvent = $aiPrediction['time_to_event'] ?? $localPrediction['time_to_event'];

        // Combine explanations
        $explanation = "تحليل مشترك:\n" .
                      "الذكاء الاصطناعي: {$aiPrediction['explanation']}\n" .
                      "الخوارزمية المحلية: {$localPrediction['explanation']}\n" .
                      "النتيجة المجمعة: خطر {$riskLevel} بنسبة " . round($combinedProbability * 100) . "%، {$timeToEvent}";

        return [
            'risk_level' => $riskLevel,
            'probability' => $combinedProbability,
            'time_to_event' => $timeToEvent,
            'explanation' => $explanation
        ];
    }

    /**
     * Find nearest hospitals using AI location-based search
     */
    public function findNearestHospitals($latitude, $longitude, array $deviceData = [])
    {
        // Use AI to find hospitals based on location and current condition
        return $this->openAIService->findNearbyHospitals($latitude, $longitude, $deviceData);
    }

    /**
     * Trigger emergency alert to family and doctors
     */
    public function triggerEmergency(User $user, $analysis, $location = null)
    {
        $contacts = $user->emergencyContacts;
        $doctors = $user->doctors;

        $message = "تنبيه طوارئ: {$user->name} في خطر محتمل لنوبة صرع. الموقع: " . ($location ? "({$location['lat']}, {$location['lng']})" : 'غير محدد');

        // Send notifications to family
        foreach ($contacts as $contact) {
            $contactUser = $contact->contactUser;
            if (!$contactUser) {
                continue;
            }

            AppNotification::create([
                'user_id' => $contactUser->id,
                'title' => 'تنبيه طوارئ',
                'message' => $message,
                'type' => 'emergency'
            ]);

            // Send email
            Mail::raw($message, function ($mail) use ($contactUser) {
                $mail->to($contactUser->email)->subject('تنبيه طوارئ من سندك - ' . now()->toDateTimeString());
            });
        }

        // Send notifications to doctors
        foreach ($doctors as $doctor) {
            AppNotification::create([
                'user_id' => $doctor->id,
                'title' => 'تنبيه طوارئ لمريض',
                'message' => $message,
                'type' => 'emergency'
            ]);

            Mail::raw($message, function ($mail) use ($doctor) {
                $mail->to($doctor->email)->subject('تنبيه طوارئ لمريض - ' . now()->toDateTimeString());
            });
        }

        // Auto-call family (simulation - in real app, use Twilio or similar)
        $this->autoCallFamily($contacts, $message);

        Log::info("Emergency triggered for user {$user->id}");
    }

    /**
     * Simulate auto-calling family
     */
    private function autoCallFamily($contacts, $message)
    {
        // In real implementation, integrate with phone service
        foreach ($contacts as $contact) {
            Log::info("Auto-calling {$contact->phone} with message: {$message}");
        }
    }

    /**
     * Get patient medical history for AI analysis
     */
    private function getPatientHistory(User $user)
    {
        $history = [];

        // Get recent seizures
        $recentSeizures = Seizure::where('user_id', $user->id)
            ->where('created_at', '>', now()->subMonths(3))
            ->count();

        if ($recentSeizures > 0) {
            $history[] = "عدد النوبات في آخر 3 أشهر: {$recentSeizures}";
        }

        // Get average vital signs
        $avgVitals = VitalSign::where('user_id', $user->id)
            ->where('created_at', '>', now()->subWeek())
            ->selectRaw('AVG(heart_rate) as avg_hr, AVG(oxygen_level) as avg_oxygen, AVG(temperature) as avg_temp')
            ->first();

        if ($avgVitals) {
            $history[] = "متوسط معدل النبض الأسبوعي: " . round($avgVitals->avg_hr ?? 0);
            $history[] = "متوسط تشبع الأكسجين الأسبوعي: " . round($avgVitals->avg_oxygen ?? 0) . "%";
            $history[] = "متوسط درجة الحرارة الأسبوعي: " . round($avgVitals->avg_temp ?? 0) . "°";
        }

        return $history;
    }

    /**
     * Create prediction alerts for family members
     */
    private function createPredictionAlert(User $user, array $analysis)
    {
        // Check if we already sent a prediction alert recently (within last hour)
        $recentAlert = AppNotification::where('user_id', $user->id)
            ->where('type', 'prediction')
            ->where('created_at', '>', now()->subHour())
            ->first();

        if ($recentAlert) {
            return; // Don't spam with alerts
        }

        $contacts = $user->emergencyContacts;
        $message = '����� ����: ' . $user->name . ' �� ��� ' . $analysis['risk_level'] . 
                  ' ����� ' . round($analysis['probability'] * 100) . '%' . 
                  ($analysis['time_to_event'] ? ' - ����� �������: ' . $analysis['time_to_event'] : '');

        // Send notifications to family
        foreach ($contacts as $contact) {
            $contactUser = $contact->contactUser;
            if (!$contactUser) {
                continue;
            }

            AppNotification::create([
                'user_id' => $contactUser->id,
                'title' => '����� ����',
                'message' => $message,
                'type' => 'prediction'
            ]);

            // Send email
            Mail::raw($message, function ($mail) use ($contactUser) {
                $mail->to($contactUser->email)->subject('����� ���� �� ���� - ' . now()->toDateTimeString());
            });
        }

        Log::info('Prediction alert created for user ' . $user->id);
    }
}
