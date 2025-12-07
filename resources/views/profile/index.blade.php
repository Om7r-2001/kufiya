@extends('layouts.app')

@section('title', 'الملف الشخصي | منصة كوفية')

@section('content')
<main>
    <section class="profile-section">
        <div class="container">

            <div class="page-header">
                <div>
                    <h1 class="page-title">الملف الشخصي</h1>
                    <p class="page-subtitle">
                        هنا تجد ملخصًا عن بيانات حسابك في منصة كوفية.
                    </p>
                </div>
                <div>
                    <a href="{{ route('profile.settings') }}" class="btn btn-primary">
                        تعديل البيانات
                    </a>
                </div>
            </div>

            <div class="profile-grid">
                {{-- بطاقة المعلومات الأساسية --}}
                <div class="profile-card">
                    <div class="profile-header">
                        <div class="profile-avatar">
                            @if($user->avatar)
                            <img src="{{ asset('storage/'.$user->avatar) }}" alt="الصورة الشخصية"
                                style="width:100%; height:100%; border-radius:50%; object-fit:cover;">
                            @else
                            <span class="profile-avatar-letter">
                                {{ mb_substr($user->name, 0, 1) }}
                            </span>
                            @endif
                        </div>
                        <div>
                            <div class="profile-name">{{ $user->name }}</div>
                            <div class="profile-role">
                                @if($user->role === 'seller')
                                مزود خدمة
                                @elseif($user->role === 'buyer')
                                مشتري خدمات
                                @else
                                مستخدم
                                @endif
                            </div>
                            <div class="profile-email">{{ $user->email }}</div>
                        </div>
                    </div>

                    <div class="profile-info-row">
                        <span class="label">رقم الهاتف:</span>
                        <span class="value">{{ $user->phone ?? 'غير مضاف' }}</span>
                    </div>
                    <div class="profile-info-row">
                        <span class="label">البلد:</span>
                        <span class="value">{{ $user->country ?? 'غير محدد' }}</span>
                    </div>
                    <div class="profile-info-row">
                        <span class="label">المدينة:</span>
                        <span class="value">{{ $user->city ?? 'غير محددة' }}</span>
                    </div>
                    <div class="profile-info-row">
                        <span class="label">عضو منذ:</span>
                        <span class="value">{{ $user->created_at?->format('Y-m-d') }}</span>
                    </div>
                </div>

                {{-- بطاقة ملخص النشاط --}}
                <div class="profile-card">
                    <h2 class="profile-card-title">ملخص النشاط</h2>
                    <ul class="profile-activity-list">
                        <li>
                            <span>نوع الحساب:</span>
                            <span>
                                @if($user->role === 'seller')
                                يمكنه إضافة خدمات واستقبال طلبات
                                @elseif($user->role === 'buyer')
                                يمكنه طلب الخدمات من المزودين
                                @else
                                يمكنه الاستفادة من خصائص المنصة العامة
                                @endif
                            </span>
                        </li>
                        <li>
                            <span>آخر تحديث للبيانات:</span>
                            <span>{{ $user->updated_at?->format('Y-m-d H:i') }}</span>
                        </li>
                    </ul>
                    <p style="font-size:13px; color:var(--color-muted); margin-top:8px;">
                        يمكنك تعديل بياناتك في أي وقت من صفحة الإعدادات.
                    </p>
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

.profile-header {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 14px;
}

.profile-avatar {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: #f2f4f8;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    font-weight: 700;
    color: #133a6f;
}

.profile-avatar-letter {
    display: inline-block;
}

.profile-name {
    font-size: 18px;
    font-weight: 700;
}

.profile-role {
    font-size: 13px;
    color: var(--color-muted);
}

.profile-email {
    font-size: 13px;
}

.profile-info-row {
    display: flex;
    justify-content: space-between;
    font-size: 13px;
    padding: 4px 0;
    border-bottom: 1px dashed #f0f0f0;
}

.profile-info-row:last-child {
    border-bottom: none;
}

.profile-info-row .label {
    color: var(--color-muted);
}

.profile-card-title {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 10px;
}

.profile-activity-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.profile-activity-list li {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    font-size: 13px;
    padding: 4px 0;
}

@media (max-width: 900px) {
    .profile-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection