<x-guest-layout>
    <div class="mb-4 text-center">
        <h2 class="fw-bold">إعادة تعيين كلمة المرور</h2>
        <p class="text-muted mb-0">اختر كلمة مرور جديدة لحسابك.</p>
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

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">البريد الإلكتروني</label>
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" class="form-control form-control-lg">
        </div>

        <div class="mb-3">
            <label for="password" class="form-label fw-semibold">كلمة المرور الجديدة</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" class="form-control form-control-lg">
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label fw-semibold">تأكيد كلمة المرور</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="form-control form-control-lg">
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-100">تحديث كلمة المرور</button>

        <div class="auth-footer mt-4">
            <a href="{{ route('login') }}">العودة لتسجيل الدخول</a>
        </div>
    </form>
</x-guest-layout>
