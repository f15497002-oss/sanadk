# دليل التثبيت والتشغيل - تطبيق سندك

## المتطلبات الأساسية

قبل البدء، تأكد من توفر:
- PHP 8.1 أو أحدث
- Composer
- Node.js 18 أو أحدث
- npm أو yarn
- SQLite (مدمج في PHP)

## خطوات التثبيت

### 1. فك ضغط المشروع

```bash
# إذا كنت تستخدم tar.gz
tar -xzf sanadk-complete.tar.gz
cd sanadk

# أو إذا كنت تستخدم zip
unzip sanadk-complete.zip
cd sanadk
```

### 2. تثبيت مكتبات PHP

```bash
composer install
```

### 3. تثبيت مكتبات JavaScript

```bash
npm install
```

### 4. إعداد ملف البيئة

```bash
cp .env.example .env
```

### 5. إنشاء مفتاح التطبيق

```bash
php artisan key:generate
```

### 6. إنشاء قاعدة البيانات

```bash
# تأكد من وجود ملف قاعدة البيانات
touch database/database.sqlite

# تشغيل الهجرات
php artisan migrate
```

### 7. ملء قاعدة البيانات بالبيانات الأولية

```bash
# تشغيل جميع البذور
php artisan db:seed

# أو تشغيل بذور محددة
php artisan db:seed --class=SeizureSeeder
php artisan db:seed --class=AdminSeeder
```

### 8. بناء الأصول

```bash
npm run build
```

### 9. تشغيل التطبيق

```bash
php artisan serve
```

التطبيق سيكون متاحاً على: **http://localhost:8000**

## بيانات الدخول الافتراضية

### المريض
```
البريد: patient@sanadk.com
كلمة المرور: password
```

### الطبيب
```
البريد: doctor@sanadk.com
كلمة المرور: password
```

### الأهل
```
البريد: family@sanadk.com
كلمة المرور: password
```

### الإدارة
```
البريد: admin@sanadk.com
كلمة المرور: password
```

## التشغيل في بيئة الإنتاج

### 1. تحسين الأداء

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 2. تشغيل خادم الويب

```bash
# استخدام Apache أو Nginx
# تأكد من أن document root يشير إلى public/
```

### 3. تفعيل HTTPS

```bash
# استخدم Let's Encrypt أو شهادة SSL أخرى
```

## استكشاف الأخطاء

### مشكلة: "No such file or directory" عند الوصول إلى قاعدة البيانات

**الحل:**
```bash
touch database/database.sqlite
chmod 666 database/database.sqlite
chmod 777 database/
```

### مشكلة: أخطاء الأذونات

**الحل:**
```bash
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

### مشكلة: المكتبات غير مثبتة

**الحل:**
```bash
# أعد تثبيت المكتبات
composer install --no-dev
npm install
```

### مشكلة: الخريطة لا تظهر

**الحل:**
```bash
# تأكد من تثبيت Leaflet
npm install leaflet
npm run build
```

## الميزات المتاحة

✅ لوحات تحكم متخصصة (مريض، طبيب، أهل، إدارة)
✅ نظام الذكاء الاصطناعي للتنبؤ
✅ خرائط تفاعلية (OpenStreetMap + Leaflet)
✅ نظام الإنذارات الفورية
✅ تتبع الموقع الجغرافي
✅ PWA (تطبيق قابل للتثبيت)
✅ دعم العمل بدون إنترنت
✅ واجهة استجابة (Responsive Design)

## الدعم الفني

في حالة واجهتك أي مشاكل:

1. تحقق من ملف السجل: `storage/logs/laravel.log`
2. تأكد من أن جميع المكتبات مثبتة بشكل صحيح
3. تأكد من أن قاعدة البيانات تعمل بشكل صحيح
4. جرب مسح الذاكرة المؤقتة: `php artisan cache:clear`

## الخطوات التالية

بعد التثبيت الناجح:

1. **إنشاء حسابات جديدة** عبر صفحة التسجيل
2. **ربط المرضى بالأطباء** عبر لوحة الإدارة
3. **إضافة جهات اتصال طارئة** في إعدادات المريض
4. **اختبار النظام** بإدخال بيانات حيوية وهمية
5. **تفعيل الإشعارات** على الهاتف الذكي

## نصائح الأمان

⚠️ **تغيير كلمات المرور الافتراضية** فوراً في الإنتاج
⚠️ **تفعيل HTTPS** لجميع الاتصالات
⚠️ **عمل نسخ احتياطية** منتظمة من قاعدة البيانات
⚠️ **تحديث المكتبات** بانتظام
⚠️ **مراقبة السجلات** للكشف عن الأنشطة المريبة

---

**تم إنشاؤه بواسطة:** فريق تطوير سندك
**آخر تحديث:** مايو 2026
