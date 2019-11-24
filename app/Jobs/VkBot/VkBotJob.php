<?php

namespace App\Jobs\VkBot;

use App\Jobs\Job;
use App\Services\IncomeMessage;

class VkBotJob extends Job
{
    protected $incomeMessage;
    protected $user;

    /**
     * Create a new job instance.
     *
     * @param IncomeMessage $incomeMessage
     */
    public function __construct(IncomeMessage $incomeMessage)
    {
        $this->incomeMessage = $incomeMessage;
        $this->user = $incomeMessage->getUser();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
    }
}
