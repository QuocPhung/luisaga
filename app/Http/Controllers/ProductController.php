<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Sale;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['thumbnail', 'sales']);
        $selectedCategory = null;

        if ($request->filled('category_id')) {
            $selectedCategory = Category::find($request->category_id);

            if ($selectedCategory) {
                $categoryIds = $selectedCategory->getAllDescendantCategoryIds();
                $query->whereIn('category_id', $categoryIds);
            }
        }

        $products = $query->latest()->paginate(12);

        return view('pages.products.index', [
            'products' => $products,
            'categories' => $this->getCategoryTree(),
            'selectedCategory' => $selectedCategory
        ]);
    }

    public function show($id)
    {
        $product = Product::with(['thumbnail', 'sales'])->findOrFail($id);

        return view('pages.products.show', [
            'product' => $product,
            'categories' => $this->getCategoryTree()
        ]);
    }

    public function saleProducts()
    {
        $now = now();

        $productIds = Sale::where('status', 1)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->with('products')
            ->get()
            ->pluck('products')
            ->flatten()
            ->pluck('id')
            ->unique();

        $products = Product::with(['thumbnail', 'sales'])
            ->whereIn('id', $productIds)
            ->get();

        return view('pages.products.sale', [
            'products' => $products,
            'categories' => $this->getCategoryTree()
        ]);
    }

    private function getCategoryTree()
    {
        return Category::with('childrenRecursive')
            ->where('status', 1)
            ->whereNull('parent_id')
            ->get();
    }
}
