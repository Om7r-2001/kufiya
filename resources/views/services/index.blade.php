@extends('layouts.app')

@section('title', 'جميع الخدمات | منصة كوفية')

@section('content')
<main>
    <section class="services-section">
        <div class="container">

            <div class="page-header">
                <div>
                    <h1 class="page-title">جميع الخدمات</h1>
                    <p class="page-subtitle">
                        تصفّح الخدمات حسب التصنيف أو ابحث بكلمة مفتاحية للوصول إلى ما تحتاجه بسرعة.
                    </p>
                </div>
                <form method="GET" action="{{ route('services.index') }}" class="services-search-form">
                    <input type="text" name="q" value="{{ $search }}" placeholder="ابحث عن خدمة...">
                    @if($currentCategory)
                    <input type="hidden" name="category" value="{{ $currentCategory->slug }}">
                    @endif
                    <button type="submit" class="btn btn-outline">بحث</button>
                </form>
            </div>

            <div class="services-layout">
                {{-- فلترة حسب التصنيف --}}
                <aside class="services-sidebar">
                    <div class="filter-card">
                        <h3 class="filter-title">التصنيفات</h3>
                        <ul class="filter-list">
                            <li>
                                <a href="{{ route('services.index') }}"
                                    class="{{ !$currentCategory ? 'active-filter' : '' }}">
                                    جميع التصنيفات
                                </a>
                            </li>
                            @foreach($categories as $category)
                            <li>
                                <a href="{{ route('services.index', ['category' => $category->slug] + request()->except('page')) }}"
                                    class="{{ $currentCategory && $currentCategory->id === $category->id ? 'active-filter' : '' }}">
                                    {{ $category->name }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </aside>

                {{-- قائمة الخدمات --}}
                <div class="services-grid">
                    @if($services->count())
                    @foreach($services as $service)
                    <article class="service-card">
                        <a href="{{ route('services.show', $service->slug) }}" class="service-thumbnail">
                            @php
                            $mainImage = $service->images->firstWhere('is_main', true) ?? $service->images->first();
                            @endphp
                            @if($mainImage)
                            <img src="{{ asset('storage/'.$mainImage->path) }}" alt="{{ $service->title }}">
                            @else
                            <div class="service-placeholder"></div>
                            @endif>
                        </a>
                        <div class="service-body">
                            <div class="service-category">
                                {{ $service->category?->name ?? 'غير مصنّف' }}
                            </div>
                            <h2 class="service-title">
                                <a href="{{ route('services.show', $service->slug) }}">
                                    {{ $service->title }}
                                </a>
                            </h2>
                            <p class="service-desc">
                                {{ \Illuminate\Support\Str::limit($service->short_description ?? $service->description, 90) }}
                            </p>
                            <div class="service-meta-row">
                                <div class="service-seller">
                                    {{ $service->seller->name }}
                                </div>
                                <div class="service-price">
                                    ابتداءً من {{ number_format($service->price,2) }}$
                                </div>
                            </div>
                            <div class="service-meta-row">
                                <div class="service-rating">
                                    @if($service->rating_count)
                                    ★ {{ $service->rating_avg }} ({{ $service->rating_count }})
                                    @else
                                    لا يوجد تقييم بعد
                                    @endif
                                </div>
                                <div class="service-delivery">
                                    {{ $service->delivery_time }} يوم
                                </div>
                            </div>
                        </div>
                    </article>
                    @endforeach

                    <div style="margin-top:16px;">
                        {{ $services->links() }}
                    </div>
                    @else
                    <p style="font-size:14px; color:var(--color-muted);">
                        لا توجد خدمات مطابقة لخيارات الفلترة/البحث الحالية.
                    </p>
                    @endif
                </div>

            </div>

        </div>
    </section>
</main>

@endsection