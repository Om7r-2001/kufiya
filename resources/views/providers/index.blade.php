@extends('layouts.app')

@section('title', 'مزودو الخدمات')

@section('content')
<section class="page-section services-page providers-page">
    <div class="container">

        {{-- الهيدر --}}
        <div class="page-section-header">
            <div>
                <h1 class="section-title">مزودو الخدمات</h1>
                <p class="section-subtitle">
                    تصفّح أفضل مزودي الخدمات في منصة كوفية وابحث عن الشخص المناسب لتنفيذ مشروعك.
                </p>
            </div>

            <form method="GET" action="{{ route('providers.index') }}" class="services-filter-bar">
                <div class="services-search">
                    <input type="text" name="q" value="{{ request('q') }}"
                        placeholder="ابحث عن مزود خدمة بالاسم أو التخصص..." class="services-search-input">
                    <button type="submit" class="services-search-button">
                        بحث
                    </button>
                </div>
            </form>
        </div>

        {{-- قائمة المزوّدين --}}
        <div class="providers-grid">
            @forelse($providers as $seller)
            <article class="service-card provider-card">
                <div class="service-body">

                    {{-- معلومات أساسية عن المزوّد --}}
                    <div class="provider-header">
                        <div class="seller-avatar">
                            {{ mb_substr($seller->name, 0, 1) }}
                        </div>

                        <div>
                            <h2 class="service-title" style="margin-bottom: 2px;">
                                {{ $seller->name }}
                            </h2>

                            <div class="service-meta">
                                <span class="service-seller">
                                    مزود {{ $seller->services_count }} خدمة منشورة
                                    @if(!is_null($seller->avg_rating))
                                    • تقييم {{ number_format($seller->avg_rating, 1) }} من {{ $seller->reviews_count }}
                                    تقييم
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- نبذة مختصرة --}}
                    <p class="service-desc" style="margin-top: 8px;">
                        @if($seller->bio)
                        {{ \Illuminate\Support\Str::limit($seller->bio, 120) }}
                        @else
                        لم يقم مزود الخدمة بكتابة نبذة تعريفية بعد.
                        @endif
                    </p>

                    {{-- معلومات إضافية صغيرة تحت --}}
                    <div class="provider-meta-row">
                        <span class="provider-meta-label">البلد:</span>
                        <span class="provider-meta-value">{{ $seller->country ?? 'غير محدد' }}</span>
                    </div>

                    <div class="provider-meta-row">
                        <span class="provider-meta-label">تاريخ الانضمام:</span>
                        <span class="provider-meta-value">
                            {{ optional($seller->created_at)->format('Y-m-d') }}
                        </span>
                    </div>

                    {{-- زر عرض الملف --}}
                    <div class="service-card-footer">
                        <a href="{{ route('providers.show', $seller) }}" class="btn btn-outline">
                            عرض ملف مزود الخدمة
                        </a>
                    </div>
                </div>
            </article>
            @empty
            <div class="empty-state">
                لا يوجد مزودو خدمات مطابقون لبحثك حالياً.
            </div>
            @endforelse
        </div>

        {{-- ترقيم الصفحات --}}
        @if(method_exists($providers, 'links'))
        <div class="pagination-wrapper">
            {{ $providers->withQueryString()->links() }}
        </div>
        @endif
    </div>
</section>
@endsection