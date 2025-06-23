@extends('admin.layout')

@section('title', 'Quản lý danh mục')

@section('content')
    @can('manage-categories')

        {{-- Thông báo thành công / lỗi --}}
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

        @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Lỗi',
                text: '{{ session('error') }}',
                toast: true,
                position: 'top-end',
                timer: 2500,
                showConfirmButton: false
            });
        </script>
        @endif

        <div class="container mx-auto p-6">
            <a href="{{ route('admin.dashboard') }}"
                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Quay Lại
            </a>
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold">Danh sách danh mục</h2>
                <a href="{{ route('admin.categories.create') }}"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    + Thêm danh mục
                </a>
            </div>

            <table class="table-auto w-full border border-gray-300 text-sm">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-4 py-2 border border-gray-300">Tên danh mục</th>
                        <th class="px-4 py-2 border border-gray-300">Danh mục cha</th>
                        <th class="px-4 py-2 border border-gray-300">Slug</th>
                        <th class="px-4 py-2 border border-gray-300">Trạng thái</th>
                        <th class="px-4 py-2 border border-gray-300">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        function showCategories($categories, $parent_id = null, $prefix = '')
                        {
                            foreach ($categories as $category) {
                                if ($category->parent_id === $parent_id) {
                                    echo '<tr>';
                                    echo '<td class="px-4 py-2 border border-gray-300">' . $prefix . $category->name . '</td>';
                                    echo '<td class="px-4 py-2 border border-gray-300">' . ($category->parent->name ?? 'Không có') . '</td>';
                                    echo '<td class="px-4 py-2 border border-gray-300">' . $category->slug . '</td>';
                                    echo '<td class="px-4 py-2 border border-gray-300">';
                                    echo $category->status
                                        ? '<span class="text-green-600 font-semibold">Hiển thị</span>'
                                        : '<span class="text-red-500 font-semibold">Ẩn</span>';
                                    echo '</td>';
                                    echo '<td class="px-4 py-2 border border-gray-300">
                                            <div class="flex items-center gap-2">
                                                <a href="' . route('admin.categories.edit', $category->id) . '" class="text-yellow-600 hover:underline">Sửa</a>
                                                <form action="' . route('admin.categories.destroy', $category->id) . '" method="POST" onsubmit="return confirm(\'Bạn có chắc chắn muốn xóa?\')">
                                                    ' . csrf_field() . method_field('DELETE') . '
                                                    <button type="submit" class="text-red-600 hover:underline">Xóa</button>
                                                </form>
                                            </div>
                                        </td>';
                                    echo '</tr>';

                                    showCategories($categories, $category->id, $prefix . '— ');
                                }
                            }
                        }
                    @endphp


                    @php showCategories($categories); @endphp
                </tbody>
            </table>
        </div>

    @else
        @php abort(403); @endphp
    @endcan
@endsection
