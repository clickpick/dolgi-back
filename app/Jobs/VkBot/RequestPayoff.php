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
use Spatie\Regex\Regex;

class RequestPayoff extends VkBotJob
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

        if (!$debtor) {
            $this->user->sendVkMessage(new OutgoingMessage('Этот пользователь у нас еще не зерегистрировался'));
            return;
        }

        try {
            $friendList = (new VkClient(VkClient::APP_TOKEN))->getFriends($this->user->id);
            if (!in_array($debtor->id, $friendList)) {
                $this->user->sendVkMessage(new OutgoingMessage('Запросить перевод можно только у друга'));
                return;
            }
        } catch (\Exception $e) {
            $this->user->sendVkMessage(new OutgoingMessage('Я не смог найти этого пользователя у тебя в друзьях, возможно у тебя приватный профиль'));
            return;
        }

        if (!$debtor->messages_are_enabled) {
            $this->user->sendVkMessage(new OutgoingMessage('Твой друг не разрешил писать мне'));
            return;
        }

        $debtValue = $this->user->debtValueForDebtor($debtor);

        if ($debtValue <= 0) {
            $this->user->sendVkMessage(new OutgoingMessage('Он тебе ничего не должен'));
            return;
        }

        $vkPayBtn = new VkPayButton([
            'action' => 'pay-to-user',
            'amount' => abs($debtValue),
            'description' => 'Погашение долга',
            'user_id' => $this->user->vk_user_id
        ]);

        $formattedDebtValue = number_format($debtValue, 0, '.', ' ');

        $keyboard = new VkKeyboard();
        $keyboard->addButton($vkPayBtn);

        $message = new OutgoingMessage("{$this->user->first_name} {$this->user->last_name} просит погасить долг в размере {$formattedDebtValue}₽");
        $message->setKeyboard($keyboard);


        try {
            $debtor->sendVkMessage($message);
            $this->user->sendVkMessage(new OutgoingMessage('Запрос отправлен'));

        } catch (\Exception $e) {
            $this->user->sendVkMessage(new OutgoingMessage('Я не смог отправить запрос твоему другу'));
        }
    }
}
