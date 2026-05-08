<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VitalSign;
use App\Models\Seizure;
use App\Services\SeizureDetector;
use App\Models\User;

class ApiController extends Controller
{
    public function updateVitals(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'heart_rate' => 'required|numeric',
            'oxygen_level' => 'required|numeric',
            'temperature' => 'nullable|numeric',
        ]);

        $vitals = VitalSign::create($request->all());

        // Run AI Detection
        $detector = new SeizureDetector();
        $isSeizure = $detector->analyze($vitals);

        if ($isSeizure) {
            Seizure::create([
                'user_id' => $request->user_id,
                'start_time' => now(),
                'is_predicted' => false,
            ]);
            return response()->json(['status' => 'alert', 'message' => 'Seizure detected!']);
        }

        return response()->json(['status' => 'ok', 'data' => $vitals]);
    }

    public function getPatientStatus($id)
    {
        $user = User::findOrFail($id);
        $latestVitals = VitalSign::where('user_id', $id)->latest()->first();
        $activeSeizure = Seizure::where('user_id', $id)->whereNull('end_time')->first();

        return response()->json([
            'name' => $user->name,
            'vitals' => $latestVitals,
            'is_in_seizure' => $activeSeizure ? true : false
        ]);
    }
}
