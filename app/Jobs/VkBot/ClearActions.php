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

class ClearActions extends VkBotJob
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->user->clearActions();

        dispatch(new Start($this->incomeMessage));
    }
}
