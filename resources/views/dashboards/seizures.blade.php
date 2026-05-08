<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/modern-ui.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    @include('components.header-nav', ['title' => 'سجل النوبات'])

    <div class="p-5">
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="text-gray-600"><i class="fas fa-chevron-right"></i></a>
                <h2 class="text-xl font-bold">سجل النوبات</h2>
            </div>
            <a href="{{ route('data-entry') }}" class="w-10 h-10 bg-primary text-white rounded-full flex items-center justify-center shadow-lg">
                <i class="fas fa-plus"></i>
            </a>
        </div>

        <div class="bg-white p-6 rounded-3xl shadow-sm mb-6 text-center">
            @php
                $seizuresRiskScore = is_numeric($riskScore) ? (int) round($riskScore) : 0;
                $seizuresRiskLabel = $seizuresRiskScore >= 70 ? 'عالي' : ($seizuresRiskScore >= 40 ? 'متوسط' : 'منخفض');
            @endphp
            <p class="text-sm text-gray-500 mb-2">مؤشر الخطر الحالي</p>
            <div class="circular-progress mx-auto mb-3" style="background: conic-gradient(var(--primary) {{ $seizuresRiskScore }}%, #EDF2F7 0deg); width: 140px; height: 140px;">
                <div class="progress-value">{{ $seizuresRiskScore }}</div>
            </div>
            <p class="text-sm font-bold text-gray-700">{{ $seizuresRiskLabel }}</p>
            <p class="text-xs text-gray-500 mt-2">احتمال حدوث نوبة خلال 30 دقيقة القادمة {{ $seizuresRiskLabel }}.</p>
        </div>

        <div class="flex gap-8 border-b border-gray-100 mb-6">
            <button id="tab-records" type="button" class="pb-4 border-b-2 border-primary text-primary font-bold text-sm" onclick="switchSeizureTab('records')">سجل النوبات</button>
            <button id="tab-device-data" type="button" class="pb-4 text-gray-400 text-sm" onclick="switchSeizureTab('device-data')">بيانات الأجهزة</button>
            <button id="tab-stats" type="button" class="pb-4 text-gray-400 text-sm" onclick="switchSeizureTab('stats')">إحصائيات</button>
        </div>

        @php
            $totalSeizures = $seizures->count();
            $averageDuration = $seizures->whereNotNull('end_time')->map(fn($s) => $s->end_time->diffInMinutes($s->start_time))->average() ?? 0;
            $activeSeizures = $seizures->whereNull('end_time')->count();
            $predictedSeizures = $seizures->where('is_predicted', true)->count();
        @endphp

        <div id="recordsTab" class="space-y-4">
            @forelse($seizures as $seizure)
                <div class="bg-white p-4 rounded-2xl shadow-sm flex items-center gap-4 border-r-4 {{ $seizure->intensity === 'high' ? 'border-red-500' : ($seizure->intensity === 'medium' ? 'border-orange-400' : 'border-green-400') }}">
                    <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center text-gray-300">
                        <i class="fas fa-wave-square"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-center mb-1">
                            <h4 class="font-bold text-sm">{{ $seizure->start_time->format('d M Y') }}</h4>
                            <span class="text-[10px] text-gray-400">{{ $seizure->start_time->format('H:i') }}</span>
                        </div>
                        <p class="text-[10px] text-gray-500 mb-1">المدة: {{ $seizure->end_time ? $seizure->end_time->diffInMinutes($seizure->start_time) . ' دقيقة' : 'جارية' }}</p>
                        <div class="flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full {{ $seizure->intensity === 'high' ? 'bg-red-500' : ($seizure->intensity === 'medium' ? 'bg-orange-400' : 'bg-green-400') }}"></span>
                            <span class="text-[10px] {{ $seizure->intensity === 'high' ? 'text-red-500 font-bold' : ($seizure->intensity === 'medium' ? 'text-orange-400' : 'text-green-400') }}">{{ $seizure->intensity === 'high' ? 'شدة عالية' : ($seizure->intensity === 'medium' ? 'شدة متوسطة' : 'شدة منخفضة') }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <i class="fas fa-clipboard-list text-gray-300 text-4xl mb-4"></i>
                    <p class="text-gray-500">لا توجد نوبات مسجلة</p>
                </div>
            @endforelse
        </div>

        <div id="deviceDataTab" class="hidden space-y-4">
            <div class="bg-white p-6 rounded-3xl shadow-sm">
                <h4 class="font-bold mb-4">بيانات الأجهزة الحية</h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-primary" id="liveHeartRate">--</div>
                        <div class="text-xs text-gray-500">معدل النبض</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-primary" id="liveBloodPressure">--/--</div>
                        <div class="text-xs text-gray-500">ضغط الدم</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-primary" id="liveMuscleTension">--</div>
                        <div class="text-xs text-gray-500">توتر العضلات</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-primary" id="liveBrainActivity">--</div>
                        <div class="text-xs text-gray-500">نشاط المخ</div>
                    </div>
                </div>
                <div class="text-center">
                    <button id="refreshDeviceData" class="btn-modern-secondary text-sm">
                        <i class="fas fa-sync-alt"></i> تحديث البيانات
                    </button>
                </div>
            </div>
        </div>

        <div id="statsTab" class="hidden space-y-4">
            <div class="grid gap-4 md:grid-cols-3">
                <div class="bg-white p-6 rounded-3xl shadow-sm">
                    <h4 class="font-bold mb-2">مجموع النوبات</h4>
                    <p class="text-3xl font-bold text-primary">{{ $totalSeizures }}</p>
                </div>
                <div class="bg-white p-6 rounded-3xl shadow-sm">
                    <h4 class="font-bold mb-2">متوسط المدة</h4>
                    <p class="text-3xl font-bold text-primary">{{ round($averageDuration, 1) }} دقيقة</p>
                </div>
                <div class="bg-white p-6 rounded-3xl shadow-sm">
                    <h4 class="font-bold mb-2">النوبات النشطة</h4>
                    <p class="text-3xl font-bold text-primary">{{ $activeSeizures }}</p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-3xl shadow-sm">
                <h4 class="font-bold mb-2">النوبات المتوقعة</h4>
                <p class="text-sm text-gray-500 mb-3">عدد النوبات المسجلة على أنها متوقعة.</p>
                <span class="text-2xl font-bold text-secondary">{{ $predictedSeizures }}</span>
            </div>
        </div>
    </div>

    <script>
        function switchSeizureTab(tab) {
            const records = document.getElementById('recordsTab');
            const deviceData = document.getElementById('deviceDataTab');
            const stats = document.getElementById('statsTab');
            const recordsBtn = document.getElementById('tab-records');
            const deviceDataBtn = document.getElementById('tab-device-data');
            const statsBtn = document.getElementById('tab-stats');

            // Hide all tabs
            records.classList.add('hidden');
            deviceData.classList.add('hidden');
            stats.classList.add('hidden');

            // Reset all buttons
            recordsBtn.classList.remove('border-b-2', 'border-primary', 'text-primary', 'font-bold');
            recordsBtn.classList.add('text-gray-400');
            deviceDataBtn.classList.remove('border-b-2', 'border-primary', 'text-primary', 'font-bold');
            deviceDataBtn.classList.add('text-gray-400');
            statsBtn.classList.remove('border-b-2', 'border-primary', 'text-primary', 'font-bold');
            statsBtn.classList.add('text-gray-400');

            // Show selected tab and activate button
            if (tab === 'records') {
                records.classList.remove('hidden');
                recordsBtn.classList.add('border-b-2', 'border-primary', 'text-primary', 'font-bold');
                recordsBtn.classList.remove('text-gray-400');
            } else if (tab === 'device-data') {
                deviceData.classList.remove('hidden');
                deviceDataBtn.classList.add('border-b-2', 'border-primary', 'text-primary', 'font-bold');
                deviceDataBtn.classList.remove('text-gray-400');
            } else if (tab === 'stats') {
                stats.classList.remove('hidden');
                statsBtn.classList.add('border-b-2', 'border-primary', 'text-primary', 'font-bold');
                statsBtn.classList.remove('text-gray-400');
            }
        }

        // Live device data update function
        async function updateLiveData() {
            try {
                const response = await fetch('/devices/live-data');
                const data = await response.json();

                if (data.success) {
                    document.getElementById('liveHeartRate').textContent = data.heart_rate || '--';
                    document.getElementById('liveBloodPressure').textContent = `${data.blood_pressure_systolic || '--'}/${data.blood_pressure_diastolic || '--'}`;
                    document.getElementById('liveMuscleTension').textContent = data.muscle_tension || '--';
                    document.getElementById('liveBrainActivity').textContent = data.brain_activity || '--';
                }
            } catch (error) {
                console.error('Error updating live data:', error);
            }
        }

        // Update data every 30 seconds
        setInterval(updateLiveData, 30000);

        // Initial load
        updateLiveData();

        // Manual refresh button
        document.getElementById('refreshDeviceData').addEventListener('click', function() {
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التحديث...';
            updateLiveData().then(() => {
                this.innerHTML = '<i class="fas fa-sync-alt"></i> تحديث البيانات';
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            switchSeizureTab('records');
        });
    </script>

    <!-- Bottom Navigation -->
    @include('components.bottom-nav', ['active' => 'seizures'])
            <i class="fas fa-home"></i>
            <span>الرئيسية</span>
        </a>
        <a href="{{ route('reports') }}" class="nav-item">
            <i class="fas fa-chart-bar"></i>
            <span>التقارير</span>
        </a>
        <a href="{{ route('data-entry') }}" class="nav-item">
            <i class="fas fa-edit"></i>
            <span>إدخال البيانات</span>
        </a>
        <a href="{{ route('seizures') }}" class="nav-item active">
            <i class="fas fa-clipboard-list"></i>
            <span>السجل</span>
        </a>
        <a href="{{ route('settings') }}" class="nav-item">
            <i class="fas fa-cog"></i>
            <span>الإعدادات</span>
        </a>
    </div>
</x-app-layout>
