<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'PC Store') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>
<body class="bg-gray-50 text-gray-800">
    {{-- Header --}}
    @include('layouts.partials.header')

    <div class="min-h-screen px-4 py-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        {{-- Sidebar trái --}}
        <aside class="md:col-span-1 hidden md:block">
            @include('layouts.partials.sidebar')
        </aside>

        {{-- Slider + Banner phải --}}
        <div class="md:col-span-3 grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Slider: 2/3 --}}
            <div class="md:col-span-2">
                @include('layouts.partials.banner-main')
            </div>

            {{-- Banner phải: hiển thị 2–3 cái đầu --}}
            <div class="md:col-span-1 space-y-4">
                @foreach ($banners->skip(0)->take(3) as $banner)
                    <a href="{{ $banner->link ?? '#' }}">
                        <img src="{{ asset('storage/' . $banner->image) }}"
                            alt="{{ $banner->title }}"
                            class="w-full h-32 object-cover rounded shadow">
                    </a>
                @endforeach
            </div>
        </div>
        
        {{-- Banner dư: full chiều ngang --}}
        @if ($banners->count() > 3)
            <div class="md:col-span-4 mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                @foreach ($banners->skip(3) as $banner)
                    <a href="{{ $banner->link ?? '#' }}" class="block">
                        <img src="{{ asset('storage/' . $banner->image) }}"
                            alt="{{ $banner->title }}"
                            class="w-full h-32 object-cover rounded shadow">
                    </a>
                @endforeach
            </div>
        @endif
    </div>
    {{-- Nội dung chính --}}
    <main class="p-4">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('layouts.partials.footer')
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
