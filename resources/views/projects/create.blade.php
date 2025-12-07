@extends('layouts.app')

@section('title', 'إنشاء مشروع جديد')

@section('content')
<section class="add-service-section">
    <div class="container">

        <div class="page-header add-service-header">
            <div>
                <h1 class="page-title">إنشاء مشروع جديد</h1>
                <p class="page-subtitle">
                    أضف تفاصيل مشروعك ليستطيع مزودو الخدمات تقديم عروض مناسبة.
                </p>
            </div>
        </div>

        <div class="add-service-grid">
            <div class="add-service-main">
                <form class="add-service-form" method="POST" action="{{ route('projects.store') }}"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="service-form-block">
                        <h2 class="service-form-title">المعلومات الأساسية</h2>

                        <div class="service-form-grid-2">
                            <div class="form-group">
                                <label>عنوان المشروع</label>
                                <input type="text" name="title" value="{{ old('title') }}" required>
                                @error('title') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="form-group">
                                <label>التصنيف</label>
                                <input type="text" name="category" value="{{ old('category') }}"
                                    placeholder="مثال: برمجة، تصميم...">
                            </div>
                        </div>

                        <div class="service-form-grid-3">
                            <div class="form-group">
                                <label>الميزانية الدنيا</label>
                                <div class="input-with-addon">
                                    <input type="number" name="budget_min" value="{{ old('budget_min') }}" min="0">
                                    <span class="input-addon">$</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>الميزانية العليا</label>
                                <div class="input-with-addon">
                                    <input type="number" name="budget_max" value="{{ old('budget_max') }}" min="0">
                                    <span class="input-addon">$</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>مدة التنفيذ المتوقعة (أيام)</label>
                                <input type="number" name="delivery_days" value="{{ old('delivery_days') }}" min="1">
                            </div>
                        </div>
                    </div>
                    <div class="service-form-block">
                        <h2 class="service-form-title small">صورة المشروع (اختياري)</h2>

                        <div class="form-group">
                            <label>صورة غلاف للمشروع</label>
                            <label class="file-upload-area">
                                انقر هنا لاختيار صورة أو قم بسحبها وإفلاتها
                                <input type="file" name="image" accept="image/*">
                                <div class="file-upload-text">
                                    يفضّل صورة أبعاد أفقية بحجم لا يزيد عن 2 ميجابايت.
                                </div>
                            </label>
                            @error('image') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="service-form-block">
                        <h2 class="service-form-title">وصف المشروع</h2>

                        <div class="form-group">
                            <label>وصف تفصيلي</label>
                            <textarea name="description" rows="5" required>{{ old('description') }}</textarea>
                            @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="service-form-actions">
                        <a href="{{ route('projects.index') }}" class="btn btn-outline">إلغاء</a>
                        <button type="submit" class="btn btn-primary">نشر المشروع</button>
                    </div>
                </form>
            </div>

            <aside class="add-service-sidebar">
                <div class="service-tips-card">
                    <h3 class="service-form-title small">نصائح لكتابة مشروع واضح</h3>
                    <ul class="tips-list">
                        <li>اشرح هدف المشروع بشكل مباشر.</li>
                        <li>اذكر المتطلبات الأساسية والنتائج المتوقعة.</li>
                        <li>حدّد ميزانية تقريبية ومدة تنفيذ منطقية.</li>
                        <li>كلما كان الوصف أوضح حصلت على عروض أفضل.</li>
                    </ul>
                </div>
            </aside>
        </div>

    </div>
</section>
@endsection