@extends('layouts.app')

@section('title', 'إدارة المستخدمين')

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
                من هنا يمكنك مراجعة جميع مستخدمي المنصة، تصفيتهم حسب نوع الحساب
                أو البحث بالاسم والبريد، بالإضافة إلى تعديل الدور أو حذف المستخدم.
            </div>
        </aside>

        {{-- المحتوى الرئيسي --}}
        <div class="dashboard-main">

            {{-- عنوان صفحة إدارة المستخدمين --}}
            <div class="dashboard-header">
                <div>
                    <h1 class="page-title">إدارة المستخدمين</h1>
                    <p class="page-subtitle">
                        عرض، بحث وتصفية جميع مستخدمي المنصة مع إحصائيات سريعة لكل نوع حساب.
                    </p>
                </div>
            </div>

            {{-- رسائل النجاح / الأخطاء العامة --}}
            @if(session('success'))
            <div class="alert-success">
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="alert-error">
                @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
                @endforeach
            </div>
            @endif

            {{-- كروت الإحصائيات --}}
            <div class="dashboard-stats-grid">
                <div class="dash-stat-card">
                    <div class="dash-stat-label">إجمالي المستخدمين</div>
                    <div class="dash-stat-value">{{ $totalUsers ?? $users->total() }}</div>
                    <div class="dash-stat-meta">كل الحسابات المسجلة في المنصة</div>
                </div>

                <div class="dash-stat-card">
                    <div class="dash-stat-label">مزودو الخدمة</div>
                    <div class="dash-stat-value">{{ $totalSellers ?? 0 }}</div>
                    <div class="dash-stat-meta">حسابات مقدمي الخدمات</div>
                </div>

                <div class="dash-stat-card">
                    <div class="dash-stat-label">المشترون</div>
                    <div class="dash-stat-value">{{ $totalBuyers ?? 0 }}</div>
                    <div class="dash-stat-meta">حسابات العملاء / المشترين</div>
                </div>

                <div class="dash-stat-card">
                    <div class="dash-stat-label">المشرفون</div>
                    <div class="dash-stat-value">{{ $totalAdmins ?? 0 }}</div>
                    <div class="dash-stat-meta">حسابات الإدارة</div>
                </div>
            </div>

            {{-- شريط البحث والتصفية --}}
            <div class="services-filters-bar" style="margin-bottom: 16px;">
                <form method="GET" action="{{ route('admin.users.index') }}" class="services-filters-form">

                    <div class="services-filters-group">
                        <input type="text" name="q" class="input-control"
                            placeholder="بحث بالاسم أو البريد الإلكتروني..." value="{{ $search ?? '' }}">
                    </div>

                    <div class="services-filters-group">
                        <select name="role" class="input-control">
                            <option value="">كل الأدوار</option>
                            <option value="seller" {{ ($role ?? '') === 'seller' ? 'selected' : '' }}>مزود خدمة</option>
                            <option value="buyer" {{ ($role ?? '') === 'buyer' ? 'selected' : '' }}>مشتري</option>
                            <option value="admin" {{ ($role ?? '') === 'admin' ? 'selected' : '' }}>مشرف</option>
                        </select>
                    </div>

                    <div class="services-filters-actions">
                        <button type="submit" class="btn btn-primary">
                            تطبيق الفلترة
                        </button>

                        @if(($role ?? null) || ($search ?? null))
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline">
                            إعادة التعيين
                        </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- جدول المستخدمين --}}
            <div class="services-list-table">
                <div class="services-list-row services-list-header">
                    <div>المستخدم</div>
                    <div>نوع الحساب</div>
                    <div>تاريخ التسجيل</div>
                    <div>إجراءات</div>
                </div>

                @forelse($users as $user)
                <div class="services-list-row">
                    {{-- بيانات المستخدم --}}
                    <div>
                        <div class="svc-list-title">{{ $user->name }}</div>
                        <div class="svc-list-sub">
                            {{ $user->email }}
                        </div>
                    </div>

                    {{-- نوع الحساب مع فورم تغيير الدور --}}
                    <div>
                        @if(auth()->id() === $user->id && $user->role === 'admin')
                        {{-- المستخدم الحالي وهو مشرف: عرض الدور فقط بدون إمكانية التعديل --}}
                        <span class="status-badge"
                            style="background-color:#E0F2FE; color:#0369A1; padding:4px 10px; border-radius:999px; font-size:11px;">
                            مشرف (لا يمكن تغيير الدور)
                        </span>
                        @else
                        {{-- باقي المستخدمين: يمكن تعديل الدور --}}
                        <form action="{{ route('admin.users.updateRole', $user) }}" method="POST">
                            @csrf
                            <select name="role" class="input-control" style="min-width:130px;"
                                onchange="this.form.submit()">
                                <option value="buyer" {{ $user->role === 'buyer' ? 'selected' : '' }}>مشتري</option>
                                <option value="seller" {{ $user->role === 'seller' ? 'selected' : '' }}>مزود خدمة
                                </option>
                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>مشرف</option>
                            </select>
                        </form>
                        @endif
                    </div>


                    {{-- تاريخ التسجيل --}}
                    <div>
                        {{ $user->created_at?->format('Y-m-d') }}
                        <div class="svc-list-sub">
                            {{ $user->created_at?->diffForHumans() }}
                        </div>
                    </div>

                    {{-- الإجراءات (عرض / حذف) --}}
                    <div class="svc-list-actions">

                        {{-- رابط لصفحة تفاصيل المستخدمين --}}
                        <a href="{{ route('admin.users.show', $user) }}" class="link-button small">
                            عرض التفاصيل
                        </a>

                        {{-- زر حذف المستخدم مع حماية بسيطة --}}
                        @if(auth()->id() !== $user->id)
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline;"
                            onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="link-button small" style="color:#b91c1c;">
                                حذف
                            </button>
                        </form>
                        @else
                        <span class="svc-list-sub">لا يمكنك حذف حسابك.</span>
                        @endif
                    </div>
                </div>
                @empty
                <p class="empty-state-text">
                    لا يوجد مستخدمون مطابقون لمعايير البحث الحالية.
                </p>
                @endforelse
            </div>

            {{-- ترقيم الصفحات --}}
            <div style="margin-top: 16px;">
                {{ $users->links() }}
            </div>

        </div>
    </div>
</section>
@endsection