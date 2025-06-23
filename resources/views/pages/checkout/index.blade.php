@extends('pages.cart.cart')

@section('title', 'Thanh toán')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow mt-6">
    <h2 class="text-2xl font-bold mb-6 text-blue-600">Thông tin thanh toán</h2>

    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-4 rounded mb-4">{{ session('error') }}</div>
    @endif

    <form action="{{ route('checkout.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @csrf
        <div class="col-span-1">
            <label class="block text-sm font-medium">Họ tên</label>
            <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                   class="w-full border rounded p-2 mt-1" required>
        </div>

        <div class="col-span-1">
            <label class="block text-sm font-medium">Email</label>
            <input type="text" name="name" value="{{ old('email', auth()->user()->email) }}"
                   class="w-full border rounded p-2 mt-1" required>
        </div>

        <div class="col-span-1">
            <label class="block text-sm font-medium">Số điện thoại</label>
            <input type="text" name="phone" value="{{ old('phone') }}"
                   class="w-full border rounded p-2 mt-1" required>
        </div>

        <div class="col-span-2">
            <label class="block text-sm font-medium">Địa chỉ giao hàng</label>
            <textarea name="address" rows="3" class="w-full border rounded p-2 mt-1" required>{{ old('address') }}</textarea>
        </div>

        <div class="col-span-2">
            <label class="block text-sm font-medium">Phương thức thanh toán</label>
            <div class="flex items-center gap-4 mt-1">
                <label class="flex items-center gap-2">
                    <input type="radio" name="payment_method" value="cod" checked>
                    <span>Thanh toán khi nhận hàng (COD)</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="radio" name="payment_method" value="bank_transfer">
                    <span>Chuyển khoản ngân hàng</span>
                </label>
            </div>
        </div>

        <div class="col-span-2 mt-6 text-right">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Đặt hàng</button>
        </div>
    </form>

    <hr class="my-6">

    <h3 class="text-xl font-semibold mb-4">Tóm tắt đơn hàng</h3>
    <div class="space-y-4">
        @foreach ($cart->items as $item)
            <div class="flex items-center justify-between border-b pb-2">
                <div>
                    <p class="font-medium">{{ $item->product->name }}</p>
                    <p class="text-sm text-gray-500">x{{ $item->quantity }}</p>
                </div>
                <div class="text-right text-blue-700">
                    {{ number_format($item->product->final_price * $item->quantity, 0, ',', '.') }} đ
                </div>
            </div>
        @endforeach
        <div class="text-right font-bold text-lg text-green-700">
            Tổng cộng: {{ number_format($cart->items->sum(fn($item) => $item->product->final_price * $item->quantity), 0, ',', '.') }} đ
        </div>
    </div>
</div>
@endsection
