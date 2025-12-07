<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Service;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    /**
     * عرض قائمة المستخدمين مع البحث والفلترة
     */
    public function index(Request $request)
    {
        $role   = $request->get('role');   // لتصفية حسب الدور
        $search = $request->get('q');      // للبحث بالاسم أو البريد

        $query = User::query();

        // فلترة حسب نوع الحساب
        if ($role && in_array($role, ['admin', 'seller', 'buyer'])) {
            $query->where('role', $role);
        }

        // البحث بالاسم أو البريد
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // جلب المستخدمين مع ترقيم الصفحات
        $users = $query->latest()->paginate(15)->withQueryString();

        // إحصائيات سريعة
        $totalUsers   = User::count();
        $totalSellers = User::where('role', 'seller')->count();
        $totalBuyers  = User::where('role', 'buyer')->count();
        $totalAdmins  = User::where('role', 'admin')->count();

        return view('admin.users.index', compact(
            'users',
            'role',
            'search',
            'totalUsers',
            'totalSellers',
            'totalBuyers',
            'totalAdmins'
        ));
    }

    /**
     * تحديث دور المستخدم (buyer / seller / admin)
     */
    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,seller,buyer',
        ], [
            'role.required' => 'يرجى اختيار نوع الحساب.',
            'role.in'       => 'نوع الحساب غير صالح.',
        ]);

        // حماية: لا تسمح لنفسك كمشرف أن تنزع عن نفسك دور admin
        if ($user->id === auth()->id() && $request->role !== 'admin') {
            return back()->withErrors([
                'role' => 'لا يمكنك إزالة صلاحية المشرف عن حسابك.',
            ]);
        }

        $user->role = $request->role;
        $user->save();

        return back()->with('success', 'تم تحديث دور المستخدم بنجاح.');
    }

    /**
     * حذف مستخدم
     */
    public function destroy(User $user)
    {
        // حماية: لا تسمح للمشرف بحذف حسابه بنفسه
        if ($user->id === auth()->id()) {
            return back()->withErrors([
                'delete' => 'لا يمكنك حذف حسابك أنت كمشرف.',
            ]);
        }

        $user->delete();

        return back()->with('success', 'تم حذف المستخدم بنجاح.');
    }

    /**
     * تفاصيل مستخدم
     */
public function show(User $user)
{
    // عدد الخدمات التي يملكها هذا المستخدم (كمزود خدمة)
    $servicesCount = Service::where('user_id', $user->id)->count();
    // لو عندك اسم آخر للعمود (مثلاً owner_id) عدّله هنا

    // الطلبات كمشتري
    $buyerOrdersCount = Order::where('buyer_id', $user->id)->count();

    // الطلبات كمزود خدمة
    $sellerOrdersCount = Order::where('seller_id', $user->id)->count();

    // الطلبات المكتملة كمشتري أو كبائع
    $completedOrdersCount = Order::where(function ($q) use ($user) {
            $q->where('buyer_id', $user->id)
              ->orWhere('seller_id', $user->id);
        })
        ->where('status', 'completed')
        ->count();

    return view('admin.users.show', compact(
        'user',
        'servicesCount',
        'buyerOrdersCount',
        'sellerOrdersCount',
        'completedOrdersCount'
    ));
}


}
