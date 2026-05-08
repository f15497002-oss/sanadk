<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/modern-ui.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @include('components.header-nav', ['title' => 'التقارير'])

    <div class="p-5">
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="text-gray-600"><i class="fas fa-chevron-right"></i></a>
                <h2 class="text-xl font-bold">التقارير</h2>
            </div>
            <button class="text-gray-400"><i class="fas fa-calendar-alt"></i></button>
        </div>

        <div class="bg-white p-6 rounded-3xl shadow-sm mb-8 text-center">
            @php
                $reportsRiskScore = is_numeric($riskScore) ? (int) round($riskScore) : 0;
                $reportsRiskLabel = $reportsRiskScore >= 70 ? 'عالي' : ($reportsRiskScore >= 40 ? 'متوسط' : 'منخفض');
            @endphp
            <p class="text-sm text-gray-500 mb-2">مؤشر الخطر الحالي</p>
            <div class="circular-progress mx-auto mb-3" style="background: conic-gradient(var(--primary) {{ $reportsRiskScore }}%, #EDF2F7 0deg); width: 140px; height: 140px;">
                <div class="progress-value">{{ $reportsRiskScore }}</div>
            </div>
            <p class="text-sm font-bold text-gray-700">{{ $reportsRiskLabel }}</p>
            <p class="text-xs text-gray-500 mt-2">احتمال حدوث نوبة خلال 30 دقيقة القادمة {{ $reportsRiskLabel }}.</p>
        </div>

        <div class="flex bg-gray-100 p-1 rounded-2xl mb-8">
            <button id="weekBtn" type="button" class="flex-1 py-2 text-sm text-gray-400">أسبوع</button>
            <button id="monthBtn" type="button" class="flex-1 py-2 text-sm bg-primary text-white rounded-xl font-bold shadow-sm">شهر</button>
            <button id="quarterBtn" type="button" class="flex-1 py-2 text-sm text-gray-400">3 أشهر</button>
        </div>

        <div class="card mb-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold">مؤشر الخطر</h3>
                <span class="bg-primary/10 text-primary text-[10px] px-2 py-1 rounded-lg font-bold">{{ $riskScore ?? 50 }}</span>
            </div>
            <canvas id="riskChart" height="200"></canvas>
        </div>

        <div class="card mb-8">
            <h3 class="font-bold mb-6">بيانات الأجهزة الحية</h3>
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

        <div class="card">
            <h3 class="font-bold mb-6">العوامل المؤثرة</h3>
            <div class="flex items-center gap-8">
                <div class="relative w-32 h-32">
                    <canvas id="factorsChart"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-[10px] text-gray-400">مستوى</span>
                        <span class="text-sm font-bold">التأثير</span>
                    </div>
                </div>
                <div class="flex-1 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                            <span class="text-xs text-gray-500">النوم</span>
                        </div>
                        <span class="text-xs font-bold">{{ $sleepImpact ?? 40 }}%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-orange-400 rounded-full"></span>
                            <span class="text-xs text-gray-500">التوتر</span>
                        </div>
                        <span class="text-xs font-bold">{{ $stressImpact ?? 30 }}%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-blue-400 rounded-full"></span>
                            <span class="text-xs text-gray-500">النشاط</span>
                        </div>
                        <span class="text-xs font-bold">{{ $activityImpact ?? 20 }}%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-purple-400 rounded-full"></span>
                            <span class="text-xs text-gray-500">أخرى</span>
                        </div>
                        <span class="text-xs font-bold">{{ $otherImpact ?? 10 }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Navigation -->
    @include('components.bottom-nav', ['active' => 'reports'])
            <i class="fas fa-home"></i>
            <span>الرئيسية</span>
        </a>
        <a href="{{ route('reports') }}" class="nav-item active">
            <i class="fas fa-chart-bar"></i>
            <span>التقارير</span>
        </a>
        <a href="{{ route('data-entry') }}" class="nav-item">
            <i class="fas fa-edit"></i>
            <span>إدخال البيانات</span>
        </a>
        <a href="{{ route('seizures') }}" class="nav-item">
            <i class="fas fa-clipboard-list"></i>
            <span>السجل</span>
        </a>
        <a href="{{ route('settings') }}" class="nav-item">
            <i class="fas fa-cog"></i>
            <span>الإعدادات</span>
        </a>
    </div>

    <script>
        // Risk Chart
        const ctxRisk = document.getElementById('riskChart').getContext('2d');
        const riskLabels = @json($riskLabels);
        const riskData = @json($riskData);

        const periodData = {
            week: {
                labels: riskLabels,
                data: riskData
            },
            month: {
                labels: riskLabels,
                data: riskData
            },
            quarter: {
                labels: riskLabels,
                data: riskData.map(value => Math.round(value * 0.85))
            }
        };

        const riskChart = new Chart(ctxRisk, {
            type: 'line',
            data: {
                labels: periodData.month.labels,
                datasets: [{
                    label: 'مؤشر الخطر',
                    data: periodData.month.data,
                    borderColor: '#4A90E2',
                    backgroundColor: 'rgba(74, 144, 226, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#4A90E2'
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, max: 100, ticks: { stepSize: 25 } },
                    x: { grid: { display: false } }
                }
            }
        });

        function setReportPeriod(period) {
            const weekBtn = document.getElementById('weekBtn');
            const monthBtn = document.getElementById('monthBtn');
            const quarterBtn = document.getElementById('quarterBtn');
            const buttons = [weekBtn, monthBtn, quarterBtn];
            buttons.forEach(btn => {
                btn.classList.remove('bg-primary', 'text-white', 'shadow-sm');
                btn.classList.add('text-gray-400');
            });

            if (period === 'week') {
                weekBtn.classList.add('bg-primary', 'text-white', 'shadow-sm');
                riskChart.data.labels = periodData.week.labels;
                riskChart.data.datasets[0].data = periodData.week.data;
            } else if (period === 'month') {
                monthBtn.classList.add('bg-primary', 'text-white', 'shadow-sm');
                riskChart.data.labels = periodData.month.labels;
                riskChart.data.datasets[0].data = periodData.month.data;
            } else {
                quarterBtn.classList.add('bg-primary', 'text-white', 'shadow-sm');
                riskChart.data.labels = periodData.quarter.labels;
                riskChart.data.datasets[0].data = periodData.quarter.data;
            }

            riskChart.update();
        }

        document.getElementById('weekBtn').addEventListener('click', () => setReportPeriod('week'));
        document.getElementById('monthBtn').addEventListener('click', () => setReportPeriod('month'));
        document.getElementById('quarterBtn').addEventListener('click', () => setReportPeriod('quarter'));

        // Factors Chart
        const ctxFactors = document.getElementById('factorsChart').getContext('2d');
        new Chart(ctxFactors, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [{{ $sleepImpact }}, {{ $stressImpact }}, {{ $activityImpact }}, {{ $otherImpact }}],
                    backgroundColor: ['#4ADE80', '#FB923C', '#60A5FA', '#A78BFA'],
                    borderWidth: 0,
                    cutout: '80%'
                }]
            },
            options: {
                plugins: { tooltip: { enabled: false } }
            }
        });

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
    </script>
</x-app-layout>
