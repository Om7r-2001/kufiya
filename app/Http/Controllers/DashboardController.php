<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Service;
use App\Models\Project;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // لوحة المشتري
    public function buyer()
    {
        $userId = auth()->id();

        $totalOrders     = Order::where('buyer_id', $userId)->count();
        $inProgress      = Order::where('buyer_id', $userId)
                                ->whereIn('status', ['pending', 'in_progress'])
                                ->count();
        $completedOrders = Order::where('buyer_id', $userId)
                                ->where('status', 'completed')
                                ->count();

        $latestOrders = Order::with(['service', 'project', 'seller'])
            ->where('buyer_id', $userId)
            ->latest()
            ->take(5)
            ->get();
            
        $latestProjects = Project::where('user_id', $userId)
            ->latest()
            ->take(5)
            ->withCount('bids')
            ->get();

        return view('dashboard.buyer.index', compact(
            'totalOrders',
            'inProgress',
            'completedOrders',
            'latestOrders',
            'latestProjects'
        ));
    }

    // لوحة البائع
    public function seller()
    {
        $userId = auth()->id();

        $totalServices = Service::where('user_id', $userId)->count();

        $totalOrders = Order::where('seller_id', $userId)->count();

        $inProgress = Order::where('seller_id', $userId)
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();

        // أرباح تقريبية (بدون خصومات المنصة حالياً)
        $estimatedEarnings = Order::where('seller_id', $userId)
            ->whereIn('status', ['completed'])
            ->sum('price');

        $latestOrders = Order::with(['service', 'buyer'])
            ->where('seller_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        $latestServices = Service::where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.seller.index', compact(
            'totalServices',
            'totalOrders',
            'inProgress',
            'estimatedEarnings',
            'latestOrders',
            'latestServices'
        ));
    }
}
