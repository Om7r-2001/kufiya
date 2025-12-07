@extends('layouts.app')

@section('title', 'تعيين كلمة مرور جديدة | منصة كوفية')

@section('content')
<main>
    <section class="auth-section">
        <div class="container">
            <div class="auth-card">
                <h1 class="auth-title">تعيين كلمة مرور جديدة</h1>
                <p class="auth-subtitle">
                    أدخل كلمة المرور الجديدة لحسابك ثم قم بتأكيدها.
                </p>

                @if($errors->any())
                    <div class="alert-error">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" class="auth-form">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-group">
                        <label for="email">البريد الإلكتروني</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', $email) }}"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="password">كلمة المرور الجديدة</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">تأكيد كلمة المرور الجديدة</label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            required
                        >
                    </div>

                    <button type="submit" class="btn btn-primary auth-btn">
                        حفظ كلمة المرور
                    </button>

                    <div class="auth-footer-links">
                        <a href="{{ route('auth') }}">العودة إلى تسجيل الدخول</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<style>
/* نستخدم نفس تنسيقات auth-section, auth-card, ... من صفحة forgot-password */
</style>
@endsection
