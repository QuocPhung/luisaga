<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sản phẩm')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>
<body class="bg-gray-50 text-gray-800">
    
    {{-- Header --}}
    @include('layouts.partials.header')

    {{-- Nội dung chính --}}
    <div class="min-h-screen grid grid-cols-1 md:grid-cols-4 gap-4 p-4">
        {{-- Sidebar --}}
        <aside class="md:col-span-1 hidden md:block">
            @include('layouts.partials.sidebar') {{-- Đây là menu danh mục --}}
        </aside>

        {{-- Content --}}
        <main class="md:col-span-3">
            {{-- Filter chọn danh mục --}}
            <div class="flex justify-between items-center mb-4">
                <form method="GET" action="{{ route('product.index') }}">
                    <select name="category_id"
                            onchange="this.form.submit()"
                            class="border rounded px-3 py-1 text-sm focus:outline-none focus:ring focus:border-blue-300">
                        <option value="">-- Tất cả sản phẩm --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                            @foreach ($cat->childrenRecursive as $child)
                                <option value="{{ $child->id }}" {{ request('category_id') == $child->id ? 'selected' : '' }}>
                                    &nbsp;&nbsp;↳ {{ $child->name }}
                                </option>
                            @endforeach
                        @endforeach
                    </select>
                </form>

                {{-- Nút xem tất cả --}}
                @if (request()->has('category_id'))
                    <a href="{{ route('product.index') }}" class="text-sm text-blue-600 underline">
                        ← Xem tất cả sản phẩm
                    </a>
                @endif
            </div>

            {{-- Nội dung cụ thể --}}
            @yield('content')
        </main>

    </div>
    @if(auth()->check())
    <script>
        // Gắn cờ login toàn cục
        window.userLoggedIn = @json(Auth::check());

        // Cập nhật đếm số lượng hiển thị
        async function updateCartCount() {
            const el = document.querySelector('#cart-count');
            if (!el) return;

            if (window.userLoggedIn) {
                try {
                    const res = await fetch('/cart/count', {
                        headers: { 'Accept': 'application/json' },
                        credentials: 'same-origin'
                    });
                    const data = await res.json();
                    el.textContent = data.count;
                } catch (e) {
                    console.error('Lỗi lấy số lượng:', e);
                    el.textContent = '0';
                }
            } else {
                const cart = JSON.parse(localStorage.getItem('cart') || '[]');
                const total = cart.reduce((sum, item) => sum + item.quantity, 0);
                el.textContent = total;
            }
        }

        // Khi load trang lần đầu
        document.addEventListener('DOMContentLoaded', async () => {
            await updateCartCount();

            // Nếu user đã login và localStorage có giỏ → sync vào DB
            if (window.userLoggedIn) {
                const localCart = JSON.parse(localStorage.getItem('cart') || '[]');
                if (localCart.length > 0) {
                    try {
                        await fetch('/cart/sync', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                items: localCart.map(item => ({
                                    product_id: item.id,
                                    quantity: item.quantity
                                }))
                            })
                        });

                        localStorage.removeItem('cart');
                        await updateCartCount();
                    } catch (err) {
                        console.error('Lỗi đồng bộ giỏ hàng:', err);
                    }
                }
            }
        });

        // Gọi ở chỗ khác khi thêm vào giỏ hàng
        async function addToCart(productId, quantity = 1) {
            if (!productId) return;

            if (window.userLoggedIn) {
                await fetch('/api/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ product_id: productId, quantity })
                });
            } else {
                let cart = JSON.parse(localStorage.getItem('cart') || '[]');
                let existing = cart.find(item => item.id === productId);
                if (existing) {
                    existing.quantity += quantity;
                } else {
                    cart.push({ id: productId, quantity });
                }
                localStorage.setItem('cart', JSON.stringify(cart));
            }

            await updateCartCount();
        }

    </script>

    @endif
</body>
</html>
