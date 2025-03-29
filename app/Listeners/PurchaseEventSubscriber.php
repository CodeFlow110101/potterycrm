<?php

namespace App\Listeners;

use App\Events\PurchaseCreated;
use App\Models\User;
use App\Notifications\Purchase;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Notification;

class PurchaseEventSubscriber
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    public function handlePurchaseCreated(PurchaseCreated $event): void
    {
        Notification::send(User::where('role_id', 1)->orWhere('id', $event->purchase->user_id)->get(), new Purchase($event->purchase));
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            PurchaseCreated::class,
            [PurchaseEventSubscriber::class, 'handleUserLogin']
        );
    }
}
