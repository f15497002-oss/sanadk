<x-guest-layout>
    <div class="mb-4 text-center">
        <h2 class="fw-bold">تسجيل الدخول</h2>
        <p class="text-muted mb-0">ادخل بريدك الإلكتروني والمرور للوصول إلى حسابك.</p>
    </div>

    @if (session('status'))
        <div class="alert alert-success text-end">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger text-end">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">البريد الإلكتروني</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="form-control form-control-lg">
        </div>

        <div class="mb-3">
            <label for="password" class="form-label fw-semibold">المرور</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" class="form-control form-control-lg">
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input id="remember_me" class="form-check-input" type="checkbox" name="remember">
                <label class="form-check-label" for="remember_me">تذكرني</label>
            </div>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="small">نسيت المرور؟</a>
            @endif
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-100">دخول</button>

        <div class="auth-footer mt-4">
            <span class="text-muted">ليس لديك حساب؟</span>
            <a href="{{ route('register') }}">إنشاء حساب جديد</a>
        </div>
    </form>
</x-guest-layout>
