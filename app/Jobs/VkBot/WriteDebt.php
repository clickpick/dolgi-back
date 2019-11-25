<?php

namespace App\Jobs\VkBot;

use App\Services\Matex;
use App\Services\OutgoingMessage;
use App\Services\VkKeyboard;
use App\User;
use Illuminate\Support\Collection;
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

        $strings = new Collection(explode("\n", $this->incomeMessage->getText()));

        $result = $strings->reduce(function ($carry, $string) {
            $regex = Regex::match('/([-+*\/]?\d+(\.\d+)?)*/', $string);
            $value = $regex->result();

            if (!$value) {
                return $carry;
            }

            $result = (new Matex())->execute($value);

            return array_merge([[
                'value' => $result,
                'comment' => trim(str_replace($value, '', $string))
            ]], $carry);
        }, []);


        if (empty($result)) {
            $message = new OutgoingMessage("Не найдена сумма, попробуй еще раз");
            $this->user->sendVkMessage($message);
            return;
        }

        foreach ($result as $item) {
            $this->user->addDebt($item['value'], $item['comment'], $debtor);
        }

        $message = new OutgoingMessage('Записано!');
        $message->setKeyboard(VkKeyboard::starting());

        $this->user->sendVkMessage($message);
        $this->user->clearActions();
    }
}
