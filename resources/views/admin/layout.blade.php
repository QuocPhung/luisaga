<!-- resources/views/layouts/admin.blade.php -->
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Quản trị')</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <nav class="bg-blue-600 p-4 text-white flex justify-between">
        <div><a href="{{ route('admin.dashboard') }}">🛠 Luisaga Admin</a></div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="bg-red-500 px-3 py-1 rounded hover:bg-red-600">Đăng xuất</button>
        </form>
    </nav>

    <main class="p-6">
        @yield('content')
    </main>
    <x-head.tinymce-config />
    @yield('scripts')
</body>
</html>
