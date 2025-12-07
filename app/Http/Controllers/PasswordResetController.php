<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\ResetPasswordMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    // صفحة إدخال الإيميل
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    // استلام الإيميل وتوليد رابط الاستعادة (بدون إرسال بريد فعلياً)
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'لا يوجد مستخدم مسجل بهذا البريد.',
        ]);
        $user = User::where('email', $request->email)->first();

        // التأكد أن البريد مفعّل قبل إرسال رابط الاستعادة
        if (! $user->hasVerifiedEmail()) {
            return back()->withErrors([
                'email' => 'هذا الحساب لم يتم تفعيل بريده الإلكتروني بعد. يرجى تفعيل البريد أولاً.',
            ]);
        }
        $token = Str::random(64);

        // حذف أي طلبات قديمة لهذا الإيميل
        DB::table('password_resets')->where('email', $request->email)->delete();

        // إنشاء طلب جديد
        DB::table('password_resets')->insert([
            'email'      => $request->email,
            'token'      => $token,
            'created_at' => now(),
        ]);

        // في نظام حقيقي نرسل هذا الرابط عبر البريد
        $resetLink = url('/reset-password/' . $token . '?email=' . urlencode($request->email));

        // هنا يمكنك لاحقاً استخدام Mail لإرسال الرابط فعلياً
        Mail::to($user->email)->send(new ResetPasswordMail($resetLink , $user));
        
        // لأغراض مشروع التخرج: نعرض الرابط في رسالة نجاح فقط
        return back()->with('status', 'تم إرسال رابط استعادة كلمة المرور إلى بريدك الإلكتروني.');
    }

    // صفحة إدخال كلمة المرور الجديدة
    public function showResetForm(Request $request, $token)
    {
        $email = $request->query('email');

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    // حفظ كلمة المرور الجديدة
    public function reset(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email|exists:users,email',
            'password' => 'required|confirmed|min:6',
        ]);

        $resetRecord = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (! $resetRecord) {
            return back()->withErrors([
                'email' => 'رابط استعادة كلمة المرور غير صالح أو منتهي الصلاحية.',
            ]);
        }

        // تحديث كلمة مرور المستخدم
        $user = User::where('email', $request->email)->firstOrFail();
        $user->password = Hash::make($request->password);
        $user->save();

        // حذف السجل حتى لا يُستخدم الرابط مرة أخرى
        DB::table('password_resets')
            ->where('email', $request->email)
            ->delete();

        return redirect()->route('auth')->with('success', 'تم تحديث كلمة المرور بنجاح، يمكنك الآن تسجيل الدخول.');
    }
}