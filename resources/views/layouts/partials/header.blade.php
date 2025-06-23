<header class="bg-white shadow px-4 py-3 flex items-center justify-between">
    {{-- Logo --}}
    <a href="{{ route('home') }}" class="text-2xl font-extrabold text-blue-600 tracking-wide">
        <span class="text-gray-800">🖥️</span> PCStore
    </a>

    {{-- Navigation --}}
    <nav class="space-x-6 hidden md:flex">
        <a href="/" class="text-gray-600 hover:text-blue-600 font-medium">Trang chủ</a>
        <a href="/products" class="text-gray-600 hover:text-blue-600 font-medium">Sản phẩm</a>
        <a href="#" class="text-gray-600 hover:text-blue-600 font-medium">Liên hệ</a>
    </nav>

    {{-- Actions --}}
    <div class="flex items-center space-x-4">
        {{-- Giỏ hàng --}}
        <a href="{{ route('cart.index') }}" class="relative">
            🛒
            <span id="cart-count" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full">0</span>
        </a>

        {{-- Auth --}}
        @guest
            <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600 font-medium">Đăng nhập</a>
            <a href="{{ route('register') }}" class="text-gray-600 hover:text-blue-600 font-medium">Đăng ký</a>
        @else
            <div class="flex items-center space-x-2">
                {{-- Avatar --}}
                <img src="{{ Auth::user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}" 
                     class="w-8 h-8 rounded-full border shadow" alt="Avatar">

                {{-- Tên --}}
                <span class="text-gray-700 font-medium">{{ Auth::user()->name }}</span>

                {{-- Logout --}}
                <form action="{{ route('logout') }}" method="POST" class="ml-2">
                    @csrf
                    <button type="submit" class="text-sm text-red-500 hover:underline">Đăng xuất</button>
                </form>
            </div>
        @endguest
    </div>
</header>
