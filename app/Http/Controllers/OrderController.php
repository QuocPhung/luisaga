<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;
class OrderController extends Controller
{
    public function index()
    {
        $orders = Auth::user()->orders()->latest()->paginate(10);
        return view('pages.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        // Đảm bảo user chỉ xem được đơn hàng của mình
        abort_if($order->user_id !== Auth::id(), 403);

        return view('pages.orders.show', compact('order'));
    }
    public function cancel(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'Không thể hủy đơn hàng đã xử lý.');
        }

        // Trả lại số lượng sản phẩm
        foreach ($order->items as $item) {
            $item->product->increment('stock', $item->quantity); // ✅ dùng 'stock' thay vì 'quantity'
        }        

        $order->update(['status' => 'cancelled']);

        return redirect()->route('orders.index')->with('success', 'Đơn hàng đã được hủy.');
    }
    public function edit(Order $order)
    {
        // Chỉ cho phép sửa nếu là chủ đơn và trạng thái là pending
        abort_if($order->user_id !== auth()->id(), 403);
        if ($order->status !== 'pending') {
            return redirect()->route('orders.index')->with('error', 'Chỉ có thể chỉnh sửa đơn hàng đang chờ xử lý.');
        }

        return view('pages.orders.edit', compact('order'));
    }
    public function update(Request $request, Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);
        if ($order->status !== 'pending') {
            return redirect()->route('orders.index')->with('error', 'Chỉ có thể cập nhật đơn hàng đang chờ xử lý.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'phone' => 'required|regex:/^[0-9]{9,15}$/',
            'address' => 'required|string|max:500',
            'payment_method' => 'required|in:cod,bank',
        ]);

        $order->update($request->only(['name', 'email', 'phone', 'address', 'payment_method']));

        return redirect()->route('orders.show', $order->id)->with('success', 'Cập nhật đơn hàng thành công!');
    }
}
