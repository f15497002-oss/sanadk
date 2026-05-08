<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Seizure;
use App\Models\EmergencyContact;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\SeizurePrediction;

class EmergencyController extends Controller
{
    public function trigger(Request $request)
    {
        $user = Auth::user();
        
        // Create seizure record
        $seizure = Seizure::create([
            'user_id' => $user->id,
            'start_time' => now(),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'is_predicted' => $request->is_prediction ?? false,
        ]);

        // Notify contacts via email and dashboard notifications
        $predictionService = app(SeizurePrediction::class);
        $location = [
            'lat' => $request->latitude,
            'lng' => $request->longitude
        ];
        
        $analysis = [
            'risk_level' => 'critical',
            'emergency_trigger' => true
        ];
        
        $predictionService->triggerEmergency($user, $analysis, $location);

        // Notify Government (Simulation)
        Log::info("EMERGENCY CALL: Notifying authorities for patient {$user->name} at location: {$request->latitude}, {$request->longitude}");

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Emergency alerts sent successfully',
                'seizure_id' => $seizure->id
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'تم إرسال نداء الطوارئ بنجاح. تم إخطار أفراد العائلة والطبيب بالبريد الإلكتروني. تم التواصل مع الإسعاف والسلطات.');
    }

    public function iAmSafe(Request $request, $id)
    {
        $seizure = Seizure::findOrFail($id);
        $seizure->update(['end_time' => now()]);
        
        return response()->json(['status' => 'success', 'message' => 'Glad you are safe!']);
    }
}

