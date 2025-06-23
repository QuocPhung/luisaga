@extends('admin.layout')

@section('content')
@section('title', 'Thêm sản phẩm')
@can('manage-products')

{{-- Thông báo thành công --}}

<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-6">Thêm sản phẩm</h2>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Tên sản phẩm --}}
        <div class="mb-4">
            <label class="block font-medium">Tên sản phẩm</label>
            <input type="text" name="name" class="w-full border p-2 rounded" required>
        </div>

        {{-- Mã sản phẩm --}}
        <div class="mb-4">
            <label class="block font-medium">Mã sản phẩm</label>
            <input type="text" name="code" class="w-full border p-2 rounded" required>
        </div>

        {{-- Giá, Giá KM, Kho --}}
        <div class="grid grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block font-medium">Giá gốc</label>
                <input type="number" name="price" class="w-full border p-2 rounded" required>
            </div>
            <div>
                <label class="block font-medium">Giá KM (tự tính nếu có khuyến mãi)</label>
                <input type="text" class="w-full border p-2 rounded bg-gray-100 text-red-600" 
                    value="Sẽ tự động tính sau khi gán khuyến mãi" disabled>
            </div>
            <div>
                <label class="block font-medium">Kho</label>
                <input type="number" name="stock" class="w-full border p-2 rounded" required>
            </div>
        </div>

        {{-- Danh mục --}}
        <div class="mb-4">
            <label class="block font-medium">Danh mục</label>
            <select name="category_id" class="w-full border p-2 rounded" required>
                <option value="">-- Chọn danh mục --</option>
                @foreach ($categories as $cate)
                    <option value="{{ $cate->id }}">{{ $cate->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Trạng thái --}}
        <div class="mb-4">
            <label class="block font-medium">Trạng thái</label>
            <select name="status" class="w-full border p-2 rounded">
                <option value="1">Hiển thị</option>
                <option value="0">Ẩn</option>
            </select>
        </div>

        {{-- Mô tả --}}
        <div class="mb-4">
            <label class="block font-medium mb-1">Mô tả sản phẩm</label>
            <x-forms.tinymce-editor name="description" />
        </div>

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

        {{-- Thông số kỹ thuật --}}
        <div class="mb-6">
            <label class="block font-medium mb-2">Thông số kỹ thuật</label>
            <div id="attribute-list" class="space-y-2">
                <div class="flex gap-2">
                    <input type="text" name="attributes[0][name]" placeholder="Tên thuộc tính (VD: CPU)" class="w-1/2 border p-2 rounded">
                    <input type="text" name="attributes[0][value]" placeholder="Giá trị (VD: Intel i5-12400F)" class="w-1/2 border p-2 rounded">
                </div>
            </div>
            <button type="button" onclick="addAttribute()" class="mt-2 text-blue-600 hover:underline text-sm">+ Thêm thuộc tính</button>
        </div>

        {{-- Submit --}}
        <div class="mt-6 flex justify-between">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Thêm</button>
            <a href="{{ route('admin.products.index') }}" class="text-gray-600 hover:underline">Quay lại</a>
        </div>
    </form>
</div>
@else
@php abort(403); @endphp
@endcan
@endsection

@section('scripts')
    <x-head.tinymce-config />

    <script>
        let attributeIndex = 1;
        function addAttribute() {
            const container = document.getElementById('attribute-list');
            const div = document.createElement('div');
            div.classList.add('flex', 'gap-2', 'mt-1');
            div.innerHTML = `
                <input type="text" name="attributes[${attributeIndex}][name]" placeholder="Tên thuộc tính" class="w-1/2 border p-2 rounded">
                <input type="text" name="attributes[${attributeIndex}][value]" placeholder="Giá trị" class="w-1/2 border p-2 rounded">
            `;
            container.appendChild(div);
            attributeIndex++;
        }
    </script>
@endsection
