<?php

namespace App\Jobs\VkBot;

use App\Services\OutgoingMessage;
use App\Services\VkClient;
use App\Services\VkCommand;
use App\Services\VkKeyboard;
use App\Services\VkTextButton;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Regex\Regex;
use Spatie\Regex\RegexFailed;

class FindDebtor extends VkBotJob
{
    /**
     * Execute the job.
     *
     * @return void
     * @throws RegexFailed
     */
    public function handle()
    {
        $text = $this->incomeMessage->getText();

        $vkUser = null;

        if (Str::contains($text, '@')) {
            $vkId = Str::after($text, '@');
            $vkId = Str::before($vkId, ']');
            $vkId = Str::before($vkId, ' ');

            try {
                $vkUser = (new VkClient())->getUsers($vkId, ['first_name', 'last_name', 'photo_id']);
            } catch (\Exception $e) {
            }
        } elseif (Regex::match('/(\w*)(\s\w*)?/mu', $text)->result() === $text) {

            $regex = Regex::match('/(\w*)(\s\w*)?/mu', $text);

            $firstName = $regex->group(1);
            $secondName = null;
            if (count($regex->groups()) > 2) {
                $secondName = trim($regex->group(2));
            }

            try {
                $friends = (new VkClient(VkClient::APP_TOKEN))->getFriends($this->user->vk_user_id, [
                    'first_name', 'last_name', 'photo_id'
                ]);
            } catch (\Exception $e) {
                dispatch(new StartAddingDebtor($this->incomeMessage));
                return;
            }

            $friends = new Collection($friends);

            if ($secondName) {
                $fullNameFinds = $friends->filter(function ($friend) use ($firstName, $secondName) {
                    return mb_strtolower($firstName) == mb_strtolower($friend['first_name']) && mb_strtolower($secondName) == mb_strtolower($friend['last_name']) || mb_strtolower($firstName) == mb_strtolower($friend['last_name']) && mb_strtolower($secondName) == mb_strtolower($friend['first_name']);
                });

                if ($fullNameFinds->isNotEmpty()) {
                    $vkUser = $fullNameFinds->first();
                }
            }

            if (!$vkUser) {
                $fullNameFinds = $friends->filter(function ($friend) use ($firstName) {
                    return mb_strtolower($firstName) == mb_strtolower($friend['first_name']) || mb_strtolower($firstName) == mb_strtolower($friend['last_name']);
                });

                if ($fullNameFinds->isNotEmpty()) {
                    $vkUser = $fullNameFinds->first();
                }
            }
        }

        if (!$vkUser) {
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


        $message = new OutgoingMessage("{$vkUser['first_name']} {$vkUser['last_name']}?");

        if (isset($vkUser['photo_id'])) {
            $message->addAttachment('photo' . $vkUser['photo_id']);
        }

        $message->setKeyboard($keyboard);
        $this->user->sendVkMessage($message);
        $this->user->clearActions();
    }
}
