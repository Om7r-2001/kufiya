<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use App\Models\Order;
use App\Models\Notification;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    // عرض صفحة المحادثة الخاصة بطلب معيّن
    public function show(Order $order)
    {
        // تأكيد أن المستخدم الحالي طرف في الطلب (مشتري أو بائع)
        if (! $this->userBelongsToOrder($order)) {
            abort(403, 'ليست لديك صلاحية لعرض هذه المحادثة.');
        }

        $order->load(['service', 'project', 'buyer', 'seller', 'messages.sender']);

        return view('orders.chat', [
            'order'  => $order,
            'messages' => $order->messages()->orderBy('created_at')->get(),
        ]);
    }

    // إرسال رسالة جديدة في الطلب
    public function store(Request $request, Order $order)
    {
        if (! $this->userBelongsToOrder($order)) {
            abort(403, 'ليست لديك صلاحية لإرسال رسالة في هذا الطلب.');
        }

        $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $sender = auth()->user();
        $receiverId = $sender->id === $order->buyer_id
            ? $order->seller_id
            : $order->buyer_id;

        Message::create([
            'order_id'    => $order->id,
            'sender_id'   => $sender->id,
            'receiver_id' => $receiverId,
            'body'        => $request->body,
            'is_read'     => false,
        ]);
        
        Notification::create([
            'user_id' => $receiverId, // الطرف الثاني في المحادثة
            'type'    => 'message',
            'title'   => 'رسالة جديدة في طلب #' . $order->id,
            'body'    => mb_substr($request->message, 0, 120),
            'link'    => route('orders.chat', $order),
        ]);

        return redirect()
            ->route('orders.chat', $order)
            ->with('success', 'تم إرسال الرسالة بنجاح.');
    }

    // دالة خاصة للتحقق أن المستخدم طرف في الطلب
    protected function userBelongsToOrder(Order $order): bool
    {
        $userId = auth()->id();
        return $order->buyer_id === $userId || $order->seller_id === $userId;
    }

    public function startChat($seller)
    {
        $buyer = auth()->user();

        // منع مراسلة النفس
        if ($buyer->id == $seller) {
            return back();
        }

        // جلب بيانات مزود الخدمة
        $sellerUser = User::findOrFail($seller);

        // هنا يمكنك جلب الرسائل السابقة بينهما لو عندك جدول messages
        // حالياً سنرسل صفحة المحادثة فارغة بين الطرفين

        return view('orders.chat', [
            'buyer'  => $buyer,
            'seller' => $sellerUser,
            // 'messages' => $messages ?? [],
        ]);
    }

}
