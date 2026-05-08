<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\AppNotification;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $patients = User::where('role', 'patient')->get();

        foreach ($patients as $patient) {
            AppNotification::create([
                'user_id' => $patient->id,
                'title' => 'تنبيه طوارئ',
                'message' => 'نوبة صرعية قيد الحدوث',
                'type' => 'emergency',
                'is_read' => false
            ]);

            AppNotification::create([
                'user_id' => $patient->id,
                'title' => 'تنبيه تنبؤ',
                'message' => 'احتمال نوبة صرعية عالي',
                'type' => 'prediction',
                'is_read' => false
            ]);

            AppNotification::create([
                'user_id' => $patient->id,
                'title' => 'انتهت النوبة',
                'message' => 'انتهت نوبة بعد 3 دقائق',
                'type' => 'recovery',
                'is_read' => true
            ]);
        }
    }
}
