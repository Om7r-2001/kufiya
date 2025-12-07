@php
    // نحدد النص ولون الشارة حسب حالة الطلب
    $labelClass = 'status-badge';
    $labelText  = $status;

    switch ($status) {
        case 'pending_payment':
            $labelClass .= ' waiting';
            $labelText  = 'بانتظار الدفع';
            break;

        case 'pending':
            $labelClass .= ' waiting';
            $labelText  = 'بانتظار قبول البائع';
            break;

        case 'in_progress':
            $labelClass .= ' in-progress';
            $labelText  = 'قيد التنفيذ';
            break;

        case 'delivered':
            $labelClass .= ' active';
            $labelText  = 'تم التسليم – بانتظار تأكيد المشتري';
            break;

        case 'completed':
            $labelClass .= ' active';
            $labelText  = 'مكتمل';
            break;

        case 'cancelled':
            $labelClass .= ' paused';
            $labelText  = 'ملغى';
            break;

        default:
            $labelClass .= ' paused';
            $labelText  = $status;
            break;
    }
@endphp

<span class="{{ $labelClass }}">
    {{ $labelText }}
</span>
