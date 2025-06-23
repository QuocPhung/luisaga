<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $cart = $user->cart()->with('items.product')->first();
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống.');
        }

        return view('pages.checkout.index', ['cart' => $cart]);
    }

    public function create()
    {
        $user = Auth::user();
        $cart = $user->cart()->with(['items.product'])->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        return view('checkout.create', compact('cart'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|regex:/^0[0-9]{9}$/',
            'address' => 'required|string|max:500',
            'payment_method' => 'required|in:cod,bank',
        ]);

        $user = auth()->user();

        $cart = $user->cart()->with('items.product')->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        try {
            DB::beginTransaction();

            // 1. Tạo đơn hàng
            $order = Order::create([
                'user_id'        => $user->id,
                'name'           => $request->name,
                'email'          => $user->email, // lấy từ user hiện tại
                'phone'          => $request->phone,
                'address'        => $request->address,
                'payment_method' => $request->payment_method,
                'status'         => 'pending',
                'total_price'   => 0, // tính sau
            ]);

            $total = 0;

            // 2. Thêm từng sản phẩm vào đơn hàng
            foreach ($cart->items as $item) {
                $product = $item->product;
            
                if (!$product || $product->stock < $item->quantity) {
                    throw new \Exception("Sản phẩm '{$product->name}' không đủ hàng.");
                }
            
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $product->id,
                    'price'      => $product->final_price,
                    'quantity'   => $item->quantity,
                ]);
            
                // Trừ số lượng tồn kho (sửa lại là stock)
                $product->decrement('stock', $item->quantity);
            
                $total += $product->final_price * $item->quantity;
            }            

            // 3. Cập nhật tổng tiền
            $order->update(['total_price' => $total]);

            // 4. Xóa giỏ hàng
            $cart->items()->delete();

            DB::commit();

            return redirect()->route('orders.index')->with('success', 'Đặt hàng thành công!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Đặt hàng thất bại: ' . $e->getMessage());
        }
    }

}
