@extends('admin.layout')

@section('content')
@section('title', 'Cập nhật sản phẩm')
@can('manage-products')

{{-- Thông báo thành công --}}
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Cập nhật sản phẩm</h2>

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Thông tin chung --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="font-medium">Tên</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" class="w-full border p-2 rounded">
            </div>
            <div>
                <label class="font-medium">Mã</label>
                <input type="text" value="{{ $product->code }}" disabled class="w-full border p-2 rounded bg-gray-100">
            </div>
        </div>

        {{-- Giá / kho --}}
        <div class="grid grid-cols-3 gap-4 mt-4">
            <div>
                <label>Giá gốc</label>
                <input type="number" name="price" value="{{ $product->price }}" class="w-full border p-2 rounded">
            </div>
            <div>
                <label>Giá KM (tự tính)</label>
                <input type="text" value="{{ number_format($product->final_price) }} đ"
                       class="w-full border p-2 rounded bg-gray-100 text-red-600" disabled>
                @if ($product->sales()->where('status', 1)->exists())
                    <div class="text-green-600 text-sm mt-1">
                        Đang áp dụng: {{ $product->discount_text }}
                    </div>
                @endif
            </div>
            <div>
                <label>Kho</label>
                <input type="number" name="stock" value="{{ $product->stock }}" class="w-full border p-2 rounded">
            </div>
        </div>

        {{-- Danh mục & trạng thái --}}
        <div class="grid grid-cols-2 gap-4 mt-4">
            <div>
                <label>Danh mục</label>
                <select name="category_id" class="w-full border p-2 rounded">
                    @foreach($categories as $cate)
                        <option value="{{ $cate->id }}" {{ $cate->id == $product->category_id ? 'selected' : '' }}>
                            {{ $cate->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Trạng thái</label>
                <select name="status" class="w-full border p-2 rounded">
                    <option value="1" {{ $product->status ? 'selected' : '' }}>Hiển thị</option>
                    <option value="0" {{ !$product->status ? 'selected' : '' }}>Ẩn</option>
                </select>
            </div>
        </div>

        {{-- Mô tả --}}
        <div class="mt-4">
            <label>Mô tả</label>
            <x-forms.tinymce-editor name="description" :value="$product->description" />
        </div>

        {{-- Upload ảnh mới --}}
        <div class="mt-6">
            <label class="block font-medium">Ảnh mô tả</label>
            <input type="file" name="images[mo-ta][]" multiple accept="image/*" class="w-full border p-2 rounded mt-1">
        </div>

        <div class="mt-4">
            <label class="block font-medium">Ảnh kỹ thuật</label>
            <input type="file" name="images[ky-thuat][]" multiple accept="image/*" class="w-full border p-2 rounded mt-1">
        </div>

        <div class="mt-4">
            <label class="block font-medium">Ảnh thực tế</label>
            <input type="file" name="images[thuc-te][]" multiple accept="image/*" class="w-full border p-2 rounded mt-1">
        </div>

        {{-- Ảnh hiện có --}}
        <div class="mt-6">
            <label class="block font-bold text-lg mb-2">Ảnh hiện tại</label>
            <div class="grid grid-cols-4 gap-4">
                @foreach ($product->images as $img)
                    <div class="relative border p-2 rounded shadow {{ $img->is_thumbnail ? 'ring-2 ring-blue-500' : '' }}">
                        <img src="{{ asset('storage/' . $img->image) }}" class="w-full h-24 object-cover rounded">

                        <div class="mt-1 text-xs text-center italic text-gray-600">
                            {{ $img->type }}
                        </div>

                        {{-- Form chọn ảnh đại diện --}}
                        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" class="text-center mt-1">
                            @csrf
                            @method('PUT')

                            {{-- Hidden fields để giữ lại giá trị --}}
                            <input type="hidden" name="name" value="{{ $product->name }}">
                            <input type="hidden" name="price" value="{{ $product->price }}">
                            <input type="hidden" name="stock" value="{{ $product->stock }}">
                            <input type="hidden" name="category_id" value="{{ $product->category_id }}">
                            <input type="hidden" name="status" value="{{ $product->status }}">
                            <input type="hidden" name="description" value="{{ $product->description }}">

                            <label>
                                <input type="radio" name="thumbnail" value="{{ $img->id }}"
                                    onchange="this.form.submit()" {{ $img->is_thumbnail ? 'checked' : '' }}> Đại diện
                            </label>
                        </form>

                        {{-- Form xoá ảnh --}}
                        <form action="{{ route('admin.products.images.delete', $img->id) }}" method="POST"
                            onsubmit="return confirm('Xoá ảnh này?')" class="absolute top-0 right-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 p-1 text-xl leading-none">×</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>


        {{-- Nút --}}
        <div class="mt-6 flex justify-between">
            <button class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Cập nhật</button>
            <a href="{{ route('admin.products.index') }}" class="text-gray-600 hover:underline">Quay lại</a>
        </div>
    </form>
</div>
@else
@php abort(403); @endphp
@endcan
@endsection
