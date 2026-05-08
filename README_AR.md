# تطبيق سندك (SANADK) - نظام الكشف عن نوبات الصرع والتنبؤ بها

## نظرة عامة

**سندك** هو تطبيق ويب متقدم يستخدم الذكاء الاصطناعي للكشف عن نوبات الصرع والتنبؤ بها قبل حدوثها، مع توفير نظام إنذار فوري وتتبع جغرافي للمرضى.

## المميزات الرئيسية

### 1. نظام الذكاء الاصطناعي
- تحليل لحظي للبيانات الحيوية (نبض القلب، مستوى الأكسجين، إشارات EEG/EMG)
- التنبؤ بالنوبات قبل حدوثها بـ 10-15 دقيقة
- تعلم النماذج من السجل الطبي للمريض

### 2. نظام الإنذارات الفورية
- تنبيهات فورية للأهل والأطباء والجهات المختصة
- إرسال الموقع الجغرافي تلقائياً
- تعليمات السلامة الفورية للمريض

### 3. الخرائط والتتبع الجغرافي
- تتبع موقع المريض الحالي باستخدام OpenStreetMap و Leaflet.js
- عرض المستشفيات والعيادات القريبة
- حساب المسافة والوقت المتوقع للوصول

### 4. لوحات التحكم المتخصصة

#### لوحة المريض
- عرض العلامات الحيوية الحالية
- سجل النوبات السابقة
- جهات الاتصال الطارئة
- المساعد الذكي (AI Chat)
- زر الاستغاثة الطارئ

#### لوحة الطبيب
- قائمة المرضى المسجلين
- مراقبة العلامات الحيوية لحظياً
- سجل النوبات والتحليلات
- إضافة ملاحظات طبية
- معدل نجاح التنبؤ

#### لوحة الأهل
- مراقبة حالة الأفراد المشمولين بالرعاية
- تتبع الموقع الجغرافي
- سجل النوبات
- إعدادات التنبيهات

#### لوحة الإدارة
- إحصائيات شاملة
- إدارة المرضى والأطباء
- سجل جميع النوبات
- التقارير والتحليلات

### 5. PWA (Progressive Web App)
- تطبيق قابل للتثبيت على الهواتف
- يعمل بدون إنترنت (Offline Support)
- سرعة تحميل عالية
- واجهة استجابة (Responsive Design)

## المتطلبات

- PHP 8.1+
- Laravel 10+
- SQLite
- Node.js 18+
- npm أو yarn

## التثبيت

### 1. استنساخ المشروع
```bash
git clone <repository-url>
cd sanadk
```

### 2. تثبيت المكتبات
```bash
composer install
npm install
```

### 3. إعداد البيئة
```bash
cp .env.example .env
php artisan key:generate
```

### 4. إعداد قاعدة البيانات
```bash
touch database/database.sqlite
php artisan migrate
php artisan db:seed --class=SeizureSeeder
```

### 5. بناء الأصول
```bash
npm run build
```

### 6. تشغيل التطبيق
```bash
php artisan serve
```

التطبيق سيكون متاحاً على: `http://localhost:8000`

## بيانات الدخول للاختبار

### المريض
- البريد: `patient@sanadk.com`
- كلمة المرور: `password`

### الطبيب
- البريد: `doctor@sanadk.com`
- كلمة المرور: `password`

### الأهل
- البريد: `family@sanadk.com`
- كلمة المرور: `password`

### الإدارة
- البريد: `admin@sanadk.com`
- كلمة المرور: `password`

## هيكل المشروع

```
sanadk/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── DashboardController.php
│   │   │   ├── EmergencyController.php
│   │   │   └── ...
│   ├── Models/
│   │   ├── User.php
│   │   ├── Seizure.php
│   │   ├── VitalSign.php
│   │   ├── EmergencyContact.php
│   │   └── PatientDoctor.php
│   ├── Services/
│   │   ├── SeizureDetector.php
│   │   └── SeizurePrediction.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── views/
│   │   ├── dashboards/
│   │   │   ├── patient.blade.php
│   │   │   ├── doctor.blade.php
│   │   │   ├── family.blade.php
│   │   │   ├── admin.blade.php
│   │   │   ├── map.blade.php
│   │   │   └── ai_chat.blade.php
│   │   └── layouts/
├── routes/
│   ├── web.php
│   └── api.php
└── public/
    ├── css/
    ├── js/
    └── img/
```

## الميزات التقنية

### Backend
- Laravel 10 Framework
- PHP 8.1+
- SQLite Database
- RESTful API

### Frontend
- Blade Templates
- Tailwind CSS
- Vanilla JavaScript
- Leaflet.js (Maps)

### Real-time Features
- WebSockets (Optional)
- Live Notifications
- Real-time Location Tracking

## قاعدة البيانات

### جداول رئيسية

#### Users
- id, name, email, password, role, phone, address, emergency_code

#### Seizures
- id, user_id, start_time, end_time, type, notes, is_predicted, latitude, longitude

#### VitalSigns
- id, user_id, heart_rate, eeg_signal, emg_signal, oxygen_level, temperature

#### EmergencyContacts
- id, user_id, name, phone, relationship, notify_on_prediction, notify_on_seizure

#### PatientDoctors
- id, patient_id, doctor_id, is_active

## الأمان

- تشفير كلمات المرور باستخدام bcrypt
- حماية CSRF
- التحقق من الصلاحيات (Authorization)
- معالجة الأخطاء الآمنة

## الدعم والمساهمة

للإبلاغ عن الأخطاء أو المساهمة في المشروع، يرجى التواصل عبر:
- البريد الإلكتروني: support@sanadk.com
- الموقع: www.sanadk.com

## الترخيص

هذا المشروع مرخص تحت رخصة MIT.

## شكر وتقدير

شكراً لاستخدامك تطبيق سندك. نتمنى أن يساهم في حماية صحتك وسلامتك.

---

**آخر تحديث**: مايو 2026
