<?php

namespace App\Listeners;

use App\Events\DebtLogSaved;
use App\Events\DebtsSynced;
use App\Events\ExampleEvent;
use App\Events\GotNewMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CalcDebtValuesForSyncedDebtors
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
     * @param DebtsSynced $event
     * @return void
     */
    public function handle(DebtsSynced $event)
    {
        $u1 = $event->u1;
        $u2 = $event->u2;

        $value1 = $u1->debtValueForDebtor($u2);
        $value2 = $u2->debtValueForDebtor($u1);

        $u1->debtors()->updateExistingPivot($u2->id, [
            'debt_value' => $value1
        ]);

        $u2->debtors()->updateExistingPivot($u1->id, [
            'debt_value' => $value2
        ]);
    }
}
