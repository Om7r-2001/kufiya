<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectBid;
use App\Models\Order; 
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectBidController extends Controller
{
    // تقديم عرض على مشروع
    public function store(Request $request, Project $project)
    {
        $user = Auth::user();

        // التأكد أنه مزود خدمة
        if ($user->role !== 'seller' && $user->role !== 'both') {
            return back()->with('error', 'فقط مزود الخدمة يمكنه تقديم عرض على المشروع.');
        }

        // لا يمكن لصاحب المشروع تقديم عرض على مشروعه
        if ($project->user_id == $user->id) {
            return back()->with('error', 'لا يمكنك تقديم عرض على مشروع قمت بإنشائه.');
        }

        // المشروع يجب أن يكون مفتوحاً
        if ($project->status !== 'open') {
            return back()->with('error', 'هذا المشروع لم يعد مفتوحاً لاستقبال العروض.');
        }

        // منع تكرار عرض لنفس المشروع
        $exists = ProjectBid::where('project_id', $project->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'لقد قدّمت عرضاً لهذا المشروع من قبل.');
        }

        $data = $request->validate([
            'amount'        => 'required|integer|min:1',
            'delivery_days' => 'nullable|integer|min:1',
            'message'       => 'nullable|string|max:2000',
        ]);

        $data['project_id'] = $project->id;
        $data['user_id']    = $user->id;

        $bid = ProjectBid::create($data);

        // تحديث عدد العروض في المشروع
        $project->increment('bids_count');

        // ====== إشعار صاحب المشروع بعرض جديد ======
        Notification::create([
            'user_id' => $project->user_id, // صاحب المشروع (المشتري)
            'type'    => 'project_bid',
            'title'   => 'عرض جديد على مشروعك "' . $project->title . '"',
            'body'    => 'قدّم ' . ($user->name ?? 'مزود خدمة') . ' عرضاً بقيمة ' . $bid->amount . '$ على مشروعك.',
            'link'    => route('projects.show', $project),
        ]);
        // =========================================

        return back()->with('success', 'تم إرسال عرضك بنجاح.');

    }

    // قبول عرض من قبل صاحب المشروع
    public function accept(Project $project, ProjectBid $bid)
    {
        $user = Auth::user();

        // صاحب المشروع فقط
        if ($project->user_id != $user->id) {
            abort(403, 'غير مسموح لك بتنفيذ هذا الإجراء.');
        }

        // تأكد أن العرض يتبع هذا المشروع
        if ($bid->project_id != $project->id) {
            abort(404);
        }

        // المشروع يجب أن يكون مفتوحاً
        if ($project->status !== 'open') {
            return back()->with('error', 'هذا المشروع لم يعد مفتوحاً.');
        }

        // حفظ العرض المختار في المشروع
        $project->update([
            'selected_bid_id' => $bid->id,
            // نترك status = open إلى أن يتم الدفع فعلياً
        ]);

        // حساب المبالغ (مثل الخدمات تماماً)
        $total         = $bid->amount;
        $platformFee   = round($total * 0.20, 2);          // 20%
        $sellerEarning = $total - $platformFee;

        // تحديد مدة التسليم من العرض أو المشروع
        $deliveryDays = $bid->delivery_days
            ?? $project->delivery_days
            ?? 7;

        // إنشاء الطلب بحالة "بانتظار الدفع"
        $order = Order::create([
            'service_id'      => null,               // طلب من مشروع وليس من خدمة
            'project_id'      => $project->id,
            'buyer_id'        => $project->user_id,  // صاحب المشروع هو من يدفع
            'seller_id'       => $bid->user_id,      // صاحب العرض هو مزود الخدمة
            'price'           => $bid->amount,
            'status'          => 'pending_payment',
            'notes'           => null,
            'delivery_date'   => now()->addDays($deliveryDays),
            'completed_at'    => null,
            'total_price'     => $total,
            'platform_fee'    => $platformFee,
            'seller_earnings' => $sellerEarning,
            // payment_status عندك غالباً default = 'pending' من الميجريشن
        ]);

        // توجيه المشتري مباشرة إلى بوابة الدفع الوهمية
        return redirect()
            ->route('orders.payment.show', $order)
            ->with('success', 'تم اختيار العرض بنجاح. الرجاء إتمام عملية الدفع لإرسال الطلب إلى مزود الخدمة.');
    }

}

