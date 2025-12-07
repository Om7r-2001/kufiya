@extends('layouts.app')

@section('title', 'طلبات العملاء | منصة كوفية')

@section('content')
<main>
    <section class="orders-section">
        <div class="container">

            <div class="page-header">
                <div>
                    <h1 class="page-title">طلبات العملاء</h1>
                    <p class="page-subtitle">
                        الطلبات التي وصلتك على خدماتك من المشترين في منصة كوفية.
                    </p>
                </div>
            </div>

            <div class="orders-table-wrapper">
                @if($orders->count())
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>الخدمة</th>
                            <th>المشتري</th>
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

                <div style="margin-top:15px;">
                    {{ $orders->links() }}
                </div>
                @else
                <p style="font-size:14px; color:var(--color-muted);">
                    لم تستلم أي طلبات حتى الآن. يمكنك تحسين ظهور خدماتك لإجتذاب المزيد من العملاء.
                </p>
                @endif
            </div>

        </div>
    </section>
</main>
@endsection