@extends('admin.layout')

@section('content')
@section('title', 'Thêm danh mục')
@can('manage-categories')

{{-- Thông báo thành công --}}
<div class="max-w-lg mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Thêm danh mục</h2>
    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="block font-medium">Tên danh mục</label>
            <input type="text" name="name" class="w-full border p-2 rounded" required>
        </div>

        <div class="mb-4">
            <label class="block font-medium">Danh mục cha</label>
            <select name="parent_id" class="w-full border p-2 rounded">
                <option value="">-- Không có --</option>
                @foreach ($categories as $cate)
                    <option value="{{ $cate->id }}">{{ $cate->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block font-medium">Trạng thái</label>
            <select name="status" class="w-full border p-2 rounded">
                <option value="1" selected>Hiển thị</option>
                <option value="0">Ẩn</option>
            </select>
        </div>

        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Thêm</button>
        <a href="{{ route('admin.categories.index') }}" class="ml-4 text-gray-600 hover:underline">Quay lại</a>
    </form>
</div>
@else
@php abort(403); @endphp
@endcan
@endsection
