@extends('layouts.app')

@section('title', 'تعديل خدمة | منصة كوفية')

@section('content')
<main>
    <section class="add-service-section">
        <div class="container">

            <div class="add-service-header page-header">
                <div>
                    <h1 class="page-title">تعديل الخدمة</h1>
                    <p class="page-subtitle">
                        يمكنك تعديل بيانات هذه الخدمة، مع الحفاظ على طلباتها وتقييماتها الحالية.
                    </p>
                </div>
            </div>

            <div class="add-service-grid">
                <div class="add-service-main">

                    @if ($errors->any())
                    <div
                        style="background:#ffe6e6; border-radius:12px; padding:10px 12px; font-size:13px; margin-bottom:10px;">
                        @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                        @endforeach
                    </div>
                    @endif

                    <form class="add-service-form" method="POST"
                        action="{{ route('services.update', $service->slug) }}">
                        @csrf
                        @method('PUT')

                        <div class="service-form-block">
                            <h2 class="service-form-title">معلومات الخدمة الأساسية</h2>

                            <div class="service-form-grid-2">
                                <div class="form-group">
                                    <label for="title">عنوان الخدمة</label>
                                    <input type="text" id="title" name="title"
                                        value="{{ old('title', $service->title) }}">
                                </div>

                                <div class="form-group">
                                    <label for="category_id">التصنيف</label>
                                    <select id="category_id" name="category_id">
                                        @foreach($categories as $category)
                                        <option value="{{ $category->id }}" @selected(old('category_id', $service->
                                            category_id) == $category->id)>
                                            {{ $category->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="short_description">وصف مختصر</label>
                                <input type="text" id="short_description" name="short_description"
                                    value="{{ old('short_description', $service->short_description) }}">
                            </div>

                            <div class="form-group">
                                <label for="description">وصف تفصيلي للخدمة</label>
                                <textarea id="description" name="description"
                                    rows="5">{{ old('description', $service->description) }}</textarea>
                            </div>
                        </div>

                        <div class="service-form-block">
                            <h2 class="service-form-title">التسعير ومدة التنفيذ</h2>

                            <div class="service-form-grid-3">
                                <div class="form-group">
                                    <label for="price">السعر</label>
                                    <div class="input-with-addon">
                                        <input type="number" step="0.5" min="1" id="price" name="price"
                                            value="{{ old('price', $service->price) }}">
                                        <span class="input-addon">$</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="delivery_time">مدة التسليم (أيام)</label>
                                    <input type="number" min="1" id="delivery_time" name="delivery_time"
                                        value="{{ old('delivery_time', $service->delivery_time) }}">
                                </div>

                                <div class="form-group">
                                    <label for="level">مستوى الباقة</label>
                                    <select id="level" name="level">
                                        <option value="basic" @selected(old('level', $service->level) ===
                                            'basic')>أساسية</option>
                                        <option value="standard" @selected(old('level', $service->level) ===
                                            'standard')>متوسطة</option>
                                        <option value="premium" @selected(old('level', $service->level) ===
                                            'premium')>مميزة</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="service-form-block">
                            <h2 class="service-form-title small">حالة الخدمة</h2>

                            <div class="service-form-grid-2">
                                <div class="form-group">
                                    <label for="status">الحالة</label>
                                    <select id="status" name="status">
                                        <option value="active" @selected(old('status', $service->status) ===
                                            'active')>نشطة</option>
                                        <option value="paused" @selected(old('status', $service->status) ===
                                            'paused')>متوقفة مؤقتاً</option>
                                        <option value="draft" @selected(old('status', $service->status) ===
                                            'draft')>مسودة</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="service-form-actions">
                            <button type="submit" class="btn btn-primary">
                                حفظ التعديلات
                            </button>
                            <a href="{{ route('seller.services.index') }}" class="btn btn-outline">
                                إلغاء
                            </a>
                        </div>

                    </form>
                </div>

                <aside class="add-service-sidebar">
                    <div class="service-tips-card">
                        <h3 class="service-form-title small">ملاحظة</h3>
                        <p style="font-size:13px; color:var(--color-muted);">
                            يمكنك في أي وقت تعديل نصوص الخدمة وسعرها وحالتها.
                            في هذه النسخة لا نقوم بتعديل الصور من هنا، وتظل كما هي حتى نضيف صفحة مخصصة لإدارة الصور.
                        </p>
                    </div>
                </aside>
            </div>

        </div>
    </section>
</main>
@endsection