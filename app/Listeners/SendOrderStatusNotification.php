<?php

namespace App\Listeners;

use App\Events\OrderStatusUpdated;
use App\Notifications\OrderStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendOrderStatusNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderStatusUpdated $event): void
    {
        $event->order->status_id && Notification::send($event->order->purchase->user, new OrderStatus($event->order));
    }
}
