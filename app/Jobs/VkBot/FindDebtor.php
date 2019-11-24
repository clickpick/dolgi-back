<?php

namespace App\Jobs\VkBot;

use App\Services\OutgoingMessage;
use App\Services\VkClient;
use App\Services\VkCommand;
use App\Services\VkKeyboard;
use App\Services\VkTextButton;
use Illuminate\Support\Str;

class FindDebtor extends VkBotJob
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $text = $this->incomeMessage->getText();

        if (Str::contains($text, '@')) {
            $vkId = Str::after($text, '@');
            $vkId = Str::before($vkId, ']');
            $vkId = Str::before($vkId, ' ');

            try {
                $vkUser = (new VkClient())->getUsers($vkId, ['first_name', 'last_name']);
            } catch (\Exception $e) {
                dispatch(new StartAddingDebtor($this->incomeMessage));
                return;
            }
        } else {
            dispatch(new StartAddingDebtor($this->incomeMessage));
            return;
        }

        if ($this->user->debtors()->where('vk_user_id', $vkUser['id'])->exists()) {
            $debtor = $this->user->debtors()->where('vk_user_id', $vkUser['id'])->first();
            $debtor->pivot->touch();
            $message = new OutgoingMessage('Этот должник у тебя уже есть, я поместил его наверх');
            $this->user->sendVkMessage($message);

            $this->user->clearActions();
            dispatch(new Start($this->incomeMessage));
            return;
        }

        $acceptBtn = new VkTextButton("Да");
        $acceptBtn->setCommand(new VkCommand(VkCommand::ACCEPT_DEBTOR, [
            'vk_user_id' => $vkUser['id']
        ]));

        $keyboard = new VkKeyboard();
        $keyboard->addButton($acceptBtn);
        $keyboard->addButton(VkTextButton::cancel());

        $message = new OutgoingMessage("Это {$vkUser['first_name']} {$vkUser['last_name']}?");
        $message->setKeyboard($keyboard);

        $this->user->sendVkMessage($message);
        $this->user->clearActions();
    }
}
