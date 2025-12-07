@extends('layouts.app')

@section('title', 'إضافة خدمة جديدة | منصة كوفية')

@section('content')
<main>
    <section class="add-service-section">
        <div class="container">

            <div class="add-service-header page-header">
                <div>
                    <h1 class="page-title">إضافة خدمة جديدة</h1>
                    <p class="page-subtitle">
                        أضف خدمة احترافية مع وصف واضح وسعر مناسب لتصل إلى العملاء عبر منصة كوفية.
                    </p>
                </div>
            </div>

            {{-- شبكة العمودين --}}
            <div class="add-service-grid">

                {{-- العمود الرئيسي: نموذج إضافة الخدمة --}}
                <div class="add-service-main">

                    {{-- عرض الأخطاء --}}
                    @if ($errors->any())
                    <div
                        style="background:#ffe6e6; border-radius:12px; padding:10px 12px; font-size:13px; margin-bottom:10px;">
                        @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                        @endforeach
                    </div>
                    @endif

                    {{-- رسالة نجاح --}}
                    @if (session('success'))
                    <div
                        style="background:#e6ffef; border-radius:12px; padding:10px 12px; font-size:13px; margin-bottom:10px;">
                        {{ session('success') }}
                    </div>
                    @endif

                    <form class="add-service-form" method="POST" action="{{ route('services.store') }}"
                        enctype="multipart/form-data">
                        @csrf

                        {{-- معلومات أساسية --}}
                        <div class="service-form-block">
                            <h2 class="service-form-title">معلومات الخدمة الأساسية</h2>

                            <div class="service-form-grid-2">
                                <div class="form-group">
                                    <label for="title">عنوان الخدمة</label>
                                    <input type="text" id="title" name="title" value="{{ old('title') }}"
                                        placeholder="مثال: تصميم شعار احترافي لشركتك">
                                </div>

                                <div class="form-group">
                                    <label for="category_id">التصنيف</label>
                                    <select id="category_id" name="category_id">
                                        <option value="">اختر التصنيف</option>
                                        @foreach($categories as $category)
                                        <option value="{{ $category->id }}" @selected(old('category_id')==$category->
                                            id)>
                                            {{ $category->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <span class="field-hint">اختر التصنيف الأقرب لطبيعة خدمتك.</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="short_description">وصف مختصر</label>
                                <input type="text" id="short_description" name="short_description"
                                    value="{{ old('short_description') }}" placeholder="جملة قصيرة تلخص الخدمة">
                                <span class="field-hint">يظهر الوصف المختصر في البطاقات والقوائم.</span>
                            </div>

                            <div class="form-group">
                                <label for="description">وصف تفصيلي للخدمة</label>
                                <textarea id="description" name="description" rows="5"
                                    placeholder="اشرح هنا تفاصيل الخدمة، خطوات العمل، ما الذي سيحصل عليه العميل، وغيرها من الملاحظات المهمة.">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        {{-- التسعير والمدة --}}
                        <div class="service-form-block">
                            <h2 class="service-form-title">التسعير ومدة التنفيذ</h2>

                            <div class="service-form-grid-3">
                                <div class="form-group">
                                    <label for="price">السعر الابتدائي</label>
                                    <div class="input-with-addon">
                                        <input type="number" step="0.5" min="1" id="price" name="price"
                                            value="{{ old('price') }}" placeholder="مثال: 50">
                                        <span class="input-addon">$</span>
                                    </div>
                                    <span class="field-hint">يمكنك تعديل السعر لاحقاً من لوحة البائع.</span>
                                </div>

                                <div class="form-group">
                                    <label for="delivery_time">مدة التسليم (أيام)</label>
                                    <input type="number" min="1" id="delivery_time" name="delivery_time"
                                        value="{{ old('delivery_time') ?? 3 }}">
                                    <span class="field-hint">المدة التقريبية لتسليم الطلب بعد قبوله.</span>
                                </div>

                                <div class="form-group">
                                    <label for="level">مستوى الباقة</label>
                                    <select id="level" name="level">
                                        <option value="basic" @selected(old('level')==='basic' )>أساسية</option>
                                        <option value="standard" @selected(old('level')==='standard' )>متوسطة</option>
                                        <option value="premium" @selected(old('level')==='premium' )>مميزة</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- الحالة و الإعدادات --}}
                        <div class="service-form-block">
                            <h2 class="service-form-title small">إعدادات الخدمة</h2>

                            <div class="service-form-grid-2">
                                <div class="form-group">
                                    <label for="status">حالة الخدمة</label>
                                    <select id="status" name="status">
                                        <option value="active" @selected(old('status')==='active' )>نشطة</option>
                                        <option value="paused" @selected(old('status')==='paused' )>متوقفة مؤقتاً
                                        </option>
                                        <option value="draft" @selected(old('status')==='draft' )>مسودة (غير ظاهرة)
                                        </option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>المراسلة قبل الطلب</label>
                                    <div class="toggle-row">
                                        <label class="switch">
                                            <input type="checkbox" name="allow_messages_before_order" value="1"
                                                {{ old('allow_messages_before_order', true) ? 'checked' : '' }}>
                                            <span class="slider"></span>
                                        </label>
                                        <span>السماح للمشترين بمراسلتك قبل إرسال الطلب</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- رفع الصور --}}
                        <div class="service-form-block">
                            <h2 class="service-form-title small">صور ونماذج أعمال (اختياري)</h2>

                            <div class="form-group">
                                <label>صور الخدمة</label>
                                <label class="file-upload-area">
                                    انقر هنا لاختيار الصور أو قم بسحبها وإفلاتها
                                    <input type="file" name="images[]" multiple>
                                    <div class="file-upload-text">
                                        يمكنك رفع أكثر من صورة واحدة (حتى 2 ميجابايت لكل صورة).
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- أزرار الإرسال --}}
                        <div class="service-form-actions">
                            <button type="submit" class="btn btn-primary">
                                حفظ الخدمة
                            </button>
                        </div>
                    </form>

                </div>

                {{-- العمود الجانبي: نصائح + معاينة مبسطة --}}
                <aside class="add-service-sidebar">

                    <div class="service-tips-card">
                        <h3 class="service-form-title small">نصائح لكتابة خدمة ناجحة</h3>
                        <ul class="tips-list">
                            <li>اكتب عنواناً واضحاً يصف الخدمة بدقة.</li>
                            <li>اشرح خطوات العمل وما يتوقعه العميل بالتفصيل.</li>
                            <li>حدد مدة تسليم واقعية تناسب عبء العمل لديك.</li>
                            <li>اختر سعراً يناسب جودة العمل والمنافسة.</li>
                            <li>أضف نماذج وصوراً حقيقية لأعمالك السابقة إن أمكن.</li>
                        </ul>
                    </div>

                    <div class="service-preview-card">
                        <div class="service-image">
                            <span class="service-badge">معاينة</span>
                        </div>
                        <div class="service-body">
                            <div class="service-title">
                                {{ old('title', 'عنوان الخدمة سيظهر هنا') }}
                            </div>
                            <div class="service-seller">
                                بواسطة: {{ auth()->user()->name }}
                            </div>
                            <div class="service-meta">
                                <div>مدة التسليم: {{ old('delivery_time', 3) }} أيام</div>
                                <div class="service-price">
                                    ابتداءً من {{ old('price', 50) }}$
                                </div>
                            </div>
                        </div>
                        <div class="service-footer">
                            <div class="stars">★★★★★</div>
                            <span style="font-size:11px; color:var(--color-muted);">
                                هذه مجرد معاينة شكلية لبطاقة الخدمة.
                            </span>
                        </div>
                    </div>

                </aside>

            </div>
        </div>
    </section>
</main>
@endsection