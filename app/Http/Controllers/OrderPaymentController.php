<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Notification;
use App\Models\ProjectBid;
use Illuminate\Http\Request;

class OrderPaymentController extends Controller
{
    public function show(Order $order)
    {
        // تأكد أن هذا الطلب يخص المستخدم الحالي
        if ($order->buyer_id !== auth()->id()) {
            abort(403);
        }

        // لو تم الدفع سابقاً، رجّعه إلى صفحة الطلب
        if ($order->payment_status !== 'pending') {
            return redirect()->route('orders.chat', $order);
        }

        return view('orders.payment', compact('order'));
    }

    public function pay(Request $request, Order $order)
    {
        // تأكد أن هذا الطلب يخص المستخدم الحالي
        if ($order->buyer_id !== auth()->id()) {
            abort(403);
        }

        // لو تم الدفع سابقاً، رجّعه إلى صفحة الطلب
        if ($order->payment_status !== 'pending') {
            return redirect()->route('orders.chat', $order);
        }

        // التحقق من طريقة الدفع الوهمية
        $validated = $request->validate([
            'payment_method' => 'required|in:card,wallet,test',
        ]);

        // تحديث بيانات الدفع في الطلب
        $order->update([
            'payment_method' => $validated['payment_method'],
            'payment_status' => 'paid',
            'paid_at'        => now(),
            'status'         => 'in_progress', // البائع يمكنه البدء الآن
        ]);

        // إذا كان الطلب مرتبطاً بمشروع، حدّث حالة المشروع والعروض
        if ($order->project_id) {
            $project = $order->project; // مع افتراض وجود علاقة project() في موديل Order

            if ($project) {
                // تحديث حالة المشروع إلى قيد التنفيذ
                $project->status = 'in_progress';
                $project->save();

                // لو يوجد selected_bid_id، نحدّث حالة العروض
                if ($project->selected_bid_id) {
                    ProjectBid::where('id', $project->selected_bid_id)
                        ->update(['status' => 'accepted']);

                    ProjectBid::where('project_id', $project->id)
                        ->where('id', '!=', $project->selected_bid_id)
                        ->update(['status' => 'rejected']);
                }
            }
        }

        // إشعار البائع بأن الطلب أصبح مدفوعاً وجاهزاً للعمل
        Notification::create([
            'user_id' => $order->seller_id,
            'type'    => 'order',
            'title'   => 'طلب جديد مدفوع #' . $order->id,
            'body'    => 'تم إتمام الدفع للطلب #' . $order->id . ' وهو جاهز للتنفيذ.',
            'link'    => route('orders.chat', $order),
        ]);

        // إشعار المشتري بتأكيد الدفع
        Notification::create([
            'user_id' => $order->buyer_id,
            'type'    => 'order',
            'title'   => 'تم إتمام الدفع للطلب #' . $order->id,
            'body'    => 'تم إتمام عملية الدفع، يمكنك الآن متابعة الطلب من صفحة المحادثة.',
            'link'    => route('orders.chat', $order),
        ]);

        return redirect()
            ->route('orders.chat', $order)
            ->with('success', 'تم إتمام عملية الدفع، وتم إرسال الطلب إلى مزود الخدمة.');
    }

}

