<?php

namespace App\Listeners;

use App\Events\DebtLogSaved;
use App\Services\OutgoingMessage;
use App\User;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendNotificationToSyncedDebt implements ShouldQueue
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

        /**
         * @var User $debtor
         */
        $debtor = User::find($debtLog->debtor_id);

        if ($user->isDebtorSynced($debtor)) {
            $debtValueForSynced = $debtLog->getFormattedDebtValue(-1);
            $comment = $debtLog->comment;


            $message = "Новая запись от {$user->first_name} {$user->last_name} \n";
            $message .= "{$debtValueForSynced} {$comment}\n\n";


            $totalForDebt = $debtor->debtValueForDebtor($user);
            $emoji = $totalForDebt < 0 ? '➖' : '➕';
            $value = $emoji . number_format(abs($totalForDebt), 0, '.', ' ');

            $message .= "Итого: {$value}";

            $outgoingMessage = new OutgoingMessage($message);

            $debtor->sendVkMessage($outgoingMessage);
        }
    }
}
