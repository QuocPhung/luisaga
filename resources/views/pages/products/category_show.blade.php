@extends('pages.products.app')

@section('title', $category->name)

@section('content')
<div class="container mx-auto py-6">

    <h1 class="text-2xl font-bold mb-6">{{ $category->name }}</h1>

    @if ($products->count())
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
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
            {{ $products->links() }} {{-- Phân trang --}}
        </div>

    @else
        <p>Chưa có sản phẩm trong danh mục này.</p>
    @endif

</div>
@endsection
