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

class AcceptDebtor extends VkBotJob
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $command = $this->incomeMessage->getCommand();

        $vkId = $command->getParams()['vk_user_id'];

        $debtor = User::getByVkId($vkId);
        $this->user->debtors()->attach($debtor->id);

        $message = new OutgoingMessage('Должник добавлен');
        $message->setKeyboard(VkKeyboard::starting());

        $this->user->sendVkMessage($message);
    }
}
