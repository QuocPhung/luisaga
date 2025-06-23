@extends('admin.layout')

@section('content')
@section('title', 'Quản lý Banner')
@can('manage-banners')

{{-- Thông báo thành công --}}
<div class="container mx-auto p-6">
    <a href="{{ route('admin.dashboard') }}"
           class="bg-green-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Quay Lại
    </a>
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Danh sách banner</h2>
        <a href="{{ route('admin.banners.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Thêm</a>
    </div>

    <table class="w-full table-auto border">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-2">#</th>
                <th class="border px-2 py-2">Ảnh</th>
                <th class="border px-2 py-2">Tiêu đề</th>
                <th class="border px-2 py-2">Link</th>
                <th class="border px-2 py-2">Vị trí</th>
                <th class="border px-2 py-2">Trạng thái</th>
                <th class="border px-2 py-2">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($banners as $index => $banner)
                <tr>
                    <td class="border px-2 py-1 text-center">{{ $index + 1 }}</td>
                    <td class="border px-2 py-1 text-center">
                        <img src="{{ asset('storage/' . $banner->image) }}" class="h-12 mx-auto">
                    </td>
                    <td class="border px-2 py-1">{{ $banner->title }}</td>
                    <td class="border px-2 py-1">
                        <a href="{{ $banner->link }}" class="text-blue-600" target="_blank">{{ $banner->link }}</a>
                    </td>
                    <td class="border px-2 py-1 text-center">{{ $banner->position }}</td>
                    <td class="border px-2 py-1 text-center">
                        @if ($banner->status)
                            <span class="text-green-600 font-semibold">Hiển thị</span>
                        @else
                            <span class="text-gray-500 italic">Ẩn</span>
                        @endif
                    </td>
                    <td class="border px-2 py-1 text-center">
                        <a href="{{ route('admin.banners.edit', $banner->id) }}" class="text-blue-600">Sửa</a>
                        <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" class="inline-block ml-2"
                              onsubmit="return confirm('Bạn chắc chắn muốn xóa?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500">Xoá</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
@php abort(403); @endphp
@endcan
@endsection
