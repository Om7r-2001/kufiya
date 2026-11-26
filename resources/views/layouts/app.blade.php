<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>@yield('title', 'منصة كوفية')</title>

    <!-- الخط -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- ملف التنسيقات -->
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>

<body>

    <header>
        <div class="container">
            <nav class="navbar">

                {{-- الشعار --}}
                <div class="logo">
                    <img src="{{ asset('logo.png') }}" alt="شعار منصة كوفية" class="logo-image">
                    <div class="logo-info">
                        <div class="logo-text"> كوفية</div>
                        <div class="logo-sub"> منصة عربية بروح فلسطينية </div>
                    </div>
                </div>

                {{-- روابط الهيدر في الشاشات الكبيرة --}}
                <div class="nav-links">
                    <a href="{{ route('home') }}"
                        class="{{ request()->routeIs('home') ? 'active-link' : '' }}">الرئيسية</a>
                    <a href="{{ route('services.index') }}"
                        class="{{ request()->routeIs('services.*') ? 'active-link' : '' }}">الخدمات</a>
                    <a href="{{ route('providers.index') }}"
                        class="{{ request()->routeIs('providers.*') ? 'active-link' : '' }}">مزودي الخدمات</a>
                    <a href="{{ route('projects.index') }}"
                        class="{{ request()->routeIs('projects.*') ? 'active-link' : '' }}"> المشاريع </a>
                </div>

                <div class="avatar-noti">
                    <!-- لوحة الاشعارات -->
                    @auth
                    @php
                    $unreadCount = auth()->user()->unreadNotifications()->count();
                    $latestNotifications = auth()->user()->unreadNotifications()->take(5)->get();
                    @endphp

                    <div class="nav-notifications">
                        <div class="notif-dropdown">
                            <button type="button" class="notif-button" id="notifToggle">
                                🔔
                                @if($unreadCount > 0)
                                <span class="notif-badge">{{ $unreadCount }}</span>
                                @endif
                            </button>

                            <div class="notif-menu">
                                <div class="notif-menu-header">
                                    <span>الإشعارات</span>
                                    @if($unreadCount > 0)
                                    <form action="{{ route('notifications.readAll') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="notif-mark-all">تحديد الكل كمقروء</button>
                                    </form>
                                    @endif
                                </div>

                                @forelse($latestNotifications as $notification)
                                <form action="{{ route('notifications.read', $notification) }}" method="POST"
                                    class="notif-item">
                                    @csrf
                                    <button type="submit" class="notif-item-btn">
                                        <div class="notif-item-title">{{ $notification->title }}</div>
                                        <div class="notif-item-body">
                                            {{ \Illuminate\Support\Str::limit($notification->body, 80) }}
                                        </div>
                                        <div class="notif-item-time">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </div>
                                    </button>
                                </form>
                                @empty
                                <div class="notif-empty">
                                    لا توجد إشعارات جديدة.
                                </div>
                                @endforelse

                                <div class="notif-menu-footer">
                                    <a href="{{ route('notifications.index') }}">عرض جميع الإشعارات</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endauth
                    {{-- أزرار/قائمة المستخدم في الشاشات الكبيرة --}}
                    <div class="nav-actions">
                        @auth
                        {{-- قائمة المستخدم التي أنشأناها سابقاً --}}
                        <div class="user-menu">
                            <button class="user-menu-trigger">
                                @if(auth()->user()->avatar)
                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="الصورة الشخصية"
                                    class="user-avatar"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <span class="user-initial" style="display: none;">
                                    {{ mb_substr(auth()->user()->name, 0, 1) }}
                                </span>
                                @else
                                <span class="user-initial">
                                    {{ mb_substr(auth()->user()->name, 0, 1) }}
                                </span>
                                @endif
                                <span class="user-name">{{ auth()->user()->name }}</span>
                            </button>

                            <div class="user-menu-dropdown">
                                <a href="{{ route('profile.index') }}" class="user-menu-item">الملف الشخصي</a>
                                <a href="{{ route('profile.settings') }}" class="user-menu-item">إعدادات الحساب</a>

                                @if(auth()->user()->role === 'buyer')
                                <a href="{{ route('dashboard.buyer') }}" class="user-menu-item">لوحة المشتري</a>
                                @elseif(auth()->user()->role === 'seller')
                                <a href="{{ route('dashboard.seller') }}" class="user-menu-item">لوحة البائع</a>
                                @elseif(auth()->user()->role === 'admin')
                                <a href="{{ route('admin.dashboard') }}" class="user-menu-item">لوحة المشرف</a>
                                @endif

                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button class="user-menu-item logout-btn">تسجيل الخروج</button>
                                </form>
                            </div>
                        </div>
                        @else
                        <a href="{{ route('auth') }}" class="btn btn-outline">تسجيل الدخول</a>
                        <a href="{{ route('auth') }}?tab=register" class="btn btn-primary">
                            إنشاء حساب
                        </a>
                        @endauth
                    </div>
                </div>

                {{-- زر قائمة الموبايل (الهامبرجر) --}}
                <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="فتح القائمة">
                    ☰
                </button>
            </nav>

            {{-- قائمة الموبايل التي تظهر عند الضغط على الزر --}}
            <div class="mobile-menu" id="mobileMenu">

                @auth
                <div class="mobile-user-box">
                    <div class="mobile-user-avatar">
                        @if(auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="الصورة الشخصية"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <span class="avatar-letter" style="display: none;">
                            {{ mb_substr(auth()->user()->name, 0, 1) }}
                        </span>
                        @else
                        <span class="avatar-letter">
                            {{ mb_substr(auth()->user()->name, 0, 1) }}
                        </span>
                        @endif
                    </div>

                    <div class="mobile-user-info">
                        <div class="mobile-user-name">{{ auth()->user()->name }}</div>
                        <div class="mobile-user-email">{{ auth()->user()->email }}</div>
                    </div>
                </div>

                <hr style="margin:12px 0; border-color:#eee;">
                @endauth

                <a href="{{ route('home') }}">الرئيسية</a>
                <a href="{{ route('services.index') }}">الخدمات</a>
                <a href="{{ route('providers.index') }}">مزودي الخدمات</a>
                <a href="{{ route('projects.index') }}"> المشاريع </a>

                @auth
                <hr style="margin:12px 0; border-color:#eee;">

                <a href="{{ route('profile.index') }}">الملف الشخصي</a>

                @if(auth()->user()->role === 'buyer')
                <a href="{{ route('dashboard.buyer') }}">لوحة المشتري</a>
                @elseif(auth()->user()->role === 'seller')
                <a href="{{ route('dashboard.seller') }}">لوحة البائع</a>
                @endif

                <form action="{{ route('logout') }}" method="POST" style="padding:10px 0;">
                    @csrf
                    <button type="submit" class="logout-btn" style="width: 100%; text-align:right;">
                        تسجيل الخروج
                    </button>
                </form>

                @else
                <div class="mobile-auth-buttons">
                    <a href="{{ route('auth') }}" class="btn btn-outline">تسجيل الدخول</a>
                    <a href="{{ route('auth') }}#register" class="btn btn-primary">إنشاء حساب</a>
                </div>
                @endauth

            </div>

        </div>
    </header>


    @yield('content')

    <footer>
        <div class="container footer-grid">
            <div>© 2025 منصة كوفية – جميع الحقوق محفوظة.</div>
            <div class="footer-links">
                <a href="{{ route('terms') }}" class="{{ request()->routeIs('terms') ? 'active-link' : '' }}"> الشروط
                    والأحكام </a>
                <a href="{{ route('privacy') }}" class="{{ request()->routeIs('privacy') ? 'active-link' : '' }}">سياسة
                    الخصوصية</a>
                <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active-link' : '' }}">عن
                    المنصة</a>
                <a href="{{ route('faq') }}"
                    class="{{ request()->routeIs('faq') ? 'active-link' : '' }}">الأسئلةالشائعة</a>
            </div>
        </div>
    </footer>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileToggle = document.getElementById('mobileMenuToggle');
        const mobileMenu = document.getElementById('mobileMenu');

        if (mobileToggle && mobileMenu) {
            mobileToggle.addEventListener('click', function() {
                mobileMenu.classList.toggle('open');
            });
        }
    });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropdown = document.querySelector('.notif-dropdown');
        const toggleBtn = document.getElementById('notifToggle');

        if (!dropdown || !toggleBtn) return;

        // فتح/إغلاق القائمة عند الضغط على الأيقونة
        toggleBtn.addEventListener('click', function(e) {
            e.stopPropagation(); // منع انتشار الحدث
            dropdown.classList.toggle('open');
        });

        // إغلاق القائمة عند الضغط خارجها
        document.addEventListener('click', function(e) {
            if (!dropdown.contains(e.target)) {
                dropdown.classList.remove('open');
            }
        });
    });
    </script>

</body>

</html>
