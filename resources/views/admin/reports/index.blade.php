@extends('admin.layout')

@section('content')
@section('title', 'Thống kê doanh thu')
@can('manage-reports')
    <a href="{{ route('admin.dashboard') }}"
       class="bg-green-600 text-white px-4 py-2 rounded hover:bg-blue-700 mb-4 inline-block">
        Quay Lại
    </a>
<h1 class="text-2xl font-bold mb-6">Biểu đồ doanh thu theo ngày</h1>

{{-- Bộ lọc thời gian --}}
<form method="GET" class="flex items-end gap-4 mb-6">
    <div>
        <label class="block font-medium mb-1">Từ ngày</label>
        <input type="date" name="from" value="{{ request('from') }}" class="border rounded p-2">
    </div>
    <div>
        <label class="block font-medium mb-1">Đến ngày</label>
        <input type="date" name="to" value="{{ request('to') }}" class="border rounded p-2">
    </div>
    <div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Lọc
        </button>
    </div>
</form>

<canvas id="revenueChart" height="100"></canvas>
<h2 class="text-lg font-bold mb-3">Top 10 sản phẩm bán chạy ({{ $from }} – {{ $to }})</h2>

<table class="w-full text-sm border mb-12">
    <thead>
        <tr class="bg-gray-100">
            <th class="p-2 border">Sản phẩm</th>
            <th class="p-2 border">Số lượng đã bán</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($topProducts as $item)
            <tr>
                <td class="p-2 border">{{ $item->product->name ?? 'Đã xóa' }}</td>
                <td class="p-2 border">{{ $item->total_sold }}</td>
            </tr>
        @empty
            <tr><td colspan="2" class="text-center text-gray-500 p-4">Không có dữ liệu</td></tr>
        @endforelse
    </tbody>
</table>
@else
    @php abort(403); @endphp
@endcan
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($labels) !!},
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: {!! json_encode($data) !!},
                fill: true,
                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                borderColor: 'rgba(59, 130, 246, 1)',
                tension: 0.3
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' đ';
                        }
                    }
                }
            }
        }
    });
</script>
@endsection
