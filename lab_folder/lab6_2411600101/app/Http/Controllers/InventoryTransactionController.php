<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryTransactionController extends Controller
{
    public function index(Request $request): View
    {
        $query = InventoryTransaction::with(['product', 'user'])->recent();

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->paginate(20)->withQueryString();
        $products = Product::orderBy('name')->get(['id', 'name', 'sku']);
        $types = InventoryTransaction::types();

        return view('transactions.index', compact('transactions', 'products', 'types'));
    }

    public function create(): View
    {
        $products = Product::orderBy('name')->get();
        $types = InventoryTransaction::types();

        return view('transactions.create', compact('products', 'types'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id'       => 'required|exists:products,id',
            'type'             => 'required|in:' . implode(',', array_keys(InventoryTransaction::types())),
            'quantity'         => 'required|integer|min:1',
            'reference_number' => 'nullable|string|max:100',
            'notes'            => 'nullable|string|max:1000',
        ], [
            'product_id.required' => 'Please select a product.',
            'product_id.exists'   => 'Selected product does not exist.',
            'type.required'       => 'Please select a transaction type.',
            'quantity.required'   => 'Quantity is required.',
            'quantity.min'        => 'Quantity must be at least 1.',
        ]);

        // Extra check for stock-out before creating (observer will also enforce)
        $product = Product::findOrFail($validated['product_id']);
        $isStockOut = in_array($validated['type'], [
            InventoryTransaction::TYPE_STOCK_OUT,
            InventoryTransaction::TYPE_DAMAGE,
        ]);

        if ($isStockOut && $validated['quantity'] > $product->quantity) {
            return back()
                ->withInput()
                ->withErrors(['quantity' => "Insufficient stock. Available: {$product->quantity}"]);
        }

        $validated['user_id'] = auth()->id();

        try {
            InventoryTransaction::create($validated);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        }

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transaction recorded successfully. Stock has been updated.');
    }

    public function show(InventoryTransaction $transaction): View
    {
        $transaction->load(['product', 'user']);

        return view('transactions.show', compact('transaction'));
    }

    public function destroy(InventoryTransaction $transaction): RedirectResponse
    {
        $transaction->delete(); // Observer will reverse the stock change

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transaction deleted and stock change reversed.');
    }

    /**
     * Stock Adjustment form
     */
    public function adjustmentForm(): View
    {
        $products = Product::orderBy('name')->get();

        return view('transactions.adjustment', compact('products'));
    }

    /**
     * Process stock adjustment
     */
    public function processAdjustment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id'     => 'required|exists:products,id',
            'physical_count' => 'required|integer|min:0',
            'reason'         => 'required|string|max:100',
            'notes'          => 'nullable|string|max:1000',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $difference = $validated['physical_count'] - $product->quantity;

        if ($difference === 0) {
            return back()->with('error', 'Physical count matches system quantity. No adjustment needed.');
        }

        InventoryTransaction::create([
            'product_id'       => $product->id,
            'user_id'          => auth()->id(),
            'type'             => InventoryTransaction::TYPE_ADJUSTMENT,
            'quantity'         => $difference, // can be positive or negative
            'reference_number' => 'ADJ-' . now()->format('YmdHis'),
            'notes'            => "Reason: {$validated['reason']}. " . ($validated['notes'] ?? '') .
                                  " System: {$product->quantity} → Physical: {$validated['physical_count']}",
        ]);

        return redirect()
            ->route('transactions.index')
            ->with('success', "Stock adjusted by {$difference}. New quantity: {$validated['physical_count']}.");
    }
}
