@extends('layouts.app')

@section('title', 'مزود الخدمة - ' . $seller->name)

@section('content')
<section class="page-section services-page">
    <div class="container">

        {{-- الهيدر --}}
        <div class="page-section-header">
            <div>
                <h1 class="section-title">مزود الخدمة</h1>
                <p class="section-subtitle">
                    معلومات عن {{ $seller->name }} والخدمات التي يقدمها في منصة كوفية.
                </p>
            </div>

            <a href="{{ route('providers.index') }}" class="link-muted">
                ← العودة إلى قائمة مزودي الخدمات
            </a>
        </div>

        <div class="service-layout-grid">

            {{-- العمود الرئيسي --}}
            <div class="service-main">

                {{-- كرت معلومات المزوّد (نفس روح seller-card) --}}
                <div class="seller-card" style="margin-bottom: 20px;">
                    <div class="seller-header">
                        <div class="seller-avatar large">
                            {{ mb_substr($seller->name, 0, 1) }}
                        </div>

                        <div class="seller-main-info">
                            <h2 class="seller-name">{{ $seller->name }}</h2>
                            <div class="seller-meta-line">
                                <span>مزود {{ $services->total() }} خدمة منشورة</span>

                                @if(!is_null($seller->avg_rating))
                                <span class="dot-separator"></span>
                                <span>تقييم {{ number_format($seller->avg_rating, 1) }} من {{ $seller->reviews_count }}
                                    تقييم</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="seller-extra">
                        <div class="seller-extra-item">
                            <span class="seller-extra-label">البلد</span>
                            <span class="seller-extra-value">{{ $seller->country ?? 'غير محدد' }}</span>
                        </div>

                        <div class="seller-extra-item">
                            <span class="seller-extra-label">تاريخ الانضمام</span>
                            <span class="seller-extra-value">
                                {{ optional($seller->created_at)->format('Y-m-d') }}
                            </span>
                        </div>

                        @isset($completedOrdersCount)
                        <div class="seller-extra-item">
                            <span class="seller-extra-label">طلبات منجزة</span>
                            <span class="seller-extra-value">{{ $completedOrdersCount }}</span>
                        </div>
                        @endisset
                    </div>

                    @if($seller->bio)
                    <p class="seller-bio">
                        {{ $seller->bio }}
                    </p>
                    @endif
                </div>

                {{-- عنوان قسم الخدمات --}}
                <h3 class="section-title small" style="margin-bottom: 12px;">
                    خدمات {{ $seller->name }}
                </h3>

                <div class="services-grid">
                    @if($services->count())
                    @foreach($services as $service)
                    <div class="service-card">
                        <a href="{{ route('services.show', $service->slug) }}" class="service-image">
                            @php
                            $mainImage = $service->images->firstWhere('is_main', true) ?? $service->images->first();
                            @endphp

                            @if($mainImage)
                            <img src="{{ asset('storage/'.$mainImage->path) }}" alt="{{ $service->title }}"
                                class="service-image-img">
                            @endif

                            <span class="service-badge">
                                {{ $service->category?->name ?? 'خدمة مميزة' }}
                            </span>
                        </a>

                        <div class="service-body">
                            <div class="service-title">
                                {{ $service->title }}
                            </div>
                            <div class="service-seller">
                                بواسطة: {{ $service->seller->name }}
                            </div>
                            <div class="service-meta">
                                <div>تسليم خلال {{ $service->delivery_time }} يوم</div>
                                <div class="service-price">
                                    ابتداءً من {{ number_format($service->price, 2) }}$
                                </div>
                            </div>
                        </div>

                        <div class="service-footer">
                            <div class="stars">
                                @if($service->rating_count)
                                ★ {{ $service->rating_avg }} ({{ $service->rating_count }})
                                @else
                                لا يوجد تقييم بعد
                                @endif
                            </div>
                            <a href="{{ route('services.show', $service->slug) }}" class="btn btn-outline"
                                style="font-size:11px; padding:4px 10px;">
                                عرض التفاصيل
                            </a>
                        </div>
                    </div>

                    @endforeach
                    @else
                    {{-- إن لم توجد خدمات بعد، نعرض الأمثلة الثابتة القديمة أو رسالة بسيطة --}}
                    <p style="font-size:13px; color:var(--color-muted);">
                        لا توجد خدمات مضافة بعد، سيتم عرضها هنا بعد إضافة أول خدمة.
                    </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection