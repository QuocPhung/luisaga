@extends('admin.layout')

@section('content')
@section('title', 'Quản lý đơn hàng')
@can('manage-orders')
<a href="{{ route('admin.dashboard') }}"
       class="bg-green-600 text-white px-4 py-2 rounded hover:bg-blue-700 mb-4 inline-block">
        Quay Lại
    </a>
<h1 class="text-2xl font-bold mb-4">Quản lý đơn hàng</h1>
@if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<form method="GET" class="mb-6 flex flex-wrap gap-4">
    <input type="text" name="name" placeholder="Tên" value="{{ request('name') }}" class="border px-2 py-1 rounded">
    <input type="text" name="email" placeholder="Email" value="{{ request('email') }}" class="border px-2 py-1 rounded">
    <select name="status" class="border px-2 py-1 rounded">
        <option value="">-- Trạng thái --</option>
        <option value="pending" @selected(request('status') == 'pending')>Đang xử lý</option>
        <option value="completed" @selected(request('status') == 'completed')>Đã xử lý</option>
        <option value="cancelled" @selected(request('status') == 'cancelled')>Đã hủy</option>
    </select>
    <input type="date" name="from" value="{{ request('from') }}" class="border px-2 py-1 rounded">
    <input type="date" name="to" value="{{ request('to') }}" class="border px-2 py-1 rounded">
    <button class="bg-blue-600 text-white px-4 py-1 rounded">Lọc</button>
</form>

<table class="w-full text-sm border">
    <thead>
        <tr class="bg-gray-200">
            <th class="p-2 border">Mã</th>
            <th class="p-2 border">Tên KH</th>
            <th class="p-2 border">Email</th>
            <th class="p-2 border">Tổng</th>
            <th class="p-2 border">Ngày</th>
            <th class="p-2 border">Trạng thái</th>
            <th class="p-2 border">Hành động</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($orders as $order)
        <tr>
            <td class="p-2 border">#{{ $order->id }}</td>
            <td class="p-2 border">{{ $order->name }}</td>
            <td class="p-2 border">{{ $order->email }}</td>
            <td class="p-2 border">{{ number_format($order->total_price) }}đ</td>
            <td class="p-2 border">{{ $order->created_at->format('d/m/Y H:i') }}</td>
            <td class="p-2 border">{{ ucfirst($order->status) }}</td>
            <td class="p-2 border">
                <a href="{{ route('admin.orders.show', $order->id) }}" class="text-blue-600 hover:underline">Xem</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="mt-4">
    {{ $orders->withQueryString()->links() }}
</div>
@else
@php abort(403); @endphp
@endcan
@endsection
