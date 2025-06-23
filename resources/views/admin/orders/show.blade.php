@extends('admin.layout')

@section('content')
@section('title', 'Chi tiết đơn hàng')
@can('manage-orders')

{{-- Thông báo thành công / lỗi --}}

{{-- Thông báo thành công --}}
<h1 class="text-2xl font-bold mb-4">Chi tiết đơn hàng #{{ $order->id }}</h1>

<div class="mb-4 space-y-1">
    <p><strong>Khách hàng:</strong> {{ $order->name }} ({{ $order->email }})</p>
    <p><strong>Điện thoại:</strong> {{ $order->phone }}</p>
    <p><strong>Địa chỉ:</strong> {{ $order->address }}</p>
    <p><strong>Phương thức thanh toán:</strong> {{ $order->payment_method === 'cod' ? 'COD' : 'Chuyển khoản' }}</p>
    <p><strong>Thời gian đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
</div>

<table class="w-full text-sm border mb-6">
    <thead>
        <tr class="bg-gray-100">
            <th class="p-2 border">Sản phẩm</th>
            <th class="p-2 border">SL</th>
            <th class="p-2 border">Đơn giá</th>
            <th class="p-2 border">Thành tiền</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($order->items as $item)
        <tr>
            <td class="p-2 border">{{ $item->product->name }}</td>
            <td class="p-2 border">{{ $item->quantity }}</td>
            <td class="p-2 border">{{ number_format($item->price) }} đ</td>
            <td class="p-2 border">{{ number_format($item->price * $item->quantity) }} đ</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="text-right text-lg font-bold mb-6">
    Tổng: {{ number_format($order->total_price) }} đ
</div>

{{-- Form đổi trạng thái --}}
<form method="POST" action="{{ route('admin.orders.updateStatus', $order->id) }}" class="max-w-sm space-y-3">
    @csrf
    @method('PATCH')

    <label class="font-medium">Trạng thái đơn hàng</label>
    <select name="status" class="border px-3 py-2 rounded w-full">
        <option value="pending" @selected($order->status === 'pending')>Đang chờ xử lý</option>
        <option value="completed" @selected($order->status === 'completed')>Đã xử lý</option>
        <option value="cancelled" @selected($order->status === 'cancelled')>Đã hủy</option>
    </select>

    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        Cập nhật trạng thái
    </button>
</form>
@else
    @php abort(403); @endphp
@endcan
@endsection
