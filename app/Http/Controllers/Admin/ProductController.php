<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\ProductImageService;
use App\Models\ProductImage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'sales', 'thumbnail']);

        if ($request->filled('keyword')) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('sale') && $request->sale == 1) {
            $query = $query->get()->filter(fn($product) => $product->final_price < $product->price);
            return view('admin.products.index', [
                'products' => paginateCollection($query, 10),
                'categories' => Category::all()
            ]);
        }

        return view('admin.products.index', [
            'products' => $query->latest()->paginate(10),
            'categories' => Category::all()
        ]);
    }

    public function create()
    {
        $categories = Category::where('status', 1)->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:products,code',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'status' => 'boolean',
            'description' => 'nullable|string',
            'images.*.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only(['code', 'name', 'price', 'stock', 'category_id', 'status', 'description']);
        $data['slug'] = Str::slug($request->name);

        $product = Product::create($data);

        if ($request->has('images')) {
            foreach ($request->file('images') as $type => $files) {
                ProductImageService::uploadImages($product, $files, false, $type);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Thêm sản phẩm thành công.');
    }

    public function edit(Product $product)
    {
        $categories = Category::where('status', 1)->get();
        $product->load('images');
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'status' => 'boolean',
            'description' => 'nullable|string',
            'images.*.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only(['name', 'price', 'stock', 'category_id', 'status', 'description']);
        $data['slug'] = Str::slug($request->name);
        $product->update($data);

        if ($request->has('images')) {
            foreach ($request->file('images') as $type => $files) {
                ProductImageService::uploadImages($product, $files, false, $type);
            }
        }

        if ($request->filled('thumbnail')) {
            $product->images()->update(['is_thumbnail' => false]);
            $product->images()->where('id', $request->thumbnail)->update(['is_thumbnail' => true]);
        }

        return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công.');
    }

    public function destroy(Product $product)
    {
        // Kiểm tra xem sản phẩm có đang nằm trong giỏ hàng hoặc đơn hàng không
        $inCarts = $product->cartItems()->count();
        $inOrders = $product->orderItems()->count();
    
        if ($inCarts > 0 || $inOrders > 0) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Không thể xoá sản phẩm vì đang được sử dụng trong giỏ hàng hoặc đơn hàng.');
        }
    
        // Xoá hình ảnh nếu không bị ràng buộc
        $product->load('images');
    
        foreach ($product->images as $img) {
            if (!empty($img->image) && Storage::disk('public')->exists($img->image)) {
                Storage::disk('public')->delete($img->image);
            }
            $img->delete();
        }
    
        $product->delete();
    
        return redirect()->route('admin.products.index')->with('success', 'Đã xoá sản phẩm thành công.');
    }

    public function deleteImage($img)
    {
        $img = \App\Models\ProductImage::findOrFail($img);
    
        if ($img->image && Storage::disk('public')->exists($img->image)) {
            Storage::disk('public')->delete($img->image);
        }
    
        $img->delete();
    
        return back()->with('success', 'Đã xóa ảnh sản phẩm.');
    }
    
    public function search(Request $request)
    {
        $query = $request->q;

        $products = Product::where('name', 'like', "%$query%")
            ->where('status', 1)
            ->select('id', 'name', 'price')
            ->limit(20)
            ->get();

        return response()->json($products);
    }

}
