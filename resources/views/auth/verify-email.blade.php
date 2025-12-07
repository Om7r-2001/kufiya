@extends('layouts.app')

@section('title', 'تفعيل البريد الإلكتروني | منصة كوفية')

@section('content')
<main>
    <section class="auth-section">
        <div class="container auth-wrapper">
            <div class="auth-card">
                <h1 class="auth-title">تفعيل البريد الإلكتروني</h1>
                <p class="auth-subtitle">
                    تم إرسال رابط تفعيل إلى بريدك الإلكتروني. يرجى فتح البريد والنقر على رابط التفعيل
                    لإكمال تفعيل حسابك.
                </p>

                @if (session('status'))
                    <div class="alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('verification.send') }}" class="auth-form">
                    @csrf
                    <button type="submit" class="btn btn-primary auth-submit-btn">
                        إعادة إرسال رابط التفعيل
                    </button>
                </form>

                <div class="auth-footer-links">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="link-button" style="border:none; background:none;">
                            تسجيل الخروج
                        </button>
                    </form>
                </div>
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
.alert-success {
    background: #e6ffef;
    border-radius: 10px;
    padding: 8px 10px;
    font-size: 13px;
    margin-bottom: 12px;
}
</style>
@endsection
