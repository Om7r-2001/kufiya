<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ServiceImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\ServiceController;
use App\Models\Service;
use App\Models\User;
use App\Models\Notification;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::orderBy('name')->get();

        $query = Service::with(['seller', 'category', 'images'])
            ->approved();

        $currentCategory = null;
        $search = $request->get('q');

        // فلترة بالتصنيف (ID أو slug)
        if ($request->filled('category')) {
            $categoryParam = $request->get('category');

            $currentCategory = Category::where('slug', $categoryParam)
                ->orWhere('id', $categoryParam)
                ->first();

            if ($currentCategory) {
                $query->where('category_id', $currentCategory->id);
            }
        }

        // فلترة بالبحث النصي
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                ->orWhere('short_description', 'like', '%' . $search . '%');
            });
        }

        $services = $query->latest()->paginate(12)->withQueryString();

        return view('services.index', compact(
            'services',
            'categories',
            'currentCategory',
            'search'
        ));
    }

    public function show(Service $service)
        {
                $user = auth()->user();

                // السماح العام للخدمات المقبولة
                if ($service->status === 'approved') {
                    $existingOrder = null;

                    if ($user && $user->role === 'buyer') {
                        $existingOrder = $service->orders()
                            ->where('buyer_id', $user->id)
                            ->latest()
                            ->first();
                    }

                    return view('services.details', compact('service', 'existingOrder'));
                }

            // خدمات غير مقبولة
            if (! $user) {
                abort(404);
            }

            $isOwner = ($user->id === $service->user_id);
            $isAdmin = ($user->role === 'admin');

            if (! $isOwner && ! $isAdmin) {
                abort(404);
            }

            // حتى لصاحب الخدمة/المشرف نحسب الـ order لو كان المستخدِم Buyer
            $existingOrder = null;
            if ($user->role === 'buyer') {
                $existingOrder = $service->orders()
                    ->where('buyer_id', $user->id)
                    ->latest()
                    ->first();
            }

            return view('services.details', compact('service', 'existingOrder'));
    }


    public function create()
    {
            // لا يسمح إلا لمزود خدمة بالدخول
        if (auth()->user()->role !== 'seller') {
        abort(403, 'هذه الصفحة متاحة لمزودي الخدمات فقط.');
    }
        $categories = Category::all();
        return view('services.add', compact('categories'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'seller') {
            abort(403, 'هذه العملية متاحة لمزودي الخدمات فقط.');
        }

        $request->validate([
            'title'             => 'required|string|max:200',
            'category_id'       => 'required|exists:categories,id',
            'short_description' => 'nullable|string|max:500',
            'description'       => 'required|string',
            'price'             => 'required|numeric|min:1',
            'delivery_time'     => 'required|integer|min:1',
            'level'             => 'required|in:basic,standard,premium',
            'status'            => 'required|in:active,paused,draft',
            'allow_messages_before_order' => 'nullable|boolean',
            'images.*' => 'nullable|image|max:8192',
        ]);

        $slug = Str::slug($request->title);

        if (Service::where('slug', $slug)->exists()) {
            $slug .= '-' . time();
        }

        $service = Service::create([
            'user_id'                    => auth()->id(),
            'category_id'                => $request->category_id,
            'title'                      => $request->title,
            'slug'                       => $slug,
            'short_description'          => $request->short_description,
            'description'                => $request->description,
            'price'                      => $request->price,
            'delivery_time'              => $request->delivery_time,
            'level'                      => $request->level,
            'status'                     => $request->status,
            'allow_messages_before_order'=> $request->boolean('allow_messages_before_order'),
        ]);
        
        // إرسال إشعار للمشرفين بوجود خدمة جديدة بانتظار المراجعة
        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type'    => 'new_service',
                'title'   => 'خدمة جديدة بانتظار المراجعة',
                'body'    => mb_substr($service->title ?? 'خدمة جديدة من ' . $service->seller->name, 0, 100),
                'link'    => route('admin.services.index'),
            ]);
        }

        // حفظ الصور (اختياري)
        if ($request->hasFile('images')) {
            $isFirst = true;

            foreach ($request->file('images') as $file) {
                $path = $file->store('services', 'public'); // ينتج مثل: services/xxxx.jpg

                ServiceImage::create([
                    'service_id' => $service->id,
                    'path'       => $path,       // سنستخدم asset('storage/'.$path) في العرض
                    'is_main'    => $isFirst,
                ]);

                $isFirst = false;
            }
        }

        return redirect()
            ->route('services.show', $service->slug)
            ->with('success', 'تم إضافة الخدمة بنجاح.');

    }
    
    public function myServices()
    {
        if (auth()->user()->role !== 'seller') {
            abort(403, 'هذه الصفحة متاحة لمزودي الخدمات فقط.');
        }

        $categories = Category::orderBy('name')->get();

        $services = Service::with('category')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('dashboard.seller.services', compact('services', 'categories'));
    }

    public function edit(Service $service)
    {
        if (auth()->id() !== $service->user_id) {
            abort(403, 'لا يمكنك تعديل خدمة لا تملكها.');
        }

        $categories = Category::orderBy('name')->get();

        return view('services.edit', compact('service', 'categories'));
    }

    public function update(Request $request, Service $service)
    {
        if (auth()->id() !== $service->user_id) {
            abort(403, 'لا يمكنك تعديل خدمة لا تملكها.');
        }

        $request->validate([
            'title'             => 'required|string|max:200',
            'category_id'       => 'required|exists:categories,id',
            'short_description' => 'nullable|string|max:500',
            'description'       => 'required|string',
            'price'             => 'required|numeric|min:1',
            'delivery_time'     => 'required|integer|min:1',
            'level'             => 'required|in:basic,standard,premium',
            'status'            => 'required|in:active,paused,draft',
        ]);

        // تحديث slug فقط لو تغيّر العنوان
        if ($service->title !== $request->title) {
            $slug = Str::slug($request->title);
            if (Service::where('slug', $slug)->where('id', '!=', $service->id)->exists()) {
                $slug .= '-' . time();
            }
            $service->slug = $slug;
        }

        $service->update([
            'category_id'       => $request->category_id,
            'title'             => $request->title,
            'short_description' => $request->short_description,
            'description'       => $request->description,
            'price'             => $request->price,
            'delivery_time'     => $request->delivery_time,
            'level'             => $request->level,
            'status'            => $request->status,
        ]);

        return redirect()
            ->route('seller.services.index')
            ->with('success', 'تم تحديث بيانات الخدمة بنجاح.');
    }

    public function destroy(Service $service)
    {
        if (auth()->id() !== $service->user_id) {
            abort(403, 'لا يمكنك حذف خدمة لا تملكها.');
        }

        // حذف الصور من التخزين (اختياري لكن أفضل)
        foreach ($service->images as $image) {
            if (\Storage::disk('public')->exists($image->path)) {
                \Storage::disk('public')->delete($image->path);
            }
            $image->delete();
        }

        $service->delete();

        return redirect()
            ->route('seller.services.index')
            ->with('success', 'تم حذف الخدمة بنجاح.');
    }
}