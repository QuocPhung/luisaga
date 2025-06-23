<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
class CartController extends Controller
{
    public function index()
    {
        return view('pages.cart.index');
    }      

    public function sync(Request $request)
    {
        $user = auth()->user();
        $items = $request->input('items', []);
    
        if (empty($items)) {
            return response()->json(['message' => 'Giỏ hàng trống'], 400);
        }
    
        // Gộp logic: nếu có thì xóa item cũ
        $cart = $user->cart()->first();
        if ($cart) {
            $cart->items()->delete();
        } else {
            $cart = $user->cart()->create();
        }
    
        foreach ($items as $item) {
            $cart->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
            ]);
        }
    
        return response()->json(['message' => 'Đã đồng bộ giỏ hàng']);
    }
    
    public function apiAdd(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1'
        ]);

        $user = $request->user();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        $item = $cart->items()->where('product_id', $request->product_id)->first();

        if ($item) {
            $item->increment('quantity', $request->quantity ?? 1);
        } else {
            $cart->items()->create([
                'product_id' => $request->product_id,
                'quantity' => $request->quantity ?? 1
            ]);
        }

        return response()->json(['message' => 'Đã thêm vào giỏ hàng']);
    }
    public function apiUpdate(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $user = $request->user();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        $item = $cart->items()->where('product_id', $request->product_id)->first();

        if ($item) {
            $item->update(['quantity' => $request->quantity]);
        }

        return response()->json(['message' => 'Đã cập nhật số lượng']);
    }
    public function apiRemove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);
    
        $user = $request->user();
        $cart = $user->cart()->first();
    
        if ($cart) {
            $cart->items()->where('product_id', $request->product_id)->delete();
        }
    
        return response()->json(['message' => 'Đã xoá sản phẩm']);
    }
    
    public function apiClear()
    {
        $user = auth()->user();
        $cart = $user->cart()->first();

        if ($cart) {
            $cart->items()->delete();
        }

        return response()->json(['message' => 'Đã xoá toàn bộ giỏ hàng']);
    }
    
    public function count()
    {
        $user = auth()->user();
        $cart = $user->cart()->first();
        $count = $cart ? $cart->items()->sum('quantity') : 0;
    
        return response()->json(['count' => $count]);
    }
    
    public function getItems()
    {
        $user = auth()->user();
        $cart = $user->cart()->with(['items.product'])->first();
    
        if (! $cart) {
            return response()->json(['items' => []]);
        }
    
        // Chuẩn hoá dữ liệu trả về
        $items = $cart->items->map(function ($item) {
            return [
                'id' => $item->product->id,
                'name' => $item->product->name,
                'thumbnail' => $item->product->thumbnail,
                'final_price' => $item->product->final_price,
                'quantity' => $item->quantity,
            ];
        });
    
        return response()->json(['items' => $items]);
    }

}
