<?php

namespace App\Services;

class VkTextButton extends VkButton {
    protected $label;

    public function __construct($label, $payload = [])
    {
        $this->type = 'text';
        $this->label = $label;
        $this->payload = $payload;
    }

    public function setCommand(VkCommand $vkCommand) {
        $this->payload['command'] = $vkCommand->toPayload();
    }

    public static function cancel() {
        $cancelButton = new self('Отмена');
        $cancelButton->setCommand(new VkCommand(VkCommand::CANCEL));
        $cancelButton->setNegativeColor();

        return $cancelButton;
    }
}
