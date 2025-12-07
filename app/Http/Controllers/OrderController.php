<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Service;
use App\Models\Message;
use App\Models\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // دالة مساعدة اختيارية داخل OrderController (ليست static)
    protected function getOtherPartyId(Order $order): int
    {
        return auth()->id() == $order->buyer_id
            ? $order->seller_id
            : $order->buyer_id;
    }

    /**
     * إنشاء طلب جديد على خدمة معيّنة
     * Route: POST /services/{service:slug}/order
     */
    public function store(Request $request, Service $service)
    {
        // يجب أن يكون المستخدم مشترياً
        if (auth()->user()->role !== 'buyer') {
            abort(403, 'فقط المشترون يمكنهم طلب الخدمات.');
        }

        // السعر (يمكن تعدّله لاحقًا إذا عندك إضافات)
        $total = $service->price;

        $platformFee   = round($total * 0.20, 2);     // 20%
        $sellerEarning = $total - $platformFee;

        // إنشاء الطلب بحالة "بانتظار الدفع"
        $order = Order::create([
            'service_id'    => $service->id,
            'buyer_id'      => auth()->id(),
            'seller_id'     => $service->user_id,
            'price'         => $service->price,
            'status'        => 'pending_payment', // هنا التغيير المهم
            'notes'         => null,
            'delivery_date' => now()->addDays($service->delivery_time),
            'completed_at'  => null,
            'total_price'     => $total,
            'platform_fee'    => $platformFee,
            'seller_earnings' => $sellerEarning,
        ]);

        // توجيه المشتري مباشرة إلى صفحة الدفع الوهمية
        return redirect()
            ->route('orders.payment.show', $order)
            ->with('success', 'تم إنشاء الطلب بنجاح. الرجاء إتمام عملية الدفع لإرسال الطلب إلى مزود الخدمة.');
    }

    /**
     * صفحة "طلباتي" للمشتري
     * Route: GET /dashboard/buyer/orders
     */
    public function buyerIndex()
    {
        $orders = Order::with(['service', 'seller'])
            ->where('buyer_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('dashboard.buyer.orders', compact('orders'));
    }

    /**
     * صفحة "طلبات العملاء" للبائع
     * Route: GET /dashboard/seller/orders
     */
    public function sellerIndex()
    {
        $orders = Order::with(['service', 'buyer'])
            ->where('seller_id', auth()->id())
            ->where('status', '!=', 'pending_payment') // هنا التصفية
            ->latest()
            ->paginate(10);

        return view('dashboard.seller.orders', compact('orders'));
    }


    /**
     * عرض صفحة الدفع الوهمية
    */
    public function showPaymentForm(Order $order)
    {
        // التأكد أن المستخدم هو المشتري صاحب الطلب
        if (auth()->id() !== $order->buyer_id) {
            abort(403);
        }

        // لا نسمح بالدفع إلا إذا كان الطلب في حالة pending_payment
        if ($order->status !== 'pending_payment') {
            return redirect()
                ->route('orders.chat', $order)
                ->with('error', 'لا يمكن إتمام الدفع لهذا الطلب في حالته الحالية.');
        }

        return view('orders.payment', compact('order'));
    }

    /**
     * تنفيذ الدفع الوهمي
     */
    public function processPayment(Request $request, Order $order)
    {
        if (auth()->id() !== $order->buyer_id) {
            abort(403);
        }

        if ($order->status !== 'pending_payment') {
            return redirect()
                ->route('orders.chat', $order)
                ->with('error', 'لا يمكن إتمام الدفع لهذا الطلب في حالته الحالية.');
        }

        // التحقق من اختيار طريقة الدفع
        $request->validate([
            'payment_method' => 'required|in:card,wallet,cash',
        ]);

        // هنا الدفع وهمي فقط: نعتبره ناجحاً
        // نغيّر حالة الطلب من pending_payment إلى pending (بانتظار قبول البائع)
        $order->update([
            'status' => 'pending',
            // لو عندك عمود payment_method يمكن حفظه هنا
            // 'payment_method' => $request->payment_method,
        ]);

        // الآن فقط نرسل إشعاراً للبائع بوجود طلب جديد مدفوع
        Notification::create([
            'user_id' => $order->seller_id,
            'type'    => 'order',
            'title'   => 'طلب جديد على خدمتك #' . $order->id,
            'body'    => 'قام ' . auth()->user()->name . ' بطلب خدمتك وأتم عملية الدفع.',
            'link'    => route('orders.chat', $order),
        ]);

        return redirect()
            ->route('orders.chat', $order)
            ->with('success', 'تم إتمام الدفع بنجاح، تم إرسال الطلب إلى مزود الخدمة وبانتظار قبوله.');
    }


    /**
     * دالة مساعدة: هل المستخدم الحالي طرف في الطلب (مشتري أو بائع)؟
     */
    protected function userBelongsToOrder(Order $order): bool
    {
        $userId = auth()->id();
        return $order->buyer_id === $userId || $order->seller_id === $userId;
    }

    /**
     * بدء العمل من طرف البائع: pending → in_progress
     * Route: POST /orders/{order}/start
     */
    public function start(Order $order)
    {
        $user = auth()->user();

        // 1) فقط البائع نفسه
        if ($user->id !== $order->seller_id) {
            abort(403, 'فقط مزود الخدمة يمكنه بدء العمل على هذا الطلب.');
        }

        // 2) يجب أن يكون الطلب في حالة pending
        if ($order->status !== 'pending') {
            return redirect()
                ->route('orders.chat', $order)
                ->with('error', 'لا يمكن بدء العمل إلا إذا كان الطلب في حالة انتظار (pending).');
        }

        // 3) تغيير حالة الطلب إلى قيد التنفيذ
        $order->update([
            'status'     => 'in_progress',
        ]);

        // 4) رسالة في محادثة الطلب
        Message::create([
            'order_id'    => $order->id,
            'sender_id'   => auth()->id(),        // البائع
            'receiver_id' => $order->buyer_id,   // المشتري
            'body'        => 'قام مزود الخدمة "' . $user->name . '" بقبول الطلب #' . $order->id . ' وبدأ العمل عليه.',
        ]);

        // 5) إشعار للمشتري بقبول الطلب وبدء العمل
        Notification::create([
            'user_id' => $order->buyer_id,
            'type'    => 'order',
            'title'   => 'تم قبول الطلب #' . $order->id,
            'body'    => 'قام مزود الخدمة "' . $user->name . '" بقبول طلبك وبدأ العمل عليه.',
            'link'    => route('orders.chat', $order),
        ]);

        return redirect()
            ->route('orders.chat', $order)
            ->with('success', 'تم قبول الطلب وبدء العمل عليه.');
    }

    /**
     * تسليم الطلب من طرف البائع: in_progress → delivered
     * Route: POST /orders/{order}/deliver
     */
    public function deliver(Request $request, Order $order)
    {
        $user = auth()->user();

        // تأكد أن المستخدم هو البائع صاحب الطلب
        if ($user->id !== $order->seller_id) {
            abort(403, 'غير مصرح لك بتسليم هذا الطلب.');
        }

        // تأكد أن حالة الطلب تسمح بالتسليم
        if (! in_array($order->status, ['in_progress', 'ongoing', 'accepted'])) {
            return back()->withErrors([
                'delivery' => 'لا يمكن تسليم طلب في هذه الحالة.',
            ]);
        }

        // التحقق من ملف التسليم
        $request->validate([
            'delivery_note' => 'nullable|string|max:5000',
            'delivery_file' => 'required|file|max:20480', // 20MB
            // يمكنك تخصيص الأنواع:
            // 'delivery_file' => 'required|file|mimes:zip,rar,7z,pdf,doc,docx,txt,mp4,mp3,jpg,jpeg,png|max:20480',
        ], [
            'delivery_file.required' => 'يجب إرفاق ملف المشروع عند التسليم.',
            'delivery_file.max'      => 'حجم الملف يجب ألا يتجاوز 20 ميجابايت.',
        ]);

        // حفظ الملف في storage
        $path = $request->file('delivery_file')->store('order_deliveries', 'public');

        $order->delivery_file = $path;
        $order->delivery_note = $request->delivery_note;
        $order->delivered_at  = now();
        $order->status        = 'delivered';

        $order->save();

        // 1) رسالة في محادثة الطلب
        $body = 'قام مزود الخدمة "' . $user->name . '" بتسليم المشروع للطلب #' . $order->id . '.';

        if ($order->delivery_note) {
            $body .= ' ملاحظة التسليم: ' . $order->delivery_note;
        }

        Message::create([
            'order_id'    => $order->id,
            'sender_id'   => $user->id,          // البائع
            'receiver_id' => $order->buyer_id,   // المشتري
            'body'        => $body,
        ]);

        // 2) إشعار للمشتري بأن المشروع تم تسليمه
        Notification::create([
            'user_id' => $order->buyer_id,
            'type'    => 'order',
            'title'   => 'تم تسليم الطلب #' . $order->id,
            'body'    => 'قام مزود الخدمة "' . $user->name . '" بتسليم المشروع. يمكنك مراجعة الملف والتأكيد.',
            'link'    => route('orders.chat', $order),
        ]);

        return back()->with('success', 'تم تسليم الطلب وإرفاق ملف المشروع بنجاح.');
    }


    public function updateDelivery(Request $request, Order $order)
    {
        // التحقق أن المستخدم هو البائع
        if (auth()->id() !== $order->seller_id) {
            abort(403);
        }

        // لا يسمح بالتعديل بعد تأكيد الاستلام (مثلاً status = completed)
        if ($order->status === 'completed') {
            return back()->with('error', 'لا يمكن تعديل ملف المشروع بعد تأكيد استلام الطلب من المشتري.');
        }

        $request->validate([
            'delivery_file' => 'required|file|max:20480|mimes:zip,rar,7z,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png',
            'delivery_note' => 'nullable|string|max:2000',
        ]);

        // حذف الملف القديم إن وجد
        if ($order->delivery_file) {
            Storage::disk('public')->delete($order->delivery_file);
        }

        // رفع الملف الجديد
        $path = $request->file('delivery_file')->store('deliveries', 'public');

        $order->delivery_file = $path;
        // تحديث الملاحظة إذا أرسل البائع ملاحظة جديدة
        if ($request->filled('delivery_note')) {
            $order->delivery_note = $request->delivery_note;
        }
        // يمكن تحديث تاريخ التسليم
        $order->delivered_at = now();
        $order->save();

        // (اختياري) إضافة رسالة في محادثة الطلب أنك عدلت الملف
        // Message::create([...]);

        return back()->with('success', 'تم تحديث ملف المشروع بنجاح، وتم استبدال الملف السابق.');
    }


        /**
     * تأكيد استلام الخدمة من طرف المشتري: delivered → completed
     * Route: POST /orders/{order}/complete
     */
    public function complete(Order $order)
    {
        $user = auth()->user();

        // فقط المشتري يمكنه تأكيد الاستلام
        if ($user->id !== $order->buyer_id) {
            abort(403, 'فقط المشتري يمكنه تأكيد استلام هذا الطلب.');
        }

        // يجب أن يكون الطلب في حالة تم التسليم
        if ($order->status !== 'delivered') {
            return redirect()
                ->route('orders.chat', $order)
                ->with('error', 'لا يمكن تأكيد الاستلام إلا بعد أن يكون الطلب في حالة "تم التسليم".');
        }

        // تحديث حالة الطلب
        $order->update([
            'status'       => 'completed',
            'completed_at' => now(),
        ]);

            if ($order->project_id && $order->project) {
                $project = $order->project;

                // فقط لو كان المشروع ما زال قيد التنفيذ أو مفتوح
                if (in_array($project->status, ['in_progress', 'open'])) {
                    $project->status = 'completed';
                    $project->save();
                }
            }

            // تحرير المبلغ للبائع إذا كان مدفوعًا ولم يتم تحريره من قبل
        if ($order->payment_status === 'paid' && is_null($order->released_at)) {
            $order->payment_status = 'released';
            $order->released_at    = now();

            // زيادة رصيد البائع (إن استخدمت balance في users)
            $seller = $order->seller; // تأكد أن عندك علاقة seller في موديل Order
            if ($seller) {
                $seller->balance = $seller->balance + $order->seller_earnings;
                $seller->save();
            }
        }
         $order->save();
        // 1) رسالة في محادثة الطلب
        $body = 'قام المشتري "' . $user->name . '" بتأكيد استلامه للطلب #' . $order->id . ' وتم إكماله بنجاح.';

        Message::create([
            'order_id'    => $order->id,
            'sender_id'   => $user->id,          // المشتري
            'receiver_id' => $order->seller_id,  // البائع
            'body'        => $body,
        ]);

        // 2) إشعار للبائع بأن الطلب اكتمل
        Notification::create([
            'user_id' => $order->seller_id,
            'type'    => 'order',
            'title'   => 'تم إكمال الطلب #' . $order->id,
            'body'    => 'قام المشتري "' . $user->name . '" بتأكيد استلام الطلب وتم إكماله. يمكنك إضافة تقييم إن رغبت.',
            'link'    => route('orders.chat', $order),
        ]);

        return redirect()
            ->route('orders.chat', $order)
            ->with('success', 'تم تأكيد استلام الطلب، وتم إكماله بنجاح.');
    }

    /**
     * إلغاء الطلب: pending أو in_progress → cancelled
     * Route: POST /orders/{order}/cancel
     */
    public function cancel(Request $request, Order $order)
    {
        // 1) السماح فقط للمشتري بإلغاء الطلب
        if (auth()->id() !== $order->buyer_id) {
            abort(403, 'فقط المشتري يمكنه إلغاء هذا الطلب.');
        }

        // 2) منع الإلغاء بعد التسليم / الإكمال / الإلغاء السابق
        if (in_array($order->status, ['delivered', 'completed', 'cancelled'])) {
            return redirect()
                ->route('orders.chat', $order)
                ->with('error', 'لا يمكن إلغاء الطلب بعد تسليم المشروع أو اكتماله.');
        }

        // مسموح الإلغاء فقط في هذه الحالات
        if (! in_array($order->status, ['pending', 'in_progress'])) {
            return redirect()
                ->route('orders.chat', $order)
                ->with('error', 'لا يمكن إلغاء هذا الطلب في حالته الحالية.');
        }

        if ($order->project_id && $order->project) {
            $project = $order->project;

            if (! in_array($project->status, ['completed', 'cancelled'])) {
                $project->status = 'cancelled';
                $project->save();
            }
        }

        // 3) التحقق من سبب الإلغاء (اختياري)
        $validated = $request->validate([
            'cancel_reason' => 'nullable|string|max:2000',
        ]);

        // 4) تحديث حالة الطلب وتخزين سبب الإلغاء وتاريخ الإلغاء
        $order->status        = 'cancelled';
        $order->cancel_reason = $validated['cancel_reason'] ?? null;
        $order->cancelled_at  = now();
        $order->save();

        // 5) تحضير نص السبب للرسائل/الإشعار
        $reasonText = $order->cancel_reason
            ? ' سبب الإلغاء: ' . $order->cancel_reason
            : ' لم يتم ذكر سبب الإلغاء.';

        // 6) إضافة رسالة في محادثة الطلب توضّح أن المشتري ألغى الطلب
        // هنا نستخدم حقل body المطلوب في جدول messages
        Message::create([
            'order_id'    => $order->id,
            'sender_id'   => auth()->id(),      // المشتري الحالي
            'receiver_id' => $order->seller_id, // البائع
            'body'        => 'قام المشتري بإلغاء الطلب #' . $order->id . '.' . $reasonText,
        ]);

        // 7) إشعار للبائع بأن المشتري ألغى الطلب
        Notification::create([
            'user_id' => $order->seller_id,
            'type'    => 'order',
            'title'   => 'تم إلغاء الطلب #' . $order->id,
            'body'    => 'قام المشتري ' . auth()->user()->name . ' بإلغاء الطلب.' . $reasonText,
            'link'    => route('orders.chat', $order),
        ]);

        return redirect()
            ->route('orders.chat', $order)
            ->with('success', 'تم إلغاء الطلب بنجاح.');
    }

        public function reject(Request $request, Order $order)
    {
        // فقط البائع صاحب الخدمة يمكنه رفض الطلب
        if (auth()->id() !== $order->seller_id) {
            abort(403, 'غير مسموح لك بتنفيذ هذا الإجراء.');
        }

        // لا نسمح برفض الطلب إلا وهو في حالة "قيد الانتظار"
        if ($order->status !== 'pending') {
            return redirect()
                ->route('orders.chat', $order)
                ->with('error', 'لا يمكن رفض الطلب في حالته الحالية.');
        }

        // يمكن لاحقاً إضافة حقل سبب الرفض في الفورم إن رغبت
        $reason = trim((string) $request->input('reject_reason'));
        $reasonText = $reason !== '' ? ' السبب: ' . $reason : '';

        // تحديث حالة الطلب إلى "ملغى" مع سبب واضح
        $order->update([
            'status'        => 'cancelled',
            'cancel_reason' => $reason !== ''
                ? 'تم رفض الطلب من قبل مزود الخدمة.' . ' ' . $reason
                : 'تم رفض الطلب من قبل مزود الخدمة.',
        ]);

        // رسالة في محادثة الطلب تُعلِم المشتري
        Message::create([
            'order_id'    => $order->id,
            'sender_id'   => auth()->id(),       // البائع
            'receiver_id' => $order->buyer_id,   // المشتري
            'body'        => 'قام مزود الخدمة برفض الطلب #' . $order->id . '.' . $reasonText,
        ]);

        // إشعار للمشتري بأن البائع رفض الطلب
        Notification::create([
            'user_id' => $order->buyer_id,
            'type'    => 'order',
            'title'   => 'تم رفض الطلب #' . $order->id,
            'body'    => 'قام مزود الخدمة برفض الطلب #' . $order->id . '.' . $reasonText,
            'link'    => route('orders.chat', $order),
        ]);

        return redirect()
            ->route('orders.chat', $order)
            ->with('success', 'تم رفض الطلب وإبلاغ المشتري.');
    }


}