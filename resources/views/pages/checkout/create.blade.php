@extends('pages.cart.cart')

@section('title', 'Thanh toán')

@section('content')
<h1 class="text-2xl font-bold mb-4">Thông tin thanh toán</h1>

<form action="{{ route('checkout.store') }}" method="POST" class="space-y-4">
    @csrf
    <div>
        <label for="name">Họ tên:</label>
        <input type="text" name="name" id="name" required class="w-full border p-2 rounded" value="{{ auth()->user()->name }}">
    </div>

    <div>
        <label for="email">Email:</label>
        <input type="email" name="email" required class="input-class" placeholder="Email của bạn">
    </div>

    <div>
        <label for="phone">Số điện thoại:</label>
        <input type="text" name="phone" id="phone" required class="w-full border p-2 rounded">
    </div>

    <div>
        <label for="address">Địa chỉ:</label>
        <textarea name="address" id="address" required class="w-full border p-2 rounded"></textarea>
    </div>

    <div>
        <label>Hình thức thanh toán:</label>
        <select name="payment_method" required class="w-full border p-2 rounded">
            <option value="cod">Thanh toán khi nhận hàng (COD)</option>
            <option value="bank_transfer">Chuyển khoản</option>
        </select>
    </div>

    <div class="text-right">
        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">Xác nhận đặt hàng</button>
    </div>
</form>
@endsection
