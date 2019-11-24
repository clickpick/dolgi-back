<?php

namespace App\Listeners;

use App\Events\DebtLogSaved;
use App\Events\ExampleEvent;
use App\Events\GotNewMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CalcDebtValues
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
     * @param DebtLogSaved $event
     * @return void
     */
    public function handle(DebtLogSaved $event)
    {
        $debtLog = $event->debtLog;

        $user = $debtLog->user;
        $debtorId = $debtLog->debtor_id;

        $value = $user->debtLogs()->where('debtor_id', $debtorId)->sum('value');

        $user->debtors()->updateExistingPivot($debtorId, [
            'debt_value' => $value
        ]);
    }
}
