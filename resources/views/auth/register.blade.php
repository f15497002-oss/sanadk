<x-guest-layout>
    <div class="mb-4 text-center">
        <h2 class="fw-bold">إنشاء حساب جديد</h2>
        <p class="text-muted mb-0">سجل حسابك بتفاصيلك الشخصية للبدء في استخدام النظام.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger text-end">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label fw-semibold">الاسم الكامل</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="form-control form-control-lg">
        </div>

        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">البريد الإلكتروني</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="form-control form-control-lg">
        </div>

        <div class="mb-3">
            <label for="role" class="form-label fw-semibold">دور المستخدم</label>
            <select id="role" name="role" required class="form-select form-select-lg">
                <option value="patient" {{ old('role') === 'patient' ? 'selected' : '' }}>مريض</option>
                <option value="family" {{ old('role') === 'family' ? 'selected' : '' }}>فرد عائلة</option>
                <option value="doctor" {{ old('role') === 'doctor' ? 'selected' : '' }}>طبيب</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label fw-semibold">المرور</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" class="form-control form-control-lg">
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label fw-semibold">تأكيد المرور</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="form-control form-control-lg">
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-100">إنشاء الحساب</button>

        <div class="auth-footer mt-4">
            <span class="text-muted">هل لديك حساب بالفعل؟</span>
            <a href="{{ route('login') }}">تسجيل الدخول</a>
        </div>
    </form>
</x-guest-layout>
