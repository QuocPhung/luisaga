<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with('products')->latest()->paginate(10);
        return view('admin.sales.index', compact('sales'));
    }

    public function create()
    {
        $now = now();
    
        $excludedProductIds = \App\Models\Sale::where('end_date', '>=', now())
            ->with('products:id')
            ->get()
            ->pluck('products')
            ->flatten()
            ->pluck('id')
            ->unique();

        // Lấy sản phẩm chưa nằm trong khuyến mãi đang chạy
        $products = Product::where('status', 1)
            ->whereNotIn('id', $excludedProductIds)
            ->get();

        return view('admin.sales.create', compact('products'));
    }
    

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => [
                'required',
                'numeric',
                'min:0',
                $request->discount_type === 'percent' ? 'max:100' : null,
            ],
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|boolean',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
        ]);
    
        // Kiểm tra nếu bất kỳ sản phẩm nào đã nằm trong chương trình khuyến mãi đang diễn ra
        $conflict = Product::whereIn('id', $request->product_ids)
            ->whereHas('sales', function ($q) {
                $q->where('end_date', '>=', now());
            })
            ->pluck('name');
    
        if ($conflict->count()) {
            return back()->withErrors([
                'product_ids' => 'Một số sản phẩm đã thuộc khuyến mãi khác: ' . $conflict->implode(', ')
            ])->withInput();
        }
    
        DB::transaction(function () use ($request) {
            $sale = Sale::create($request->only([
                'name', 'discount_type', 'discount_value', 'start_date', 'end_date', 'status'
            ]));
    
            $sale->products()->attach($request->product_ids);
        });
    
        return redirect()->route('admin.sales.index')->with('success', 'Tạo khuyến mãi thành công!');
    }    

    public function edit(Sale $sale)
    {
        $now = now();
    
        // Cho phép sản phẩm đang trong chính sale này, nhưng không lấy những cái đang sale ở chương trình khác
        $products = Product::where('status', 1)
            ->where(function ($query) use ($sale, $now) {
                $query->whereDoesntHave('sales', function ($q) use ($sale, $now) {
                    $q->where('sales.id', '!=', $sale->id)
                      ->where('end_date', '>=', $now);
                });
            })
            ->get();
    
        $selectedProducts = $sale->products->pluck('id')->toArray();
        return view('admin.sales.edit', compact('sale', 'products', 'selectedProducts'));
    }
    

    public function update(Request $request, Sale $sale)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => [
                'required',
                'numeric',
                'min:0',
                $request->discount_type === 'percent' ? 'max:100' : null,
            ],
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|boolean',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
        ]);
        $conflict = Product::whereIn('id', $request->product_ids)
        ->whereHas('sales', function ($q) use ($sale) {
            $q->where('sales.id', '!=', $sale->id)
              ->where('end_date', '>=', now());
        })
        ->pluck('name');
    
        if ($conflict->count()) {
            return back()->withErrors([
                'product_ids' => 'Một số sản phẩm đã thuộc khuyến mãi khác: ' . $conflict->implode(', ')
            ])->withInput();
        }
        
        DB::transaction(function () use ($request, $sale) {
            $sale->update($request->only([
                'name', 'discount_type', 'discount_value', 'start_date', 'end_date', 'status'
            ]));

            $sale->products()->sync($request->product_ids);
        });

        return redirect()->route('admin.sales.index')->with('success', 'Cập nhật khuyến mãi thành công!');
    }

    public function destroy(Sale $sale)
    {
        DB::transaction(function () use ($sale) {
            $sale->products()->detach();
            $sale->delete();
        });

        return redirect()->route('admin.sales.index')->with('success', 'Đã xóa khuyến mãi!');
    }
}
