@extends('layouts.app') {{-- أو اسم الـ layout عندك --}}

@section('title', 'مشاريعي')

@section('content')
<section class="dashboard-section buyer-dashboard-section">
    <div class="container dashboard-layout">

        <!-- الشريط الجانبي (مبسط) -->
        <aside class="dashboard-sidebar buyer-sidebar">
            <div class="dashboard-user">
                <div class="dashboard-avatar">
                    {{ mb_substr(auth()->user()->name, 0, 1) }}
                </div>
                <div>
                    <div class="dashboard-username">{{ auth()->user()->name }}</div>
                    <div class="dashboard-user-meta">
                        مشتري خدمات
                    </div>
                </div>
            </div>

            <nav class="dashboard-nav">
                {{-- يمكنك لاحقاً إضافة روابط أخرى هنا مثل نظرة عامة / طلباتي --}}
                <a href="#" class="dash-nav-link active">
                    مشاريعي
                </a>
            </nav>

            <div class="dashboard-sidebar-cta">
                يمكنك من هنا متابعة جميع مشاريعك المفتوحة والمنجزة.
            </div>
        </aside>

        <!-- المحتوى الرئيسي -->
        <div class="dashboard-main">

            <div class="dashboard-header">
                <div>
                    <h1 class="page-title">مشاريعي</h1>
                    <p class="page-subtitle">
                        جميع المشاريع التي قمت بإنشائها في المنصة مع حالتها وعدد العروض لكل مشروع.
                    </p>
                </div>

                <a href="{{ route('projects.create') }}" class="btn btn-primary">
                    + إنشاء مشروع جديد
                </a>
            </div>

            {{-- بلوك عرض قائمة المشاريع --}}
            <section class="dash-block">
                <div class="dash-block-header">
                    <h2 class="dash-block-title">قائمة مشاريعي</h2>
                    <span class="dash-block-link">
                        إجمالي: {{ $projects->total() }} مشروع
                    </span>
                </div>

                @if($projects->isEmpty())
                <p class="buyer-reminder-text">
                    لا يوجد لديك أي مشاريع حتى الآن. يمكنك البدء بإنشاء أول مشروع من الزر بالأعلى.
                </p>
                @else
                <div class="table-wrapper">
                    <div class="services-list-table">
                        <div class="services-list-row services-list-header">
                            <div>عنوان المشروع</div>
                            <div>الحالة</div>
                            <div>عدد العروض</div>
                            <div>تاريخ الإنشاء</div>
                            <div>إجراءات</div>
                        </div>

                        @foreach($projects as $project)
                        <div class="services-list-row">
                            <div>
                                <div class="svc-list-title">
                                    {{ $project->title }}
                                </div>
                                <div class="svc-list-sub">
                                    {{ $project->category ?: 'بدون تصنيف' }}
                                </div>
                            </div>

                            <div>
                                <span class="status-badge
                                            @if($project->status === 'open') active
                                            @elseif($project->status === 'in_progress') in-progress
                                            @elseif($project->status === 'completed') waiting
                                            @elseif($project->status === 'cancelled') late
                                            @endif
                                        ">
                                    {{ $project->status }}
                                </span>
                            </div>

                            <div>
                                {{ $project->bids_count }} عرض
                            </div>

                            <div>
                                {{ $project->created_at->format('Y-m-d') }}
                            </div>

                            <div class="svc-list-actions">
                                <a href="{{ route('projects.show', $project) }}" class="btn btn-outline btn-xs">
                                    عرض
                                </a>

                                @if($project->status === 'open')
                                <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline btn-xs">
                                    تعديل
                                </a>

                                <form action="{{ route('projects.destroy', $project) }}" method="POST"
                                    style="display:inline-block;"
                                    onsubmit="return confirm('هل أنت متأكد من حذف هذا المشروع؟ لن تتمكن من استرجاعه.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-xs">
                                        حذف
                                    </button>
                                </form>
                                @endif
                            </div>

                        </div>
                        @endforeach
                    </div>
                </div>

                <div style="margin-top:10px;">
                    {{ $projects->links() }}
                </div>
                @endif
            </section>

        </div>
    </div>
</section>
@endsection