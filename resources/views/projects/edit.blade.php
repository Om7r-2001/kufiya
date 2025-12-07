@extends('layouts.app')

@section('title', 'تعديل المشروع')

@section('content')
<section class="add-service-section">
    <div class="container">

        <div class="page-header add-service-header">
            <div>
                <h1 class="page-title">تعديل المشروع</h1>
                <p class="page-subtitle">
                    يمكنك تحديث تفاصيل مشروعك طالما ما زال مفتوحاً لاستقبال العروض.
                </p>
            </div>
        </div>

        <div class="add-service-grid">
            <div class="add-service-main">
                <form class="add-service-form" method="POST" action="{{ route('projects.update', $project) }}">
                    @csrf
                    @method('PUT')

                    <div class="service-form-block">
                        <h2 class="service-form-title">المعلومات الأساسية</h2>

                        <div class="service-form-grid-2">
                            <div class="form-group">
                                <label>عنوان المشروع</label>
                                <input type="text" name="title" value="{{ old('title', $project->title) }}" required>
                                @error('title') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="form-group">
                                <label>التصنيف</label>
                                <input type="text" name="category" value="{{ old('category', $project->category) }}"
                                    placeholder="مثال: برمجة، تصميم...">
                            </div>
                        </div>

                        <div class="service-form-grid-3">
                            <div class="form-group">
                                <label>الميزانية الدنيا</label>
                                <div class="input-with-addon">
                                    <input type="number" name="budget_min"
                                        value="{{ old('budget_min', $project->budget_min) }}" min="0">
                                    <span class="input-addon">$</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>الميزانية العليا</label>
                                <div class="input-with-addon">
                                    <input type="number" name="budget_max"
                                        value={{ old('budget_max', $project->budget_max) ?? '' }} min="0">
                                    <span class="input-addon">$</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>مدة التنفيذ المتوقعة (أيام)</label>
                                <input type="number" name="delivery_days"
                                    value="{{ old('delivery_days', $project->delivery_days) }}" min="1">
                            </div>
                        </div>
                    </div>

                    <div class="service-form-block">
                        <h2 class="service-form-title">وصف المشروع</h2>

                        <div class="form-group">
                            <label>وصف تفصيلي</label>
                            <textarea name="description" rows="5"
                                required>{{ old('description', $project->description) }}</textarea>
                            @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>
                    <div class="service-form-block">
                        <h2 class="service-form-title small">صورة المشروع</h2>

                        @if($project->image_path)
                        <div class="form-group" style="margin-bottom:12px;">
                            <label>الصورة الحالية:</label>
                            <div style="max-width:280px; border-radius:12px; overflow:hidden; border:1px solid #eee;">
                                <img src="{{ asset('storage/' . $project->image_path) }}" alt="صورة المشروع"
                                    style="width:100%; display:block;">
                            </div>
                        </div>
                        @endif

                        <div class="form-group">
                            <label>تغيير الصورة (اختياري)</label>
                            <label class="file-upload-area">
                                انقر هنا لاختيار صورة جديدة أو قم بسحبها وإفلاتها
                                <input type="file" name="image" accept="image/*">
                                <div class="file-upload-text">
                                    في حال اخترت صورة جديدة سيتم استبدال الصورة الحالية.
                                </div>
                            </label>
                            @error('image') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>
                    <div class="service-form-actions">
                        <a href="{{ route('projects.my') }}" class="btn btn-outline">إلغاء</a>
                        <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                    </div>
                </form>
            </div>

            <aside class="add-service-sidebar">
                <div class="service-tips-card">
                    <h3 class="service-form-title small">ملاحظة</h3>
                    <ul class="tips-list">
                        <li>يمكن تعديل المشروع فقط وهو في حالة "مفتوح".</li>
                        <li>بعد بدء التنفيذ أو اكتمال المشروع، يتم قفله للحفاظ على حقوق الطرفين.</li>
                    </ul>
                </div>
            </aside>
        </div>

    </div>
</section>
@endsection