@extends('layouts.app')

@section('title', 'لوحة المشتري | منصة كوفية')

@section('content')
<main>
    <section class="dashboard-section">
        <div class="container">

            <div class="page-header">
                <div>
                    <h1 class="page-title">لوحة المشتري</h1>
                    <p class="page-subtitle">
                        من هنا يمكنك متابعة طلباتك والخدمات التي اشتريتها عبر منصة كوفية.
                    </p>
                </div>
            </div>

            {{-- كروت الإحصائيات --}}
            <div class="dashboard-stats-grid">
                <div class="stat-card">
                    <div class="stat-label">إجمالي الطلبات</div>
                    <div class="stat-value">{{ $totalOrders }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">طلبات قيد التنفيذ</div>
                    <div class="stat-value">{{ $inProgress }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">طلبات مكتملة</div>
                    <div class="stat-value">{{ $completedOrders }}</div>
                </div>
            </div>
            <div class="dashboard-grid-2">
                {{-- آخر الطلبات --}}
                <div class="dashboard-panel">
                    <div class="dashboard-panel-header">
                        <h2 class="dashboard-panel-title">أحدث طلباتك</h2>
                        <a href="{{ route('buyer.orders.index') }}" class="section-link">
                            عرض كل الطلبات
                        </a>
                    </div>

                    @if($latestOrders->count())
                    <div class="orders-table-wrapper">
                        <table class="orders-table">
                            <thead>
                                <tr>
                                    <th>الخدمة / المشروع</th>
                                    <th>البائع</th>
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
                                    <td>{{ $order->seller->name }}</td>
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
                        لم تقم بأي طلبات حتى الآن. يمكنك بدء الطلب من صفحة تفاصيل أي خدمة.
                    </p>
                    @endif
                </div>

                {{-- أحدث مشاريعي --}}
                <div class="dashboard-panel">
                    <div class="dashboard-panel-header">
                        <h2 class="dashboard-panel-title">أحدث مشاريعي</h2>

                        <a href="{{ route('projects.my') }}" class="section-link">
                            إدارة جميع المشاريع
                        </a>
                    </div>

                    @if($latestProjects->count())
                    <div class="services-list-compact">
                        @foreach($latestProjects as $project)
                        <div class="service-row">
                            <div>
                                <div class="service-row-title">
                                    {{ $project->title }}
                                </div>

                                <div class="service-row-meta">
                                    {{ $project->status === 'open' ? 'مفتوح' :
                               ($project->status === 'in_progress' ? 'قيد التنفيذ' :
                               ($project->status === 'completed' ? 'مكتمل' : 'ملغى')) }}

                                    • {{ $project->bids_count }} عروض
                                    • {{ $project->created_at->format('Y-m-d') }}
                                </div>
                            </div>

                            <div>
                                <a href="{{ route('projects.show', $project->slug) }}" class="section-link">
                                    عرض
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p style="font-size:14px; color:var(--color-muted);">
                        لم تقم بإنشاء أي مشروع بعد. ابدأ بإنشاء أول مشروع من زر "إنشاء مشروع جديد".
                    </p>
                    @endif
                </div>
            </div>
        </div>
    </section>
</main>
@endsection