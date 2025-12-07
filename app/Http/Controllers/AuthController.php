<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Notification;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|confirmed|min:6',
            'role'                  => 'required|in:buyer,seller',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        // إرسال إشعار لكل المشرفين عند تسجيل مستخدم جديد
        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,              // المشرف المستلم
                'type'    => 'new_user',             // نوع مخصص
                'title'   => 'تم تسجيل مستخدم جديد',
                'body'    => "الاسم: {$user->name} - الدور: {$user->role}",
                'link'    => route('admin.users.index'),
            ]);
        }

        auth()->login($user);

        // إرسال رسالة التفعيل
        $user->sendEmailVerificationNotification();

        // توجيه المستخدم لشاشة "فعِّل بريدك"
        return redirect()->route('verification.notice');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
            ->withErrors(['email' => 'بيانات الدخول غير صحيحة'])
            ->withInput($request->only('email'))
            ->with('auth_tab', 'login'); 
        }

        $request->session()->regenerate();

        return $this->redirectAfterLogin(Auth::user());
    }

    protected function redirectAfterLogin($user)
    {
        if ($user->role === 'seller') {
            return redirect()->route('dashboard.seller');
        }

        if ($user->role === 'buyer') {
            return redirect()->route('dashboard.buyer');
        }

        return redirect()->route('home');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth');
    }
}