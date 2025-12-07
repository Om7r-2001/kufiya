<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Category;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::with(['seller', 'category'])->latest();

        // فلتر بالحالة
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        // فلتر بالتصنيف
        if ($categoryId = $request->get('category_id')) {
            $query->where('category_id', $categoryId);
        }

        // بحث بالعنوان
        if ($search = $request->get('q')) {
            $query->where('title', 'like', '%' . $search . '%');
        }

        $services   = $query->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.services.index', compact('services', 'categories'));
    }

    public function updateStatus(Request $request, Service $service)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $service->status = $request->status;
        $service->save();
        $status = $service->status;
        // رسالة الإشعار للبائع حسب الحالة
        switch ($status) {

            case 'approved':
                $title = 'تم قبول خدمتك';
                $body  = "تمت الموافقة على خدمتك: {$service->title}.";
                break;

            case 'rejected':
                $title = 'تم رفض خدمتك';
                $body  = "تم رفض خدمتك: {$service->title}.";
                break;

            case 'in:pending':
                $title = 'حالة خدمتك قيد المراجعة';
                $body  = "تم تحديث حالة خدمتك ({$service->title}) إلى: قيد التنفيذ.";
                break;

            default:
                $title = 'تحديث حالة الخدمة';
                $body  = "تم تغيير حالة خدمتك ({$service->title}) إلى: {$status}.";
        }
        // إرسال الإشعار للبائع
        Notification::create([
            'user_id' => $service->user_id,         // صاحب الخدمة
            'type'    => 'service_status',
            'title'   => $title,
            'body'    => $body,
            'link'    => route('services.show', $service->slug),
        ]);


        return back()->with('success', 'تم تحديث حالة الخدمة بنجاح.');
    }

    public function destroy(Service $service)
    {
        // إن أردت حذف الصور/العلاقات التابعة للخدمة يمكنك إضافتها هنا

        $service->delete();

        return back()->with('success', 'تم حذف الخدمة بنجاح.');
    }
}
