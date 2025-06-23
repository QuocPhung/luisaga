<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Role;
class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = \App\Models\Role::all();
        return view('admin.users.create', compact('roles'));
    }
    

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'roles'    => 'nullable|array',
            'roles.*'  => 'exists:roles,id',
        ]);
    
        $user = \App\Models\User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);
    
        $user->roles()->sync($validated['roles'] ?? []);
    
        return redirect()->route('admin.users.index')->with('success', 'Tạo người dùng thành công!');
    }
    
    public function edit(User $user)
    {
        $roles = \App\Models\Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }
    
    public function update(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
        ]);
    
        $adminRole = Role::where('name', 'admin')->first();
    
        if ($request->has('roles') && in_array($adminRole->id, $request->roles)) {
            $adminAssigned = $adminRole->users()->where('id', '!=', $user->id)->exists();
    
            if ($adminAssigned) {
                return back()->with('error', 'Chỉ được có 1 tài khoản admin.');
            }
        }
    
        $user->roles()->sync($request->roles ?? []);
    
        return redirect()->route('admin.users.index')->with('success', 'Cập nhật quyền thành công.');
    }
    
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Xóa người dùng thành công!');
    }
}
