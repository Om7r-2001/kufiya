@extends('layouts.app')

@section('title', 'تفاصيل المستخدم')

@section('content')
<section class="dashboard-section">
    <div class="container dashboard-layout">

        {{-- الشريط الجانبي للمشرف --}}
        <aside class="dashboard-sidebar">
            <div class="dashboard-user">
                <div class="dashboard-avatar">
                    {{ mb_substr(auth()->user()->name, 0, 1) }}
                </div>
                <div>
                    <div class="dashboard-username">{{ auth()->user()->name }}</div>
                    <div class="dashboard-user-meta">مشرف النظام</div>
                </div>
            </div>

            <nav class="dashboard-nav">
                <a href="{{ route('admin.dashboard') }}" class="dash-nav-link">
                    لوحة التحكم
                </a>
                <a href="{{ route('admin.services.index') }}" class="dash-nav-link">
                    إدارة الخدمات
                </a>
                <a href="{{ route('admin.users.index') }}" class="dash-nav-link active">
                    إدارة المستخدمين
                </a>
            </nav>

            <div class="dashboard-sidebar-cta">
                هذه الصفحة تعرض ملف المستخدم كما يظهر للمشرف، دون أي إمكانية لتعديله.
            </div>
        </aside>

        {{-- المحتوى الرئيسي --}}
        <div class="dashboard-main">

            {{-- هيدر الملف الشخصي --}}
            <div class="dashboard-header">
                <div class="profile-header">
                    <div class="profile-avatar-lg">
                        {{ mb_substr($user->name, 0, 1) }}
                    </div>
                    <div>
                        <h1 class="page-title" style="margin-bottom:4px;">
                            {{ $user->name }}
                        </h1>
                        <p class="page-subtitle">
                            {{ $user->email }}
                        </p>
                        <p class="page-subtitle">
                            نوع الحساب:
                            @if($user->role === 'seller')
                            مزود خدمة
                            @elseif($user->role === 'buyer')
                            مشتري
                            @elseif($user->role === 'admin')
                            مشرف
                            @else
                            {{ $user->role }}
                            @endif
                        </p>
                    </div>
                </div>
                <div>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline">
                        ← العودة لقائمة المستخدمين
                    </a>
                </div>
            </div>

            {{-- كروت إحصائية خاصة بالمستخدم --}}
            <div class="dashboard-stats-grid">
                <div class="dash-stat-card">
                    <div class="dash-stat-label">تاريخ الانضمام</div>
                    <div class="dash-stat-value">
                        {{ $user->created_at?->format('Y-m-d') }}
                    </div>
                    <div class="dash-stat-meta">
                        منذ {{ $user->created_at?->diffForHumans() }}
                    </div>
                </div>

                <div class="dash-stat-card">
                    <div class="dash-stat-label">خدماته (كمزود)</div>
                    <div class="dash-stat-value">{{ $servicesCount }}</div>
                    <div class="dash-stat-meta">
                        عدد الخدمات التي أضافها في المنصة
                    </div>
                </div>

                <div class="dash-stat-card">
                    <div class="dash-stat-label">طلباته كمشتري</div>
                    <div class="dash-stat-value">{{ $buyerOrdersCount }}</div>
                    <div class="dash-stat-meta">
                        الطلبات التي أنشأها كمشتري
                    </div>
                </div>

                <div class="dash-stat-card">
                    <div class="dash-stat-label">الطلبات المكتملة</div>
                    <div class="dash-stat-value">{{ $completedOrdersCount }}</div>
                    <div class="dash-stat-meta">
                        مكتملة كمشتري أو مزود خدمة
                    </div>
                </div>
            </div>

            {{-- معلومات تفصيلية --}}
            <div class="dash-block" style="margin-top:16px;">
                <div class="dash-block-header">
                    <div class="dash-block-title">بيانات الحساب</div>
                    <div class="dash-block-subtitle">
                        معلومات عامة عن هذا المستخدم
                    </div>
                </div>

                <div class="profile-info-grid">
                    <div class="profile-info-item">
                        <div class="profile-info-label">الاسم الكامل</div>
                        <div class="profile-info-value">{{ $user->name }}</div>
                    </div>

                    <div class="profile-info-item">
                        <div class="profile-info-label">البريد الإلكتروني</div>
                        <div class="profile-info-value">{{ $user->email }}</div>
                    </div>

                    <div class="profile-info-item">
                        <div class="profile-info-label">نوع الحساب</div>
                        <div class="profile-info-value">
                            @if($user->role === 'seller')
                            مزود خدمة
                            @elseif($user->role === 'buyer')
                            مشتري
                            @elseif($user->role === 'admin')
                            مشرف
                            @else
                            {{ $user->role }}
                            @endif
                        </div>
                    </div>

                    <div class="profile-info-item">
                        <div class="profile-info-label">تاريخ التسجيل</div>
                        <div class="profile-info-value">
                            {{ $user->created_at?->format('Y-m-d H:i') }}
                        </div>
                    </div>

                    <div class="profile-info-item">
                        <div class="profile-info-label">البريد مُفعل؟</div>
                        <div class="profile-info-value">
                            @if($user->email_verified_at)
                            نعم، منذ {{ $user->email_verified_at->diffForHumans() }}
                            @else
                            لا، لم يتم التفعيل بعد
                            @endif
                        </div>
                    </div>

                    {{-- يمكنك إضافة حقول أخرى مثل رقم الجوال إن وجد --}}
                </div>
            </div>

            {{-- بلوك إضافي: نبذة عن تعامل المستخدم في الطلبات --}}
            <div class="dash-block" style="margin-top:16px;">
                <div class="dash-block-header">
                    <div class="dash-block-title">ملخص الطلبات</div>
                    <div class="dash-block-subtitle">
                        نظرة سريعة على تعامل المستخدم في الطلبات
                    </div>
                </div>

                <div class="profile-info-grid">
                    <div class="profile-info-item">
                        <div class="profile-info-label">كمشتري</div>
                        <div class="profile-info-value">
                            {{ $buyerOrdersCount }} طلب
                        </div>
                    </div>

                    <div class="profile-info-item">
                        <div class="profile-info-label">كمزود خدمة</div>
                        <div class="profile-info-value">
                            {{ $sellerOrdersCount }} طلب
                        </div>
                    </div>

                    <div class="profile-info-item">
                        <div class="profile-info-label">طلبات مكتملة</div>
                        <div class="profile-info-value">
                            {{ $completedOrdersCount }} طلب
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection