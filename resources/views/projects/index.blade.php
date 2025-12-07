@extends('layouts.app')

@section('title', 'المشاريع المفتوحة')

@section('content')
<section class="services-page">
    <div class="container">

        <div class="page-header">
            <div>
                <h1 class="page-title">المشاريع المفتوحة</h1>
                <p class="page-subtitle">
                    استعرض المشاريع المتاحة وقدّم عروضك كمزود خدمة.
                </p>
            </div>

            @auth
            @if(auth()->user()->role === 'buyer' || auth()->user()->role === 'both')
            <a href="{{ route('projects.create') }}" class="btn btn-primary">
                + إنشاء مشروع جديد
            </a>
            @endif
            @endauth
        </div>

        <form method="GET" class="filters-bar" style="margin-bottom:16px;">
            <div class="filters-main">
                <div class="filter-item">
                    <label>بحث</label>
                    <div class="filter-input-wrap">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="ابحث بعنوان المشروع...">
                    </div>
                </div>
            </div>
        </form>

        <div class="results-summary">
            <div>عدد المشاريع: <strong>{{ $projects->total() }}</strong></div>
        </div>

        <div class="services-grid services-grid-page">
            @forelse($projects as $project)
            <div class="service-card">
                <div class="service-image">
                    @if($project->image_path)
                    <img src="{{ asset('storage/' . $project->image_path) }}" alt="صورة المشروع"
                        class="service-image-img">
                    @else
                    <span class="service-badge">
                        {{ $project->category ?: 'مشروع عام' }}
                    </span>
                    @endif
                </div>
                <div class="service-body">
                    <div class="service-title">{{ $project->title }}</div>
                    <div class="service-seller">
                        بواسطة {{ $project->owner->name ?? 'مستخدم' }}
                    </div>
                    <div class="service-desc">
                        {{ Str::limit($project->description, 90) }}
                    </div>
                    <div class="service-meta">
                        <div>
                            @if($project->budget_min || $project->budget_max)
                            ميزانية:
                            <span class="service-price">
                                {{ $project->budget_min ?? $project->budget_max }}
                                @if($project->budget_max && $project->budget_max != $project->budget_min)
                                - {{ $project->budget_max }}
                                @endif
                                $
                            </span>
                            @else
                            ميزانية غير محددة
                            @endif
                        </div>
                        <div>
                            عروض: {{ $project->bids_count }}
                        </div>
                    </div>
                </div>
                <div class="service-footer">
                    <div class="service-footer-left">
                        <span>الحالة: {{ $project->status }}</span>
                    </div>
                    <a href="{{ route('projects.show', $project) }}" class="btn btn-outline service-details-btn">
                        عرض التفاصيل
                    </a>
                </div>
            </div>
            @empty
            <p>لا توجد مشاريع حالياً.</p>
            @endforelse
        </div>

        {{ $projects->withQueryString()->links() }}

    </div>
</section>
@endsection