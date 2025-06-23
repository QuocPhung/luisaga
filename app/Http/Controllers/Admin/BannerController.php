<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('position')->get();
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'link' => 'nullable|url',
            'image' => 'sometimes|required|image|mimes:jpg,jpeg,png,webp|max:4096',
            'status' => 'boolean',
            'position' => 'nullable|integer|min:0',
        ]);        
    
        $data['image'] = $request->file('image')->store('banners', 'public');
        $data['position'] = $data['position'] ?? 0;
    
        Banner::create($data);
    
        return redirect()->route('admin.banners.index')->with('success', 'Thêm banner thành công.');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'link' => 'nullable|url',
            'image' => 'sometimes|required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'boolean',
            'position' => 'nullable|integer|min:0',
        ]);        
    
        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($banner->image);
            $data['image'] = $request->file('image')->store('banners', 'public');
        }
    
        $data['position'] = $data['position'] ?? 0;
    
        $banner->update($data);
    
        return redirect()->route('admin.banners.index')->with('success', 'Cập nhật banner thành công.');
    }

    public function destroy(Banner $banner)
    {
        Storage::disk('public')->delete($banner->image);
        $banner->delete();

        return back()->with('success', 'Đã xóa banner.');
    }
}
