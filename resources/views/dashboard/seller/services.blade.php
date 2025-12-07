@extends('layouts.app')

@section('title', 'خدماتي | منصة كوفية')

@section('content')
<main>
    <section class="dashboard-section">
        <div class="container">

            <div class="page-header">
                <div>
                    <h1 class="page-title">خدماتي</h1>
                    <p class="page-subtitle">
                        من هنا يمكنك إدارة جميع الخدمات التي أضفتها في منصة كوفية.
                    </p>
                </div>
                <div>
                    <a href="{{ route('services.create') }}" class="btn btn-primary">
                        + إضافة خدمة جديدة
                    </a>
                </div>
            </div>

            @if(session('success'))
            <div style="background:#e6ffef; border-radius:10px; padding:8px 12px; font-size:13px; margin-bottom:10px;">
                {{ session('success') }}
            </div>
            @endif

            <div class="dashboard-panel">
                @if($services->count())
                <div class="orders-table-wrapper">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>عنوان الخدمة</th>
                                <th>التصنيف</th>
                                <th>السعر</th>
                                <th>الحالة</th>
                                <th>التقييم</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($services as $service)
                            <tr>
                                <td>
                                    <a href="{{ route('services.show', $service->slug) }}">
                                        {{ $service->title }}
                                    </a>
                                </td>
                                <td>{{ $service->category?->name ?? '-' }}</td>
                                <td>{{ number_format($service->price, 2) }}$</td>
                                <td>
                                    @switch($service->status)
                                    @case('active') نشطة @break
                                    @case('paused') متوقفة مؤقتاً @break
                                    @case('draft') مسودة @break
                                    @default {{ $service->status }}
                                    @endswitch
                                </td>
                                <td>
                                    @if($service->rating_count)
                                    {{ $service->rating_avg }} ({{ $service->rating_count }} تقييم)
                                    @else
                                    لا يوجد بعد
                                    @endif
                                </td>
                                <td>
                                    <div style="display:flex; gap:6px;">
                                        <a href="{{ route('services.edit', $service->slug) }}" class="section-link">
                                            تعديل
                                        </a>
                                        <form method="POST" action="{{ route('services.destroy', $service->slug) }}"
                                            onsubmit="return confirm('هل أنت متأكد من حذف هذه الخدمة؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="section-link" style="color:#c00;">
                                                حذف
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div style="margin-top:12px;">
                        {{ $services->links() }}
                    </div>
                </div>
                @else
                <p style="font-size:14px; color:var(--color-muted);">
                    لم تقم بإضافة أي خدمة بعد. ابدأ بإضافة أول خدمة من الزر أعلاه.
                </p>
                @endif
            </div>

        </div>
    </section>
</main>
@endsection