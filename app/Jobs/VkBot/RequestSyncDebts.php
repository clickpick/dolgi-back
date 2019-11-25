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

class RequestSyncDebts extends VkBotJob
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /**
         * @var User $debtor
         */
        $debtor = $this->user->debtors()->where('users.id', $this->incomeMessage->getCommand()->getParams()['debtor_id'])->first();

        if ($debtor->pivot->is_syncing) {
            $this->user->sendVkMessage(new OutgoingMessage('Уже синхронизируются'));
            return;
        }

        if (!$this->user->isCrossDebt($debtor)) {
            $this->user->sendVkMessage(new OutgoingMessage('Вы должны добавить друг друга в должники'));
            return;
        }

        if ($this->user->receivedSyncRequests()->where('initiator_id', $debtor->id)->exists()) {
            $this->user->syncWithUser($debtor);
            $this->user->sendVkMessage(new OutgoingMessage('Ваши долги синхронизированы'));
            return;
        }

        if ($this->user->initedSyncRequests()->where('acceptor_id', $debtor->id)->exists()) {
            $this->user->sendVkMessage(new OutgoingMessage('Запрос уже отправлен'));
            return;
        }

        $this->user->initedSyncRequests()->create([
            'acceptor_id' => $debtor->id
        ]);


        if (!$debtor->messages_are_enabled) {
            $this->user->sendVkMessage(new OutgoingMessage('Твой друг не разрешил писать мне'));
            return;
        }

        try {
            $acceptRequestBtn = new VkTextButton('Принять запрос');
            $acceptRequestBtn->setPositiveColor();
            $acceptRequestBtn->setCommand(new VkCommand(VkCommand::ACCEPT_CROSS_DEBT, [
                'user_id' => $this->user->id
            ]));

            $cancelRequestBtn = new VkTextButton('Отколнить');
            $cancelRequestBtn->setCommand(new VkCommand(VkCommand::CANCEL));
            $cancelRequestBtn->setNegativeColor();

            $keyboard = new VkKeyboard();
            $keyboard->addButton($acceptRequestBtn);
            $keyboard->addButton($cancelRequestBtn);

            $message = new OutgoingMessage("{$this->user->first_name} {$this->user->last_name} предлагает синхронизировать долги");
            $message->setKeyboard($keyboard);

            $debtor->sendVkMessage($message);

        } catch (\Exception $e) {
            $this->user->sendVkMessage(new OutgoingMessage('Я не смог отправить запрос'));
            return;
        }

        $this->user->sendVkMessage(new OutgoingMessage('Запрос отправлен'));
    }
}
