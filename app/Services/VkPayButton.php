<?php

namespace App\Services;

class VkPayButton extends VkButton {
    protected $hash;

    public function __construct($payload = [], $hash = "")
    {
        $this->type = 'vkpay';
        $this->payload = $payload;
        $this->hash = $hash;
    }
}
