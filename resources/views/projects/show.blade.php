@extends('layouts.app')

@section('title', $project->title)

@section('content')
<section class="service-details-section">
    <div class="container">

        <div class="page-header service-details-header">
            <div>
                <h1 class="page-title">{{ $project->title }}</h1>
                <p class="page-subtitle">
                    {{ Str::limit($project->description, 120) }}
                </p>
                <div class="service-meta-top">
                    <span class="badge badge-accent">{{ $project->category ?: 'مشروع' }}</span>
                    <span class="badge">عروض: {{ $project->bids_count }}</span>
                    <span class="badge">مشاهدات: {{ $project->views_count }}</span>
                </div>
            </div>
        </div>

        <div class="service-details-grid">
            <div class="service-main">
                @if($project->image_path)
                <div class="service-main-image" style="margin-bottom:16px;">
                    <img src="{{ asset('storage/' . $project->image_path) }}" alt="صورة المشروع"
                        style="width:100%; max-height:320px; object-fit:cover; border-radius:16px;">
                </div>
                @endif
                <section class="service-block">
                    <h2 class="service-block-title">تفاصيل المشروع</h2>
                    <p class="service-block-text">
                        {!! nl2br(e($project->description)) !!}
                    </p>

                    <ul class="service-list">
                        <li>
                            الميزانية:
                            @if($project->budget_min || $project->budget_max)
                            {{ $project->budget_min ?? $project->budget_max }}
                            @if($project->budget_max && $project->budget_max != $project->budget_min)
                            - {{ $project->budget_max }}
                            @endif
                            $
                            @else
                            غير محددة
                            @endif
                        </li>
                        <li>
                            مدة التنفيذ المتوقعة:
                            {{ $project->delivery_days ? $project->delivery_days . ' يوم' : 'غير محددة' }}
                        </li>
                        <li>حالة المشروع: {{ $project->status }}</li>
                    </ul>
                </section>

                <section class="service-block">
                    <h2 class="service-block-title">العروض المقدمة</h2>

                    @if($project->bids->isEmpty())
                    <p class="service-block-text">لم يتم تقديم أي عروض حتى الآن.</p>
                    @else
                    <div class="reviews-list">
                        @foreach($project->bids as $bid)
                        <article class="review-item">
                            <div class="review-header">
                                <div>
                                    <div class="review-name">
                                        {{ $bid->seller->name ?? 'مزود خدمة' }}
                                    </div>
                                </div>
                                <div class="review-rating">
                                    <span>{{ $bid->amount }} $</span> |
                                    <span>{{ $bid->delivery_days ? $bid->delivery_days . ' يوم' : 'غير محددة' }}</span>
                                    |
                                    <span>الحالة: {{ $bid->status }}</span>
                                </div>
                            </div>
                            @if($bid->message)
                            <p class="review-text">
                                {{ $bid->message }}
                            </p>
                            @endif

                            @auth
                            @if(auth()->id() === $project->user_id && $project->status === 'open' && $bid->status ===
                            'pending')
                            <form method="POST" action="{{ route('projects.bids.accept', [$project, $bid]) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-xs">
                                    قبول هذا العرض
                                </button>
                            </form>
                            @endif
                            @endauth
                        </article>
                        @endforeach
                    </div>
                    @endif
                </section>
            </div>

            <aside class="service-sidebar">
                <div class="seller-card">
                    <div class="seller-header">
                        <div class="seller-avatar">
                            {{ mb_substr($project->owner->name ?? 'م', 0, 1) }}
                        </div>
                        <div>
                            <div class="seller-name">
                                {{ $project->owner->name ?? 'مستخدم' }}
                            </div>
                            <div class="seller-meta">
                                صاحب المشروع
                            </div>

                        </div>
                    </div>
                </div>

                {{-- نموذج تقديم عرض --}}
                <div class="service-pricing-card">
                    <h3 class="service-block-title small">قدّم عرضك على المشروع</h3>

                    @if(session('success'))
                    <p class="service-block-text" style="color:green;">{{ session('success') }}</p>
                    @endif
                    @if(session('error'))
                    <p class="service-block-text" style="color:red;">{{ session('error') }}</p>
                    @endif

                    @auth
                    @if($user && $user->id === $project->user_id)
                    <p class="service-block-text">
                        أنت صاحب هذا المشروع، يمكنك فقط مراجعة العروض وقبول أحدها.
                    </p>
                    @elseif($project->status !== 'open')
                    <p class="service-block-text">
                        هذا المشروع لم يعد مفتوحاً لاستقبال العروض.
                    </p>
                    @elseif($userBid)
                    <p class="service-block-text">
                        لقد قدّمت عرضاً بالفعل لهذا المشروع (الحالة: {{ $userBid->status }}).
                    </p>
                    @elseif($user->role === 'seller' || $user->role === 'both')
                    <form method="POST" action="{{ route('projects.bids.store', $project) }}">
                        @csrf

                        <div class="form-group">
                            <label>قيمة العرض (بالدولار)</label>
                            <input type="number" name="amount" min="1" required value="{{ old('amount') }}">
                            @error('amount') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label>مدة التنفيذ المقترحة (أيام)</label>
                            <input type="number" name="delivery_days" min="1" value="{{ old('delivery_days') }}">
                        </div>

                        <div class="form-group">
                            <label>رسالة العرض</label>
                            <textarea name="message" rows="3"
                                placeholder="عرّف بنفسك واشرح كيف ستنفّذ المشروع...">{{ old('message') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary service-order-btn">
                            إرسال العرض
                        </button>
                    </form>
                    @else
                    <p class="service-block-text">
                        لتقديم عرض على المشروع، يجب أن يكون نوع حسابك كمزود خدمة.
                    </p>
                    @endif
                    @else
                    <p class="service-block-text">
                        لتقديم عرض على المشروع، قم بتسجيل الدخول أولاً.
                    </p>
                    <a href="{{ route('login') ?? '#' }}" class="btn btn-primary service-order-btn">
                        تسجيل الدخول
                    </a>
                    @endauth
                </div>
            </aside>
        </div>

    </div>
</section>
@endsection