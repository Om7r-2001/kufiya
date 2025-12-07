@extends('layouts.app')

@section('content')
@php
$service = $order->service ?? null;
$project = $order->project ?? null;
@endphp
<div class="container" style="max-width:700px; margin-top:30px; margin-bottom:40px;">
    <div class="card" style="border-radius:16px; padding:20px;">
        <h1 class="section-title" style="margin-bottom:10px;">إتمام عملية الدفع</h1>
        <p class="section-subtitle" style="margin-bottom:20px;">
            راجع تفاصيل الطلب ثم اختر طريقة الدفع الوهمية لإكمال العملية.
        </p>

        <div class="order-summary-box"
            style="background:#fafafa; border-radius:14px; padding:15px; margin-bottom:20px;">

            @if($project)
            <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                <span>المشروع:</span>
                <strong>{{ $project->title }}</strong>
            </div>
            @else
            <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                <span>الخدمة:</span>
                <strong>{{ $service->title ?? 'خدمة' }}</strong>
            </div>
            @endif

            <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                <span>سعر الطلب:</span>
                <span>{{ number_format($order->total_price, 2) }} $</span>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                <span>عمولة المنصة (20%):</span>
                <span>{{ number_format($order->platform_fee, 2) }} $</span>
            </div>
            <div style="display:flex; justify-content:space-between; font-weight:bold;">
                <span>صافي المبلغ لمزود الخدمة:</span>
                <span>{{ number_format($order->seller_earnings, 2) }} $</span>
            </div>
        </div>


        <form method="POST" action="{{ route('orders.payment.pay', $order) }}">
            @csrf

            <div class="form-group" style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:6px;">اختر طريقة الدفع الوهمية:</label>
                <div class="role-options">
                    <label class="role-option">
                        <input type="radio" name="payment_method" value="card" checked>
                        <span>بطاقة بنكية (وهمي)</span>
                    </label>
                    <label class="role-option">
                        <input type="radio" name="payment_method" value="wallet">
                        <span>محفظة إلكترونية (وهمي)</span>
                    </label>
                    <label class="role-option">
                        <input type="radio" name="payment_method" value="test">
                        <span>دفع تجريبي للمشروع</span>
                    </label>
                </div>
                @error('payment_method')
                <div style="color:#c0392b; font-size:12px; margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%; margin-top:10px;">
                إتمام الدفع والانتقال إلى صفحة الطلب
            </button>
        </form>
    </div>
</div>
@endsection