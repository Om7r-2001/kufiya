@extends('layouts.app')

@section('title', 'منصة كوفية | للخدمات الإلكترونية')

@section('content')
<main>

    <!-- Hero -->
    <section class="hero" id="home">
        <div class="container hero-grid">
            <div>
                <h1 class="hero-title">
                    ابحث عن افضل مزودي الخدمات في
                    <span> فلسطين </span>
                </h1>
                <p class="hero-subtitle">
                    ابحث عن خبير في مجالات مهنية، صحية، تعليمية، مالية، رقمية وغيرها، وابدأ بالتعاون معه بثقة
                    عبر نظام دفع آمن ومراسلة مدمجة وتقييمات حقيقية.
                </p>

                <div class="hero-badges">
                    <span class="badge badge-accent">جديد</span>
                    <span class="badge">دفع آمن</span>
                    <span class="badge">مراسلة مباشرة</span>
                    <span class="badge">تقييمات موثوقة</span>
                </div>

                <form class="hero-search" method="GET" action="{{ route('services.index') }}">
                    <input type="text" name="q" value="{{ $search }}"
                        placeholder="ابحث عن خدمة (مثلاً: تصميم شعار، استشارة قانونية، برمجة…)" />

                    <select name="category">
                        <option value="">كل التصنيفات</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->slug }}"
                            {{ request('category') == $category->slug ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>

                    <button type="submit" class="btn btn-primary">بحث</button>
                </form>


                <div class="hero-meta">
                    <div class="hero-meta-item">
                        <span>+250</span> خدمة متاحة حالياً
                    </div>
                    <div class="hero-meta-item">
                        <span>+120</span> مزود خدمة نشط
                    </div>
                    <div class="hero-meta-item">
                        <span>+1,000</span> عملية مكتملة
                    </div>
                </div>
            </div>

            <div>
                <div class="hero-card">
                    <div class="hero-card-header">
                        <div>
                            <div class="hero-card-title">لوحة تحكم البائع</div>
                            <div style="font-size:11px; color:var(--color-muted);">
                                مثال حي لواجهة مزود الخدمة في كوفية
                            </div>
                        </div>
                        <div class="hero-card-pill">عرض توضيحي</div>
                    </div>

                    <div class="hero-card-stats">
                        <div class="stat">
                            <div class="stat-label">طلبات الشهر</div>
                            <div class="stat-value">14</div>
                        </div>
                        <div class="stat">
                            <div class="stat-label">أرباح متوقعة</div>
                            <div class="stat-value">820$</div>
                        </div>
                        <div class="stat">
                            <div class="stat-label">خدمات نشطة</div>
                            <div class="stat-value">5</div>
                        </div>
                    </div>

                    <div class="hero-mini-services">
                        <div class="mini-service">
                            <div class="mini-service-name">تصميم هوية لمتجر إلكتروني</div>
                            <div class="mini-service-meta">
                                <span class="mini-dot"></span>
                                <span>بميزانية 120$</span>
                            </div>
                        </div>
                        <div class="mini-service">
                            <div class="mini-service-name">تطوير موقع حجز مواعيد</div>
                            <div class="mini-service-meta">
                                <span class="mini-dot"></span>
                                <span>تسليم خلال 7 أيام</span>
                            </div>
                        </div>
                        <div class="mini-service">
                            <div class="mini-service-name">استشارة قانونية تجارية</div>
                            <div class="mini-service-meta">
                                <span class="mini-dot"></span>
                                <span>جلسة أونلاين 60 دقيقة</span>
                            </div>
                        </div>
                    </div>

                    <div
                        style="margin-top:14px; display:flex; justify-content:space-between; align-items:center; font-size:11px;">
                        <span>كل شيء في مكان واحد: خدمات، طلبات، أرباح، رسائل.</span>
                        @guest
                        <a href="{{ route('auth') }}?tab=register" class="btn btn-outline"
                            style="font-size:11px; padding:5px 10px;"> جرّب كـ مزود</a>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories -->
    <section id="categories">
        <div class="container">
            <div class="section-header">
                <div>
                    <h2 class="section-title">تصفح حسب التصنيف</h2>
                    <p class="section-subtitle">
                        اختر المجال الذي يناسب احتياجاتك وابدأ في استكشاف الخدمات المتاحة.
                    </p>
                </div>
            </div>

            <div class="categories-grid">
                @foreach($categories as $category)
                <div class="category-card"
                    onclick="window.location='{{ route('services.index', ['category' => $category->slug]) }}'">
                    <div class="category-icon">
                        @if($category->icon)
                        <i class="{{ $category->icon }}"></i>
                        @else
                        {{ mb_substr($category->name, 0, 1) }}
                        @endif
                    </div>


                    <div class="category-name">
                        {{ $category->name }}
                    </div>

                    <div class="category-desc">
                        @if($category->description)
                        {{ \Illuminate\Support\Str::limit($category->description, 80) }}
                        @else
                        {{ $category->services_count }} خدمات متاحة
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

        </div>
    </section>

    <!-- Services -->
    <section id="services">
        <div class="container">
            <div class="section-header">
                <div>
                    <h2 class="section-title">خدمات مميزة</h2>
                    <p class="section-subtitle">
                       تصفح الخدمات الاعلى مبيعا و اختر الخدمة اللتي تناسب احتايجاتك
                    </p>
                </div>
                <a href="{{ route('services.index') }}" class="section-link">عرض كل الخدمات</a>
            </div>

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
    </section>

    <!-- How it works -->
    <section id="how">
        <div class="container">
            <div class="section-header">
                <div>
                    <h2 class="section-title">كيف تعمل منصة كوفية؟</h2>
                    <p class="section-subtitle">
                        خطوات بسيطة تربط بين البائع والمشتري عبر نظام واضح وشفاف.
                    </p>
                </div>
            </div>

            <div class="how-section">
                <div class="how-grid">
                    <div class="how-step">
                        <div class="how-step-number">1</div>
                        <div class="how-step-title">إنشاء حساب</div>
                        <div class="how-step-text">
                            سجّل كمشتري أو مزود خدمة، وأكمل بياناتك الأساسية وملفك الشخصي.
                        </div>
                    </div>
                    <div class="how-step">
                        <div class="how-step-number">2</div>
                        <div class="how-step-title">تصفح أو أنشئ خدمة</div>
                        <div class="how-step-text">
                            المشترون يتصفحون الخدمات، والبائعون ينشئون خدماتهم في التصنيفات المناسبة.
                        </div>
                    </div>
                    <div class="how-step">
                        <div class="how-step-number">3</div>
                        <div class="how-step-title">مراسلة واتفاق</div>
                        <div class="how-step-text">
                            يتم التواصل داخل المنصة لتوضيح التفاصيل والاتفاق على السعر ومدة التنفيذ.
                        </div>
                    </div>
                    <div class="how-step">
                        <div class="how-step-number">4</div>
                        <div class="how-step-title">تنفيذ وتسليم آمن</div>
                        <div class="how-step-text">
                            يتم الدفع عبر نظام آمن، ويُفرج عن المبلغ بعد التسليم، مع إمكانية التقييم.
                        </div>
                    </div>
                </div>
            </div>

            <div class="cta">
                <div>
                    <div class="cta-title">ابدأ اليوم على منصة كوفية</div>
                    <div class="cta-text">
                        انضم كمزوّد خدمة لتوسيع دائرة عملائك، أو كمشتري للحصول على خدمات احترافية
                        بسهولة وأمان.
                    </div>
                </div>
                <div class="cta-actions">
                    @guest
                    <a href="{{ route('auth') }}?tab=register" class="btn btn-ghost-light">التسجيل كمزود خدمة</a>
                    <a href="{{ route('auth') }}?tab=register" class="btn btn-primary">التسجيل كمشتري</a>
                    @endguest
                </div>
            </div>
        </div>
    </section>

</main>
@endsection