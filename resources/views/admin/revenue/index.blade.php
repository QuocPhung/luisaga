@extends('admin.layout')

@section('content')
@section('title', 'Thống kê doanh thu')

{{-- Thông báo thành công --}}
@can('manage-revenue')
    <a href="{{ route('admin.dashboard') }}"
       class="bg-green-600 text-white px-4 py-2 rounded hover:bg-blue-700 mb-4 inline-block">
        Quay Lại
    </a>
<h1 class="text-2xl font-bold mb-4">Thống kê doanh thu</h1>

<form method="GET" class="flex gap-4 mb-6">
    <div>
        <label>Từ ngày:</label>
        <input type="date" name="from" value="{{ $from }}" class="border p-2 rounded">
    </div>
    <div>
        <label>Đến ngày:</label>
        <input type="date" name="to" value="{{ $to }}" class="border p-2 rounded">
    </div>
    <div class="flex items-end">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Lọc</button>
    </div>
</form>

<div class="bg-white p-4 rounded shadow mb-6">
    <p><strong>Tổng đơn hàng:</strong> {{ $totalOrders }}</p>
    <p><strong>Tổng doanh thu:</strong> {{ number_format($totalRevenue) }} đ</p>
</div>

<table class="w-full text-sm border">
    <thead class="bg-gray-100">
        <tr>
            <th class="p-2 border">Ngày</th>
            <th class="p-2 border">Doanh thu</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dailyRevenue as $day)
            <tr>
                <td class="p-2 border">{{ \Carbon\Carbon::parse($day->date)->format('d/m/Y') }}</td>
                <td class="p-2 border">{{ number_format($day->total) }} đ</td>
            </tr>
        @endforeach
    </tbody>
</table>
@else
@php abort(403); @endphp
@endcan
@endsection
