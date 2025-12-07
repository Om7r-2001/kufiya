@extends('layouts.auth')

@section('content')

<header>
    <div class="container">
        <nav class="navbar">
            <div class="logo">
                <img src="/logo.png" alt="شعار منصة كوفية" class="logo-image">
                <div class="logo-info">
                    <div class="logo-text">منصة كوفية</div>
                    <div class="logo-sub">سوق رقمي موثوق للخدمات</div>
                </div>
            </div>

            <div class="nav-links" style="display: block !important;">
                <a href="{{ route('home') }}">الرئيسية</a>
            </div>
        </nav>
    </div>
</header>

<main>
    <section class="auth-section">
        <div class="container auth-wrapper">
            <div class="auth-card">
                <h1 class="auth-title">مرحباً بك في منصة كوفية</h1>
                <p class="auth-subtitle">سجّل كمستخدم جديد أو قم بتسجيل الدخول لاستكمال استخدام المنصة.</p>
                <!-- شريط التبويب -->
                <div class="auth-tabs" id="authTabs">
                    <button class="auth-tab-btn active" id="tab-login" type="button" onclick="switchAuthTab('login')">
                        تسجيل الدخول
                    </button>

                    <button class="auth-tab-btn" id="tab-register" type="button" onclick="switchAuthTab('register')">
                        إنشاء حساب
                    </button>
                </div>
                <!-- عرض للأخطاء -->
                @if ($errors->any())
                <div
                    style="background:#ffe6e6; border-radius:12px; padding:8px 12px; font-size:12px; margin-bottom:10px;">
                    @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                    @endforeach
                </div>
                @endif
                <!-- نموذج إنشاء حساب -->
                <form class="auth-form hidden" id="registerForm" method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="auth-grid-2">
                        <div class="form-group">
                            <label for="regName">الاسم الكامل</label>
                            <input type="text" id="regName" name="name" placeholder="اكتب اسمك">
                        </div>

                        <div class="form-group">
                            <label for="regEmail">البريد الإلكتروني</label>
                            <input type="email" id="regEmail" name="email" placeholder="name@example.com">
                        </div>
                    </div>

                    <div class="auth-grid-2">
                        <div class="form-group">
                            <label for="regPassword">كلمة المرور</label>
                            <input type="password" id="regPassword" name="password" placeholder="••••••••">
                        </div>

                        <div class="form-group">
                            <label for="regPasswordConfirm">تأكيد كلمة المرور</label>
                            <input type="password" id="regPasswordConfirm" name="password_confirmation"
                                placeholder="أعد إدخال كلمة المرور">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>نوع الحساب</label>
                        <div class="role-options">
                            <label class="role-option">
                                <input type="radio" name="role" value="buyer">
                                <span>مشتري خدمات</span>
                            </label>
                            <label class="role-option">
                                <input type="radio" name="role" value="seller" checked>
                                <span>مزود خدمة</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="regPhone">رقم الجوال (اختياري)</label>
                        <input type="text" id="regPhone" placeholder="مثال: 00970xxxxxxxxx">
                    </div>

                    <div class="form-group form-check">
                        <label>
                            <input type="checkbox" checked>
                            أوافق على <a href="{{ route('terms') }}"
                                class="{{ request()->routeIs('terms') ? 'active-link' : '' }}"> الشروط
                                والأحكام </a> و <a href="{{ route('privacy') }}"
                                class="{{ request()->routeIs('privacy') ? 'active-link' : '' }}">سياسة
                                الخصوصية</a>
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary auth-submit-btn">
                        إنشاء حساب جديد
                    </button>
                </form>

                <!-- نموذج تسجيل دخول -->
                <form class="auth-form" id="loginForm" method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group">
                        <label for="loginEmail">البريد الإلكتروني</label>
                        <input type="email" id="loginEmail" name="email" placeholder="name@example.com">
                    </div>

                    <div class="form-group">
                        <label for="loginPassword">كلمة المرور</label>
                        <input type="password" id="loginPassword" name="password" placeholder="••••••••">
                    </div>

                    <div class="form-row-between">
                        <label class="form-check">
                            <input type="checkbox" name="remember">
                            تذكرني
                        </label>
                        <a href="{{ route('password.request') }}" class="form-link">
                            نسيت كلمة المرور؟
                        </a>
                    </div>
                    <button type="submit" class="btn btn-primary auth-submit-btn">
                        تسجيل الدخول
                    </button>
                </form>
            </div>
        </div>
    </section>
</main>

<script>
function switchAuthTab(target) {
    const tabRegister = document.getElementById("tab-register");
    const tabLogin = document.getElementById("tab-login");
    const registerForm = document.getElementById("registerForm");
    const loginForm = document.getElementById("loginForm");

    if (target === "register") {
        tabRegister.classList.add("active");
        tabLogin.classList.remove("active");
        registerForm.classList.remove("hidden");
        loginForm.classList.add("hidden");
    } else {
        tabLogin.classList.add("active");
        tabRegister.classList.remove("active");
        loginForm.classList.remove("hidden");
        registerForm.classList.add("hidden");
    }
}
</script>

<script>
// قراءة قيمة tab من الـ URL
const urlParams = new URLSearchParams(window.location.search);
const tab = urlParams.get('tab');

if (tab === 'register') {
    switchAuthTab('register');
} else if (tab === 'login') {
    switchAuthTab('login');
}
</script>


@endsection