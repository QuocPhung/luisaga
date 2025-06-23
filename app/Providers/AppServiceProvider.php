<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use App\Observers\ProductObserver;
use App\Models\Product;
use App\Models\Category;
use App\Models\Banner;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // === Gate Definitions ===
        Gate::define('manage-products', fn($user) => $user->hasAnyRole(['admin', 'productMng']));
        Gate::define('manage-categories', fn($user) => $user->hasAnyRole(['admin', 'categoryMng']));
        Gate::define('manage-orders', fn($user) => $user->hasAnyRole(['admin', 'orderMng']));
        Gate::define('manage-banners', fn($user) => $user->hasAnyRole(['admin', 'bannerMng']));
        Gate::define('manage-users', fn($user) => $user->hasAnyRole(['admin', 'userMng']));
        Gate::define('manage-reports', fn($user) => $user->hasAnyRole(['admin', 'reportMng']));
        Gate::define('manage-revenue', fn($user) => $user->hasAnyRole(['admin', 'revenueMng']));
        Gate::define('manage-sales', fn($user) => $user->hasAnyRole(['admin', 'saleMng']));
        Gate::define('view-dashboard', fn($user) =>
            $user->hasAnyRole(['admin', 'productMng', 'categoryMng', 'orderMng', 'bannerMng', 'userMng', 'reportMng', 'revenueMng', 'saleMng'])
        );

        // === Model Observers ===
        Product::observe(ProductObserver::class);

        // === View Composer: Sidebar (danh mục đa cấp) ===
        View::composer(['layouts.partials.sidebar', 'layouts.components.category-menu'], function ($view) {
            $sidebarCategories = Category::with('childrenRecursive')
                ->whereNull('parent_id')
                ->where('status', 1)
                ->get();
        
            $view->with('sidebarCategories', $sidebarCategories);
        });
        

        // === View Composer: Header menu nếu cần danh mục ===
        View::composer(['layouts.partials.header'], function ($view) {
            $headerCategories = Category::with('childrenRecursive')
                ->whereNull('parent_id')
                ->where('status', 1)
                ->get();

            $view->with('headerCategories', $headerCategories);
        });

        // === View Composer: Banners (Main & Side) ===
        View::composer(['layouts.*'], function ($view) {
            $banners = Banner::where('status', 1)->orderBy('position')->get();

            $mainBanners = $banners->take(5);   // Slider chính
            $sideBanners = $banners->slice(5);  // Banners phụ

            $view->with([
                'banners' => $banners,
                'mainBanners' => $mainBanners,
                'sideBanners' => $sideBanners,
            ]);
        });
    }
}
