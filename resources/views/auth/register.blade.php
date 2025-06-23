<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">
    @include('layouts.partials.header')
    {{-- SweetAlert2 thông báo --}}
    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Thành công!',
                text: '{{ session('success') }}',
                toast: true,
                position: 'top-end',
                timer: 2500,
                showConfirmButton: false
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Lỗi!',
                text: '{{ session('error') }}',
                toast: true,
                position: 'top-end',
                timer: 2500,
                showConfirmButton: false
            });
        </script>
    @endif

    <main class="flex justify-center items-center min-h-screen">
        <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
            <h2 class="text-2xl font-semibold text-center text-gray-700 mb-6">Đăng ký tài khoản</h2>

            {{-- Hiển thị lỗi validate tổng quát --}}
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 text-sm">
                    <strong>Lỗi:</strong>
                    <ul class="list-disc pl-5 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register.submit') }}" method="POST" class="space-y-4">
                @csrf

                {{-- Họ tên --}}
                <div>
                    <input type="text" name="name" placeholder="Họ và tên"
                        class="w-full border p-2 rounded focus:outline-none focus:ring focus:border-blue-300">
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <input type="email" name="email" placeholder="Email"
                        class="w-full border p-2 rounded focus:outline-none focus:ring focus:border-blue-300">
                    @error('email')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Mật khẩu --}}
                <div class="relative">
                    <input type="password" name="password" id="password"
                        placeholder="Mật khẩu"
                        class="w-full border p-2 rounded pr-10 focus:outline-none focus:ring focus:border-blue-300">
                    <span onclick="toggleVisibility('password', 'eyeIcon1')" class="absolute right-3 top-1/2 transform -translate-y-1/2 cursor-pointer text-gray-500">
                        <i id="eyeIcon1" class="fa-solid fa-eye"></i>
                    </span>
                    @error('password')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Xác nhận mật khẩu --}}
                <div class="relative">
                    <input type="password" name="confirm_password" id="confirm_password"
                        placeholder="Nhập lại mật khẩu"
                        class="w-full border p-2 rounded pr-10 focus:outline-none focus:ring focus:border-blue-300">
                    <span onclick="toggleVisibility('confirm_password', 'eyeIcon2')" class="absolute right-3 top-1/2 transform -translate-y-1/2 cursor-pointer text-gray-500">
                        <i id="eyeIcon2" class="fa-solid fa-eye"></i>
                    </span>
                    @error('confirm_password')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
                    Đăng ký
                </button>

                <p class="text-center text-sm text-gray-600 mt-4">
                    Đã có tài khoản? <a href="{{ route('login') }}" class="text-blue-500 hover:underline">Đăng nhập</a>
                </p>
            </form>
        </div>
    </main>
    @include('layouts.partials.header')
    <script>
        function toggleVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash", "text-blue-600");
            } else {
                input.type = "password";
                icon.classList.add("fa-eye");
                icon.classList.remove("fa-eye-slash", "text-blue-600");
            }
        }
    </script>
</body>
</html>
