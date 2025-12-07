@extends('layouts.app')

@section('title', 'طلباتي | منصة كوفية')

@section('content')
<main>
    <section class="orders-section">
        <div class="container">

            <div class="page-header">
                <div>
                    <h1 class="page-title">طلباتي</h1>
                    <p class="page-subtitle">
                        جميع الطلبات التي قمت بها على الخدمات المختلفة في منصة كوفية.
                    </p>
                </div>
            </div>

            @if(session('success'))
            <div style="background:#e6ffef; border-radius:10px; padding:10px 12px; font-size:13px; margin-bottom:12px;">
                {{ session('success') }}
            </div>
            @endif

            <div class="orders-table-wrapper">
                @if($orders->count())
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>الخدمة</th>
                            <th>البائع</th>
                            <th>السعر</th>
                            <th>الحالة</th>
                            <th>تاريخ الإنشاء</th>
                            <th>محادثة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
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

                <div style="margin-top:15px;">
                    {{ $orders->links() }}
                </div>
                @else
                <p style="font-size:14px; color:var(--color-muted);">
                    لم تقم بأي طلبات حتى الآن. يمكنك بدء الطلب من صفحة تفاصيل الخدمة.
                </p>
                @endif
            </div>

        </div>
    </section>
</main>
@endsection