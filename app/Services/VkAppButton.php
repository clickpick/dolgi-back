<?php

namespace App\Services;

class VkAppButton extends VkButton {

    protected $label;
    protected $appId;
    protected $hash;
    protected $ownerId;

    public function __construct($label, $appId, $hash = "", $payload = [], $ownerId = null)
    {
        $this->type = 'open_app';
        $this->payload = $payload;
        $this->label = $label;
        $this->appId = $appId;
        $this->hash = $hash;
        $this->ownerId = $ownerId;
    }
}
