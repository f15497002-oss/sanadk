<x-guest-layout>
    <div class="mb-4 text-center">
        <h2 class="fw-bold">نسيت كلمة المرور</h2>
        <p class="text-muted mb-0">أدخل بريدك الإلكتروني وسنرسل لك رابطًا لإعادة تعيين كلمة المرور.</p>
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

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">البريد الإلكتروني</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="form-control form-control-lg">
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-100">إرسال رابط إعادة التعيين</button>

        <div class="auth-footer mt-4">
            <a href="{{ route('login') }}">العودة لتسجيل الدخول</a>
        </div>
    </form>
</x-guest-layout>
