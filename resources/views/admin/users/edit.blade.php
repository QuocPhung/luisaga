@extends('admin.layout')

@section('content')
@section('title', 'Gán quyền cho người dùng')
@can('manage-users')

{{-- Thông báo thành công --}}
<h1 class="text-xl font-bold mb-4">Gán quyền cho người dùng: {{ $user->name }}</h1>

<form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="space-y-4 max-w-lg">
    @csrf
    @method('PUT')

    @php
        // Xác định đây có phải là admin gốc không (tuỳ cách bạn xác định: id hoặc email)
        $isMainAdmin = $user->id === 1; // hoặc: $user->email === 'admin@example.com';
    @endphp

    @if ($isMainAdmin)
        <div class="p-3 bg-yellow-100 text-yellow-800 rounded">
            Đây là tài khoản <strong>admin chính</strong>. Không thể thay đổi vai trò.
        </div>
    @else
        <div>
            <label class="block font-medium mb-1">Vai trò</label>
            @foreach ($roles as $role)
                @php
                    $isAdminRole = $role->name === 'admin';
                    $adminExists = \App\Models\Role::where('name', 'admin')->first()?->users()->where('id', '!=', $user->id)->exists();
                @endphp

                @continue($isAdminRole && $adminExists)

                <label class="inline-flex items-center mr-4 mb-2">
                    <input type="checkbox" name="roles[]"
                        value="{{ $role->id }}"
                        {{ $user->roles->contains($role->id) ? 'checked' : '' }}>
                    {{ $role->name }}
                </label>
            @endforeach
        </div>
    @endif

    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
        {{ $isMainAdmin ? 'disabled' : '' }}>
        Cập nhật
    </button>
    <a href="{{ route('admin.users.index') }}" class="ml-4 text-gray-600 hover:underline">Quay lại</a>
</form>
@else
@php abort(403); @endphp
@endcan
@endsection
