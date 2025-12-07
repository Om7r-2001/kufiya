@extends('layouts.app')

@section('title', 'إدارة الخدمات')

@section('content')
<section class="dashboard-section">
    <div class="container dashboard-layout">

        {{-- الشريط الجانبي للمشرف (يمكن تعديله كما تريد) --}}
        <aside class="dashboard-sidebar">
            <div class="dashboard-user">
                <div class="dashboard-avatar">A</div>
                <div>
                    <div class="dashboard-username">{{ auth()->user()->name }}</div>
                    <div class="dashboard-user-meta">مشرف النظام</div>
                </div>
            </div>

            <nav class="dashboard-nav">
                <a href="{{ route('admin.dashboard') }}" class="dash-nav-link">
                    لوحة التحكم
                </a>
                <a href="{{ route('admin.services.index') }}" class="dash-nav-link active">
                    إدارة الخدمات
                </a>
                <a href="{{ route('admin.users.index') }}" class="dash-nav-link">
                    إدارة المستخدمين
                </a>
            </nav>

            <div class="dashboard-sidebar-cta">
                يمكنك من هنا مراجعة جميع الخدمات والتحكم في حالتها أو حذفها.
            </div>
        </aside>

        {{-- المحتوى الرئيسي --}}
        <div class="dashboard-main">

            <div class="dashboard-header">
                <div>
                    <h1 class="page-title">إدارة الخدمات</h1>
                    <p class="page-subtitle">
                        مراجعة الخدمات المضافة من مزودي الخدمات، وتفعيلها أو رفضها.
                    </p>
                </div>
            </div>

            {{-- فلاتر البحث --}}
            <form method="GET" action="{{ route('admin.services.index') }}" class="filters-bar">
                <div class="filters-main">
                    <div class="filter-item">
                        <label>بحث عن خدمة</label>
                        <div class="filter-input-wrap">
                            <input type="text" name="q" value="{{ request('q') }}"
                                placeholder="عنوان الخدمة أو جزء منه...">
                        </div>
                    </div>

                    <div class="filter-item small">
                        <label>التصنيف</label>
                        <select name="category_id">
                            <option value="">كل التصنيفات</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(request('category_id')==$category->id)>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-item small">
                        <label>الحالة</label>
                        <select name="status">
                            <option value="">كل الحالات</option>
                            <option value="pending" @selected(request('status')==='pending' )>معلقة</option>
                            <option value="approved" @selected(request('status')==='approved' )>مقبولة</option>
                            <option value="rejected" @selected(request('status')==='rejected' )>مرفوضة</option>
                        </select>
                    </div>
                </div>

                <div class="filters-extra">
                    <button type="submit" class="btn btn-primary">
                        تطبيق الفلاتر
                    </button>
                    <a href="{{ route('admin.services.index') }}" class="btn btn-outline filters-reset-btn">
                        إعادة تعيين
                    </a>
                </div>
            </form>

            {{-- رسائل فلاش --}}
            @if(session('success'))
            <div style="background:#E8FFF3; border-radius:12px; padding:8px 12px; font-size:12px; margin-bottom:10px;">
                {{ session('success') }}
            </div>
            @endif

            {{-- جدول الخدمات --}}
            <div class="dash-block">
                <div class="dash-block-header">
                    <div class="dash-block-title">
                        كل الخدمات ({{ $services->total() }})
                    </div>
                </div>

                <div class="services-list-table">
                    <div class="services-list-row services-list-header">
                        <div>الخدمة</div>
                        <div>مزود الخدمة</div>
                        <div>التصنيف</div>
                        <div>الحالة</div>
                        <div>إجراءات</div>
                    </div>

                    @forelse($services as $service)
                    <div class="services-list-row">
                        <div>
                            <div class="svc-list-title">
                                {{ $service->title }}
                            </div>
                            <div class="svc-list-sub">
                                #{{ $service->id }} • أضيفت {{ $service->created_at->diffForHumans() }}
                            </div>
                        </div>

                        <div>
                            {{ optional($service->seller)->name ?? '-' }}
                        </div>

                        <div>
                            {{ optional($service->category)->name ?? '-' }}
                        </div>

                        <div>
                            <span class="status-badge status-{{ $service->status }}">
                                @switch($service->status)
                                @case('approved') مقبولة @break
                                @case('rejected') مرفوضة @break
                                @default معلقة
                                @endswitch
                            </span>
                        </div>

                        <div class="svc-list-actions">
                            <a href="{{ route('services.show', $service->slug) }}" class="link-button small">
                                عرض في المنصة
                            </a>

                            {{-- تغيير الحالة --}}
                            <form method="POST" action="{{ route('admin.services.updateStatus', $service) }}">
                                @csrf
                                @method('PATCH')
                                <select name="status" onchange="this.form.submit()"
                                    style="font-size:11px; padding:4px 8px; border-radius:999px;">
                                    <option value="">تغيير الحالة</option>
                                    <option value="pending" @selected($service->status === 'pending')>معلقة</option>
                                    <option value="approved" @selected($service->status === 'approved')>مقبولة</option>
                                    <option value="rejected" @selected($service->status === 'rejected')>مرفوضة</option>
                                </select>
                            </form>

                            {{-- حذف الخدمة --}}
                            <form method="POST" action="{{ route('admin.services.destroy', $service) }}"
                                onsubmit="return confirm('هل أنت متأكد من حذف هذه الخدمة نهائياً؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="link-button small" style="color:#c0392b;">
                                    حذف
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <p style="font-size:13px; color:#777; margin-top:10px;">
                        لا توجد خدمات مطابقة حالياً.
                    </p>
                    @endforelse
                </div>

                {{-- ترقيم الصفحات --}}
                <div style="margin-top:10px;">
                    {{ $services->links() }}
                </div>
            </div>

        </div>
    </div>
</section>
@endsection