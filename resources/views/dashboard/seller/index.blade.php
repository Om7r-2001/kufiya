@extends('layouts.app')

@section('title', 'لوحة البائع | منصة كوفية')

@section('content')
<main>
    <section class="dashboard-section">
        <div class="container">

            <div class="page-header">
                <div>
                    <h1 class="page-title">لوحة البائع</h1>
                    <p class="page-subtitle">
                        من هنا يمكنك إدارة خدماتك، متابعة طلبات العملاء، ومعرفة أرباحك التقديرية.
                    </p>
                </div>
                <div>
                    <a href="{{ route('services.create') }}" class="btn btn-primary">
                        + إضافة خدمة جديدة
                    </a>
                </div>
            </div>

            {{-- كروت الإحصائيات --}}
            <div class="dashboard-stats-grid">
                <div class="stat-card">
                    <div class="stat-label">عدد خدماتك</div>
                    <div class="stat-value">{{ $totalServices }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">إجمالي الطلبات</div>
                    <div class="stat-value">{{ $totalOrders }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">طلبات قيد التنفيذ</div>
                    <div class="stat-value">{{ $inProgress }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">أرباح مكتملة (تقريبية)</div>
                    <div class="stat-value">{{ number_format($estimatedEarnings, 2) }}$</div>
                </div>
            </div>

            <div class="dashboard-grid-2">
                {{-- آخر الطلبات --}}
                <div class="dashboard-panel">
                    <div class="dashboard-panel-header">
                        <h2 class="dashboard-panel-title">أحدث طلبات العملاء</h2>
                        <a href="{{ route('seller.orders.index') }}" class="section-link">
                            عرض كل الطلبات
                        </a>
                    </div>

                    @if($latestOrders->count())
                    <div class="orders-table-wrapper">
                        <table class="orders-table">
                            <thead>
                                <tr>
                                    <th>الخدمة</th>
                                    <th>المشتري</th>
                                    <th>السعر</th>
                                    <th>الحالة</th>
                                    <th>تاريخ الطلب</th>
                                    <th>محادثة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($latestOrders as $order)
                                <tr>
                                    <td>
                                        @if($order->service)
                                        {{-- طلب من خدمة --}}
                                        <a href="{{ route('services.show', $order->service->slug) }}">
                                            {{ $order->service->title }}
                                        </a>
                                        @elseif($order->project)
                                        {{-- طلب من مشروع --}}
                                        <a href="{{ route('projects.show', $order->project) }}">
                                            {{ $order->project->title }}
                                        </a>
                                        @else
                                        {{-- حالة نادرة لو لا خدمة ولا مشروع --}}
                                        <span>طلب بدون مرجع</span>
                                        @endif
                                    </td>
                                    <td>{{ $order->buyer->name }}</td>
                                    <td>{{ number_format($order->price, 2) }}$</td>
                                    <td>{{ $order->status }}</td>
                                    <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <a href="{{ route('orders.chat', $order) }}" class="section-link">
                                            فتح المحادثة
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p style="font-size:14px; color:var(--color-muted);">
                        لم تستلم أي طلبات بعد. شارك رابط خدماتك مع العملاء لزيادة فرص الشراء.
                    </p>
                    @endif
                </div>

                {{-- آخر الخدمات --}}
                <div class="dashboard-panel">
                    <div class="dashboard-panel-header">
                        <h2 class="dashboard-panel-title">أحدث خدماتك</h2>
                        <a href="{{ route('seller.services.index') }}" class="section-link">
                            إدارة جميع الخدمات
                        </a>

                        <a href="{{ route('services.index') }}" class="section-link">
                            عرض في صفحة الخدمات
                        </a>
                    </div>

                    @if($latestServices->count())
                    <div class="services-list-compact">
                        @foreach($latestServices as $service)
                        <div class="service-row">
                            <div>
                                <div class="service-row-title">
                                    {{ $service->title }}
                                </div>
                                <div class="service-row-meta">
                                    {{ $service->status === 'active' ? 'نشطة' : ($service->status === 'paused' ? 'متوقفة' : 'مسودة') }}
                                    • {{ $service->price }}$
                                    • {{ $service->delivery_time }} أيام
                                </div>
                            </div>
                            <div>
                                <a href="{{ route('services.show', $service->slug) }}" class="section-link">
                                    عرض
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p style="font-size:14px; color:var(--color-muted);">
                        لم تقم بإضافة أي خدمة بعد. ابدأ بإضافة أول خدمة من زر "إضافة خدمة جديدة".
                    </p>
                    @endif
                </div>
            </div>

        </div>
    </section>
</main>

<style>

</style>
@endsection