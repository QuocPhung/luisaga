@extends('admin.layout')

@section('content')
@section('title', 'Sửa danh mục')
@can('manage-categories')

{{-- Thông báo thành công --}}
<div class="max-w-lg mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Sửa danh mục</h2>
    <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block font-medium">Tên danh mục</label>
            <input type="text" name="name" value="{{ $category->name }}" class="w-full border p-2 rounded" required>
        </div>

        <div class="mb-4">
            <label class="block font-medium">Danh mục cha</label>
            <select name="parent_id" class="w-full border p-2 rounded">
                <option value="">-- Không có --</option>
                @foreach ($categories as $cate)
                    <option value="{{ $cate->id }}" {{ $cate->id == $category->parent_id ? 'selected' : '' }}>
                        {{ $cate->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block font-medium">Trạng thái</label>
            <select name="status" class="w-full border p-2 rounded">
                <option value="1" {{ $category->status ? 'selected' : '' }}>Hiển thị</option>
                <option value="0" {{ !$category->status ? 'selected' : '' }}>Ẩn</option>
            </select>
        </div>
        @if($category->parent && $category->parent->status == 0)
            <p class="text-sm text-red-500 mt-1">⚠ Danh mục cha "{{ $category->parent->name }}" đang bị ẩn.</p>
        @endif

        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Cập nhật</button>
        <a href="{{ route('admin.categories.index') }}" class="ml-4 text-gray-600 hover:underline">Quay lại</a>
    </form>
</div>
@else
@php abort(403); @endphp
@endcan
@endsection
