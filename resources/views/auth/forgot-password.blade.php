@extends('layouts.app')

@section('title', 'نسيت كلمة المرور | منصة كوفية')

@section('content')
<main>
    <section class="auth-section">
        <div class="container">
            <div class="auth-card">
                <h1 class="auth-title">استعادة كلمة المرور</h1>
                <p class="auth-subtitle">
                    أدخل بريدك الإلكتروني المسجل وسنقوم بإنشاء رابط لاستعادة كلمة المرور.
                </p>

                @if(session('status'))
                <div class="alert-success">
                    {{ session('status') }}
                </div>
                @endif

                @if($errors->any())
                <div class="alert-error">
                    @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                    @endforeach
                </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="auth-form">
                    @csrf

                    <div class="form-group">
                        <label for="email">البريد الإلكتروني</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                    </div>

                    <button type="submit" class="btn btn-primary auth-btn">
                        إرسال رابط الاستعادة
                    </button>

                    <div class="auth-footer-links">
                        <a href="{{ route('auth') }}">العودة إلى صفحة تسجيل الدخول</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<style>
.auth-section {
    padding: 40px 0;
}

.auth-card {
    max-width: 420px;
    margin: 0 auto;
    background: #fff;
    border-radius: 16px;
    padding: 20px 22px;
    border: 1px solid #eee;
}

.auth-title {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 6px;
}

.auth-subtitle {
    font-size: 13px;
    color: var(--color-muted);
    margin-bottom: 16px;
}

.alert-success {
    background: #e6ffef;
    border-radius: 10px;
    padding: 8px 10px;
    font-size: 13px;
    margin-bottom: 12px;
}

.alert-error {
    background: #ffe6e6;
    border-radius: 10px;
    padding: 8px 10px;
    font-size: 13px;
    margin-bottom: 12px;
}

.auth-form .form-group {
    margin-bottom: 12px;
}

.auth-form label {
    display: block;
    font-size: 13px;
    margin-bottom: 4px;
}

.auth-form input[type="email"] {
    width: 100%;
    border-radius: 10px;
    border: 1px solid #ddd;
    padding: 8px 10px;
    font-size: 13px;
}

.auth-btn {
    width: 100%;
    margin-top: 6px;
}

.auth-footer-links {
    margin-top: 12px;
    font-size: 13px;
    text-align: center;
}
</style>
@endsection