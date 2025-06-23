@extends('admin.layout')

@section('content')
@section('title', 'Quản lý sản phẩm')
@can('manage-products')

{{-- Include SweetAlert --}}
<div class="container mx-auto p-6">

    {{-- Flash message --}}
    @if(session('success'))
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

    <a href="{{ route('admin.dashboard') }}"
       class="bg-green-600 text-white px-4 py-2 rounded hover:bg-blue-700 mb-4 inline-block">
        Quay Lại
    </a>

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">Danh sách sản phẩm</h2>
        <a href="{{ route('admin.products.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + Thêm sản phẩm
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-300 text-sm">
            <thead class="bg-gray-200">
                <tr>
                    <th class="px-4 py-2 border">Ảnh</th>
                    <th class="px-4 py-2 border">Tên sản phẩm</th>
                    <th class="px-4 py-2 border">Mã</th>
                    <th class="px-4 py-2 border">Giá</th>
                    <th class="px-4 py-2 border">Kho</th>
                    <th class="px-4 py-2 border">Danh mục</th>
                    <th class="px-4 py-2 border">Trạng thái</th>
                    <th class="px-4 py-2 border">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-2 py-2 border text-center">
                            @if ($product->thumbnail)
                                <img src="{{ asset('storage/' . $product->thumbnail->image) }}" alt="" class="h-12 mx-auto object-cover rounded">
                            @else
                                <span class="text-gray-400 italic">Không ảnh</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 border">
                            <div class="flex items-center gap-2">
                                <span>{{ $product->name }}</span>
                                @if ($product->final_price < $product->price)
                                    <span class="text-xs bg-red-600 text-white px-2 py-0.5 rounded-full">SALE</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-2 border">{{ $product->code }}</td>
                        <td class="px-4 py-2 border text-center">
                            @if ($product->final_price < $product->price)
                                <div class="text-red-600 font-semibold">{{ number_format($product->final_price) }} đ</div>
                                <div class="line-through text-sm text-gray-500">{{ number_format($product->price) }} đ</div>
                                <div class="text-xs text-blue-500">{{ $product->discount_text }}</div>
                            @else
                                <div>{{ number_format($product->price) }} đ</div>
                            @endif
                        </td>
                        <td class="px-4 py-2 border text-center">{{ $product->stock }}</td>
                        <td class="px-4 py-2 border">{{ $product->category->name ?? 'Không có' }}</td>
                        <td class="px-4 py-2 border">
                            @if ($product->status)
                                <span class="text-green-600 font-semibold">Hiển thị</span>
                            @else
                                <span class="text-gray-500 italic">Ẩn</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 border text-center whitespace-nowrap">
                            <a href="{{ route('admin.products.edit', $product->id) }}"
                               class="text-yellow-600 hover:underline mr-2">Sửa</a>
                            <form action="{{ route('admin.products.destroy', $product->id) }}"
                                  method="POST" class="inline-block"
                                  onsubmit="return confirm('Bạn có chắc chắn muốn xóa?')">
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
            {{ $products->links('pagination::tailwind') }}
        </div>
    </div>
</div>
@else
@php abort(403); @endphp
@endif
@endsection
