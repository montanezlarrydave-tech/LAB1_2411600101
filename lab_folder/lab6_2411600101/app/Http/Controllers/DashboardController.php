<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the fitness inventory dashboard with real statistics.
     */
    public function index()
    {
        $totalProducts   = Product::count();
        $lowStockCount   = Product::lowStock()->count();
        $outOfStockCount = Product::outOfStock()->count();
        $totalValue      = Product::selectRaw('SUM(quantity * unit_price) as total')
                                  ->value('total') ?? 0;

        $recentProducts  = Product::latest()->take(5)->get();
        $lowStockItems   = Product::lowStock()
                                  ->orderBy('quantity')
                                  ->take(5)
                                  ->get();

        // Category breakdown for potential charts
        $categoryStats = Product::selectRaw('category, COUNT(*) as count, SUM(quantity * unit_price) as value')
                                ->groupBy('category')
                                ->get();

        return view('dashboard', compact(
            'totalProducts',
            'lowStockCount',
            'outOfStockCount',
            'totalValue',
            'recentProducts',
            'lowStockItems',
            'categoryStats'
        ));
    }
}
