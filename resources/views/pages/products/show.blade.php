@extends('pages.products.app')

@section('title', $product->name)

@section('content')
<div class="bg-white shadow rounded p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">{{ $product->name }}</h1>

    <img src="{{ asset('storage/' . ($product->thumbnail->image ?? 'default.jpg')) }}" 
         alt="{{ $product->name }}" 
         class="w-full max-w-md mx-auto mb-4 rounded shadow">

    <p class="text-gray-600 mb-2">
        Giá gốc: <span class="line-through">{{ number_format($product->price, 0, ',', '.') }}đ</span>
    </p>

    <p class="text-red-600 text-xl font-semibold mb-4">
        Giá khuyến mãi: {{ number_format($product->final_price ?? $product->price, 0, ',', '.') }}đ
    </p>
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
    <p class="text-gray-700">
        {!! nl2br(e($product->description ?? 'Không có mô tả')) !!}
    </p>


</div>
@endsection
