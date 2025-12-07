@extends('layouts.app')

@section('title', 'سياسة الخصوصية | منصة كوفية')

@section('content')
<main>
    <section class="static-page-section">
        <div class="container static-page">

            <div class="page-header">
                <div>
                    <h1 class="page-title">سياسة الخصوصية</h1>
                    <p class="page-subtitle">
                        توضح هذه السياسة كيفية تعامل منصة كوفية مع بيانات المستخدمين وحماية خصوصيتهم.
                    </p>
                </div>
            </div>

            <div class="static-block">
                <h2 class="static-title">جمع المعلومات</h2>
                <p class="static-text">
                    تقوم المنصة بجمع بعض البيانات الأساسية مثل الاسم، البريد الإلكتروني، وبيانات
                    الاستخدام العامة بهدف تحسين التجربة وتقديم الخدمات بالشكل الأمثل.
                </p>
            </div>

            <div class="static-block">
                <h2 class="static-title">استخدام المعلومات</h2>
                <ul class="static-list">
                    <li>إدارة حساب المستخدم وتقديم الخدمات المطلوبة.</li>
                    <li>تحسين واجهة وتجربة استخدام المنصة.</li>
                    <li>التواصل مع المستخدم عند الضرورة بخصوص الطلبات أو التحديثات.</li>
                </ul>
            </div>

            <div class="static-block">
                <h2 class="static-title">حماية البيانات</h2>
                <p class="static-text">
                    تسعى المنصة إلى حماية بيانات المستخدمين باستخدام إجراءات تقنية وتنظيمية مناسبة،
                    مع العلم أنه لا يوجد نظام آمن بنسبة 100% على الإنترنت.
                </p>
            </div>

            <div class="static-block">
                <h2 class="static-title">مشاركة المعلومات</h2>
                <p class="static-text">
                    لا تقوم المنصة ببيع أو تأجير بيانات المستخدمين لأطراف أخرى، ويتم مشاركة بعض
                    البيانات فقط عند الحاجة القانونية أو لتحسين الخدمة وفق ضوابط محددة.
                </p>
            </div>

        </div>
    </section>
</main>
@endsection