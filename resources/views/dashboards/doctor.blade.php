<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('لوحة تحكم الطبيب') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(Auth::user()->role === 'patient')
                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-800 p-4 rounded-lg mb-6">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 gap-6">
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-xl font-bold mb-3">طلب ارتباط بالطبيب</h3>
                        <p class="text-gray-500 mb-6">اختر طبيباً لتلقي إشعارات النوبات والقرارات الطبية المهمة.</p>
                        <form method="POST" action="{{ route('doctor.store') }}" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700">اختر الطبيب</label>
                                <select name="doctor_id" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm" required>
                                    <option value="">اختر الطبيب</option>
                                    @foreach($availableDoctors as $doctor)
                                        <option value="{{ $doctor->id }}">{{ $doctor->name }} - {{ $doctor->email }}</option>
                                    @endforeach
                                </select>
                                @error('doctor_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                            </div>
                            <button type="submit" class="btn-modern">طلب الارتباط</button>
                        </form>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-xl font-bold mb-3">الأطباء المرتبطون</h3>
                        @if($linkedDoctors->isEmpty())
                            <p class="text-gray-500">لم يتم ربطك بأي طبيب بعد. أرسل طلب ارتباط لطبيب موثوق.</p>
                        @else
                            <div class="space-y-4">
                                @foreach($linkedDoctors as $doctor)
                                    <div class="border rounded-xl p-4">
                                        <div class="flex justify-between items-center gap-4">
                                            <div>
                                                <p class="font-semibold">{{ $doctor->name }}</p>
                                                <p class="text-sm text-gray-500">{{ $doctor->email }}</p>
                                            </div>
                                            <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs">مرتبط</span>
                                        </div>
                                        <p class="mt-2 text-sm text-gray-500">سيتم إشعار هذا الطبيب عندما تتعرض لنوبة.</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <!-- Quick Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <p class="text-gray-500 text-sm mb-2">إجمالي المرضى</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $patients->count() }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <p class="text-gray-500 text-sm mb-2">حالات نشطة</p>
                        <p class="text-3xl font-bold text-red-600">{{ $activePatientCount }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <p class="text-gray-500 text-sm mb-2">إجمالي النوبات</p>
                        <p class="text-3xl font-bold text-orange-600">{{ $totalSeizures }}</p>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-200">
                        <nav class="flex gap-8 px-6" aria-label="Tabs">
                            <button onclick="showDoctorTab('patients')" class="doctor-tab-button active py-4 px-1 border-b-2 border-blue-500 font-medium text-sm text-blue-600">المرضى</button>
                            <button onclick="showDoctorTab('vitals')" class="doctor-tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">العلامات الحيوية</button>
                            <button onclick="showDoctorTab('seizures')" class="doctor-tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">النوبات</button>
                            <button onclick="showDoctorTab('analysis')" class="doctor-tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">التحليل</button>
                        </nav>
                    </div>

                    <!-- Patients Tab -->
                    <div id="patients-tab" class="doctor-tab-content p-6">
                        <h3 class="text-lg font-bold mb-4">قائمة المرضى</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @forelse($patients as $patient)
                                <div class="border rounded-lg p-4 hover:shadow-md transition">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <h4 class="font-bold text-lg">{{ $patient->name }}</h4>
                                            <p class="text-sm text-gray-500">{{ $patient->email }}</p>
                                        </div>
                                        <span class="px-2 py-1 text-xs rounded-full {{ $patient->seizures()->whereNull('end_time')->exists() ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                            {{ $patient->seizures()->whereNull('end_time')->exists() ? 'نوبة نشطة' : 'مستقر' }}
                                        </span>
                                    </div>
                                    <div class="space-y-2 text-sm">
                                        <p><strong>الهاتف:</strong> {{ $patient->phone }}</p>
                                        <p><strong>العنوان:</strong> {{ $patient->address ?? 'غير محدد' }}</p>
                                        <p><strong>آخر فحص:</strong> {{ $patient->vitalSigns()->latest()->first()?->created_at->diffForHumans() ?? 'لا يوجد' }}</p>
                                    </div>
                                    <div class="mt-4 flex gap-2">
                                        <button class="text-blue-600 hover:underline text-sm" onclick="viewPatientDetails({{ $patient->id }})">عرض التفاصيل</button>
                                        <button class="text-green-600 hover:underline text-sm">إضافة ملاحظة</button>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 col-span-full">لا يوجد مرضى مسجلين</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Vitals Tab -->
                    <div id="vitals-tab" class="doctor-tab-content hidden p-6">
                        <h3 class="text-lg font-bold mb-4">العلامات الحيوية الحالية</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المريض</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">نبض القلب</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">مستوى الأكسجين</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحرارة</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الوقت</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($patients->flatMap(fn($p) => $p->vitalSigns()->latest()->take(1)->get()) as $vital)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $vital->user->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $vital->heart_rate ?? '--' }} BPM</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $vital->oxygen_level ?? '--' }}%</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $vital->temperature ?? '--' }}°C</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $vital->created_at->diffForHumans() }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">لا توجد بيانات</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Seizures Tab -->
                    <div id="seizures-tab" class="doctor-tab-content hidden p-6">
                        <h3 class="text-lg font-bold mb-4">سجل النوبات</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المريض</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التاريخ</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المدة</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">متنبأ بها</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الملاحظات</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse(\App\Models\Seizure::whereIn('user_id', $patients->pluck('id'))->latest()->get() as $seizure)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $seizure->user->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $seizure->start_time->format('Y-m-d H:i') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $seizure->end_time ? $seizure->end_time->diffInMinutes($seizure->start_time) . ' دقيقة' : 'جارية' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $seizure->is_predicted ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $seizure->is_predicted ? 'نعم' : 'لا' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">{{ $seizure->notes ?? '--' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">لا توجد بيانات</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Analysis Tab -->
                    <div id="analysis-tab" class="doctor-tab-content hidden p-6">
                        <h3 class="text-lg font-bold mb-4">التحليل والإحصائيات</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h4 class="font-bold mb-4">معدل النجاح في التنبؤ</h4>
                                <div class="text-4xl font-bold text-blue-600">
                                    {{ $predictionRate ?? 0 }}%
                                </div>
                            </div>
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h4 class="font-bold mb-4">متوسط مدة النوبة</h4>
                                <div class="text-4xl font-bold text-green-600">
                                    {{ round($avgSeizureDuration ?? 0, 1) }} دقيقة
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function showDoctorTab(tabName) {
            document.querySelectorAll('.doctor-tab-content').forEach(tab => {
                tab.classList.add('hidden');
            });
            document.querySelectorAll('.doctor-tab-button').forEach(btn => {
                btn.classList.remove('border-blue-500', 'text-blue-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });

            document.getElementById(tabName + '-tab').classList.remove('hidden');
            event.target.classList.remove('border-transparent', 'text-gray-500');
            event.target.classList.add('border-blue-500', 'text-blue-600');
        }

        function viewPatientDetails(patientId) {
            alert('سيتم عرض تفاصيل المريض رقم: ' + patientId);
        }
    </script>
</x-app-layout>
