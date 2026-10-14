<?php

namespace App\Observers;

use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class InventoryTransactionObserver
{
    /**
     * Handle the InventoryTransaction "created" event.
     * Automatically updates product quantity and triggers low-stock alerts.
     */
    public function created(InventoryTransaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            $product = Product::lockForUpdate()->find($transaction->product_id);

            if (!$product) {
                throw ValidationException::withMessages([
                    'product_id' => 'Product not found.',
                ]);
            }

            $signedQty = $transaction->signed_quantity;
            $newQuantity = $product->quantity + $signedQty;

            // Prevent negative inventory on stock-out / damage
            if ($newQuantity < 0) {
                throw ValidationException::withMessages([
                    'quantity' => "Insufficient stock. Available: {$product->quantity}, Requested: " . abs($signedQty),
                ]);
            }

            $product->quantity = $newQuantity;
            $product->save();

            // Trigger low stock notification if needed
            if ($product->isLowStock()) {
                $this->sendLowStockAlert($product);
            }
        });
    }

    /**
     * Handle the InventoryTransaction "deleted" event.
     * Reverses the stock change when a transaction is deleted.
     */
    public function deleted(InventoryTransaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            $product = Product::lockForUpdate()->find($transaction->product_id);

            if (!$product) {
                return;
            }

            // Reverse the original signed quantity
            $reversedQty = -$transaction->signed_quantity;
            $newQuantity = $product->quantity + $reversedQty;

            // Don't allow negative after reverse either
            if ($newQuantity < 0) {
                $newQuantity = 0;
            }

            $product->quantity = $newQuantity;
            $product->save();
        });
    }

    /**
     * Send low stock notification to all admin users.
     * Avoids duplicate notifications within a short window.
     */
    protected function sendLowStockAlert(Product $product): void
    {
        try {
            $admins = User::all(); // In production, filter by role

            foreach ($admins as $admin) {
                // Check if a similar unread notification already exists
                $alreadyNotified = $admin->unreadNotifications()
                    ->where('type', LowStockNotification::class)
                    ->where('data->product_id', $product->id)
                    ->exists();

                if (!$alreadyNotified) {
                    $admin->notify(new LowStockNotification($product));
                }
            }
        } catch (\Exception $e) {
            Log::warning('Failed to send low stock notification: ' . $e->getMessage());
        }
    }
}
