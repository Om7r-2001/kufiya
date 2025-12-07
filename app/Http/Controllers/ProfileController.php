<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * عرض الملف الشخصي
     */
    public function index()
    {
        $user = auth()->user();

        return view('profile.index', compact('user'));
    }

    /**
     * صفحة إعدادات الحساب (تعديل البيانات + كلمة المرور)
     */
    public function settings()
    {
        $user = auth()->user();

        return view('profile.settings', compact('user'));
    }

    /**
     * تحديث البيانات الأساسية (الاسم، الإيميل، الهاتف، البلد، المدينة، الصورة)
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users,email,' . $user->id,
            'phone'   => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'city'    => 'nullable|string|max:100',
            'avatar'  => 'nullable|image|max:2048', // 2MB
        ]);

        // تحديث البيانات الأساسية
        $user->name    = $request->name;
        $user->email   = $request->email;
        $user->phone   = $request->phone;
        $user->country = $request->country;
        $user->city    = $request->city;

        // رفع الصورة الشخصية إن وُجدت
        if ($request->hasFile('avatar')) {
            // حذف القديمة اختيارياً
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path; // مثال: avatars/xxx.jpg
        }

        $user->save();

        return redirect()
            ->route('profile.settings')
            ->with('success_profile', 'تم تحديث بيانات الملف الشخصي بنجاح.');
    }

    /**
     * تغيير كلمة المرور
     */
    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'current_password'      => 'required',
            'password'              => 'required|confirmed|min:6',
        ]);

        // التأكد أن كلمة المرور الحالية صحيحة
        if (! Hash::check($request->current_password, $user->password)) {
            return back()
                ->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة.'])
                ->withInput();
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()
            ->route('profile.settings')
            ->with('success_password', 'تم تغيير كلمة المرور بنجاح.');
    }
}
