@extends('layouts.app')

@section('title', 'محادثة الطلب #' . $order->id . ' | منصة كوفية')

@section('content')
<main>
    <section class="orders-section">
        <div class="container">

            <div class="page-header">
                <div>
                    @php
                    $service = $order->service ?? null;
                    $project = $order->project ?? null;

                    // عنوان الطلب حسب المصدر
                    if ($service) {
                    $contextType = 'الخدمة';
                    $contextTitle = $service->title;
                    $contextLink = route('services.show', $service->slug);
                    } elseif ($project) {
                    $contextType = 'المشروع';
                    $contextTitle = $project->title;
                    $contextLink = route('projects.show', $project);
                    } else {
                    $contextType = 'الطلب';
                    $contextTitle = 'بدون عنوان محدد';
                    $contextLink = null;
                    }
                    @endphp
                    <h1 class="page-title">
                        محادثة طلب #{{ $order->id }}
                    </h1>

                    <p class="page-subtitle">
                        {{ $contextType }}: {{ $contextTitle }}

                        @if($contextLink)
                        • <a href="{{ $contextLink }}" class="section-link" target="_blank">
                            عرض صفحة {{ $contextType }}
                        </a>
                        @endif
                    </p>
                </div>
                <div class="breadcrumb">
                    <a href="{{ route('home') }}">الرئيسية</a>
                    <span>/</span>
                    <a href="{{ route('buyer.orders.index') }}">طلباتي</a>
                    <span>/</span>
                    <span>محادثة الطلب</span>
                </div>
            </div>

            <div class="service-details-grid">

                {{-- العمود الرئيسي: المحادثة + معلومات الطلب --}}
                <div class="service-main">

                    {{-- معلومات سريعة عن حالة الطلب --}}
                    <div class="chat-order-summary">
                        <div class="chat-order-status">
                            <span>الحالة الحالية للطلب:</span>
                            <strong>
                                @include('orders.partials.status_label', ['status' => $order->status])
                            </strong>
                        </div>

                        <div class="chat-order-meta">
                            <div>
                                <span class="meta-label">مزود الخدمة:</span>
                                <span class="meta-value">{{ $order->seller->name }}</span>
                            </div>
                            <div>
                                <span class="meta-label">المشتري:</span>
                                <span class="meta-value">{{ $order->buyer->name }}</span>
                            </div>
                            <div>
                                <span class="meta-label">سعر الطلب:</span>
                                <span class="meta-value">{{ number_format($order->price, 2) }} $</span>
                            </div>
                            <div>
                                <span class="meta-label">تاريخ الإنشاء:</span>
                                <span class="meta-value">{{ $order->created_at->format('Y-m-d') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- في حال كان الطلب ملغى أو مكتمل نعرض تنبيه أعلى المحادثة --}}
                    @if(in_array($order->status, ['cancelled','completed']))
                    <div class="chat-system-box" style="margin-bottom:12px; padding: 10px;">
                        @if($order->status === 'completed')
                        <strong>تم إكمال الطلب.</strong>
                        <p style="margin:6px 0 0; font-size:13px; color:#777;">
                            تم تأكيد استلام المشروع من قبل المشتري وتحويل المبلغ إلى مزود الخدمة.
                        </p>
                        @elseif($order->status === 'cancelled')
                        <strong>تم إلغاء الطلب.</strong>
                        <p style="margin:6px 0 0; font-size:13px; color:#777;">
                            {{ $order->cancel_reason ?? 'تم إلغاء الطلب.' }}
                        </p>
                        @endif
                    </div>
                    @endif

                    {{-- صندوق المحادثة --}}
                    <div class="chat-box">
                        @if($messages->count())
                        ...

                        @foreach($messages as $message)
                        @php
                        $isMine = $message->sender_id === auth()->id();
                        @endphp
                        <div class="chat-message-row {{ $isMine ? 'mine' : 'theirs' }}">
                            <div class="chat-message-bubble">
                                <div class="chat-message-header">
                                    <span class="chat-message-name">
                                        {{ $isMine ? 'أنا' : $message->sender->name }}
                                    </span>
                                    <span class="chat-message-time">
                                        {{ $message->created_at->format('H:i Y-m-d') }}
                                    </span>
                                </div>
                                <div class="chat-message-body">
                                    {{ $message->body }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @else
                        <p style="font-size:14px; color:var(--color-muted);">
                            لا توجد رسائل بعد في هذا الطلب. ابدأ المحادثة الآن لتوضيح التفاصيل.
                        </p>
                        @endif
                    </div>
                    @if(!in_array($order->status, ['cancelled','completed']))
                    <form class="chat-input-row" method="POST" action="{{ route('orders.chat.send', $order) }}">
                        @csrf
                        <textarea name="body" rows="2" placeholder="اكتب رسالتك هنا...">{{ old('body') }}</textarea>
                        <button type="submit" class="btn btn-primary">
                            إرسال
                        </button>
                    </form>

                    @error('body')
                    <div style="font-size:12px; color:#b00020; margin-top:4px;">
                        {{ $message }}
                    </div>
                    @enderror
                    @else
                    <div class="chat-system-box" style="margin-top:12px;">
                        <p style="margin:0; font-size:13px; color:#777;">
                            لا يمكن إرسال رسائل جديدة لأن الطلب في حالة منتهية (مكتمل أو ملغى).
                        </p>
                    </div>
                    @endif

                    {{-- نموذج تسليم الطلب من البائع --}}
                    @auth
                    @if(auth()->id() === $order->seller_id && in_array($order->status, ['in_progress']))
                    <div class="order-delivery-box"
                        style="margin-top: 16px; padding:12px; border-radius:10px; border:1px solid #eee;">
                        <h3 style="font-size:14px; margin-bottom:8px;">تسليم الطلب للمشتري</h3>

                        @if($errors->has('delivery') || $errors->has('delivery_file'))
                        <div
                            style="background:#ffe6e6; border-radius:8px; padding:6px 8px; font-size:12px; margin-bottom:8px;">
                            @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                            @endforeach
                        </div>
                        @endif

                        <form method="POST" action="{{ route('orders.deliver', $order) }}"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="form-group">
                                <label for="delivery_note" style="font-size:12px;">رسالة للمشتري (اختياري)</label>
                                <textarea id="delivery_note" name="delivery_note" rows="3"
                                    style="width:100%; border-radius:8px; border:1px solid #ddd; padding:6px 8px; font-size:13px;"
                                    placeholder="اكتب ملاحظات حول المشروع أو طريقة التشغيل إن لزم...">{{ old('delivery_note') }}</textarea>
                            </div>

                            <div class="form-group" style="margin-top:8px;">
                                <label for="delivery_file" style="font-size:12px;">ملف المشروع (مطلوب)</label>
                                <input type="file" id="delivery_file" name="delivery_file"
                                    style="display:block; margin-top:4px; font-size:12px;">
                            </div>
                            <div class="order-delivery-actions">
                                <button type="submit" class="btn btn-primary btn-sm" style="width: 100%">
                                    تسليم الطلب
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif
                    @endauth
                </div>

                {{-- العمود الجانبي: تفاصيل الطلب + أزرار الحالة --}}
                <aside class="service-sidebar">

                    @if(!in_array($order->status, ['cancelled']))
                    <div class="service-pricing-card">
                        <div class="service-price-top">
                            <div>
                                <span class="service-price-label">سعر الطلب</span>
                                <div class="service-price-main">{{ number_format($order->price, 2) }} $</div>
                                <div class="service-price-note">
                                    يتم احتجاز المبلغ في المنصة حتى تأكيد استلام الخدمة.
                                </div>
                            </div>
                        </div>

                        <div class="service-price-meta">
                            <div>
                                <div class="meta-label">مدة التسليم المتفق عليها</div>
                                <div class="meta-value">
                                    {{ $order->delivery_at?->format('Y-m-d') ?? 'لم تُحدد' }}
                                </div>
                            </div>
                            <div>
                                <div class="meta-label">حالة الطلب</div>
                                <div class="meta-value">
                                    @include('orders.partials.status_label', ['status' => $order->status])
                                </div>
                            </div>
                        </div>

                        {{-- أزرار حسب نوع المستخدم وحالة الطلب --}}
                        <div class="order-status-actions">
                            @auth
                            {{-- أزرار البائع --}}
                            @if(auth()->id() === $order->seller_id)
                            @if($order->status === 'pending')
                            <div class="order-status-actions-row">
                                <form method="POST" action="{{ route('orders.start', $order) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        بدء العمل
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('orders.reject', $order) }}"
                                    onsubmit="return confirm('هل أنت متأكد من رفض هذا الطلب؟ سيتم إبلاغ المشتري وإغلاق الطلب.');">
                                    @csrf
                                    <button type="submit" class="btn btn-outline btn-sm order-reject-button">
                                        رفض الطلب
                                    </button>
                                </form>
                            </div>
                            @endif
                            @endif

                            <!-- تعديل الملف -->
                            {{-- يظهر فقط للبائع، وقبل أن يؤكد المشتري الاستلام --}}
                            @if(auth()->id() === $order->seller_id && $order->status !== 'completed' &&
                            $order->delivery_file)
                            <div class="chat-system-box"
                                style="margin-top:16px; padding:12px; border-radius:12px; background:#f9fafb;">
                                <div style="font-size:13px; font-weight:600; margin-bottom:6px;">
                                    ملف المشروع الحالي
                                </div>
                                <div style="font-size:12px; margin-bottom:8px; color:#555;">
                                    يمكنك استبدال ملف المشروع طالما أن المشتري لم يؤكد استلام الطلب.
                                </div>

                                <form action="{{ route('orders.delivery.update', $order) }}" method="POST"
                                    enctype="multipart/form-data" style="margin-top:8px;">
                                    @csrf

                                    <div class="form-group" style="margin-bottom:8px;">
                                        <label style="font-size:13px; font-weight:600;">استبدال ملف المشروع</label>
                                        <input type="file" name="delivery_file" required
                                            style="font-size:12px; margin-top:4px;">
                                        <div style="font-size:11px; color:#777; margin-top:2px;">
                                            سيتم استبدال الملف السابق بهذا الملف.
                                        </div>
                                    </div>

                                    <div class="form-group" style="margin-bottom:8px;">
                                        <label style="font-size:13px; font-weight:600;">ملاحظات إضافية (اختياري)</label>
                                        <textarea name="delivery_note" rows="3"
                                            style="width:100%; font-size:12px;">{{ old('delivery_note', $order->delivery_note) }}</textarea>
                                    </div>

                                    <button type="submit" class="btn btn-primary"
                                        style="font-size:13px; padding:6px 14px;">
                                        تعديل ملف المشروع
                                    </button>
                                </form>
                            </div>
                            @endif

                            {{-- أزرار المشتري --}}
                            @if(auth()->id() === $order->buyer_id)

                            {{-- إلغاء الطلب قبل بدء العمل أو أثناء التنفيذ (حسب منطقك في الكنترولر) --}}
                            @if(in_array($order->status, ['pending','in_progress']))
                            <form method="POST" action="{{ route('orders.cancel', $order) }}"
                                onsubmit="return confirm('هل أنت متأكد من إلغاء الطلب؟');">
                                @csrf
                                <button type="submit" class="btn btn-outline btn-sm">
                                    إلغاء الطلب
                                </button>
                            </form>
                            @endif

                            {{-- تأكيد استلام الطلب بعد أن يكون في حالة "تم التسليم" --}}
                            @if($order->status === 'delivered')
                            <form method="POST" action="{{ route('orders.complete', $order) }}"
                                onsubmit="return confirm('بتأكيد الاستلام سيتم تحويل المبلغ إلى مزود الخدمة. هل أنت متأكد؟');">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm">
                                    تأكيد استلام الطلب
                                </button>
                            </form>
                            @endif

                            @endif
                            @endauth
                            @if($order->delivery_file)
                            <div class="order-delivery-info"
                                style="margin-top:12px; padding:10px; border-radius:10px; background:#E8FFF3;">
                                <div style="font-size:13px; font-weight:600; margin-bottom:4px;">
                                    تم تسليم هذا الطلب من قبل مزود الخدمة.
                                </div>
                                @if($order->delivery_note)
                                <div style="font-size:12px; margin-bottom:4px;">
                                    <strong>ملاحظة البائع:</strong> {{ $order->delivery_note }}
                                </div>
                                @endif
                                <a href="{{ asset('storage/'.$order->delivery_file) }}" class="btn btn-outline" download
                                    style="font-size:12px; padding:4px 10px;">
                                    تحميل ملف المشروع
                                </a>
                                @if($order->delivered_at)
                                <div style="font-size:11px; color:#555; margin-top:4px;">
                                    تاريخ التسليم: {{ $order->delivered_at->format('Y-m-d H:i') }}
                                </div>
                                @endif

                            </div>
                            @endif
                        </div>

                    </div>

                    @else

                    {{-- رسالة واضحة تفيد بأن الطلب ملغي --}}
                    <div class="chat-system-box" style="padding:14px; margin-bottom:12px;">
                        <strong>تم إلغاء الطلب.</strong>
                        <p style="margin:6px 0 0; font-size:13px; color:#777;">
                            {{ $order->cancel_reason ?? 'تم إلغاء الطلب من قبل أحد الطرفين.' }}
                        </p>
                    </div>

                    @endif

                    {{-- معلومات إضافية عن المشتري / البائع --}}
                    <div class="seller-card">
                        <div class="seller-header">
                            <div class="seller-avatar">
                                {{ mb_substr($order->buyer->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="seller-name">{{ $order->buyer->name }}</div>
                                <div class="seller-meta">
                                    <span>المشتري</span>
                                </div>
                            </div>
                        </div>

                        <div class="seller-info-row">
                            <span class="seller-info-label">تاريخ الانضمام:</span>
                            <span class="seller-info-value">
                                {{ $order->buyer->created_at->format('Y-m-d') }}
                            </span>
                        </div>
                    </div>

                    <div class="seller-card" style="margin-top:10px;">
                        <div class="seller-header">
                            <div class="seller-avatar">
                                {{ mb_substr($order->seller->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="seller-name">{{ $order->seller->name }}</div>
                                <div class="seller-meta">
                                    <span>مزود الخدمة</span>
                                </div>
                            </div>
                        </div>

                        <div class="seller-info-row">
                            <span class="seller-info-label">تاريخ الانضمام:</span>
                            <span class="seller-info-value">
                                {{ $order->seller->created_at->format('Y-m-d') }}
                            </span>
                        </div>
                    </div>

                    {{-- نموذج تقييم الخدمة بعد إكمال الطلب (للمشتري فقط) --}}
                    @if(auth()->id() === $order->buyer_id && $order->status === 'completed' && !$order->review)
                    <div class="service-faq-card" style="margin-top:12px;">
                        <h3 class="service-block-title small">قيّم تجربتك مع هذه الخدمة</h3>

                        <form method="POST" action="{{ route('orders.review.store', $order) }}">
                            @csrf

                            <div class="form-group">
                                <label for="rating">التقييم العام</label>
                                <select id="rating" name="rating">
                                    <option value="5">5 - ممتاز</option>
                                    <option value="4">4 - جيد جداً</option>
                                    <option value="3">3 - جيد</option>
                                    <option value="2">2 - مقبول</option>
                                    <option value="1">1 - ضعيف</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="comment">تعليقك (اختياري)</label>
                                <textarea id="comment" name="comment" rows="3"
                                    placeholder="اكتب رأيك في الخدمة وجودة التنفيذ...">{{ old('comment') }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-outline btn-sm">
                                حفظ التقييم
                            </button>
                        </form>
                    </div>
                    @endif

                </aside>

            </div>

        </div>
    </section>
</main>
@endsection