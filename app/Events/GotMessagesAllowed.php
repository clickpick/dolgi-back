<?php

namespace App\Events;

class GotMessagesAllowed extends Event
{
    public $object;

    /**
     * Create a new event instance.
     *
     * @param $object
     */
    public function __construct($object)
    {
        $this->object = $object;
    }
}
