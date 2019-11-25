<?php

namespace App\Jobs\VkBot;

use App\DebtLog;
use App\Services\OutgoingMessage;
use App\User;

class ShowDebtorHistory extends VkBotJob
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $command = $this->incomeMessage->getCommand();
        $debtorId = $command->getParams()['debtor_id'];

        /**
         * @var User $debtor
         */
        $debtor = User::find($debtorId);
        $isSyncedLog = $this->user->isDebtorSynced($debtor);


        $debtLogs = $this->user->debtLogs()->where('debtor_id', $debtorId)->limit(15)->orderBy('created_at', 'desc')->get();

        if ($isSyncedLog) {
            $debtLogs->each(function (DebtLog $debtLog) {
                $debtLog->setAttribute('self', true);
            });


            $debtorDebtLogs = $debtor->debtLogs()->where('debtor_id', $this->user->id)->limit(15)->orderBy('created_at', 'desc')->get();
            $debtorDebtLogs->each(function(DebtLog $debtLog) {
                $debtLog->setAttribute('self', false);
            });
            $debtLogs = $debtLogs->merge($debtorDebtLogs);
        }

        $debtLogs = $debtLogs->sortBy('created_at');

        $message = "";

        $debtLogs->each(function (DebtLog $debtLog) use (&$message, $isSyncedLog, $debtor) {
            if ($isSyncedLog) {
                $suffix = $debtLog->getAttribute('self') ? 'Я' : $debtor->first_name;
                $debtValue = $debtLog->getFormattedDebtValue($debtLog->getAttribute('self') ? 1 : (-1));

                $message .= "{$debtValue} {$debtLog->comment}, {$this->user->getLocalDate($debtLog->created_at)->format('d.m H:i')} ({$suffix}) \n";
            } else {
                $message .= "{$debtLog->getFormattedDebtValue()} {$debtLog->comment}, {$this->user->getLocalDate($debtLog->created_at)->format('d.m H:i')} \n";
            }
        });

        $outgoingMessage = new OutgoingMessage($message);
        $this->user->sendVkMessage($outgoingMessage);

//        dispatch(new DebtorList($this->incomeMessage));

    }
}
