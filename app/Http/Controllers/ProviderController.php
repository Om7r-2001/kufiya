<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Service;
use App\Models\Review;
use Illuminate\Http\Request;

class ProviderController extends Controller
{
    /**
     * صفحة عرض جميع مزودي الخدمات
     */
    public function index(Request $request)
    {
        // بدء الاستعلام من مستخدمين دورهم مزود خدمة
        $query = User::where('role', 'seller');

        // فلترة بالبحث إن وجد
        if ($search = $request->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // جلب المزوّدين مع عدد الخدمات المنشورة لكل واحد
        $providers = $query
            ->withCount('services')   // تأكد أن علاقة services موجودة في موديل User
            ->orderBy('name')
            ->paginate(8);

        // المهم هنا: تمرير المتغير باسم $providers
        return view('providers.index', compact('providers'));
    }

    /**
     * صفحة تفاصيل مزود خدمة معيّن
     */
 public function show(User $seller)
    {
        // الخدمات الموافق عليها لهذا المزوّد + الصور + التصنيف
        $services = Service::with(['category', 'images'])
            ->where('user_id', $seller->id)      // حسب عمود الربط عندك
            ->where('status', 'approved')
            ->latest()
            ->paginate(6);

        // عدد الطلبات المنجزة كمزوّد (إن كان عندك هذه العلاقة)
        $completedOrdersCount = $seller->ordersAsSeller()
            ->where('status', 'completed')
            ->count();

        // تقييمات المزوّد (من جدول المراجعات)
        $reviews = Review::with(['buyer', 'service'])
            ->where('seller_id', $seller->id)
            ->latest()
            ->take(8)
            ->get();

        return view('providers.show', compact(
            'seller',
            'services',
            'completedOrdersCount',
            'reviews'
        ));
    }
}