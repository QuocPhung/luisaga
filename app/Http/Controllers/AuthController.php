<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin() {
        return view('auth.login');
    }

    public function showRegister() {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // 1. Validate dữ liệu đầu vào
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'confirm_password' => 'required|same:password',
        ], [
            'confirm_password.same' => 'Mật khẩu xác nhận không khớp.',
            'email.unique' => 'Email đã tồn tại trong hệ thống.',
        ]);
    
        // 2. Tạo người dùng
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
    
        // 3. Gán role mặc định (nếu dùng bảng trung gian role_user)
        $user->roles()->attach(10); // user chẳng hạn
    
        // 4. Đăng nhập người dùng
        Auth::login($user, $request->has('remember')); // remember = true nếu có checkbox
    
        // 5. Chuyển hướng
        return redirect()->route('home')->with('success', 'Đăng ký thành công!'); // redirect đến trang chính
    }
    
    public function login(Request $request)
    {
        $credentials = [
            'email' => $request->get('email'),
            'password' => $request->get('password'),
        ];
    
        if (Auth::attempt($credentials, $request->has('remember'))) {
            $user = Auth::user();
    
            // GỘP cart chứ không ghi đè
            if ($request->has('cart_items')) {
                $cart = \App\Models\Cart::firstOrCreate(['user_id' => $user->id]);
    
                foreach ($request->cart_items as $item) {
                    $existing = $cart->items()->where('product_id', $item['product_id'])->first();
    
                    if ($existing) {
                        $existing->increment('quantity', $item['quantity']);
                    } else {
                        $cart->items()->create([
                            'product_id' => $item['product_id'],
                            'quantity' => $item['quantity'],
                        ]);
                    }
                }
            }
    
            // Trả JSON nếu là request từ JS
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Đăng nhập thành công', 'redirect' => route('cart.index')]);
            }
    
            // Nếu là quản trị viên
            if ($user->hasAnyRole(['admin', 'productMng', 'categoryMng', 'orderMng', 'bannerMng', 'userMng'])) {
                return redirect()->route('admin.dashboard');
            }
    
            return redirect()->route('home');
        }
    
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Email hoặc mật khẩu không đúng'], 401);
        }
    
        return back()->with('error', 'Email hoặc mật khẩu không đúng');
    }    

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Bạn đã đăng xuất.');
    }
}
