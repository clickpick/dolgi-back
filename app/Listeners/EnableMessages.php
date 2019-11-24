<?php

namespace App\Listeners;

use App\Events\ExampleEvent;
use App\Events\GotNewMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class EnableMessages
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param GotNewMessage $event
     * @return void
     */
    public function handle(GotNewMessage $event)
    {
        $incomeMessage = $event->incomeMessage;

        $user = $incomeMessage->getUser();
        $user->enableMessages();
    }
}
