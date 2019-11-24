<?php

namespace App\Events;

use App\DebtLog;
use App\User;

class DebtLogSaved extends Event
{

    public $debtLog;

    /**
     * Create a new event instance.
     *
     * @param DebtLog $debtLog
     */
    public function __construct(DebtLog $debtLog)
    {
        $this->debtLog = $debtLog;
    }
}
