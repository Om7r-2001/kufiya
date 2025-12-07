<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>{{ $title ?? 'منصة كوفية - تسجيل الدخول' }}</title>

    <!-- الخط -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- ملف التنسيقات -->
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>

<body>

    @yield('content')

</body>

</html>