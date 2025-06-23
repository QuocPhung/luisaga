@extends('layouts.app')

@section('title', 'Trang chủ')

@section('content')
@if ($discountedProducts->count())
<section class="mb-8" x-data="{ showAll: false }">
    <h2 class="text-xl font-bold mb-4 text-blue-600">Sản phẩm khuyến mãi</h2>
    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach ($discountedProducts as $index => $product)
            <div 
                class="bg-white shadow rounded p-4 hover:shadow-lg transition" 
                x-show="showAll || {{ $index }} < 4"
                x-transition
            >
                <img src="{{ asset('storage/' . ($product->thumbnail->image ?? 'default.jpg')) }}" 
                     alt="{{ $product->name }}" 
                     class="w-full h-40 object-cover rounded mb-2">

                <h3 class="font-semibold text-gray-800">{{ $product->name }}</h3>

                @if ($product->final_price < $product->price)
                    <p class="text-sm text-gray-600 line-through">
                        {{ number_format($product->price, 0, ',', '.') }}đ
                    </p>
                    <p class="text-red-500 font-bold text-lg">
                        {{ number_format($product->final_price, 0, ',', '.') }}đ
                    </p>
                    <p class="text-sm text-green-600 italic">
                        {{ $product->sale_name }} – {{ $product->sale_status_label }}
                    </p>
                @else
                    <p class="text-gray-800 font-bold text-lg">
                        {{ number_format($product->price, 0, ',', '.') }}đ
                    </p>
                @endif
                    <button 
                        onclick="cart.addToCart({
                            id: {{ $product->id }},
                            name: '{{ $product->name }}',
                            price: {{ $product->price }},
                            final_price: {{ $product->final_price }},
                            image: '{{ asset('storage/' . ($product->thumbnail->image ?? 'default.jpg')) }}'
                        })"
                        class="mt-2 bg-green-600 hover:bg-green-700 text-white px-4 py-1 rounded text-sm"
                    >
                        Thêm vào giỏ
                    </button>
                <a href="{{ route('product.show', $product->id) }}"
                   class="mt-2 inline-block bg-blue-600 text-white px-4 py-1 rounded text-sm">Xem chi tiết</a>
            </div>
        @endforeach
    </div>

    @if ($discountedProducts->count() > 4)
        <div class="text-center mt-4">
            <a href="{{ route('product.sale') }}" class="text-blue-600 font-semibold hover:underline">
                Xem thêm →
            </a>
        </div>
    @endif
</section>
@endif

@if ($categoriesWithProducts->count())
    @foreach ($categoriesWithProducts as $category)
        <section class="mb-10">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-blue-600">{{ $category->name }}</h2>
                <a href="{{ route('category.show', $category->id) }}" class="text-sm text-blue-500 hover:underline">
                    Xem tất cả →
                </a>
            </div>

            @if ($category->allProducts->count())
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach ($category->allProducts as $product)
                        <div class="bg-white shadow rounded p-4 hover:shadow-lg transition">
                            <img src="{{ asset('storage/' . ($product->thumbnail->image ?? 'default.jpg')) }}"
                                 alt="{{ $product->name }}"
                                 class="w-full h-40 object-cover rounded mb-2">

                            <h3 class="font-semibold text-gray-800 truncate">{{ $product->name }}</h3>

                            @if ($product->final_price < $product->price)
                                <p class="text-sm text-gray-600 line-through">
                                    {{ number_format($product->price, 0, ',', '.') }}đ
                                </p>
                                <p class="text-red-500 font-bold text-lg">
                                    {{ number_format($product->final_price, 0, ',', '.') }}đ
                                </p>
                            @else
                                <p class="text-gray-800 font-bold text-lg">
                                    {{ number_format($product->price, 0, ',', '.') }}đ
                                </p>
                            @endif
                            <button 
                                onclick="cart.addToCart({
                                    id: {{ $product->id }},
                                    name: '{{ $product->name }}',
                                    price: {{ $product->price }},
                                    final_price: {{ $product->final_price }},
                                    image: '{{ asset('storage/' . ($product->thumbnail->image ?? 'default.jpg')) }}'
                                })"
                                class="mt-2 bg-green-600 hover:bg-green-700 text-white px-4 py-1 rounded text-sm"
                            >
                                Thêm vào giỏ
                            </button>

                            <a href="{{ route('product.show', $product->id) }}"
                               class="mt-2 inline-block bg-blue-600 text-white px-4 py-1 rounded text-sm">Xem chi tiết</a>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 italic">Chưa có sản phẩm trong danh mục này.</p>
            @endif
        </section>
    @endforeach
@endif

@endsection
