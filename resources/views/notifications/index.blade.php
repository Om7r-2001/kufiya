@extends('layouts.app')

@section('title', 'الإشعارات')

@section('content')
<section class="dashboard-section">
    <div class="container">
        <h1 class="page-title">الإشعارات</h1>
        <p class="page-subtitle">هنا تجد جميع الإشعارات الخاصة بحسابك.</p>

        @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
        @endif

        <div class="dash-block" style="margin-top:16px;">
            <div class="dash-block-header">
                <div class="dash-block-title">الإشعارات</div>
                @if($notifications->where('is_read', false)->count() > 0)
                <form method="POST" action="{{ route('notifications.readAll') }}">
                    @csrf
                    <button class="btn btn-outline" type="submit">
                        تحديد الكل كمقروء
                    </button>
                </form>
                @endif
            </div>

            @forelse($notifications as $notification)
            <div class="notif-list-item {{ $notification->is_read ? 'is-read' : 'is-unread' }}">
                <div>
                    <div class="notif-item-title">{{ $notification->title }}</div>
                    @if($notification->body)
                    <div class="notif-item-body">{{ $notification->body }}</div>
                    @endif
                    <div class="notif-item-time">
                        {{ $notification->created_at->format('Y-m-d H:i') }}
                        • {{ $notification->created_at->diffForHumans() }}
                    </div>
                </div>
                <div>
                    @if(!$notification->is_read)
                    <form method="POST" action="{{ route('notifications.read', $notification) }}">
                        @csrf
                        <button class="link-button small" type="submit">
                            تعليم كمقروء
                        </button>
                    </form>
                    @endif
                    @if($notification->link)
                    <a href="{{ $notification->link }}" class="link-button small">
                        فتح
                    </a>
                    @endif
                </div>
            </div>
            @empty
            <p class="empty-state-text">لا توجد إشعارات حتى الآن.</p>
            @endforelse

            <div style="margin-top: 12px;">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
</section>
@endsection