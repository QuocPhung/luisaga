<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController as  AdminCategoryController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\RevenueController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;

// Trang chủ & sản phẩm
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('product.index');
Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');
Route::get('/sale', [ProductController::class, 'saleProducts'])->name('product.sale');
Route::get('/category/{id}', [CategoryController::class, 'show'])->name('category.show');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register')->middleware('guest');

Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit')->middleware('guest');

// API giỏ hàng cho user đã đăng nhập
Route::middleware('auth')->group(function () {
    Route::get('/api/cart/items', [CartController::class, 'getItems']);
    Route::get('/cart/count', [CartController::class, 'count']);
    Route::post('/api/cart/add', [CartController::class, 'apiAdd']);
    Route::post('/api/cart/update', [CartController::class, 'apiUpdate']);
    Route::post('/api/cart/remove', [CartController::class, 'apiRemove']);
    Route::post('/api/cart/sync', [CartController::class, 'sync']);
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');

});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'can:view-dashboard'])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');

    // Quản lý danh mục
    Route::middleware('can:manage-categories')->group(function () {
        Route::resource('categories', AdminCategoryController::class);
    });

    // Quản lý sản phẩm
    Route::middleware('can:manage-products')->group(function () {
        Route::resource('products', AdminProductController::class);
        Route::delete('products/images/{image}', [AdminProductController::class, 'deleteImage'])->name('products.images.delete');
        Route::get('products/search', [AdminProductController::class, 'search'])->name('products.search');
    });

    // Quản lý khuyến mãi
    Route::middleware('can:manage-sales')->group(function () {
        Route::resource('sales', SaleController::class);
    });

    // Quản lý banner
    Route::middleware('can:manage-banners')->group(function () {
        Route::resource('banners', BannerController::class);
    });

    // Quản lý đơn hàng
    Route::middleware('can:manage-orders')->group(function () {
        Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
    });

    // Quản lý doanh thu
    Route::middleware('can:manage-revenue')->group(function () {
        Route::get('revenue', [RevenueController::class, 'index'])->name('revenue.index');
    });

    // Quản lý báo cáo
    Route::middleware('can:manage-reports')->group(function () {
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    });

    // Quản lý người dùng
    Route::middleware('can:manage-users')->group(function () {
        Route::resource('users', UserController::class);
    });
});