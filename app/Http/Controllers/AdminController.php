<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Service;
use App\Models\Order;
use App\Models\Category;

class AdminController extends Controller
{
    public function index()
    {
        // إحصائيات المستخدمين
        $totalUsers   = User::count();
        $totalSellers = User::where('role', 'seller')->count();
        $totalBuyers  = User::where('role', 'buyer')->count();

        // إحصائيات الخدمات
        $totalServices      = Service::count();
        $approvedServices   = Service::where('status', 'approved')->count();
        $pendingServices    = Service::where('status', 'pending')->count();
        $rejectedServices   = Service::where('status', 'rejected')->count();

        // إحصائيات الطلبات
        $totalOrders     = Order::count();
        $pendingOrders   = Order::whereIn('status', ['pending', 'in_progress'])->count();
        $deliveredOrders = Order::where('status', 'delivered')->count();
        $completedOrders = Order::where('status', 'completed')->count();

        // أحدث العناصر
        $latestUsers = User::latest()->take(5)->get();
        $latestServices = Service::with('seller')->latest()->take(5)->get();
        $pendingServicesList = Service::with('seller')
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        $recentOrders = Order::with(['buyer', 'seller', 'service'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalSellers',
            'totalBuyers',
            'totalServices',
            'approvedServices',
            'pendingServices',
            'rejectedServices',
            'totalOrders',
            'pendingOrders',
            'deliveredOrders',
            'completedOrders',
            'latestUsers',
            'latestServices',
            'pendingServicesList',
            'recentOrders'
        ));
    }
        public function dashboard()
    {
        // فقط نستدعي index حتى لا نكرر الكود
        return $this->index();
    }
}
