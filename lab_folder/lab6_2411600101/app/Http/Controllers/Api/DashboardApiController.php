<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class DashboardApiController extends Controller
{
    /**
     * GET /api/dashboard/stats
     */
    public function stats(): JsonResponse
    {
        $totalProducts   = Product::count();
        $lowStockCount   = Product::lowStock()->count();
        $outOfStockCount = Product::outOfStock()->count();
        $totalValue      = Product::selectRaw('SUM(quantity * unit_price) as total')->value('total') ?? 0;

        return response()->json([
            'total_products'    => $totalProducts,
            'low_stock_count'   => $lowStockCount,
            'out_of_stock_count'=> $outOfStockCount,
            'total_value'       => round((float) $totalValue, 2),
            'updated_at'        => now()->toIso8601String(),
        ]);
    }

    /**
     * GET /api/products/low-stock
     */
    public function lowStock(): JsonResponse
    {
        $products = Product::lowStock()
            ->orderBy('quantity')
            ->take(10)
            ->get(['id', 'name', 'sku', 'quantity', 'reorder_level', 'category']);

        return response()->json($products);
    }

    /**
     * GET /api/transactions/recent
     */
    public function recentTransactions(): JsonResponse
    {
        $transactions = InventoryTransaction::with(['product:id,name,sku', 'user:id,name'])
            ->recent()
            ->take(10)
            ->get()
            ->map(function ($t) {
                return [
                    'id'               => $t->id,
                    'type'             => $t->type,
                    'type_label'       => $t->type_label,
                    'quantity'         => $t->quantity,
                    'signed_quantity'  => $t->signed_quantity,
                    'reference_number' => $t->reference_number,
                    'product_name'     => $t->product?->name,
                    'product_sku'      => $t->product?->sku,
                    'user_name'        => $t->user?->name,
                    'created_at'       => $t->created_at->toIso8601String(),
                    'created_at_human' => $t->created_at->diffForHumans(),
                ];
            });

        return response()->json($transactions);
    }

    /**
     * GET /api/products/{product}/current-stock
     * Used by the create/adjustment forms via AJAX
     */
    public function currentStock(Product $product): JsonResponse
    {
        return response()->json([
            'id'            => $product->id,
            'name'          => $product->name,
            'sku'           => $product->sku,
            'quantity'      => $product->quantity,
            'reorder_level' => $product->reorder_level,
            'stock_status'  => $product->stock_status,
        ]);
    }
}
