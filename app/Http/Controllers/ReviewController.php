<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use App\Models\Service;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * حفظ تقييم جديد مرتبِط بطلب مكتمل
     */
    public function store(Request $request, Order $order)
    {
        // فقط المشتري يمكنه تقييم الطلب
        if (auth()->id() !== $order->buyer_id) {
            abort(403, 'فقط المشتري يمكنه إضافة تقييم لهذا الطلب.');
        }

        // يجب أن يكون الطلب مكتمل
        if ($order->status !== 'completed') {
            return redirect()
                ->route('orders.chat', $order)
                ->with('error', 'لا يمكن إضافة تقييم إلا بعد إكمال الطلب.');
        }

        // لا نسمح بأكثر من تقييم لنفس الطلب
        if ($order->review) {
            return redirect()
                ->route('orders.chat', $order)
                ->with('error', 'لقد قمت بتقييم هذا الطلب مسبقاً.');
        }

        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
        ]);

        // إنشاء التقييم
        $review = Review::create([
            'service_id' => $order->service_id,
            'project_id' => $order->project_id,
            'order_id'   => $order->id,
            'buyer_id'   => $order->buyer_id,
            'seller_id'  => $order->seller_id,
            'rating'     => $request->rating,
            'comment'    => $request->comment,
        ]);

        // تحديث متوسط التقييم وعدد التقييمات في جدول الخدمات
        $service = Service::findOrFail($order->service_id);

        $service->rating_avg   = round($service->reviews()->avg('rating'), 2);
        $service->rating_count = $service->reviews()->count();
        $service->save();

        return redirect()
            ->route('orders.chat', $order)
            ->with('success', 'تم إضافة التقييم بنجاح.');
    }
}
