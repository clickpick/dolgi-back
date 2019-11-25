<?php

namespace App\Jobs\VkBot;

use App\Services\OutgoingMessage;
use App\User;

class TotalDebtorPayoff extends VkBotJob
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
        $debtor = User::findOrFail($debtorId);

        $totalDebtValue = $this->user->debtValueForDebtor($debtor);

        $this->user->addDebt($totalDebtValue * (-1), 'Тотальное погашение', $debtor);

        $message = new OutgoingMessage('Полностью погашено!');
        $this->user->sendVkMessage($message);

        dispatch(new DebtorList($this->incomeMessage));

    }
}
