<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('تتبع الموقع الجغرافي') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative mb-6">
                <div id="map" class="w-full h-96 rounded-[28px] border border-slate-200 shadow-lg overflow-hidden"></div>

                <div class="absolute inset-x-4 top-4 bg-white/95 backdrop-blur-md rounded-3xl border border-slate-200 shadow-sm p-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <p class="text-xs text-slate-500">الموقع الحالي</p>
                            <h2 id="nearestHospitalName" class="text-lg font-semibold text-slate-900">أقرب مستشفى</h2>
                            <p id="navigationDescription" class="text-sm text-slate-500">نظام الملاحة مُفعّل لتوجيهك إلى أسرع طريق.</p>
                        </div>
                        <div class="inline-flex gap-2">
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-2xl border border-slate-300 bg-white text-slate-700 px-4 py-2 text-sm shadow-sm hover:bg-slate-50 transition">
                                <i class="fas fa-arrow-left"></i>
                                لوحة التحكم
                            </a>
                            <button class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 text-white px-4 py-2 text-sm shadow-sm hover:bg-slate-700 transition">
                                <i class="fas fa-map-marker-alt"></i>
                                أقرب مستشفى
                            </button>
                            <button class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 text-white px-4 py-2 text-sm shadow-sm hover:bg-emerald-500 transition">
                                <i class="fas fa-phone-alt"></i>
                                اتصال
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-4">
                <div class="bg-white rounded-[28px] border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-200 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-slate-900">أقرب المستشفيات</h3>
                            <p class="text-sm text-slate-500">اختر المستشفى لبدء الملاحة أو الاتصال</p>
                        </div>
                        <span id="hospitalCount" class="inline-flex items-center rounded-full bg-blue-100 text-blue-700 px-3 py-1 text-xs font-semibold">0 مواقع</span>
                    </div>
                    <div id="hospitalsList" class="grid gap-3 p-4 md:grid-cols-2">
                        <!-- Hospitals will be loaded dynamically -->
                    </div>
                </div>

                <div class="bg-white rounded-[28px] border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-200">
                        <h3 class="text-base font-semibold text-slate-900">مرضى متتابعة</h3>
                        <p class="text-sm text-slate-500">اضغط لعرض موقع المريض على الخريطة</p>
                    </div>
                    <div class="grid gap-3 p-4">
                        @forelse($patients as $patient)
                            @php
                                $seizures = collect($patient['seizures'] ?? []);
                                $hasActiveSeizure = $seizures->whereNull('end_time')->isNotEmpty();
                            @endphp
                            <button type="button" onclick="focusPatient({{ $patient['id'] }})" class="w-full text-right rounded-3xl border border-slate-200 p-4 bg-slate-50 hover:bg-slate-100 transition text-slate-800">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="space-y-1">
                                        <h4 class="font-semibold">{{ $patient['name'] }}</h4>
                                        <p class="text-sm text-slate-500">{{ $patient['phone'] ?? 'بدون رقم' }}</p>
                                    </div>
                                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold {{ $hasActiveSeizure ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">
                                        <span class="w-2.5 h-2.5 rounded-full {{ $hasActiveSeizure ? 'bg-red-600' : 'bg-emerald-600' }}"></span>
                                        {{ $hasActiveSeizure ? 'نوبة نشطة' : 'مستقر' }}
                                    </span>
                                </div>
                            </button>
                        @empty
                            <p class="text-sm text-slate-500">لا يوجد مرضى للعرض</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" />

    <script>
        const patients = @json($patients);
        const defaultCoords = [24.7136, 46.6753];
        const initialCoords = patients.length && patients[0].latitude && patients[0].longitude
            ? [patients[0].latitude, patients[0].longitude]
            : defaultCoords;

        const hospitals = [
            { name: 'مستشفى الملك فيصل التخصصي', lat: 24.7133, lng: 46.6840, distance: '2.5 كم', eta: '8 دقائق', address: 'الرياض، المملكة العربية السعودية' },
            { name: 'مستشفى الحرس الوطني', lat: 24.7040, lng: 46.6908, distance: '3.8 كم', eta: '12 دقيقة', address: 'الرياض، المملكة العربية السعودية' },
            { name: 'مدينة الملك عبدالعزيز الطبية', lat: 24.6969, lng: 46.7500, distance: '5.2 كم', eta: '15 دقيقة', address: 'الرياض، المملكة العربية السعودية' },
        ];

        function loadLeafletScript() {
            const sources = [
                'https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js',
                'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'
            ];

            function loadSrc(src) {
                return new Promise((resolve, reject) => {
                    if (window.L) {
                        return resolve(window.L);
                    }
                    const script = document.createElement('script');
                    script.src = src;
                    script.onload = () => {
                        if (window.L) {
                            resolve(window.L);
                        } else {
                            reject(new Error('Leaflet تم تحميله لكن لم يُعَرَّف.'));
                        }
                    };
                    script.onerror = () => reject(new Error(`فشل تحميل مكتبة Leaflet من ${src}`));
                    document.head.appendChild(script);
                });
            }

            return sources.reduce((promise, src) => {
                return promise.catch(() => loadSrc(src));
            }, Promise.reject()).catch(error => {
                throw new Error('فشل تحميل مكتبة Leaflet: ' + error.message);
            });
        }

        function renderHospitalCount(count) {
            const hospitalCount = document.getElementById('hospitalCount');
            if (hospitalCount) {
                hospitalCount.innerText = `${count} موقع${count === 1 ? '' : '‎ات'}`;
            }
        }

        function setNearestHospitalTitle(hospital) {
            const title = document.getElementById('nearestHospitalName');
            const description = document.getElementById('navigationDescription');
            if (title) {
                title.innerText = hospital?.name || 'أقرب مستشفى';
            }
            if (description) {
                description.innerText = hospital ? 'نظام الملاحة مُفعّل لتوجيهك إلى أسرع طريق.' : 'لا يوجد مستشفى متاح حالياً.';
            }
        }

        function displayHospitals(hospitalsList) {
            const hospitalsListElement = document.getElementById('hospitalsList');
            if (!hospitalsListElement) return;
            renderHospitalCount(hospitalsList.length);
            setNearestHospitalTitle(hospitalsList[0]);

            hospitalsListElement.innerHTML = hospitalsList.map((hospital, index) =>
                `<button onclick="focusHospital(${index})" class="text-right w-full p-4 rounded-3xl border border-slate-200 bg-slate-50 hover:bg-slate-100 transition text-slate-800">
                    <div class="font-semibold">${hospital.name}</div>
                    <div class="text-sm text-slate-600">${hospital.distance} · ${hospital.eta}</div>
                    <div class="text-xs text-slate-500 mt-2">${hospital.address}</div>
                </button>`
            ).join('');
        }

        function addHospitalsToMap(mapInstance, hospitalsList) {
            hospitalsList.forEach(hospital => {
                const icon = L.divIcon({
                    className: 'hospital-marker',
                    html: `<div class="rounded-full bg-blue-600 text-white text-[10px] px-2 py-1">🏥</div>`,
                    iconSize: [32, 32],
                    iconAnchor: [16, 32]
                });
                const marker = L.marker([hospital.lat || defaultCoords[0], hospital.lng || defaultCoords[1]], { icon }).addTo(mapInstance);
                marker.bindPopup(`<strong>${hospital.name}</strong><br>${hospital.address}<br>${hospital.distance} - ${hospital.eta}`);
            });
        }

        function getPatientMarkers(mapInstance) {
            const markers = {};
            patients.forEach(patient => {
                const lat = patient.latitude || defaultCoords[0] + (Math.random() - 0.5) * 0.2;
                const lng = patient.longitude || defaultCoords[1] + (Math.random() - 0.5) * 0.2;
                const hasActiveSeizure = patient.seizures && patient.seizures.length > 0 && patient.seizures.some(s => !s.end_time);
                const statusColor = hasActiveSeizure ? '#ef4444' : '#10b981';
                const marker = L.circleMarker([lat, lng], {
                    radius: 9,
                    fillColor: statusColor,
                    color: '#ffffff',
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.9
                }).addTo(mapInstance);
                marker.bindPopup(`<strong>${patient.name}</strong><br>${patient.phone || 'رقم غير متوفر'}<br>${patient.address || 'العنوان غير متوفر'}`);
                markers[patient.id] = { marker, lat, lng };
            });
            return markers;
        }

        function initMap() {
            return loadLeafletScript().then(() => {
                const map = L.map('map').setView(initialCoords, 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors',
                    maxZoom: 19
                }).addTo(map);

                const markers = getPatientMarkers(map);
                const hospitalList = hospitals.slice();

                displayHospitals(hospitalList);
                addHospitalsToMap(map, hospitalList);

                window.focusPatient = function(id) {
                    if (markers[id]) {
                        const patient = markers[id];
                        map.setView([patient.lat, patient.lng], 15);
                        patient.marker.openPopup();
                    }
                };

                window.focusHospital = function(index) {
                    const hospital = hospitalList[index];
                    if (!hospital) return;
                    map.setView([hospital.lat, hospital.lng], 15);
                };

                if (!patients.length) {
                    L.popup({ closeOnClick: false, autoClose: false })
                        .setLatLng(initialCoords)
                        .setContent('لا توجد بيانات موقعيّة متاحة حالياً.')
                        .openOn(map);
                }

                setInterval(() => {
                    Object.values(markers).forEach(patientEntry => {
                        const newLat = patientEntry.lat + (Math.random() - 0.5) * 0.0006;
                        const newLng = patientEntry.lng + (Math.random() - 0.5) * 0.0006;
                        patientEntry.lat = newLat;
                        patientEntry.lng = newLng;
                        patientEntry.marker.setLatLng([newLat, newLng]);
                    });
                }, 6000);
            }).catch(error => {
                console.error(error);
                const message = document.createElement('div');
                message.className = 'p-4 rounded-3xl bg-red-50 text-red-700 border border-red-200 mt-4';
                message.innerText = 'حدث خطأ أثناء تحميل خريطة الموقع. حاول تحديث الصفحة.';
                document.querySelector('.max-w-5xl')?.prepend(message);
            });
        }

        window.addEventListener('load', initMap);
    </script>
</x-app-layout>
