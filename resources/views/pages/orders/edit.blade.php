@extends('pages.cart.cart')

@section('title', 'Chỉnh sửa đơn hàng')

@section('content')
<h1 class="text-xl font-bold mb-6">Chỉnh sửa đơn hàng #{{ $order->id }}</h1>

<form method="POST" action="{{ route('orders.update', $order) }}" class="space-y-4">
    @csrf
    @method('PUT')

    <div>
        <label class="block font-semibold">Tên người nhận</label>
        <input type="text" name="name" value="{{ old('name', $order->name) }}" class="form-input w-full">
    </div>

    <div>
        <label class="block font-semibold">Email người nhận</label>
        <input type="text" name="email" value="{{ old('email', $order->email) }}" class="form-input w-full">
    </div>

    <div>
        <label class="block font-semibold">Số điện thoại</label>
        <input type="text" name="phone" value="{{ old('phone', $order->phone) }}" class="form-input w-full">
    </div>

    <div>
        <label class="block font-semibold">Địa chỉ</label>
        <textarea name="address" class="form-textarea w-full">{{ old('address', $order->address) }}</textarea>
    </div>

    <div>
        <label class="block font-semibold">Phương thức thanh toán</label>
        <select name="payment_method" class="form-select w-full">
            <option value="cod" @selected(old('payment_method', $order->payment_method) === 'cod')>Thanh toán khi nhận hàng</option>
            <option value="bank" @selected(old('payment_method', $order->payment_method) === 'bank')>Chuyển khoản</option>
        </select>
    </div>

    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Cập nhật</button>
</form>
@endsection
