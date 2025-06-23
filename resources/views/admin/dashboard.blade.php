<!-- resources/views/admin/index.blade.php -->
@extends('admin.layout')

@section('title', 'Bảng điều khiển')

@section('content')
@can('view-dashboard')
    <h1 class="text-2xl font-bold mb-4">Chào mừng {{ auth()->user()->name }} đến trang quản trị!</h1>

    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        @can('manage-categories')
        <a href="{{ route('admin.categories.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-100">
            📁 Quản lý Danh mục
        </a>
        @endcan

        @can('manage-products')
        <a href="{{ route('admin.products.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-100">
            🖼 Quản lý Sản phẩm
        </a>
        @endcan

        @can('manage-sales')
        <a href="{{ route('admin.sales.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-100">
            🎁 Quản lý Khuyến Mãi
        </a>
        @endcan

        @can('manage-banners')
        <a href="{{ route('admin.banners.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-100">
            🖼 Quản lý Banner
        </a>
        @endcan

        @can('manage-orders')
        <a href="{{ route('admin.orders.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-100">
            🖼 Quản lý Order
        </a>
        @endcan

        @can('manage-revenue')
        <a href="{{ route('admin.revenue.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-100">
            📈 Quản lý Doanh thu
        </a>
        @endcan

        @can('manage-users')
        <a href="{{ route('admin.users.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-100">
            👤 Quản lý Người dùng
        </a>
        @endcan

        @can('manage-reports')
        <a href="{{ route('admin.reports.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-100">
            📊 Quản lý Báo cáo
        </a>
        @endcan
    </div>
@else
    @php abort(403); @endphp
@endcan

@endsection
