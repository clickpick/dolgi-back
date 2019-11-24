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

class DebtorList extends VkBotJob
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $debtors = $this->user->debtors()->orderBy('pivot_updated_at', 'desc')->get();

        if ($debtors->isEmpty()) {
            $message = new OutgoingMessage('Должников нет!');
            $message->setKeyboard(VkKeyboard::starting());
            $this->user->sendVkMessage($message);
            return;
        }

        $keyboard = new VkKeyboard();
        $keyboard->setRows(2);

        $debtors->each(function (User $debtor) use ($keyboard) {

            $emoji = $debtor->pivot->debt_value < 0 ? '➖' : '➕';
            $value = $emoji . number_format(abs($debtor->pivot->debt_value), 0, '.', ' ');

            $firstName = mb_substr($debtor->first_name, 0, 1) . '.';

            $btn = new VkTextButton("{$firstName} {$debtor->last_name} {$value}");
            $btn->setCommand(new VkCommand(VkCommand::SELECT_DEBTOR, [
                'user_id' => $debtor->id
            ]));
            $btn->setSecondaryColor();
            $keyboard->addButton($btn);
        });

        $keyboard->addButton(VkTextButton::cancel());

        $message = new OutgoingMessage('Нажми на должника, чтобы добавить или списать долг');
        $message->setKeyboard($keyboard);

        $this->user->sendVkMessage($message);
    }
}
