<?php

namespace App\Jobs\VkBot;

use App\Services\OutgoingMessage;
use App\Services\VkClient;
use App\Services\VkCommand;
use App\Services\VkKeyboard;
use App\Services\VkTextButton;
use App\User;
use App\VkAction;
use Illuminate\Support\Str;
use Spatie\Regex\Regex;

class AcceptSyncDebts extends VkBotJob
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $command = $this->incomeMessage->getCommand();

        $userId = $command->getParams()['user_id'];

        $debtor = User::find($userId);

        if (!$this->user->receivedSyncRequests()->where('initiator_id', $debtor->id)->exists()) {
            $message = new OutgoingMessage('Ошибка');
            $keyboard = VkKeyboard::starting();
            $message->setKeyboard($keyboard);
            $this->user->sendVkMessage($message);
            return;
        }

        $this->user->syncWithUser($debtor);

        $message = new OutgoingMessage('Ваши долги синхронинизрованы');
        $keyboard = VkKeyboard::starting();
        $message->setKeyboard($keyboard);

        $this->user->sendVkMessage($message);

    }
}
