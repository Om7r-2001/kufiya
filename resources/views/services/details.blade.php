@extends('layouts.app')

@section('title', $service->title . ' | منصة كوفية')

@section('content')
<main>
    <section class="service-details-section">
        <div class="container">

            {{-- العنوان والمسار --}}
            <div class="page-header service-details-header">
                <div>
                    <h1 class="page-title">{{ $service->title }}</h1>

                    <p class="page-subtitle">
                        {{ $service->short_description ?? 'تفاصيل خدمة مقدمة عبر منصة كوفية.' }}
                    </p>

                    <div class="service-meta-top">
                        @if($service->category)
                        <span class="badge badge-accent">
                            {{ $service->category->name }}
                        </span>
                        @endif

                        <span class="badge">
                            {{ $service->level === 'basic' ? 'باقات أساسية' :
                               ($service->level === 'standard' ? 'باقات متوسطة' : 'باقات مميزة') }}
                        </span>

                        <span class="service-top-rating">
                            <span class="stars">★★★★★</span>
                            <span>
                                {{ $service->rating_avg }} ({{ $service->rating_count }} تقييم)
                            </span>
                        </span>
                    </div>
                </div>

                <div class="breadcrumb">
                    <a href="{{ route('home') }}">الرئيسية</a>
                    <span>/</span>
                    <a href="{{ route('services.index') }}">الخدمات</a>
                    <span>/</span>
                    <span>{{ \Illuminate\Support\Str::limit($service->title, 40) }}</span>
                </div>
            </div>

            {{-- شبكة محتوى التفاصيل --}}
            <div class="service-details-grid">

                {{-- العمود الأيسر: تفاصيل الخدمة --}}
                <div class="service-main">

                    {{-- المعرض --}}
                    <div class="service-gallery">
                        <div class="service-main-image">
                            @if($service->images->count())
                            @php
                            $mainImage = $service->images->firstWhere('is_main', true)
                            ?? $service->images->first();
                            @endphp
                            <img src="{{ asset('storage/'.$mainImage->path) }}"
                                alt="صورة رئيسية للخدمة {{ $service->title }}" style="width:100%; height:auto;">
                            @else
                            <div class="service-main-image-placeholder">
                                لا توجد صورة مرفقة لهذه الخدمة حالياً، يمكن إضافة صور من لوحة البائع.
                            </div>
                            @endif
                        </div>

                        @if($service->images->count() > 1)
                        <div class="service-thumbs">
                            @foreach($service->images as $image)
                            <div class="service-thumb-item">
                                <img src="{{ asset('storage/'.$image->path) }}" alt="معاينة للخدمة"
                                    style="width:100%; border-radius:10px;">
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    {{-- وصف الخدمة --}}
                    <section class="service-block">
                        <h2 class="service-block-title">وصف الخدمة</h2>

                        <p class="service-block-text">
                            {!! nl2br(e($service->description)) !!}
                        </p>

                        <ul class="service-list">
                            <li>يتم تنفيذ الخدمة خلال {{ $service->delivery_time }} يوم من تاريخ بدء الطلب.</li>
                            <li>الدفع يتم عبر منصة كوفية مع حفظ حقوق الطرفين.</li>
                            <li>إمكانية المراسلة داخل الطلب لتوضيح التفاصيل.</li>
                        </ul>
                    </section>

                    {{-- ما الذي ستحصل عليه --}}
                    <section class="service-block">
                        <h2 class="service-block-title">ما الذي ستحصل عليه؟</h2>

                        <div class="service-features-grid">
                            <div class="service-feature-item">
                                <h3>تنفيذ احترافي</h3>
                                <p>تنفيذ الخدمة وفق التفاصيل المتفق عليها مع مراعاة الجودة والوقت.</p>
                            </div>
                            <div class="service-feature-item">
                                <h3>متابعة ومراسلة</h3>
                                <p>إمكانية متابعة حالة الطلب والتواصل مع مزود الخدمة في أي وقت.</p>
                            </div>
                            <div class="service-feature-item">
                                <h3>ضمان عبر المنصة</h3>
                                <p>المبلغ يبقى معلقاً في المنصة حتى تأكيد استلامك النهائي للخدمة.</p>
                            </div>
                        </div>
                    </section>

                    {{-- التقييمات --}}
                    <section class="service-block">
                        <h2 class="service-block-title">آراء العملاء (التقييمات)</h2>

                        <div class="reviews-summary">
                            <div class="reviews-score">
                                <div class="reviews-score-main">{{ $service->rating_avg }}</div>
                                <div class="stars">★★★★★</div>
                                <div class="reviews-count">
                                    استناداً إلى {{ $service->rating_count }} تقييم
                                </div>
                            </div>
                            <div class="reviews-meta">
                                <p>
                                    تعكس هذه التقييمات تجارب حقيقية لعملاء تعاملوا مع مزود الخدمة
                                    من خلال منصة كوفية.
                                </p>
                            </div>
                        </div>

                        <div class="reviews-list">
                            @forelse($service->reviews as $review)
                            <article class="review-item">
                                <div class="review-header">
                                    <div>
                                        <div class="review-name">
                                            {{ $review->buyer->name ?? 'عميل من المنصة' }}
                                        </div>
                                    </div>
                                    <div class="review-rating">
                                        <span class="stars">★★★★★</span>
                                        <span>
                                            {{ $review->created_at?->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>

                                <p class="review-text">
                                    {{ $review->comment ?: 'لم يترك تعليقاً نصياً.' }}
                                </p>
                            </article>
                            @empty
                            <p style="font-size:13px; color:var(--color-muted);">
                                لا توجد تقييمات لهذه الخدمة بعد. كن أول من يقيّم هذه الخدمة بعد تجربتها.
                            </p>
                            @endforelse
                        </div>
                    </section>

                </div>

                {{-- العمود الأيمن: السعر + البائع --}}
                <aside class="service-sidebar">

                    {{-- بطاقة التسعير --}}
                    <div class="service-pricing-card">
                        <div class="service-price-top">
                            <div>
                                <span class="service-price-label">سعر الخدمة</span>
                                <div class="service-price-main">
                                    ابتداءً من {{ number_format($service->price, 2) }}$
                                </div>
                                <div class="service-price-note">
                                    يشمل باقة
                                    {{ $service->level === 'basic' ? 'أساسية' : ($service->level === 'standard' ? 'متوسطة' : 'مميزة') }}
                                </div>
                            </div>
                        </div>

                        <div class="service-price-meta">
                            <div>
                                <div class="meta-label">مدة التسليم المتوقعة</div>
                                <div class="meta-value">
                                    {{ $service->delivery_time }} يوم
                                </div>
                            </div>
                            <div>
                                <div class="meta-label">تقييم عام للبائع</div>
                                <div class="meta-value">
                                    {{ $service->seller->receivedReviews()->avg('rating') ? number_format($service->seller->receivedReviews()->avg('rating'), 1) : 'غير متوفر بعد' }}
                                </div>
                            </div>
                        </div>

                        {{-- بطاقة التسعير --}}
                        <div class="service-pricing-card">
                            <div class="service-price-top">
                                <div>
                                    <span class="service-price-label">سعر الخدمة</span>
                                    <div class="service-price-main">
                                        ابتداءً من {{ number_format($service->price, 2) }}$
                                    </div>
                                    <div class="service-price-note">
                                        يشمل باقة
                                        {{ $service->level === 'basic' ? 'أساسية' : ($service->level === 'standard' ? 'متوسطة' : 'مميزة') }}
                                    </div>
                                </div>
                            </div>

                            <div class="service-price-meta">
                                <div>
                                    <div class="meta-label">مدة التسليم المتوقعة</div>
                                    <div class="meta-value">
                                        {{ $service->delivery_time }} يوم
                                    </div>
                                </div>
                                <div>
                                    <div class="meta-label">تقييم عام للبائع</div>
                                    <div class="meta-value">
                                        {{ $service->seller->receivedReviews()->avg('rating') ? number_format($service->seller->receivedReviews()->avg('rating'), 1) : 'غير متوفر بعد' }}
                                    </div>
                                </div>
                            </div>

                            @auth
                            @if(auth()->user()->role === 'buyer')
                            <form method="POST" action="{{ route('orders.store', $service) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary service-order-btn">
                                    طلب الخدمة الآن
                                </button>
                            </form>
                            @elseif(auth()->user()->id === $service->seller->id)
                            <p class="service-safe-text">
                                أنت صاحب هذه الخدمة، لا يمكنك طلبها كمشتري.
                            </p>
                            @else
                            <p class="service-safe-text">
                                حسابك الحالي ليس من نوع "مشتري"، لا يمكنك طلب الخدمة بهذا الحساب.
                            </p>
                            @endif
                            @else
                            <a href="{{ route('auth') }}" class="btn btn-primary service-order-btn">
                                سجّل الدخول لطلب الخدمة
                            </a>
                            @endauth

                            <p class="service-safe-text">
                                يتم حفظ المبلغ في محفظة المنصة ولا يُحوَّل لمزود الخدمة إلا بعد تأكيدك استلام العمل.
                            </p>
                        </div>


                        <p class="service-safe-text">
                            يتم حفظ المبلغ في محفظة المنصة ولا يُحوَّل لمزود الخدمة إلا بعد تأكيدك استلام العمل.
                        </p>
                    </div>

                    {{-- بطاقة البائع --}}
                    <div class="seller-card">
                        <div class="seller-header">
                            <div class="seller-avatar">
                                {{ mb_substr($service->seller->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="seller-name">
                                    {{ $service->seller->name }}
                                </div>
                                <div class="seller-meta">
                                    <span>دور: {{ $service->seller->role === 'seller' ? 'مزود خدمة' : 'مستخدم' }}</span>
                                    •
                                    <span>
                                        {{ $service->seller->ordersAsSeller()->count() }} خدمة منجزة
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="seller-info-row">
                            <span class="seller-info-label">البلد:</span>
                            <span class="seller-info-value">
                                {{ $service->seller->country ?? 'غير محدد' }}
                            </span>
                        </div>

                        <div class="seller-info-row">
                            <span class="seller-info-label">عضو منذ:</span>
                            <span class="seller-info-value">
                                {{ optional($service->seller->created_at)->format('Y') }}
                            </span>
                        </div>

                        <div class="seller-info-row">
                            <span class="seller-info-label">متوسط سرعة الرد:</span>
                            <span class="seller-info-value">
                                خلال 24 ساعة (قابلة للتخصيص لاحقاً)
                            </span>
                        </div>

                        {{-- زر مراسلة مزود الخدمة --}}
                        <div style="margin-top: 16px;">
                            @auth
                            {{-- يمنع أن يراسل نفسه كبائع --}}
                            @if(auth()->user()->role === 'buyer' && auth()->id() !== $service->seller_id)

                            @if(!empty($existingOrder))
                            {{-- يوجد طلب سابق لهذه الخدمة لهذا المشتري → نفتح الشات مباشرة --}}
                            <a href="{{ route('orders.chat', $existingOrder->id) }}" class="btn btn-secondary">
                                مراسلة مزود الخدمة
                            </a>
                            @else
                            {{-- لا يوجد طلب بعد --}}
                            <button type="button" class="btn btn-secondary"
                                onclick="alert('يجب شراء الخدمة أولاً لفتح محادثة مع مزود الخدمة.');">
                                مراسلة مزود الخدمة
                            </button>
                            @endif

                            @endif
                            @else
                            {{-- المستخدم غير مسجل --}}
                            <a href="{{ route('login') }}" class="btn btn-secondary">
                                سجّل الدخول لمراسلة مزود الخدمة
                            </a>
                            @endauth
                        </div>


                    </div>

                    {{-- أسئلة متكررة ثابتة حالياً --}}
                    <div class="service-faq-card">
                        <h3 class="service-block-title small">أسئلة متكررة</h3>

                        <div class="faq-item">
                            <div class="faq-q">هل يمكن طلب تعديلات بعد التسليم؟</div>
                            <div class="faq-a">
                                يتم الاتفاق على عدد التعديلات قبل بدء التنفيذ، ويمكن طلب تعديلات إضافية برسوم متفق
                                عليها.
                            </div>
                        </div>

                        <div class="faq-item">
                            <div class="faq-q">هل يمكن إلغاء الطلب؟</div>
                            <div class="faq-a">
                                نعم، يمكن طلب الإلغاء قبل تسليم العمل النهائي، وفق سياسة المنصة في الضمان وحفظ الحقوق.
                            </div>
                        </div>
                    </div>

                </aside>

            </div>
        </div>
    </section>
</main>
@endsection