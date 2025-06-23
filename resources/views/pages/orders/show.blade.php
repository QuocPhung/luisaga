@extends('pages.cart.cart')

@section('title', 'Chi tiết đơn hàng')

@section('content')
    <a href=""></a>
    <h2 class="text-2xl font-bold mb-6">Chi tiết đơn hàng #{{ $order->id }}</h2>

    <div class="mb-4">
        <p><strong>Họ tên:</strong> {{ $order->name }}</p>
        <p><strong>SĐT:</strong> {{ $order->phone }}</p>
        <p><strong>Địa chỉ:</strong> {{ $order->address }}</p>
        <p><strong>Phương thức thanh toán:</strong> {{ $order->payment_method === 'cod' ? 'COD' : 'Chuyển khoản' }}</p>
        <p><strong>Thời gian:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
    </div>

    <table class="w-full text-sm border">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-2 border">Sản phẩm</th>
                <th class="p-2 border">Số lượng</th>
                <th class="p-2 border">Đơn giá</th>
                <th class="p-2 border">Thành tiền</th>
                <th class="p-2 border">Trạng thái</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td class="p-2 border">{{ $item->product->name }}</td>
                    <td class="p-2 border">{{ $item->quantity }}</td>
                    <td class="p-2 border">{{ number_format($item->price) }} đ</td>
                    <td class="p-2 border">{{ number_format($item->price * $item->quantity) }} đ</td>
                    @php
                        $statusColor = match($order->status) {
                            'pending' => 'bg-yellow-300 text-yellow-900',
                            'cancelled' => 'bg-red-400 text-white',
                            'completed' => 'bg-green-400 text-white',
                            default => 'bg-gray-200 text-gray-800',
                        };

                        $statusLabel = match($order->status) {
                            'pending' => 'Đang chờ xử lý',
                            'cancelled' => 'Đã hủy',
                            'completed' => 'Đã xử lý',
                            default => 'Không rõ',
                        };
                    @endphp

<td class="p-2 border rounded {{ $statusColor }}">
    {{ $statusLabel }}
</td>
                </tr>
            @endforeach
        </tbody>

    </table>

    @if ($order->status === 'pending')
        <div class="mt-4 flex gap-4">
            {{-- Nút hủy --}}
            <form action="{{ route('orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn này?')">
                @csrf
                @method('PATCH')
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                    Hủy đơn hàng
                </button>
            </form>

            {{-- Nút sửa --}}
            <a href="{{ route('orders.edit', $order->id) }}" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
                Sửa thông tin
            </a>
        </div>
    @endif

@endsection
