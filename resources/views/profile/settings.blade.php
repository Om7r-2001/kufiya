@extends('layouts.app')

@section('title', 'إعدادات الحساب | منصة كوفية')

@section('content')
<main>
    <section class="profile-section">
        <div class="container">

            <div class="page-header">
                <div>
                    <h1 class="page-title">إعدادات الحساب</h1>
                    <p class="page-subtitle">
                        عدّل بيانات حسابك، معلومات التواصل، وكلمة المرور من هنا.
                    </p>
                </div>
            </div>

            <div class="profile-grid">
                {{-- نموذج تعديل البيانات الأساسية --}}
                <div class="profile-card">
                    <h2 class="profile-card-title">البيانات الأساسية</h2>

                    @if(session('success_profile'))
                    <div
                        style="background:#e6ffef; border-radius:10px; padding:8px 12px; font-size:13px; margin-bottom:10px;">
                        {{ session('success_profile') }}
                    </div>
                    @endif

                    @if($errors->any() && ! $errors->has('current_password'))
                    <div
                        style="background:#ffe6e6; border-radius:10px; padding:8px 12px; font-size:13px; margin-bottom:10px;">
                        @foreach($errors->all() as $error)
                        @if($error !== $errors->first('current_password'))
                        <div>{{ $error }}</div>
                        @endif
                        @endforeach
                    </div>
                    @endif

                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data"
                        class="settings-form">
                        @csrf

                        <div class="form-group">
                            <label for="name">الاسم الكامل</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}">
                        </div>

                        <div class="form-group">
                            <label for="email">البريد الإلكتروني</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}">
                        </div>

                        <div class="form-group">
                            <label for="phone">رقم الهاتف</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                        </div>

                        <div class="settings-grid-2">
                            <div class="form-group">
                                <label for="country">البلد</label>
                                <input type="text" id="country" name="country"
                                    value="{{ old('country', $user->country) }}">
                            </div>
                            <div class="form-group">
                                <label for="city">المدينة</label>
                                <input type="text" id="city" name="city" value="{{ old('city', $user->city) }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>الصورة الشخصية</label>
                            <div style="display:flex; align-items:center; gap:12px;">
                                <div class="profile-avatar small">
                                    @if($user->avatar)
                                    <img src="{{ asset('storage/'.$user->avatar) }}" alt="الصورة الشخصية"
                                        style="width:100%; height:100%; border-radius:50%; object-fit:cover;">
                                    @else
                                    <span class="profile-avatar-letter">
                                        {{ mb_substr($user->name, 0, 1) }}
                                    </span>
                                    @endif
                                </div>
                                <input type="file" name="avatar" accept="image/*">
                            </div>
                            <span class="field-hint">يُفضّل صورة مربعة بأبعاد مناسبة، بحد أقصى 2 ميجابايت.</span>
                        </div>

                        <div class="settings-actions">
                            <button type="submit" class="btn btn-primary">
                                حفظ التغييرات
                            </button>
                        </div>
                    </form>
                </div>

                {{-- نموذج تغيير كلمة المرور --}}
                <div class="profile-card">
                    <h2 class="profile-card-title">تغيير كلمة المرور</h2>

                    @if(session('success_password'))
                    <div
                        style="background:#e6ffef; border-radius:10px; padding:8px 12px; font-size:13px; margin-bottom:10px;">
                        {{ session('success_password') }}
                    </div>
                    @endif

                    @if($errors->has('current_password') || $errors->has('password'))
                    <div
                        style="background:#ffe6e6; border-radius:10px; padding:8px 12px; font-size:13px; margin-bottom:10px;">
                        @error('current_password')
                        <div>{{ $message }}</div>
                        @enderror
                        @error('password')
                        <div>{{ $message }}</div>
                        @enderror
                    </div>
                    @endif

                    <form method="POST" action="{{ route('profile.password') }}" class="settings-form">
                        @csrf

                        <div class="form-group">
                            <label for="current_password">كلمة المرور الحالية</label>
                            <input type="password" id="current_password" name="current_password">
                        </div>

                        <div class="form-group">
                            <label for="password">كلمة المرور الجديدة</label>
                            <input type="password" id="password" name="password">
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">تأكيد كلمة المرور الجديدة</label>
                            <input type="password" id="password_confirmation" name="password_confirmation">
                        </div>

                        <div class="settings-actions">
                            <button type="submit" class="btn btn-outline">
                                تحديث كلمة المرور
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </section>
</main>

<style>
.profile-section {
    padding: 32px 0;
}

.profile-grid {
    display: grid;
    grid-template-columns: minmax(0, 3fr) minmax(0, 2fr);
    gap: 16px;
}

.profile-card {
    background: #fff;
    border-radius: 16px;
    padding: 16px 18px;
    border: 1px solid #eee;
}

.profile-card-title {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 12px;
}

.settings-form .form-group {
    margin-bottom: 10px;
}

.settings-form label {
    display: block;
    font-size: 13px;
    margin-bottom: 4px;
}

.settings-form input[type="text"],
.settings-form input[type="email"],
.settings-form input[type="password"],
.settings-form input[type="file"] {
    width: 100%;
    border-radius: 10px;
    border: 1px solid #ddd;
    padding: 7px 9px;
    font-size: 13px;
}

.settings-grid-2 {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
}

.settings-actions {
    margin-top: 10px;
}

.field-hint {
    font-size: 11px;
    color: var(--color-muted);
}

.profile-avatar.small {
    width: 48px;
    height: 48px;
}

@media (max-width: 900px) {
    .profile-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection