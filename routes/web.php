<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\VerifyEmailController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\OrderPaymentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectBidController;
use Illuminate\Http\Request;

// الصفحة الرئيسية
Route::get('/', [HomeController::class, 'index'])
    ->name('home');

// صفحة auth (التبويب بين تسجيل و تسجيل دخول)
Route::get('/auth', function() {
    return view('auth.login-register');
})->name('auth');

// POST routes
// تسجيل مستخدم جديد
Route::post('/register', [AuthController::class, 'register'])->name('register');

// تسجيل الدخول
Route::post('/login', [AuthController::class, 'login'])->name('login');

// أي طلب GET على /login يتم تحويله لصفحة auth
Route::get('/login', function () {return redirect()->route('auth');});

// تسجيل الخروج 
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// الخدمات
Route::get('/services', [ServiceController::class, 'index'])
    ->name('services.index');

Route::get('/services/{service:slug}', [ServiceController::class, 'show'])
    ->name('services.show');

// مزودو الخدمات (صفحة عامة متاحة للزوار)
Route::get('/providers', [ProviderController::class, 'index'])
    ->name('providers.index');

Route::get('/providers/{seller}', [ProviderController::class, 'show'])
    ->name('providers.show');

// قائمة المشاريع + تفاصيلها (متاحة للجميع)
Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');

Route::middleware('auth')->group(function ()
    {
        //قائمة الخدمات 
        Route::get('/services/{service:slug}/edit', [ServiceController::class, 'edit'])
            ->name('services.edit');

        Route::put('/services/{service:slug}', [ServiceController::class, 'update'])
            ->name('services.update');

        Route::delete('/services/{service:slug}', [ServiceController::class, 'destroy'])
            ->name('services.destroy');

        // قائمة المشاريع 
        Route::get('/projects/create', [ProjectController::class, 'create'])
            ->name('projects.create');

        Route::post('/projects', [ProjectController::class, 'store'])
            ->name('projects.store');

        Route::get('/my-projects', [ProjectController::class, 'myProjects'])
            ->name('projects.my');

        // تقديم عرض على مشروع (مزود خدمة فقط – سنتحقق من الدور داخل الكنترولر)
        Route::post('/projects/{project}/bids', [ProjectBidController::class, 'store'])
            ->name('projects.bids.store');

        // قبول عرض (صاحب المشروع فقط)
        Route::post('/projects/{project}/bids/{bid}/accept', [ProjectBidController::class, 'accept'])
            ->name('projects.bids.accept');

        Route::get('/projects/{project:slug}/edit', [ProjectController::class, 'edit'])
            ->name('projects.edit');

        Route::put('/projects/{project:slug}', [ProjectController::class, 'update'])
            ->name('projects.update');

        Route::delete('/projects/{project:slug}', [ProjectController::class, 'destroy'])
            ->name('projects.destroy');

        //قائمة الاشعارات 
        Route::get('/notifications', [NotificationController::class, 'index'])
            ->name('notifications.index');

        Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])
            ->name('notifications.read');

        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
            ->name('notifications.readAll');

    });

Route::get('/projects/{project:slug}', [ProjectController::class, 'show'])->name('projects.show');

// الصفحات الثابتة العامة
Route::view('/about', 'static.about')->name('about');
Route::view('/faq', 'static.faq')->name('faq');
Route::view('/terms', 'static.terms')->name('terms');
Route::view('/privacy', 'static.privacy')->name('privacy');

//حظر الزائر من الخدمات 
Route::middleware(['auth', 'verified'])->group(function () 
    {
    
        // لوحة المشتري 
        Route::get('/dashboard/buyer', [DashboardController::class, 'buyer'])
            ->name('dashboard.buyer');

        // لوحة البائع 
        Route::get('/dashboard/seller', [DashboardController::class, 'seller'])
            ->name('dashboard.seller');

        // طلبات المشتري
        Route::get('/dashboard/buyer/orders', [OrderController::class, 'buyerIndex'])
            ->name('buyer.orders.index');

        // طلبات البائع
        Route::get('/dashboard/seller/orders', [OrderController::class, 'sellerIndex'])
            ->name('seller.orders.index');

        // إضافة خدمة
        Route::get('/add-service', [ServiceController::class, 'create'])
            ->name('services.create');

        Route::post('/add-service', [ServiceController::class, 'store'])
            ->name('services.store');

        // محادثة الطلب
        Route::get('/orders/{order}/chat', [MessageController::class, 'show'])
            ->name('orders.chat');

        Route::post('/orders/{order}/chat', [MessageController::class, 'store'])
            ->name('orders.chat.send');

        // إنشاء طلب على خدمة معيّنة
        Route::post('/services/{service:slug}/order', [OrderController::class, 'store'])
            ->name('orders.store');

        // صفحة الدفع الوهمية لطلب معيّن (عرض النموذج)
        Route::get('/orders/{order}/payment', [OrderController::class, 'showPaymentForm'])
            ->name('orders.payment.show');

        // تنفيذ الدفع الوهمي (من نموذج الدفع)
        Route::post('/orders/{order}/payment', [OrderController::class, 'processPayment'])
            ->name('orders.payment.pay');

        // بدء العمل على الطلب
        Route::post('/orders/{order}/start', [OrderController::class, 'start'])
            ->name('orders.start');

        //تسليم الطلب 
        Route::post('/orders/{order}/deliver', [OrderController::class, 'deliver'])
            ->name('orders.deliver');

        // تعديل الطلب 
        Route::post('/orders/{order}/delivery/update', [OrderController::class, 'updateDelivery'])
        ->name('orders.delivery.update');

        // إكمال الطلب بعد تأكيد المشتري
        Route::post('/orders/{order}/complete', [OrderController::class, 'complete'])
            ->name('orders.complete');

        // إلغاء الطلب من قبل المشتري
        Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])
            ->name('orders.cancel');

        // رفض الطلب من قبل البائع
        Route::post('/orders/{order}/reject', [OrderController::class, 'reject'])
            ->name('orders.reject');

        // إضافة تقييم لطلب مكتمل
        Route::post('/orders/{order}/review', [ReviewController::class, 'store'])
            ->name('orders.review.store');

        // صفحة عرض الملف الشخصي
        Route::get('/profile', [ProfileController::class, 'index'])
            ->name('profile.index');

        // صفحة الإعدادات (تعديل البيانات)
        Route::get('/settings', [ProfileController::class, 'settings'])
            ->name('profile.settings');

        // حفظ تعديل البيانات الأساسية
        Route::post('/settings/profile', [ProfileController::class, 'updateProfile'])
            ->name('profile.update');

        // تغيير كلمة المرور
        Route::post('/settings/password', [ProfileController::class, 'updatePassword'])
            ->name('profile.password');

        // قائمة خدمات البائع
        Route::get('/dashboard/seller/services', [ServiceController::class, 'myServices'])
            ->name('seller.services.index');

    });

// عرض صفحة "نسيت كلمة المرور"
Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])
    ->name('password.request');

// إرسال طلب الاستعادة (توليد رابط إعادة التعيين)
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])
    ->name('password.email');

// عرض صفحة إدخال كلمة المرور الجديدة
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])
    ->name('password.reset');

// حفظ كلمة المرور الجديدة
Route::post('/reset-password', [PasswordResetController::class, 'reset'])
    ->name('password.update');

// صفحة "رجاء فعّل بريدك"
Route::get('/email/verify', function () {
    return view('auth.verify-email');
    })->middleware('auth')->name('verification.notice');

// رابط التفعيل القادم من الإيميل (بدون EmailVerificationRequest)
Route::get('/email/verify/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

// إعادة إرسال رابط التفعيل
Route::post('/email/verification-notification', function (Request $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return back()->with('status', 'تم تفعيل بريدك مسبقاً.');
    }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'تم إرسال رابط تفعيل جديد إلى بريدك الإلكتروني.');
    })->middleware(['auth', 'throttle:6,1'])->name('verification.send');

    // مسارات لوحة المشرف
    Route::middleware(['auth', 'admin'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('/dashboard', [AdminController::class, 'dashboard'])
                ->name('dashboard');

    // إدارة المستخدمين
    Route::get('/users', [AdminUserController::class, 'index'])
        ->name('users.index');

    Route::get('/users/{user}', [AdminUserController::class, 'show'])
        ->name('users.show');

    Route::patch('/users/{user}/role', [AdminUserController::class, 'updateRole'])
        ->name('users.updateRole');

    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])
        ->name('users.destroy');

    // إدارة الخدمات
    Route::get('/services', [AdminServiceController::class, 'index'])
        ->name('services.index');

    Route::patch('/services/{service}/status', [AdminServiceController::class, 'updateStatus'])
        ->name('services.updateStatus');

    Route::delete('/services/{service}', [AdminServiceController::class, 'destroy'])
        ->name('services.destroy');

    Route::get('/services/{service}', [ServiceController::class, 'show'])
        ->name('services.show');
        });
