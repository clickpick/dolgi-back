<?php

namespace App\Events;

use App\Services\IncomeMessage;

class GotNewMessage extends Event
{

    public $incomeMessage;

    /**
     * Create a new event instance.
     *
     * @param $object
     */
    public function __construct($object)
    {
        $this->incomeMessage = new IncomeMessage($object);
    }
}
