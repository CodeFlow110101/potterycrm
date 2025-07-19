<?php

namespace App\Listeners;

use App\Events\ConfirmationCodeGenerated;
use App\Notifications\ConfirmationCode;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;


class SendConfirmationCode implements ShouldQueue
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
    public function handle(ConfirmationCodeGenerated $event): void
    {
        Notification::send($event->code->phoneno, new ConfirmationCode($event->code));
    }
}
