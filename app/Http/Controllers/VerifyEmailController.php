<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    public function __invoke(Request $request, $id, $hash)
    {
        // البحث عن المستخدم
        $user = User::findOrFail($id);

        // التحقق من أن الـ hash في الرابط صحيح
        if (! hash_equals(sha1($user->getEmailForVerification()), (string) $hash)) {
            abort(403, 'Invalid verification link.');
        }

        // إن لم يكن البريد مفعلاً بالفعل، نفعّله الآن
        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        // يمكن (اختياريًا) تسجيل الدخول تلقائياً بعد التفعيل
        auth()->login($user);

        return redirect()->route('home')->with('success', 'تم تفعيل بريدك الإلكتروني بنجاح.');
    }
}
