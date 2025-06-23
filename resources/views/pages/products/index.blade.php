@extends('pages.products.app')

@section('title', $selectedCategory->name ?? 'Tất cả sản phẩm')

@section('content')
    <h1 class="text-2xl font-bold mb-4 text-blue-600">
        {{ $selectedCategory->name ?? 'Tất cả sản phẩm' }}
    </h1>

    @if ($products->count())
        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach ($products as $product)
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

        <div class="mt-6">
            {{ $products->links() }}
        </div>
    @else
        <p class="text-gray-500 italic">Không có sản phẩm nào.</p>
    @endif
@endsection
