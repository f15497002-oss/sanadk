<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('لوحة تحكم الإدارة') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">إجمالي المرضى</p>
                            <p class="text-3xl font-bold text-blue-600">{{ $totalPatients }}</p>
                        </div>
                        <div class="text-blue-600 text-4xl">👥</div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">الأطباء المسجلون</p>
                            <p class="text-3xl font-bold text-green-600">{{ $totalDoctors }}</p>
                        </div>
                        <div class="text-green-600 text-4xl">👨‍⚕️</div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">النوبات المسجلة</p>
                            <p class="text-3xl font-bold text-red-600">{{ $totalSeizures }}</p>
                        </div>
                        <div class="text-red-600 text-4xl">⚠️</div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">النوبات اليوم</p>
                            <p class="text-3xl font-bold text-orange-600">{{ $todaySeizures }}</p>
                        </div>
                        <div class="text-orange-600 text-4xl">📊</div>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-200">
                    <nav class="flex gap-8 px-6" aria-label="Tabs">
                        <button onclick="showTab('patients')" class="tab-button active py-4 px-1 border-b-2 border-blue-500 font-medium text-sm text-blue-600">المرضى</button>
                        <button onclick="showTab('doctors')" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">الأطباء</button>
                        <button onclick="showTab('seizures')" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">النوبات</button>
                        <button onclick="showTab('reports')" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">التقارير</button>
                    </nav>
                </div>

                <!-- Patients Tab -->
                <div id="patients-tab" class="tab-content p-6">
                    <h3 class="text-lg font-bold mb-4">قائمة المرضى</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الاسم</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">البريد الإلكتروني</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الهاتف</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">عدد النوبات</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($patients as $patient)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $patient->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $patient->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $patient->phone ?? 'غير متوفر' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $patient->seizures->count() }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <button class="text-blue-600 hover:text-blue-900">تفاصيل</button>
                                        </td>
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

                <!-- Doctors Tab -->
                <div id="doctors-tab" class="tab-content hidden p-6">
                    <h3 class="text-lg font-bold mb-4">قائمة الأطباء</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الاسم</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">البريد الإلكتروني</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الهاتف</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">عدد المرضى</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($doctors as $doctor)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $doctor->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $doctor->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $doctor->phone ?? 'غير متوفر' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $doctor->patients->count() }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <button class="text-blue-600 hover:text-blue-900">تفاصيل</button>
                                        </td>
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
                <div id="seizures-tab" class="tab-content hidden p-6">
                    <h3 class="text-lg font-bold mb-4">سجل النوبات</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المريض</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التاريخ والوقت</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المدة</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">النوع</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">متنبأ بها</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse(\App\Models\Seizure::latest()->take(20)->get() as $seizure)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $seizure->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $seizure->start_time->format('Y-m-d H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $seizure->end_time ? $seizure->end_time->diffInMinutes($seizure->start_time) . ' دقيقة' : 'جارية' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $seizure->type ?? 'غير محدد' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $seizure->is_predicted ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $seizure->is_predicted ? 'نعم' : 'لا' }}
                                            </span>
                                        </td>
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

                <!-- Reports Tab -->
                <div id="reports-tab" class="tab-content hidden p-6">
                    <h3 class="text-lg font-bold mb-4">التقارير الإحصائية</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h4 class="font-bold mb-4">نسبة النوبات المتنبأ بها</h4>
                            <div class="text-4xl font-bold text-blue-600">{{ $predictedRate }}%</div>
                        </div>
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h4 class="font-bold mb-4">متوسط مدة النوبة</h4>
                            <div class="text-4xl font-bold text-green-600">{{ $averageDuration }} دقيقة</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.add('hidden');
            });
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('border-blue-500', 'text-blue-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });

            // Show selected tab
            document.getElementById(tabName + '-tab').classList.remove('hidden');
            event.target.classList.remove('border-transparent', 'text-gray-500');
            event.target.classList.add('border-blue-500', 'text-blue-600');
        }
    </script>
</x-app-layout>
