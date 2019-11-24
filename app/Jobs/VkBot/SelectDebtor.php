<?php

namespace App\Jobs\VkBot;

use App\Services\OutgoingMessage;
use App\Services\VkClient;
use App\Services\VkCommand;
use App\Services\VkKeyboard;
use App\Services\VkPayButton;
use App\Services\VkTextButton;
use App\User;
use App\VkAction;
use Illuminate\Support\Str;

class SelectDebtor extends VkBotJob
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $debtor = User::find($this->incomeMessage->getCommand()->getParams()['user_id']);

        $this->user->setAction(VkAction::WAIT_DEBT, [
            'user_id' => $debtor->id
        ]);

        $message = new OutgoingMessage('Введи сумму и сообщение');


        $showHistoryBtn = new VkTextButton('Показать историю');
        $showHistoryBtn->setCommand(new VkCommand(VkCommand::SHOW_HISTORY, [
            'debtor_id' => $debtor->id
        ]));

        $totalPayoffBtn = new VkTextButton('Погасить полностью');
        $totalPayoffBtn->setCommand(new VkCommand(VkCommand::TOTAL_PAYOFF, [
            'debtor_id' => $debtor->id
        ]));

        $debtValue = $this->user->debtValueForDebtor($debtor);

        $keyboard = new VkKeyboard();
        $keyboard->addButton($showHistoryBtn);

        if ($debtValue != 0) {
            $keyboard->addButton($totalPayoffBtn);
        }

        if ($debtValue < 0) {
            $vkPayBtn = new VkPayButton([
                'action' => 'pay-to-user',
                'amount' => abs($debtValue),
                'description' => 'Погашение долга',
                'user_id' => $debtor->vk_user_id
            ]);
            $keyboard->addButton($vkPayBtn);
        }

        if ($debtValue > 0) {
            $vkPayRequestBtn = new VkTextButton('Запросить перевод');
            $vkPayRequestBtn->setCommand(new VkCommand(VkCommand::REQUEST_PAYOFF, [
                'debtor_id' => $debtor->id
            ]));
            $keyboard->addButton($vkPayRequestBtn);
        }

        $keyboard->addButton(VkTextButton::cancel());

        $message->setKeyboard($keyboard);

        $this->user->sendVkMessage($message);
    }
}
