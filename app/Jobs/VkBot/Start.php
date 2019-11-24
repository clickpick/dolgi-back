<?php

namespace App\Jobs\VkBot;

use App\Services\OutgoingMessage;
use App\Services\VkClient;
use App\Services\VkCommand;
use App\Services\VkKeyboard;
use App\Services\VkTextButton;
use Illuminate\Support\Str;

class Start extends VkBotJob
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->user->totalDebtValue() > 0) {
            dispatch(new ShowActiveDebtors($this->incomeMessage));
            return;
        }

        $message = "Смотри че могу";

        if (!$this->user->utc_offset) {
            $message .= "\n\nОтправь свое местоположение, чтобы определить таймзону";
        }

        $outgoingMessage = new OutgoingMessage($message);
        $outgoingMessage->setKeyboard(VkKeyboard::starting($this->user));


        $this->user->sendVkMessage($outgoingMessage);
    }
}
