@extends('admin.layout')

@section('content')
@section('title', 'Thêm Banner')
@can('manage-banners')

{{-- Thông báo thành công --}}
<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Thêm banner mới</h2>

    <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="font-medium">Tiêu đề</label>
                <input type="text" name="title" value="{{ old('title') }}" class="w-full border p-2 rounded">
            </div>
            <div>
                <label class="font-medium">Link</label>
                <input type="url" name="link" value="{{ old('link') }}" class="w-full border p-2 rounded">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mt-4">
            <div>
                <label>Vị trí</label>
                <input type="number" name="position" value="{{ old('position', 0) }}" class="w-full border p-2 rounded">
            </div>
            <div>
                <label>Trạng thái</label>
                <select name="status" class="w-full border p-2 rounded">
                    <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Hiển thị</option>
                    <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Ẩn</option>
                </select>
            </div>
        </div>

        <div class="mt-4">
            <label>Ảnh <span class="text-red-500">*</span></label>
            <input type="file" name="image" required class="w-full border p-2 rounded mt-1">
        </div>

        <div class="mt-6">
            <button class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Lưu</button>
            <a href="{{ route('admin.banners.index') }}" class="ml-4 text-gray-600 hover:underline">Quay lại</a>
        </div>
    </form>
</div>
@else
@php abort(403); @endphp
@endcan
@endsection
