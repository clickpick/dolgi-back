<?php

namespace App\Jobs\VkBot;

use App\Services\OutgoingMessage;
use App\Services\VkKeyboard;
use App\User;

class ShowActiveDebtors extends VkBotJob
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $debtors = $this->user->debtors()->wherePivot('debt_value', '!=', 0)->get();

        if ($debtors->isEmpty()) {
            $message = new OutgoingMessage('Должников нет!');
            $message->setKeyboard(VkKeyboard::starting());
            $this->user->sendVkMessage($message);
            return;
        }


        $message = $debtors->reduce(function ($carry, User $debtor) {
            $emoji = $debtor->pivot->debt_value < 0 ? '➖' : '➕';
            $value = $emoji . number_format(abs($debtor->pivot->debt_value), 0, '.', ' ');

            return $carry . "\n" . "{$debtor->first_name} {$debtor->last_name} {$value}";
        }, '');

        $total = $this->user->totalDebtValue();
        $emoji = $total < 0 ? '➖' : '➕';
        $totalValue = $emoji . number_format(abs($total), 0, '.', ' ');

        $message .= "\n\nИтого: {$totalValue}";

        $outgoingMessage = new OutgoingMessage($message);

        $outgoingMessage->setKeyboard(VkKeyboard::starting());

        $this->user->sendVkMessage($outgoingMessage);
    }
}
