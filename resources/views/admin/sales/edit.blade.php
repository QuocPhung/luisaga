@extends('admin.layout')

@section('head')
    {{-- Thêm Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
@section('title', 'Cập nhật khuyến mãi')

{{-- Thông báo thành công --}}
@can('manage-sales')
<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Cập nhật khuyến mãi</h2>
    <form action="{{ route('admin.sales.update', $sale->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Tên, loại giảm, giá trị --}}
        <div class="mb-4">
            <label class="font-medium">Tên khuyến mãi</label>
            <input type="text" name="name" class="w-full border p-2 rounded" value="{{ old('name', $sale->name) }}" required>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label>Loại giảm</label>
                <select name="discount_type" class="w-full border p-2 rounded" required>
                    <option value="percent" {{ $sale->discount_type == 'percent' ? 'selected' : '' }}>Phần trăm (%)</option>
                    <option value="fixed" {{ $sale->discount_type == 'fixed' ? 'selected' : '' }}>Giảm cố định (VNĐ)</option>
                </select>
            </div>
            <div>
                <label>Giá trị giảm</label>
                <input type="number" name="discount_value" min="0" class="w-full border p-2 rounded" value="{{ $sale->discount_value }}" required>
            </div>
        </div>

        {{-- Ngày --}}
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label>Bắt đầu</label>
                <input type="date" name="start_date" class="w-full border p-2 rounded" value="{{ $sale->start_date->format('Y-m-d') }}" required>
            </div>
            <div>
                <label>Kết thúc</label>
                <input type="date" name="end_date" class="w-full border p-2 rounded" value="{{ $sale->end_date->format('Y-m-d') }}" required>
            </div>
        </div>

        {{-- Trạng thái --}}
        <div class="mb-4">
            <label>Trạng thái</label>
            <select name="status" class="w-full border p-2 rounded">
                <option value="1" {{ $sale->status ? 'selected' : '' }}>Hiển thị</option>
                <option value="0" {{ !$sale->status ? 'selected' : '' }}>Ẩn</option>
            </select>
        </div>

        {{-- Sản phẩm --}}
        <div class="mb-4">
            <label class="font-semibold">Chọn sản phẩm áp dụng khuyến mãi</label>

            <input type="text" id="product-search" placeholder="Tìm sản phẩm..." class="w-full border p-2 mb-2 rounded">

            <div id="product-list" class="border p-2 rounded h-64 overflow-y-auto space-y-1">
            @php
                $selectedProductIds = $sale->products->pluck('id')->toArray();
            @endphp

            @forelse ($products as $product)
                <label class="flex items-center space-x-2">
                    <input 
                        type="checkbox" 
                        name="product_ids[]" 
                        value="{{ $product->id }}"
                        {{ in_array($product->id, $selectedProductIds) ? 'checked' : '' }}
                    >
                    <span>{{ $product->name }} ({{ number_format($product->price) }}đ)</span>
                </label>
            @empty
                <p class="text-sm text-red-500">Không còn sản phẩm nào chưa có khuyến mãi!</p>
            @endforelse
            </div>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Cập nhật</button>
        <a href="{{ route('admin.sales.index') }}" class="ml-4 text-gray-600 hover:underline">Quay lại</a>
    </form>
</div>
@else
    @php abort(403); @endphp
@endcan
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('product-search');
        const labels = document.querySelectorAll('#product-list label');

        searchInput.addEventListener('input', function () {
            const keyword = this.value.toLowerCase();

            labels.forEach(label => {
                const labelText = label.innerText.toLowerCase();
                if (labelText.includes(keyword)) {
                    label.style.display = 'flex'; // hiện
                } else {
                    label.style.display = 'none'; // ẩn
                }
            });
        });
    });
    </script>

@endsection
