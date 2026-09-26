<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalItemsInStock = (int) Product::sum('current_stock');
        $totalCostValuation = (float) Product::select(DB::raw('SUM(current_stock * cost_price) as total'))->value('total') ?? 0;
        $totalRetailValuation = (float) Product::select(DB::raw('SUM(current_stock * selling_price) as total'))->value('total') ?? 0;
        $potentialProfit = $totalRetailValuation - $totalCostValuation;

        $outOfStockCount = Product::where('current_stock', '<=', 0)->count();
        $lowStockCount = Product::where('current_stock', '>', 0)
            ->whereColumn('current_stock', '<=', 'min_stock_alert')
            ->count();

        // Items requiring urgent restock
        $lowStockProducts = Product::with(['category', 'supplier'])
            ->whereColumn('current_stock', '<=', 'min_stock_alert')
            ->orderBy('current_stock', 'asc')
            ->take(6)
            ->get();

        // Recent 8 stock movements
        $recentMovements = StockMovement::with('product')
            ->latest()
            ->take(8)
            ->get();

        // Category breakdown for doughnut chart
        $categoryBreakdown = Category::withCount('products')
            ->with(['products' => function ($query) {
                $query->select('category_id', 'current_stock', 'cost_price');
            }])
            ->get()
            ->map(function ($cat) {
                $stockVal = $cat->products->sum(fn($p) => $p->current_stock * $p->cost_price);
                return [
                    'name' => $cat->name,
                    'count' => $cat->products_count,
                    'stock_value' => round($stockVal, 2),
                ];
            });

        // 7-day Stock Movement Trends (In vs Out)
        $movementTrends = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $inQty = (int) StockMovement::whereDate('created_at', $date)->where('type', 'IN')->sum('quantity');
            $outQty = (int) StockMovement::whereDate('created_at', $date)->where('type', 'OUT')->sum('quantity');
            $movementTrends[] = [
                'date' => now()->subDays($i)->format('M d'),
                'in' => $inQty,
                'out' => $outQty,
            ];
        }

        return view('dashboard.index', compact(
            'totalProducts',
            'totalItemsInStock',
            'totalCostValuation',
            'totalRetailValuation',
            'potentialProfit',
            'outOfStockCount',
            'lowStockCount',
            'lowStockProducts',
            'recentMovements',
            'categoryBreakdown',
            'movementTrends'
        ));
    }
}
