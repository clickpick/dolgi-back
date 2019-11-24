<?php

namespace App\Jobs\VkBot;

use App\Services\OutgoingMessage;
use App\Services\VkClient;
use App\Services\VkCommand;
use App\Services\VkKeyboard;
use App\Services\VkTextButton;
use App\User;
use Illuminate\Support\Str;
use Spatie\Regex\Regex;

class WriteDebt extends VkBotJob
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $debtor = User::find($this->user->getAction()->params['user_id']);


        $regex = Regex::match('/[\+\-]?\d+/m', $this->incomeMessage->getText());
        $value = $regex->result();

        if (!$value) {
            $message = new OutgoingMessage("Не найдена сумма, попробуй еще раз");
            $this->user->sendVkMessage($message);
            return;
        }

        $comment = trim(str_replace($value, '', $this->incomeMessage->getText()));

        $this->user->addDebt($value, $comment, $debtor);

        $message = new OutgoingMessage('Записано!');
        $message->setKeyboard(VkKeyboard::starting());

        $this->user->sendVkMessage($message);
        $this->user->clearActions();
    }
}
