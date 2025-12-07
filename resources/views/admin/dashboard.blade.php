@extends('layouts.app')

@section('title', 'لوحة المشرف')

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
                <a href="{{ route('admin.dashboard') }}" class="dash-nav-link active">
                    لوحة التحكم
                </a>

                <a href="{{ route('admin.services.index') }}" class="dash-nav-link">
                    إدارة الخدمات
                </a>

                <a href="{{ route('admin.users.index') }}" class="dash-nav-link">
                    إدارة المستخدمين
                </a>

                {{-- يمكنك لاحقاً إضافة روابط أخرى مثل إدارة الطلبات / المستخدمين --}}
                {{-- <a href="#" class="dash-nav-link">إدارة الطلبات</a> --}}
                {{-- <a href="#" class="dash-nav-link">إدارة المستخدمين</a> --}}
            </nav>

            <div class="dashboard-sidebar-cta">
                يمكنك من هنا متابعة أداء المنصّة، مراجعة الخدمات والطلبات والمستخدمين،
                واتخاذ قرارات سريعة من مكان واحد.
            </div>
        </aside>

        {{-- المحتوى الرئيسي --}}
        <div class="dashboard-main">

            {{-- عنوان الصفحة --}}
            <div class="dashboard-header">
                <div>
                    <h1 class="page-title">لوحة المشرف</h1>
                    <p class="page-subtitle">
                        نظرة عامة سريعة على مستخدمي المنصة، الخدمات والطلبات.
                    </p>
                </div>
            </div>

            {{-- كروت الإحصائيات --}}
            <div class="dashboard-stats-grid">

                {{-- المستخدمون --}}
                <div class="dash-stat-card">
                    <div class="dash-stat-label">إجمالي المستخدمين</div>
                    <div class="dash-stat-value">{{ $totalUsers }}</div>
                    <div class="dash-stat-meta">
                        {{ $totalSellers }} مزود خدمة • {{ $totalBuyers }} مشتري
                    </div>
                </div>

                {{-- الخدمات --}}
                <div class="dash-stat-card">
                    <div class="dash-stat-label">الخدمات</div>
                    <div class="dash-stat-value">{{ $totalServices }}</div>
                    <div class="dash-stat-meta">
                        {{ $approvedServices }} مقبولة •
                        {{ $pendingServices }} قيد المراجعة •
                        {{ $rejectedServices }} مرفوضة
                    </div>
                </div>

                {{-- الطلبات --}}
                <div class="dash-stat-card">
                    <div class="dash-stat-label">الطلبات</div>
                    <div class="dash-stat-value">{{ $totalOrders }}</div>
                    <div class="dash-stat-meta">
                        {{ $pendingOrders }} قيد التنفيذ •
                        {{ $deliveredOrders }} تم التسليم •
                        {{ $completedOrders }} مكتملة
                    </div>
                </div>

                {{-- بطاقة إضافية يمكن تخصيصها لاحقاً (مثلاً الإيرادات) --}}
                <div class="dash-stat-card">
                    <div class="dash-stat-label">نظرة عامة</div>
                    <div class="dash-stat-value">منصة كوفية</div>
                    <div class="dash-stat-meta">
                        راقب أداء المنصة وراجع الطلبات الحرجة والخدمات المعلّقة.
                    </div>
                </div>
            </div>

            {{-- شبكة المحتوى (جداول مختصرة) --}}
            <div class="dashboard-content-grid">

                {{-- بلوك: أحدث الخدمات --}}
                <div class="dash-block">
                    <div class="dash-block-header">
                        <div class="dash-block-title">أحدث الخدمات المضافة</div>
                        <div class="dash-block-subtitle">
                            آخر {{ $latestServices->count() }} خدمات
                        </div>
                    </div>

                    <div class="services-list-table">
                        <div class="services-list-row services-list-header">
                            <div>الخدمة</div>
                            <div>مزود الخدمة</div>
                            <div>الحالة</div>
                        </div>

                        @forelse($latestServices as $service)
                        <div class="services-list-row">
                            <div>
                                <div class="svc-list-title">{{ $service->title }}</div>
                                <div class="svc-list-sub">
                                    #{{ $service->id }} • {{ $service->created_at->diffForHumans() }}
                                </div>
                            </div>
                            <div>
                                {{ optional($service->seller)->name ?? '-' }}
                            </div>
                            <div>
                                <span class="status-badge status-{{ $service->status }}">
                                    @switch($service->status)
                                    @case('approved') مقبولة @break
                                    @case('pending') قيد المراجعة @break
                                    @case('rejected') مرفوضة @break
                                    @default —
                                    @endswitch
                                </span>
                            </div>
                        </div>
                        @empty
                        <p class="empty-state-text">لا توجد خدمات مسجلة حتى الآن.</p>
                        @endforelse
                    </div>

                    <div class="dash-block-footer">
                        <a href="{{ route('admin.services.index') }}" class="link-button">
                            عرض جميع الخدمات
                        </a>
                    </div>
                </div>

                {{-- بلوك: الخدمات المعلّقة / أحدث المستخدمين --}}
                <div class="dash-block">
                    <div class="dash-block-header">
                        <div class="dash-block-title">خدمات قيد المراجعة</div>
                        <div class="dash-block-subtitle">
                            تحتاج إلى قبول أو رفض
                        </div>
                    </div>

                    <div class="services-list-table">
                        @forelse($pendingServicesList as $service)
                        <div class="services-list-row">
                            <div>
                                <div class="svc-list-title">{{ $service->title }}</div>
                                <div class="svc-list-sub">
                                    {{ optional($service->seller)->name ?? 'غير معروف' }}
                                </div>
                            </div>
                            <div style="text-align:left;">
                                <a href="{{ route('admin.services.index', ['status' => 'pending']) }}"
                                    class="link-button small">
                                    إدارة
                                </a>
                            </div>
                        </div>
                        @empty
                        <p class="empty-state-text">لا توجد خدمات معلّقة حالياً.</p>
                        @endforelse
                    </div>

                    <hr style="border:none; border-top:1px solid #eee; margin:10px 0;">

                    <div class="dash-block-header" style="margin-top:4px;">
                        <div class="dash-block-title" style="font-size:14px;">أحدث المستخدمين</div>
                        <div class="dash-block-subtitle">آخر {{ $latestUsers->count() }} مستخدمين</div>
                    </div>

                    <div class="users-list">
                        @forelse($latestUsers as $user)
                        <div class="users-list-row">
                            <div class="user-circle-avatar">
                                {{ mb_substr($user->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="users-list-name">{{ $user->name }}</div>
                                <div class="users-list-meta">
                                    {{ $user->email }} •
                                    @if($user->role === 'seller') مزود خدمة
                                    @elseif($user->role === 'buyer') مشتري
                                    @else {{ $user->role }}
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <p class="empty-state-text">لا يوجد مستخدمون بعد.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection