<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100 font-[Quicksand]">
    @include('layouts.partials.header')
    <div class="flex justify-center items-center min-h-screen">
        <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-md">
            <h2 class="text-2xl font-semibold text-center mb-6">Đăng nhập tài khoản</h2>

            {{-- SweetAlert2 --}}
            @if(session('success'))
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công',
                        text: '{{ session('success') }}',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000
                    });
                </script>
            @endif

            @if(session('error'))
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Thất bại',
                        text: '{{ session('error') }}',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000
                    });
                </script>
            @endif

            {{-- Lỗi validate toàn cục --}}
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

            <form id="login-form" action="{{ route('login.submit') }}" method="POST" class="space-y-4">
                @csrf

                <input type="email" name="email" placeholder="Email"
                       required class="w-full border p-2 rounded">

                <div class="relative">
                    <input type="password" id="password" name="password" placeholder="Mật khẩu"
                           required class="w-full border p-2 rounded pr-10">
                    <span onclick="togglePassword()" class="absolute right-3 top-1/2 transform -translate-y-1/2 cursor-pointer text-gray-500">
                        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 
                                       9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </span>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="remember" class="border-gray-300 rounded">
                    <label for="remember">Ghi nhớ đăng nhập</label>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    Đăng nhập
                </button>

                <p class="text-center text-sm text-gray-600 mt-4">
                    Chưa có tài khoản? <a href="{{ route('register') }}" class="text-blue-500 hover:underline">Đăng ký</a>
                </p>
            </form>
        </div>
    </div>
    @include('layouts.partials.footer')
    {{-- Hiển thị/ẩn mật khẩu --}}
    <script>
            document.querySelector('#login-form').addEventListener('submit', function (e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);
            const cartItems = JSON.parse(localStorage.getItem('cart') || '[]');

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    email: formData.get('email'),
                    password: formData.get('password'),
                    cart_items: cartItems.map(item => ({
                        product_id: item.id,
                        quantity: item.quantity
                    }))
                }),
            })
            .then(res => {
                if (!res.ok) throw new Error('Sai thông tin đăng nhập');
                return res.json();
            })
            .then(data => {
                localStorage.removeItem('cart');
                window.location.href = data.redirect || '/';
            })
            .catch(err => {
                alert(err.message);
            });
        });
        function togglePassword() {
            const input = document.getElementById("password");
            const icon = document.getElementById("eyeIcon");
            if (input.type === "password") {
                input.type = "text";
                icon.classList.add("text-blue-600");
            } else {
                input.type = "password";
                icon.classList.remove("text-blue-600");
            }
        }
    </script>
</body>
</html>
