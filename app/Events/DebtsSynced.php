<?php

namespace App\Events;

use App\DebtLog;
use App\User;

class DebtsSynced extends Event
{
    public $u1;
    public $u2;

    /**
     * Create a new event instance.
     *
     * @param User $u1
     * @param User $u2
     */
    public function __construct(User $u1, User $u2)
    {
        $this->u1 = $u1;
        $this->u2 = $u2;
    }
}
