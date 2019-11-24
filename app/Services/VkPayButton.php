<?php

namespace App\Services;

class VkPayButton extends VkButton {
    protected $hash;

    public function __construct($hash = [], $payload = [])
    {
        $this->type = 'vkpay';
        $this->payload = $payload;
        $this->hash = http_build_query($hash);
    }
}
