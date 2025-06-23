<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
class ReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->input('from') ?? now()->subDays(7)->toDateString();
        $to = $request->input('to') ?? now()->toDateString();
    
        // Doanh thu theo ngày
        $revenues = DB::table('orders')
            ->selectRaw('DATE(created_at) as date, SUM(total_price) as total')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->groupByRaw('DATE(created_at)')
            ->orderBy('date')
            ->get();
    
        $labels = $revenues->pluck('date')->map(fn($d) => Carbon::parse($d)->format('d/m'))->toArray();
        $data = $revenues->pluck('total')->toArray();
    
        // Top sản phẩm bán chạy
        $topProducts = OrderItem::with('product')
            ->whereHas('order', function ($query) use ($from, $to) {
                $query->where('status', 'completed')
                    ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
            })
            ->select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();
    
        return view('admin.reports.index', compact('labels', 'data', 'topProducts', 'from', 'to'));
    }
}
