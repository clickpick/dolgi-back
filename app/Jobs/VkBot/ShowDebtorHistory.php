<?php

namespace App\Jobs\VkBot;

use App\DebtLog;
use App\Services\OutgoingMessage;

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

        $debtLogs = $this->user->debtLogs()->where('debtor_id', $debtorId)->limit(15)->orderBy('created_at', 'desc')->get();
        $debtLogs = $debtLogs->sortBy('created_at');

        $message = "";

        $debtLogs->each(function (DebtLog $debtLog) use (&$message) {
            $message .= "{$debtLog->getFormattedDebtValue()} {$debtLog->comment}, {$this->user->getLocalDate($debtLog->created_at)->format('d.m H:i')} \n";
        });

        $outgoingMessage = new OutgoingMessage($message);
        $this->user->sendVkMessage($outgoingMessage);

        dispatch(new DebtorList($this->incomeMessage));

    }
}
