@extends('pages.cart.cart')

@section('title', 'Đơn hàng của tôi')

@section('content')
    <h2 class="text-2xl font-bold mb-6">Lịch sử đơn hàng</h2>

    @forelse($orders as $order)
        <div class="bg-white p-4 shadow rounded mb-4">
            <div class="flex justify-between">
                <div>
                    <p><strong>Mã đơn:</strong> #{{ $order->id }}</p>
                    <p><strong>Tổng tiền:</strong> {{ number_format($order->total_price) }} đ</p>
                    <p><strong>Phương thức:</strong> {{ $order->payment_method === 'cod' ? 'COD' : 'Chuyển khoản' }}</p>
                    <p><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Trạng thái</strong> {{ $order->status === 'pending' ? 'Đang chờ xử lý' : ($order->status === 'cancelled' ? 'Đã hủy' : 'Đã xử lý') }}</p>
                </div>
                <div>
                    <a href="{{ route('orders.show', $order) }}" class="text-blue-500 hover:underline">Chi tiết</a>
                </div>
            </div>
        </div>
    @empty
        <p class="text-gray-500 italic">Bạn chưa đặt đơn hàng nào.</p>
    @endforelse

    <div class="mt-4">
        {{ $orders->links() }}
    </div>
@endsection
