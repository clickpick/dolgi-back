<?php

namespace App\Services;

class VkLocationButton extends VkButton {

    public function __construct($payload = [])
    {
        $this->type = 'location';
        $this->payload = $payload;
    }
}
