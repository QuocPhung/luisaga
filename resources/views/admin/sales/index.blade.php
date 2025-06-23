@extends('admin.layout')

@section('content')
@section('title', 'Danh sách khuyến mãi')
@can('manage-sales')

{{-- Thông báo thành công --}}
<div class="container mx-auto p-6">
    <a href="{{ route('admin.dashboard') }}"
       class="bg-green-600 text-white px-4 py-2 rounded hover:bg-blue-700 mb-4 inline-block">
        Quay Lại
    </a>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Danh sách khuyến mãi</h2>
        <a href="{{ route('admin.sales.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Thêm khuyến mãi</a>
    </div>

    {{-- Flash message --}}
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Thành công',
                text: '{{ session('success') }}',
                toast: true,
                position: 'top-end',
                timer: 2500,
                showConfirmButton: false
            });
        </script>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-300 text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border">Tên</th>
                    <th class="px-4 py-2 border">Loại</th>
                    <th class="px-4 py-2 border">Giá trị</th>
                    <th class="px-4 py-2 border">Thời gian</th>
                    <th class="px-4 py-2 border">Sản phẩm</th>
                    <th class="px-4 py-2 border">Trạng thái</th>
                    <th class="px-4 py-2 border">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sales as $sale)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-2 border">{{ $sale->name }}</td>
                        <td class="px-4 py-2 border">
                            {{ $sale->discount_type == 'percent' ? 'Phần trăm' : 'Giá trị cố định' }}
                        </td>
                        <td class="px-4 py-2 border">
                            {{ $sale->discount_value }} {{ $sale->discount_type == 'percent' ? '%' : '₫' }}
                        </td>
                        <td class="px-4 py-2 border">
                            {{ \Carbon\Carbon::parse($sale->start_date)->format('d/m/Y') }} - 
                            {{ \Carbon\Carbon::parse($sale->end_date)->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-2 border">
                            {{ $sale->products->count() }} sp
                        </td>
                        <td class="px-4 py-2 border">
                            @if($sale->status)
                                <span class="text-green-600 font-semibold">Hiển thị</span>
                            @else
                                <span class="text-gray-500 italic">Ẩn</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 border text-center whitespace-nowrap">
                            <a href="{{ route('admin.sales.edit', $sale->id) }}"
                               class="text-yellow-600 hover:underline mr-2">Sửa</a>
                            <form action="{{ route('admin.sales.destroy', $sale->id) }}" method="POST"
                                  onsubmit="return confirm('Xóa khuyến mãi này?')" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $sales->links('pagination::tailwind') }}
        </div>
    </div>
</div>
@else
    @php abort(403); @endphp
@endcan
@endsection
