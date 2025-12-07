@extends('layouts.app')

@section('title', 'الأسئلة الشائعة | منصة كوفية')

@section('content')
<main>
    <section class="static-page-section">
        <div class="container static-page">

            <div class="page-header">
                <div>
                    <h1 class="page-title">الأسئلة الشائعة</h1>
                    <p class="page-subtitle">
                        في هذه الصفحة تجد إجابات عن أكثر الأسئلة تكراراً حول طريقة عمل منصة كوفية.
                    </p>
                </div>
            </div>

            <div class="static-block faq-block">
                <h2 class="static-title">أسئلة عامة</h2>

                <div class="faq-item">
                    <div class="faq-q">ما هي منصة كوفية؟</div>
                    <div class="faq-a">
                        هي منصة إلكترونية تجمع بين مزودي الخدمات والعملاء في مجالات متعددة
                        مع نظام طلبات، مراسلة، وتقييمات، لضمان تجربة آمنة واحترافية للطرفين.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-q">كيف أسجّل في المنصة؟</div>
                    <div class="faq-a">
                        يمكنك إنشاء حساب جديد من خلال صفحة التسجيل، ثم اختيار نوع حسابك
                        كمشتري أو مزود خدمة، واستكمال بياناتك الأساسية.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-q">هل يمكن أن أكون مشترياً وبائعاً في نفس الوقت؟</div>
                    <div class="faq-a">
                        يمكن للمستخدم التسجيل بدور واحد مبدئياً، ويمكن توسيع النظام لاحقاً
                        لدعم تعدد الأدوار حسب سياسة المنصة.
                    </div>
                </div>
            </div>

            <div class="static-block faq-block">
                <h2 class="static-title">الطلبات والدفع</h2>

                <div class="faq-item">
                    <div class="faq-q">كيف يتم إنشاء طلب جديد؟</div>
                    <div class="faq-a">
                        من صفحة تفاصيل الخدمة، يمكن للمشتري النقر على زر "طلب الخدمة الآن"،
                        ليتم إنشاء طلب مرتبط بالخدمة والبائع، ويمكن متابعته من لوحة المشتري.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-q">كيف يتم الدفع؟</div>
                    <div class="faq-a">
                        في النسخة الحالية من المشروع، الدفع افتراضي (تجريبي) لأغراض التقييم الأكاديمي،
                        ويمكن ربطه مستقبلاً ببوابات دفع حقيقية.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-q">متى يعتبر الطلب مكتملًا؟</div>
                    <div class="faq-a">
                        بعد قيام البائع بتسليم الطلب، يقوم المشتري بتأكيد استلام الخدمة، عندها
                        ينتقل الطلب إلى حالة "مكتمل" ويمكن للمشتري إضافة تقييم.
                    </div>
                </div>
            </div>

        </div>
    </section>
</main>
@endsection