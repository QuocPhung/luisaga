@extends('admin.layout')

@section('content')
@section('title', 'Quản lý người dùng')
@can('manage-users')
    <a href="{{ route('admin.dashboard') }}"
       class="bg-green-600 text-white px-4 py-2 rounded hover:bg-blue-700 mb-4 inline-block">
        Quay Lại
    </a>
<h1 class="text-2xl font-bold mb-4">Quản lý người dùng</h1>

<table class="w-full border text-sm">
    <thead>
        <tr class="bg-gray-100">
            <th class="p-2 border">Tên</th>
            <th class="p-2 border">Email</th>
            <th class="p-2 border">Quyền</th>
            <th class="p-2 border">Hành động</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
        <tr>
            <td class="p-2 border">{{ $user->name }}</td>
            <td class="p-2 border">{{ $user->email }}</td>
            <td class="p-2 border">
                @foreach ($user->roles as $role)
                    <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-1">{{ $role->name }}</span>
                @endforeach
            </td>
            <td class="p-2 border">
                <a href="{{ route('admin.users.edit', $user->id) }}" class="text-blue-600 hover:underline">Sửa</a>
                @if (auth()->id() !== $user->id)
                <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" class="inline" onsubmit="return confirm('Xóa người dùng này?')">
                    @csrf @method('DELETE')
                    <button class="text-red-600 hover:underline ml-2">Xóa</button>
                </form>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="mt-4">
    {{ $users->links() }}
</div>
@else
@php abort(403); @endphp
@endcan
@endsection
