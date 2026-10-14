<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Product $product)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('⚠️ Low Stock Alert: ' . $this->product->name)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('The following product has reached or fallen below its reorder level:')
            ->line('**Product:** ' . $this->product->name)
            ->line('**SKU:** ' . $this->product->sku)
            ->line('**Current Quantity:** ' . $this->product->quantity)
            ->line('**Reorder Level:** ' . $this->product->reorder_level)
            ->action('View Product', url('/products/' . $this->product->id))
            ->line('Please consider placing a purchase order soon.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'product_id'    => $this->product->id,
            'product_name'  => $this->product->name,
            'sku'           => $this->product->sku,
            'quantity'      => $this->product->quantity,
            'reorder_level' => $this->product->reorder_level,
            'message'       => "{$this->product->name} (SKU: {$this->product->sku}) is low on stock. Qty: {$this->product->quantity} / Reorder: {$this->product->reorder_level}",
        ];
    }
}
