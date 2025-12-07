<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // أحدث الخدمات (مثلاً آخر 6 خدمات نشطة)
        $services = Service::with(['seller', 'category', 'images'])
            ->approved()
            ->latest()
            ->take(6)
            ->get();

        // التصنيفات مع عدد الخدمات في كل تصنيف
        $categories = Category::withCount('services')
            ->orderBy('name')
            ->get();

        // قيمة البحث الحالية (إن وُجدت) لنعرضها في حقل البحث
        $search = $request->query('q');

        // تمرير كل المتغيرات للواجهة
        return view('home.index', compact('services', 'categories', 'search'));
    }
}
