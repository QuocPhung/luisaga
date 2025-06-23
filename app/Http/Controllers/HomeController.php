<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Banner;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::with('childrenRecursive')
        ->where('status', 1)
        ->whereNull('parent_id')
        ->get();
        $banners = Banner::where('status', 1)->orderBy('position')->get();
        $products = Product::with('thumbnail', 'sales')->latest()->take(8)->get();
        $categoriesWithProducts = Category::with('childrenRecursive')
        ->where('status', 1)
        ->whereNull('parent_id')
        ->get()
        ->map(function ($category) {
            // Đảm bảo childrenRecursive đã load sẵn
            $category->load('childrenRecursive');
    
            $categoryIds = $category->getAllDescendantCategoryIds();
    
            $category->allProducts = Product::with(['thumbnail', 'sales'])
                ->whereIn('category_id', $categoryIds)
                ->latest()
                ->take(4)
                ->get();
    
            return $category;
        });    

        $discountedProducts = Product::whereHas('sales', function ($q) {
            $q->where('start_date', '<=', now())  // chỉ lấy các khuyến mãi đã bắt đầu
              ->where('end_date', '>=', now());   // và chưa kết thúc
        })
        ->with(['thumbnail', 'sales'])
        ->onSale()
        ->get();
        
    
        return view('pages.home', compact('categories', 'banners', 'products', 'discountedProducts','categoriesWithProducts'));
    }
}
