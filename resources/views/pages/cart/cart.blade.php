<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Giỏ hàng')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        window.userLoggedIn = @json(auth()->check());
    </script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>
<body class="bg-gray-100 text-gray-800 min-h-screen flex flex-col">

    {{-- Header --}}
    @include('layouts.partials.header')

    {{-- Nội dung chính --}}
    <main class="flex-grow container mx-auto px-4 py-6">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('layouts.partials.footer')

</body>
</html>
