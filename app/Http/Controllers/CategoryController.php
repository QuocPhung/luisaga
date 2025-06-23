<?php

namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show($id)
    {
        $category = Category::with('childrenRecursive')->findOrFail($id);
    
        $categoryIds = $category->getAllDescendantCategoryIds();
    
        $products = Product::with(['thumbnail', 'sales'])
            ->whereIn('category_id', $categoryIds)
            ->paginate(12);
    
        // Lấy danh sách cha để render select nếu cần
        $categories = Category::with('childrenRecursive')
            ->where('status', 1)
            ->whereNull('parent_id')
            ->get();
    
        return view('pages.products.category_show', compact('category', 'products', 'categories'));
    }    
}
